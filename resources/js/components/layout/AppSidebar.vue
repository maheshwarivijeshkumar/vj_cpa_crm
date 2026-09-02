<script setup lang="ts">
import { computed } from 'vue'
import { Link, usePage } from '@inertiajs/vue3'
import {
    LayoutDashboard, Users, Briefcase, FileText, BookOpen,
    Clock, Calendar, FolderOpen, FileSignature, Mail,
    Bell, Receipt, CreditCard, BarChart2, Building2,
    Settings, ShieldCheck, Workflow, CheckSquare, TrendingUp,
    ChevronRight, Landmark, PiggyBank, Package, Globe,
    Bot, Webhook, Download, Upload, LogOut,
} from '@lucide/vue'
import { useAuthStore } from '@/stores/auth'
import { useUiStore } from '@/stores/ui'
import { router } from '@inertiajs/vue3'

defineProps<{ open: boolean }>()

const auth = useAuthStore()
const ui   = useUiStore()
const page = usePage()

const currentUrl = computed(() => page.url)

function isActive(href: string): boolean {
    return currentUrl.value.startsWith(href)
}

function isExact(href: string): boolean {
    return currentUrl.value === href
}

function logout(): void {
    router.post('/logout')
}

// ── Navigation structure ───────────────────────────────────────────────────────
const navSections = computed(() => [
    {
        label: 'Overview',
        items: [
            { href: '/dashboard', label: 'Dashboard', icon: LayoutDashboard, permission: null },
        ],
    },
    {
        label: 'CRM',
        items: [
            { href: '/crm/leads',    label: 'Leads',    icon: TrendingUp,  permission: 'leads.viewAny' },
            { href: '/clients',      label: 'Clients',  icon: Users,       permission: 'clients.viewAny' },
            { href: '/contacts',     label: 'Contacts', icon: Users,       permission: 'contacts.viewAny' },
            { href: '/entities',     label: 'Entities', icon: Building2,   permission: 'entities.viewAny' },
            { href: '/services',     label: 'Services', icon: Package,     permission: 'services.viewAny' },
        ],
    },
    {
        label: 'Practice',
        items: [
            { href: '/engagements', label: 'Engagements', icon: Briefcase,   permission: 'engagements.viewAny' },
            { href: '/filings',     label: 'Filings',     icon: FileText,    permission: 'filings.viewAny' },
            { href: '/taxation',    label: 'Taxation',    icon: Globe,       permission: 'taxation.viewAny' },
            { href: '/deadlines',   label: 'Deadlines',   icon: Clock,       permission: 'deadlines.viewAny' },
            { href: '/workflows',   label: 'Workflows',   icon: Workflow,    permission: 'workflows.viewAny' },
            { href: '/tasks',       label: 'Tasks',       icon: CheckSquare, permission: 'tasks.viewAny' },
            { href: '/time',        label: 'Time',        icon: Clock,       permission: 'time.viewAny' },
            { href: '/calendar',    label: 'Calendar',    icon: Calendar,    permission: 'calendar.viewAny' },
        ],
    },
    {
        label: 'Documents',
        items: [
            { href: '/documents',   label: 'Documents',    icon: FolderOpen,     permission: 'documents.viewAny' },
            { href: '/signatures',  label: 'E-Signatures', icon: FileSignature,  permission: 'esignatures.viewAny' },
            { href: '/proposals',   label: 'Proposals',    icon: BookOpen,       permission: 'proposals.viewAny' },
            { href: '/templates',   label: 'Templates',    icon: FileText,       permission: 'templates.viewAny' },
        ],
    },
    {
        label: 'Communications',
        items: [
            { href: '/communications', label: 'Messages',       icon: Mail,       permission: 'communications.viewAny' },
            { href: '/notifications',  label: 'Notifications',  icon: Bell,       permission: 'notifications.viewAny' },
        ],
    },
    {
        label: 'Finance',
        items: [
            { href: '/accounting',  label: 'Accounting',  icon: BookOpen,    permission: 'accounting.viewAny' },
            { href: '/banking',     label: 'Banking',     icon: Landmark,    permission: 'banking.viewAny' },
            { href: '/invoicing',   label: 'Invoices',    icon: Receipt,     permission: 'invoicing.viewAny' },
            { href: '/payments',    label: 'Payments',    icon: CreditCard,  permission: 'payments.viewAny' },
            { href: '/expenses',    label: 'Expenses',    icon: PiggyBank,   permission: 'expenses.viewAny' },
        ],
    },
    {
        label: 'Analytics',
        items: [
            { href: '/reports',  label: 'Reports',  icon: BarChart2, permission: 'reports.viewAny' },
            { href: '/imports',  label: 'Imports',  icon: Upload,    permission: 'imports.view' },
            { href: '/exports',  label: 'Exports',  icon: Download,  permission: 'exports.view' },
            { href: '/webhooks', label: 'Webhooks', icon: Webhook,   permission: 'webhooks.viewAny' },
            { href: '/ai',       label: 'AI',       icon: Bot,       permission: 'ai.use' },
        ],
    },
    {
        label: 'Administration',
        items: [
            { href: '/settings',         label: 'Settings',    icon: Settings,    permission: 'settings.view' },
            { href: '/settings/users',   label: 'Users',       icon: Users,       permission: 'users.viewAny' },
            { href: '/settings/roles',   label: 'Roles',       icon: ShieldCheck, permission: 'roles.viewAny' },
            { href: '/settings/offices', label: 'Offices',     icon: Building2,   permission: 'offices.viewAny' },
        ],
    },
])

