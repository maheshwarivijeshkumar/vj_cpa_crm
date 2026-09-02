<?php

namespace App\Repositories\Eloquents;

use App\Repositories\Contracts\RepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Collection;

/**
 * Base repository implementing RepositoryInterface.
 *
 * Concrete repositories extend this and override:
 *   - model()             → return Model::class
 *   - applyFilters()      → add query constraints from $filters array
 *   - allowedSortColumns()→ list of safely sortable column names
 */
abstract class BaseRepository implements RepositoryInterface
{
    protected Model $model;

    public function __construct()
    {
        $this->model = app($this->model());
    }

    /** Return the fully-qualified model class name. */
    abstract protected function model(): string;

    /**
     * List of column names that can be sorted by via the sort_by query param.
     * Override in concrete repositories to expand.
     *
     * @return string[]
     */
    protected function allowedSortColumns(): array
    {
        return ['id', 'created_at', 'updated_at'];
    }

    /**
     * Apply filter conditions to the query.
     * Override in concrete repositories.
     *
     * @param  Builder             $query
     * @param  array<string,mixed> $filters
     */
    protected function applyFilters(Builder $query, array $filters): void
    {
        // Base: support simple equality filters keyed as column => value
        foreach ($filters as $column => $value) {
            if ($value === null || $value === '') {
                continue;
            }
            if (in_array($column, $this->allowedSortColumns(), true)) {
                $query->where($column, $value);
            }
        }
    }

    // ── RepositoryInterface implementation ─────────────────────────────────

    public function find(int|string $id): ?Model
    {
        return $this->model->newQuery()->find($id);
    }

    public function findOrFail(int|string $id): Model
    {
        return $this->model->newQuery()->findOrFail($id);
    }

    public function all(array $columns = ['*']): Collection
    {
        return $this->model->newQuery()->get($columns);
    }

    public function paginate(
        int    $perPage  = 25,
        string $sortBy   = 'created_at',
        string $sortDir  = 'desc',
        array  $filters  = [],
        array  $with     = [],
        array  $columns  = ['*'],
    ): LengthAwarePaginator {
        $query = $this->model->newQuery();

        // Eager loading
        if (! empty($with)) {
            $query->with($with);
        }

        // Safe sort
        $allowed = $this->allowedSortColumns();
        if (! in_array($sortBy, $allowed, true)) {
            $sortBy = 'created_at';
        }
        $sortDir = in_array(strtolower($sortDir), ['asc', 'desc'], true) ? $sortDir : 'desc';

        // Filters
        $this->applyFilters($query, $filters);

        return $query
            ->orderBy($sortBy, $sortDir)
            ->paginate($perPage, $columns)
            ->withQueryString();
    }

    public function create(array $data): Model
    {
        return $this->model->newQuery()->create($data);
    }

    public function update(int|string $id, array $data): Model
    {
        $record = $this->findOrFail($id);
        $record->update($data);
        return $record->fresh();
    }

    public function delete(int|string $id): bool
    {
        $record = $this->findOrFail($id);
        return (bool) $record->delete();
    }

    public function restore(int|string $id): bool
    {
        // Only works when model uses SoftDeletes
        if (! in_array(SoftDeletes::class, class_uses_recursive($this->model), true)) {
            return false;
        }
        $record = $this->model->newQuery()->withTrashed()->findOrFail($id);
        return (bool) $record->restore();
    }

    // ── Protected query helpers for concrete repositories ──────────────────

    protected function newQuery(): Builder
    {
        return $this->model->newQuery();
    }
}
