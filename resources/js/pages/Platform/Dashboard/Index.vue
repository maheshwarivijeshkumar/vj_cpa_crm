<script setup lang="ts">
import PlatformLayout from '@/layouts/PlatformLayout.vue'
import SeoHead from '@/components/ui/SeoHead.vue'
import { Link } from '@inertiajs/vue3'
import {
    Building2, Users, TrendingUp, AlertCircle,
    Clock, CheckCircle, ChevronRight, Activity,
} from '@lucide/vue'
import { useUiStore } from '@/stores/ui'

defineOptions({ layout: PlatformLayout })

const ui = useUiStore()
ui.setPageTitle('Platform Dashboard')

const props = defineProps<{
    stats?: {
        tenants:      { total: number; active: number; trial: number; suspended: number }
        users:        { total: number; platform_admins: number; firm_users: number }
        blog_posts:   { total: number; published: number }
        trial_expiring: number
    }
}>()

// Fallback demo stats when not yet provided by controller
const stats = props.stats ?? {
    tenants:   { total: 0, active: 0, trial: 0, suspended: 0 },
    users:     { total: 0, platform_admins: 0, firm_users: 0 },
    blog_posts:{ total: 0, published: 0 },
    trial_expiring: 0,
}

const statCards = [
    { label: 'Total Tenants',    value: stats.tenants.total,    icon: Building2,    color: 'teal',   href: '/platform/tenants' },
    { label: 'Active Firms',     value: stats.tenants.active,   icon: CheckCircle,  color: 'green',  href: '/platform/tenants?status=active' },
    { label: 'On Trial',         value: stats.tenants.trial,    icon: Clock,        color: 'amber',  href: '/platform/tenants?status=trial' },
    { label: 'Total Users',      value: stats.users.total,      icon: Users,        color: 'blue',   href: '/platform/users' },
]
</script>

<template>
    <SeoHead :seo="{ title: 'Platform Dashboard — VJ CPA CRM', robots: 'noindex,nofollow' }" />

    <!-- Page header -->
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl font-800 text-gray-900 tracking-tight">Platform Dashboard</h1>
            <p class="text-sm text-gray-500 mt-0.5">Overview of all tenants, users and system health.</p>
        </div>
        <Link href="/platform/tenants" class="btn btn-primary btn-sm">
            <Building2 :size="14" />
            Manage Tenants
        </Link>
    </div>

    <!-- Trial expiry alert -->
    <div v-if="stats.trial_expiring > 0" class="mb-5 flex items-start gap-3 bg-amber-50 border border-amber-200 rounded-xl px-4 py-3">
        <AlertCircle :size="18" class="text-amber-600 flex-shrink-0 mt-0.5" />
        <div>
            <p class="text-sm font-600 text-amber-800">
                {{ stats.trial_expiring }} firm{{ stats.trial_expiring !== 1 ? 's' : '' }} expiring trial within 7 days
            </p>
            <Link href="/platform/tenants?trial_expiring=1" class="text-xs text-amber-700 hover:underline">
                View expiring tenants <ChevronRight :size="12" class="inline" />
            </Link>
        </div>
    </div>

    <!-- Stat cards -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
        <Link
            v-for="card in statCards"
            :key="card.label"
            :href="card.href"
            class="bg-white border border-gray-100 rounded-xl p-5 hover:shadow-md transition-shadow group"
        >
            <div class="flex items-start justify-between mb-3">
                <div
                    class="w-10 h-10 rounded-lg flex items-center justify-center"
                    :class="{
                        'bg-teal-50 text-teal-700': card.color === 'teal',
                        'bg-green-50 text-green-700': card.color === 'green',
                        'bg-amber-50 text-amber-700': card.color === 'amber',
                        'bg-blue-50 text-blue-700': card.color === 'blue',
                    }"
                >
                    <component :is="card.icon" :size="18" />
                </div>
                <ChevronRight :size="14" class="text-gray-300 group-hover:text-gray-400 transition-colors mt-1" />
            </div>
            <p class="text-2xl font-800 text-gray-900">{{ card.value.toLocaleString() }}</p>
            <p class="text-xs font-600 text-gray-500 uppercase tracking-wide mt-1">{{ card.label }}</p>
        </Link>
    </div>

    <!-- Secondary stats row -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-4">
        <div class="bg-white border border-gray-100 rounded-xl p-5">
            <div class="flex items-center gap-2 mb-4">
                <Building2 :size="16" class="text-teal-600" />
                <h3 class="text-sm font-700 text-gray-800">Tenant Breakdown</h3>
            </div>
            <div class="space-y-2">
                <div v-for="(val, key) in { Active: stats.tenants.active, Trial: stats.tenants.trial, Suspended: stats.tenants.suspended }"
                    :key="key"
                    class="flex items-center justify-between text-sm"
                >
                    <span class="text-gray-600">{{ key }}</span>
                    <span class="font-600 text-gray-800">{{ val }}</span>
                </div>
            </div>
        </div>

        <div class="bg-white border border-gray-100 rounded-xl p-5">
            <div class="flex items-center gap-2 mb-4">
                <Users :size="16" class="text-blue-600" />
                <h3 class="text-sm font-700 text-gray-800">User Breakdown</h3>
            </div>
            <div class="space-y-2">
                <div v-for="(val, key) in { 'Platform Admins': stats.users.platform_admins, 'Firm Users': stats.users.firm_users }"
                    :key="key"
                    class="flex items-center justify-between text-sm"
                >
                    <span class="text-gray-600">{{ key }}</span>
                    <span class="font-600 text-gray-800">{{ val }}</span>
                </div>
            </div>
        </div>

        <div class="bg-white border border-gray-100 rounded-xl p-5">
            <div class="flex items-center gap-2 mb-4">
                <Activity :size="16" class="text-purple-600" />
                <h3 class="text-sm font-700 text-gray-800">Quick Actions</h3>
            </div>
            <div class="space-y-2">
                <Link href="/platform/tenants" class="flex items-center justify-between text-sm text-teal-700 hover:text-teal-900 transition-colors py-1">
                    <span>Manage Tenants</span>
                    <ChevronRight :size="14" />
                </Link>
                <Link href="/platform/users" class="flex items-center justify-between text-sm text-teal-700 hover:text-teal-900 transition-colors py-1">
                    <span>Manage Users</span>
                    <ChevronRight :size="14" />
                </Link>
                <Link href="/platform/settings" class="flex items-center justify-between text-sm text-teal-700 hover:text-teal-900 transition-colors py-1">
                    <span>Platform Settings</span>
                    <ChevronRight :size="14" />
                </Link>
            </div>
        </div>
    </div>
</template>
