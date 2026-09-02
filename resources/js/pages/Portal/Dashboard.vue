<script setup lang="ts">
import { Link } from '@inertiajs/vue3'
import { CreditCard, Gift, TrendingUp, ChevronRight, AlertCircle } from '@lucide/vue'
import PortalLayout from '@/layouts/PortalLayout.vue'
import StatusBadge from '@/components/ui/StatusBadge.vue'

interface Subscription {
    plan: string
    status: string
    ends_at: string
    billing_cycle: string
}

interface ReferralBalance {
    points: number
    credit: string
}

interface RecentActivity {
    id: number
    event: string
    created_at: string
}

const props = defineProps<{
    tenant: { id: number; name: string; plan: string }
    subscription: Subscription | null
    referralBalance: ReferralBalance
    recentActivity: RecentActivity[]
}>()

function daysUntil(dateStr: string): number {
    return Math.max(0, Math.ceil((new Date(dateStr).getTime() - Date.now()) / 86400000))
}

const subDaysLeft = props.subscription
    ? daysUntil(props.subscription.ends_at)
    : null
</script>

<template>
    <PortalLayout>
        <template #header>
            <span class="text-sm font-medium text-cpa-text-primary">Dashboard</span>
        </template>

        <div class="space-y-6">

            <!-- Welcome -->
            <div>
                <h1 class="text-2xl font-semibold text-cpa-text-primary">
                    Welcome back, {{ tenant.name }} 👋
                </h1>
                <p class="text-sm text-cpa-text-muted mt-1">Here's an overview of your account.</p>
            </div>

            <!-- Subscription expiry warning -->
            <div
                v-if="subscription && subDaysLeft !== null && subDaysLeft <= 7"
                class="flex items-start gap-3 bg-cpa-warning-bg border border-cpa-warning/30 rounded-xl px-4 py-3"
            >
                <AlertCircle :size="18" class="text-cpa-warning mt-0.5 flex-shrink-0" />
                <div class="text-sm">
                    <span class="font-semibold text-cpa-warning">
                        {{ subDaysLeft === 0 ? 'Subscription expired today' : `Subscription expires in ${subDaysLeft} day${subDaysLeft === 1 ? '' : 's'}` }}
                    </span>
                    <span class="text-cpa-text-muted ml-1">—</span>
                    <Link href="/portal/subscription" class="ml-1 text-cpa-medium-dark font-medium hover:underline">
                        Renew now →
                    </Link>
                </div>
            </div>

            <!-- Stats -->
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">

                <!-- Subscription card -->
                <div class="bg-white border border-cpa-border rounded-xl p-5 shadow-sm">
                    <div class="flex items-center justify-between mb-3">
                        <p class="text-xs font-semibold text-cpa-text-secondary uppercase tracking-wide">Subscription</p>
                        <div class="w-9 h-9 bg-cpa-very-light rounded-xl flex items-center justify-center">
                            <CreditCard :size="17" class="text-cpa-medium-dark" />
                        </div>
                    </div>
                    <template v-if="subscription">
                        <p class="text-2xl font-bold text-cpa-text-primary capitalize">{{ subscription.plan }}</p>
                        <div class="flex items-center gap-2 mt-1.5">
                            <StatusBadge :status="subscription.status" />
                            <span class="text-xs text-cpa-text-muted">
                                {{ subDaysLeft !== null ? `${subDaysLeft}d left` : '' }}
                            </span>
                        </div>
                    </template>
                    <template v-else>
                        <p class="text-sm text-cpa-text-muted mt-1">No active subscription</p>
                        <Link href="/portal/subscription" class="text-xs text-cpa-medium-dark font-medium mt-1.5 block hover:underline">
                            View plans →
                        </Link>
                    </template>
                </div>

                <!-- Referral points -->
                <div class="bg-white border border-cpa-border rounded-xl p-5 shadow-sm">
                    <div class="flex items-center justify-between mb-3">
                        <p class="text-xs font-semibold text-cpa-text-secondary uppercase tracking-wide">Referral Points</p>
                        <div class="w-9 h-9 bg-cpa-very-light rounded-xl flex items-center justify-center">
                            <Gift :size="17" class="text-cpa-medium-dark" />
                        </div>
                    </div>
                    <p class="text-2xl font-bold text-cpa-text-primary">{{ referralBalance.points.toLocaleString() }}</p>
                    <p class="text-xs text-cpa-text-muted mt-1">
                        + ${{ referralBalance.credit }} credit
                    </p>
                </div>

                <!-- Plan tier -->
                <div class="bg-white border border-cpa-border rounded-xl p-5 shadow-sm">
                    <div class="flex items-center justify-between mb-3">
                        <p class="text-xs font-semibold text-cpa-text-secondary uppercase tracking-wide">Current Plan</p>
                        <div class="w-9 h-9 bg-cpa-very-light rounded-xl flex items-center justify-center">
                            <TrendingUp :size="17" class="text-cpa-medium-dark" />
                        </div>
                    </div>
                    <p class="text-2xl font-bold text-cpa-text-primary capitalize">{{ tenant.plan }}</p>
                    <Link href="/portal/subscription" class="text-xs text-cpa-medium-dark font-medium mt-1.5 block hover:underline">
                        Manage →
                    </Link>
                </div>
            </div>

            <!-- Quick actions -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <Link href="/portal/subscription" class="group flex items-center justify-between bg-white border border-cpa-border rounded-xl p-5 shadow-sm hover:border-cpa-medium transition-colors">
                    <div class="flex items-center gap-3">
                        <div class="w-9 h-9 bg-cpa-very-light rounded-xl flex items-center justify-center group-hover:bg-cpa-light transition-colors">
                            <CreditCard :size="17" class="text-cpa-medium-dark" />
                        </div>
                        <div>
                            <p class="text-sm font-semibold text-cpa-text-primary">Manage Subscription</p>
                            <p class="text-xs text-cpa-text-muted">View, renew, or cancel your plan</p>
                        </div>
                    </div>
                    <ChevronRight :size="16" class="text-cpa-text-muted group-hover:text-cpa-medium-dark transition-colors" />
                </Link>

                <Link href="/portal/referrals" class="group flex items-center justify-between bg-white border border-cpa-border rounded-xl p-5 shadow-sm hover:border-cpa-medium transition-colors">
                    <div class="flex items-center gap-3">
                        <div class="w-9 h-9 bg-cpa-very-light rounded-xl flex items-center justify-center group-hover:bg-cpa-light transition-colors">
                            <Gift :size="17" class="text-cpa-medium-dark" />
                        </div>
                        <div>
                            <p class="text-sm font-semibold text-cpa-text-primary">Refer & Earn</p>
                            <p class="text-xs text-cpa-text-muted">Share your referral link and earn rewards</p>
                        </div>
                    </div>
                    <ChevronRight :size="16" class="text-cpa-text-muted group-hover:text-cpa-medium-dark transition-colors" />
                </Link>
            </div>

            <!-- Recent activity -->
            <div v-if="recentActivity.length" class="bg-white border border-cpa-border rounded-xl shadow-sm p-5">
                <h2 class="text-base font-semibold text-cpa-text-primary mb-4">Recent Activity</h2>
                <div class="space-y-3">
                    <div v-for="item in recentActivity" :key="item.id" class="flex items-start gap-3 text-sm">
                        <div class="w-1.5 h-1.5 rounded-full bg-cpa-medium mt-2 flex-shrink-0" />
                        <div>
                            <p class="text-cpa-text-primary font-medium capitalize">{{ item.event.replace(/[._]/g, ' ') }}</p>
                            <p class="text-xs text-cpa-text-muted mt-0.5">{{ new Date(item.created_at).toLocaleString() }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </PortalLayout>
</template>
