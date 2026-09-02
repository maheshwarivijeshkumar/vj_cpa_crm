<script setup lang="ts">
import { ref, computed } from 'vue'
import { Link, router, usePage } from '@inertiajs/vue3'
import {
    Search, Bell, MessageSquare, Settings, LogOut,
    User, Plus, CalendarCheck, Users, ClipboardList,
    ChevronDown, X,
} from '@lucide/vue'
import { useAuthStore } from '@/stores/auth'
import { useUiStore } from '@/stores/ui'

const auth = useAuthStore()
const ui   = useUiStore()
const page = usePage()

// ── Active link detection ─────────────────────────────────────────────────────
const currentUrl = computed(() => page.url)
function isActive(href: string): boolean {
    if (href === '/dashboard') return currentUrl.value === '/dashboard'
    return currentUrl.value.startsWith(href)
}

// ── Navigation items (matches screenshot order) ───────────────────────────────
const navLinks = computed(() => [
    { href: '/dashboard',       label: 'Dashboard',     permission: null },
    { href: '/clients',         label: 'Clients',       permission: 'clients.viewAny' },
    { href: '/filings',         label: 'T1 Season',     permission: 'filings.viewAny' },
    { href: '/deadlines',       label: 'Reminders',     permission: 'deadlines.viewAny' },
    { href: '/documents',       label: 'Documents',     permission: 'documents.viewAny' },
    { href: '/tasks',           label: 'Files',         permission: 'tasks.viewAny' },
    { href: '/communications',  label: 'Inbox',         permission: 'communications.viewAny', badge: true },
    { href: '/messages',        label: 'Messages',      permission: 'communications.viewAny' },
    { href: '/reports',         label: 'Reports',       permission: 'reports.viewAny' },
    { href: '/workflows',       label: 'Workflow',      permission: 'workflows.viewAny' },
    { href: '/walk-ins',        label: 'Walk-ins',      permission: 'tasks.viewAny' },
    { href: '/calendar',        label: 'Appointments',  permission: 'calendar.viewAny' },
    { href: '/settings',        label: 'Settings',      permission: 'settings.view' },
])

const visibleLinks = computed(() =>
    navLinks.value.filter(l => !l.permission || auth.can(l.permission))
)

// ── + New dropdown ────────────────────────────────────────────────────────────
const newMenuOpen = ref(false)
const newMenuItems = [
    { label: 'Reminder',   icon: CalendarCheck, href: '/deadlines/create' },
    { label: 'Client',     icon: Users,         href: '/clients/create' },
    { label: 'Walk-in',    icon: User,          href: '/walk-ins/create' },
    { label: 'Task',       icon: ClipboardList, href: '/tasks/create' },
]
function toggleNewMenu() { newMenuOpen.value = !newMenuOpen.value }
function closeNewMenu()  { newMenuOpen.value = false }

// ── User dropdown ─────────────────────────────────────────────────────────────
const userMenuOpen = ref(false)
function toggleUserMenu() { userMenuOpen.value = !userMenuOpen.value }
function closeUserMenu()  { userMenuOpen.value = false }

function logout() {
    closeUserMenu()
    router.post('/logout')
}

// ── Search ────────────────────────────────────────────────────────────────────
const searchQuery = ref('')
const searchOpen  = ref(false)

// ── Notification count (placeholder until notifications module built) ─────────
const notifCount = ref(0)
const msgCount   = ref(0)
</script>

