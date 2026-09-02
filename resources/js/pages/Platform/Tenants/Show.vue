<script setup lang="ts">
import { Link, router } from '@inertiajs/vue3'
import {
    ArrowLeft, Building2, Users, CreditCard, Calendar,
    MoreHorizontal, ShieldOff, ShieldCheck, Trash2,
    Mail, Phone, Globe, MapPin,
} from '@lucide/vue'
import PlatformLayout from '@/layouts/PlatformLayout.vue'
import StatusBadge from '@/components/ui/StatusBadge.vue'

interface Tenant {
    id: number
    name: string
    slug: string
    email: string
    phone?: string
    website?: string
    address?: string
    status: string
    plan: string
    users_count: number
    trial_ends_at?: string
    created_at: string
    subscription?: {
        plan: string
        status: string
        ends_at: string
        amount_paid: string
    }
    settings?: Record<string, unknown>
}

const props = defineProps<{
    tenant: Tenant
    recentAuditLogs?: Array<{
        id: number
        event: string
        created_at: string
        causer?: { first_name: string; last_name: string; email: string }
    }>
}>()

function suspend() {
    if (!confirm(`Suspend ${props.tenant.name}? They will lose access immediately.`)) return
    router.post(`/platform/tenants/${props.tenant.id}/suspend`)
}

function reinstate() {
    router.post(`/platform/tenants/${props.tenant.id}/reinstate`)
}

const planBadge: Record<string, string> = {
    trial:        'bg-cpa-light text-cpa-dark',
    starter:      'bg-cpa-very-light text-cpa-medium-dark',
    professional: 'bg-cpa-medium text-white',
    enterprise:   'bg-cpa-dark text-white',
}
</script>

