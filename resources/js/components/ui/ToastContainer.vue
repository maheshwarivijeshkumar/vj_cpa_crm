<script setup lang="ts">
import { CheckCircle2, XCircle, AlertTriangle, Info, X } from '@lucide/vue'
import { useUiStore } from '@/stores/ui'

const ui = useUiStore()

const config = {
    success: { icon: CheckCircle2, cls: 'border-cpa-success bg-cpa-success-bg text-cpa-success' },
    error:   { icon: XCircle,      cls: 'border-cpa-danger  bg-cpa-danger-bg  text-cpa-danger' },
    warning: { icon: AlertTriangle, cls: 'border-cpa-warning bg-cpa-warning-bg text-cpa-warning' },
    info:    { icon: Info,          cls: 'border-cpa-info    bg-cpa-info-bg    text-cpa-info' },
} as const
</script>

<template>
    <Teleport to="body">
        <div
            class="fixed bottom-5 right-5 z-[999] flex flex-col gap-2.5 max-w-sm w-full pointer-events-none"
            aria-live="polite"
            aria-label="Notifications"
        >
            <TransitionGroup
                enter-active-class="transition duration-200 ease-out"
                enter-from-class="opacity-0 translate-y-2"
                enter-to-class="opacity-100 translate-y-0"
                leave-active-class="transition duration-150 ease-in"
                leave-from-class="opacity-100"
                leave-to-class="opacity-0 scale-95"
            >
                <div
                    v-for="toast in ui.toasts"
                    :key="toast.id"
                    :class="[
                        'pointer-events-auto flex items-start gap-3 px-4 py-3 rounded-xl border shadow-lg bg-white',
                        config[toast.type]?.cls ?? '',
                    ]"
                    role="alert"
                >
                    <component
                        :is="config[toast.type]?.icon"
                        :size="17"
                        class="flex-shrink-0 mt-0.5"
                    />
                    <p class="flex-1 text-[13.5px] font-medium text-cpa-text-primary leading-snug">
                        {{ toast.message }}
                    </p>
                    <button
                        class="flex-shrink-0 text-cpa-text-muted hover:text-cpa-text-primary transition-colors"
                        :aria-label="`Dismiss ${toast.type} notification`"
                        @click="ui.removeToast(toast.id)"
                    >
                        <X :size="15" />
                    </button>
                </div>
            </TransitionGroup>
        </div>
    </Teleport>
</template>
