<script setup lang="ts">
/**
 * TwoFactor/Setup.vue
 *
 * Full 2FA setup flow: three sequential steps rendered in one page.
 *   Step 1 — Initiate: POST /two-factor/enable → get QR code + secret
 *   Step 2 — Confirm: POST /two-factor/confirm → enter code → get recovery codes
 *   Step 3 — Recovery codes: display + download
 *
 * On completion the user is redirected to Settings/Security.
 */
import { ref, reactive } from 'vue'
import { router } from '@inertiajs/vue3'
import { Shield, Key, CheckCircle, Download, ArrowLeft, RefreshCw, Eye, EyeOff } from '@lucide/vue'
import SettingsLayout from '@/layouts/SettingsLayout.vue'

type Step = 'start' | 'scan' | 'codes'

const step       = ref<Step>('start')
const loading    = ref(false)
const qrCode     = ref('')       // SVG or data-url from backend
const secretKey  = ref('')       // base-32 secret for manual entry
const recoveryCodes = ref<string[]>([])
const showSecret    = ref(false)

const confirmForm = reactive({
    code:  '',
    error: '',
})

async function csrfFetch(url: string, body: object) {
    const token = (document.querySelector('meta[name="csrf-token"]') as HTMLMetaElement)?.content ?? ''
    const res = await fetch(url, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': token },
        body: JSON.stringify(body),
    })
    return { res, json: await res.json() }
}

async function initiate() {
    loading.value = true
    try {
        const { res, json } = await csrfFetch('/two-factor/enable', {})
        if (res.ok) {
            qrCode.value    = json.data.qr_code_url ?? ''
            secretKey.value = json.data.secret ?? ''
            step.value      = 'scan'
        }
    } finally {
        loading.value = false
    }
}

async function confirm() {
    confirmForm.error = ''
    if (!/^\d{6}$/.test(confirmForm.code)) {
        confirmForm.error = 'Please enter the 6-digit code from your authenticator app.'
        return
    }
    loading.value = true
    try {
        const { res, json } = await csrfFetch('/two-factor/confirm', { code: confirmForm.code })
        if (res.ok) {
            recoveryCodes.value = json.data.recovery_codes ?? []
            step.value          = 'codes'
        } else {
            confirmForm.error = json.message ?? 'Invalid code. Please try again.'
        }
    } finally {
        loading.value = false
    }
}

function downloadCodes() {
    const text = `${window.location.hostname} — 2FA Recovery Codes\n\n${recoveryCodes.value.join('\n')}\n\nStore these safely. Each code can only be used once.`
    const a    = document.createElement('a')
    a.href     = 'data:text/plain;charset=utf-8,' + encodeURIComponent(text)
    a.download = '2fa-recovery-codes.txt'
    a.click()
}

function done() {
    router.visit('/settings/security')
}
</script>

