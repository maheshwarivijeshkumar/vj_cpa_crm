<?php

declare(strict_types=1);

namespace App\Repositories\Contracts;

use App\Models\Tenant;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface TenantRepositoryInterface extends RepositoryInterface
{
    public function findBySlug(string $slug): ?Tenant;
    public function findByUuid(string $uuid): ?Tenant;
    public function existsWithSlug(string $slug, ?int $excludeId = null): bool;
    public function paginate(int $perPage = 25, string $sortBy = 'created_at', string $sortDir = 'desc', array $filters = [], array $with = [], array $columns = ['*']): LengthAwarePaginator;
    public function countByStatus(string $status): int;
    public function countByPlan(string $plan): int;
    /** Tenants expiring trial within $days days */
    public function trialExpiringSoon(int $days = 7): \Illuminate\Support\Collection;
}
