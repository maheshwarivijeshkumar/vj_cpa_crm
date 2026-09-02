import { useUiStore } from '@/stores/ui'

/**
 * Composable for showing toast notifications.
 * Usage: const { toast } = useToast()
 *        toast.success('Client saved.')
 */
export function useToast() {
    const ui = useUiStore()
    return { toast: ui.toast }
}
