<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

/**
 * @property int $id
 * @property string $uuid
 * @property string $title
 * @property string $slug
 * @property string|null $excerpt
 * @property string $body
 * @property string|null $featured_image
 * @property string $status   draft|review|published|archived
 * @property \Carbon\CarbonImmutable|null $published_at
 * @property int $view_count
 * @property int $read_time_minutes
 * @property string $robots
 */
class BlogPost extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'uuid', 'blog_category_id', 'author_id',
        'title', 'slug', 'excerpt', 'body', 'featured_image',
        'meta_title', 'meta_description', 'meta_keywords',
        'canonical_url', 'og_title', 'og_description', 'og_image',
        'schema_json', 'robots', 'status', 'published_at',
        'view_count', 'read_time_minutes',
    ];

    protected function casts(): array
    {
        return [
            'published_at'     => 'immutable_datetime',
            'schema_json'      => 'array',
            'view_count'       => 'integer',
            'read_time_minutes'=> 'integer',
        ];
    }

    protected static function booted(): void
    {
        static::creating(static function (self $post): void {
            if (empty($post->uuid)) {
                $post->uuid = (string) Str::uuid();
            }
            // Auto-calculate reading time from body word count
            if (empty($post->read_time_minutes) && $post->body) {
                $words = str_word_count(strip_tags($post->body));
                $post->read_time_minutes = (int) max(1, round($words / 200));
            }
        });

        static::updating(static function (self $post): void {
            if ($post->isDirty('body') && $post->body) {
                $words = str_word_count(strip_tags($post->body));
                $post->read_time_minutes = (int) max(1, round($words / 200));
            }
        });
    }

    // ── Relationships ─────────────────────────────────────────────────────────

    public function category(): BelongsTo
    {
        return $this->belongsTo(BlogCategory::class, 'blog_category_id');
    }

    public function author(): BelongsTo
    {
        return $this->belongsTo(User::class, 'author_id');
    }

    public function tags(): BelongsToMany
    {
        return $this->belongsToMany(BlogTag::class, 'blog_post_tag', 'blog_post_id', 'blog_tag_id');
    }

    // ── Scopes ────────────────────────────────────────────────────────────────

    public function scopePublished($query)
    {
        return $query
            ->where('status', 'published')
            ->whereNotNull('published_at')
            ->where('published_at', '<=', now());
    }

    public function scopeForCategory($query, string $slug)
    {
        return $query->whereHas('category', fn ($q) => $q->where('slug', $slug));
    }

    // ── Helpers ───────────────────────────────────────────────────────────────

    public function isPublished(): bool
    {
        return $this->status === 'published'
            && $this->published_at !== null
            && $this->published_at->isPast();
    }

    /** Resolve the best SEO title for this post */
    public function seoTitle(): string
    {
        return $this->meta_title ?: $this->title;
    }

    /** Resolve the best SEO description */
    public function seoDescription(): string
    {
        return $this->meta_description
            ?: ($this->excerpt ?: Str::limit(strip_tags($this->body), 155));
    }

    /** Increment view counter without triggering model events */
    public function incrementViews(): void
    {
        static::withoutTimestamps(fn () => $this->increment('view_count'));
    }
}
