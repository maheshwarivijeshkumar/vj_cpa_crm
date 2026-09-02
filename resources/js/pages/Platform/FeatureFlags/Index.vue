<script setup lang="ts">
import { ref } from 'vue'
import { router } from '@inertiajs/vue3'
import { Flag, ToggleLeft, ToggleRight, Filter } from '@lucide/vue'
import PlatformLayout from '@/layouts/PlatformLayout.vue'

interface FeatureFlag {
    id: number
    key: string
    module: string
    description?: string
    is_enabled: boolean
    scope?: string
    created_at: string
}

const props = defineProps<{
    flags: FeatureFlag[]
    modules: string[]
}>()

const activeModule = ref('all')
const toggling     = ref<number | null>(null)

const filteredFlags = ref(props.flags)

function filterByModule(mod: string) {
    activeModule.value = mod
    filteredFlags.value = mod === 'all'
        ? props.flags
        : props.flags.filter(f => f.module === mod)
}

async function toggle(flag: FeatureFlag) {
    toggling.value = flag.id

    router.post(
        `/platform/feature-flags/${flag.id}/toggle`,
        {},
        {
            preserveState: true,
            onSuccess: () => { toggling.value = null },
            onError:   () => { toggling.value = null },
        }
    )
}
</script>

<template>
    <PlatformLayout>
        <template #header>
            <span class="text-sm font-medium text-cpa-text-primary">Feature Flags</span>
        </template>

        <div class="space-y-6">

            <!-- Page header -->
            <div class="flex items-center justify-between flex-wrap gap-4">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-cpa-very-light flex items-center justify-center">
                        <Flag :size="20" class="text-cpa-medium-dark" />
                    </div>
                    <div>
                        <h1 class="text-xl font-semibold text-cpa-text-primary">Feature Flags</h1>
                        <p class="text-xs text-cpa-text-muted mt-0.5">
                            {{ flags.length }} flags — {{ flags.filter(f => f.is_enabled).length }} enabled
                        </p>
                    </div>
                </div>
            </div>

            <!-- Module filter tabs -->
            <div class="flex flex-wrap gap-2">
                <button
                    :class="['px-3 py-1.5 rounded-lg text-sm font-medium transition-colors', activeModule === 'all' ? 'bg-cpa-medium-dark text-white' : 'bg-white border border-cpa-border text-cpa-text-secondary hover:border-cpa-medium hover:text-cpa-dark']"
                    @click="filterByModule('all')"
                >
                    All modules
                </button>
                <button
                    v-for="mod in modules"
                    :key="mod"
                    :class="['px-3 py-1.5 rounded-lg text-sm font-medium transition-colors capitalize', activeModule === mod ? 'bg-cpa-medium-dark text-white' : 'bg-white border border-cpa-border text-cpa-text-secondary hover:border-cpa-medium hover:text-cpa-dark']"
                    @click="filterByModule(mod)"
                >
                    {{ mod }}
                </button>
            </div>

            <!-- Flags grid -->
            <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-3 gap-4">
                <div
                    v-for="flag in filteredFlags"
                    :key="flag.id"
                    class="bg-white border rounded-xl shadow-sm p-5 transition-colors"
                    :class="flag.is_enabled ? 'border-cpa-medium/30' : 'border-cpa-border'"
                >
                    <div class="flex items-start justify-between gap-3">
                        <div class="flex-1 min-w-0">
                            <!-- Key badge -->
                            <p class="font-mono text-xs text-cpa-text-muted mb-1">{{ flag.module }}/{{ flag.key }}</p>
                            <!-- Description -->
                            <p class="text-sm font-medium text-cpa-text-primary truncate">
                                {{ flag.description ?? flag.key.replace(/_/g, ' ') }}
                            </p>
                            <div class="flex items-center gap-2 mt-1.5">
                                <span :class="['text-[11px] font-semibold px-1.5 py-0.5 rounded-full',
                                    flag.is_enabled ? 'bg-cpa-success-bg text-cpa-success' : 'bg-gray-100 text-gray-500']">
                                    {{ flag.is_enabled ? 'Enabled' : 'Disabled' }}
                                </span>
                                <span v-if="flag.scope" class="text-[11px] text-cpa-text-muted capitalize">
                                    {{ flag.scope }} scope
                                </span>
                            </div>
                        </div>

                        <!-- Toggle button -->
                        <button
                            :disabled="toggling === flag.id"
                            :aria-label="`${flag.is_enabled ? 'Disable' : 'Enable'} ${flag.key}`"
                            class="flex-shrink-0 transition-colors disabled:opacity-50"
                            @click="toggle(flag)"
                        >
                            <component
                                :is="flag.is_enabled ? ToggleRight : ToggleLeft"
                                :size="32"
                                :class="flag.is_enabled ? 'text-cpa-medium-dark' : 'text-gray-300'"
                            />
                        </button>
                    </div>
                </div>

                <div v-if="!filteredFlags.length" class="col-span-full py-16 text-center">
                    <Flag :size="32" class="mx-auto text-cpa-border mb-3" />
                    <p class="text-cpa-text-muted text-sm">No feature flags for this module.</p>
                </div>
            </div>

        </div>
    </PlatformLayout>
</template>
