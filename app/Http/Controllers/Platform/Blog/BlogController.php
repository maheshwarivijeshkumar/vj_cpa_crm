<?php

declare(strict_types=1);

namespace App\Http\Controllers\Platform\Blog;

use App\DTOs\BlogPostData;
use App\DTOs\PaginationDTO;
use App\Enums\BlogPostStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\Blog\StoreBlogPostRequest;
use App\Http\Requests\Blog\UpdateBlogPostRequest;
use App\Http\Resources\Blog\BlogPostCollection;
use App\Http\Resources\Blog\BlogPostResource;
use App\Models\BlogCategory;
use App\Repositories\Contracts\BlogPostRepositoryInterface;
use App\Services\Audit\AuditService;
use App\Support\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Inertia\Inertia;
use Inertia\Response;

/**
 * Platform BlogController — full CMS for platform admins.
 *
 * Routes: /platform/blog
 * Covers: index, create page, store, edit page, update, publish, unpublish, destroy.
 *
 * Clean controller: validation in Form Requests, business logic in Repository + Service.
 */
final class BlogController extends Controller
{
    public function __construct(
        private readonly BlogPostRepositoryInterface $posts,
    ) {}

    /** Inertia page: posts listing */
    public function index(Request $request): Response
    {
        $pagination = PaginationDTO::fromRequest(
            $request,
            'blog_posts',
            ['id', 'title', 'status', 'published_at', 'created_at', 'view_count'],
        );

        $posts = $this->posts->paginate(
            perPage: $pagination->perPage,
            sortBy:  $pagination->sortBy,
            sortDir: $pagination->sortDir,
            filters: array_filter([
                'search'      => $pagination->search ?: null,
                'status'      => $request->string('status', '')->toString() ?: null,
                'category_id' => $request->integer('category_id') ?: null,
            ]),
            with: ['category:id,name', 'author:id,first_name,last_name'],
        );

        return Inertia::render('Platform/Blog/Index', [
            'posts'       => new BlogPostCollection($posts),
            'filters'     => array_merge($pagination->toFrontend(), [
                'status'      => $request->string('status')->toString(),
                'category_id' => $request->integer('category_id') ?: null,
            ]),
            'perPageOpts' => PaginationDTO::perPageOptions(),
            'categories'  => BlogCategory::query()->orderBy('name')->get(['id', 'name']),
            'statusOptions' => collect(BlogPostStatus::cases())->map(fn ($s) => [
                'value' => $s->value,
                'label' => $s->label(),
            ]),
        ]);
    }

    /** Inertia page: create new post */
    public function create(): Response
    {
        return Inertia::render('Platform/Blog/Form', [
            'post'       => null,
            'categories' => BlogCategory::query()->orderBy('name')->get(['id', 'name']),
        ]);
    }

    /** API: store new post */
    public function store(StoreBlogPostRequest $request): JsonResponse
    {
        $data = $request->validated();

        // Auto-generate slug from title if not provided
        if (empty($data['slug'])) {
            $data['slug'] = Str::slug($data['title']) . '-' . Str::lower(Str::random(4));
        }

        // Handle featured image upload
        if ($request->hasFile('featured_image')) {
            $data['featured_image_url'] = $request->file('featured_image')
                ->store('blog/images', 'public');
            unset($data['featured_image']);
        }

        $tags = $data['tags'] ?? [];
        unset($data['tags']);

        $data['author_id']   = $request->user()->id;
        $data['published_at'] = $data['status'] === BlogPostStatus::Published->value
            ? ($data['published_at'] ?? now())
            : null;

        $postData = BlogPostData::fromArray($data);
        $post     = $this->posts->create($postData->toModelArray());

        // Sync tags
        if (! empty($tags)) {
            $this->posts->syncTags($post->id, $tags);
        }

        AuditService::log('blog_post.created', $post, ['title' => $post->title]);

        return ApiResponse::created(new BlogPostResource($post), 'Blog post created.');
    }

    /** Inertia page: edit existing post */
    public function edit(int $id): Response
    {
        $post = $this->posts->findOrFail($id);
        $post->load(['category:id,name', 'tags:id,name,slug', 'author:id,first_name,last_name']);

        return Inertia::render('Platform/Blog/Form', [
            'post'       => new BlogPostResource($post),
            'categories' => BlogCategory::query()->orderBy('name')->get(['id', 'name']),
        ]);
    }

    /** API: update existing post */
    public function update(UpdateBlogPostRequest $request, int $id): JsonResponse
    {
        $post = $this->posts->findOrFail($id);
        $data = $request->validated();

        if ($request->hasFile('featured_image')) {
            if ($post->featured_image_url) {
                Storage::disk('public')->delete($post->featured_image_url);
            }
            $data['featured_image_url'] = $request->file('featured_image')
                ->store('blog/images', 'public');
            unset($data['featured_image']);
        }

        $tags = $data['tags'] ?? null;
        unset($data['tags']);

        // Set published_at when transitioning to published
        if (isset($data['status']) && $data['status'] === BlogPostStatus::Published->value && ! $post->published_at) {
            $data['published_at'] = $data['published_at'] ?? now();
        }

        $post = $this->posts->update($id, $data);

        if ($tags !== null) {
            $this->posts->syncTags($id, $tags);
        }

        AuditService::log('blog_post.updated', $post, ['fields' => array_keys($data)]);

        return ApiResponse::success(new BlogPostResource($post), 'Post updated.');
    }

    /** API: soft-delete a post */
    public function destroy(int $id): JsonResponse
    {
        $post = $this->posts->findOrFail($id);

        AuditService::log('blog_post.deleted', $post, ['title' => $post->title]);

        $this->posts->delete($id);

        return ApiResponse::noContent('Post deleted.');
    }

    /** API: quick status toggle (publish/unpublish) */
    public function updateStatus(Request $request, int $id): JsonResponse
    {
        $request->validate([
            'status' => ['required', 'string', 'in:draft,published,archived'],
        ]);

        $post = $this->posts->findOrFail($id);

        $data = ['status' => $request->string('status')->toString()];

        if ($data['status'] === 'published' && ! $post->published_at) {
            $data['published_at'] = now();
        }

        $post = $this->posts->update($id, $data);

        return ApiResponse::success(['status' => $post->status], 'Status updated.');
    }
}
