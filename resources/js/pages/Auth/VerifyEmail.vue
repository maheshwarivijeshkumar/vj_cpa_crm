<script setup lang="ts">
import { computed } from 'vue'
import { useForm } from '@inertiajs/vue3'
import { Mail, LogOut } from '@lucide/vue'
import AuthLayout from '@/layouts/AuthLayout.vue'

defineOptions({ layout: AuthLayout })

defineProps<{ status?: string }>()

const form = useForm({})

function submit(): void {
    form.post('/email/verification-notification')
}

function logout(): void {
    useForm({}).post('/logout')
}
</script>

<template>
    <div class="auth-card text-center">
        <div class="w-14 h-14 bg-cpa-very-light rounded-full flex items-center justify-center mx-auto mb-5">
            <Mail :size="26" class="text-cpa-medium-dark" />
        </div>

        <h1 class="auth-title mb-2">Check your email</h1>
        <p class="auth-subtitle mb-5">
            We sent a verification link to your email address.
            Click the link to verify your account before continuing.
        </p>

        <div
            v-if="status === 'verification-link-sent'"
            class="mb-5 px-3 py-2.5 rounded-lg bg-cpa-success-bg text-cpa-success text-[13px] font-medium"
        >
            A new verification link has been sent to your email address.
        </div>

        <form @submit.prevent="submit">
            <button
                type="submit"
                class="btn btn-primary w-full btn-lg"
                :disabled="form.processing"
            >
                <span v-if="form.processing" class="inline-block w-4 h-4 border-2 border-white/40 border-t-white rounded-full animate-spin" />
                {{ form.processing ? 'Sending…' : 'Resend verification email' }}
            </button>
        </form>

        <button
            class="btn btn-ghost w-full mt-2 text-cpa-text-muted"
            @click="logout"
        >
            <LogOut :size="15" />
            Sign out
        </button>
    </div>
</template>
