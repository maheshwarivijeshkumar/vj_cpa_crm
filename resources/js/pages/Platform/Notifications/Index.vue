<script setup lang="ts">
import { ref } from 'vue'
import { Link, router } from '@inertiajs/vue3'
import { Bell, Search, Eye, Edit2 } from '@lucide/vue'
import PlatformLayout from '@/layouts/PlatformLayout.vue'
import Pagination from '@/components/ui/Pagination.vue'

interface NotificationTemplate {
    id: number
    key: string
    name: string
    channel: string
    category: string
    subject?: string
    description?: string
    is_active: boolean
}

interface PaginatedTemplates {
    data: NotificationTemplate[]
    total: number
    per_page: number
    links: Array<{ url: string | null; label: string; active: boolean }>
}

const props = defineProps<{
    templates: PaginatedTemplates
    filters: {
        search: string
        channel: string
        category: string
        sort_by: string
        per_page: number
    }
    perPageOpts: number[]
    channelOptions: string[]
    categoryOptions: string[]
}>()

const search   = ref(props.filters.search ?? '')
const channel  = ref(props.filters.channel ?? '')
const category = ref(props.filters.category ?? '')

function applyFilters() {
    router.get('/platform/notifications', {
        search:   search.value || undefined,
        channel:  channel.value || undefined,
        category: category.value || undefined,
    }, { preserveState: true, replace: true })
}

const channelBadge: Record<string, string> = {
    email:  'bg-cpa-info-bg text-cpa-info',
    in_app: 'bg-cpa-very-light text-cpa-medium-dark',
    sms:    'bg-cpa-warning-bg text-cpa-warning',
}
</script>

<template>
    <PlatformLayout>
        <template #header>
            <span class="text-sm font-medium text-cpa-text-primary">Notification Templates</span>
        </template>

        <div class="space-y-6">

            <!-- Page header -->
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-cpa-very-light flex items-center justify-center">
                    <Bell :size="20" class="text-cpa-medium-dark" />
                </div>
                <div>
                    <h1 class="text-xl font-semibold text-cpa-text-primary">Notification Templates</h1>
                    <p class="text-xs text-cpa-text-muted mt-0.5">{{ templates.total }} templates</p>
                </div>
            </div>

            <!-- Filters -->
            <div class="bg-white border border-cpa-border rounded-xl shadow-sm p-4">
                <div class="flex flex-wrap items-end gap-3">
                    <div class="flex-1 min-w-[180px]">
                        <label class="block text-xs font-medium text-cpa-text-secondary mb-1">Search</label>
                        <div class="relative">
                            <Search :size="14" class="absolute left-2.5 top-2.5 text-cpa-text-muted" />
                            <input
                                v-model="search"
                                type="text"
                                placeholder="Key or name…"
                                class="w-full pl-8 pr-3 py-2 text-sm border border-cpa-border rounded-lg bg-cpa-bg focus:outline-none focus:ring-2 focus:ring-cpa-medium placeholder:text-cpa-text-muted"
                                @keyup.enter="applyFilters"
                            />
                        </div>
                    </div>
                    <div class="min-w-[120px]">
                        <label class="block text-xs font-medium text-cpa-text-secondary mb-1">Channel</label>
                        <select v-model="channel" class="w-full px-3 py-2 text-sm border border-cpa-border rounded-lg bg-cpa-bg focus:outline-none focus:ring-2 focus:ring-cpa-medium" @change="applyFilters">
                            <option value="">All</option>
                            <option v-for="c in channelOptions" :key="c" :value="c" class="capitalize">{{ c.replace('_', ' ') }}</option>
                        </select>
                    </div>
                    <div class="min-w-[120px]">
                        <label class="block text-xs font-medium text-cpa-text-secondary mb-1">Category</label>
                        <select v-model="category" class="w-full px-3 py-2 text-sm border border-cpa-border rounded-lg bg-cpa-bg focus:outline-none focus:ring-2 focus:ring-cpa-medium" @change="applyFilters">
                            <option value="">All</option>
                            <option v-for="c in categoryOptions" :key="c" :value="c" class="capitalize">{{ c }}</option>
                        </select>
                    </div>
                    <button class="bg-cpa-medium-dark hover:bg-cpa-dark text-white font-medium rounded-lg px-4 py-2 text-sm transition-colors" @click="applyFilters">
                        Apply
                    </button>
                </div>
            </div>

            <!-- Table -->
            <div class="bg-white border border-cpa-border rounded-xl shadow-sm overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead class="bg-cpa-bg border-b border-cpa-border">
                            <tr>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-cpa-text-secondary uppercase tracking-wide">Template</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-cpa-text-secondary uppercase tracking-wide">Channel</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-cpa-text-secondary uppercase tracking-wide">Category</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-cpa-text-secondary uppercase tracking-wide">Subject</th>
                                <th class="px-4 py-3 text-right text-xs font-semibold text-cpa-text-secondary uppercase tracking-wide">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-cpa-border">
                            <tr v-for="tmpl in templates.data" :key="tmpl.id" class="hover:bg-cpa-very-light transition-colors">
                                <td class="px-4 py-3">
                                    <p class="font-medium text-cpa-text-primary">{{ tmpl.name }}</p>
                                    <p class="text-xs text-cpa-text-muted font-mono mt-0.5">{{ tmpl.key }}</p>
                                </td>
                                <td class="px-4 py-3">
                                    <span :class="['inline-flex items-center px-2 py-0.5 rounded-full text-[11px] font-semibold capitalize', channelBadge[tmpl.channel] ?? 'bg-gray-100 text-gray-500']">
                                        {{ tmpl.channel.replace('_', ' ') }}
                                    </span>
                                </td>
                                <td class="px-4 py-3 text-cpa-text-secondary capitalize">{{ tmpl.category }}</td>
                                <td class="px-4 py-3 text-cpa-text-muted text-xs truncate max-w-xs">{{ tmpl.subject ?? '—' }}</td>
                                <td class="px-4 py-3 text-right">
                                    <Link
                                        :href="`/platform/notifications/${tmpl.id}/edit`"
                                        class="inline-flex items-center gap-1.5 text-sm text-cpa-medium-dark hover:text-cpa-dark font-medium transition-colors"
                                    >
                                        <Edit2 :size="13" /> Edit
                                    </Link>
                                </td>
                            </tr>
                            <tr v-if="!templates.data.length">
                                <td colspan="5" class="px-4 py-12 text-center text-cpa-text-muted text-sm">No templates match your filters.</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <div class="border-t border-cpa-border px-4 py-3">
                    <Pagination :links="templates.links" :total="templates.total" :per-page="templates.per_page" />
                </div>
            </div>

        </div>
    </PlatformLayout>
</template>