<template>
    <header class="app-navbar">
        <div class="navbar-inner">

            <!-- ── Logo ───────────────────────────────────────────────────── -->
            <Link href="/dashboard" class="navbar-logo">
                <div class="navbar-logo-mark">
                    <span>{{ auth.tenant?.name?.charAt(0) ?? 'C' }}</span>
                </div>
                <span class="navbar-logo-name">
                    {{ auth.tenant?.name ?? 'CPA CRM' }}
                </span>
            </Link>

            <!-- ── Nav links ──────────────────────────────────────────────── -->
            <nav class="navbar-links" aria-label="Main navigation">
                <Link
                    v-for="link in visibleLinks"
                    :key="link.href"
                    :href="link.href"
                    class="navbar-link"
                    :class="{ active: isActive(link.href) }"
                >
                    {{ link.label }}
                    <!-- Inbox badge -->
                    <span v-if="link.badge && notifCount > 0" class="navbar-link-badge">
                        {{ notifCount }}
                    </span>
                </Link>
            </nav>

            <!-- ── Right actions ──────────────────────────────────────────── -->
            <div class="navbar-actions">

                <!-- Search -->
                <button
                    class="navbar-icon-btn"
                    aria-label="Search"
                    @click="searchOpen = !searchOpen"
                >
                    <Search :size="17" />
                </button>

                <!-- Notifications bell -->
                <button
                    class="navbar-icon-btn relative"
                    aria-label="Notifications"
                    @click="ui.toggleNotifications()"
                >
                    <Bell :size="17" />
                    <span v-if="notifCount > 0" class="navbar-notif-dot" />
                </button>

                <!-- Messages -->
                <button
                    class="navbar-icon-btn relative"
                    aria-label="Messages"
                >
                    <MessageSquare :size="17" />
                    <span v-if="msgCount > 0" class="navbar-notif-dot" />
                </button>

                <!-- + New button -->
                <div class="relative">
                    <button
                        class="navbar-new-btn"
                        aria-label="Create new"
                        @click="toggleNewMenu"
                    >
                        <Plus :size="15" />
                        New
                        <ChevronDown :size="13" />
                    </button>

                    <Transition
                        enter-active-class="transition duration-100 ease-out"
                        enter-from-class="opacity-0 translate-y-1"
                        enter-to-class="opacity-100 translate-y-0"
                        leave-active-class="transition duration-75 ease-in"
                        leave-from-class="opacity-100"
                        leave-to-class="opacity-0"
                    >
                        <div
                            v-if="newMenuOpen"
                            class="dropdown-menu right-0 top-full mt-1.5 w-44"
                            style="position: absolute;"
                            @mouseleave="closeNewMenu"
                        >
                            <Link
                                v-for="item in newMenuItems"
                                :key="item.label"
                                :href="item.href"
                                class="dropdown-item"
                                @click="closeNewMenu"
                            >
                                <component :is="item.icon" :size="14" class="text-cpa-text-muted" />
                                {{ item.label }}
                            </Link>
                        </div>
                    </Transition>
                </div>

                <!-- User menu -->
                <div class="relative">
                    <button
                        class="navbar-user-btn"
                        aria-label="User menu"
                        @click="toggleUserMenu"
                    >
                        <div class="navbar-avatar">
                            <img
                                v-if="auth.user?.avatar_path"
                                :src="auth.user.avatar_path"
                                :alt="auth.user.name"
                                class="w-full h-full object-cover"
                            />
                            <span v-else>{{ auth.initials }}</span>
                        </div>
                        <div class="navbar-user-info hidden md:block">
                            <span class="navbar-user-name">{{ auth.user?.first_name }} {{ auth.user?.last_name }}</span>
                            <span class="navbar-user-role">{{ auth.user?.user_type === 'platform_admin' ? 'Admin' : (auth.roles[0] ?? '') }}</span>
                        </div>
                        <ChevronDown :size="13" class="text-cpa-text-muted" />
                    </button>

                    <Transition
                        enter-active-class="transition duration-100 ease-out"
                        enter-from-class="opacity-0 translate-y-1"
                        enter-to-class="opacity-100 translate-y-0"
                        leave-active-class="transition duration-75 ease-in"
                        leave-from-class="opacity-100"
                        leave-to-class="opacity-0"
                    >
                        <div
                            v-if="userMenuOpen"
                            class="dropdown-menu right-0 top-full mt-1.5 w-56"
                            style="position: absolute;"
                            @mouseleave="closeUserMenu"
                        >
                            <!-- User header -->
                            <div class="px-3 py-2.5 border-b border-cpa-border">
                                <p class="text-[13px] font-semibold text-cpa-text-primary truncate">{{ auth.user?.name }}</p>
                                <p class="text-[11.5px] text-cpa-text-muted truncate mt-0.5">{{ auth.user?.email }}</p>
                            </div>

                            <Link href="/settings/profile" class="dropdown-item" @click="closeUserMenu">
                                <User :size="14" />
                                My Profile
                            </Link>
                            <Link href="/settings" class="dropdown-item" @click="closeUserMenu">
                                <Settings :size="14" />
                                Settings
                            </Link>
                            <div class="dropdown-divider" />
                            <button class="dropdown-item danger w-full" @click="logout">
                                <LogOut :size="14" />
                                Sign out
                            </button>
                        </div>
                    </Transition>
                </div>
            </div>
        </div>

        <!-- ── Inline search bar (expands below navbar) ───────────────────── -->
        <Transition
            enter-active-class="transition duration-150 ease-out"
            enter-from-class="opacity-0 -translate-y-2"
            enter-to-class="opacity-100 translate-y-0"
            leave-active-class="transition duration-100 ease-in"
            leave-from-class="opacity-100"
            leave-to-class="opacity-0"
        >
            <div v-if="searchOpen" class="navbar-search-bar">
                <div class="navbar-search-inner">
                    <Search :size="15" class="text-cpa-text-muted flex-shrink-0" />
                    <input
                        v-model="searchQuery"
                        type="text"
                        placeholder="Search clients, filings, tasks..."
                        class="navbar-search-input"
                        autofocus
                        @keydown.escape="searchOpen = false"
                    />
                    <button class="navbar-icon-btn" @click="searchOpen = false">
                        <X :size="15" />
                    </button>
                </div>
            </div>
        </Transition>
    </header>
</template>
