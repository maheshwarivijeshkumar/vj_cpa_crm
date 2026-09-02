<?php

namespace App\Support;

use Illuminate\Http\Request;

/**
 * Stateless helper for resolving consistent per-page, sort-by, and sort-dir
 * values from an HTTP request, with safe defaults and validation.
 *
 * Usage in controllers / repositories:
 *   [$perPage, $sortBy, $sortDir] = PaginationHelper::fromRequest($request, 'clients');
 */
final class PaginationHelper
{
    /**
     * Resolve per-page, sort-by, and sort-dir from the current request.
     *
     * @param  string   $module   Key in config('pagination.modules')
     * @param  string[] $allowedSorts  Columns that may be sorted by
     * @return array{0: int, 1: string, 2: string}   [perPage, sortBy, sortDir]
     */
    public static function fromRequest(
        Request $request,
        string  $module = '',
        array   $allowedSorts = [],
    ): array {
        $defaults = config("pagination.modules.{$module}", []);

        // ── Per page ──────────────────────────────────────────────────────────
        $default  = (int) ($defaults['per_page'] ?? config('pagination.default_per_page', 25));
        $max      = (int) config('pagination.max_per_page', 200);
        $options  = config('pagination.per_page_options', [10, 25, 50, 100]);
        $perPage  = $request->integer('per_page', $default);

        // Only allow listed options (if options defined); else clamp to max
        if (! empty($options) && ! in_array($perPage, $options, true)) {
            $perPage = $default;
        }
        $perPage = max(1, min($perPage, $max));

        // ── Sort by ───────────────────────────────────────────────────────────
        $defaultSort = $defaults['sort_by'] ?? 'created_at';
        $sortBy      = $request->string('sort_by', $defaultSort)->toString();
        if (! empty($allowedSorts) && ! in_array($sortBy, $allowedSorts, true)) {
            $sortBy = $defaultSort;
        }

        // ── Sort direction ────────────────────────────────────────────────────
        $defaultDir = $defaults['sort_dir'] ?? config('pagination.default_sort_direction', 'desc');
        $sortDir    = strtolower($request->string('sort_dir', $defaultDir)->toString());
        if (! in_array($sortDir, ['asc', 'desc'], true)) {
            $sortDir = $defaultDir;
        }

        return [$perPage, $sortBy, $sortDir];
    }

    /**
     * Return the per-page options array for the frontend.
     */
    public static function options(): array
    {
        return config('pagination.per_page_options', [10, 25, 50, 100]);
    }
}
