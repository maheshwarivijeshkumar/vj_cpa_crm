<script setup lang="ts">
import PlatformLayout from '@/layouts/PlatformLayout.vue'
import SeoHead from '@/components/ui/SeoHead.vue'
import StatusBadge from '@/components/ui/StatusBadge.vue'
import EmptyState from '@/components/ui/EmptyState.vue'
import { Link, router } from '@inertiajs/vue3'
import { ref, watch } from 'vue'
import {
    Search, Users, RefreshCw, ChevronLeft, ChevronRight,
    MoreHorizontal, Eye, Ban, RotateCcw, Trash2, LogOut,
    Shield,
} from '@lucide/vue'
import { useUiStore } from '@/stores/ui'

defineOptions({ layout: PlatformLayout })

const ui = useUiStore()
ui.setPageTitle('Users')

interface UserItem {
    id: number; uuid: string; name: string; email: string; username: string | null
    user_type: { value: string; label: string }
    status:    { value: string; label: string; badge_class: string }
    two_factor_enabled: boolean
    last_login_at: string | null
    created_at: string
    tenant?: { id: number; name: string; slug: string } | null
}
interface Meta { current_page: number; last_page: number; per_page: number; total: number; from: number | null; to: number | null; per_page_options: number[] }

const props = defineProps<{
    users:       { data: UserItem[]; meta: Meta }
    filters:     { search: string; user_type: string; status: string; per_page: number; page: number }
    perPageOpts: number[]
}>()

const search   = ref(props.filters.search ?? '')
const userType = ref(props.filters.user_type ?? '')
const status   = ref(props.filters.status ?? '')
const perPage  = ref(props.filters.per_page ?? 25)

let timer: ReturnType<typeof setTimeout>
function reload() {
    clearTimeout(timer)
    timer = setTimeout(() => {
        router.get('/platform/users', {
            search:    search.value    || undefined,
            user_type: userType.value  || undefined,
            status:    status.value    || undefined,
            per_page:  perPage.value,
        }, { preserveState: true, replace: true })
    }, 300)
}
watch([search, userType, status, perPage], reload)

function goPage(page: number) {
    router.get('/platform/users', { ...props.filters, page }, { preserveState: true })
}

function formatDate(d: string | null) {
    if (!d) return 'Never'
    return new Date(d).toLocaleDateString('en-CA', { month: 'short', day: 'numeric', year: 'numeric' })
}

const openMenuId = ref<number | null>(null)

function forceLogout(id: number) {
    if (!confirm('Force logout all sessions for this user?')) return
    router.post(`/platform/users/${id}/force-logout`, {}, { preserveState: false })
}
function deleteUser(id: number) {
    if (!confirm('Delete this user?')) return
    router.delete(`/platform/users/${id}`, { preserveState: false })
}
function restoreUser(id: number) {
    router.post(`/platform/users/${id}/restore`, {}, { preserveState: false })
}
</script>

