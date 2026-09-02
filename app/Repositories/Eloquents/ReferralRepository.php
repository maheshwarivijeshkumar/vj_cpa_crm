<?php

declare(strict_types=1);

namespace App\Repositories\Eloquents;

use App\Models\Referral;
use App\Models\ReferralLink;
use App\Models\ReferralReward;
use App\Repositories\Contracts\ReferralRepositoryInterface;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

final class ReferralRepository extends BaseRepository implements ReferralRepositoryInterface
{
    protected function model(): string
    {
        return Referral::class;
    }

    protected function allowedSortColumns(): array
    {
        return ['id', 'status', 'clicked_at', 'signed_up_at', 'rewarded_at', 'created_at'];
    }

    protected function applyFilters(Builder $query, array $filters): void
    {
        // Delegate to ReferralFilters when implemented
    }

    public function linkForTenant(int $tenantId): ReferralLink
    {
        return ReferralLink::firstOrCreate(
            ['tenant_id' => $tenantId],
            ['code' => 'REF-' . strtoupper(Str::random(6)), 'is_active' => true],
        );
    }

    public function findLinkByCode(string $code): ?ReferralLink
    {
        return ReferralLink::query()
            ->where('code', strtoupper($code))
            ->where('is_active', true)
            ->first();
    }

    public function recordClick(ReferralLink $link, ?string $ip): Referral
    {
        // Increment link click count atomically
        DB::table('referral_links')->where('id', $link->id)->increment('click_count');

        return Referral::create([
            'referrer_tenant_id' => $link->tenant_id,
            'referral_link_id'   => $link->id,
            'referee_ip'         => $ip,
            'status'             => 'pending',
            'clicked_at'         => now(),
            'expires_at'         => now()->addDays(config('cpa.referral_expiry_days', 30)),
        ]);
    }

    public function forReferrer(int $tenantId): Collection
    {
        return Referral::query()
            ->where('referrer_tenant_id', $tenantId)
            ->with(['refereeTenant:id,name', 'rewards'])
            ->orderByDesc('clicked_at')
            ->get();
    }

    public function findExpiredPending(): Collection
    {
        return Referral::query()
            ->expiring()
            ->with(['referrerTenant:id,name'])
            ->get();
    }

    public function rewardBalance(int $tenantId, string $rewardType): string
    {
        // Balance = sum of earn entries - sum of spend entries
        $earn = (string) (DB::table('referral_rewards')
            ->where('tenant_id', $tenantId)
            ->where('reward_type', $rewardType)
            ->where('entry_type', 'earn')
            ->sum('amount') ?? '0');

        $spend = (string) (DB::table('referral_rewards')
            ->where('tenant_id', $tenantId)
            ->where('reward_type', $rewardType)
            ->whereIn('entry_type', ['spend', 'expire'])
            ->sum('amount') ?? '0');

        return bcsub($earn, $spend, 6);
    }

    public function appendReward(
        int    $tenantId,
        string $referralId,
        string $rewardType,
        string $amount,
        string $entryType,
        string $description,
        ?int   $currencyId,
    ): void {
        $balance = $this->rewardBalance($tenantId, $rewardType);

        $balanceAfter = $entryType === 'earn'
            ? bcadd($balance, $amount, 6)
            : bcsub($balance, $amount, 6);

        DB::table('referral_rewards')->insert([
            'tenant_id'     => $tenantId,
            'referral_id'   => $referralId,
            'reward_type'   => $rewardType,
            'amount'        => $amount,
            'currency_id'   => $currencyId,
            'description'   => $description,
            'entry_type'    => $entryType,
            'balance_after' => $balanceAfter,
            'created_at'    => now(),
        ]);
    }

    public function countRewarded(int $tenantId): int
    {
        return (int) Referral::query()
            ->where('referrer_tenant_id', $tenantId)
            ->where('status', 'rewarded')
            ->count();
    }
}
