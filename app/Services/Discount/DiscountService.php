<?php

declare(strict_types=1);

namespace App\Services\Discount;

use App\DTOs\DiscountData;
use App\Enums\DiscountTrigger;
use App\Enums\DiscountType;
use App\Events\Discount\DiscountApplied;
use App\Events\Discount\DiscountCreated;
use App\Events\Discount\WinbackDiscountSent;
use App\Models\Discount;
use App\Models\Tenant;
use App\Repositories\Contracts\DiscountRepositoryInterface;
use App\Services\Audit\AuditService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

/**
 * DiscountService — all business logic for discount codes.
 *
 * Responsibilities:
 *  - Create discounts (manual or system-generated via trigger)
 *  - Validate a discount code for a given tenant + plan + amount
 *  - Apply a discount: calculate savings, write DiscountUsage record,
 *    increment global counter atomically, return final amount
 *  - Automatically generate and email win-back discounts for lapsed tenants
 *  - Auto-expire discounts past their valid_until date (called by scheduled job)
 */
final class DiscountService
{
    public function __construct(
        private readonly DiscountRepositoryInterface $discounts,
    ) {}

    /**
     * Create a discount — validation in FormRequest, business logic here.
     *
     * @throws \DomainException if code already exists
     */
    public function create(DiscountData $data, int $createdByUserId): Discount
    {
        if ($this->discounts->findByCode($data->code) !== null) {
            throw new \DomainException("Discount code '{$data->code}' already exists.");
        }

        $discount = DB::transaction(function () use ($data, $createdByUserId): Discount {
            $discount = $this->discounts->create(array_merge(
                $data->toModelArray(),
                ['created_by' => $createdByUserId],
            ));

            // Sync tenant assignments when applicability = specific
            if (! empty($data->tenantIds)) {
                $this->discounts->syncTenantAssignments($discount->id, $data->tenantIds);
            }

            return $discount;
        });

        DB::afterCommit(fn () => DiscountCreated::dispatch($discount));

        AuditService::log('created', $discount, ['trigger' => $data->trigger->value]);

        return $discount;
    }

    /**
     * Update an existing discount.
     *
     * @throws \DomainException if trying to change code to one that already exists
     */
    public function update(string $discountId, DiscountData $data): Discount
    {
        $existing = $this->discounts->findOrFail($discountId);

        // Code uniqueness check (exclude self)
        $byCode = $this->discounts->findByCode($data->code);
        if ($byCode !== null && $byCode->id !== $discountId) {
            throw new \DomainException("Discount code '{$data->code}' is already used by another discount.");
        }

        return DB::transaction(function () use ($discountId, $data): Discount {
            $discount = $this->discounts->update($discountId, $data->toModelArray());

            $this->discounts->syncTenantAssignments($discountId, $data->tenantIds);

            return $discount;
        });
    }

    /**
     * Validate a discount code for a given context.
     * Returns the Discount model if valid, throws a DomainException with reason if not.
     *
     * @throws \DomainException  with a user-safe error message
     */
    public function validate(string $code, int $tenantId, string $plan, string $amount): Discount
    {
        $discount = $this->discounts->findByCode($code);

        if ($discount === null) {
            throw new \DomainException('This discount code does not exist.');
        }

        if ($discount->status->value !== 'active') {
            throw new \DomainException('This discount code is no longer active.');
        }

        if ($discount->isExpired()) {
            throw new \DomainException('This discount code has expired.');
        }

        if ($discount->isDepletedForGlobal()) {
            throw new \DomainException('This discount code has reached its maximum usage limit.');
        }

        if ($discount->isDepletedForTenant($tenantId)) {
            throw new \DomainException('You have already used this discount code the maximum number of times.');
        }

        // Applicability check
        $usable = $this->discounts->usableForTenant($tenantId, $plan);
        if ($usable->where('id', $discount->id)->isEmpty()) {
            throw new \DomainException('This discount code is not applicable to your account or plan.');
        }

        return $discount;
    }

