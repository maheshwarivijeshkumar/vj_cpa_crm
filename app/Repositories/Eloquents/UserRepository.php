<?php

namespace App\Repositories\Eloquents;

use App\Models\User;
use App\Repositories\Contracts\UserRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;

class UserRepository extends BaseRepository implements UserRepositoryInterface
{
    protected function model(): string
    {
        return User::class;
    }

    protected function allowedSortColumns(): array
    {
        return ['id', 'first_name', 'last_name', 'email', 'user_type', 'status', 'created_at'];
    }

    protected function applyFilters(Builder $query, array $filters): void
    {
        if (! empty($filters['search'])) {
            $s = $filters['search'];
            $query->where(function (Builder $q) use ($s): void {
                $q->where('first_name', 'like', "%{$s}%")
                  ->orWhere('last_name',  'like', "%{$s}%")
                  ->orWhere('email',      'like', "%{$s}%")
                  ->orWhere('username',   'like', "%{$s}%");
            });
        }
        if (! empty($filters['user_type'])) {
            $query->where('user_type', $filters['user_type']);
        }
        if (! empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }
        if (! empty($filters['tenant_id'])) {
            $query->where('tenant_id', $filters['tenant_id']);
        }
        if (array_key_exists('with_trashed', $filters) && $filters['with_trashed']) {
            $query->withTrashed();
        }
    }

    public function findByEmail(string $email): ?User
    {
        return User::query()->where('email', $email)->first();
    }

    public function findByUuid(string $uuid): ?User
    {
        return User::query()->where('uuid', $uuid)->first();
    }

    public function forTenant(
        int    $tenantId,
        int    $perPage  = 25,
        string $sortBy   = 'created_at',
        string $sortDir  = 'desc',
        array  $filters  = [],
    ): LengthAwarePaginator {
        return $this->paginate(
            $perPage,
            $sortBy,
            $sortDir,
            array_merge($filters, ['tenant_id' => $tenantId]),
            ['roles:id,slug,name', 'office:id,name'],
        );
    }

    public function platformAdmins(): Collection
    {
        return User::query()
            ->where('user_type', 'platform_admin')
            ->whereNull('tenant_id')
            ->get();
    }

    public function existsWithEmail(string $email, ?int $excludeId = null): bool
    {
        $query = User::query()->where('email', $email);
        if ($excludeId) {
            $query->where('id', '!=', $excludeId);
        }
        return $query->exists();
    }
}
