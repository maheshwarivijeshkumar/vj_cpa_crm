<?php

declare(strict_types=1);

namespace App\DTOs;

use App\Enums\BlogPostStatus;
use Illuminate\Support\Carbon;

/** Immutable DTO for creating or updating a BlogPost. */
final readonly class BlogPostData
{
    public function __construct(
        public string         $title,
        public string         $slug,
        public string         $body,
        public BlogPostStatus $status,
        public ?int           $blogCategoryId = null,
        public ?int           $authorId       = null,
        public ?string        $excerpt        = null,
        public ?string        $featuredImage  = null,
        public ?string        $metaTitle      = null,
        public ?string        $metaDescription = null,
        public ?string        $metaKeywords   = null,
        public ?string        $canonicalUrl   = null,
        public ?string        $ogTitle        = null,
        public ?string        $ogDescription  = null,
        public ?string        $ogImage        = null,
        public array          $schemaJson     = [],
        public string         $robots         = 'index,follow',
        public ?Carbon        $publishedAt    = null,
        public array          $tagIds         = [],
    ) {}

    public static function fromArray(array $v): self
    {
        return new self(
            title:           $v['title'],
            slug:            $v['slug'],
            body:            $v['body'],
            status:          BlogPostStatus::from($v['status'] ?? 'draft'),
            blogCategoryId:  $v['blog_category_id'] ?? null,
            authorId:        $v['author_id']         ?? null,
            excerpt:         $v['excerpt']            ?? null,
            featuredImage:   $v['featured_image']     ?? null,
            metaTitle:       $v['meta_title']         ?? null,
            metaDescription: $v['meta_description']   ?? null,
            metaKeywords:    $v['meta_keywords']      ?? null,
            canonicalUrl:    $v['canonical_url']      ?? null,
            ogTitle:         $v['og_title']           ?? null,
            ogDescription:   $v['og_description']     ?? null,
            ogImage:         $v['og_image']           ?? null,
            schemaJson:      $v['schema_json']        ?? [],
            robots:          $v['robots']             ?? 'index,follow',
            publishedAt:     isset($v['published_at'])
                ? Carbon::parse($v['published_at'])
                : null,
            tagIds:          $v['tag_ids']            ?? [],
        );
    }

    public function toModelArray(): array
    {
        return array_filter([
            'title'            => $this->title,
            'slug'             => $this->slug,
            'body'             => $this->body,
            'status'           => $this->status->value,
            'blog_category_id' => $this->blogCategoryId,
            'author_id'        => $this->authorId,
            'excerpt'          => $this->excerpt,
            'featured_image'   => $this->featuredImage,
            'meta_title'       => $this->metaTitle,
            'meta_description' => $this->metaDescription,
            'meta_keywords'    => $this->metaKeywords,
            'canonical_url'    => $this->canonicalUrl,
            'og_title'         => $this->ogTitle,
            'og_description'   => $this->ogDescription,
            'og_image'         => $this->ogImage,
            'schema_json'      => empty($this->schemaJson) ? null : $this->schemaJson,
            'robots'           => $this->robots,
            'published_at'     => $this->publishedAt?->toDateTimeString(),
        ], fn ($v) => $v !== null);
    }
}
