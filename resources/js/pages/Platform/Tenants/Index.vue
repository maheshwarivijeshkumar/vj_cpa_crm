<script setup lang="ts">
import PlatformLayout from '@/layouts/PlatformLayout.vue'
import SeoHead from '@/components/ui/SeoHead.vue'
import StatusBadge from '@/components/ui/StatusBadge.vue'
import EmptyState from '@/components/ui/EmptyState.vue'
import { Link, router } from '@inertiajs/vue3'
import { ref, watch } from 'vue'
import {
    Search, Building2, Plus, RefreshCw, ChevronLeft, ChevronRight,
    MoreHorizontal, Eye, Pencil, Ban, RotateCcw, Trash2,
} from '@lucide/vue'
import { useUiStore } from '@/stores/ui'

defineOptions({ layout: PlatformLayout })

const ui = useUiStore()
ui.setPageTitle('Tenants')

interface Tenant {
    id: number; uuid: string; name: string; slug: string; email: string
    plan: { value: string; label: string }
    status: { value: string; label: string; badge_class: string }
    trial_ends_at: string | null
    created_at: string
    user_count?: number
}

interface PaginationMeta {
    current_page: number; last_page: number; per_page: number
    total: number; from: number | null; to: number | null; per_page_options: number[]
}

const props = defineProps<{
    tenants:     { data: Tenant[]; meta: PaginationMeta }
    filters:     { search: string; sort_by: string; sort_dir: string; per_page: number; page: number }
    perPageOpts: number[]
    stats?:      { total: number; active: number; trial: number; suspended: number }
}>()

const search  = ref(props.filters.search ?? '')
const perPage = ref(props.filters.per_page ?? 25)
let searchTimer: ReturnType<typeof setTimeout>

function doSearch() {
    clearTimeout(searchTimer)
    searchTimer = setTimeout(() => {
        router.get('/platform/tenants', { search: search.value || undefined, per_page: perPage.value }, {
            preserveState: true, replace: true,
        })
    }, 350)
}

function changePerPage() {
    router.get('/platform/tenants', { search: search.value || undefined, per_page: perPage.value }, {
        preserveState: true, replace: true,
    })
}

watch(search, doSearch)

function goPage(page: number) {
    router.get('/platform/tenants', { ...props.filters, page }, { preserveState: true })
}

function formatDate(d: string | null) {
    if (!d) return '—'
    return new Date(d).toLocaleDateString('en-CA', { year: 'numeric', month: 'short', day: 'numeric' })
}

const openMenuId = ref<number | null>(null)
function toggleMenu(id: number) { openMenuId.value = openMenuId.value === id ? null : id }

function suspendTenant(id: number) {
    if (!confirm('Suspend this tenant? Their users will lose access.')) return
    router.post(`/platform/tenants/${id}/suspend`, {}, { preserveState: false })
}
function reinstateTenant(id: number) {
    router.post(`/platform/tenants/${id}/reinstate`, {}, { preserveState: false })
}
function deleteTenant(id: number) {
    if (!confirm('Delete this tenant? This action cannot be easily undone.')) return
    router.delete(`/platform/tenants/${id}`, { preserveState: false })
}
</script>

