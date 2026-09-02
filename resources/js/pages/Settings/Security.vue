<script setup lang="ts">
import { useForm } from '@inertiajs/vue3'
import { Shield, Lock, Key, CheckCircle, Eye, EyeOff } from '@lucide/vue'
import { ref } from 'vue'
import SettingsLayout from '@/layouts/SettingsLayout.vue'

const props = defineProps<{
    twoFactorEnabled: boolean
}>()

// ── Change password form ───────────────────────────────────────────────────
const pwForm = useForm({
    current_password:      '',
    password:              '',
    password_confirmation: '',
})

const showCurrent = ref(false)
const showNew     = ref(false)
const showConfirm = ref(false)

function changePassword() {
    pwForm.patch(route('settings.password.update'), {
        onSuccess: () => pwForm.reset(),
    })
}
</script>

<template>
    <SettingsLayout>
        <div class="space-y-5">

            <!-- Success flash -->
            <Transition name="fade">
                <div
                    v-if="$page.props.flash?.success"
                    class="flex items-center gap-2.5 bg-cpa-success-bg text-cpa-success border border-cpa-success/20 rounded-xl px-4 py-3 text-sm font-medium"
                >
                    <CheckCircle :size="16" class="flex-shrink-0" />
                    {{ $page.props.flash.success }}
                </div>
            </Transition>

            <!-- Change password -->
            <div class="bg-white border border-cpa-border rounded-xl shadow-sm p-6">
                <div class="flex items-center gap-2 mb-5">
                    <Lock :size="16" class="text-cpa-medium-dark" />
                    <h2 class="text-base font-semibold text-cpa-text-primary">Change Password</h2>
                </div>

                <div class="space-y-4 max-w-sm">
                    <!-- Current password -->
                    <div>
                        <label for="current_pw" class="block text-sm font-medium text-cpa-text-primary mb-1.5">Current Password</label>
                        <div class="relative">
                            <input
                                id="current_pw"
                                v-model="pwForm.current_password"
                                :type="showCurrent ? 'text' : 'password'"
                                autocomplete="current-password"
                                class="w-full pr-10 px-3 py-2 text-sm border rounded-lg focus:outline-none focus:ring-2 focus:ring-cpa-medium focus:border-cpa-medium transition-colors"
                                :class="pwForm.errors.current_password ? 'border-cpa-danger' : 'border-cpa-border'"
                            />
                            <button type="button" class="absolute right-2.5 top-2.5 text-cpa-text-muted hover:text-cpa-text-primary" @click="showCurrent = !showCurrent">
                                <component :is="showCurrent ? EyeOff : Eye" :size="15" />
                            </button>
                        </div>
                        <p v-if="pwForm.errors.current_password" class="text-cpa-danger text-xs mt-1">{{ pwForm.errors.current_password }}</p>
                    </div>

                    <!-- New password -->
                    <div>
                        <label for="new_pw" class="block text-sm font-medium text-cpa-text-primary mb-1.5">New Password</label>
                        <div class="relative">
                            <input
                                id="new_pw"
                                v-model="pwForm.password"
                                :type="showNew ? 'text' : 'password'"
                                autocomplete="new-password"
                                class="w-full pr-10 px-3 py-2 text-sm border rounded-lg focus:outline-none focus:ring-2 focus:ring-cpa-medium focus:border-cpa-medium transition-colors"
                                :class="pwForm.errors.password ? 'border-cpa-danger' : 'border-cpa-border'"
                            />
                            <button type="button" class="absolute right-2.5 top-2.5 text-cpa-text-muted hover:text-cpa-text-primary" @click="showNew = !showNew">
                                <component :is="showNew ? EyeOff : Eye" :size="15" />
                            </button>
                        </div>
                        <p v-if="pwForm.errors.password" class="text-cpa-danger text-xs mt-1">{{ pwForm.errors.password }}</p>
                        <p class="text-xs text-cpa-text-muted mt-1">Min 12 characters. Use letters, numbers and symbols.</p>
                    </div>

                    <!-- Confirm password -->
                    <div>
                        <label for="confirm_pw" class="block text-sm font-medium text-cpa-text-primary mb-1.5">Confirm New Password</label>
                        <div class="relative">
                            <input
                                id="confirm_pw"
                                v-model="pwForm.password_confirmation"
                                :type="showConfirm ? 'text' : 'password'"
                                autocomplete="new-password"
                                class="w-full pr-10 px-3 py-2 text-sm border border-cpa-border rounded-lg focus:outline-none focus:ring-2 focus:ring-cpa-medium focus:border-cpa-medium transition-colors"
                            />
                            <button type="button" class="absolute right-2.5 top-2.5 text-cpa-text-muted hover:text-cpa-text-primary" @click="showConfirm = !showConfirm">
                                <component :is="showConfirm ? EyeOff : Eye" :size="15" />
                            </button>
                        </div>
                    </div>

                    <div class="flex justify-end pt-1">
                        <button
                            :disabled="pwForm.processing"
                            type="button"
                            class="flex items-center gap-1.5 bg-cpa-medium-dark hover:bg-cpa-dark text-white font-medium rounded-lg px-5 py-2 text-sm transition-colors disabled:opacity-60"
                            @click="changePassword"
                        >
                            {{ pwForm.processing ? 'Updating…' : 'Update Password' }}
                        </button>
                    </div>
                </div>
            </div>

            <!-- Two-Factor Authentication -->
            <div class="bg-white border border-cpa-border rounded-xl shadow-sm p-6">
                <div class="flex items-center justify-between flex-wrap gap-3">
                    <div class="flex items-center gap-2">
                        <Key :size="16" class="text-cpa-medium-dark" />
                        <h2 class="text-base font-semibold text-cpa-text-primary">Two-Factor Authentication</h2>
                    </div>
                    <span :class="['text-xs font-semibold px-2.5 py-0.5 rounded-full', twoFactorEnabled ? 'bg-cpa-success-bg text-cpa-success' : 'bg-gray-100 text-gray-500']">
                        {{ twoFactorEnabled ? 'Enabled' : 'Disabled' }}
                    </span>
                </div>

                <p class="text-sm text-cpa-text-muted mt-2 mb-4 max-w-md">
                    Add an extra layer of security. When enabled, you'll need a code from your authenticator app each time you sign in.
                </p>

                <div class="flex items-center gap-3">
                    <a
                        v-if="!twoFactorEnabled"
                        href="/two-factor/setup"
                        class="flex items-center gap-1.5 bg-cpa-medium-dark hover:bg-cpa-dark text-white font-medium rounded-lg px-4 py-2 text-sm transition-colors"
                    >
                        <Shield :size="14" /> Enable 2FA
                    </a>
                    <a
                        v-else
                        href="/two-factor/setup"
                        class="flex items-center gap-1.5 border border-cpa-border text-cpa-text-secondary hover:border-cpa-medium hover:text-cpa-dark rounded-lg px-4 py-2 text-sm transition-colors"
                    >
                        Manage 2FA
                    </a>
                </div>
            </div>

        </div>
    </SettingsLayout>
</template>

<style scoped>
.fade-enter-active, .fade-leave-active { transition: opacity .25s ease; }
.fade-enter-from, .fade-leave-to { opacity: 0; }
</style>
