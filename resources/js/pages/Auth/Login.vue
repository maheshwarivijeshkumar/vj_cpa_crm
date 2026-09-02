<script setup lang="ts">
import { ref, computed } from 'vue'
import { useForm, Link } from '@inertiajs/vue3'
import { Eye, EyeOff, LogIn } from '@lucide/vue'
import AuthLayout from '@/layouts/AuthLayout.vue'

defineOptions({ layout: AuthLayout })

// Props from Inertia (flash errors, etc.)
defineProps<{
    canResetPassword?: boolean
    status?: string
}>()

const showPassword = ref(false)

const form = useForm({
    email:     '',
    password:  '',
    remember:  false,
})

function submit(): void {
    form.post('/login', {
        onFinish: () => form.reset('password'),
    })
}

// Field error: show in placeholder
const emailPlaceholder  = computed(() => form.errors.email    || 'Email address')
const passwordPlaceholder = computed(() => form.errors.password || 'Password')
</script>

<template>
    <div class="auth-card">
        <!-- Header -->
        <div class="mb-6">
            <h1 class="auth-title">Sign in</h1>
            <p class="auth-subtitle">
                Welcome back. Enter your credentials to access your account.
            </p>
        </div>

        <!-- Status message (e.g. password reset confirmation) -->
        <div
            v-if="status"
            class="mb-4 px-3 py-2.5 rounded-lg bg-cpa-success-bg text-cpa-success text-[13px] font-medium"
        >
            {{ status }}
        </div>

        <form @submit.prevent="submit" class="space-y-4" novalidate>
            <!-- Email -->
            <div class="form-group">
                <label for="email" class="form-label required">Email address</label>
                <input
                    id="email"
                    v-model="form.email"
                    type="email"
                    autocomplete="email"
                    :placeholder="emailPlaceholder"
                    :class="['form-input', { error: form.errors.email }]"
                    :aria-invalid="!!form.errors.email"
                    autofocus
                />
            </div>

            <!-- Password -->
            <div class="form-group">
                <div class="flex items-center justify-between mb-1">
                    <label for="password" class="form-label required">Password</label>
                    <Link
                        v-if="canResetPassword"
                        href="/forgot-password"
                        class="text-[12px] text-cpa-medium-dark hover:underline"
                    >
                        Forgot password?
                    </Link>
                </div>
                <div class="relative">
                    <input
                        id="password"
                        v-model="form.password"
                        :type="showPassword ? 'text' : 'password'"
                        autocomplete="current-password"
                        :placeholder="passwordPlaceholder"
                        :class="['form-input pr-10', { error: form.errors.password }]"
                        :aria-invalid="!!form.errors.password"
                    />
                    <button
                        type="button"
                        class="absolute right-3 top-1/2 -translate-y-1/2 text-cpa-text-muted hover:text-cpa-dark transition-colors"
                        :aria-label="showPassword ? 'Hide password' : 'Show password'"
                        @click="showPassword = !showPassword"
                    >
                        <EyeOff v-if="showPassword" :size="16" />
                        <Eye v-else :size="16" />
                    </button>
                </div>
            </div>

            <!-- Remember me -->
            <div class="flex items-center gap-2.5">
                <input
                    id="remember"
                    v-model="form.remember"
                    type="checkbox"
                    class="w-4 h-4 rounded border-cpa-border text-cpa-medium-dark accent-cpa-medium-dark cursor-pointer"
                />
                <label for="remember" class="text-[13px] text-cpa-text-muted cursor-pointer select-none">
                    Keep me signed in
                </label>
            </div>

            <!-- Submit -->
            <button
                type="submit"
                class="btn btn-primary w-full btn-lg mt-2"
                :disabled="form.processing"
            >
                <span
                    v-if="form.processing"
                    class="inline-block w-4 h-4 border-2 border-white/40 border-t-white rounded-full animate-spin"
                />
                <LogIn v-else :size="16" />
                {{ form.processing ? 'Signing in…' : 'Sign in' }}
            </button>
        </form>

        <!-- Footer -->
        <p class="mt-5 text-center text-[12.5px] text-cpa-text-muted">
            Need access?
            <a href="mailto:support@cpacrm.com" class="text-cpa-medium-dark hover:underline font-medium">
                Contact your administrator
            </a>
        </p>
    </div>
</template>
