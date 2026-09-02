<script setup lang="ts">
import MarketingLayout from '@/layouts/MarketingLayout.vue'
import SeoHead from '@/components/ui/SeoHead.vue'
import { Link } from '@inertiajs/vue3'
import { Clock, Eye, Tag, ChevronRight, ArrowLeft, User, BookOpen } from '@lucide/vue'

defineOptions({ layout: MarketingLayout })

interface TagItem  { name: string; slug: string }
interface RelatedPost { title: string; slug: string; excerpt: string | null; featured_image: string | null; published_at: string | null; read_time: number }

const props = defineProps<{
    seo?: Record<string, unknown>
    post: {
        id: number; title: string; slug: string; excerpt: string | null
        body: string; featured_image: string | null
        published_at: string | null; read_time: number; view_count: number
        category: { name: string; slug: string } | null
        author:   { name: string; avatar_path: string | null } | null
        tags:     TagItem[]
    }
    related: RelatedPost[]
}>()

function formatDate(d: string | null) {
    if (!d) return ''
    return new Date(d).toLocaleDateString('en-CA', { year: 'numeric', month: 'long', day: 'numeric' })
}

function authorInitials(name: string) {
    return name.split(' ').map(n => n[0]).join('').toUpperCase().slice(0, 2)
}
</script>

<template>
    <SeoHead :seo="(seo as any)" />

    <div class="post-page">
        <div class="marketing-container">

            <!-- ── Back breadcrumb ─────────────────────────────────────── -->
            <div class="post-breadcrumb">
                <Link href="/blog" class="post-back-link">
                    <ArrowLeft :size="14" /> Blog
                </Link>
                <span class="post-breadcrumb-sep" aria-hidden="true">/</span>
                <span v-if="post.category">
                    <Link :href="`/blog?category=${post.category.slug}`" class="post-back-link">
                        {{ post.category.name }}
                    </Link>
                    <span class="post-breadcrumb-sep" aria-hidden="true">/</span>
                </span>
                <span class="post-breadcrumb-current">{{ post.title }}</span>
            </div>

            <div class="post-layout">
                <!-- ── Article ───────────────────────────────────────── -->
                <article class="post-article" itemscope itemtype="https://schema.org/BlogPosting">

                    <!-- Category -->
                    <div v-if="post.category" class="post-category-pill">
                        <Link :href="`/blog?category=${post.category.slug}`">
                            {{ post.category.name }}
                        </Link>
                    </div>

                    <!-- Title -->
                    <h1 class="post-title" itemprop="headline">{{ post.title }}</h1>

                    <!-- Meta bar -->
                    <div class="post-meta-bar">
                        <!-- Author avatar + name -->
                        <div v-if="post.author" class="post-author">
                            <div class="post-author-avatar">
                                <img
                                    v-if="post.author.avatar_path"
                                    :src="post.author.avatar_path"
                                    :alt="post.author.name"
                                />
                                <span v-else>{{ authorInitials(post.author.name) }}</span>
                            </div>
                            <span class="post-author-name" itemprop="author">{{ post.author.name }}</span>
                        </div>

                        <span class="post-meta-sep" aria-hidden="true">·</span>
                        <time
                            v-if="post.published_at"
                            :datetime="post.published_at"
                            itemprop="datePublished"
                        >
                            {{ formatDate(post.published_at) }}
                        </time>
                        <span class="post-meta-sep" aria-hidden="true">·</span>
                        <span class="flex items-center gap-1">
                            <Clock :size="13" /> {{ post.read_time }} min read
                        </span>
                        <span class="post-meta-sep" aria-hidden="true">·</span>
                        <span class="flex items-center gap-1">
                            <Eye :size="13" /> {{ post.view_count }} views
                        </span>
                    </div>

                    <!-- Featured image -->
                    <div v-if="post.featured_image" class="post-featured-image">
                        <img
                            :src="post.featured_image"
                            :alt="post.title"
                            itemprop="image"
                            loading="eager"
                        />
                    </div>

                    <!-- Body -->
                    <div
                        class="post-body prose"
                        itemprop="articleBody"
                        v-html="post.body"
                    />

                    <!-- Tags -->
                    <div v-if="post.tags.length" class="post-tags">
                        <Tag :size="14" class="text-cpa-text-muted flex-shrink-0" />
                        <Link
                            v-for="tag in post.tags"
                            :key="tag.slug"
                            :href="`/blog?tag=${tag.slug}`"
                            class="post-tag"
                        >
                            {{ tag.name }}
                        </Link>
                    </div>

                    <!-- Author box -->
                    <div v-if="post.author" class="post-author-box">
                        <div class="post-author-box-avatar">
                            <img
                                v-if="post.author.avatar_path"
                                :src="post.author.avatar_path"
                                :alt="post.author.name"
                            />
                            <span v-else>{{ authorInitials(post.author.name) }}</span>
                        </div>
                        <div>
                            <p class="post-author-box-name">{{ post.author.name }}</p>
                            <p class="post-author-box-bio">
                                Published by the VJ CPA CRM team. We write about practice management,
                                tax compliance, and accounting technology for Canadian CPA firms.
                            </p>
                        </div>
                    </div>

                </article>

                <!-- ── Sticky sidebar ────────────────────────────────── -->
                <aside class="post-sidebar">
                    <div class="post-sidebar-card">
                        <p class="post-sidebar-cta-label">Ready to streamline your practice?</p>
                        <p class="post-sidebar-cta-body">
                            Join thousands of CPA firms managing clients, filings, and workflows with VJ CPA CRM.
                        </p>
                        <Link href="/register" class="post-sidebar-cta-btn">
                            Start free trial <ChevronRight :size="14" />
                        </Link>
                        <Link href="/demo" class="post-sidebar-cta-link">
                            Watch the demo
                        </Link>
                    </div>
                </aside>
            </div>

            <!-- ── Related posts ──────────────────────────────────────── -->
            <section v-if="related.length" class="post-related">
                <h2 class="post-related-title">Related articles</h2>
                <div class="post-related-grid">
                    <article
                        v-for="rel in related"
                        :key="rel.slug"
                        class="post-related-card"
                    >
                        <Link :href="`/blog/${rel.slug}`" class="post-related-image-wrap">
                            <div v-if="rel.featured_image" class="post-related-image">
                                <img :src="rel.featured_image" :alt="rel.title" loading="lazy" />
                            </div>
                            <div v-else class="post-related-image-placeholder">
                                <BookOpen :size="22" class="text-cpa-medium-light" />
                            </div>
                        </Link>
                        <div class="post-related-body">
                            <h3 class="post-related-post-title">
                                <Link :href="`/blog/${rel.slug}`">{{ rel.title }}</Link>
                            </h3>
                            <p v-if="rel.excerpt" class="post-related-excerpt">{{ rel.excerpt }}</p>
                            <div class="post-related-meta">
                                <Clock :size="12" /> {{ rel.read_time }} min
                                <span class="mx-1 text-gray-300">·</span>
                                {{ formatDate(rel.published_at) }}
                            </div>
                        </div>
                    </article>
                </div>
            </section>

        </div>
    </div>
