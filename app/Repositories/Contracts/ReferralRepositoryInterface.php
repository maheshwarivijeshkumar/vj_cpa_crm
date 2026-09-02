<?php

declare(strict_types=1);

namespace App\Repositories\Contracts;

use App\Models\Referral;
use App\Models\ReferralLink;
use Illuminate\Support\Collection;

interface ReferralRepositoryInterface extends RepositoryInterface
{
    /** Get (or create) the referral link for a tenant. */
    public function linkForTenant(int $tenantId): ReferralLink;

    /** Find a referral link by its code. */
    public function findLinkByCode(string $code): ?ReferralLink;

    /** Record a click on a referral link (atomic increment). */
    public function recordClick(ReferralLink $link, ?string $ip): Referral;

    /** All referrals created by a tenant as referrer. */
    public function forReferrer(int $tenantId): Collection;

    /** Pending referrals that have passed their expiry. */
    public function findExpiredPending(): Collection;

    /** Points/credit balance for a tenant (derived from reward ledger). */
    public function rewardBalance(int $tenantId, string $rewardType): string;

    /** Append a reward entry to the ledger. */
    public function appendReward(
        int    $tenantId,
        string $referralId,
        string $rewardType,
        string $amount,
        string $entryType,
        string $description,
        ?int   $currencyId,
    ): void;

    /** Count successful referrals for a tenant. */
    public function countRewarded(int $tenantId): int;
}
