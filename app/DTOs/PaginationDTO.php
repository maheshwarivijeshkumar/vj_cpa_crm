<?php

declare(strict_types=1);

namespace App\DTOs;

use App\Support\PaginationHelper;
use Illuminate\Http\Request;

/**
 * Immutable DTO carrying resolved pagination + sort parameters.
 * Use PaginationDTO::fromRequest() in every controller that lists records.
 */
final readonly class PaginationDTO
{
    public function __construct(
        public int    $perPage,
        public string $sortBy,
        public string $sortDir,
        public int    $page,
        public string $search,
    ) {}

    /**
     * @param string   $module       Key in config('pagination.modules')
     * @param string[] $allowedSorts Column names that may be sorted by
     */
    public static function fromRequest(
        Request $request,
        string  $module       = '',
        array   $allowedSorts = [],
    ): self {
        [$perPage, $sortBy, $sortDir] = PaginationHelper::fromRequest($request, $module, $allowedSorts);

        return new self(
            perPage: $perPage,
            sortBy:  $sortBy,
            sortDir: $sortDir,
            page:    max(1, $request->integer('page', 1)),
            search:  $request->string('search', '')->trim()->toString(),
        );
    }

    /** Array to pass back to frontend for persisting pagination state. */
    public function toFrontend(): array
    {
        return [
            'per_page'  => $this->perPage,
            'sort_by'   => $this->sortBy,
            'sort_dir'  => $this->sortDir,
            'page'      => $this->page,
            'search'    => $this->search,
        ];
    }

    /** Sorted list of allowed per-page values for the frontend dropdown. */
    public static function perPageOptions(): array
    {
        return PaginationHelper::options();
    }
}