</template>

<style scoped>
.marketing-container { max-width: 1200px; margin: 0 auto; padding: 0 1.5rem; }

/* Breadcrumb */
.post-page { padding: 2rem 0 5rem; background: #fff; }
.post-breadcrumb { display: flex; align-items: center; flex-wrap: wrap; gap: 6px; font-size: 13px; color: #9CA3AF; margin-bottom: 2rem; }
.post-back-link { display: inline-flex; align-items: center; gap: 5px; color: #6B7280; text-decoration: none; transition: color .12s; }
.post-back-link:hover { color: #1D9792; }
.post-breadcrumb-sep { color: #E5E7EB; }
.post-breadcrumb-current { color: #374151; font-weight: 500; max-width: 300px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }

/* Layout */
.post-layout { display: grid; grid-template-columns: 1fr 280px; gap: 3rem; align-items: start; }
@media (max-width: 900px) { .post-layout { grid-template-columns: 1fr; } .post-sidebar { display: none; } }

/* Article */
.post-article { min-width: 0; }
.post-category-pill { display: inline-block; font-size: 11px; font-weight: 700; letter-spacing: .7px; text-transform: uppercase; color: #1D9792; margin-bottom: .625rem; text-decoration: none; }
.post-category-pill a { color: inherit; text-decoration: none; }
.post-category-pill a:hover { text-decoration: underline; }

.post-title { font-size: clamp(1.5rem, 3.5vw, 2.25rem); font-weight: 800; color: #0D2B2A; line-height: 1.25; letter-spacing: -.5px; margin-bottom: 1rem; }

.post-meta-bar { display: flex; align-items: center; flex-wrap: wrap; gap: 8px; font-size: 13px; color: #6B7280; margin-bottom: 1.5rem; }
.post-meta-sep { color: #D1D5DB; }
.post-author { display: flex; align-items: center; gap: 8px; }
.post-author-avatar { width: 28px; height: 28px; border-radius: 50%; background: #E6F5F4; color: #1D9792; font-size: 11px; font-weight: 700; display: flex; align-items: center; justify-content: center; overflow: hidden; flex-shrink: 0; }
.post-author-avatar img { width: 100%; height: 100%; object-fit: cover; }
.post-author-name { font-weight: 500; color: #374151; }

.post-featured-image { margin-bottom: 2rem; border-radius: 12px; overflow: hidden; }
.post-featured-image img { width: 100%; height: auto; display: block; }

/* Body prose */
.post-body.prose { font-size: 16px; line-height: 1.8; color: #374151; }
.post-body.prose :deep(h2) { font-size: 1.35rem; font-weight: 700; color: #0D2B2A; margin: 2rem 0 .75rem; letter-spacing: -.3px; }
.post-body.prose :deep(h3) { font-size: 1.1rem; font-weight: 700; color: #0D2B2A; margin: 1.5rem 0 .5rem; }
.post-body.prose :deep(p)  { margin-bottom: 1.1rem; }
.post-body.prose :deep(ul),
.post-body.prose :deep(ol) { margin: 0 0 1rem 1.375rem; }
.post-body.prose :deep(li) { margin-bottom: .4rem; }
.post-body.prose :deep(strong) { font-weight: 700; color: #0D2B2A; }
.post-body.prose :deep(a)  { color: #1D9792; text-decoration: underline; }
.post-body.prose :deep(a:hover) { color: #055E5A; }
.post-body.prose :deep(blockquote) {
    border-left: 3px solid #1D9792;
    margin: 1.5rem 0; padding: .75rem 1.25rem;
    background: #F4FAFA; border-radius: 0 8px 8px 0;
    font-style: italic; color: #4D7374;
}
.post-body.prose :deep(code) { background: #F4FAFA; padding: 2px 6px; border-radius: 4px; font-size: .875em; font-family: 'JetBrains Mono', monospace; color: #055E5A; }
.post-body.prose :deep(pre) { background: #0D2B2A; color: #C5E8E5; padding: 1.25rem; border-radius: 10px; overflow-x: auto; margin-bottom: 1rem; }
.post-body.prose :deep(pre code) { background: none; color: inherit; padding: 0; }

/* Tags */
.post-tags { display: flex; align-items: center; flex-wrap: wrap; gap: 7px; margin-top: 2rem; padding-top: 1.5rem; border-top: 1px solid #E6F5F4; }
.post-tag { padding: 4px 12px; border-radius: 9999px; font-size: 12.5px; background: #F4FAFA; color: #374151; text-decoration: none; border: 1px solid #E6F5F4; transition: background .1s, color .1s; }
.post-tag:hover { background: #E6F5F4; color: #055E5A; }

/* Author box */
.post-author-box { display: flex; gap: 1rem; align-items: flex-start; background: #F4FAFA; border: 1px solid #E6F5F4; border-radius: 12px; padding: 1.25rem 1.5rem; margin-top: 2rem; }
.post-author-box-avatar { width: 48px; height: 48px; border-radius: 50%; background: #1D9792; color: #fff; font-size: 16px; font-weight: 700; display: flex; align-items: center; justify-content: center; flex-shrink: 0; overflow: hidden; }
.post-author-box-avatar img { width: 100%; height: 100%; object-fit: cover; }
.post-author-box-name { font-size: 14.5px; font-weight: 700; color: #0D2B2A; margin-bottom: 4px; }
.post-author-box-bio { font-size: 13.5px; color: #6B7280; line-height: 1.6; }

/* Sidebar */
.post-sidebar { position: sticky; top: 80px; }
.post-sidebar-card { background: linear-gradient(160deg, #F4FAFA 0%, #E6F5F4 100%); border: 1px solid #C5E8E5; border-radius: 14px; padding: 1.5rem; }
.post-sidebar-cta-label { font-size: 14px; font-weight: 700; color: #0D2B2A; margin-bottom: .5rem; }
.post-sidebar-cta-body { font-size: 13px; color: #4D7374; line-height: 1.6; margin-bottom: 1.25rem; }
.post-sidebar-cta-btn { display: flex; align-items: center; justify-content: center; gap: 6px; background: #1D9792; color: #fff; font-size: 14px; font-weight: 600; padding: 10px 16px; border-radius: 8px; text-decoration: none; transition: background .12s; margin-bottom: .625rem; }
.post-sidebar-cta-btn:hover { background: #055E5A; }
.post-sidebar-cta-link { display: block; text-align: center; font-size: 13px; color: #1D9792; text-decoration: none; font-weight: 500; }
.post-sidebar-cta-link:hover { text-decoration: underline; }

/* Related */
.post-related { margin-top: 4rem; padding-top: 2.5rem; border-top: 1px solid #E6F5F4; }
.post-related-title { font-size: 1.2rem; font-weight: 800; color: #0D2B2A; margin-bottom: 1.5rem; }
.post-related-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 1.25rem; }
@media (max-width: 800px) { .post-related-grid { grid-template-columns: 1fr; } }
.post-related-card { background: #F4FAFA; border: 1px solid #E6F5F4; border-radius: 10px; overflow: hidden; }
.post-related-image-wrap { display: block; aspect-ratio: 16/9; overflow: hidden; text-decoration: none; }
.post-related-image img { width: 100%; height: 100%; object-fit: cover; transition: transform .3s; }
.post-related-card:hover .post-related-image img { transform: scale(1.04); }
.post-related-image-placeholder { width: 100%; height: 100%; background: #E6F5F4; display: flex; align-items: center; justify-content: center; }
.post-related-body { padding: 1rem; }
.post-related-post-title { font-size: 14px; font-weight: 700; color: #0D2B2A; line-height: 1.4; margin-bottom: .4rem; }
.post-related-post-title a { text-decoration: none; color: inherit; }
.post-related-post-title a:hover { color: #1D9792; }
.post-related-excerpt { font-size: 12.5px; color: #6B7280; line-height: 1.5; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden; margin-bottom: .5rem; }
.post-related-meta { display: flex; align-items: center; gap: 4px; font-size: 12px; color: #9CA3AF; }
</style>
