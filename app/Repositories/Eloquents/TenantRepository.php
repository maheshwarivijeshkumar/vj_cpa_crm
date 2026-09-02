<?php

declare(strict_types=1);

namespace App\Repositories\Eloquents;

use App\Filters\TenantFilters;
use App\Models\Tenant;
use App\Repositories\Contracts\TenantRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Request;

final class TenantRepository extends BaseRepository implements TenantRepositoryInterface
{
    protected function model(): string
    {
        return Tenant::class;
    }

    protected function allowedSortColumns(): array
    {
        return ['id', 'name', 'email', 'plan', 'status', 'created_at', 'trial_ends_at'];
    }

    protected function applyFilters(Builder $query, array $filters): void
    {
        // Delegate to the dedicated TenantFilters pipeline
        TenantFilters::applyTo($query, Request::instance());
    }

    public function findBySlug(string $slug): ?Tenant
    {
        return Tenant::query()->where('slug', $slug)->first();
    }

    public function findByUuid(string $uuid): ?Tenant
    {
        return Tenant::query()->where('uuid', $uuid)->first();
    }

    public function existsWithSlug(string $slug, ?int $excludeId = null): bool
    {
        $query = Tenant::query()->where('slug', $slug);
        if ($excludeId !== null) {
            $query->where('id', '!=', $excludeId);
        }
        return $query->exists();
    }

    public function countByStatus(string $status): int
    {
        return (int) Tenant::query()->where('status', $status)->count();
    }

    public function countByPlan(string $plan): int
    {
        return (int) Tenant::query()->where('plan', $plan)->count();
    }

    public function trialExpiringSoon(int $days = 7): Collection
    {
        return Tenant::query()
            ->where('status', 'trial')
            ->where('trial_ends_at', '<=', now()->addDays($days))
            ->where('trial_ends_at', '>', now())
            ->with(['users' => fn ($q) => $q->where('user_type', 'firm_owner')])
            ->orderBy('trial_ends_at')
            ->get();
    }
}
