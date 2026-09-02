<script setup lang="ts">
import { ref, computed } from 'vue'
import { Link, router } from '@inertiajs/vue3'
import { Menu, Bell, Search, ChevronDown, Settings, LogOut, User } from '@lucide/vue'
import { useAuthStore } from '@/stores/auth'
import { useUiStore } from '@/stores/ui'

const auth = useAuthStore()
const ui   = useUiStore()

const userMenuOpen = ref(false)

function toggleUserMenu(): void { userMenuOpen.value = !userMenuOpen.value }
function closeUserMenu(): void  { userMenuOpen.value = false }

function logout(): void {
    closeUserMenu()
    router.post('/logout')
}
</script>

<template>
    <header class="app-topbar">
        <!-- Mobile hamburger -->
        <button
            class="topbar-icon-btn lg:hidden"
            aria-label="Toggle sidebar"
            @click="ui.toggleSidebar()"
        >
            <Menu :size="20" />
        </button>

        <!-- Breadcrumbs -->
        <nav v-if="ui.breadcrumbs.length" class="topbar-breadcrumb" aria-label="Breadcrumb">
            <template v-for="(crumb, i) in ui.breadcrumbs" :key="i">
                <span v-if="i > 0" class="topbar-breadcrumb-sep">/</span>
                <Link
                    v-if="crumb.href && i < ui.breadcrumbs.length - 1"
                    :href="crumb.href"
                    class="topbar-breadcrumb-item hover:text-cpa-medium-dark transition-colors"
                >
                    {{ crumb.label }}
                </Link>
                <span v-else class="topbar-breadcrumb-item current">{{ crumb.label }}</span>
            </template>
        </nav>
        <div v-else class="topbar-breadcrumb">
            <span class="topbar-breadcrumb-item current font-semibold text-cpa-text-primary text-[15px]">
                {{ ui.pageTitle }}
            </span>
        </div>

        <!-- Topbar actions -->
        <div class="topbar-actions">
            <!-- Search button (future) -->
            <button class="topbar-icon-btn hidden sm:flex" aria-label="Search">
                <Search :size="18" />
            </button>

            <!-- Notifications -->
            <button
                class="topbar-icon-btn relative"
                aria-label="Notifications"
                @click="ui.toggleNotifications()"
            >
                <Bell :size="18" />
                <!-- Unread dot — will be driven by store later -->
                <span class="topbar-badge" />
            </button>

            <!-- User menu -->
            <div class="relative">
                <button
                    class="topbar-user-btn"
                    aria-label="User menu"
                    @click="toggleUserMenu"
                >
                    <div class="topbar-avatar">
                        <img
                            v-if="auth.user?.avatar_path"
                            :src="auth.user.avatar_path"
                            :alt="auth.user?.name"
                        />
                        <span v-else>{{ auth.initials }}</span>
                    </div>
                    <span class="topbar-user-name hidden sm:block">
                        {{ auth.user?.first_name }}
                    </span>
                    <ChevronDown :size="14" class="text-cpa-text-muted hidden sm:block" />
                </button>

                <!-- Dropdown -->
                <Transition
                    enter-active-class="transition duration-100 ease-out"
                    enter-from-class="opacity-0 scale-95"
                    enter-to-class="opacity-100 scale-100"
                    leave-active-class="transition duration-75 ease-in"
                    leave-from-class="opacity-100 scale-100"
                    leave-to-class="opacity-0 scale-95"
                >
                    <div
                        v-if="userMenuOpen"
                        v-click-outside="closeUserMenu"
                        class="dropdown-menu right-0 top-full mt-1 w-52"
                        style="position: absolute;"
                    >
                        <!-- User info header -->
                        <div class="px-3 py-2 border-b border-cpa-border mb-1">
                            <p class="text-[13px] font-semibold text-cpa-text-primary truncate">
                                {{ auth.user?.name }}
                            </p>
                            <p class="text-[12px] text-cpa-text-muted truncate">
                                {{ auth.user?.email }}
                            </p>
                        </div>

                        <Link
                            href="/settings/profile"
                            class="dropdown-item"
                            @click="closeUserMenu"
                        >
                            <User :size="15" />
                            My Profile
                        </Link>

                        <Link
                            href="/settings"
                            class="dropdown-item"
                            @click="closeUserMenu"
                        >
                            <Settings :size="15" />
                            Settings
                        </Link>

                        <div class="dropdown-divider" />

                        <button class="dropdown-item danger w-full" @click="logout">
                            <LogOut :size="15" />
                            Sign out
                        </button>
                    </div>
                </Transition>
            </div>
        </div>
    </header>
</template>