<template>
    <SettingsLayout>
        <div class="max-w-md space-y-5">

            <!-- Step: start -->
            <template v-if="step === 'start'">
                <div class="bg-white border border-cpa-border rounded-xl shadow-sm p-6 space-y-4">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-xl bg-cpa-very-light flex items-center justify-center">
                            <Shield :size="20" class="text-cpa-medium-dark" />
                        </div>
                        <div>
                            <h2 class="text-base font-semibold text-cpa-text-primary">Enable Two-Factor Authentication</h2>
                            <p class="text-xs text-cpa-text-muted mt-0.5">Secure your account with TOTP</p>
                        </div>
                    </div>

                    <p class="text-sm text-cpa-text-secondary leading-relaxed">
                        Two-factor authentication adds a second layer of security. After entering your password,
                        you'll also need to provide a 6-digit code from your authenticator app.
                    </p>

                    <div class="bg-cpa-very-light rounded-lg p-3 text-xs text-cpa-text-secondary space-y-1">
                        <p class="font-semibold text-cpa-text-primary mb-1">Recommended apps:</p>
                        <p>• Google Authenticator (iOS / Android)</p>
                        <p>• Authy</p>
                        <p>• Microsoft Authenticator</p>
                        <p>• 1Password / Bitwarden (built-in TOTP)</p>
                    </div>

                    <button
                        :disabled="loading"
                        class="w-full flex items-center justify-center gap-2 bg-cpa-medium-dark hover:bg-cpa-dark text-white font-medium rounded-lg py-2.5 text-sm transition-colors disabled:opacity-60"
                        @click="initiate"
                    >
                        <RefreshCw v-if="loading" :size="15" class="animate-spin" />
                        <Shield v-else :size="15" />
                        {{ loading ? 'Setting up…' : 'Get Started' }}
                    </button>
                </div>
            </template>

            <!-- Step: scan QR code -->
            <template v-if="step === 'scan'">
                <div class="bg-white border border-cpa-border rounded-xl shadow-sm p-6 space-y-5">
                    <h2 class="text-base font-semibold text-cpa-text-primary">Scan QR Code</h2>

                    <p class="text-sm text-cpa-text-secondary">Open your authenticator app and scan this QR code:</p>

                    <!-- QR Code display -->
                    <div class="flex items-center justify-center bg-white border-2 border-cpa-border rounded-xl p-4">
                        <div v-if="qrCode" v-html="qrCode" class="w-48 h-48" />
                        <div v-else class="w-48 h-48 bg-cpa-bg rounded-lg flex items-center justify-center">
                            <p class="text-xs text-cpa-text-muted">QR Code loading…</p>
                        </div>
                    </div>

                    <!-- Manual entry -->
                    <div>
                        <p class="text-xs text-cpa-text-muted mb-1.5">Can't scan? Enter this key manually:</p>
                        <div class="flex items-center gap-2">
                            <code
                                class="flex-1 bg-cpa-bg border border-cpa-border rounded-lg px-3 py-2 text-xs font-mono text-cpa-text-primary tracking-widest"
                                :class="showSecret ? 'select-all' : 'filter blur-sm'"
                            >{{ secretKey }}</code>
                            <button class="flex-shrink-0 p-2 border border-cpa-border rounded-lg hover:bg-cpa-very-light transition-colors" @click="showSecret = !showSecret">
                                <component :is="showSecret ? EyeOff : Eye" :size="14" class="text-cpa-text-muted" />
                            </button>
                        </div>
                    </div>

                    <hr class="border-cpa-border" />

                    <!-- Confirm code -->
                    <div>
                        <p class="text-sm font-medium text-cpa-text-primary mb-3">Enter the 6-digit code from your app to confirm setup:</p>
                        <input
                            v-model="confirmForm.code"
                            type="text"
                            inputmode="numeric"
                            pattern="\d{6}"
                            maxlength="6"
                            placeholder="000000"
                            class="w-full px-3 py-2 text-center text-xl font-mono tracking-[0.5em] border rounded-lg focus:outline-none focus:ring-2 focus:ring-cpa-medium transition-colors"
                            :class="confirmForm.error ? 'border-cpa-danger' : 'border-cpa-border'"
                            @keyup.enter="confirm"
                        />
                        <p v-if="confirmForm.error" class="text-cpa-danger text-xs mt-1.5 text-center">{{ confirmForm.error }}</p>
                    </div>

                    <div class="flex gap-2">
                        <button class="flex-1 border border-cpa-border text-cpa-text-secondary hover:bg-cpa-bg rounded-lg py-2 text-sm font-medium transition-colors" @click="step = 'start'">
                            <ArrowLeft :size="14" class="inline mr-1" /> Back
                        </button>
                        <button
                            :disabled="loading || confirmForm.code.length !== 6"
                            class="flex-1 bg-cpa-medium-dark hover:bg-cpa-dark text-white font-medium rounded-lg py-2 text-sm transition-colors disabled:opacity-60"
                            @click="confirm"
                        >
                            {{ loading ? 'Verifying…' : 'Confirm & Enable 2FA' }}
                        </button>
                    </div>
                </div>
            </template>

            <!-- Step: recovery codes -->
            <template v-if="step === 'codes'">
                <div class="bg-white border border-cpa-border rounded-xl shadow-sm p-6 space-y-5">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-xl bg-cpa-success-bg flex items-center justify-center">
                            <CheckCircle :size="20" class="text-cpa-success" />
                        </div>
                        <div>
                            <h2 class="text-base font-semibold text-cpa-text-primary">2FA Enabled!</h2>
                            <p class="text-xs text-cpa-text-muted mt-0.5">Save your recovery codes now</p>
                        </div>
                    </div>

                    <div class="bg-cpa-warning-bg border border-cpa-warning/30 rounded-lg px-4 py-3">
                        <p class="text-sm font-semibold text-cpa-warning">⚠ Store these codes somewhere safe</p>
                        <p class="text-xs text-cpa-text-secondary mt-1">
                            If you lose access to your authenticator app, use one of these codes to sign in.
                            Each code works once. You cannot retrieve them after leaving this page.
                        </p>
                    </div>

                    <div class="grid grid-cols-2 gap-2">
                        <code
                            v-for="code in recoveryCodes"
                            :key="code"
                            class="bg-cpa-bg border border-cpa-border rounded-lg px-3 py-2 text-xs font-mono text-center text-cpa-text-primary tracking-widest select-all"
                        >{{ code }}</code>
                    </div>

                    <div class="flex gap-2">
                        <button
                            class="flex items-center gap-1.5 flex-1 justify-center border border-cpa-border text-cpa-text-secondary hover:bg-cpa-very-light rounded-lg py-2 text-sm font-medium transition-colors"
                            @click="downloadCodes"
                        >
                            <Download :size="14" /> Download
                        </button>
                        <button
                            class="flex-1 bg-cpa-medium-dark hover:bg-cpa-dark text-white font-medium rounded-lg py-2 text-sm transition-colors"
                            @click="done"
                        >
                            I've saved my codes →
                        </button>
                    </div>
                </div>
            </template>

        </div>
    </SettingsLayout>
</template>
