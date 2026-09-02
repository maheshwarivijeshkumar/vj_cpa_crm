<script setup lang="ts">
import { ref, computed } from 'vue'
import { Link, usePage, router } from '@inertiajs/vue3'
import {
    LayoutDashboard, Building2, Users, Settings,
    Bell, LogOut, Menu, X,
    Activity, Flag, BookOpen, ShieldAlert,
    Globe, ChevronRight,
} from '@lucide/vue'
import { useAuthStore } from '@/stores/auth'
import { useFlash } from '@/composables/useFlash'
import ToastContainer from '@/components/ui/ToastContainer.vue'

useFlash()

const auth = useAuthStore()
const page = usePage()

// ── Active route detection ─────────────────────────────────────────────────
const currentUrl = computed(() => page.url)

/**
 * Exact match for /platform dashboard, prefix match for everything else.
 * Prevents /platform from being active when on /platform/tenants etc.
 */
function isActive(href: string): boolean {
    if (href === '/platform') return currentUrl.value === '/platform'
    return currentUrl.value === href || currentUrl.value.startsWith(href + '/')
}

// ── Sidebar state ──────────────────────────────────────────────────────────
const sidebarOpen = ref(
    typeof window !== 'undefined' ? window.innerWidth >= 1024 : true
)
function toggleSidebar() { sidebarOpen.value = !sidebarOpen.value }
function closeSidebarOnMobile() {
    if (typeof window !== 'undefined' && window.innerWidth < 1024) {
        sidebarOpen.value = false
    }
}

function logout() { router.post('/logout') }

// ── Navigation groups ──────────────────────────────────────────────────────
const navGroups = [
    {
        label: null,
        items: [
            { href: '/platform',       icon: LayoutDashboard, label: 'Dashboard' },
        ],
    },
    {
        label: 'Management',
        items: [
            { href: '/platform/tenants', icon: Building2, label: 'Tenants' },
            { href: '/platform/users',   icon: Users,     label: 'Users'   },
        ],
    },
    {
        label: 'Content',
        items: [
            { href: '/platform/blog',          icon: BookOpen, label: 'Blog'           },
            { href: '/platform/notifications', icon: Bell,     label: 'Notifications'  },
        ],
    },
    {
        label: 'Security',
        items: [
            { href: '/platform/feature-flags',  icon: Flag,         label: 'Feature Flags'  },
            { href: '/platform/audit-logs',     icon: Activity,     label: 'Audit Logs'     },
            { href: '/platform/login-attempts', icon: ShieldAlert,  label: 'Login Attempts' },
        ],
    },
    {
        label: 'System',
        items: [
            { href: '/platform/settings', icon: Settings, label: 'Settings' },
        ],
    },
]
</script>

<template>
    <div class="flex min-h-screen bg-cpa-bg">

        <!-- Mobile overlay -->
        <Transition name="fade">
            <div
                v-if="sidebarOpen"
                class="fixed inset-0 bg-black/50 z-30 lg:hidden"
                @click="sidebarOpen = false"
            />
        </Transition>

        <!-- ── Sidebar ────────────────────────────────────────────────────── -->
        <aside
            class="sidebar"
            :class="sidebarOpen ? 'sidebar--open' : 'sidebar--closed'"
            aria-label="Platform navigation"
        >
            <!-- Logo lockup -->
            <div class="sidebar-logo">
                <div class="sidebar-logo-mark">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none">
                        <path d="M9 5H7a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2V7a2 2 0 0 0-2-2h-2" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                        <rect x="9" y="3" width="6" height="4" rx="1" stroke="currentColor" stroke-width="2"/>
                        <path d="M9 12h6M9 16h4" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                    </svg>
                </div>
                <div class="min-w-0">
                    <p class="text-white text-[13px] font-semibold leading-tight tracking-tight">VJ CPA CRM</p>
                    <p class="text-white/40 text-[10px] font-semibold tracking-widest uppercase mt-0.5">Platform Admin</p>
                </div>
            </div>

            <!-- Nav groups -->
            <nav class="flex-1 overflow-y-auto py-3 space-y-1" role="navigation">
                <template v-for="group in navGroups" :key="group.label ?? 'root'">
                    <!-- Section label -->
                    <p
                        v-if="group.label"
                        class="px-4 pt-3 pb-1 text-[10px] font-semibold text-white/30 uppercase tracking-widest select-none"
                    >
                        {{ group.label }}
                    </p>

                    <!-- Nav links -->
                    <Link
                        v-for="item in group.items"
                        :key="item.href"
                        :href="item.href"
                        class="nav-link"
                        :class="{ 'nav-link--active': isActive(item.href) }"
                        @click="closeSidebarOnMobile"
                    >
                        <component :is="item.icon" :size="16" class="flex-shrink-0" />
                        <span class="truncate">{{ item.label }}</span>
                        <ChevronRight
                            v-if="isActive(item.href)"
                            :size="12"
                            class="ml-auto flex-shrink-0 text-cpa-medium"
                        />
                    </Link>
                </template>
            </nav>

            <!-- Bottom: quick links + user -->
            <div class="border-t border-white/[0.07] pt-2 pb-3 px-2 space-y-0.5">
                <!-- Back to marketing site -->
                <Link
                    href="/"
                    class="nav-link text-white/40 hover:text-white/70"
                    target="_blank"
                >
                    <Globe :size="15" class="flex-shrink-0" />
                    <span class="truncate text-[12px]">Marketing Site</span>
                </Link>

                <!-- User card -->
                <div class="flex items-center gap-2.5 px-2 py-2 mt-1 rounded-lg">
                    <div
                        class="w-7 h-7 rounded-full bg-cpa-medium flex items-center justify-center
                               text-white text-[11px] font-bold flex-shrink-0 select-none"
                        aria-hidden="true"
                    >
                        {{ auth.initials }}
                    </div>
                    <div class="min-w-0 flex-1">
                        <p class="text-white text-[12px] font-medium truncate">{{ auth.user?.name }}</p>
                        <p class="text-white/35 text-[10px] truncate">{{ auth.user?.email }}</p>
                    </div>
                </div>

                <!-- Logout -->
                <button
                    class="nav-link w-full text-left text-white/50 hover:text-white/80 hover:bg-white/[0.04]"
                    type="button"
                    @click="logout"
                >
                    <LogOut :size="15" class="flex-shrink-0" />
                    <span class="text-[12px]">Sign out</span>
                </button>
            </div>
        </aside>

        <!-- ── Main content area ──────────────────────────────────────────── -->
        <div class="main-wrapper">

            <!-- Topbar -->
            <header class="topbar" role="banner">
                <!-- Mobile: hamburger -->
                <button
                    class="topbar-icon-btn lg:hidden"
                    type="button"
                    aria-label="Toggle sidebar"
                    @click="toggleSidebar"
                >
                    <component :is="sidebarOpen ? X : Menu" :size="18" />
                </button>

                <!-- Breadcrumb / page title via slot -->
                <div class="flex-1 min-w-0">
                    <slot name="header" />
                </div>

                <!-- Right actions -->
                <div class="flex items-center gap-1.5">
                    <!-- Notifications bell -->
                    <button
                        class="topbar-icon-btn relative"
                        type="button"
                        aria-label="Notifications"
                    >
                        <Bell :size="17" />
                        <!-- Unread badge — shown when there are unread items -->
                        <span
                            class="absolute top-1 right-1 w-1.5 h-1.5 bg-cpa-danger rounded-full"
                            aria-hidden="true"
                        />
                    </button>

                    <!-- Role badge -->
                    <span class="hidden sm:flex items-center gap-1.5 text-[11px] font-semibold
                                 text-amber-700 bg-amber-50 border border-amber-200
                                 rounded-lg px-2.5 py-1 select-none">
                        <ShieldAlert :size="12" />
                        Platform Admin
                    </span>
                </div>
            </header>

            <!-- Page slot -->
            <main class="page-content">
                <slot />
            </main>
        </div>

        <ToastContainer />
    </div>
