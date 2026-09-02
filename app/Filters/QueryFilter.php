<?php

declare(strict_types=1);

namespace App\Filters;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

/**
 * QueryFilter — base class for all Eloquent query filter pipelines.
 *
 * Usage in a repository:
 *   UserFilters::apply($query, $request);
 *
 * Or via the pipeline:
 *   $pipeline->send($query)->through([UserFilters::class])->thenReturn();
 *
 * Each concrete filter class implements filter methods named after
 * request parameters, e.g. filterBySearch(), filterByStatus().
 * Methods are called automatically when the corresponding request param is present.
 */
abstract class QueryFilter
{
    protected Builder $query;
    protected Request $request;

    final public function __construct(Builder $query, Request $request)
    {
        $this->query   = $query;
        $this->request = $request;
    }

    /**
     * Apply all filter methods whose corresponding request param is non-empty.
     * Method naming convention: camelCase from snake_case key.
     * e.g. request param 'sort_by' → method 'sortBy'
     *
     * @return Builder
     */
    final public function apply(): Builder
    {
        foreach ($this->request->all() as $key => $value) {
            if ($value === null || $value === '') {
                continue;
            }

            $method = $this->paramToMethod($key);

            if (method_exists($this, $method)) {
                $this->{$method}($value);
            }
        }

        return $this->query;
    }

    /**
     * Static convenience wrapper — creates the filter and applies it.
     */
    final public static function applyTo(Builder $query, Request $request): Builder
    {
        return (new static($query, $request))->apply();
    }

    /**
     * Convert snake_case param name to camelCase method name.
     * e.g. 'sort_by' → 'sortBy', 'with_trashed' → 'withTrashed'
     */
    private function paramToMethod(string $param): string
    {
        return lcfirst(str_replace('_', '', ucwords($param, '_')));
    }
}
