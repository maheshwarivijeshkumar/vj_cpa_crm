<?php

namespace App\Repositories\Contracts;

use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

interface UserRepositoryInterface extends RepositoryInterface
{
    public function findByEmail(string $email): ?User;

    public function findByUuid(string $uuid): ?User;

    /** Users belonging to a specific tenant. */
    public function forTenant(
        int    $tenantId,
        int    $perPage  = 25,
        string $sortBy   = 'created_at',
        string $sortDir  = 'desc',
        array  $filters  = [],
    ): LengthAwarePaginator;

    /** Platform admin users (tenant_id = null). */
    public function platformAdmins(): Collection;

    public function existsWithEmail(string $email, ?int $excludeId = null): bool;
}