<template>
    <PlatformLayout>
        <template #header>
            <div class="flex items-center gap-2 text-sm text-cpa-text-muted">
                <Link href="/platform/tenants" class="hover:text-cpa-text-primary transition-colors">Tenants</Link>
                <span>/</span>
                <span class="text-cpa-text-primary font-medium truncate">{{ tenant.name }}</span>
            </div>
        </template>

        <div class="space-y-6">

            <!-- Back + Actions header -->
            <div class="flex items-start justify-between gap-4 flex-wrap">
                <div class="flex items-center gap-3">
                    <Link
                        href="/platform/tenants"
                        class="flex items-center justify-center w-8 h-8 rounded-lg border border-cpa-border hover:bg-cpa-very-light transition-colors"
                    >
                        <ArrowLeft :size="16" class="text-cpa-text-secondary" />
                    </Link>
                    <div>
                        <h1 class="text-2xl font-semibold text-cpa-text-primary flex items-center gap-2">
                            {{ tenant.name }}
                            <StatusBadge :status="tenant.status" />
                        </h1>
                        <p class="text-sm text-cpa-text-muted mt-0.5">{{ tenant.email }}</p>
                    </div>
                </div>

                <div class="flex items-center gap-2">
                    <button
                        v-if="tenant.status === 'active'"
                        class="flex items-center gap-1.5 border border-cpa-danger text-cpa-danger hover:bg-cpa-danger-bg rounded-lg px-3 py-2 text-sm font-medium transition-colors"
                        @click="suspend"
                    >
                        <ShieldOff :size="15" /> Suspend
                    </button>
                    <button
                        v-else
                        class="flex items-center gap-1.5 border border-cpa-success text-cpa-success hover:bg-cpa-success-bg rounded-lg px-3 py-2 text-sm font-medium transition-colors"
                        @click="reinstate"
                    >
                        <ShieldCheck :size="15" /> Reinstate
                    </button>
                </div>
            </div>

            <!-- Stats row -->
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                <div class="bg-white border border-cpa-border rounded-xl p-4 shadow-sm">
                    <p class="text-xs font-semibold text-cpa-text-secondary uppercase tracking-wide">Plan</p>
                    <p class="mt-1.5 text-lg font-bold text-cpa-text-primary capitalize">{{ tenant.plan }}</p>
                    <span :class="['mt-1 inline-flex items-center px-2 py-0.5 rounded-full text-[11px] font-semibold', planBadge[tenant.plan] ?? 'bg-gray-100 text-gray-500']">
                        {{ tenant.plan }}
                    </span>
                </div>
                <div class="bg-white border border-cpa-border rounded-xl p-4 shadow-sm">
                    <p class="text-xs font-semibold text-cpa-text-secondary uppercase tracking-wide">Users</p>
                    <p class="mt-1.5 text-3xl font-bold text-cpa-text-primary">{{ tenant.users_count }}</p>
                </div>
                <div class="bg-white border border-cpa-border rounded-xl p-4 shadow-sm">
                    <p class="text-xs font-semibold text-cpa-text-secondary uppercase tracking-wide">Subscription</p>
                    <p class="mt-1.5 text-sm font-semibold text-cpa-text-primary">
                        {{ tenant.subscription?.status ?? 'None' }}
                    </p>
                    <p v-if="tenant.subscription?.ends_at" class="text-xs text-cpa-text-muted mt-0.5">
                        Until {{ new Date(tenant.subscription.ends_at).toLocaleDateString() }}
                    </p>
                </div>
                <div class="bg-white border border-cpa-border rounded-xl p-4 shadow-sm">
                    <p class="text-xs font-semibold text-cpa-text-secondary uppercase tracking-wide">Member Since</p>
                    <p class="mt-1.5 text-sm font-semibold text-cpa-text-primary">
                        {{ new Date(tenant.created_at).toLocaleDateString('en-US', { year: 'numeric', month: 'short', day: 'numeric' }) }}
                    </p>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

                <!-- Tenant details -->
                <div class="lg:col-span-2 space-y-4">
                    <div class="bg-white border border-cpa-border rounded-xl shadow-sm p-6">
                        <h2 class="text-base font-semibold text-cpa-text-primary mb-4">Tenant Information</h2>
                        <dl class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <dt class="text-xs font-semibold text-cpa-text-secondary uppercase tracking-wide">Slug</dt>
                                <dd class="mt-1 text-sm text-cpa-text-primary font-mono">{{ tenant.slug }}</dd>
                            </div>
                            <div v-if="tenant.phone">
                                <dt class="text-xs font-semibold text-cpa-text-secondary uppercase tracking-wide">Phone</dt>
                                <dd class="mt-1 text-sm text-cpa-text-primary flex items-center gap-1.5">
                                    <Phone :size="13" class="text-cpa-text-muted" /> {{ tenant.phone }}
                                </dd>
                            </div>
                            <div v-if="tenant.website">
                                <dt class="text-xs font-semibold text-cpa-text-secondary uppercase tracking-wide">Website</dt>
                                <dd class="mt-1 text-sm text-cpa-medium-dark flex items-center gap-1.5">
                                    <Globe :size="13" />
                                    <a :href="tenant.website" target="_blank" class="hover:underline">{{ tenant.website }}</a>
                                </dd>
                            </div>
                            <div v-if="tenant.address">
                                <dt class="text-xs font-semibold text-cpa-text-secondary uppercase tracking-wide">Address</dt>
                                <dd class="mt-1 text-sm text-cpa-text-primary flex items-center gap-1.5">
                                    <MapPin :size="13" class="text-cpa-text-muted flex-shrink-0" /> {{ tenant.address }}
                                </dd>
                            </div>
                        </dl>
                    </div>

                    <!-- Subscription detail -->
                    <div v-if="tenant.subscription" class="bg-white border border-cpa-border rounded-xl shadow-sm p-6">
                        <h2 class="text-base font-semibold text-cpa-text-primary mb-4 flex items-center gap-2">
                            <CreditCard :size="16" class="text-cpa-medium-dark" /> Subscription
                        </h2>
                        <dl class="grid grid-cols-2 sm:grid-cols-3 gap-4">
                            <div>
                                <dt class="text-xs font-semibold text-cpa-text-secondary uppercase tracking-wide">Plan</dt>
                                <dd class="mt-1 text-sm font-semibold text-cpa-text-primary capitalize">{{ tenant.subscription.plan }}</dd>
                            </div>
                            <div>
                                <dt class="text-xs font-semibold text-cpa-text-secondary uppercase tracking-wide">Status</dt>
                                <dd class="mt-1"><StatusBadge :status="tenant.subscription.status" /></dd>
                            </div>
                            <div>
                                <dt class="text-xs font-semibold text-cpa-text-secondary uppercase tracking-wide">Amount</dt>
                                <dd class="mt-1 text-sm font-semibold text-cpa-text-primary">${{ tenant.subscription.amount_paid }}</dd>
                            </div>
                            <div>
                                <dt class="text-xs font-semibold text-cpa-text-secondary uppercase tracking-wide">Expires</dt>
                                <dd class="mt-1 text-sm text-cpa-text-primary">
                                    {{ new Date(tenant.subscription.ends_at).toLocaleDateString() }}
                                </dd>
                            </div>
                        </dl>
                    </div>
                </div>

                <!-- Recent audit log -->
                <div class="space-y-4">
                    <div class="bg-white border border-cpa-border rounded-xl shadow-sm p-6">
                        <h2 class="text-base font-semibold text-cpa-text-primary mb-4 flex items-center gap-2">
                            <Calendar :size="16" class="text-cpa-medium-dark" /> Recent Activity
                        </h2>
                        <div v-if="recentAuditLogs?.length" class="space-y-3">
                            <div
                                v-for="log in recentAuditLogs"
                                :key="log.id"
                                class="text-sm"
                            >
                                <p class="font-medium text-cpa-text-primary">{{ log.event }}</p>
                                <p class="text-xs text-cpa-text-muted mt-0.5">
                                    {{ log.causer ? `${log.causer.first_name} ${log.causer.last_name}` : 'System' }}
                                    · {{ new Date(log.created_at).toLocaleString() }}
                                </p>
                            </div>
                        </div>
                        <p v-else class="text-sm text-cpa-text-muted">No recent activity.</p>
                    </div>
                </div>
            </div>
        </div>
    </PlatformLayout>
</template>
