<script setup lang="ts">
import { ref } from 'vue'
import { useForm } from '@inertiajs/vue3'
import { ShieldCheck, Lock, Key, CheckCircle } from '@lucide/vue'
import AppLayout from '@/layouts/AppLayout.vue'

const props = defineProps<{
    twoFactorEnabled: boolean
    recoveryCodes?: string[]
}>()

// ── Change password form ───────────────────────────────────────────────────
const pwForm = useForm({
    current_password:      '',
    password:              '',
    password_confirmation: '',
})

const pwSuccess = ref(false)

function changePassword() {
    pwForm.patch('/settings/password', {
        onSuccess: () => {
            pwSuccess.value = true
            pwForm.reset()
            setTimeout(() => { pwSuccess.value = false }, 4000)
        },
    })
}

// ── 2FA state ──────────────────────────────────────────────────────────────
const show2fa = ref(false)
</script>

<template>
    <AppLayout title="Security Settings">
        <div class="max-w-2xl mx-auto space-y-6">

            <!-- Page header -->
            <div class="flex items-center gap-3 mb-2">
                <div class="w-9 h-9 rounded-xl bg-cpa-very-light flex items-center justify-center">
                    <ShieldCheck :size="18" class="text-cpa-medium-dark" />
                </div>
                <div>
                    <h1 class="text-xl font-semibold text-cpa-text-primary">Security</h1>
                    <p class="text-xs text-cpa-text-muted">Manage password and two-factor authentication</p>
                </div>
            </div>

            <!-- Change password -->
            <div class="bg-white border border-cpa-border rounded-xl shadow-sm p-6">
                <div class="flex items-center gap-2 mb-5">
                    <Lock :size="16" class="text-cpa-medium-dark" />
                    <h2 class="text-base font-semibold text-cpa-text-primary">Change Password</h2>
                </div>

                <Transition name="fade">
                    <div v-if="pwSuccess" class="flex items-center gap-2 bg-cpa-success-bg text-cpa-success text-sm font-medium rounded-lg px-4 py-2.5 mb-4">
                        <CheckCircle :size="15" /> Password updated successfully.
                    </div>
                </Transition>

                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-cpa-text-primary mb-1.5">Current Password</label>
                        <input
                            v-model="pwForm.current_password"
                            type="password"
                            autocomplete="current-password"
                            class="w-full px-3 py-2 text-sm border border-cpa-border rounded-lg focus:outline-none focus:ring-2 focus:ring-cpa-medium"
                            :class="{ 'border-cpa-danger': pwForm.errors.current_password }"
                        />
                        <p v-if="pwForm.errors.current_password" class="text-cpa-danger text-xs mt-1">{{ pwForm.errors.current_password }}</p>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-cpa-text-primary mb-1.5">New Password</label>
                        <input
                            v-model="pwForm.password"
                            type="password"
                            autocomplete="new-password"
                            class="w-full px-3 py-2 text-sm border border-cpa-border rounded-lg focus:outline-none focus:ring-2 focus:ring-cpa-medium"
                            :class="{ 'border-cpa-danger': pwForm.errors.password }"
                        />
                        <p v-if="pwForm.errors.password" class="text-cpa-danger text-xs mt-1">{{ pwForm.errors.password }}</p>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-cpa-text-primary mb-1.5">Confirm New Password</label>
                        <input
                            v-model="pwForm.password_confirmation"
                            type="password"
                            autocomplete="new-password"
                            class="w-full px-3 py-2 text-sm border border-cpa-border rounded-lg focus:outline-none focus:ring-2 focus:ring-cpa-medium"
                        />
                    </div>

                    <div class="flex justify-end pt-1">
                        <button
                            :disabled="pwForm.processing"
                            class="flex items-center gap-1.5 bg-cpa-medium-dark hover:bg-cpa-dark text-white font-medium rounded-lg px-5 py-2 text-sm transition-colors disabled:opacity-60"
                            @click="changePassword"
                        >
                            {{ pwForm.processing ? 'Updating…' : 'Update Password' }}
                        </button>
                    </div>
                </div>
            </div>

            <!-- 2FA status -->
            <div class="bg-white border border-cpa-border rounded-xl shadow-sm p-6">
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-2">
                        <Key :size="16" class="text-cpa-medium-dark" />
                        <h2 class="text-base font-semibold text-cpa-text-primary">Two-Factor Authentication</h2>
                    </div>
                    <span :class="['text-xs font-semibold px-2 py-0.5 rounded-full', twoFactorEnabled ? 'bg-cpa-success-bg text-cpa-success' : 'bg-gray-100 text-gray-500']">
                        {{ twoFactorEnabled ? 'Enabled' : 'Disabled' }}
                    </span>
                </div>
                <p class="text-sm text-cpa-text-muted mt-2 mb-4">
                    Add an extra layer of security with a time-based one-time password (TOTP) app like Google Authenticator or Authy.
                </p>
                <button
                    class="text-sm font-medium text-cpa-medium-dark hover:text-cpa-dark transition-colors"
                    @click="show2fa = !show2fa"
                >
                    {{ twoFactorEnabled ? 'Manage 2FA →' : 'Enable 2FA →' }}
                </button>
            </div>

        </div>
    </AppLayout>
</template>

<style scoped>
.fade-enter-active, .fade-leave-active { transition: opacity .2s ease; }
.fade-enter-from, .fade-leave-to { opacity: 0; }
</style>
