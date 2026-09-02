<script setup lang="ts">
import { ref, reactive } from 'vue'
import { Bell, CheckCircle, Save } from '@lucide/vue'
import SettingsLayout from '@/layouts/SettingsLayout.vue'

interface PrefCategory {
    key: string
    label: string
    description: string
    email: boolean
    in_app: boolean
    sms: boolean
}

const props = defineProps<{
    preferences?: Record<string, { email: boolean; in_app: boolean; sms: boolean }>
}>()

const defaults: PrefCategory[] = [
    { key: 'filing_deadlines',    label: 'Filing Deadlines',    description: 'Reminders for upcoming and overdue filing deadlines', email: true,  in_app: true,  sms: false },
    { key: 'invoice_sent',        label: 'Invoice Activity',    description: 'When invoices are sent, paid, or become overdue',     email: true,  in_app: true,  sms: false },
    { key: 'task_assigned',       label: 'Tasks',               description: 'When tasks are assigned to you or updated',           email: false, in_app: true,  sms: false },
    { key: 'subscription_events', label: 'Subscription',        description: 'Billing confirmations and subscription changes',      email: true,  in_app: true,  sms: false },
    { key: 'referral_updates',    label: 'Referrals',           description: 'When your referral link is used or rewards earned',   email: true,  in_app: true,  sms: false },
    { key: 'payment_received',    label: 'Payments',            description: 'When payments are received or failed',                email: true,  in_app: true,  sms: false },
]

// Merge saved preferences over defaults
const categories = reactive<PrefCategory[]>(
    defaults.map(d => ({
        ...d,
        ...(props.preferences?.[d.key] ?? {}),
    }))
)

const saving  = ref(false)
const saved   = ref(false)

async function save() {
    saving.value = true
    const payload: Record<string, { email: boolean; in_app: boolean; sms: boolean }> = {}
    for (const c of categories) {
        payload[c.key] = { email: c.email, in_app: c.in_app, sms: c.sms }
    }

    try {
        await fetch('/api/v1/settings/notifications', {
            method: 'PATCH',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': (document.querySelector('meta[name="csrf-token"]') as HTMLMetaElement)?.content ?? '',
            },
            body: JSON.stringify({ preferences: payload }),
        })
        saved.value = true
        setTimeout(() => { saved.value = false }, 3000)
    } finally {
        saving.value = false
    }
}
</script>

<template>
    <SettingsLayout>
        <div class="space-y-5">

            <!-- Success flash -->
            <Transition name="fade">
                <div
                    v-if="saved"
                    class="flex items-center gap-2.5 bg-cpa-success-bg text-cpa-success border border-cpa-success/20 rounded-xl px-4 py-3 text-sm font-medium"
                >
                    <CheckCircle :size="16" class="flex-shrink-0" />
                    Notification preferences saved.
                </div>
            </Transition>

            <div class="bg-white border border-cpa-border rounded-xl shadow-sm p-6">
                <div class="flex items-center gap-2 mb-5">
                    <Bell :size="16" class="text-cpa-medium-dark" />
                    <h2 class="text-base font-semibold text-cpa-text-primary">Notification Preferences</h2>
                </div>

                <!-- Channel headers -->
                <div class="grid grid-cols-[1fr_auto_auto_auto] gap-x-6 mb-2 text-xs font-semibold text-cpa-text-secondary uppercase tracking-wide">
                    <span>Category</span>
                    <span class="text-center w-12">Email</span>
                    <span class="text-center w-12">In-App</span>
                    <span class="text-center w-12">SMS</span>
                </div>

                <!-- Preference rows -->
                <div class="divide-y divide-cpa-border">
                    <div
                        v-for="cat in categories"
                        :key="cat.key"
                        class="grid grid-cols-[1fr_auto_auto_auto] gap-x-6 py-3.5 items-center"
                    >
                        <div>
                            <p class="text-sm font-medium text-cpa-text-primary">{{ cat.label }}</p>
                            <p class="text-xs text-cpa-text-muted mt-0.5">{{ cat.description }}</p>
                        </div>
                        <!-- Email -->
                        <div class="flex justify-center w-12">
                            <input
                                v-model="cat.email"
                                type="checkbox"
                                :id="`${cat.key}_email`"
                                :aria-label="`Email for ${cat.label}`"
                                class="w-4 h-4 rounded border-cpa-border text-cpa-medium-dark focus:ring-cpa-medium cursor-pointer"
                            />
                        </div>
                        <!-- In-App -->
                        <div class="flex justify-center w-12">
                            <input
                                v-model="cat.in_app"
                                type="checkbox"
                                :id="`${cat.key}_inapp`"
                                :aria-label="`In-app notification for ${cat.label}`"
                                class="w-4 h-4 rounded border-cpa-border text-cpa-medium-dark focus:ring-cpa-medium cursor-pointer"
                            />
                        </div>
                        <!-- SMS -->
                        <div class="flex justify-center w-12">
                            <input
                                v-model="cat.sms"
                                type="checkbox"
                                :id="`${cat.key}_sms`"
                                :aria-label="`SMS for ${cat.label}`"
                                class="w-4 h-4 rounded border-cpa-border text-cpa-medium-dark focus:ring-cpa-medium cursor-pointer"
                            />
                        </div>
                    </div>
                </div>

                <!-- Save -->
                <div class="flex justify-end pt-4 border-t border-cpa-border mt-2">
                    <button
                        :disabled="saving"
                        type="button"
                        class="flex items-center gap-1.5 bg-cpa-medium-dark hover:bg-cpa-dark text-white font-medium rounded-lg px-5 py-2 text-sm transition-colors disabled:opacity-60"
                        @click="save"
                    >
                        <Save :size="14" />
                        {{ saving ? 'Saving…' : 'Save Preferences' }}
                    </button>
                </div>
            </div>
        </div>
    </SettingsLayout>
</template>

<style scoped>
.fade-enter-active, .fade-leave-active { transition: opacity .25s ease; }
.fade-enter-from, .fade-leave-to { opacity: 0; }
</style>
