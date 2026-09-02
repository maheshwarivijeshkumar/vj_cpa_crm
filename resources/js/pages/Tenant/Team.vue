<script setup lang="ts">
import { ref, reactive } from 'vue'
import { router, useForm } from '@inertiajs/vue3'
import {
    Users, Plus, Search, Mail, MoreHorizontal,
    CheckCircle, XCircle, Trash2, RefreshCw,
} from '@lucide/vue'
import AppLayout from '@/layouts/AppLayout.vue'
import Pagination from '@/components/ui/Pagination.vue'
import StatusBadge from '@/components/ui/StatusBadge.vue'

interface TeamUser {
    id: number
    first_name: string
    last_name: string
    email: string
    status: string
    invited_at?: string
    last_login_at?: string
    roles?: Array<{ id: number; name: string; slug: string }>
    office?: { id: number; name: string } | null
}

interface PaginatedUsers {
    data: TeamUser[]
    total: number
    per_page: number
    links: Array<{ url: string | null; label: string; active: boolean }>
}

const props = defineProps<{
    users: PaginatedUsers
    roles: Array<{ id: number; name: string; slug: string }>
    offices: Array<{ id: number; name: string }>
    filters: {
        search: string
        role: string
        sort_by: string
        per_page: number
    }
    perPageOpts: number[]
}>()

// ── Filters ────────────────────────────────────────────────────────────────
const search  = ref(props.filters.search ?? '')
const role    = ref(props.filters.role ?? '')
const perPage = ref(props.filters.per_page ?? 15)

function applyFilters() {
    router.get('/portal/team', {
        search:   search.value || undefined,
        role:     role.value   || undefined,
        per_page: perPage.value,
    }, { preserveState: true, replace: true })
}

// ── Invite modal ──────────────────────────────────────────────────────────
const showInviteModal = ref(false)
const inviteSuccess   = ref('')

const inviteForm = useForm({
    first_name: '',
    last_name:  '',
    email:      '',
    role:       props.roles[0]?.slug ?? '',
    office_id:  '' as string | number,
})

function openInvite()  { showInviteModal.value = true }
function closeInvite() { showInviteModal.value = false; inviteForm.reset() }

async function submitInvite() {
    inviteForm.post('/portal/team/invite', {
        onSuccess: () => {
            inviteSuccess.value = `Invitation sent to ${inviteForm.email}`
            closeInvite()
            router.reload({ only: ['users'] })
            setTimeout(() => { inviteSuccess.value = '' }, 4000)
        },
    })
}

// ── Edit member row ────────────────────────────────────────────────────────
const editingId = ref<number | null>(null)
const editState = reactive<{ role: string; status: string; office_id: string | number }>({
    role: '', status: '', office_id: '',
})

function startEdit(user: TeamUser) {
    editingId.value  = user.id
    editState.role   = user.roles?.[0]?.slug ?? ''
    editState.status = user.status
    editState.office_id = user.office?.id ?? ''
}

function cancelEdit() { editingId.value = null }

function saveEdit(userId: number) {
    router.patch(`/portal/team/${userId}`, {
        role:      editState.role,
        status:    editState.status,
        office_id: editState.office_id || null,
    }, {
        preserveState: true,
        onSuccess: () => { editingId.value = null; router.reload({ only: ['users'] }) },
    })
}

// ── Remove ─────────────────────────────────────────────────────────────────
function removeUser(user: TeamUser) {
    if (!confirm(`Remove ${user.first_name} ${user.last_name} from your team?\n\nThey will lose access immediately.`)) return
    router.delete(`/portal/team/${user.id}`, {
        preserveState: false,
    })
}

// ── Resend invite ──────────────────────────────────────────────────────────
function resendInvite(user: TeamUser) {
    router.post(`/portal/team/${user.id}/resend-invite`, {}, {
        preserveState: true,
        onSuccess: () => {
            inviteSuccess.value = `Invitation resent to ${user.email}`
            setTimeout(() => { inviteSuccess.value = '' }, 3000)
        },
    })
}
</script>

