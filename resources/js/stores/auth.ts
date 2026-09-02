import { defineStore } from 'pinia'
import { usePage } from '@inertiajs/vue3'
import { computed } from 'vue'
import type { AuthUser, AuthTenant } from '@/types/shared'

export const useAuthStore = defineStore('auth', () => {
    const page = usePage()

    // ─── State (derived from Inertia shared props) ────────────────────────────
    const user = computed<AuthUser | null>(() => page.props.auth?.user ?? null)
    const tenant = computed<AuthTenant | null>(() => page.props.tenant ?? null)
    const permissions = computed<string[]>(() => page.props.auth?.permissions ?? [])
    const roles = computed<string[]>(() => page.props.auth?.roles ?? [])

    // ─── Permission helpers ───────────────────────────────────────────────────

    /**
     * Check if the user has a specific permission.
     * Platform admins always return true.
     * Use for UI visibility only — backend is the authority.
     */
    function can(permission: string): boolean {
        if (!user.value) return false
        if (user.value.user_type === 'platform_admin') return true
        return permissions.value.includes(permission)
    }

    /**
     * Check if user has ANY of the given permissions.
     */
    function canAny(perms: string[]): boolean {
        return perms.some((p) => can(p))
    }

    /**
     * Check if user has ALL of the given permissions.
     */
    function canAll(perms: string[]): boolean {
        return perms.every((p) => can(p))
    }

    /**
     * Check if user has a specific role slug.
     */
    function hasRole(slug: string): boolean {
        return roles.value.includes(slug)
    }

    /**
     * Check if user has any of the given role slugs.
     */
    function hasAnyRole(slugs: string[]): boolean {
        return slugs.some((s) => hasRole(s))
    }

    // ─── Type helpers ─────────────────────────────────────────────────────────
    const isPlatformAdmin = computed(() => user.value?.user_type === 'platform_admin')
    const isFirmOwner = computed(() => user.value?.user_type === 'firm_owner')
    const isFirmUser = computed(() => ['firm_owner', 'firm_user'].includes(user.value?.user_type ?? ''))
    const isClient = computed(() => user.value?.user_type === 'client')
    const isAuthenticated = computed(() => user.value !== null)
    const mustChangePassword = computed(() => user.value?.must_change_password ?? false)

    // ─── Display helpers ──────────────────────────────────────────────────────
    const initials = computed(() => {
        if (!user.value) return '?'
        const first = user.value.first_name?.[0] ?? ''
        const last = user.value.last_name?.[0] ?? ''
        return (first + last).toUpperCase() || user.value.email[0].toUpperCase()
    })

    return {
        user,
        tenant,
        permissions,
        roles,
        can,
        canAny,
        canAll,
        hasRole,
        hasAnyRole,
        isPlatformAdmin,
        isFirmOwner,
        isFirmUser,
        isClient,
        isAuthenticated,
        mustChangePassword,
        initials,
    }
})
