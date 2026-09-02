<script setup lang="ts">
import { ref } from 'vue'
import { Link, router } from '@inertiajs/vue3'
import { Building2, Plus, Settings, Users, CheckCircle } from '@lucide/vue'
import AppLayout from '@/layouts/AppLayout.vue'

interface Office {
    id: number
    name: string
    code?: string
    email?: string
    phone?: string
    address?: string
    is_headquarters: boolean
    is_active: boolean
    users_count: number
}

const props = defineProps<{
    offices: Office[]
}>()

const createOpen = ref(false)
const formData = ref({ name: '', code: '', email: '', phone: '' })
const saving    = ref(false)
const flashMsg  = ref('')

async function createOffice() {
    saving.value = true
    try {
        const response = await fetch('/portal/offices', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': (document.querySelector('meta[name="csrf-token"]') as HTMLMetaElement)?.content ?? '',
            },
            body: JSON.stringify(formData.value),
        })
        if (response.ok) {
            flashMsg.value = 'Office created.'
            createOpen.value = false
            formData.value = { name: '', code: '', email: '', phone: '' }
            router.reload({ only: ['offices'] })
            setTimeout(() => { flashMsg.value = '' }, 3000)
        }
    } finally {
        saving.value = false
    }
}

function deactivate(office: Office) {
    if (!confirm(`Remove office "${office.name}"? This cannot be undone.`)) return
    router.delete(`/portal/offices/${office.id}`, { preserveState: false })
}
</script>