<template>
    <AppLayout>
        <div class="space-y-6">

            <!-- Page header -->
            <div class="flex items-center justify-between flex-wrap gap-4">
                <div class="flex items-center gap-3">
                    <div class="w-9 h-9 rounded-xl bg-cpa-very-light flex items-center justify-center">
                        <Users :size="18" class="text-cpa-medium-dark" />
                    </div>
                    <div>
                        <h1 class="text-xl font-semibold text-cpa-text-primary">Team Members</h1>
                        <p class="text-xs text-cpa-text-muted">{{ users.total }} member{{ users.total === 1 ? '' : 's' }}</p>
                    </div>
                </div>
                <button
                    class="flex items-center gap-1.5 bg-cpa-medium-dark hover:bg-cpa-dark text-white font-medium rounded-lg px-4 py-2 text-sm transition-colors"
                    @click="openInvite"
                >
                    <Plus :size="15" /> Invite Member
                </button>
            </div>

            <!-- Success flash -->
            <Transition name="fade">
                <div
                    v-if="inviteSuccess"
                    class="flex items-center gap-2.5 bg-cpa-success-bg text-cpa-success border border-cpa-success/20 rounded-xl px-4 py-3 text-sm font-medium"
                >
                    <CheckCircle :size="16" class="flex-shrink-0" />
                    {{ inviteSuccess }}
                </div>
            </Transition>

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
                                placeholder="Name or email…"
                                class="w-full pl-8 pr-3 py-2 text-sm border border-cpa-border rounded-lg bg-cpa-bg focus:outline-none focus:ring-2 focus:ring-cpa-medium placeholder:text-cpa-text-muted"
                                @keyup.enter="applyFilters"
                            />
                        </div>
                    </div>
                    <div class="min-w-[140px]">
                        <label class="block text-xs font-medium text-cpa-text-secondary mb-1">Role</label>
                        <select v-model="role" class="w-full px-3 py-2 text-sm border border-cpa-border rounded-lg bg-cpa-bg focus:outline-none focus:ring-2 focus:ring-cpa-medium" @change="applyFilters">
                            <option value="">All Roles</option>
                            <option v-for="r in roles" :key="r.slug" :value="r.slug">{{ r.name }}</option>
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
                                <th class="px-4 py-3 text-left text-xs font-semibold text-cpa-text-secondary uppercase tracking-wide">Member</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-cpa-text-secondary uppercase tracking-wide">Role</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-cpa-text-secondary uppercase tracking-wide">Office</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-cpa-text-secondary uppercase tracking-wide">Status</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-cpa-text-secondary uppercase tracking-wide">Last Login</th>
                                <th class="px-4 py-3 text-right text-xs font-semibold text-cpa-text-secondary uppercase tracking-wide">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-cpa-border">
                            <template v-for="user in users.data" :key="user.id">
                                <!-- View row -->
                                <tr v-if="editingId !== user.id" class="hover:bg-cpa-very-light transition-colors">
                                    <td class="px-4 py-3">
                                        <div class="flex items-center gap-2.5">
                                            <div class="w-8 h-8 rounded-full bg-cpa-medium flex items-center justify-center text-white text-xs font-bold flex-shrink-0 select-none">
                                                {{ user.first_name[0] }}{{ user.last_name[0] }}
                                            </div>
                                            <div>
                                                <p class="font-medium text-cpa-text-primary">{{ user.first_name }} {{ user.last_name }}</p>
                                                <p class="text-xs text-cpa-text-muted">{{ user.email }}</p>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-4 py-3 text-cpa-text-secondary text-xs">
                                        {{ user.roles?.[0]?.name ?? '—' }}
                                    </td>
                                    <td class="px-4 py-3 text-cpa-text-secondary text-xs">
                                        {{ user.office?.name ?? '—' }}
                                    </td>
                                    <td class="px-4 py-3">
                                        <StatusBadge :status="user.status" />
                                    </td>
                                    <td class="px-4 py-3 text-cpa-text-muted text-xs whitespace-nowrap">
                                        {{ user.last_login_at ? new Date(user.last_login_at).toLocaleDateString() : (user.status === 'invited' ? 'Invited' : 'Never') }}
                                    </td>
                                    <td class="px-4 py-3">
                                        <div class="flex items-center justify-end gap-1.5">
                                            <button
                                                v-if="user.status === 'invited'"
                                                class="inline-flex items-center gap-1 text-xs text-cpa-medium-dark hover:text-cpa-dark font-medium px-2 py-1 rounded hover:bg-cpa-very-light transition-colors"
                                                title="Resend invitation"
                                                @click="resendInvite(user)"
                                            >
                                                <RefreshCw :size="12" /> Resend
                                            </button>
                                            <button
                                                class="inline-flex items-center gap-1 text-xs text-cpa-medium-dark hover:text-cpa-dark font-medium px-2 py-1 rounded hover:bg-cpa-very-light transition-colors"
                                                @click="startEdit(user)"
                                            >
                                                Edit
                                            </button>
                                            <button
                                                class="inline-flex items-center gap-1 text-xs text-cpa-danger hover:text-red-700 px-2 py-1 rounded hover:bg-cpa-danger-bg transition-colors"
                                                @click="removeUser(user)"
                                            >
                                                <Trash2 :size="12" />
                                            </button>
                                        </div>
                                    </td>
                                </tr>

                                <!-- Inline edit row -->
                                <tr v-else class="bg-cpa-very-light">
                                    <td class="px-4 py-3">
                                        <div class="flex items-center gap-2.5">
                                            <div class="w-8 h-8 rounded-full bg-cpa-medium flex items-center justify-center text-white text-xs font-bold flex-shrink-0 select-none">
                                                {{ user.first_name[0] }}{{ user.last_name[0] }}
                                            </div>
                                            <div>
                                                <p class="font-medium text-cpa-text-primary">{{ user.first_name }} {{ user.last_name }}</p>
                                                <p class="text-xs text-cpa-text-muted">{{ user.email }}</p>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-4 py-3">
                                        <select v-model="editState.role" class="px-2 py-1 text-xs border border-cpa-border rounded-lg bg-white focus:outline-none focus:ring-2 focus:ring-cpa-medium">
                                            <option v-for="r in roles" :key="r.slug" :value="r.slug">{{ r.name }}</option>
                                        </select>
                                    </td>
                                    <td class="px-4 py-3">
                                        <select v-model="editState.office_id" class="px-2 py-1 text-xs border border-cpa-border rounded-lg bg-white focus:outline-none focus:ring-2 focus:ring-cpa-medium">
                                            <option value="">No office</option>
                                            <option v-for="o in offices" :key="o.id" :value="o.id">{{ o.name }}</option>
                                        </select>
                                    </td>
                                    <td class="px-4 py-3">
                                        <select v-model="editState.status" class="px-2 py-1 text-xs border border-cpa-border rounded-lg bg-white focus:outline-none focus:ring-2 focus:ring-cpa-medium">
                                            <option value="active">Active</option>
                                            <option value="inactive">Inactive</option>
                                            <option value="suspended">Suspended</option>
                                        </select>
                                    </td>
                                    <td class="px-4 py-3 text-cpa-text-muted text-xs" />
                                    <td class="px-4 py-3">
                                        <div class="flex items-center justify-end gap-1.5">
                                            <button class="text-xs bg-cpa-medium-dark hover:bg-cpa-dark text-white font-medium px-3 py-1 rounded-lg transition-colors" @click="saveEdit(user.id)">Save</button>
                                            <button class="text-xs border border-cpa-border text-cpa-text-secondary hover:bg-cpa-bg px-3 py-1 rounded-lg transition-colors" @click="cancelEdit">Cancel</button>
                                        </div>
                                    </td>
                                </tr>
                            </template>

                            <tr v-if="!users.data.length">
                                <td colspan="6" class="px-4 py-16 text-center text-cpa-text-muted text-sm">
                                    <Users :size="32" class="mx-auto text-cpa-border mb-3" />
                                    No team members yet.
                                    <button class="ml-1 text-cpa-medium-dark hover:underline font-medium" @click="openInvite">Invite your first member.</button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <div class="border-t border-cpa-border px-4 py-3">
                    <Pagination :links="users.links" :total="users.total" :per-page="users.per_page" />
                </div>
            </div>
        </div>

        <!-- Invite modal -->
        <Transition name="fade">
            <div v-if="showInviteModal" class="fixed inset-0 bg-black/50 z-50 flex items-center justify-center p-4" @click.self="closeInvite">
                <div class="bg-white rounded-xl shadow-xl max-w-md w-full p-6">
                    <div class="flex items-center gap-3 mb-5">
                        <div class="w-9 h-9 bg-cpa-very-light rounded-xl flex items-center justify-center">
                            <Mail :size="18" class="text-cpa-medium-dark" />
                        </div>
                        <h3 class="text-base font-semibold text-cpa-text-primary">Invite Team Member</h3>
                    </div>

                    <div class="space-y-4">
                        <div class="grid grid-cols-2 gap-3">
                            <div>
                                <label class="block text-sm font-medium text-cpa-text-primary mb-1.5">First Name</label>
                                <input v-model="inviteForm.first_name" type="text" class="w-full px-3 py-2 text-sm border border-cpa-border rounded-lg focus:outline-none focus:ring-2 focus:ring-cpa-medium" :class="{ 'border-cpa-danger': inviteForm.errors.first_name }" />
                                <p v-if="inviteForm.errors.first_name" class="text-cpa-danger text-xs mt-0.5">{{ inviteForm.errors.first_name }}</p>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-cpa-text-primary mb-1.5">Last Name</label>
                                <input v-model="inviteForm.last_name" type="text" class="w-full px-3 py-2 text-sm border border-cpa-border rounded-lg focus:outline-none focus:ring-2 focus:ring-cpa-medium" :class="{ 'border-cpa-danger': inviteForm.errors.last_name }" />
                                <p v-if="inviteForm.errors.last_name" class="text-cpa-danger text-xs mt-0.5">{{ inviteForm.errors.last_name }}</p>
                            </div>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-cpa-text-primary mb-1.5">Email Address</label>
                            <input v-model="inviteForm.email" type="email" placeholder="colleague@yourfirm.com" class="w-full px-3 py-2 text-sm border border-cpa-border rounded-lg focus:outline-none focus:ring-2 focus:ring-cpa-medium placeholder:text-cpa-text-muted" :class="{ 'border-cpa-danger': inviteForm.errors.email }" />
                            <p v-if="inviteForm.errors.email" class="text-cpa-danger text-xs mt-0.5">{{ inviteForm.errors.email }}</p>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-cpa-text-primary mb-1.5">Role</label>
                            <select v-model="inviteForm.role" class="w-full px-3 py-2 text-sm border border-cpa-border rounded-lg bg-white focus:outline-none focus:ring-2 focus:ring-cpa-medium">
                                <option v-for="r in roles" :key="r.slug" :value="r.slug">{{ r.name }}</option>
                            </select>
                        </div>

                        <div v-if="offices.length">
                            <label class="block text-sm font-medium text-cpa-text-primary mb-1.5">Office <span class="text-cpa-text-muted font-normal">(optional)</span></label>
                            <select v-model="inviteForm.office_id" class="w-full px-3 py-2 text-sm border border-cpa-border rounded-lg bg-white focus:outline-none focus:ring-2 focus:ring-cpa-medium">
                                <option value="">No specific office</option>
                                <option v-for="o in offices" :key="o.id" :value="o.id">{{ o.name }}</option>
                            </select>
                        </div>

                        <p class="text-xs text-cpa-text-muted bg-cpa-very-light rounded-lg px-3 py-2">
                            An invitation email will be sent. The recipient sets their own password.
                        </p>
                    </div>

                    <div class="flex gap-2 mt-5">
                        <button class="flex-1 border border-cpa-border text-cpa-text-secondary hover:bg-cpa-bg rounded-lg py-2 text-sm font-medium transition-colors" @click="closeInvite">Cancel</button>
                        <button
                            :disabled="inviteForm.processing"
                            class="flex-1 bg-cpa-medium-dark hover:bg-cpa-dark text-white font-medium rounded-lg py-2 text-sm transition-colors disabled:opacity-60"
                            @click="submitInvite"
                        >
                            {{ inviteForm.processing ? 'Sending…' : 'Send Invitation' }}
                        </button>
                    </div>
                </div>
            </div>
        </Transition>
    </AppLayout>
</template>

<style scoped>
.fade-enter-active, .fade-leave-active { transition: opacity .2s ease; }
.fade-enter-from, .fade-leave-to { opacity: 0; }
</style>
