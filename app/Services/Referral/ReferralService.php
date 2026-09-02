<?php

declare(strict_types=1);

namespace App\Services\Referral;

use App\Events\Referral\ReferralRewarded;
use App\Events\Referral\ReferralSignedUp;
use App\Models\Referral;
use App\Models\ReferralLink;
use App\Models\Tenant;
use App\Repositories\Contracts\ReferralRepositoryInterface;
use App\Services\Audit\AuditService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

/**
 * ReferralService — all business logic for the referral & reward system.
 *
 * Flow:
 *   1. Tenant shares their referral link (GET /r/{code})
 *   2. Prospect clicks link → recordClick() → Referral(pending)
 *   3. Prospect completes registration → linkRefereeToReferral() → Referral(signed)
 *   4. Prospect verifies email → markVerified() → Referral(verified)
 *   5. First paid subscription by referee → reward() → Referral(rewarded)
 *        → ReferralReward ledger entry written
 *        → Optionally generate a discount code for the referrer
 *   6. Tenant redeems points/credit on next subscription → redeemBalance()
 */
final class ReferralService
{
    /** Default reward per successful referral (points) */
    private const DEFAULT_POINTS_REWARD = '500.000000';

    /** Default credit reward per successful referral (billing credit, same currency as their plan) */
    private const DEFAULT_CREDIT_REWARD = '10.000000';

    public function __construct(
        private readonly ReferralRepositoryInterface $referrals,
    ) {}

    /**
     * Get or create the referral link for a tenant.
     */
    public function getLinkForTenant(int $tenantId): ReferralLink
    {
        return $this->referrals->linkForTenant($tenantId);
    }

    /**
     * Handle a referral link click.
     * Creates a pending Referral row and increments the link click count.
     *
     * @return Referral  The pending referral record (ID stored in session for later linking)
     */
    public function handleClick(string $code, Request $request): Referral
    {
        $link = $this->referrals->findLinkByCode($code);

        if ($link === null || ! $link->is_active) {
            throw new \DomainException('This referral link is invalid or no longer active.');
        }

        return $this->referrals->recordClick($link, $request->ip());
    }

    /**
     * Link a newly registered tenant to a pending referral.
     * Called by RegisterService if a referral_id is in the session.
     */
    public function linkRefereeToReferral(string $referralId, int $refereeTenantId, string $refereeEmail): void
    {
        $referral = $this->referrals->findOrFail($referralId);

        if ($referral->status->value !== 'pending') {
            return; // Already processed or expired
        }

        DB::table('referrals')
            ->where('id', $referralId)
            ->update([
                'referee_tenant_id' => $refereeTenantId,
                'referee_email'     => $refereeEmail,
                'status'            => 'signed',
                'signed_up_at'      => now(),
                'updated_at'        => now(),
            ]);

        // Increment link signup count
        DB::table('referral_links')
            ->where('id', $referral->referral_link_id)
            ->increment('signup_count');

        DB::afterCommit(fn () => ReferralSignedUp::dispatch($referral->fresh()));
    }

    /**
     * Mark a referral as verified (called when referee verifies their email).
     */
    public function markVerified(int $refereeTenantId): void
    {
        DB::table('referrals')
            ->where('referee_tenant_id', $refereeTenantId)
            ->where('status', 'signed')
            ->update([
                'status'      => 'verified',
                'verified_at' => now(),
                'updated_at'  => now(),
            ]);
    }

