<script setup lang="ts">
import { computed } from 'vue'
import { AlertTriangle, Trash2, X } from '@lucide/vue'

const props = withDefaults(defineProps<{
    open: boolean
    title?: string
    message?: string
    confirmLabel?: string
    cancelLabel?: string
    /** 'danger' | 'warning' | 'info' */
    variant?: 'danger' | 'warning' | 'info'
    loading?: boolean
}>(), {
    title:        'Are you sure?',
    message:      'This action cannot be undone.',
    confirmLabel: 'Confirm',
    cancelLabel:  'Cancel',
    variant:      'danger',
    loading:      false,
})

const emit = defineEmits<{
    confirm: []
    cancel: []
}>()

const iconComponent = computed(() =>
    props.variant === 'danger' ? Trash2 : AlertTriangle,
)

const iconBgClass = computed(() =>
    ({
        danger:  'bg-cpa-danger-bg text-cpa-danger',
        warning: 'bg-cpa-warning-bg text-cpa-warning',
        info:    'bg-cpa-very-light text-cpa-medium-dark',
    }[props.variant] ?? 'bg-cpa-danger-bg text-cpa-danger'),
)

const confirmBtnClass = computed(() =>
    ({
        danger:  'btn btn-danger',
        warning: 'btn btn-primary',
        info:    'btn btn-primary',
    }[props.variant] ?? 'btn btn-danger'),
)

function onOverlayClick(e: MouseEvent): void {
    if ((e.target as HTMLElement).classList.contains('modal-overlay')) {
        emit('cancel')
    }
}
</script>

<template>
    <Teleport to="body">
        <Transition
            enter-active-class="transition duration-150 ease-out"
            enter-from-class="opacity-0"
            enter-to-class="opacity-100"
            leave-active-class="transition duration-100 ease-in"
            leave-from-class="opacity-100"
            leave-to-class="opacity-0"
        >
            <div
                v-if="open"
                class="modal-overlay"
                role="dialog"
                aria-modal="true"
                :aria-labelledby="'confirm-title'"
                @click="onOverlayClick"
            >
                <Transition
                    enter-active-class="transition duration-150 ease-out"
                    enter-from-class="opacity-0 scale-95"
                    enter-to-class="opacity-100 scale-100"
                    leave-active-class="transition duration-100 ease-in"
                    leave-from-class="opacity-100 scale-100"
                    leave-to-class="opacity-0 scale-95"
                >
                    <div v-if="open" class="modal modal-sm">
                        <!-- Header -->
                        <div class="modal-header">
                            <div class="flex items-center gap-3">
                                <div :class="['w-9 h-9 rounded-lg flex items-center justify-center flex-shrink-0', iconBgClass]">
                                    <component :is="iconComponent" :size="17" />
                                </div>
                                <h2 id="confirm-title" class="modal-title">{{ title }}</h2>
                            </div>
                            <button
                                class="topbar-icon-btn"
                                aria-label="Close"
                                @click="emit('cancel')"
                            >
                                <X :size="17" />
                            </button>
                        </div>

                        <!-- Body -->
                        <div class="modal-body">
                            <p class="text-sm text-cpa-text-muted leading-relaxed">
                                <slot>{{ message }}</slot>
                            </p>
                        </div>

                        <!-- Footer -->
                        <div class="modal-footer">
                            <button
                                class="btn btn-outline btn-sm"
                                :disabled="loading"
                                @click="emit('cancel')"
                            >
                                {{ cancelLabel }}
                            </button>
                            <button
                                :class="[confirmBtnClass, 'btn-sm']"
                                :disabled="loading"
                                @click="emit('confirm')"
                            >
                                <span v-if="loading" class="inline-block w-3.5 h-3.5 border-2 border-white/40 border-t-white rounded-full animate-spin" />
                                {{ confirmLabel }}
                            </button>
                        </div>
                    </div>
                </Transition>
            </div>
        </Transition>
    </Teleport>
</template>