</template>

<style scoped>
/* ── Sidebar ───────────────────────────────────────────────────────────── */
.sidebar {
    width: 232px;
    flex-shrink: 0;
    background: #0D2B2A;
    display: flex;
    flex-direction: column;
    position: fixed;
    inset-block: 0;
    left: 0;
    z-index: 40;
    transition: transform 0.2s ease;
    overflow: hidden;
}

/* On desktop: sticky in-flow element (not fixed) */
@media (min-width: 1024px) {
    .sidebar {
        position: sticky;
        top: 0;
        height: 100vh;
        transform: translateX(0) !important;
    }
}

.sidebar--open  { transform: translateX(0); }
.sidebar--closed { transform: translateX(-100%); }

/* Logo lockup */
.sidebar-logo {
    display: flex;
    align-items: center;
    gap: 10px;
    padding: 1.125rem 1rem;
    border-bottom: 1px solid rgba(255,255,255,.06);
    flex-shrink: 0;
}
.sidebar-logo-mark {
    width: 34px; height: 34px;
    background: #1D9792;
    border-radius: 8px;
    display: flex; align-items: center; justify-content: center;
    color: #fff; flex-shrink: 0;
}

/* Nav link base */
.nav-link {
    display: flex;
    align-items: center;
    gap: 9px;
    margin: 0 8px;
    padding: 7px 10px;
    border-radius: 7px;
    font-size: 13px;
    font-weight: 500;
    color: rgba(255, 255, 255, 0.58);
    text-decoration: none;
    cursor: pointer;
    border: none;
    background: transparent;
    transition: background 0.12s ease, color 0.12s ease;
    white-space: nowrap;
    width: calc(100% - 16px);
}
.nav-link:hover {
    background: rgba(255,255,255,.06);
    color: rgba(255,255,255,.9);
}
.nav-link--active {
    background: rgba(29,151,146,.2);
    color: #48BCB9;
    font-weight: 600;
}
.nav-link--active:hover {
    background: rgba(29,151,146,.28);
    color: #8CD3CF;
}

/* ── Main wrapper ──────────────────────────────────────────────────────── */
.main-wrapper {
    flex: 1;
    min-width: 0;
    display: flex;
    flex-direction: column;
    min-height: 100vh;
    margin-left: 232px;
}
@media (max-width: 1023px) {
    .main-wrapper { margin-left: 0; }
}

/* Topbar */
.topbar {
    height: 54px;
    background: #fff;
    border-bottom: 1px solid #E5E7EB;
    display: flex;
    align-items: center;
    padding: 0 1.25rem;
    gap: 0.75rem;
    position: sticky;
    top: 0;
    z-index: 20;
    flex-shrink: 0;
}

.topbar-icon-btn {
    width: 34px; height: 34px;
    display: flex; align-items: center; justify-content: center;
    border-radius: 8px;
    border: none; background: transparent; cursor: pointer;
    color: #6B7280;
    transition: background .12s, color .12s;
    flex-shrink: 0;
}
.topbar-icon-btn:hover { background: #F3F4F6; color: #0D2B2A; }

/* Page content */
.page-content {
    flex: 1;
    padding: 1.5rem;
    max-width: 1440px;
    width: 100%;
}

/* Transition for mobile overlay */
.fade-enter-active,
.fade-leave-active { transition: opacity 0.2s ease; }
.fade-enter-from,
.fade-leave-to { opacity: 0; }
</style>
