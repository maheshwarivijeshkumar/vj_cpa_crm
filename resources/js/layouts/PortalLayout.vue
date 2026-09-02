<script setup lang="ts">
import { ref, computed } from 'vue'
import { Link, usePage, router } from '@inertiajs/vue3'
import {
    LayoutDashboard, CreditCard, Users, Gift,
    Bell, LogOut, Menu, X, Settings, ChevronRight,
} from '@lucide/vue'
import { useAuthStore } from '@/stores/auth'
import { useFlash } from '@/composables/useFlash'
import ToastContainer from '@/components/ui/ToastContainer.vue'

useFlash()

const auth = useAuthStore()
const page = usePage()

const currentUrl = computed(() => page.url)

function isActive(href: string): boolean {
    if (href === '/portal') return currentUrl.value === '/portal'
    return currentUrl.value === href || currentUrl.value.startsWith(href + '/')
}

const sidebarOpen = ref(
    typeof window !== 'undefined' ? window.innerWidth >= 1024 : true
)
function closeSidebarOnMobile() {
    if (typeof window !== 'undefined' && window.innerWidth < 1024) {
        sidebarOpen.value = false
    }
}
function logout() { router.post('/logout') }

const navItems = [
    { href: '/portal',              icon: LayoutDashboard, label: 'Dashboard'    },
    { href: '/portal/subscription', icon: CreditCard,      label: 'Subscription' },
    { href: '/portal/referrals',    icon: Gift,            label: 'Referrals'    },
    { href: '/portal/profile',      icon: Settings,        label: 'Profile'      },
]
</script>

<template>
    <div class="flex min-h-screen bg-cpa-bg">

        <!-- Mobile overlay -->
        <Transition name="fade">
            <div v-if="sidebarOpen" class="fixed inset-0 bg-black/50 z-30 lg:hidden" @click="sidebarOpen = false" />
        </Transition>

        <!-- Sidebar -->
        <aside
            class="portal-sidebar"
            :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'"
        >
            <!-- Logo + tenant name -->
            <div class="portal-logo">
                <div class="portal-logo-mark">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none">
                        <path d="M9 5H7a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2V7a2 2 0 0 0-2-2h-2" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                        <rect x="9" y="3" width="6" height="4" rx="1" stroke="currentColor" stroke-width="2"/>
                    </svg>
                </div>
                <div class="min-w-0">
                    <p class="text-white text-[13px] font-semibold leading-tight">VJ CPA CRM</p>
                    <p class="text-white/40 text-[10px] font-medium tracking-wider mt-0.5 truncate">
                        {{ (auth.user as any)?.tenant?.name ?? 'Client Portal' }}
                    </p>
                </div>
            </div>

            <!-- Nav -->
            <nav class="flex-1 overflow-y-auto py-3" role="navigation">
                <Link
                    v-for="item in navItems"
                    :key="item.href"
                    :href="item.href"
                    class="portal-nav-link"
                    :class="{ 'portal-nav-link--active': isActive(item.href) }"
                    @click="closeSidebarOnMobile"
                >
                    <component :is="item.icon" :size="16" class="flex-shrink-0" />
                    <span class="truncate">{{ item.label }}</span>
                    <ChevronRight v-if="isActive(item.href)" :size="12" class="ml-auto flex-shrink-0 text-cpa-medium" />
                </Link>
            </nav>

            <!-- User + logout -->
            <div class="border-t border-white/[0.07] p-3 space-y-0.5">
                <div class="flex items-center gap-2.5 px-2 py-2">
                    <div class="w-7 h-7 rounded-full bg-cpa-medium flex items-center justify-center text-white text-[11px] font-bold flex-shrink-0 select-none">
                        {{ auth.initials }}
                    </div>
                    <div class="min-w-0">
                        <p class="text-white text-[12px] font-medium truncate">{{ auth.user?.name }}</p>
                        <p class="text-white/35 text-[10px] truncate">{{ auth.user?.email }}</p>
                    </div>
                </div>
                <button class="portal-nav-link w-full text-left text-white/50 hover:text-white/80" @click="logout">
                    <LogOut :size="15" class="flex-shrink-0" />
                    <span class="text-[12px]">Sign out</span>
                </button>
            </div>
        </aside>

        <!-- Main -->
        <div class="portal-main">
            <!-- Topbar -->
            <header class="portal-topbar">
                <button class="topbar-icon-btn lg:hidden" type="button" @click="sidebarOpen = !sidebarOpen" aria-label="Toggle sidebar">
                    <component :is="sidebarOpen ? X : Menu" :size="18" />
                </button>
                <div class="flex-1 min-w-0"><slot name="header" /></div>
                <div class="flex items-center gap-1.5">
                    <button class="topbar-icon-btn relative" aria-label="Notifications">
                        <Bell :size="17" />
                    </button>
                </div>
            </header>

            <main class="portal-content">
                <slot />
            </main>
        </div>

        <ToastContainer />
    </div>
</template>

<style scoped>
.portal-sidebar {
    width: 220px; flex-shrink: 0;
    background: #055E5A;
    display: flex; flex-direction: column;
    position: fixed; inset-block: 0; left: 0;
    z-index: 40; transition: transform 0.2s ease; overflow: hidden;
}
@media (min-width: 1024px) {
    .portal-sidebar { position: sticky; top: 0; height: 100vh; transform: translateX(0) !important; }
}
.portal-logo {
    display: flex; align-items: center; gap: 10px;
    padding: 1.125rem 1rem;
    border-bottom: 1px solid rgba(255,255,255,.08);
    flex-shrink: 0;
}
.portal-logo-mark {
    width: 32px; height: 32px; background: #1D9792;
    border-radius: 7px; display: flex; align-items: center; justify-content: center;
    color: #fff; flex-shrink: 0;
}
.portal-nav-link {
    display: flex; align-items: center; gap: 9px;
    margin: 0 8px; padding: 7px 10px; border-radius: 7px;
    font-size: 13px; font-weight: 500; color: rgba(255,255,255,.6);
    text-decoration: none; cursor: pointer; border: none; background: transparent;
    transition: background .12s ease, color .12s ease;
    white-space: nowrap; width: calc(100% - 16px);
}
.portal-nav-link:hover { background: rgba(255,255,255,.07); color: rgba(255,255,255,.9); }
.portal-nav-link--active { background: rgba(255,255,255,.12); color: #fff; font-weight: 600; }
.portal-main {
    flex: 1; min-width: 0; display: flex; flex-direction: column;
    min-height: 100vh; margin-left: 220px;
}
@media (max-width: 1023px) { .portal-main { margin-left: 0; } }
.portal-topbar {
    height: 54px; background: #fff; border-bottom: 1px solid #E5E7EB;
    display: flex; align-items: center; padding: 0 1.25rem; gap: .75rem;
    position: sticky; top: 0; z-index: 20; flex-shrink: 0;
}
.topbar-icon-btn {
    width: 34px; height: 34px;
    display: flex; align-items: center; justify-content: center;
    border-radius: 8px; border: none; background: transparent; cursor: pointer;
    color: #6B7280; transition: background .12s, color .12s; flex-shrink: 0;
}
.topbar-icon-btn:hover { background: #F3F4F6; color: #0D2B2A; }
.portal-content { flex: 1; padding: 1.5rem; max-width: 1200px; width: 100%; }
.fade-enter-active, .fade-leave-active { transition: opacity .2s ease; }
.fade-enter-from, .fade-leave-to { opacity: 0; }
</style>
