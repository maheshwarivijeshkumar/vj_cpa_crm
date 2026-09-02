<?php

declare(strict_types=1);

namespace App\Filters;

use App\Enums\BlogPostStatus;

/** Query filters for the blog post listing. */
final class BlogFilters extends QueryFilter
{
    /** ?search= — search across title, excerpt */
    public function search(string $value): void
    {
        $this->query->where(function ($q) use ($value): void {
            $q->where('title',   'like', "%{$value}%")
              ->orWhere('excerpt', 'like', "%{$value}%");
        });
    }

    /** ?status= */
    public function status(string $value): void
    {
        if (BlogPostStatus::tryFrom($value) !== null) {
            $this->query->where('status', $value);
        }
    }

    /** ?category= — slug of blog_category */
    public function category(string $value): void
    {
        $this->query->whereHas('category', fn ($q) => $q->where('slug', $value));
    }

    /** ?tag= — slug of blog_tag */
    public function tag(string $value): void
    {
        $this->query->whereHas('tags', fn ($q) => $q->where('slug', $value));
    }

    /** ?author_id= */
    public function authorId(string $value): void
    {
        if (is_numeric($value)) {
            $this->query->where('author_id', (int) $value);
        }
    }

    /** ?published_only=1 — only published posts (public API) */
    public function publishedOnly(string $value): void
    {
        if (filter_var($value, FILTER_VALIDATE_BOOLEAN)) {
            $this->query
                ->where('status', 'published')
                ->whereNotNull('published_at')
                ->where('published_at', '<=', now());
        }
    }
}
