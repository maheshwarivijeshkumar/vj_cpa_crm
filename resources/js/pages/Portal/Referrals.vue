<script setup lang="ts">
import { ref, computed } from 'vue'
import { Gift, Copy, CheckCircle, Share2, Star, DollarSign } from '@lucide/vue'
import PortalLayout from '@/layouts/PortalLayout.vue'
import StatusBadge from '@/components/ui/StatusBadge.vue'

interface ReferralLink { code: string; full_url: string; click_count: number; signup_count: number }
interface Referral {
    id: string
    referee_email?: string
    status: string
    clicked_at: string
    signed_up_at?: string
    rewarded_at?: string
}
interface ReferralBalance { points: number; credit: string }

const props = defineProps<{
    referralLink: ReferralLink | null
    referrals: Referral[]
    balance: ReferralBalance
}>()

const copied = ref(false)

function copyLink() {
    if (!props.referralLink) return
    navigator.clipboard.writeText(props.referralLink.full_url).then(() => {
        copied.value = true
        setTimeout(() => { copied.value = false }, 2500)
    })
}

const stats = computed(() => ({
    clicks:   props.referralLink?.click_count ?? 0,
    signups:  props.referralLink?.signup_count ?? 0,
    rewarded: props.referrals.filter(r => r.status === 'rewarded').length,
}))
</script>

<template>
    <PortalLayout>
        <template #header>
            <span class="text-sm font-medium text-cpa-text-primary">Referrals</span>
        </template>

        <div class="space-y-6">

            <!-- Header -->
            <div class="flex items-center gap-3">
                <div class="w-9 h-9 rounded-xl bg-cpa-very-light flex items-center justify-center">
                    <Gift :size="18" class="text-cpa-medium-dark" />
                </div>
                <div>
                    <h1 class="text-xl font-semibold text-cpa-text-primary">Refer & Earn</h1>
                    <p class="text-xs text-cpa-text-muted">Share your link. Earn rewards when friends subscribe.</p>
                </div>
            </div>

            <!-- Referral link card -->
            <div class="bg-white border border-cpa-border rounded-xl shadow-sm p-6">
                <h2 class="text-base font-semibold text-cpa-text-primary mb-3">Your Referral Link</h2>
                <div v-if="referralLink" class="flex items-center gap-2">
                    <input
                        :value="referralLink.full_url"
                        type="text"
                        readonly
                        class="flex-1 px-3 py-2 text-sm border border-cpa-border rounded-lg bg-cpa-bg font-mono text-cpa-text-secondary focus:outline-none"
                    />
                    <button
                        :class="['flex items-center gap-1.5 px-4 py-2 text-sm font-medium rounded-lg transition-colors', copied ? 'bg-cpa-success-bg text-cpa-success border border-cpa-success/30' : 'bg-cpa-medium-dark hover:bg-cpa-dark text-white']"
                        @click="copyLink"
                    >
                        <component :is="copied ? CheckCircle : Copy" :size="14" />
                        {{ copied ? 'Copied!' : 'Copy' }}
                    </button>
                </div>
                <p v-else class="text-sm text-cpa-text-muted">Generating your referral link…</p>
            </div>

            <!-- Balance + stats -->
            <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
                <div class="bg-white border border-cpa-border rounded-xl p-4 shadow-sm text-center">
                    <div class="w-8 h-8 rounded-lg bg-cpa-warning-bg flex items-center justify-center mx-auto mb-2">
                        <Star :size="16" class="text-cpa-warning" />
                    </div>
                    <p class="text-2xl font-bold text-cpa-text-primary">{{ balance.points.toLocaleString() }}</p>
                    <p class="text-xs text-cpa-text-muted mt-0.5">Points</p>
                </div>
                <div class="bg-white border border-cpa-border rounded-xl p-4 shadow-sm text-center">
                    <div class="w-8 h-8 rounded-lg bg-cpa-success-bg flex items-center justify-center mx-auto mb-2">
                        <DollarSign :size="16" class="text-cpa-success" />
                    </div>
                    <p class="text-2xl font-bold text-cpa-text-primary">${{ balance.credit }}</p>
                    <p class="text-xs text-cpa-text-muted mt-0.5">Credit</p>
                </div>
                <div class="bg-white border border-cpa-border rounded-xl p-4 shadow-sm text-center">
                    <p class="text-2xl font-bold text-cpa-text-primary">{{ stats.clicks }}</p>
                    <p class="text-xs text-cpa-text-muted mt-0.5">Link Clicks</p>
                </div>
                <div class="bg-white border border-cpa-border rounded-xl p-4 shadow-sm text-center">
                    <p class="text-2xl font-bold text-cpa-text-primary">{{ stats.rewarded }}</p>
                    <p class="text-xs text-cpa-text-muted mt-0.5">Rewards Earned</p>
                </div>
            </div>

            <!-- Referrals table -->
            <div class="bg-white border border-cpa-border rounded-xl shadow-sm overflow-hidden">
                <div class="flex items-center justify-between px-5 py-4 border-b border-cpa-border">
                    <h2 class="text-base font-semibold text-cpa-text-primary">Referral History</h2>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead class="bg-cpa-bg border-b border-cpa-border">
                            <tr>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-cpa-text-secondary uppercase tracking-wide">Referred Email</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-cpa-text-secondary uppercase tracking-wide">Status</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-cpa-text-secondary uppercase tracking-wide">Clicked</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-cpa-text-secondary uppercase tracking-wide">Signed Up</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-cpa-border">
                            <tr v-for="ref in referrals" :key="ref.id" class="hover:bg-cpa-very-light transition-colors">
                                <td class="px-4 py-3 text-cpa-text-primary">{{ ref.referee_email ?? 'Unknown' }}</td>
                                <td class="px-4 py-3"><StatusBadge :status="ref.status" /></td>
                                <td class="px-4 py-3 text-cpa-text-muted text-xs">{{ new Date(ref.clicked_at).toLocaleDateString() }}</td>
                                <td class="px-4 py-3 text-cpa-text-muted text-xs">{{ ref.signed_up_at ? new Date(ref.signed_up_at).toLocaleDateString() : '—' }}</td>
                            </tr>
                            <tr v-if="!referrals.length">
                                <td colspan="4" class="px-4 py-12 text-center text-cpa-text-muted text-sm">
                                    No referrals yet. Share your link to get started!
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </PortalLayout>
</template>
