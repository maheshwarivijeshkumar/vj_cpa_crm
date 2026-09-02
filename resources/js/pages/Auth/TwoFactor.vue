<script setup lang="ts">
/**
 * Auth/TwoFactor.vue
 *
 * Shown during the login flow when 2FA is enabled.
 * The user enters their 6-digit TOTP code or a recovery code.
 */
import { ref, computed } from 'vue'
import { useForm, Link } from '@inertiajs/vue3'
import { Shield, RotateCcw } from '@lucide/vue'
import AuthLayout from '@/layouts/AuthLayout.vue'

const useRecovery = ref(false)

const form = useForm({
    code:          '',
    recovery_code: '',
})

const heading   = computed(() => useRecovery.value ? 'Use Recovery Code' : 'Two-Factor Authentication')
const subtext   = computed(() =>
    useRecovery.value
        ? 'Enter one of your saved recovery codes to access your account.'
        : 'Enter the 6-digit code from your authenticator app.'
)

function submit() {
    const payload = useRecovery.value
        ? { recovery_code: form.recovery_code }
        : { code: form.code }

    form.transform(() => payload).post('/two-factor/challenge', {
        onError: () => {
            form.code          = ''
            form.recovery_code = ''
        },
    })
}
</script>

<template>
    <AuthLayout :title="heading">
        <div class="space-y-6">

            <!-- Icon -->
            <div class="flex justify-center">
                <div class="w-14 h-14 rounded-2xl bg-cpa-very-light flex items-center justify-center">
                    <Shield :size="28" class="text-cpa-medium-dark" />
                </div>
            </div>

            <!-- Heading -->
            <div class="text-center">
                <h1 class="text-xl font-semibold text-cpa-text-primary">{{ heading }}</h1>
                <p class="text-sm text-cpa-text-muted mt-1.5">{{ subtext }}</p>
            </div>

            <!-- Code field -->
            <div>
                <label :for="useRecovery ? 'recovery_code' : 'code'" class="block text-sm font-medium text-cpa-text-primary mb-1.5 sr-only">
                    {{ useRecovery ? 'Recovery Code' : 'Authentication Code' }}
                </label>

                <input
                    v-if="!useRecovery"
                    id="code"
                    v-model="form.code"
                    type="text"
                    inputmode="numeric"
                    pattern="\d{6}"
                    maxlength="6"
                    placeholder="000000"
                    autofocus
                    autocomplete="one-time-code"
                    class="w-full px-4 py-3 text-center text-2xl font-mono tracking-[0.6em] border rounded-xl focus:outline-none focus:ring-2 focus:ring-cpa-medium transition-colors"
                    :class="form.errors.code ? 'border-cpa-danger placeholder:text-cpa-danger' : 'border-cpa-border placeholder:text-cpa-text-muted'"
                    @keyup.enter="submit"
                />

                <input
                    v-else
                    id="recovery_code"
                    v-model="form.recovery_code"
                    type="text"
                    placeholder="xxxx-xxxx-xxxx"
                    autofocus
                    autocomplete="off"
                    class="w-full px-4 py-3 text-center text-base font-mono border rounded-xl focus:outline-none focus:ring-2 focus:ring-cpa-medium transition-colors"
                    :class="form.errors.recovery_code ? 'border-cpa-danger placeholder:text-cpa-danger' : 'border-cpa-border placeholder:text-cpa-text-muted'"
                    @keyup.enter="submit"
                />

                <p v-if="form.errors.code || form.errors.recovery_code" class="text-cpa-danger text-sm mt-2 text-center">
                    {{ form.errors.code || form.errors.recovery_code }}
                </p>
            </div>

            <!-- Submit -->
            <button
                :disabled="form.processing"
                type="button"
                class="w-full bg-cpa-medium-dark hover:bg-cpa-dark text-white font-semibold rounded-xl py-3 text-sm transition-colors disabled:opacity-60"
                @click="submit"
            >
                {{ form.processing ? 'Verifying…' : 'Verify' }}
            </button>

            <!-- Toggle mode -->
            <div class="text-center">
                <button
                    type="button"
                    class="inline-flex items-center gap-1.5 text-sm text-cpa-medium-dark hover:text-cpa-dark transition-colors"
                    @click="useRecovery = !useRecovery; form.code = ''; form.recovery_code = ''"
                >
                    <RotateCcw :size="13" />
                    {{ useRecovery ? 'Use authenticator code instead' : 'Use a recovery code instead' }}
                </button>
            </div>

            <!-- Back to login -->
            <div class="text-center text-sm text-cpa-text-muted">
                <Link href="/logout" method="post" as="button" class="text-cpa-medium-dark hover:text-cpa-dark transition-colors">
                    ← Back to Login
                </Link>
            </div>
        </div>
    </AuthLayout>
</template>