    /**
     * Reward the referrer tenant when their referee makes their first paid subscription.
     * This is the core business rule — reward is issued ONLY on verified + first paid sub.
     *
     * @param  int     $refereeTenantId  The tenant who just subscribed
     * @param  string  $subscriptionId   The subscription that triggered the reward
     */
    public function rewardReferrer(int $refereeTenantId, string $subscriptionId): void
    {
        $referral = Referral::query()
            ->where('referee_tenant_id', $refereeTenantId)
            ->where('status', 'verified')
            ->first();

        if ($referral === null) {
            return; // No verified referral to reward
        }

        if ($referral->rewarded_at !== null) {
            return; // Already rewarded (idempotent)
        }

        DB::transaction(function () use ($referral, $subscriptionId): void {
            // Mark referral as rewarded
            DB::table('referrals')
                ->where('id', $referral->id)
                ->update([
                    'status'      => 'rewarded',
                    'rewarded_at' => now(),
                    'updated_at'  => now(),
                ]);

            // Write points reward to ledger
            $pointsBalance = $this->referrals->rewardBalance($referral->referrer_tenant_id, 'points');
            $this->referrals->appendReward(
                tenantId:    $referral->referrer_tenant_id,
                referralId:  $referral->id,
                rewardType:  'points',
                amount:      self::DEFAULT_POINTS_REWARD,
                entryType:   'earn',
                description: "Referral reward for {$referral->refereeTenant?->name}",
                currencyId:  null,
            );

            // Write credit reward to ledger
            $this->referrals->appendReward(
                tenantId:    $referral->referrer_tenant_id,
                referralId:  $referral->id,
                rewardType:  'credit',
                amount:      self::DEFAULT_CREDIT_REWARD,
                entryType:   'earn',
                description: "Referral billing credit for {$referral->refereeTenant?->name}",
                currencyId:  $referral->referrerTenant?->currency_id,
            );
        });

        DB::afterCommit(fn () => ReferralRewarded::dispatch($referral->fresh()));

        AuditService::log('referral_rewarded', $referral, [
            'subscription_id'   => $subscriptionId,
            'referrer_tenant_id'=> $referral->referrer_tenant_id,
        ]);
    }

    /**
     * Redeem points or credit balance against a subscription amount.
     * Returns the credit amount applied (reduces the subscription price).
     *
     * @return string  DECIMAL(20,6) — credit amount applied
     * @throws \DomainException if insufficient balance
     */
    public function redeemBalance(
        int    $tenantId,
        string $rewardType,
        string $requestedAmount,
        string $subscriptionId,
        ?int   $currencyId,
    ): string {
        $balance = $this->referrals->rewardBalance($tenantId, $rewardType);

        if (bccomp($balance, '0', 6) <= 0) {
            throw new \DomainException('Insufficient reward balance to redeem.');
        }

        // Redemption cannot exceed balance or the requested amount
        $redeemAmount = bccomp($requestedAmount, $balance, 6) <= 0
            ? $requestedAmount
            : $balance;

        $this->referrals->appendReward(
            tenantId:    $tenantId,
            referralId:  '', // No specific referral — this is a spend entry
            rewardType:  $rewardType,
            amount:      $redeemAmount,
            entryType:   'spend',
            description: "Redeemed against subscription {$subscriptionId}",
            currencyId:  $currencyId,
        );

        DB::table('referral_redemptions')->insert([
            'tenant_id'        => $tenantId,
            'subscription_id'  => $subscriptionId,
            'reward_type'      => $rewardType,
            'amount_redeemed'  => $redeemAmount,
            'discount_applied' => $redeemAmount,
            'currency_id'      => $currencyId,
            'description'      => "Reward redemption on subscription {$subscriptionId}",
            'redeemed_at'      => now(),
        ]);

        return $redeemAmount;
    }

    /**
     * Get current points and credit balance for a tenant.
     *
     * @return array{points: string, credit: string}
     */
    public function getBalance(int $tenantId): array
    {
        return [
            'points' => $this->referrals->rewardBalance($tenantId, 'points'),
            'credit' => $this->referrals->rewardBalance($tenantId, 'credit'),
        ];
    }

    /**
     * Expire pending referrals that have passed their expiry window.
     * Called daily by CleanupExpiredSessionsJob.
     *
     * @return int  Number of referrals expired
     */
    public function expirePendingReferrals(): int
    {
        $expired = $this->referrals->findExpiredPending();

        if ($expired->isEmpty()) {
            return 0;
        }

        DB::table('referrals')
            ->whereIn('id', $expired->pluck('id'))
            ->update(['status' => 'expired', 'updated_at' => now()]);

        return $expired->count();
    }
}
