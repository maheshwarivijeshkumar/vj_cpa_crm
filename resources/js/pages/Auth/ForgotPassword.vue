<script setup lang="ts">
import { computed } from 'vue'
import { useForm, Link } from '@inertiajs/vue3'
import { ArrowLeft, Mail } from '@lucide/vue'
import AuthLayout from '@/layouts/AuthLayout.vue'

defineOptions({ layout: AuthLayout })

defineProps<{ status?: string }>()

const form = useForm({ email: '' })

function submit(): void {
    form.post('/forgot-password')
}

const emailPlaceholder = computed(() => form.errors.email || 'Enter your email address')
</script>

<template>
    <div class="auth-card">
        <div class="mb-6">
            <h1 class="auth-title">Reset password</h1>
            <p class="auth-subtitle">
                Enter your email and we'll send you a link to reset your password.
            </p>
        </div>

        <div
            v-if="status"
            class="mb-4 px-3 py-2.5 rounded-lg bg-cpa-success-bg text-cpa-success text-[13px] font-medium"
        >
            {{ status }}
        </div>

        <form @submit.prevent="submit" class="space-y-4" novalidate>
            <div class="form-group">
                <label for="email" class="form-label required">Email address</label>
                <div class="relative">
                    <input
                        id="email"
                        v-model="form.email"
                        type="email"
                        autocomplete="email"
                        :placeholder="emailPlaceholder"
                        :class="['form-input pl-9', { error: form.errors.email }]"
                        :aria-invalid="!!form.errors.email"
                        autofocus
                    />
                    <Mail :size="15" class="absolute left-3 top-1/2 -translate-y-1/2 text-cpa-text-muted" />
                </div>
            </div>

            <button
                type="submit"
                class="btn btn-primary w-full btn-lg"
                :disabled="form.processing"
            >
                <span
                    v-if="form.processing"
                    class="inline-block w-4 h-4 border-2 border-white/40 border-t-white rounded-full animate-spin"
                />
                {{ form.processing ? 'Sending…' : 'Send reset link' }}
            </button>
        </form>

        <p class="mt-5 text-center">
            <Link href="/login" class="text-[12.5px] text-cpa-medium-dark hover:underline inline-flex items-center gap-1">
                <ArrowLeft :size="13" /> Back to sign in
            </Link>
        </p>
    </div>
</template>