<template>
    <SeoHead :seo="{ title: 'Tenants — Platform Admin', robots: 'noindex,nofollow' }" />

    <!-- Header -->
    <div class="flex items-center justify-between mb-6 flex-wrap gap-3">
        <div>
            <h1 class="text-2xl font-800 text-gray-900 tracking-tight">Tenants</h1>
            <p class="text-sm text-gray-500 mt-0.5">
                {{ tenants.meta.total.toLocaleString() }} accounting firm{{ tenants.meta.total !== 1 ? 's' : '' }} registered
            </p>
        </div>
        <div class="flex gap-2">
            <button class="btn btn-outline btn-sm" @click="router.reload()">
                <RefreshCw :size="14" /> Refresh
            </button>
            <button class="btn btn-primary btn-sm">
                <Plus :size="14" /> Add Tenant
            </button>
        </div>
    </div>

    <!-- Stats strip -->
    <div v-if="stats" class="grid grid-cols-4 gap-3 mb-5">
        <div v-for="[label, val, color] in [['Total', stats.total, 'gray'], ['Active', stats.active, 'green'], ['Trial', stats.trial, 'amber'], ['Suspended', stats.suspended, 'red']]"
            :key="label"
            class="bg-white border border-gray-100 rounded-xl px-4 py-3 text-center"
        >
            <p class="text-xl font-800 text-gray-900">{{ val }}</p>
            <p class="text-xs text-gray-500 font-500 uppercase tracking-wide">{{ label }}</p>
        </div>
    </div>

    <!-- Table wrapper -->
    <div class="data-table-wrapper">
        <!-- Toolbar -->
        <div class="table-toolbar">
            <div class="relative">
                <Search :size="14" class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400" />
                <input
                    v-model="search"
                    type="search"
                    placeholder="Search tenants…"
                    class="form-input pl-8 text-sm w-56"
                />
            </div>
            <div class="table-toolbar-right">
                <select v-model="perPage" class="form-input text-sm w-24" @change="changePerPage">
                    <option v-for="opt in perPageOpts" :key="opt" :value="opt">{{ opt }} / page</option>
                </select>
            </div>
        </div>

        <!-- Table -->
        <table class="data-table">
            <thead>
                <tr>
                    <th>Firm Name</th>
                    <th>Email</th>
                    <th>Plan</th>
                    <th>Status</th>
                    <th>Users</th>
                    <th>Created</th>
                    <th class="w-12" />
                </tr>
            </thead>
            <tbody>
                <tr v-if="!tenants.data.length">
                    <td colspan="7">
                        <EmptyState
                            title="No tenants found"
                            description="No accounting firms match your current search."
                        />
                    </td>
                </tr>
                <tr
                    v-for="tenant in tenants.data"
                    :key="tenant.id"
                    class="group"
                >
                    <td>
                        <Link :href="`/platform/tenants/${tenant.id}`" class="font-600 text-cpa-medium-dark hover:underline">
                            {{ tenant.name }}
                        </Link>
                        <p class="text-xs text-gray-400 mt-0.5">{{ tenant.slug }}</p>
                    </td>
                    <td class="text-gray-600 text-sm">{{ tenant.email }}</td>
                    <td>
                        <span class="badge badge-teal text-xs">{{ tenant.plan.label }}</span>
                    </td>
                    <td>
                        <StatusBadge :status="tenant.status.value" :label="tenant.status.label" />
                    </td>
                    <td class="text-sm text-gray-600">{{ tenant.user_count ?? '—' }}</td>
                    <td class="text-sm text-gray-500">{{ formatDate(tenant.created_at) }}</td>
                    <td class="relative">
                        <button
                            class="w-8 h-8 flex items-center justify-center rounded-lg hover:bg-gray-100 text-gray-400 transition-colors"
                            @click.stop="toggleMenu(tenant.id)"
                        >
                            <MoreHorizontal :size="16" />
                        </button>
                        <div
                            v-if="openMenuId === tenant.id"
                            v-click-outside="() => openMenuId = null"
                            class="dropdown-menu right-0 top-full mt-1 w-44"
                            style="position:absolute; z-index:60"
                        >
                            <Link :href="`/platform/tenants/${tenant.id}`" class="dropdown-item">
                                <Eye :size="14" /> View
                            </Link>
                            <div class="dropdown-divider" />
                            <button
                                v-if="tenant.status.value !== 'suspended'"
                                class="dropdown-item danger w-full"
                                @click="suspendTenant(tenant.id)"
                            >
                                <Ban :size="14" /> Suspend
                            </button>
                            <button
                                v-else
                                class="dropdown-item w-full"
                                @click="reinstateTenant(tenant.id)"
                            >
                                <RotateCcw :size="14" /> Reinstate
                            </button>
                            <button class="dropdown-item danger w-full" @click="deleteTenant(tenant.id)">
                                <Trash2 :size="14" /> Delete
                            </button>
                        </div>
                    </td>
                </tr>
            </tbody>
        </table>

        <!-- Pagination footer -->
        <div class="data-table-footer">
            <p class="text-xs">
                Showing {{ tenants.meta.from ?? 0 }}–{{ tenants.meta.to ?? 0 }} of {{ tenants.meta.total.toLocaleString() }}
            </p>
            <nav class="flex items-center gap-1">
                <button
                    class="pagination-btn"
                    :disabled="tenants.meta.current_page <= 1"
                    @click="goPage(tenants.meta.current_page - 1)"
                >
                    <ChevronLeft :size="13" />
                </button>
                <template v-for="p in tenants.meta.last_page" :key="p">
                    <button
                        v-if="p === 1 || p === tenants.meta.last_page || Math.abs(p - tenants.meta.current_page) <= 1"
                        class="pagination-btn"
                        :class="{ active: p === tenants.meta.current_page }"
                        @click="goPage(p)"
                    >{{ p }}</button>
                    <span v-else-if="Math.abs(p - tenants.meta.current_page) === 2" class="px-1 text-gray-400 text-xs">…</span>
                </template>
                <button
                    class="pagination-btn"
                    :disabled="tenants.meta.current_page >= tenants.meta.last_page"
                    @click="goPage(tenants.meta.current_page + 1)"
                >
                    <ChevronRight :size="13" />
                </button>
            </nav>
        </div>
    </div>
</template>
