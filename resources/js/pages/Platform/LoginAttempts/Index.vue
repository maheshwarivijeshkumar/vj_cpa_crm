<script setup lang="ts">
import { ref } from 'vue'
import { router } from '@inertiajs/vue3'
import { ShieldAlert, Search, CheckCircle, XCircle, RefreshCw } from '@lucide/vue'
import PlatformLayout from '@/layouts/PlatformLayout.vue'
import Pagination from '@/components/ui/Pagination.vue'

interface LoginAttempt {
    id: number
    email: string
    ip_address: string
    user_agent?: string
    was_successful: boolean
    failure_reason?: string
    created_at: string
}

interface PaginatedAttempts {
    data: LoginAttempt[]
    current_page: number
    last_page: number
    per_page: number
    total: number
    links: Array<{ url: string | null; label: string; active: boolean }>
}

const props = defineProps<{
    attempts: PaginatedAttempts
    filters: {
        search: string
        failed_only: boolean
        date: string
        per_page: number
        sort_by: string
        sort_dir: string
    }
    perPageOpts: number[]
    stats: {
        total_today: number
        failed_today: number
        unique_ips: number
    }
}>()

const search    = ref(props.filters.search ?? '')
const failedOnly = ref(props.filters.failed_only ?? false)
const date      = ref(props.filters.date ?? '')
const perPage   = ref(props.filters.per_page ?? 25)

function applyFilters() {
    router.get('/platform/login-attempts', {
        search:      search.value || undefined,
        failed_only: failedOnly.value || undefined,
        date:        date.value || undefined,
        per_page:    perPage.value,
    }, { preserveState: true, replace: true })
}

function clearFilters() {
    search.value    = ''
    failedOnly.value = false
    date.value      = ''
    router.get('/platform/login-attempts', {}, { preserveState: false })
}
</script>

<template>
    <PlatformLayout>
        <template #header>
            <span class="text-sm font-medium text-cpa-text-primary">Login Attempts</span>
        </template>

        <div class="space-y-6">

            <!-- Header -->
            <div class="flex items-center justify-between flex-wrap gap-4">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-cpa-danger-bg flex items-center justify-center">
                        <ShieldAlert :size="20" class="text-cpa-danger" />
                    </div>
                    <div>
                        <h1 class="text-xl font-semibold text-cpa-text-primary">Login Attempts</h1>
                        <p class="text-xs text-cpa-text-muted mt-0.5">Security authentication log</p>
                    </div>
                </div>
                <button
                    class="flex items-center gap-1.5 border border-cpa-border text-cpa-text-secondary hover:border-cpa-medium hover:text-cpa-dark rounded-lg px-3 py-2 text-sm font-medium transition-colors"
                    @click="clearFilters"
                >
                    <RefreshCw :size="14" /> Reset
                </button>
            </div>

            <!-- Stats -->
            <div class="grid grid-cols-3 gap-4">
                <div class="bg-white border border-cpa-border rounded-xl p-4 shadow-sm">
                    <p class="text-xs font-semibold text-cpa-text-secondary uppercase tracking-wide">Total Today</p>
                    <p class="mt-1 text-3xl font-bold text-cpa-text-primary">{{ stats.total_today }}</p>
                </div>
                <div class="bg-white border border-cpa-danger-bg rounded-xl p-4 shadow-sm">
                    <p class="text-xs font-semibold text-cpa-danger uppercase tracking-wide">Failed Today</p>
                    <p class="mt-1 text-3xl font-bold text-cpa-danger">{{ stats.failed_today }}</p>
                </div>
                <div class="bg-white border border-cpa-border rounded-xl p-4 shadow-sm">
                    <p class="text-xs font-semibold text-cpa-text-secondary uppercase tracking-wide">Unique IPs</p>
                    <p class="mt-1 text-3xl font-bold text-cpa-text-primary">{{ stats.unique_ips }}</p>
                </div>
            </div>

            <!-- Filters -->
            <div class="bg-white border border-cpa-border rounded-xl shadow-sm p-4">
                <div class="flex flex-wrap items-end gap-3">
                    <div class="flex-1 min-w-[200px]">
                        <label class="block text-xs font-medium text-cpa-text-secondary mb-1">Search email / IP</label>
                        <div class="relative">
                            <Search :size="14" class="absolute left-2.5 top-2.5 text-cpa-text-muted" />
                            <input
                                v-model="search"
                                type="text"
                                placeholder="user@example.com or 192.168.x.x"
                                class="w-full pl-8 pr-3 py-2 text-sm border border-cpa-border rounded-lg bg-cpa-bg focus:outline-none focus:ring-2 focus:ring-cpa-medium placeholder:text-cpa-text-muted"
                                @keyup.enter="applyFilters"
                            />
                        </div>
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-cpa-text-secondary mb-1">Date</label>
                        <input
                            v-model="date"
                            type="date"
                            class="px-3 py-2 text-sm border border-cpa-border rounded-lg bg-cpa-bg focus:outline-none focus:ring-2 focus:ring-cpa-medium"
                            @change="applyFilters"
                        />
                    </div>
                    <label class="flex items-center gap-2 text-sm text-cpa-text-primary cursor-pointer mb-0.5">
                        <input v-model="failedOnly" type="checkbox" class="rounded border-cpa-border text-cpa-medium-dark" @change="applyFilters" />
                        Failed only
                    </label>
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
                                <th class="px-4 py-3 text-left text-xs font-semibold text-cpa-text-secondary uppercase tracking-wide">Result</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-cpa-text-secondary uppercase tracking-wide">Email</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-cpa-text-secondary uppercase tracking-wide">IP Address</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-cpa-text-secondary uppercase tracking-wide">Reason</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-cpa-text-secondary uppercase tracking-wide">Timestamp</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-cpa-border">
                            <tr
                                v-for="attempt in attempts.data"
                                :key="attempt.id"
                                class="hover:bg-cpa-very-light transition-colors"
                            >
                                <td class="px-4 py-3">
                                    <span v-if="attempt.was_successful" class="inline-flex items-center gap-1 text-cpa-success text-xs font-semibold">
                                        <CheckCircle :size="13" /> Success
                                    </span>
                                    <span v-else class="inline-flex items-center gap-1 text-cpa-danger text-xs font-semibold">
                                        <XCircle :size="13" /> Failed
                                    </span>
                                </td>
                                <td class="px-4 py-3 text-cpa-text-primary font-medium">{{ attempt.email }}</td>
                                <td class="px-4 py-3 text-cpa-text-secondary font-mono text-xs">{{ attempt.ip_address }}</td>
                                <td class="px-4 py-3 text-cpa-text-muted text-xs">{{ attempt.failure_reason ?? '—' }}</td>
                                <td class="px-4 py-3 text-cpa-text-muted text-xs whitespace-nowrap">
                                    {{ new Date(attempt.created_at).toLocaleString() }}
                                </td>
                            </tr>
                            <tr v-if="!attempts.data.length">
                                <td colspan="5" class="px-4 py-12 text-center text-cpa-text-muted text-sm">
                                    No login attempts match your filters.
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <div class="border-t border-cpa-border px-4 py-3">
                    <Pagination :links="attempts.links" :total="attempts.total" :per-page="attempts.per_page" />
                </div>
            </div>

        </div>
    </PlatformLayout>
</template>
