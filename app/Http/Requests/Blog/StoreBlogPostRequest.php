<?php

declare(strict_types=1);

namespace App\Http\Requests\Blog;

use App\Enums\BlogPostStatus;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * StoreBlogPostRequest
 *
 * Validates new blog post creation (platform admin).
 */
final class StoreBlogPostRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->isPlatformAdmin() ?? false;
    }

    /** @return array<string, mixed> */
    public function rules(): array
    {
        return [
            'title'           => ['required', 'string', 'max:255'],
            'slug'            => ['nullable', 'string', 'max:255', 'unique:blog_posts,slug', 'regex:/^[a-z0-9-]+$/'],
            'excerpt'         => ['nullable', 'string', 'max:500'],
            'content'         => ['required', 'string'],
            'status'          => ['required', Rule::enum(BlogPostStatus::class)],
            'category_id'     => ['nullable', 'integer', 'exists:blog_categories,id'],
            'tags'            => ['nullable', 'array'],
            'tags.*'          => ['string', 'max:80'],
            'featured_image'  => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:4096'],
            'meta_title'      => ['nullable', 'string', 'max:255'],
            'meta_description'=> ['nullable', 'string', 'max:320'],
            'meta_keywords'   => ['nullable', 'string', 'max:255'],
            'published_at'    => ['nullable', 'date'],
        ];
    }
}
