<script setup lang="ts">
import { ref } from 'vue'
import { Link, router } from '@inertiajs/vue3'
import { BookOpen, Plus, Search, Eye, Edit2, Trash2, MoreHorizontal } from '@lucide/vue'
import PlatformLayout from '@/layouts/PlatformLayout.vue'
import Pagination from '@/components/ui/Pagination.vue'
import StatusBadge from '@/components/ui/StatusBadge.vue'

interface Post {
    id: number
    title: string
    slug: string
    status: string
    view_count: number
    published_at?: string
    created_at: string
    category?: { id: number; name: string }
    author?: { id: number; first_name: string; last_name: string }
}

interface PaginatedPosts {
    data: Post[]
    total: number
    per_page: number
    links: Array<{ url: string | null; label: string; active: boolean }>
}

const props = defineProps<{
    posts: PaginatedPosts
    filters: {
        search: string
        status: string
        category_id: number | null
        sort_by: string
        per_page: number
    }
    perPageOpts: number[]
    categories: Array<{ id: number; name: string }>
    statusOptions: Array<{ value: string; label: string }>
}>()

const search    = ref(props.filters.search ?? '')
const status    = ref(props.filters.status ?? '')
const catId     = ref(props.filters.category_id ?? '')
const perPage   = ref(props.filters.per_page ?? 15)

function applyFilters() {
    router.get('/platform/blog', {
        search:      search.value || undefined,
        status:      status.value || undefined,
        category_id: catId.value || undefined,
        per_page:    perPage.value,
    }, { preserveState: true, replace: true })
}

function deletePost(post: Post) {
    if (!confirm(`Delete "${post.title}"? This cannot be undone.`)) return
    router.delete(`/platform/blog/${post.id}`, { preserveState: false })
}

function quickStatus(post: Post, newStatus: string) {
    router.patch(`/platform/blog/${post.id}/status`, { status: newStatus }, { preserveState: false })
}
</script>