// Filter sections based on user permissions
const visibleSections = computed(() =>
    navSections.value
        .map((section) => ({
            ...section,
            items: section.items.filter(
                (item) => !item.permission || auth.can(item.permission),
            ),
        }))
        .filter((section) => section.items.length > 0),
)
</script>

<template>
    <aside class="app-sidebar" :class="{ open }">
        <!-- Logo -->
        <Link href="/dashboard" class="sidebar-logo">
            <div class="sidebar-logo-mark">CPA</div>
            <div>
                <div class="sidebar-logo-text">VJ CPA CRM</div>
                <div class="sidebar-logo-sub">Practice Management</div>
            </div>
        </Link>

        <!-- Navigation -->
        <nav class="sidebar-nav">
            <template v-for="section in visibleSections" :key="section.label">
                <p class="sidebar-section-label">{{ section.label }}</p>

                <Link
                    v-for="item in section.items"
                    :key="item.href"
                    :href="item.href"
                    class="sidebar-nav-item"
                    :class="{ active: isActive(item.href) }"
                    @click="ui.closeSidebar()"
                >
                    <component :is="item.icon" class="nav-icon" :size="18" />
                    {{ item.label }}
                </Link>
            </template>
        </nav>

        <!-- Bottom user / logout -->
        <div class="mt-auto border-t border-white/10 p-3">
            <div v-if="auth.user" class="flex items-center gap-2.5 px-2 py-1.5 mb-1">
                <div class="topbar-avatar flex-shrink-0 !w-7 !h-7 !text-[11px]">
                    <img v-if="auth.user.avatar_path" :src="auth.user.avatar_path" :alt="auth.user.name" />
                    <span v-else>{{ auth.initials }}</span>
                </div>
                <div class="min-w-0 flex-1">
                    <p class="text-white text-[12px] font-medium truncate">{{ auth.user.name }}</p>
                    <p class="text-white/40 text-[11px] truncate">{{ auth.user.email }}</p>
                </div>
            </div>

            <!-- Settings shortcut -->
            <Link
                href="/settings"
                class="sidebar-nav-item"
                :class="{ active: isExact('/settings') }"
                @click="ui.closeSidebar()"
            >
                <Settings class="nav-icon" :size="18" />
                Settings
            </Link>

            <!-- Logout -->
            <button
                class="sidebar-nav-item w-full text-left"
                @click="logout"
            >
                <LogOut class="nav-icon" :size="18" />
                Sign out
            </button>
        </div>
    </aside>
</template>
