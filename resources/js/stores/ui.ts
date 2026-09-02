import { defineStore } from 'pinia'
import { ref, computed } from 'vue'
import type { BreadcrumbItem } from '@/types/shared'

export const useUiStore = defineStore('ui', () => {
    // ─── Sidebar ──────────────────────────────────────────────────────────────
    const sidebarOpen = ref(window.innerWidth >= 1024)
    const sidebarCollapsed = ref(false)

    function toggleSidebar(): void {
        if (window.innerWidth < 1024) {
            sidebarOpen.value = !sidebarOpen.value
        } else {
            sidebarCollapsed.value = !sidebarCollapsed.value
        }
    }

    function closeSidebar(): void {
        sidebarOpen.value = false
    }

    // ─── Page title & breadcrumbs ─────────────────────────────────────────────
    const pageTitle = ref('')
    const breadcrumbs = ref<BreadcrumbItem[]>([])

    function setPageTitle(title: string): void {
        pageTitle.value = title
        document.title = title ? `${title} — ${import.meta.env.VITE_APP_NAME || 'CPA CRM'}` : (import.meta.env.VITE_APP_NAME || 'CPA CRM')
    }

    function setBreadcrumbs(items: BreadcrumbItem[]): void {
        breadcrumbs.value = items
    }

    // ─── Notifications panel ──────────────────────────────────────────────────
    const notificationsOpen = ref(false)

    function toggleNotifications(): void {
        notificationsOpen.value = !notificationsOpen.value
    }

    function closeNotifications(): void {
        notificationsOpen.value = false
    }

    // ─── Loading state ────────────────────────────────────────────────────────
    const globalLoading = ref(false)

    // ─── Flash toast ─────────────────────────────────────────────────────────
    interface Toast {
        id: string
        type: 'success' | 'error' | 'warning' | 'info'
        message: string
        duration?: number
    }

    const toasts = ref<Toast[]>([])

    function addToast(type: Toast['type'], message: string, duration = 4000): void {
        const id = Math.random().toString(36).slice(2)
        toasts.value.push({ id, type, message, duration })
        if (duration > 0) {
            setTimeout(() => removeToast(id), duration)
        }
    }

    function removeToast(id: string): void {
        const idx = toasts.value.findIndex((t) => t.id === id)
        if (idx !== -1) toasts.value.splice(idx, 1)
    }

    const toast = {
        success: (msg: string, dur?: number) => addToast('success', msg, dur),
        error:   (msg: string, dur?: number) => addToast('error',   msg, dur),
        warning: (msg: string, dur?: number) => addToast('warning', msg, dur),
        info:    (msg: string, dur?: number) => addToast('info',    msg, dur),
    }

    return {
        sidebarOpen,
        sidebarCollapsed,
        toggleSidebar,
        closeSidebar,
        pageTitle,
        breadcrumbs,
        setPageTitle,
        setBreadcrumbs,
        notificationsOpen,
        toggleNotifications,
        closeNotifications,
        globalLoading,
        toasts,
        addToast,
        removeToast,
        toast,
    }
})
