<script setup lang="ts">
import { ref, computed } from 'vue'
import { useForm, Link } from '@inertiajs/vue3'
import { Eye, EyeOff, ArrowRight, CheckCircle, Building2, User } from '@lucide/vue'
import AuthLayout from '@/layouts/AuthLayout.vue'

defineOptions({ layout: AuthLayout })

// ── Form ─────────────────────────────────────────────────────────────────────
const showPw = ref(false)

const form = useForm({
    first_name: '',
    last_name:  '',
    firm_name:  '',
    email:      '',
    password:   '',
    password_confirmation: '',
})

function submit() {
    form.post('/register', {
        onFinish: () => form.reset('password', 'password_confirmation'),
    })
}

// Error-in-placeholder pattern (rule 14)
const firstNamePh = computed(() => form.errors.first_name || 'First name')
const lastNamePh  = computed(() => form.errors.last_name  || 'Last name')
const firmNamePh  = computed(() => form.errors.firm_name  || 'Your accounting firm name')
const emailPh     = computed(() => form.errors.email      || 'Work email address')
const passwordPh  = computed(() => form.errors.password   || 'At least 8 characters')
const confirmPh   = computed(() => form.errors.password_confirmation || 'Repeat your password')

// Password strength indicator
const passwordStrength = computed(() => {
    const p = form.password
    if (!p) return 0
    let score = 0
    if (p.length >= 8)  score++
    if (p.length >= 12) score++
    if (/[A-Z]/.test(p)) score++
    if (/[0-9]/.test(p)) score++
    if (/[^A-Za-z0-9]/.test(p)) score++
    return score  // 0–5
})

const strengthLabel = computed(() => {
    const s = passwordStrength.value
    if (!form.password) return ''
    if (s <= 1) return 'Weak'
    if (s <= 2) return 'Fair'
    if (s <= 3) return 'Good'
    return 'Strong'
})

const strengthColor = computed(() => {
    const s = passwordStrength.value
    if (!form.password) return 'bg-gray-200'
    if (s <= 1) return 'bg-red-400'
    if (s <= 2) return 'bg-amber-400'
    if (s <= 3) return 'bg-blue-400'
    return 'bg-cpa-success'
})

const perks = [
    '14-day free trial, no card needed',
    'Full access from day one',
    'Free onboarding call included',
    'Cancel anytime, no lock-in',
]
</script>

