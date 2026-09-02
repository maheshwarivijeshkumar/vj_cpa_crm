<script setup lang="ts">
import MarketingLayout from '@/layouts/MarketingLayout.vue'
import SeoHead from '@/components/ui/SeoHead.vue'
import { Link, router } from '@inertiajs/vue3'
import { ref, computed } from 'vue'
import { Search, Clock, Eye, Tag, ChevronRight, BookOpen } from '@lucide/vue'

defineOptions({ layout: MarketingLayout })

interface Category { name: string; slug: string; count: number }
interface TagItem   { name: string; slug: string }
interface Post {
    id: number; title: string; slug: string; excerpt: string | null
    featured_image: string | null; published_at: string | null
    read_time: number; view_count: number
    category: { name: string; slug: string } | null
    author:   { name: string } | null
    tags:     TagItem[]
}
interface Filters { category: string | null; tag: string | null; search: string | null }
interface Pagination {
    data: Post[]; current_page: number; last_page: number
    per_page: number; total: number; from: number | null; to: number | null
    links?: Array<{ url: string | null; label: string; active: boolean }>
}

const props = defineProps<{
    seo?:        Record<string, unknown>
    posts:       Pagination
    categories:  Category[]
    tags:        TagItem[]
    filters:     Filters
}>()

// ── Local search ──────────────────────────────────────────────────────────────
const searchQuery = ref(props.filters.search ?? '')

function applySearch() {
    router.get('/blog', { search: searchQuery.value || undefined }, { preserveState: true, replace: true })
}

function filterCategory(slug: string | null) {
    router.get('/blog', { category: slug || undefined }, { preserveState: true, replace: true })
}

function filterTag(slug: string) {
    router.get('/blog', { tag: slug }, { preserveState: true, replace: true })
}

function clearFilters() {
    searchQuery.value = ''
    router.get('/blog', {}, { replace: true })
}

const hasFilters = computed(() =>
    props.filters.category || props.filters.tag || props.filters.search
)

// ── Format helpers ────────────────────────────────────────────────────────────
function formatDate(d: string | null) {
    if (!d) return ''
    return new Date(d).toLocaleDateString('en-CA', { year: 'numeric', month: 'long', day: 'numeric' })
}
</script>

