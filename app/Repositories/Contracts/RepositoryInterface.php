<?php

namespace App\Repositories\Contracts;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

/**
 * Generic CRUD contract for all repositories.
 * Business-specific query methods are added in concrete repository interfaces.
 */
interface RepositoryInterface
{
    public function find(int|string $id): ?Model;

    public function findOrFail(int|string $id): Model;

    public function all(array $columns = ['*']): Collection;

    /** @param  array<string,mixed>  $filters */
    public function paginate(
        int    $perPage   = 25,
        string $sortBy    = 'created_at',
        string $sortDir   = 'desc',
        array  $filters   = [],
        array  $with      = [],
        array  $columns   = ['*'],
    ): LengthAwarePaginator;

    /** @param  array<string,mixed>  $data */
    public function create(array $data): Model;

    /** @param  array<string,mixed>  $data */
    public function update(int|string $id, array $data): Model;

    public function delete(int|string $id): bool;

    public function restore(int|string $id): bool;
}
