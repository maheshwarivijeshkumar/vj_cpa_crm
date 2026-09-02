<script setup lang="ts">
import { ref, computed } from 'vue'
import { router } from '@inertiajs/vue3'
import { Activity, Search, Filter, Download, RefreshCw } from '@lucide/vue'
import PlatformLayout from '@/layouts/PlatformLayout.vue'
import Pagination from '@/components/ui/Pagination.vue'

interface AuditLog {
    id: number
    event: string
    auditable_type?: string
    auditable_id?: string
    ip_address?: string
    user_agent?: string
    old_values?: Record<string, unknown>
    new_values?: Record<string, unknown>
    created_at: string
    causer?: { id: number; first_name: string; last_name: string; email: string }
}

interface PaginatedLogs {
    data: AuditLog[]
    current_page: number
    last_page: number
    per_page: number
    total: number
    links: Array<{ url: string | null; label: string; active: boolean }>
}

const props = defineProps<{
    logs: PaginatedLogs
    filters: {
        search: string
        event: string
        tenant_id: number | null
        date: string
        sort_by: string
        sort_dir: string
        per_page: number
    }
    perPageOpts: number[]
    eventOptions: string[]
}>()

const search   = ref(props.filters.search ?? '')
const event    = ref(props.filters.event ?? '')
const date     = ref(props.filters.date ?? '')
const perPage  = ref(props.filters.per_page ?? 25)

function applyFilters() {
    router.get('/platform/audit-logs', {
        search:   search.value || undefined,
        event:    event.value || undefined,
        date:     date.value || undefined,
        per_page: perPage.value,
    }, { preserveState: true, replace: true })
}

function clearFilters() {
    search.value = ''
    event.value  = ''
    date.value   = ''
    router.get('/platform/audit-logs', {}, { preserveState: false })
}

// Format event string to a readable badge label
function eventLabel(e: string): string {
    return e.replace(/[._-]/g, ' ').replace(/\b\w/g, c => c.toUpperCase())
}

const eventBadge: Record<string, string> = {
    'login':           'bg-cpa-very-light text-cpa-medium-dark',
    'logout':          'bg-gray-100 text-gray-600',
    'login.failed':    'bg-cpa-danger-bg text-cpa-danger',
    'profile.updated': 'bg-cpa-warning-bg text-cpa-warning',
    'created':         'bg-cpa-success-bg text-cpa-success',
    'updated':         'bg-cpa-info-bg text-cpa-info',
    'deleted':         'bg-cpa-danger-bg text-cpa-danger',
}

function getBadge(e: string): string {
    for (const [key, cls] of Object.entries(eventBadge)) {
        if (e.includes(key)) return cls
    }
    return 'bg-gray-100 text-gray-500'
}

function modelName(type: string | undefined): string {
    if (!type) return '—'
    return type.split('\\').pop() ?? type
}
</script>

