<script setup lang="ts">
import { ref, computed } from 'vue'
import { useForm } from '@inertiajs/vue3'
import { CreditCard, CheckCircle, AlertCircle, XCircle } from '@lucide/vue'
import PortalLayout from '@/layouts/PortalLayout.vue'
import StatusBadge from '@/components/ui/StatusBadge.vue'

interface SubscriptionData {
    id: string
    plan: string
    status: string
    billing_cycle: string
    amount_paid: string
    starts_at: string
    ends_at: string
    discount_code?: string
    discount_amount?: string
}

const props = defineProps<{
    subscription: SubscriptionData | null
    plans: Array<{
        key: string
        label: string
        price_monthly: number
        price_annual: number
        features: string[]
        is_current: boolean
    }>
}>()

const billingCycle = ref<'monthly' | 'annual'>('monthly')

const cancelForm = useForm({
    reason:      '',
    immediately: false,
})

const showCancelModal = ref(false)

function cancelSubscription() {
    cancelForm.delete(`/api/v1/subscription/${props.subscription?.id}/cancel`, {
        onSuccess: () => { showCancelModal.value = false },
    })
}

function daysLeft(dateStr: string): number {
    return Math.max(0, Math.ceil((new Date(dateStr).getTime() - Date.now()) / 86400000))
}

const remainingDays = computed(() =>
    props.subscription?.ends_at ? daysLeft(props.subscription.ends_at) : null
)
</script>