    /**
     * Apply a validated discount to a billing amount.
     *
     * Returns an array:
     *   - 'original_amount'  — base amount before discount
     *   - 'discount_amount'  — how much is saved
     *   - 'final_amount'     — what the tenant actually pays
     *
     * IMPORTANT: Call validate() first. This method trusts the caller validated.
     * Records the DiscountUsage and increments the counter atomically.
     *
     * @return array{original_amount: string, discount_amount: string, final_amount: string}
     */
    public function apply(
        Discount $discount,
        int      $tenantId,
        string   $originalAmount,
        ?string  $subscriptionId = null,
    ): array {
        $discountAmount = $discount->calculateAmount($originalAmount);
        $finalAmount    = bcsub($originalAmount, $discountAmount, 6);

        // Ensure final amount never goes below zero
        if (bccomp($finalAmount, '0', 6) < 0) {
            $discountAmount = $originalAmount;
            $finalAmount    = '0.000000';
        }

        DB::transaction(function () use ($discount, $tenantId, $subscriptionId, $originalAmount, $discountAmount, $finalAmount): void {
            // Write immutable usage record
            DB::table('discount_usages')->insert([
                'discount_id'     => $discount->id,
                'tenant_id'       => $tenantId,
                'subscription_id' => $subscriptionId,
                'original_amount' => $originalAmount,
                'discount_amount' => $discountAmount,
                'final_amount'    => $finalAmount,
                'used_at'         => now(),
            ]);

            // Atomic counter + auto-deplete
            $this->discounts->incrementUsesCount($discount->id);
        });

        DB::afterCommit(fn () => DiscountApplied::dispatch($discount, $tenantId, $discountAmount));

        return compact('original_amount', 'discount_amount', 'final_amount');
    }

    /**
     * Generate a win-back discount for a lapsed tenant and email it to them.
     * Called by the WinbackDiscountJob for tenants lapsed 30-60 days.
     *
     * @throws \RuntimeException if the tenant has already received a winback discount recently
     */
    public function generateWinback(Tenant $tenant): Discount
    {
        // Check they don't already have an active winback code
        $existing = Discount::query()
            ->where('trigger', DiscountTrigger::Winback->value)
            ->where('status', 'active')
            ->whereHas('assignedTenants', fn ($q) => $q->where('tenant_id', $tenant->id))
            ->first();

        if ($existing !== null) {
            throw new \RuntimeException("Tenant {$tenant->id} already has an active win-back discount.");
        }

        $code = 'WINBACK-' . strtoupper(Str::random(6));

        $discountData = new DiscountData(
            code:             $code,
            name:             "Win-Back Offer for {$tenant->name}",
            type:             DiscountType::Percentage,
            value:            '20.000000',   // 20% off
            applicability:    \App\Enums\DiscountApplicability::Specific,
            trigger:          DiscountTrigger::Winback,
            description:      "Auto-generated win-back discount for lapsed tenant {$tenant->id}",
            validUntil:       now()->addDays(30),  // 30-day window to redeem
            maxUses:          null,
            maxUsesPerTenant: 1,
            autoEmail:        true,
            tenantIds:        [$tenant->id],
        );

        $discount = $this->create($discountData, createdByUserId: 0); // 0 = system

        DB::afterCommit(fn () => WinbackDiscountSent::dispatch($discount, $tenant));

        return $discount;
    }

    /**
     * Auto-expire all discounts past their valid_until date.
     * Called daily by the CleanupExpiredDiscountsJob.
     *
     * @return int  Number of discounts expired
     */
    public function expireOutdated(): int
    {
        return DB::table('discounts')
            ->where('status', 'active')
            ->whereNotNull('valid_until')
            ->where('valid_until', '<', now())
            ->update(['status' => 'expired', 'updated_at' => now()]);
    }

    /**
     * Deactivate a discount (platform admin action).
     */
    public function deactivate(string $discountId, int $byUserId): Discount
    {
        $discount = $this->discounts->findOrFail($discountId);
        $this->discounts->update($discountId, ['status' => 'inactive']);

        AuditService::log('deactivated', $discount, ['by_user_id' => $byUserId]);

        return $discount->fresh();
    }
}