<template>
    <PlatformLayout>
        <template #header>
            <span class="text-sm font-medium text-cpa-text-primary">Audit Logs</span>
        </template>

        <div class="space-y-6">

            <!-- Page header -->
            <div class="flex items-center justify-between flex-wrap gap-4">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-cpa-very-light flex items-center justify-center">
                        <Activity :size="20" class="text-cpa-medium-dark" />
                    </div>
                    <div>
                        <h1 class="text-xl font-semibold text-cpa-text-primary">Audit Logs</h1>
                        <p class="text-xs text-cpa-text-muted mt-0.5">
                            {{ logs.total.toLocaleString() }} total events
                        </p>
                    </div>
                </div>
                <button
                    class="flex items-center gap-1.5 border border-cpa-border text-cpa-text-secondary hover:border-cpa-medium hover:text-cpa-dark rounded-lg px-3 py-2 text-sm font-medium transition-colors"
                    @click="clearFilters"
                >
                    <RefreshCw :size="14" /> Reset Filters
                </button>
            </div>

            <!-- Filters bar -->
            <div class="bg-white border border-cpa-border rounded-xl shadow-sm p-4">
                <div class="flex flex-wrap items-end gap-3">
                    <!-- Search -->
                    <div class="flex-1 min-w-[180px]">
                        <label class="block text-xs font-medium text-cpa-text-secondary mb-1">Search</label>
                        <div class="relative">
                            <Search :size="14" class="absolute left-2.5 top-2.5 text-cpa-text-muted" />
                            <input
                                v-model="search"
                                type="text"
                                placeholder="Event, IP address…"
                                class="w-full pl-8 pr-3 py-2 text-sm border border-cpa-border rounded-lg bg-cpa-bg focus:outline-none focus:ring-2 focus:ring-cpa-medium focus:border-cpa-medium placeholder:text-cpa-text-muted"
                                @keyup.enter="applyFilters"
                            />
                        </div>
                    </div>

                    <!-- Event filter -->
                    <div class="min-w-[160px]">
                        <label class="block text-xs font-medium text-cpa-text-secondary mb-1">Event</label>
                        <select
                            v-model="event"
                            class="w-full px-3 py-2 text-sm border border-cpa-border rounded-lg bg-cpa-bg focus:outline-none focus:ring-2 focus:ring-cpa-medium"
                            @change="applyFilters"
                        >
                            <option value="">All Events</option>
                            <option v-for="e in eventOptions" :key="e" :value="e">{{ eventLabel(e) }}</option>
                        </select>
                    </div>

                    <!-- Date filter -->
                    <div class="min-w-[140px]">
                        <label class="block text-xs font-medium text-cpa-text-secondary mb-1">Date</label>
                        <input
                            v-model="date"
                            type="date"
                            class="w-full px-3 py-2 text-sm border border-cpa-border rounded-lg bg-cpa-bg focus:outline-none focus:ring-2 focus:ring-cpa-medium"
                            @change="applyFilters"
                        />
                    </div>

                    <!-- Per page -->
                    <div>
                        <label class="block text-xs font-medium text-cpa-text-secondary mb-1">Per page</label>
                        <select
                            v-model="perPage"
                            class="px-3 py-2 text-sm border border-cpa-border rounded-lg bg-cpa-bg focus:outline-none focus:ring-2 focus:ring-cpa-medium"
                            @change="applyFilters"
                        >
                            <option v-for="n in perPageOpts" :key="n" :value="n">{{ n }}</option>
                        </select>
                    </div>

                    <button
                        class="bg-cpa-medium-dark hover:bg-cpa-dark text-white font-medium rounded-lg px-4 py-2 text-sm transition-colors"
                        @click="applyFilters"
                    >
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
                                <th class="px-4 py-3 text-left text-xs font-semibold text-cpa-text-secondary uppercase tracking-wide">Event</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-cpa-text-secondary uppercase tracking-wide">Model</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-cpa-text-secondary uppercase tracking-wide">Causer</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-cpa-text-secondary uppercase tracking-wide">IP Address</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-cpa-text-secondary uppercase tracking-wide">Date</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-cpa-border">
                            <tr
                                v-for="log in logs.data"
                                :key="log.id"
                                class="hover:bg-cpa-very-light transition-colors"
                            >
                                <td class="px-4 py-3">
                                    <span :class="['inline-flex items-center px-2 py-0.5 rounded-full text-[11px] font-semibold', getBadge(log.event)]">
                                        {{ eventLabel(log.event) }}
                                    </span>
                                </td>
                                <td class="px-4 py-3 text-cpa-text-secondary">
                                    <span v-if="log.auditable_type">
                                        {{ modelName(log.auditable_type) }}
                                        <span v-if="log.auditable_id" class="text-cpa-text-muted">#{{ log.auditable_id }}</span>
                                    </span>
                                    <span v-else class="text-cpa-text-muted">—</span>
                                </td>
                                <td class="px-4 py-3">
                                    <span v-if="log.causer" class="text-cpa-text-primary">
                                        {{ log.causer.first_name }} {{ log.causer.last_name }}
                                        <span class="text-cpa-text-muted text-xs block">{{ log.causer.email }}</span>
                                    </span>
                                    <span v-else class="text-cpa-text-muted text-xs">System</span>
                                </td>
                                <td class="px-4 py-3 text-cpa-text-secondary font-mono text-xs">
                                    {{ log.ip_address ?? '—' }}
                                </td>
                                <td class="px-4 py-3 text-cpa-text-muted text-xs whitespace-nowrap">
                                    {{ new Date(log.created_at).toLocaleString() }}
                                </td>
                            </tr>
                            <tr v-if="!logs.data.length">
                                <td colspan="5" class="px-4 py-12 text-center text-cpa-text-muted text-sm">
                                    No audit log entries match your filters.
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div class="border-t border-cpa-border px-4 py-3">
                    <Pagination :links="logs.links" :total="logs.total" :per-page="logs.per_page" />
                </div>
            </div>

        </div>
    </PlatformLayout>
</template>
