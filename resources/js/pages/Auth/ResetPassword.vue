<script setup lang="ts">
import { ref, computed } from 'vue'
import { useForm } from '@inertiajs/vue3'
import { Eye, EyeOff } from '@lucide/vue'
import AuthLayout from '@/layouts/AuthLayout.vue'

defineOptions({ layout: AuthLayout })

const props = defineProps<{
    token: string
    email: string
}>()

const showPw        = ref(false)
const showConfirmPw = ref(false)

const form = useForm({
    token:                 props.token,
    email:                 props.email,
    password:              '',
    password_confirmation: '',
})

function submit(): void {
    form.post('/reset-password', {
        onFinish: () => form.reset('password', 'password_confirmation'),
    })
}

const emailPlaceholder    = computed(() => form.errors.email    || 'Email address')
const pwPlaceholder       = computed(() => form.errors.password || 'New password')
const pwConfirmPlaceholder = computed(() => form.errors.password_confirmation || 'Confirm new password')
</script>

<template>
    <div class="auth-card">
        <div class="mb-6">
            <h1 class="auth-title">Set new password</h1>
            <p class="auth-subtitle">Choose a strong password for your account.</p>
        </div>

        <form @submit.prevent="submit" class="space-y-4" novalidate>
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
                />
            </div>

            <div class="form-group">
                <label for="password" class="form-label required">New password</label>
                <div class="relative">
                    <input
                        id="password"
                        v-model="form.password"
                        :type="showPw ? 'text' : 'password'"
                        autocomplete="new-password"
                        :placeholder="pwPlaceholder"
                        :class="['form-input pr-10', { error: form.errors.password }]"
                        :aria-invalid="!!form.errors.password"
                        autofocus
                    />
                    <button type="button" class="absolute right-3 top-1/2 -translate-y-1/2 text-cpa-text-muted hover:text-cpa-dark transition-colors" @click="showPw = !showPw">
                        <EyeOff v-if="showPw" :size="16" />
                        <Eye v-else :size="16" />
                    </button>
                </div>
                <p class="form-hint">Minimum 8 characters</p>
            </div>

            <div class="form-group">
                <label for="password_confirmation" class="form-label required">Confirm password</label>
                <div class="relative">
                    <input
                        id="password_confirmation"
                        v-model="form.password_confirmation"
                        :type="showConfirmPw ? 'text' : 'password'"
                        autocomplete="new-password"
                        :placeholder="pwConfirmPlaceholder"
                        :class="['form-input pr-10', { error: form.errors.password_confirmation }]"
                        :aria-invalid="!!form.errors.password_confirmation"
                    />
                    <button type="button" class="absolute right-3 top-1/2 -translate-y-1/2 text-cpa-text-muted hover:text-cpa-dark transition-colors" @click="showConfirmPw = !showConfirmPw">
                        <EyeOff v-if="showConfirmPw" :size="16" />
                        <Eye v-else :size="16" />
                    </button>
                </div>
            </div>

            <button type="submit" class="btn btn-primary w-full btn-lg" :disabled="form.processing">
                <span v-if="form.processing" class="inline-block w-4 h-4 border-2 border-white/40 border-t-white rounded-full animate-spin" />
                {{ form.processing ? 'Saving…' : 'Reset password' }}
            </button>
        </form>
    </div>
</template>