<template>
    <SeoHead :seo="{ title: 'Users — Platform Admin', robots: 'noindex,nofollow' }" />

    <div class="flex items-center justify-between mb-6 flex-wrap gap-3">
        <div>
            <h1 class="text-2xl font-800 text-gray-900 tracking-tight">Users</h1>
            <p class="text-sm text-gray-500 mt-0.5">
                {{ users.meta.total.toLocaleString() }} user{{ users.meta.total !== 1 ? 's' : '' }} across all tenants
            </p>
        </div>
        <button class="btn btn-outline btn-sm" @click="router.reload()">
            <RefreshCw :size="14" /> Refresh
        </button>
    </div>

    <div class="data-table-wrapper">
        <!-- Toolbar -->
        <div class="table-toolbar flex-wrap gap-2">
            <div class="relative">
                <Search :size="14" class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400" />
                <input v-model="search" type="search" placeholder="Search users…" class="form-input pl-8 text-sm w-52" />
            </div>
            <select v-model="userType" class="form-input text-sm w-40">
                <option value="">All types</option>
                <option value="platform_admin">Platform Admin</option>
                <option value="firm_owner">Firm Owner</option>
                <option value="firm_user">Staff</option>
                <option value="client">Client</option>
            </select>
            <select v-model="status" class="form-input text-sm w-36">
                <option value="">All statuses</option>
                <option value="active">Active</option>
                <option value="inactive">Inactive</option>
                <option value="suspended">Suspended</option>
                <option value="invited">Invited</option>
            </select>
            <div class="table-toolbar-right">
                <select v-model="perPage" class="form-input text-sm w-24">
                    <option v-for="opt in perPageOpts" :key="opt" :value="opt">{{ opt }} / page</option>
                </select>
            </div>
        </div>

        <table class="data-table">
            <thead>
                <tr>
                    <th>User</th>
                    <th>Type</th>
                    <th>Status</th>
                    <th>Tenant</th>
                    <th>2FA</th>
                    <th>Last Login</th>
                    <th class="w-12" />
                </tr>
            </thead>
            <tbody>
                <tr v-if="!users.data.length">
                    <td colspan="7">
                        <EmptyState title="No users found" description="Try adjusting your search or filters." />
                    </td>
                </tr>
                <tr v-for="user in users.data" :key="user.id">
                    <td>
                        <p class="font-600 text-gray-900">{{ user.name }}</p>
                        <p class="text-xs text-gray-400 mt-0.5">{{ user.email }}</p>
                    </td>
                    <td>
                        <span class="badge badge-teal text-xs">{{ user.user_type.label }}</span>
                    </td>
                    <td>
                        <StatusBadge :status="user.status.value" :label="user.status.label" />
                    </td>
                    <td>
                        <Link v-if="user.tenant" :href="`/platform/tenants/${user.tenant.id}`" class="text-xs text-cpa-medium-dark hover:underline">
                            {{ user.tenant.name }}
                        </Link>
                        <span v-else class="text-xs text-gray-400">—</span>
                    </td>
                    <td>
                        <Shield :size="14" :class="user.two_factor_enabled ? 'text-cpa-success' : 'text-gray-300'" />
                    </td>
                    <td class="text-sm text-gray-500">{{ formatDate(user.last_login_at) }}</td>
                    <td class="relative">
                        <button
                            class="w-8 h-8 flex items-center justify-center rounded-lg hover:bg-gray-100 text-gray-400"
                            @click.stop="openMenuId = openMenuId === user.id ? null : user.id"
                        >
                            <MoreHorizontal :size="16" />
                        </button>
                        <div
                            v-if="openMenuId === user.id"
                            class="dropdown-menu right-0 top-full mt-1 w-44"
                            style="position:absolute; z-index:60"
                        >
                            <button class="dropdown-item w-full" @click="forceLogout(user.id); openMenuId = null">
                                <LogOut :size="14" /> Force Logout
                            </button>
                            <div class="dropdown-divider" />
                            <button class="dropdown-item danger w-full" @click="deleteUser(user.id); openMenuId = null">
                                <Trash2 :size="14" /> Delete
                            </button>
                        </div>
                    </td>
                </tr>
            </tbody>
        </table>

        <div class="data-table-footer">
            <p class="text-xs">Showing {{ users.meta.from ?? 0 }}–{{ users.meta.to ?? 0 }} of {{ users.meta.total.toLocaleString() }}</p>
            <nav class="flex items-center gap-1">
                <button class="pagination-btn" :disabled="users.meta.current_page <= 1" @click="goPage(users.meta.current_page - 1)">
                    <ChevronLeft :size="13" />
                </button>
                <template v-for="p in users.meta.last_page" :key="p">
                    <button
                        v-if="p === 1 || p === users.meta.last_page || Math.abs(p - users.meta.current_page) <= 1"
                        class="pagination-btn" :class="{ active: p === users.meta.current_page }"
                        @click="goPage(p)"
                    >{{ p }}</button>
                    <span v-else-if="Math.abs(p - users.meta.current_page) === 2" class="px-1 text-gray-400 text-xs">…</span>
                </template>
                <button class="pagination-btn" :disabled="users.meta.current_page >= users.meta.last_page" @click="goPage(users.meta.current_page + 1)">
                    <ChevronRight :size="13" />
                </button>
            </nav>
        </div>
    </div>
</template>
