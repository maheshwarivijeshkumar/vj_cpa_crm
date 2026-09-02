<?php

declare(strict_types=1);

namespace App\Services\Subscription;

use App\DTOs\SubscriptionData;
use App\Enums\SubscriptionStatus;
use App\Enums\TenantPlan;
use App\Events\Subscription\SubscriptionCancelled;
use App\Events\Subscription\SubscriptionCreated;
use App\Events\Subscription\SubscriptionLapsed;
use App\Events\Subscription\SubscriptionRenewed;
use App\Models\Subscription;
use App\Models\Tenant;
use App\Repositories\Contracts\DiscountRepositoryInterface;
use App\Repositories\Contracts\SubscriptionRepositoryInterface;
use App\Services\Audit\AuditService;
use App\Services\Discount\DiscountService;
use App\Services\Referral\ReferralService;
use Illuminate\Support\Facades\DB;

/**
 * SubscriptionService — all business logic for tenant subscription lifecycle.
 *
 * Lifecycle:
 *   1. create()    — new subscription (trial or paid)
 *   2. renew()     — extend an existing subscription by one billing cycle
 *   3. cancel()    — cancel with effective date
 *   4. lapseExpired() — mark ended subscriptions as lapsed (scheduled job)
 *   5. applyDiscount()  — validate + apply a discount code at billing time
 *
 * The service coordinates with DiscountService (to apply codes) and
 * ReferralService (to reward referrers on first paid subscription).
 */
final class SubscriptionService
{
    public function __construct(
        private readonly SubscriptionRepositoryInterface $subscriptions,
        private readonly DiscountService                 $discountService,
        private readonly ReferralService                 $referralService,
        private readonly DiscountRepositoryInterface     $discounts,
    ) {}

    /**
     * Create a new subscription for a tenant.
     *
     * If a discount code is provided, it is validated + applied here.
     * If the tenant was referred, the referrer is rewarded after commit.
     *
     * @param  bool  $isFirstPaidSubscription  True if this is the first non-trial paid sub
     * @throws \DomainException if discount code is invalid
     */
    public function create(
        SubscriptionData $data,
        bool             $isFirstPaidSubscription = false,
        ?string          $discountCode            = null,
    ): Subscription {
        $discountId     = null;
        $discountAmount = '0.000000';

        // Validate and apply discount if code provided
        if ($discountCode !== null) {
            $plan    = $data->plan->value;
            $tenant  = Tenant::findOrFail($data->tenantId);
            $discount = $this->discountService->validate(
                $discountCode,
                $data->tenantId,
                $plan,
                $data->amountPaid,
            );
            $discountId = $discount->id;
        }

        $subscription = DB::transaction(function () use ($data, $discountId, &$discountAmount, $discountCode): Subscription {
            // Apply discount within the transaction so we have subscription ID ready
            if ($discountId !== null) {
                $result = $this->discountService->apply(
                    $this->discounts->findOrFail($discountId),
                    $data->tenantId,
                    $data->amountPaid,
                );
                $discountAmount = $result['discount_amount'];
            }

            $finalAmount = bcsub($data->amountPaid, $discountAmount, 6);

            return $this->subscriptions->create(array_merge(
                $data->toModelArray(),
                [
                    'discount_id'    => $discountId,
                    'discount_amount'=> $discountAmount,
                    'amount_paid'    => max('0.000000', $finalAmount),
                    'status'         => SubscriptionStatus::Active->value,
                ],
            ));
        });

        // Update tenant plan + status to match new subscription
        DB::table('tenants')
            ->where('id', $data->tenantId)
            ->update([
                'plan'       => $data->plan->value,
                'status'     => 'active',
                'updated_at' => now(),
            ]);

        DB::afterCommit(fn () => SubscriptionCreated::dispatch($subscription));

        // Reward referrer if this is the first paid subscription
        if ($isFirstPaidSubscription && $data->plan !== TenantPlan::Trial) {
            DB::afterCommit(fn () => $this->referralService->rewardReferrer(
                $data->tenantId,
                $subscription->id,
            ));
        }

        AuditService::log('subscription_created', $subscription, [
            'plan'            => $data->plan->value,
            'billing_cycle'   => $data->billingCycle,
            'discount_applied'=> $discountId !== null,
        ]);

        return $subscription;
    }

