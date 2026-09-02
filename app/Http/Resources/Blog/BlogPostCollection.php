<?php

declare(strict_types=1);

namespace App\Http\Resources\Blog;

use App\Support\PaginationHelper;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;

final class BlogPostCollection extends ResourceCollection
{
    public string $collects = BlogPostResource::class;

    public function toArray(Request $request): array
    {
        return [
            'data' => $this->collection,
            'meta' => [
                'current_page'     => $this->currentPage(),
                'last_page'        => $this->lastPage(),
                'per_page'         => $this->perPage(),
                'total'            => $this->total(),
                'from'             => $this->firstItem(),
                'to'               => $this->lastItem(),
                'per_page_options' => PaginationHelper::options(),
            ],
        ];
    }
}