<template>
    <AppLayout>
        <div class="space-y-6">

            <!-- Header -->
            <div class="flex items-center justify-between flex-wrap gap-4">
                <div class="flex items-center gap-3">
                    <div class="w-9 h-9 rounded-xl bg-cpa-very-light flex items-center justify-center">
                        <Building2 :size="18" class="text-cpa-medium-dark" />
                    </div>
                    <div>
                        <h1 class="text-xl font-semibold text-cpa-text-primary">Offices</h1>
                        <p class="text-xs text-cpa-text-muted">{{ offices.length }} office{{ offices.length === 1 ? '' : 's' }}</p>
                    </div>
                </div>
                <button
                    class="flex items-center gap-1.5 bg-cpa-medium-dark hover:bg-cpa-dark text-white font-medium rounded-lg px-4 py-2 text-sm transition-colors"
                    @click="createOpen = true"
                >
                    <Plus :size="15" /> Add Office
                </button>
            </div>

            <!-- Flash -->
            <Transition name="fade">
                <div v-if="flashMsg" class="flex items-center gap-2.5 bg-cpa-success-bg text-cpa-success border border-cpa-success/20 rounded-xl px-4 py-3 text-sm font-medium">
                    <CheckCircle :size="15" class="flex-shrink-0" /> {{ flashMsg }}
                </div>
            </Transition>

            <!-- Office cards -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                <div
                    v-for="office in offices"
                    :key="office.id"
                    class="bg-white border rounded-xl shadow-sm p-5 transition-colors"
                    :class="office.is_headquarters ? 'border-cpa-medium/40' : 'border-cpa-border'"
                >
                    <div class="flex items-start justify-between gap-2 mb-3">
                        <div>
                            <div class="flex items-center gap-2">
                                <h3 class="font-semibold text-cpa-text-primary">{{ office.name }}</h3>
                                <span v-if="office.is_headquarters" class="text-[10px] bg-cpa-very-light text-cpa-medium-dark font-semibold px-2 py-0.5 rounded-full border border-cpa-medium/20">HQ</span>
                            </div>
                            <p v-if="office.code" class="text-xs text-cpa-text-muted font-mono mt-0.5">{{ office.code }}</p>
                        </div>
                        <span :class="['text-[11px] font-semibold px-2 py-0.5 rounded-full flex-shrink-0', office.is_active ? 'bg-cpa-success-bg text-cpa-success' : 'bg-gray-100 text-gray-500']">
                            {{ office.is_active ? 'Active' : 'Inactive' }}
                        </span>
                    </div>

                    <p v-if="office.address" class="text-xs text-cpa-text-muted mb-2">{{ office.address }}</p>
                    <p v-if="office.email"   class="text-xs text-cpa-text-secondary mb-1">✉ {{ office.email }}</p>
                    <p v-if="office.phone"   class="text-xs text-cpa-text-secondary mb-3">✆ {{ office.phone }}</p>

                    <div class="flex items-center justify-between mt-3 pt-3 border-t border-cpa-border">
                        <span class="flex items-center gap-1 text-xs text-cpa-text-muted">
                            <Users :size="12" /> {{ office.users_count }} user{{ office.users_count === 1 ? '' : 's' }}
                        </span>
                        <div class="flex items-center gap-2">
                            <Link
                                :href="`/portal/offices/${office.id}/settings`"
                                class="flex items-center gap-1 text-xs text-cpa-medium-dark hover:text-cpa-dark font-medium transition-colors"
                            >
                                <Settings :size="13" /> Settings
                            </Link>
                            <button
                                v-if="!office.is_headquarters"
                                class="text-xs text-cpa-danger hover:text-red-700 font-medium transition-colors"
                                @click="deactivate(office)"
                            >
                                Remove
                            </button>
                        </div>
                    </div>
                </div>

                <div v-if="!offices.length" class="col-span-full py-16 text-center text-cpa-text-muted text-sm">
                    <Building2 :size="32" class="mx-auto text-cpa-border mb-3" />
                    No offices yet. Add your first office location.
                </div>
            </div>
        </div>

        <!-- Create modal -->
        <Transition name="fade">
            <div v-if="createOpen" class="fixed inset-0 bg-black/50 z-50 flex items-center justify-center p-4" @click.self="createOpen = false">
                <div class="bg-white rounded-xl shadow-xl max-w-sm w-full p-6">
                    <h3 class="text-base font-semibold text-cpa-text-primary mb-4">Add Office</h3>
                    <div class="space-y-3">
                        <div>
                            <label class="block text-sm font-medium text-cpa-text-primary mb-1.5">Office Name *</label>
                            <input v-model="formData.name" type="text" placeholder="e.g. Toronto Main" class="w-full px-3 py-2 text-sm border border-cpa-border rounded-lg focus:outline-none focus:ring-2 focus:ring-cpa-medium placeholder:text-cpa-text-muted" />
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-cpa-text-primary mb-1.5">Code <span class="text-cpa-text-muted font-normal">(optional)</span></label>
                            <input v-model="formData.code" type="text" placeholder="e.g. TOR" class="w-full px-3 py-2 text-sm border border-cpa-border rounded-lg focus:outline-none focus:ring-2 focus:ring-cpa-medium placeholder:text-cpa-text-muted" />
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-cpa-text-primary mb-1.5">Email <span class="text-cpa-text-muted font-normal">(optional)</span></label>
                            <input v-model="formData.email" type="email" placeholder="toronto@yourfirm.com" class="w-full px-3 py-2 text-sm border border-cpa-border rounded-lg focus:outline-none focus:ring-2 focus:ring-cpa-medium placeholder:text-cpa-text-muted" />
                        </div>
                    </div>
                    <div class="flex gap-2 mt-5">
                        <button class="flex-1 border border-cpa-border text-cpa-text-secondary hover:bg-cpa-bg rounded-lg py-2 text-sm font-medium transition-colors" @click="createOpen = false">Cancel</button>
                        <button :disabled="saving || !formData.name" class="flex-1 bg-cpa-medium-dark hover:bg-cpa-dark text-white font-medium rounded-lg py-2 text-sm transition-colors disabled:opacity-60" @click="createOffice">
                            {{ saving ? 'Creating…' : 'Create Office' }}
                        </button>
                    </div>
                </div>
            </div>
        </Transition>
    </AppLayout>
</template>

<style scoped>
.fade-enter-active, .fade-leave-active { transition: opacity .2s ease; }
.fade-enter-from, .fade-leave-to { opacity: 0; }
</style>
