<?php

declare(strict_types=1);

namespace App\Repositories\Eloquents;

use App\Filters\BlogFilters;
use App\Models\BlogPost;
use App\Repositories\Contracts\BlogPostRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Request;

final class BlogPostRepository extends BaseRepository implements BlogPostRepositoryInterface
{
    protected function model(): string
    {
        return BlogPost::class;
    }

    protected function allowedSortColumns(): array
    {
        return ['id', 'title', 'status', 'published_at', 'view_count', 'created_at'];
    }

    protected function applyFilters(Builder $query, array $filters): void
    {
        BlogFilters::applyTo($query, Request::instance());
    }

    public function findBySlug(string $slug): ?BlogPost
    {
        return BlogPost::query()->where('slug', $slug)->first();
    }

    public function publishedBySlug(string $slug): ?BlogPost
    {
        return BlogPost::query()
            ->published()
            ->where('slug', $slug)
            ->with(['category:id,name,slug', 'author:id,first_name,last_name,avatar_path', 'tags:id,name,slug'])
            ->first();
    }

    public function published(
        int    $perPage = 9,
        string $sortBy  = 'published_at',
        string $sortDir = 'desc',
        array  $filters = [],
    ): LengthAwarePaginator {
        return BlogPost::query()
            ->published()
            ->with(['category:id,name,slug', 'author:id,first_name,last_name', 'tags:id,name,slug'])
            ->select(['id','blog_category_id','author_id','title','slug','excerpt','featured_image','published_at','read_time_minutes','view_count'])
            ->orderBy($sortBy, $sortDir)
            ->paginate($perPage)
            ->withQueryString();
    }

    public function relatedTo(BlogPost $post, int $limit = 3): Collection
    {
        return BlogPost::query()
            ->published()
            ->where('id', '!=', $post->id)
            ->when($post->blog_category_id, fn ($q) => $q->where('blog_category_id', $post->blog_category_id))
            ->select(['id','title','slug','excerpt','featured_image','published_at','read_time_minutes'])
            ->orderByDesc('published_at')
            ->limit($limit)
            ->get();
    }

    public function incrementViews(int $id): void
    {
        // Raw UPDATE to avoid race condition & skip model events/timestamps
        DB::table('blog_posts')->where('id', $id)->increment('view_count');
    }
}
