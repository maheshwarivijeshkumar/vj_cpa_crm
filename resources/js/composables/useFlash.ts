import { usePage } from '@inertiajs/vue3'
import { watch, onMounted } from 'vue'
import { useToast } from './useToast'
import type { FlashMessages } from '@/types/shared'

/**
 * Watches Inertia flash props and converts them to UI toasts automatically.
 * Drop this composable in any layout to auto-display server flash messages.
 *
 * Usage: useFlash() inside setup() of AppLayout.vue
 */
export function useFlash(): void {
    const page   = usePage()
    const { toast } = useToast()

    function processFlash(flash: FlashMessages): void {
        if (flash.success) toast.success(flash.success)
        if (flash.error)   toast.error(flash.error)
        if (flash.warning) toast.warning(flash.warning)
        if (flash.info)    toast.info(flash.info)
    }

    onMounted(() => {
        const flash = (page.props as any).flash as FlashMessages
        if (flash) processFlash(flash)
    })

    watch(
        () => (page.props as any).flash as FlashMessages,
        (flash) => { if (flash) processFlash(flash) },
    )
}