<template>
    <!-- AuthLayout provides the split-panel shell -->
    <!-- Override the brand panel slot with trial-specific content -->

    <div class="auth-card register-card">

        <!-- Header -->
        <div class="mb-5">
            <div class="flex items-center gap-2 mb-3">
                <div class="w-8 h-8 bg-cpa-very-light rounded-lg flex items-center justify-center">
                    <Building2 :size="16" class="text-cpa-medium-dark" />
                </div>
                <span class="text-xs font-semibold text-cpa-medium-dark tracking-wide uppercase">
                    Free 14-day trial
                </span>
            </div>
            <h1 class="auth-title">Create your account</h1>
            <p class="auth-subtitle">Set up your firm in under 2 minutes.</p>
        </div>

        <!-- Perks row -->
        <div class="register-perks">
            <div v-for="perk in perks" :key="perk" class="register-perk">
                <CheckCircle :size="13" class="text-cpa-success flex-shrink-0" />
                <span>{{ perk }}</span>
            </div>
        </div>

        <!-- Form -->
        <form @submit.prevent="submit" class="register-form" novalidate>

            <!-- Name row -->
            <div class="register-row">
                <div class="form-group">
                    <label for="first_name" class="form-label required">First name</label>
                    <input
                        id="first_name"
                        v-model="form.first_name"
                        type="text"
                        autocomplete="given-name"
                        :placeholder="firstNamePh"
                        :class="['form-input', { error: form.errors.first_name }]"
                        :aria-invalid="!!form.errors.first_name"
                        autofocus
                    />
                </div>
                <div class="form-group">
                    <label for="last_name" class="form-label required">Last name</label>
                    <input
                        id="last_name"
                        v-model="form.last_name"
                        type="text"
                        autocomplete="family-name"
                        :placeholder="lastNamePh"
                        :class="['form-input', { error: form.errors.last_name }]"
                        :aria-invalid="!!form.errors.last_name"
                    />
                </div>
            </div>

            <!-- Firm name -->
            <div class="form-group">
                <label for="firm_name" class="form-label required">Firm name</label>
                <div class="relative">
                    <Building2 :size="15" class="absolute left-3 top-1/2 -translate-y-1/2 text-cpa-text-muted" />
                    <input
                        id="firm_name"
                        v-model="form.firm_name"
                        type="text"
                        autocomplete="organization"
                        :placeholder="firmNamePh"
                        :class="['form-input pl-9', { error: form.errors.firm_name }]"
                        :aria-invalid="!!form.errors.firm_name"
                    />
                </div>
            </div>

            <!-- Email -->
            <div class="form-group">
                <label for="email" class="form-label required">Work email</label>
                <input
                    id="email"
                    v-model="form.email"
                    type="email"
                    autocomplete="email"
                    :placeholder="emailPh"
                    :class="['form-input', { error: form.errors.email }]"
                    :aria-invalid="!!form.errors.email"
                />
            </div>

            <!-- Password -->
            <div class="form-group">
                <label for="password" class="form-label required">Password</label>
                <div class="relative">
                    <input
                        id="password"
                        v-model="form.password"
                        :type="showPw ? 'text' : 'password'"
                        autocomplete="new-password"
                        :placeholder="passwordPh"
                        :class="['form-input pr-10', { error: form.errors.password }]"
                        :aria-invalid="!!form.errors.password"
                    />
                    <button
                        type="button"
                        class="absolute right-3 top-1/2 -translate-y-1/2 text-cpa-text-muted hover:text-cpa-dark transition-colors"
                        :aria-label="showPw ? 'Hide password' : 'Show password'"
                        @click="showPw = !showPw"
                    >
                        <EyeOff v-if="showPw" :size="16" />
                        <Eye    v-else         :size="16" />
                    </button>
                </div>
                <!-- Strength bar -->
                <div v-if="form.password" class="register-strength">
                    <div class="register-strength-bar">
                        <div
                            class="register-strength-fill transition-all duration-300"
                            :class="strengthColor"
                            :style="{ width: `${(passwordStrength / 5) * 100}%` }"
                        />
                    </div>
                    <span class="register-strength-label" :class="{
                        'text-red-400':       passwordStrength <= 1,
                        'text-amber-500':     passwordStrength === 2,
                        'text-blue-500':      passwordStrength === 3,
                        'text-cpa-success':   passwordStrength >= 4,
                    }">{{ strengthLabel }}</span>
                </div>
            </div>

            <!-- Confirm password -->
            <div class="form-group">
                <label for="password_confirmation" class="form-label required">Confirm password</label>
                <input
                    id="password_confirmation"
                    v-model="form.password_confirmation"
                    type="password"
                    autocomplete="new-password"
                    :placeholder="confirmPh"
                    :class="['form-input', { error: form.errors.password_confirmation }]"
                    :aria-invalid="!!form.errors.password_confirmation"
                />
            </div>

            <!-- Submit -->
            <button
                type="submit"
                class="btn btn-primary w-full btn-lg mt-1"
                :disabled="form.processing"
            >
                <span
                    v-if="form.processing"
                    class="inline-block w-4 h-4 border-2 border-white/40 border-t-white rounded-full animate-spin"
                    aria-hidden="true"
                />
                <ArrowRight v-else :size="16" />
                {{ form.processing ? 'Creating account…' : 'Create free account' }}
            </button>

        </form>

        <!-- Footer -->
        <p class="mt-5 text-center text-[12.5px] text-cpa-text-muted">
            Already have an account?
            <Link href="/login" class="text-cpa-medium-dark hover:underline font-medium">
                Sign in
            </Link>
        </p>

        <p class="mt-3 text-center text-[11.5px] text-cpa-text-muted leading-relaxed">
            By creating an account you agree to our
            <Link href="/terms"   class="text-cpa-medium-dark hover:underline">Terms</Link>
            and
            <Link href="/privacy" class="text-cpa-medium-dark hover:underline">Privacy Policy</Link>.
        </p>
    </div>
</template>

<style scoped>
/* Slightly wider card for registration */
.register-card { max-width: 480px !important; }

.register-perks {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 6px;
    background: var(--color-cpa-very-light, #E6F5F4);
    border-radius: 10px;
    padding: 12px 14px;
    margin-bottom: 1.25rem;
}
.register-perk {
    display: flex;
    align-items: flex-start;
    gap: 6px;
    font-size: 12px;
    color: #374151;
    line-height: 1.4;
}

.register-form { display: flex; flex-direction: column; gap: .875rem; }

.register-row {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: .875rem;
}
@media (max-width: 420px) { .register-row { grid-template-columns: 1fr; } }

.register-strength {
    display: flex;
    align-items: center;
    gap: 8px;
    margin-top: 5px;
}
.register-strength-bar {
    flex: 1;
    height: 4px;
    background: #E5E7EB;
    border-radius: 9999px;
    overflow: hidden;
}
.register-strength-fill {
    height: 100%;
    border-radius: 9999px;
}
.register-strength-label {
    font-size: 11.5px;
    font-weight: 600;
    min-width: 44px;
    text-align: right;
}
</style>
