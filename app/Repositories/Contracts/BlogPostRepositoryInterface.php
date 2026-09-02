<?php

declare(strict_types=1);

namespace App\Repositories\Contracts;

use App\Models\BlogPost;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface BlogPostRepositoryInterface extends RepositoryInterface
{
    public function findBySlug(string $slug): ?BlogPost;
    public function publishedBySlug(string $slug): ?BlogPost;
    public function published(int $perPage = 9, string $sortBy = 'published_at', string $sortDir = 'desc', array $filters = []): LengthAwarePaginator;
    public function relatedTo(BlogPost $post, int $limit = 3): \Illuminate\Support\Collection;
    public function incrementViews(int $id): void;
}
