import { useAuthStore } from '@/stores/auth'

/**
 * Composable shorthand for permission checks.
 * Keeps template code clean: can('clients.create')
 */
export function usePermission() {
    const auth = useAuthStore()
    return {
        can:        auth.can,
        canAny:     auth.canAny,
        canAll:     auth.canAll,
        hasRole:    auth.hasRole,
        hasAnyRole: auth.hasAnyRole,
    }
}