<template>
    <SeoHead :seo="(seo as any)" />

    <!-- ── Hero ──────────────────────────────────────────────────────────── -->
    <section class="blog-hero">
        <div class="marketing-container">
            <div class="section-eyebrow">Blog</div>
            <h1 class="blog-hero-title">Insights for CPA Firms</h1>
            <p class="blog-hero-subtitle">
                Practice management tips, tax filing guides, product updates, and industry insights
                — written for accounting professionals.
            </p>

            <!-- Search bar -->
            <form class="blog-search" @submit.prevent="applySearch">
                <Search :size="16" class="blog-search-icon" />
                <input
                    v-model="searchQuery"
                    type="search"
                    placeholder="Search articles…"
                    class="blog-search-input"
                    aria-label="Search blog"
                />
                <button type="submit" class="blog-search-btn">Search</button>
            </form>
        </div>
    </section>

    <!-- ── Body ──────────────────────────────────────────────────────────── -->
    <section class="blog-body">
        <div class="marketing-container">
            <div class="blog-layout">

                <!-- ── Sidebar ──────────────────────────────────────────── -->
                <aside class="blog-sidebar">

                    <!-- Active filter notice -->
                    <div v-if="hasFilters" class="sidebar-filter-notice">
                        <span>Filters active</span>
                        <button class="sidebar-clear-btn" @click="clearFilters">Clear all</button>
                    </div>

                    <!-- Categories -->
                    <div class="sidebar-section">
                        <h3 class="sidebar-heading">Categories</h3>
                        <button
                            class="sidebar-cat-btn"
                            :class="{ active: !filters.category }"
                            @click="filterCategory(null)"
                        >
                            All posts
                            <span class="sidebar-cat-count">{{ posts.total }}</span>
                        </button>
                        <button
                            v-for="cat in categories"
                            :key="cat.slug"
                            class="sidebar-cat-btn"
                            :class="{ active: filters.category === cat.slug }"
                            @click="filterCategory(cat.slug)"
                        >
                            {{ cat.name }}
                            <span class="sidebar-cat-count">{{ cat.count }}</span>
                        </button>
                    </div>

                    <!-- Tags -->
                    <div v-if="tags.length" class="sidebar-section">
                        <h3 class="sidebar-heading">Tags</h3>
                        <div class="sidebar-tags">
                            <button
                                v-for="t in tags"
                                :key="t.slug"
                                class="sidebar-tag"
                                :class="{ active: filters.tag === t.slug }"
                                @click="filterTag(t.slug)"
                            >
                                {{ t.name }}
                            </button>
                        </div>
                    </div>

                </aside>

                <!-- ── Post grid ─────────────────────────────────────────── -->
                <div class="blog-main">

                    <!-- Results info -->
                    <div v-if="hasFilters || posts.total" class="blog-results-info">
                        <span v-if="filters.search">
                            Results for <strong>"{{ filters.search }}"</strong> ·
                        </span>
                        <span v-else-if="filters.category">
                            Category: <strong>{{ categories.find(c => c.slug === filters.category)?.name }}</strong> ·
                        </span>
                        <span v-else-if="filters.tag">
                            Tag: <strong>{{ tags.find(t => t.slug === filters.tag)?.name }}</strong> ·
                        </span>
                        {{ posts.total }} article{{ posts.total !== 1 ? 's' : '' }}
                    </div>

                    <!-- Empty state -->
                    <div v-if="!posts.data.length" class="blog-empty">
                        <BookOpen :size="40" class="text-cpa-border mb-3" />
                        <p class="text-cpa-text-muted text-sm">No articles found.</p>
                        <button class="marketing-link-btn mt-3 text-sm" @click="clearFilters">
                            Clear filters
                        </button>
                    </div>

                    <!-- Post grid -->
                    <div v-else class="blog-grid">
                        <article
                            v-for="post in posts.data"
                            :key="post.id"
                            class="post-card"
                        >
                            <!-- Featured image -->
                            <Link :href="`/blog/${post.slug}`" class="post-card-image-wrap" tabindex="-1" aria-hidden="true">
                                <div v-if="post.featured_image" class="post-card-image">
                                    <img :src="post.featured_image" :alt="post.title" loading="lazy" />
                                </div>
                                <div v-else class="post-card-image-placeholder">
                                    <BookOpen :size="28" class="text-cpa-medium-light" />
                                </div>
                            </Link>

                            <!-- Content -->
                            <div class="post-card-body">
                                <!-- Category pill -->
                                <div v-if="post.category" class="post-card-category">
                                    {{ post.category.name }}
                                </div>

                                <h2 class="post-card-title">
                                    <Link :href="`/blog/${post.slug}`">{{ post.title }}</Link>
                                </h2>

                                <p v-if="post.excerpt" class="post-card-excerpt">{{ post.excerpt }}</p>

                                <div class="post-card-meta">
                                    <span v-if="post.author" class="post-card-author">
                                        {{ post.author.name }}
                                    </span>
                                    <span class="post-card-sep" aria-hidden="true">·</span>
                                    <span class="post-card-date">{{ formatDate(post.published_at) }}</span>
                                    <span class="post-card-sep" aria-hidden="true">·</span>
                                    <span class="flex items-center gap-1">
                                        <Clock :size="12" /> {{ post.read_time }} min read
                                    </span>
                                </div>

                                <Link :href="`/blog/${post.slug}`" class="post-card-link">
                                    Read article <ChevronRight :size="14" class="inline" />
                                </Link>
                            </div>
                        </article>
                    </div>

                    <!-- Pagination -->
                    <nav v-if="posts.last_page > 1" class="blog-pagination" aria-label="Pagination">
                        <template v-if="posts.links">
                            <Link
                                v-for="link in posts.links"
                                :key="link.label"
                                :href="link.url ?? '#'"
                                class="pagination-btn"
                                :class="{ active: link.active, disabled: !link.url }"
                                v-html="link.label"
                                :aria-current="link.active ? 'page' : undefined"
                            />
                        </template>
                    </nav>

                </div>
            </div>
        </div>
    </section>
