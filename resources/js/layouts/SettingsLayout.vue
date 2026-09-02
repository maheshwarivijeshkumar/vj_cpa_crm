<script setup lang="ts">
import { computed } from 'vue'
import { Link, usePage } from '@inertiajs/vue3'
import { User, Shield, Bell, CreditCard, Gift } from '@lucide/vue'
import AppLayout from '@/layouts/AppLayout.vue'

const page = usePage()
const currentUrl = computed(() => page.url)

function isActive(href: string): boolean {
    return currentUrl.value === href || currentUrl.value.startsWith(href + '?')
}

const tabs = [
    { href: '/settings/profile',       icon: User,       label: 'Profile'       },
    { href: '/settings/security',      icon: Shield,     label: 'Security'      },
    { href: '/settings/notifications', icon: Bell,       label: 'Notifications' },
]
</script>

<template>
    <AppLayout>
        <div class="max-w-4xl mx-auto space-y-6 px-4 sm:px-0">

            <!-- Page header -->
            <div>
                <h1 class="text-2xl font-semibold text-cpa-text-primary">Account Settings</h1>
                <p class="text-sm text-cpa-text-muted mt-1">Manage your profile, security and preferences</p>
            </div>

            <div class="flex flex-col md:flex-row gap-6">

                <!-- Sidebar tabs -->
                <nav class="md:w-48 flex-shrink-0" aria-label="Settings navigation">
                    <div class="bg-white border border-cpa-border rounded-xl shadow-sm overflow-hidden">
                        <Link
                            v-for="tab in tabs"
                            :key="tab.href"
                            :href="tab.href"
                            class="flex items-center gap-2.5 px-4 py-3 text-sm font-medium transition-colors border-b border-cpa-border last:border-b-0"
                            :class="isActive(tab.href)
                                ? 'bg-cpa-very-light text-cpa-medium-dark border-l-2 border-l-cpa-medium-dark'
                                : 'text-cpa-text-secondary hover:bg-cpa-bg hover:text-cpa-text-primary'"
                        >
                            <component :is="tab.icon" :size="15" class="flex-shrink-0" />
                            {{ tab.label }}
                        </Link>
                    </div>
                </nav>

                <!-- Content -->
                <div class="flex-1 min-w-0">
                    <slot />
                </div>
            </div>
        </div>
    </AppLayout>
</template>
