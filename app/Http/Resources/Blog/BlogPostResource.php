<?php

declare(strict_types=1);

namespace App\Http\Resources\Blog;

use App\Enums\BlogPostStatus;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin \App\Models\BlogPost
 */
final class BlogPostResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $isAdmin = $request->user()?->isPlatformAdmin() ?? false;

        return [
            'id'             => $this->id,
            'title'          => $this->title,
            'slug'           => $this->slug,
            'excerpt'        => $this->excerpt,
            'featured_image' => $this->featured_image,
            'read_time'      => $this->read_time_minutes,
            'view_count'     => $this->view_count,
            'published_at'   => $this->published_at?->toDateString(),

            'status' => [
                'value'       => $this->status,
                'label'       => BlogPostStatus::tryFrom($this->status)?->label() ?? $this->status,
                'badge_class' => BlogPostStatus::tryFrom($this->status)?->badgeClass() ?? 'badge-gray',
            ],

            'category' => $this->whenLoaded('category', fn () =>
                $this->category ? ['id' => $this->category->id, 'name' => $this->category->name, 'slug' => $this->category->slug] : null,
            ),
            'author' => $this->whenLoaded('author', fn () =>
                $this->author ? [
                    'id'          => $this->author->id,
                    'name'        => $this->author->full_name,
                    'avatar_path' => $this->author->avatar_path,
                ] : null,
            ),
            'tags' => $this->whenLoaded('tags', fn () =>
                $this->tags->map(fn ($t) => ['id' => $t->id, 'name' => $t->name, 'slug' => $t->slug])
            ),

            'created_at' => $this->created_at?->toIso8601String(),
            'updated_at' => $this->updated_at?->toIso8601String(),

            // Admin-only fields
            'body'             => $this->when($isAdmin, $this->body),
            'meta_title'       => $this->when($isAdmin, $this->meta_title),
            'meta_description' => $this->when($isAdmin, $this->meta_description),
            'meta_keywords'    => $this->when($isAdmin, $this->meta_keywords),
            'canonical_url'    => $this->when($isAdmin, $this->canonical_url),
            'og_title'         => $this->when($isAdmin, $this->og_title),
            'og_description'   => $this->when($isAdmin, $this->og_description),
            'og_image'         => $this->when($isAdmin, $this->og_image),
            'robots'           => $this->when($isAdmin, $this->robots),
        ];
    }
}
