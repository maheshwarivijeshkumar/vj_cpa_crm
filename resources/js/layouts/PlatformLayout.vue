<script setup lang="ts">
import { ref, computed } from 'vue'
import { Link, usePage, router } from '@inertiajs/vue3'
import {
    LayoutDashboard, Building2, Users, Settings, Shield,
    FileText, Bell, LogOut, ChevronDown, Menu, X,
    Activity, Flag,
} from '@lucide/vue'
import { useAuthStore } from '@/stores/auth'
import { useUiStore } from '@/stores/ui'
import { useFlash } from '@/composables/useFlash'
import ToastContainer from '@/components/ui/ToastContainer.vue'

useFlash()

const auth = useAuthStore()
const ui   = useUiStore()
const page = usePage()

const currentUrl = computed(() => page.url)
function isActive(href: string) {
    return currentUrl.value === href || currentUrl.value.startsWith(href + '/')
}

const sidebarOpen = ref(window.innerWidth >= 1024)

function logout() { router.post('/logout') }

const navItems = [
    { href: '/platform',          icon: LayoutDashboard, label: 'Dashboard'  },
    { href: '/platform/tenants',  icon: Building2,       label: 'Tenants'    },
    { href: '/platform/users',    icon: Users,           label: 'Users'      },
    { href: '/platform/settings', icon: Settings,        label: 'Settings'   },
    { href: '/platform/audit-logs', icon: Activity,      label: 'Audit Logs' },
]
</script>

<template>
    <div class="flex min-h-screen bg-gray-50">

        <!-- Sidebar overlay (mobile) -->
        <div
            v-if="sidebarOpen && $el?.clientWidth < 1024"
            class="fixed inset-0 bg-black/40 z-30 lg:hidden"
            @click="sidebarOpen = false"
        />

        <!-- ── Platform Sidebar ─────────────────────────────────────── -->
        <aside
            class="platform-sidebar"
            :class="{ 'translate-x-0': sidebarOpen, '-translate-x-full': !sidebarOpen }"
        >
            <!-- Logo -->
            <div class="platform-sidebar-logo">
                <div class="platform-logo-mark">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none">
                        <path d="M9 5H7a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2V7a2 2 0 0 0-2-2h-2" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                        <rect x="9" y="3" width="6" height="4" rx="1" stroke="currentColor" stroke-width="2"/>
                        <path d="M9 12h6M9 16h4" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                    </svg>
                </div>
                <div>
                    <p class="text-white text-[13px] font-700 leading-tight">VJ CPA CRM</p>
                    <p class="text-white/40 text-[10px] font-600 tracking-wider uppercase">Platform Admin</p>
                </div>
            </div>

            <!-- Nav -->
            <nav class="flex-1 py-3 overflow-y-auto">
                <Link
                    v-for="item in navItems"
                    :key="item.href"
                    :href="item.href"
                    class="platform-nav-item"
                    :class="{ active: isActive(item.href) }"
                    @click="sidebarOpen = window.innerWidth >= 1024"
                >
                    <component :is="item.icon" :size="17" class="flex-shrink-0" />
                    {{ item.label }}
                </Link>
            </nav>

            <!-- Bottom: user + logout -->
            <div class="border-t border-white/10 p-3 space-y-1">
                <div class="flex items-center gap-2.5 px-3 py-2">
                    <div class="w-7 h-7 rounded-full bg-cpa-medium flex items-center justify-center text-white text-[11px] font-700 flex-shrink-0">
                        {{ auth.initials }}
                    </div>
                    <div class="min-w-0">
                        <p class="text-white text-[12px] font-500 truncate">{{ auth.user?.name }}</p>
                        <p class="text-white/40 text-[11px] truncate">{{ auth.user?.email }}</p>
                    </div>
                </div>
                <Link href="/" class="platform-nav-item text-white/60">
                    <Flag :size="16" />
                    Marketing Site
                </Link>
                <button class="platform-nav-item w-full text-left" @click="logout">
                    <LogOut :size="16" />
                    Sign out
                </button>
            </div>
        </aside>

        <!-- ── Main ────────────────────────────────────────────────── -->
        <div class="platform-main">

            <!-- Topbar -->
            <header class="platform-topbar">
                <button
                    class="lg:hidden w-8 h-8 flex items-center justify-center rounded-lg hover:bg-gray-100 transition-colors"
                    @click="sidebarOpen = !sidebarOpen"
                    aria-label="Toggle sidebar"
                >
                    <component :is="sidebarOpen ? X : Menu" :size="18" />
                </button>

                <div class="flex-1" />

                <div class="flex items-center gap-2">
                    <button class="w-8 h-8 flex items-center justify-center rounded-lg hover:bg-gray-100 text-gray-500 transition-colors">
                        <Bell :size="16" />
                    </button>
                    <div class="text-[13px] font-500 text-gray-700 bg-amber-50 border border-amber-200 rounded-lg px-2.5 py-1">
                        Platform Admin
                    </div>
                </div>
            </header>

            <!-- Page content -->
            <main class="platform-content">
                <slot />
            </main>
        </div>

        <ToastContainer />
    </div>
</template>

<style scoped>
/* ── Platform Sidebar ──────────────────────────────────────────────── */
.platform-sidebar {
    width: 240px;
    flex-shrink: 0;
    background: #0D2B2A;
    display: flex;
    flex-direction: column;
    position: fixed;
    top: 0; left: 0; bottom: 0;
    z-index: 40;
    transition: transform 0.22s ease;
    overflow-y: auto;
}
@media (min-width: 1024px) {
    .platform-sidebar { transform: translateX(0) !important; position: sticky; }
}

.platform-sidebar-logo {
    display: flex;
    align-items: center;
    gap: 10px;
    padding: 1.25rem 1rem;
    border-bottom: 1px solid rgba(255,255,255,.07);
}
.platform-logo-mark {
    width: 34px; height: 34px;
    background: #1D9792;
    border-radius: 8px;
    display: flex; align-items: center; justify-content: center;
    color: #fff; flex-shrink: 0;
}

.platform-nav-item {
    display: flex; align-items: center; gap: 9px;
    margin: 1px 8px; padding: 8px 10px;
    border-radius: 7px;
    font-size: 13px; font-weight: 500;
    color: rgba(255,255,255,.6);
    text-decoration: none; cursor: pointer;
    border: none; background: transparent;
    transition: background .12s, color .12s;
    white-space: nowrap;
}
.platform-nav-item:hover { background: rgba(255,255,255,.06); color: #fff; }
.platform-nav-item.active { background: rgba(29,151,146,.25); color: #48BCB9; font-weight: 600; }

/* ── Platform Main ─────────────────────────────────────────────────── */
.platform-main {
    flex: 1;
    margin-left: 240px;
    display: flex;
    flex-direction: column;
    min-height: 100vh;
}
@media (max-width: 1023px) { .platform-main { margin-left: 0; } }

.platform-topbar {
    height: 56px;
    background: #fff;
    border-bottom: 1px solid #E5E7EB;
    display: flex; align-items: center;
    padding: 0 1.5rem; gap: .75rem;
    position: sticky; top: 0; z-index: 20;
}

.platform-content {
    flex: 1;
    padding: 1.5rem;
    max-width: 1400px;
    width: 100%;
}
</style>
