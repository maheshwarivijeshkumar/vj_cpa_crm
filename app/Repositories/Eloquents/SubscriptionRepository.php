<?php

declare(strict_types=1);

namespace App\Repositories\Eloquents;

use App\Filters\SubscriptionFilters;
use App\Models\Subscription;
use App\Repositories\Contracts\SubscriptionRepositoryInterface;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Request;

final class SubscriptionRepository extends BaseRepository implements SubscriptionRepositoryInterface
{
    protected function model(): string
    {
        return Subscription::class;
    }

    protected function allowedSortColumns(): array
    {
        return ['id', 'plan', 'status', 'starts_at', 'ends_at', 'amount_paid', 'created_at'];
    }

    protected function applyFilters(Builder $query, array $filters): void
    {
        SubscriptionFilters::applyTo($query, Request::instance());
    }

    public function currentForTenant(int $tenantId): ?Subscription
    {
        return Subscription::query()
            ->where('tenant_id', $tenantId)
            ->whereIn('status', ['active', 'trial', 'past_due'])
            ->orderByDesc('starts_at')
            ->with(['currency:id,currency,currency_symbol', 'discount:id,code,name'])
            ->first();
    }

    public function historyForTenant(int $tenantId): Collection
    {
        return Subscription::query()
            ->where('tenant_id', $tenantId)
            ->orderByDesc('starts_at')
            ->with(['currency:id,currency,currency_symbol', 'discount:id,code,name'])
            ->get();
    }

    public function findExpiredUnlapsed(int $graceDays = 0): Collection
    {
        return Subscription::query()
            ->expiredAndNotLapsed($graceDays)
            ->with(['tenant:id,name,email,plan'])
            ->get();
    }

    public function findWinbackCandidates(int $minDays = 30, int $maxDays = 60): Collection
    {
        return Subscription::query()
            ->winbackCandidates($minDays, $maxDays)
            ->with(['tenant:id,name,email,plan'])
            ->get();
    }

    public function countByPlan(string $plan): int
    {
        return (int) Subscription::query()
            ->where('plan', $plan)
            ->whereIn('status', ['active', 'trial'])
            ->count();
    }

    public function mrrSnapshot(): string
    {
        return (string) (DB::table('subscriptions')
            ->join('tenants', 'tenants.id', '=', 'subscriptions.tenant_id')
            ->where('subscriptions.status', 'active')
            ->where('subscriptions.billing_cycle', 'monthly')
            ->sum('subscriptions.amount_paid') ?? '0');
    }
}
