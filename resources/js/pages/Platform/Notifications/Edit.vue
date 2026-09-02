<script setup lang="ts">
import { ref, reactive } from 'vue'
import { Link, router } from '@inertiajs/vue3'
import { ArrowLeft, Bell, Eye, Save } from '@lucide/vue'
import PlatformLayout from '@/layouts/PlatformLayout.vue'
import { useFlash } from '@/composables/useFlash'

interface NotificationTemplate {
    id: number
    key: string
    name: string
    channel: string
    category: string
    subject?: string
    body_html?: string
    body_text?: string
    body_short?: string
    description?: string
    available_variables?: string[]
    is_active: boolean
}

const props = defineProps<{ template: NotificationTemplate }>()
const { flash } = useFlash()

const form = reactive({
    name:       props.template.name ?? '',
    subject:    props.template.subject ?? '',
    body_html:  props.template.body_html ?? '',
    body_text:  props.template.body_text ?? '',
    body_short: props.template.body_short ?? '',
    description:props.template.description ?? '',
})

const processing = ref(false)
const preview    = ref<string | null>(null)
const showPreview = ref(false)

function save() {
    processing.value = true
    router.patch(
        `/platform/notifications/${props.template.id}`,
        form,
        {
            preserveState: true,
            onSuccess: () => { processing.value = false },
            onError:   () => { processing.value = false },
        }
    )
}

async function loadPreview() {
    const response = await fetch(`/platform/notifications/${props.template.id}/preview`, {
        method: 'POST',
        headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') ?? '', 'Accept': 'application/json' },
    })
    const data = await response.json()
    preview.value = data?.data?.rendered ?? ''
    showPreview.value = true
}

const variablesList = Array.isArray(props.template.available_variables)
    ? props.template.available_variables
    : []

// Returns "{{variable}}" string safely without Vue parsing it as interpolation
function varPlaceholder(v: string): string {
    return `{{${v}}}`
}
</script>

