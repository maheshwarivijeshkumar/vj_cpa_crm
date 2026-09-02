<script setup lang="ts">
import { reactive, ref } from 'vue'
import { Link } from '@inertiajs/vue3'
import { ArrowLeft, Building2, Save, CheckCircle } from '@lucide/vue'
import AppLayout from '@/layouts/AppLayout.vue'

interface OfficeData {
    id: number
    name: string
    code?: string
    is_headquarters: boolean
}

const props = defineProps<{
    office: OfficeData
    settings: {
        invoice_prefix?: string
        invoice_due_days?: string | number
        date_format?: string
        timezone?: string
        currency?: string
        fiscal_year_start_month?: string | number
    }
}>()

const form = reactive({
    invoice_prefix:          props.settings.invoice_prefix          ?? '',
    invoice_due_days:        props.settings.invoice_due_days        ?? 30,
    date_format:             props.settings.date_format             ?? 'Y-m-d',
    timezone:                props.settings.timezone                ?? '',
    currency:                props.settings.currency                ?? 'CAD',
    fiscal_year_start_month: props.settings.fiscal_year_start_month ?? 1,
})

const saving  = ref(false)
const saved   = ref(false)
const errors  = reactive<Record<string, string>>({})

async function save() {
    saving.value = true
    Object.keys(errors).forEach(k => { delete errors[k] })

    try {
        const response = await fetch(`/portal/offices/${props.office.id}/settings`, {
            method: 'PATCH',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': (document.querySelector('meta[name="csrf-token"]') as HTMLMetaElement)?.content ?? '',
            },
            body: JSON.stringify({ settings: form }),
        })

        const json = await response.json()

        if (!response.ok && json.errors) {
            Object.assign(errors, Object.fromEntries(
                Object.entries(json.errors).map(([k, v]) => [k.replace('settings.', ''), (v as string[]).join(', ')])
            ))
        } else {
            saved.value = true
            setTimeout(() => { saved.value = false }, 3000)
        }
    } finally {
        saving.value = false
    }
}

const dateFormatOptions = [
    { value: 'Y-m-d',   label: 'YYYY-MM-DD (ISO 8601)' },
    { value: 'd/m/Y',   label: 'DD/MM/YYYY' },
    { value: 'm/d/Y',   label: 'MM/DD/YYYY' },
    { value: 'F j, Y',  label: 'Month D, YYYY' },
]

const months = [
    'January','February','March','April','May','June',
    'July','August','September','October','November','December',
]
</script>

<template>
    <AppLayout>
        <div class="max-w-2xl space-y-6">

            <!-- Header -->
            <div class="flex items-center gap-3">
                <Link href="/portal/offices" class="flex items-center justify-center w-8 h-8 rounded-lg border border-cpa-border hover:bg-cpa-very-light transition-colors">
                    <ArrowLeft :size="16" class="text-cpa-text-secondary" />
                </Link>
                <div class="flex items-center gap-2">
                    <div class="w-9 h-9 rounded-xl bg-cpa-very-light flex items-center justify-center">
                        <Building2 :size="17" class="text-cpa-medium-dark" />
                    </div>
                    <div>
                        <h1 class="text-xl font-semibold text-cpa-text-primary">{{ office.name }}</h1>
                        <p class="text-xs text-cpa-text-muted">Office settings override</p>
                    </div>
                </div>
            </div>

            <!-- Success flash -->
            <Transition name="fade">
                <div v-if="saved" class="flex items-center gap-2.5 bg-cpa-success-bg text-cpa-success border border-cpa-success/20 rounded-xl px-4 py-3 text-sm font-medium">
                    <CheckCircle :size="16" class="flex-shrink-0" /> Office settings saved.
                </div>
            </Transition>

            <div class="bg-white border border-cpa-border rounded-xl shadow-sm p-6 space-y-5">
                <p class="text-xs text-cpa-text-muted bg-cpa-very-light rounded-lg px-3 py-2">
                    These settings override the firm-wide defaults for this office only. Leave blank to inherit the firm default.
                </p>

                <!-- Invoice settings -->
                <div>
                    <h3 class="text-sm font-semibold text-cpa-text-primary mb-3">Invoicing</h3>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-cpa-text-primary mb-1.5">Invoice Prefix</label>
                            <input v-model="form.invoice_prefix" type="text" placeholder="e.g. TOR-INV" class="w-full px-3 py-2 text-sm border border-cpa-border rounded-lg focus:outline-none focus:ring-2 focus:ring-cpa-medium placeholder:text-cpa-text-muted" :class="{ 'border-cpa-danger': errors.invoice_prefix }" />
                            <p v-if="errors.invoice_prefix" class="text-cpa-danger text-xs mt-1">{{ errors.invoice_prefix }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-cpa-text-primary mb-1.5">Invoice Due Days</label>
                            <input v-model.number="form.invoice_due_days" type="number" min="0" max="365" placeholder="30" class="w-full px-3 py-2 text-sm border border-cpa-border rounded-lg focus:outline-none focus:ring-2 focus:ring-cpa-medium placeholder:text-cpa-text-muted" />
                        </div>
                    </div>
                </div>

                <!-- Locale settings -->
                <div>
                    <h3 class="text-sm font-semibold text-cpa-text-primary mb-3">Locale</h3>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-cpa-text-primary mb-1.5">Currency</label>
                            <input v-model="form.currency" type="text" placeholder="CAD" maxlength="3" class="w-full px-3 py-2 text-sm border border-cpa-border rounded-lg focus:outline-none focus:ring-2 focus:ring-cpa-medium placeholder:text-cpa-text-muted uppercase" />
                            <p class="text-xs text-cpa-text-muted mt-1">ISO 4217 code (e.g. CAD, USD, GBP)</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-cpa-text-primary mb-1.5">Date Format</label>
                            <select v-model="form.date_format" class="w-full px-3 py-2 text-sm border border-cpa-border rounded-lg bg-white focus:outline-none focus:ring-2 focus:ring-cpa-medium">
                                <option v-for="opt in dateFormatOptions" :key="opt.value" :value="opt.value">{{ opt.label }}</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-cpa-text-primary mb-1.5">Timezone</label>
                            <input v-model="form.timezone" type="text" placeholder="America/Toronto" class="w-full px-3 py-2 text-sm border border-cpa-border rounded-lg focus:outline-none focus:ring-2 focus:ring-cpa-medium placeholder:text-cpa-text-muted" />
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-cpa-text-primary mb-1.5">Fiscal Year Start Month</label>
                            <select v-model.number="form.fiscal_year_start_month" class="w-full px-3 py-2 text-sm border border-cpa-border rounded-lg bg-white focus:outline-none focus:ring-2 focus:ring-cpa-medium">
                                <option v-for="(name, idx) in months" :key="idx+1" :value="idx+1">{{ name }}</option>
                            </select>
                        </div>
                    </div>
                </div>

                <!-- Save -->
                <div class="flex justify-end pt-2 border-t border-cpa-border">
                    <button
                        :disabled="saving"
                        type="button"
                        class="flex items-center gap-1.5 bg-cpa-medium-dark hover:bg-cpa-dark text-white font-medium rounded-lg px-5 py-2 text-sm transition-colors disabled:opacity-60"
                        @click="save"
                    >
                        <Save :size="14" />
                        {{ saving ? 'Saving…' : 'Save Office Settings' }}
                    </button>
                </div>
            </div>
        </div>
    </AppLayout>
</template>

<style scoped>
.fade-enter-active, .fade-leave-active { transition: opacity .25s ease; }
.fade-enter-from, .fade-leave-to { opacity: 0; }
</style>