<template>
    <PlatformLayout>
        <template #header>
            <span class="text-sm font-medium text-cpa-text-primary">Blog</span>
        </template>

        <div class="space-y-6">

            <!-- Header -->
            <div class="flex items-center justify-between flex-wrap gap-4">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-cpa-very-light flex items-center justify-center">
                        <BookOpen :size="20" class="text-cpa-medium-dark" />
                    </div>
                    <div>
                        <h1 class="text-xl font-semibold text-cpa-text-primary">Blog Posts</h1>
                        <p class="text-xs text-cpa-text-muted mt-0.5">{{ posts.total }} posts total</p>
                    </div>
                </div>
                <Link
                    href="/platform/blog/create"
                    class="flex items-center gap-1.5 bg-cpa-medium-dark hover:bg-cpa-dark text-white font-medium rounded-lg px-4 py-2 text-sm transition-colors"
                >
                    <Plus :size="16" /> New Post
                </Link>
            </div>

            <!-- Filters -->
            <div class="bg-white border border-cpa-border rounded-xl shadow-sm p-4">
                <div class="flex flex-wrap items-end gap-3">
                    <div class="flex-1 min-w-[180px]">
                        <label class="block text-xs font-medium text-cpa-text-secondary mb-1">Search</label>
                        <div class="relative">
                            <Search :size="14" class="absolute left-2.5 top-2.5 text-cpa-text-muted" />
                            <input v-model="search" type="text" placeholder="Title or slug…" class="w-full pl-8 pr-3 py-2 text-sm border border-cpa-border rounded-lg bg-cpa-bg focus:outline-none focus:ring-2 focus:ring-cpa-medium placeholder:text-cpa-text-muted" @keyup.enter="applyFilters" />
                        </div>
                    </div>
                    <div class="min-w-[130px]">
                        <label class="block text-xs font-medium text-cpa-text-secondary mb-1">Status</label>
                        <select v-model="status" class="w-full px-3 py-2 text-sm border border-cpa-border rounded-lg bg-cpa-bg focus:outline-none focus:ring-2 focus:ring-cpa-medium" @change="applyFilters">
                            <option value="">All</option>
                            <option v-for="s in statusOptions" :key="s.value" :value="s.value">{{ s.label }}</option>
                        </select>
                    </div>
                    <div class="min-w-[130px]">
                        <label class="block text-xs font-medium text-cpa-text-secondary mb-1">Category</label>
                        <select v-model="catId" class="w-full px-3 py-2 text-sm border border-cpa-border rounded-lg bg-cpa-bg focus:outline-none focus:ring-2 focus:ring-cpa-medium" @change="applyFilters">
                            <option value="">All</option>
                            <option v-for="c in categories" :key="c.id" :value="c.id">{{ c.name }}</option>
                        </select>
                    </div>
                    <button class="bg-cpa-medium-dark hover:bg-cpa-dark text-white font-medium rounded-lg px-4 py-2 text-sm transition-colors" @click="applyFilters">Apply</button>
                </div>
            </div>

            <!-- Table -->
            <div class="bg-white border border-cpa-border rounded-xl shadow-sm overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead class="bg-cpa-bg border-b border-cpa-border">
                            <tr>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-cpa-text-secondary uppercase tracking-wide">Title</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-cpa-text-secondary uppercase tracking-wide">Status</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-cpa-text-secondary uppercase tracking-wide">Category</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-cpa-text-secondary uppercase tracking-wide">Views</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-cpa-text-secondary uppercase tracking-wide">Published</th>
                                <th class="px-4 py-3 text-right text-xs font-semibold text-cpa-text-secondary uppercase tracking-wide">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-cpa-border">
                            <tr v-for="post in posts.data" :key="post.id" class="hover:bg-cpa-very-light transition-colors">
                                <td class="px-4 py-3">
                                    <p class="font-medium text-cpa-text-primary truncate max-w-xs">{{ post.title }}</p>
                                    <p class="text-xs text-cpa-text-muted font-mono mt-0.5">{{ post.slug }}</p>
                                </td>
                                <td class="px-4 py-3"><StatusBadge :status="post.status" /></td>
                                <td class="px-4 py-3 text-cpa-text-secondary text-xs">{{ post.category?.name ?? '—' }}</td>
                                <td class="px-4 py-3 text-cpa-text-secondary">{{ post.view_count.toLocaleString() }}</td>
                                <td class="px-4 py-3 text-cpa-text-muted text-xs whitespace-nowrap">
                                    {{ post.published_at ? new Date(post.published_at).toLocaleDateString() : '—' }}
                                </td>
                                <td class="px-4 py-3 text-right">
                                    <div class="flex items-center justify-end gap-1.5">
                                        <a
                                            :href="`/blog/${post.slug}`"
                                            target="_blank"
                                            class="inline-flex items-center gap-1 text-xs text-cpa-text-secondary hover:text-cpa-medium-dark transition-colors px-2 py-1 rounded hover:bg-cpa-very-light"
                                            title="View on site"
                                        >
                                            <Eye :size="13" />
                                        </a>
                                        <Link
                                            :href="`/platform/blog/${post.id}/edit`"
                                            class="inline-flex items-center gap-1 text-xs text-cpa-medium-dark hover:text-cpa-dark transition-colors px-2 py-1 rounded hover:bg-cpa-very-light font-medium"
                                        >
                                            <Edit2 :size="13" /> Edit
                                        </Link>
                                        <button
                                            class="inline-flex items-center gap-1 text-xs text-cpa-danger hover:text-red-700 transition-colors px-2 py-1 rounded hover:bg-cpa-danger-bg"
                                            @click="deletePost(post)"
                                        >
                                            <Trash2 :size="13" />
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            <tr v-if="!posts.data.length">
                                <td colspan="6" class="px-4 py-16 text-center text-cpa-text-muted text-sm">
                                    <BookOpen :size="32" class="mx-auto text-cpa-border mb-3" />
                                    No blog posts yet.
                                    <Link href="/platform/blog/create" class="ml-1 text-cpa-medium-dark hover:underline">Create the first one.</Link>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <div class="border-t border-cpa-border px-4 py-3">
                    <Pagination :links="posts.links" :total="posts.total" :per-page="posts.per_page" />
                </div>
            </div>
        </div>
    </PlatformLayout>
</template>
