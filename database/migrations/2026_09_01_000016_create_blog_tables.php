<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Blog system — 3NF compliant, fully SEO-ready.
 *
 * Tables:
 *   blog_categories  — flat category list
 *   blog_posts       — articles (belongs to category, author)
 *   blog_tags        — flat tag list
 *   blog_post_tag    — pivot
 */
return new class extends Migration
{
    public function up(): void
    {
        // ── Categories ────────────────────────────────────────────────────────
        Schema::create('blog_categories', function (Blueprint $table): void {
            $table->id();
            $table->string('name', 100);
            $table->string('slug', 120)->unique();
            $table->text('description')->nullable();
            $table->string('meta_title')->nullable();
            $table->text('meta_description')->nullable();
            $table->integer('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // ── Tags ──────────────────────────────────────────────────────────────
        Schema::create('blog_tags', function (Blueprint $table): void {
            $table->id();
            $table->string('name', 80);
            $table->string('slug', 100)->unique();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // ── Posts ─────────────────────────────────────────────────────────────
        Schema::create('blog_posts', function (Blueprint $table): void {
            $table->id();
            $table->uuid('uuid')->unique();

            // Ownership
            $table->foreignId('blog_category_id')
                ->nullable()
                ->constrained('blog_categories')
                ->nullOnDelete();

            $table->foreignId('author_id')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            // Content
            $table->string('title');
            $table->string('slug')->unique();
            $table->text('excerpt')->nullable();         // Short summary for listing
            $table->longText('body');                    // Full HTML/Markdown content
            $table->string('featured_image')->nullable(); // Storage path or URL

            // SEO fields (per-post, fully dynamic)
            $table->string('meta_title')->nullable();
            $table->text('meta_description')->nullable();
            $table->string('meta_keywords')->nullable();
            $table->string('canonical_url')->nullable();
            $table->string('og_title')->nullable();
            $table->text('og_description')->nullable();
            $table->string('og_image')->nullable();
            $table->json('schema_json')->nullable();
            $table->enum('robots', [
                'index,follow', 'noindex,follow', 'index,nofollow', 'noindex,nofollow',
            ])->default('index,follow');

            // Publishing
            $table->enum('status', ['draft', 'review', 'published', 'archived'])->default('draft');
            $table->timestamp('published_at')->nullable();

            // Analytics helpers
            $table->unsignedInteger('view_count')->default(0);
            $table->unsignedInteger('read_time_minutes')->default(0);

            $table->timestamps();
            $table->softDeletes();

            $table->index(['status', 'published_at']);
            $table->index('blog_category_id');
            $table->index('author_id');
        });

        // ── Post–Tag pivot ────────────────────────────────────────────────────
        Schema::create('blog_post_tag', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('blog_post_id')
                ->constrained('blog_posts')
                ->cascadeOnDelete();
            $table->foreignId('blog_tag_id')
                ->constrained('blog_tags')
                ->cascadeOnDelete();
            $table->unique(['blog_post_id', 'blog_tag_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('blog_post_tag');
        Schema::dropIfExists('blog_posts');
        Schema::dropIfExists('blog_tags');
        Schema::dropIfExists('blog_categories');
    }
};