</template>

<style scoped>
.marketing-container { max-width: 1200px; margin: 0 auto; padding: 0 1.5rem; }
.section-eyebrow { font-size: 12px; font-weight: 700; letter-spacing: 1.2px; text-transform: uppercase; color: #1D9792; margin-bottom: .5rem; }
.marketing-link-btn { font-size: 14px; font-weight: 600; color: #1D9792; background: none; border: none; cursor: pointer; padding: 0; text-decoration: none; }
.marketing-link-btn:hover { color: #055E5A; }

/* Hero */
.blog-hero { padding: 4rem 0 2.5rem; background: linear-gradient(160deg, #F4FAFA 0%, #E6F5F4 60%, #fff 100%); }
.blog-hero-title { font-size: clamp(1.75rem, 4vw, 2.75rem); font-weight: 800; color: #0D2B2A; letter-spacing: -.5px; margin-bottom: .5rem; }
.blog-hero-subtitle { font-size: 1rem; color: #4D7374; max-width: 580px; line-height: 1.7; margin-bottom: 1.75rem; }

.blog-search { display: flex; align-items: center; gap: 8px; background: #fff; border: 1.5px solid #D4ECEA; border-radius: 10px; padding: 6px 6px 6px 12px; max-width: 480px; transition: border-color .15s; }
.blog-search:focus-within { border-color: #1D9792; box-shadow: 0 0 0 3px rgba(29,151,146,.1); }
.blog-search-icon { color: #9CA3AF; flex-shrink: 0; }
.blog-search-input { flex: 1; border: none; outline: none; font-size: 14px; color: #0D2B2A; background: transparent; font-family: inherit; padding: 4px 0; }
.blog-search-input::placeholder { color: #9CA3AF; }
.blog-search-btn { background: #1D9792; color: #fff; border: none; border-radius: 7px; padding: 7px 16px; font-size: 13.5px; font-weight: 600; cursor: pointer; white-space: nowrap; transition: background .12s; }
.blog-search-btn:hover { background: #055E5A; }

/* Body layout */
.blog-body { padding: 3rem 0 5rem; background: #fff; }
.blog-layout { display: grid; grid-template-columns: 230px 1fr; gap: 2.5rem; align-items: start; }
@media (max-width: 800px) { .blog-layout { grid-template-columns: 1fr; } .blog-sidebar { display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; } }
@media (max-width: 500px) { .blog-sidebar { grid-template-columns: 1fr; } }

/* Sidebar */
.sidebar-filter-notice { display: flex; align-items: center; justify-content: space-between; background: #FEF3C7; border: 1px solid #FDE68A; border-radius: 8px; padding: 8px 12px; font-size: 12.5px; color: #92400E; margin-bottom: .75rem; }
.sidebar-clear-btn { font-size: 12px; font-weight: 600; color: #D97706; background: none; border: none; cursor: pointer; padding: 0; }
.sidebar-clear-btn:hover { color: #92400E; }
.sidebar-section { margin-bottom: 1.5rem; }
.sidebar-heading { font-size: 11px; font-weight: 700; letter-spacing: .8px; text-transform: uppercase; color: #9CA3AF; margin-bottom: .625rem; }
.sidebar-cat-btn { display: flex; align-items: center; justify-content: space-between; width: 100%; padding: 7px 10px; border-radius: 7px; font-size: 13.5px; color: #374151; background: transparent; border: none; cursor: pointer; transition: background .1s, color .1s; text-align: left; margin-bottom: 2px; }
.sidebar-cat-btn:hover { background: #E6F5F4; color: #055E5A; }
.sidebar-cat-btn.active { background: #E6F5F4; color: #1D9792; font-weight: 600; }
.sidebar-cat-count { font-size: 11px; background: #F3F4F6; color: #9CA3AF; padding: 2px 7px; border-radius: 9999px; }
.sidebar-cat-btn.active .sidebar-cat-count { background: #C5E8E5; color: #055E5A; }
.sidebar-tags { display: flex; flex-wrap: wrap; gap: 6px; }
.sidebar-tag { padding: 4px 10px; border-radius: 9999px; font-size: 12px; background: #F3F4F6; color: #374151; border: none; cursor: pointer; transition: background .1s, color .1s; }
.sidebar-tag:hover { background: #E6F5F4; color: #055E5A; }
.sidebar-tag.active { background: #1D9792; color: #fff; }

/* Main blog area */
.blog-results-info { font-size: 13px; color: #6B7280; margin-bottom: 1.25rem; }
.blog-empty { display: flex; flex-direction: column; align-items: center; padding: 4rem 1rem; text-align: center; background: #F9FAFB; border-radius: 12px; border: 1px solid #E5E7EB; }

.blog-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 1.5rem; }
@media (max-width: 1100px) { .blog-grid { grid-template-columns: repeat(2, 1fr); } }
@media (max-width: 600px) { .blog-grid { grid-template-columns: 1fr; } }

/* Post card */
.post-card { background: #fff; border: 1px solid #E6F5F4; border-radius: 12px; overflow: hidden; display: flex; flex-direction: column; transition: box-shadow .15s, transform .15s; }
.post-card:hover { box-shadow: 0 6px 24px rgba(2,62,60,.09); transform: translateY(-2px); }

.post-card-image-wrap { display: block; text-decoration: none; aspect-ratio: 16/9; overflow: hidden; }
.post-card-image { width: 100%; height: 100%; }
.post-card-image img { width: 100%; height: 100%; object-fit: cover; transition: transform .3s; }
.post-card:hover .post-card-image img { transform: scale(1.03); }
.post-card-image-placeholder { width: 100%; height: 100%; background: #F4FAFA; display: flex; align-items: center; justify-content: center; }

.post-card-body { padding: 1.125rem; flex: 1; display: flex; flex-direction: column; gap: .5rem; }
.post-card-category { display: inline-block; font-size: 10.5px; font-weight: 700; letter-spacing: .6px; text-transform: uppercase; color: #1D9792; }
.post-card-title { font-size: 15px; font-weight: 700; color: #0D2B2A; line-height: 1.4; }
.post-card-title a { text-decoration: none; color: inherit; }
.post-card-title a:hover { color: #1D9792; }
.post-card-excerpt { font-size: 13px; color: #6B7280; line-height: 1.6; display: -webkit-box; -webkit-line-clamp: 3; -webkit-box-orient: vertical; overflow: hidden; }
.post-card-meta { display: flex; align-items: center; flex-wrap: wrap; gap: 5px; font-size: 12px; color: #9CA3AF; margin-top: auto; }
.post-card-author { font-weight: 500; color: #6B7280; }
.post-card-sep { color: #D1D5DB; }
.post-card-link { font-size: 13px; font-weight: 600; color: #1D9792; text-decoration: none; display: inline-flex; align-items: center; gap: 3px; margin-top: .25rem; transition: color .12s; }
.post-card-link:hover { color: #055E5A; }

/* Pagination */
.blog-pagination { display: flex; align-items: center; justify-content: center; gap: 4px; margin-top: 2.5rem; flex-wrap: wrap; }
.pagination-btn { display: flex; align-items: center; justify-content: center; min-width: 36px; height: 36px; padding: 0 10px; border-radius: 8px; border: 1px solid #E5E7EB; background: #fff; font-size: 13px; color: #374151; text-decoration: none; cursor: pointer; transition: all .12s; }
.pagination-btn:hover { border-color: #1D9792; color: #1D9792; }
.pagination-btn.active { background: #1D9792; border-color: #1D9792; color: #fff; font-weight: 600; }
.pagination-btn.disabled { opacity: .4; pointer-events: none; }
</style>