<template>
    <PortalLayout>
        <template #header>
            <span class="text-sm font-medium text-cpa-text-primary">Subscription</span>
        </template>

        <div class="space-y-6">

            <div class="flex items-center gap-3">
                <div class="w-9 h-9 rounded-xl bg-cpa-very-light flex items-center justify-center">
                    <CreditCard :size="18" class="text-cpa-medium-dark" />
                </div>
                <div>
                    <h1 class="text-xl font-semibold text-cpa-text-primary">Subscription</h1>
                    <p class="text-xs text-cpa-text-muted">Manage your billing and plan</p>
                </div>
            </div>

            <!-- Current subscription status -->
            <div v-if="subscription" class="bg-white border border-cpa-border rounded-xl shadow-sm p-6">
                <div class="flex items-start justify-between flex-wrap gap-4">
                    <div>
                        <p class="text-xs font-semibold text-cpa-text-secondary uppercase tracking-wide mb-1.5">Current Plan</p>
                        <div class="flex items-center gap-3">
                            <h2 class="text-2xl font-bold text-cpa-text-primary capitalize">{{ subscription.plan }}</h2>
                            <StatusBadge :status="subscription.status" />
                        </div>
                        <dl class="grid grid-cols-2 sm:grid-cols-4 gap-4 mt-4">
                            <div>
                                <dt class="text-xs text-cpa-text-muted">Billing</dt>
                                <dd class="text-sm font-semibold text-cpa-text-primary capitalize mt-0.5">{{ subscription.billing_cycle }}</dd>
                            </div>
                            <div>
                                <dt class="text-xs text-cpa-text-muted">Start Date</dt>
                                <dd class="text-sm font-semibold text-cpa-text-primary mt-0.5">{{ new Date(subscription.starts_at).toLocaleDateString() }}</dd>
                            </div>
                            <div>
                                <dt class="text-xs text-cpa-text-muted">Renews / Expires</dt>
                                <dd class="text-sm font-semibold mt-0.5" :class="remainingDays !== null && remainingDays <= 7 ? 'text-cpa-warning' : 'text-cpa-text-primary'">
                                    {{ new Date(subscription.ends_at).toLocaleDateString() }}
                                    <span v-if="remainingDays !== null" class="text-xs font-normal text-cpa-text-muted ml-1">({{ remainingDays }}d)</span>
                                </dd>
                            </div>
                            <div>
                                <dt class="text-xs text-cpa-text-muted">Amount Paid</dt>
                                <dd class="text-sm font-semibold text-cpa-text-primary mt-0.5">${{ subscription.amount_paid }}</dd>
                            </div>
                        </dl>
                    </div>
                    <button
                        v-if="subscription.status === 'active' || subscription.status === 'trial'"
                        class="text-sm text-cpa-danger font-medium hover:text-red-700 transition-colors border border-cpa-danger/30 hover:border-cpa-danger px-3 py-1.5 rounded-lg flex-shrink-0"
                        @click="showCancelModal = true"
                    >
                        Cancel subscription
                    </button>
                </div>
            </div>

            <div v-else class="bg-cpa-warning-bg border border-cpa-warning/30 rounded-xl p-4 flex items-center gap-3">
                <AlertCircle :size="18" class="text-cpa-warning flex-shrink-0" />
                <p class="text-sm text-cpa-text-primary">You don't have an active subscription. Choose a plan below to get started.</p>
            </div>

            <!-- Plan selection -->
            <div>
                <div class="flex items-center justify-between mb-4">
                    <h2 class="text-base font-semibold text-cpa-text-primary">Available Plans</h2>
                    <!-- Toggle -->
                    <div class="flex items-center bg-cpa-very-light rounded-lg p-0.5 text-sm border border-cpa-border">
                        <button :class="['px-3 py-1 rounded-md font-medium transition-colors', billingCycle === 'monthly' ? 'bg-white shadow-sm text-cpa-text-primary' : 'text-cpa-text-muted']" @click="billingCycle = 'monthly'">Monthly</button>
                        <button :class="['px-3 py-1 rounded-md font-medium transition-colors', billingCycle === 'annual' ? 'bg-white shadow-sm text-cpa-text-primary' : 'text-cpa-text-muted']" @click="billingCycle = 'annual'">
                            Annual
                            <span class="ml-1 text-[10px] bg-cpa-success-bg text-cpa-success font-semibold px-1.5 py-0.5 rounded-full">-20%</span>
                        </button>
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                    <div
                        v-for="plan in plans"
                        :key="plan.key"
                        class="bg-white border rounded-xl p-5 shadow-sm transition-colors"
                        :class="plan.is_current ? 'border-cpa-medium ring-1 ring-cpa-medium' : 'border-cpa-border hover:border-cpa-medium/50'"
                    >
                        <div class="flex items-start justify-between mb-3">
                            <h3 class="font-semibold text-cpa-text-primary capitalize">{{ plan.label }}</h3>
                            <span v-if="plan.is_current" class="text-[10px] bg-cpa-success-bg text-cpa-success font-semibold px-1.5 py-0.5 rounded-full">Current</span>
                        </div>
                        <div class="mb-4">
                            <span class="text-3xl font-bold text-cpa-text-primary">
                                ${{ billingCycle === 'monthly' ? plan.price_monthly : plan.price_annual }}
                            </span>
                            <span class="text-xs text-cpa-text-muted ml-1">/ mo</span>
                        </div>
                        <ul class="space-y-1.5 mb-5">
                            <li v-for="feature in plan.features" :key="feature" class="flex items-center gap-1.5 text-xs text-cpa-text-secondary">
                                <CheckCircle :size="12" class="text-cpa-success flex-shrink-0" />
                                {{ feature }}
                            </li>
                        </ul>
                        <button
                            v-if="!plan.is_current"
                            class="w-full bg-cpa-medium-dark hover:bg-cpa-dark text-white font-medium rounded-lg py-2 text-sm transition-colors"
                        >
                            Select Plan
                        </button>
                        <div v-else class="w-full text-center text-xs text-cpa-text-muted py-2 font-medium">Active Plan</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Cancel modal -->
        <Transition name="fade">
            <div v-if="showCancelModal" class="fixed inset-0 bg-black/50 z-50 flex items-center justify-center p-4" @click.self="showCancelModal = false">
                <div class="bg-white rounded-xl shadow-xl max-w-md w-full p-6">
                    <div class="flex items-center gap-3 mb-4">
                        <div class="w-10 h-10 bg-cpa-danger-bg rounded-xl flex items-center justify-center">
                            <XCircle :size="20" class="text-cpa-danger" />
                        </div>
                        <h3 class="text-base font-semibold text-cpa-text-primary">Cancel Subscription</h3>
                    </div>
                    <p class="text-sm text-cpa-text-muted mb-4">Your access will continue until the end of the current billing period. You can re-subscribe anytime.</p>
                    <div class="space-y-3">
                        <div>
                            <label class="block text-sm font-medium text-cpa-text-primary mb-1.5">Reason for cancellation</label>
                            <textarea v-model="cancelForm.reason" rows="3" placeholder="Help us improve by sharing why you're cancelling…" class="w-full px-3 py-2 text-sm border border-cpa-border rounded-lg focus:outline-none focus:ring-2 focus:ring-cpa-medium resize-none placeholder:text-cpa-text-muted" />
                        </div>
                        <label class="flex items-center gap-2 text-sm text-cpa-text-primary cursor-pointer">
                            <input v-model="cancelForm.immediately" type="checkbox" class="rounded border-cpa-border text-cpa-danger" />
                            Cancel immediately (lose access now)
                        </label>
                    </div>
                    <div class="flex gap-2 mt-5">
                        <button class="flex-1 border border-cpa-border text-cpa-text-secondary hover:bg-cpa-very-light rounded-lg py-2 text-sm font-medium transition-colors" @click="showCancelModal = false">Keep Subscription</button>
                        <button
                            :disabled="cancelForm.processing"
                            class="flex-1 bg-cpa-danger hover:bg-red-700 text-white font-medium rounded-lg py-2 text-sm transition-colors disabled:opacity-60"
                            @click="cancelSubscription"
                        >
                            {{ cancelForm.processing ? 'Processing…' : 'Confirm Cancel' }}
                        </button>
                    </div>
                </div>
            </div>
        </Transition>
    </PortalLayout>
</template>

<style scoped>
.fade-enter-active, .fade-leave-active { transition: opacity .2s ease; }
.fade-enter-from, .fade-leave-to { opacity: 0; }
</style>