    /**
     * Renew an existing subscription by one billing cycle.
     * Previous subscription is kept for audit history; new one is created.
     *
     * @throws \DomainException if tenant has no current subscription
     */
    public function renew(
        int     $tenantId,
        ?string $discountCode = null,
    ): Subscription {
        $current = $this->subscriptions->currentForTenant($tenantId);

        if ($current === null) {
            throw new \DomainException('No active subscription found for this tenant.');
        }

        $startsAt = $current->ends_at->addDay();
        $endsAt   = $current->billing_cycle === 'annual'
            ? $startsAt->addYear()
            : $startsAt->addMonth();

        $data = new SubscriptionData(
            tenantId:     $tenantId,
            plan:         $current->plan,
            startsAt:     $startsAt,
            endsAt:       $endsAt,
            amountPaid:   (string) $current->plan->monthlyPrice(),
            billingCycle: $current->billing_cycle,
            currencyId:   $current->currency_id,
        );

        $subscription = $this->create($data, false, $discountCode);

        DB::afterCommit(fn () => SubscriptionRenewed::dispatch($subscription, $current));

        return $subscription;
    }

    /**
     * Cancel a subscription.
     * Access is still allowed until ends_at if end of billing period.
     */
    public function cancel(
        string  $subscriptionId,
        string  $reason,
        bool    $immediately = false,
    ): Subscription {
        $subscription = $this->subscriptions->findOrFail($subscriptionId);

        $cancelledAt = now();
        $newStatus   = $immediately
            ? SubscriptionStatus::Cancelled->value
            : SubscriptionStatus::Active->value; // Stays active until period end

        $this->subscriptions->update($subscriptionId, [
            'status'              => $newStatus,
            'cancelled_at'        => $cancelledAt,
            'cancellation_reason' => $reason,
            'ends_at'             => $immediately ? now()->toDateString() : $subscription->ends_at,
        ]);

        DB::afterCommit(fn () => SubscriptionCancelled::dispatch($subscription->fresh()));

        AuditService::log('subscription_cancelled', $subscription, [
            'reason'      => $reason,
            'immediately' => $immediately,
        ]);

        return $subscription->fresh();
    }

    /**
     * Mark expired subscriptions as lapsed.
     * Called daily by a scheduled job.
     * Triggers the win-back discount flow via SubscriptionLapsed event.
     *
     * @return int  Number of subscriptions lapsed
     */
    public function lapseExpired(int $graceDays = 3): int
    {
        $expired = $this->subscriptions->findExpiredUnlapsed($graceDays);

        if ($expired->isEmpty()) {
            return 0;
        }

        DB::table('subscriptions')
            ->whereIn('id', $expired->pluck('id'))
            ->update([
                'status'     => SubscriptionStatus::Lapsed->value,
                'lapsed_at'  => now(),
                'updated_at' => now(),
            ]);

        // Update tenant status
        DB::table('tenants')
            ->whereIn('id', $expired->pluck('tenant_id'))
            ->update(['status' => 'active', 'updated_at' => now()]); // Tenant stays 'active' (data preserved)

        // Fire event per subscription — SubscriptionLapsed listener sends win-back email
        foreach ($expired as $subscription) {
            DB::afterCommit(fn () => SubscriptionLapsed::dispatch($subscription));
        }

        return $expired->count();
    }

    /**
     * Get platform-level subscription statistics.
     *
     * @return array{total_active: int, mrr: string, by_plan: array, by_cycle: array}
     */
    public function platformStats(): array
    {
        return [
            'total_active' => DB::table('subscriptions')->where('status', 'active')->count(),
            'mrr'          => $this->subscriptions->mrrSnapshot(),
            'by_plan'      => collect(TenantPlan::cases())
                ->mapWithKeys(fn ($p) => [$p->value => $this->subscriptions->countByPlan($p->value)])
                ->toArray(),
        ];
    }
}