<template>
    <PlatformLayout>
        <template #header>
            <div class="flex items-center gap-2 text-sm text-cpa-text-muted">
                <Link href="/platform/notifications" class="hover:text-cpa-text-primary transition-colors">Notifications</Link>
                <span>/</span>
                <span class="text-cpa-text-primary font-medium">Edit Template</span>
            </div>
        </template>

        <div class="max-w-3xl space-y-6">
            <!-- Header -->
            <div class="flex items-center gap-3">
                <Link href="/platform/notifications" class="flex items-center justify-center w-8 h-8 rounded-lg border border-cpa-border hover:bg-cpa-very-light transition-colors">
                    <ArrowLeft :size="16" class="text-cpa-text-secondary" />
                </Link>
                <div>
                    <h1 class="text-xl font-semibold text-cpa-text-primary">{{ template.name }}</h1>
                    <p class="text-xs text-cpa-text-muted font-mono mt-0.5">{{ template.key }} · {{ template.channel }}</p>
                </div>
            </div>

            <!-- Form card -->
            <div class="bg-white border border-cpa-border rounded-xl shadow-sm p-6 space-y-5">

                <!-- Name -->
                <div>
                    <label class="block text-sm font-medium text-cpa-text-primary mb-1.5">Template Name</label>
                    <input v-model="form.name" type="text" class="w-full px-3 py-2 text-sm border border-cpa-border rounded-lg focus:outline-none focus:ring-2 focus:ring-cpa-medium focus:border-cpa-medium" />
                </div>

                <!-- Description -->
                <div>
                    <label class="block text-sm font-medium text-cpa-text-primary mb-1.5">Description</label>
                    <input v-model="form.description" type="text" placeholder="Internal notes…" class="w-full px-3 py-2 text-sm border border-cpa-border rounded-lg focus:outline-none focus:ring-2 focus:ring-cpa-medium focus:border-cpa-medium placeholder:text-cpa-text-muted" />
                </div>

                <!-- Subject (email only) -->
                <div v-if="template.channel === 'email'">
                    <label class="block text-sm font-medium text-cpa-text-primary mb-1.5">Email Subject</label>
                    <input v-model="form.subject" type="text" placeholder="Use {{variable}} for dynamic content" class="w-full px-3 py-2 text-sm border border-cpa-border rounded-lg focus:outline-none focus:ring-2 focus:ring-cpa-medium focus:border-cpa-medium placeholder:text-cpa-text-muted" />
                </div>

                <!-- In-app short body -->
                <div v-if="template.channel === 'in_app'">
                    <label class="block text-sm font-medium text-cpa-text-primary mb-1.5">Notification Text</label>
                    <input v-model="form.body_short" type="text" placeholder="Short notification message…" class="w-full px-3 py-2 text-sm border border-cpa-border rounded-lg focus:outline-none focus:ring-2 focus:ring-cpa-medium focus:border-cpa-medium placeholder:text-cpa-text-muted" />
                </div>

                <!-- HTML body -->
                <div v-if="template.channel === 'email'">
                    <label class="block text-sm font-medium text-cpa-text-primary mb-1.5">HTML Body</label>
                    <textarea
                        v-model="form.body_html"
                        rows="12"
                        class="w-full px-3 py-2 text-sm border border-cpa-border rounded-lg font-mono focus:outline-none focus:ring-2 focus:ring-cpa-medium focus:border-cpa-medium placeholder:text-cpa-text-muted"
                        placeholder="<p>HTML email body with {{variable}} placeholders</p>"
                    />
                </div>

                <!-- Plain-text body -->
                <div v-if="template.channel === 'email'">
                    <label class="block text-sm font-medium text-cpa-text-primary mb-1.5">Plain Text Fallback</label>
                    <textarea
                        v-model="form.body_text"
                        rows="5"
                        class="w-full px-3 py-2 text-sm border border-cpa-border rounded-lg font-mono focus:outline-none focus:ring-2 focus:ring-cpa-medium focus:border-cpa-medium placeholder:text-cpa-text-muted"
                        placeholder="Plain text version for email clients that don't support HTML"
                    />
                </div>

                <!-- Available variables -->
                <div v-if="variablesList.length">
                    <p class="text-xs font-semibold text-cpa-text-secondary uppercase tracking-wide mb-2">Available Variables</p>
                    <div class="flex flex-wrap gap-1.5">
                        <span
                            v-for="v in variablesList"
                            :key="v"
                            class="font-mono text-[11px] bg-cpa-very-light text-cpa-dark px-2 py-0.5 rounded border border-cpa-border cursor-default"
                        >
                            {{ varPlaceholder(v) }}
                        </span>
                    </div>
                </div>

                <!-- Actions -->
                <div class="flex items-center justify-between pt-2 border-t border-cpa-border">
                    <button
                        v-if="template.channel === 'email'"
                        class="flex items-center gap-1.5 border border-cpa-border text-cpa-text-secondary hover:border-cpa-medium hover:text-cpa-dark rounded-lg px-3 py-2 text-sm font-medium transition-colors"
                        type="button"
                        @click="loadPreview"
                    >
                        <Eye :size="14" /> Preview
                    </button>
                    <div v-else />

                    <button
                        :disabled="processing"
                        class="flex items-center gap-1.5 bg-cpa-medium-dark hover:bg-cpa-dark text-white font-medium rounded-lg px-5 py-2 text-sm transition-colors disabled:opacity-60"
                        @click="save"
                    >
                        <Save :size="14" />
                        {{ processing ? 'Saving…' : 'Save Changes' }}
                    </button>
                </div>
            </div>
        </div>

        <!-- Preview modal -->
        <Transition name="fade">
            <div v-if="showPreview" class="fixed inset-0 bg-black/50 z-50 flex items-center justify-center p-4" @click.self="showPreview = false">
                <div class="bg-white rounded-xl shadow-xl max-w-2xl w-full max-h-[80vh] overflow-y-auto">
                    <div class="flex items-center justify-between px-5 py-4 border-b border-cpa-border">
                        <h3 class="font-semibold text-cpa-text-primary">Email Preview</h3>
                        <button class="text-cpa-text-muted hover:text-cpa-text-primary" @click="showPreview = false">✕</button>
                    </div>
                    <div class="p-5 prose max-w-none text-sm" v-html="preview" />
                </div>
            </div>
        </Transition>
    </PlatformLayout>
</template>

<style scoped>
.fade-enter-active, .fade-leave-active { transition: opacity .2s ease; }
.fade-enter-from, .fade-leave-to { opacity: 0; }
</style>
