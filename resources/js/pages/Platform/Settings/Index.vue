<script setup lang="ts">
import PlatformLayout from '@/layouts/PlatformLayout.vue'
import SeoHead from '@/components/ui/SeoHead.vue'
import { router } from '@inertiajs/vue3'
import { ref } from 'vue'
import { Save, RefreshCw, Settings } from '@lucide/vue'
import { useToast } from '@/composables/useToast'
import { useUiStore } from '@/stores/ui'

defineOptions({ layout: PlatformLayout })

const ui = useUiStore()
ui.setPageTitle('Platform Settings')
const { toast } = useToast()

interface SettingItem {
    id: number; group: string; key: string
    value: string | null; type: string; description: string | null; is_public: boolean
}

const props = defineProps<{
    settingGroups: Record<string, SettingItem[]>
}>()

// Local editable copy
const localGroups = ref<Record<string, SettingItem[]>>(
    JSON.parse(JSON.stringify(props.settingGroups))
)

const saving = ref(false)

async function save() {
    saving.value = true
    const settings = Object.values(localGroups.value).flat().map(s => ({
        group: s.group,
        key:   s.key,
        value: s.value,
        type:  s.type,
    }))

    router.patch('/platform/settings', { settings }, {
        onSuccess: () => toast.success('Settings saved.'),
        onError:   () => toast.error('Failed to save settings.'),
        onFinish:  () => { saving.value = false },
    })
}

async function clearCache() {
    router.post('/platform/settings/clear-cache', {}, {
        onSuccess: () => toast.success('Cache cleared.'),
    })
}

function groupLabel(group: string) {
    return group.replace(/_/g, ' ').replace(/\b\w/g, l => l.toUpperCase())
}
</script>

<template>
    <SeoHead :seo="{ title: 'Platform Settings — Admin', robots: 'noindex,nofollow' }" />

    <div class="flex items-center justify-between mb-6 flex-wrap gap-3">
        <div>
            <h1 class="text-2xl font-800 text-gray-900 tracking-tight">Platform Settings</h1>
            <p class="text-sm text-gray-500 mt-0.5">Global configuration for the entire platform.</p>
        </div>
        <div class="flex gap-2">
            <button class="btn btn-outline btn-sm" @click="clearCache">
                <RefreshCw :size="14" /> Clear Cache
            </button>
            <button class="btn btn-primary btn-sm" :disabled="saving" @click="save">
                <Save :size="14" /> {{ saving ? 'Saving…' : 'Save Changes' }}
            </button>
        </div>
    </div>

    <div class="space-y-6">
        <div
            v-for="(items, group) in localGroups"
            :key="group"
            class="bg-white border border-gray-100 rounded-xl overflow-hidden"
        >
            <div class="flex items-center gap-2.5 px-5 py-3.5 bg-gray-50 border-b border-gray-100">
                <Settings :size="15" class="text-gray-400" />
                <h3 class="text-sm font-700 text-gray-700 uppercase tracking-wide">{{ groupLabel(group as string) }}</h3>
            </div>

            <div class="divide-y divide-gray-50">
                <div
                    v-for="setting in items"
                    :key="setting.id"
                    class="grid grid-cols-[1fr_2fr] gap-4 px-5 py-4 items-start hover:bg-gray-50/50"
                >
                    <div>
                        <p class="text-sm font-600 text-gray-800">{{ setting.key.replace(/_/g, ' ').replace(/\b\w/g, l => l.toUpperCase()) }}</p>
                        <p v-if="setting.description" class="text-xs text-gray-400 mt-0.5 leading-snug">{{ setting.description }}</p>
                        <span v-if="setting.is_public" class="mt-1 inline-block badge badge-teal text-[10px]">Public</span>
                    </div>
                    <div>
                        <!-- Boolean toggle -->
                        <div v-if="setting.type === 'boolean'" class="flex items-center gap-2.5">
                            <button
                                type="button"
                                class="relative w-10 h-5 rounded-full transition-colors"
                                :class="setting.value === 'true' ? 'bg-cpa-medium-dark' : 'bg-gray-200'"
                                @click="setting.value = setting.value === 'true' ? 'false' : 'true'"
                            >
                                <span
                                    class="absolute top-0.5 w-4 h-4 bg-white rounded-full shadow transition-transform"
                                    :class="setting.value === 'true' ? 'translate-x-5' : 'translate-x-0.5'"
                                />
                            </button>
                            <span class="text-sm text-gray-600">{{ setting.value === 'true' ? 'Enabled' : 'Disabled' }}</span>
                        </div>
                        <!-- Integer input -->
                        <input
                            v-else-if="setting.type === 'integer'"
                            v-model="setting.value"
                            type="number"
                            class="form-input text-sm w-32"
                        />
                        <!-- Text input -->
                        <input
                            v-else
                            v-model="setting.value"
                            type="text"
                            class="form-input text-sm w-full max-w-sm"
                        />
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
