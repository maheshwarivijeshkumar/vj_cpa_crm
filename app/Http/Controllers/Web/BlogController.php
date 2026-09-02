<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\BlogCategory;
use App\Models\BlogPost;
use App\Models\BlogTag;
use App\Services\Seo\SeoService;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class BlogController extends Controller
{
    /**
     * Blog listing page — paginated, filterable by category/tag/search.
     */
    public function index(Request $request): Response
    {
        $perPage  = 9;
        $category = $request->query('category');
        $tag      = $request->query('tag');
        $search   = $request->query('search');

        $query = BlogPost::query()
            ->published()
            ->with(['category:id,name,slug', 'author:id,first_name,last_name', 'tags:id,name,slug'])
            ->select([
                'id', 'uuid', 'blog_category_id', 'author_id',
                'title', 'slug', 'excerpt', 'featured_image',
                'meta_title', 'meta_description',
                'published_at', 'read_time_minutes', 'view_count',
            ])
            ->orderByDesc('published_at');

        if ($category) {
            $query->forCategory($category);
        }

        if ($tag) {
            $query->whereHas('tags', fn ($q) => $q->where('slug', $tag));
        }

        if ($search) {
            $query->where(function ($q) use ($search): void {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('excerpt', 'like', "%{$search}%");
            });
        }

        $posts = $query->paginate($perPage)->withQueryString();

        // Format posts for frontend (no raw Eloquent in Inertia props)
        $formattedPosts = $posts->through(fn (BlogPost $post) => [
            'id'            => $post->id,
            'title'         => $post->title,
            'slug'          => $post->slug,
            'excerpt'       => $post->excerpt,
            'featured_image'=> $post->featured_image,
            'published_at'  => $post->published_at?->toDateString(),
            'read_time'     => $post->read_time_minutes,
            'view_count'    => $post->view_count,
            'category'      => $post->category ? ['name' => $post->category->name, 'slug' => $post->category->slug] : null,
            'author'        => $post->author  ? ['name' => $post->author->first_name . ' ' . $post->author->last_name] : null,
            'tags'          => $post->tags->map(fn ($t) => ['name' => $t->name, 'slug' => $t->slug])->toArray(),
        ]);

        return Inertia::render('Blog/Index', [
            'seo'        => SeoService::make('blog'),
            'posts'      => $formattedPosts,
            'categories' => BlogCategory::active()
                ->withCount(['publishedPosts as post_count'])
                ->get(['id', 'name', 'slug'])
                ->map(fn ($c) => ['name' => $c->name, 'slug' => $c->slug, 'count' => $c->post_count])
                ->toArray(),
            'tags'       => BlogTag::active()->get(['name', 'slug'])->toArray(),
            'filters'    => [
                'category' => $category,
                'tag'      => $tag,
                'search'   => $search,
            ],
        ]);
    }

    /**
     * Single post page — full content with per-post dynamic SEO.
     */
    public function show(string $slug): Response
    {
        $post = BlogPost::query()
            ->published()
            ->where('slug', $slug)
            ->with(['category:id,name,slug', 'author:id,first_name,last_name,avatar_path', 'tags:id,name,slug'])
            ->firstOrFail();

        // Increment view count asynchronously (no event, no timestamps touch)
        $post->incrementViews();

        // Related posts — same category, exclude current
        $related = BlogPost::query()
            ->published()
            ->where('id', '!=', $post->id)
            ->when($post->blog_category_id, fn ($q) => $q->where('blog_category_id', $post->blog_category_id))
            ->select(['id', 'title', 'slug', 'excerpt', 'featured_image', 'published_at', 'read_time_minutes'])
            ->orderByDesc('published_at')
            ->limit(3)
            ->get()
            ->map(fn (BlogPost $p) => [
                'title'          => $p->title,
                'slug'           => $p->slug,
                'excerpt'        => $p->excerpt,
                'featured_image' => $p->featured_image,
                'published_at'   => $p->published_at?->toDateString(),
                'read_time'      => $p->read_time_minutes,
            ])
            ->toArray();

        $appUrl = rtrim(config('app.url', url('/')), '/');

        // Build fully dynamic per-post SEO with JSON-LD Article schema
        $postUrl  = $appUrl . '/blog/' . $post->slug;
        $ogImage  = $post->og_image ?? $post->featured_image ?? $appUrl . '/images/og-default.png';

        $articleSchema = [
            '@context'        => 'https://schema.org',
            '@type'           => 'BlogPosting',
            'headline'        => $post->seoTitle(),
            'description'     => $post->seoDescription(),
            'image'           => $ogImage,
            'author'          => [
                '@type' => 'Person',
                'name'  => $post->author?->first_name . ' ' . $post->author?->last_name,
            ],
            'publisher'       => [
                '@type' => 'Organization',
                'name'  => config('app.name', 'VJ CPA CRM'),
                'logo'  => ['@type' => 'ImageObject', 'url' => $appUrl . '/images/logo.png'],
            ],
            'datePublished'   => $post->published_at?->toIso8601String(),
            'dateModified'    => $post->updated_at?->toIso8601String(),
            'mainEntityOfPage'=> ['@type' => 'WebPage', '@id' => $postUrl],
        ];

        // Merge post-level schema_json if author has set custom JSON-LD
        if (! empty($post->schema_json)) {
            $articleSchema = array_merge($articleSchema, $post->schema_json);
        }

        $seo = SeoService::dynamic(
            title:       $post->seoTitle(),
            description: $post->seoDescription(),
            canonical:   $post->canonical_url ?? $postUrl,
            image:       $ogImage,
            robots:      $post->robots,
            schema:      $articleSchema,
        );

        // Override OG type to article
        $seo['og']['type'] = 'article';
        if ($post->published_at) {
            $seo['og']['article:published_time'] = $post->published_at->toIso8601String();
        }
        if ($post->category) {
            $seo['og']['article:section'] = $post->category->name;
        }
        $seo['keywords'] = $post->meta_keywords ?? implode(', ', $post->tags->pluck('name')->toArray());

        return Inertia::render('Blog/Show', [
            'seo'  => $seo,
            'post' => [
                'id'             => $post->id,
                'title'          => $post->title,
                'slug'           => $post->slug,
                'excerpt'        => $post->excerpt,
                'body'           => $post->body,
                'featured_image' => $post->featured_image,
                'published_at'   => $post->published_at?->toDateString(),
                'read_time'      => $post->read_time_minutes,
                'view_count'     => $post->view_count,
                'category'       => $post->category ? ['name' => $post->category->name, 'slug' => $post->category->slug] : null,
                'author'         => $post->author ? [
                    'name'        => $post->author->first_name . ' ' . $post->author->last_name,
                    'avatar_path' => $post->author->avatar_path,
                ] : null,
                'tags'           => $post->tags->map(fn ($t) => ['name' => $t->name, 'slug' => $t->slug])->toArray(),
            ],
            'related' => $related,
        ]);
    }
}
