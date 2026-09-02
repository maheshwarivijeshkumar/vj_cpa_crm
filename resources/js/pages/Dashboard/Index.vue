<script setup lang="ts">
import { ref, computed } from 'vue'
import { Link } from '@inertiajs/vue3'
import {
    AlertTriangle, Activity, Briefcase, Users, Clock,
    TrendingUp, FileText, Bell, Plus, RefreshCw,
    ChevronRight, LayoutDashboard, Maximize2,
} from '@lucide/vue'
import { useAuthStore } from '@/stores/auth'
import { useUiStore } from '@/stores/ui'
import StatusBadge from '@/components/ui/StatusBadge.vue'

defineOptions({ layout: undefined }) // uses AppLayout from app.ts default

const auth = useAuthStore()
const ui   = useUiStore()

ui.setPageTitle('Dashboard')
ui.setBreadcrumbs([{ label: 'Dashboard' }])

// ── Urgency Overview (placeholder until real data) ────────────────────────────
const urgency = ref({
    total:     1412,
    overdue:   1228,
    thisMonth:  156,
    nextMonth:   17,
    upcoming:    11,
})

// ── Critical Alerts ───────────────────────────────────────────────────────────
const alerts = ref([
    {
        id: 1, name: 'ABC Holdings Inc.', count: 3,
        filings: [
            { label: 'GST/HST (Annual 2025) - 2816', status: 'overdue', days: 3 },
            { label: 'T3 (Annual 2025) - 2884', status: 'overdue', days: 5 },
        ],
    },
    {
        id: 2, name: 'Abbotsford Agricultural Supply', count: 4,
        filings: [
            { label: 'GST/HST (Q1 2026) - 2990', status: 'overdue', days: 1 },
            { label: 'Corp T2 (2026) - 2991', status: 'overdue', days: 2 },
            { label: 'GST/HST (Q2 2026) - 3002', status: 'overdue', days: 4 },
        ],
    },
    {
        id: 3, name: 'Alberta Grain Elevators Inc.', count: 2,
        filings: [
            { label: 'Payroll (Q1 2026) - 3150', status: 'overdue', days: 2 },
            { label: 'Payroll (Q2 2026) - 3160', status: 'overdue', days: 3 },
        ],
    },
    {
        id: 4, name: 'Bear Creek Industrial Park', count: 2,
        filings: [
            { label: 'Payroll (Q3 2026) - 1900', status: 'overdue', days: 1 },
            { label: 'EHT January (2026) - 884', status: 'overdue', days: 1 },
        ],
    },
])

// ── Recent Activity ───────────────────────────────────────────────────────────
const recentActivity = ref({
    received: 10, review: 8, sentToday: 0, awaiting: 4,
    documents: [
        { client: 'YOO Technologies Inc.', name: 'PaySummary - 2026.pdf', daysAgo: 2, isNew: true },
        { client: 'YOO Technologies Inc.', name: 'ExpenseSummary - 2025.pdf', daysAgo: 2, isNew: true },
        { client: 'Demo Inc.', name: 'Healthy Bites For Growing Minds Workshop.pdf', daysAgo: 3, status: 'approved' },
    ],
    reminders: [
        { subject: 'Campaign', client: 'Sidhu Group Inc.', daysAgo: 1, status: 'unsent' },
    ],
})

// ── Work Queue ────────────────────────────────────────────────────────────────
const workQueue = ref([
    { type: 'GST/NST', period: 'Annual 2021', name: 'ABC Holdings Inc.', deadline: 'Feb 28 2025', due: 'May 28 2025', assignee: 'Gagan Kambo', late: 3610 },
    { type: 'T2 Trust', period: 'Annual 2025', name: 'ABC Holdings Inc.', deadline: 'Apr 30 2025', due: 'Jul 29 2025', assignee: 'Gagan Kambo', late: 2990 },
    { type: 'GST/NST', period: 'Q1 2026', name: 'Abbotsford Agricultural Supply', deadline: 'Jun 30 2025', due: 'Jul 30 2025', late: 2380 },
    { type: 'Payroll', period: 'Q2 2026', name: 'Alberta Grain Elevators Inc.', deadline: 'Aug 31 2025', due: 'Sep 15 2025', late: 2310 },
])

const queueSearch = ref('')
const queueFilter = ref('All Staff')

// ── Period-End Pipeline ────────────────────────────────────────────────────────
const pipeline = ref([
    { month: 'Feb', year: '2026', total: 234, overdue: 172 },
    { month: 'Mar', year: '2026', total: 445, overdue: 400 },
    { month: 'Apr', year: '2026', total: 293, overdue: 182 },
    { month: 'May', year: '2026', total: 297, overdue: 0 },
    { month: 'Jun', year: '2026', total: 425, overdue: 0 },
    { month: 'Jul', year: '2026', total: 265, overdue: 0 },
])
const pipelineMax = computed(() => Math.max(...pipeline.value.map(p => p.total)))

// ── Team Productivity ──────────────────────────────────────────────────────────
const team = ref([
    { initials: 'MC', name: 'Mike Chen',    role: 'Accountant', total: 15, overdue: 10, due: 4,  coming: 1, color: '#1D9792' },
    { initials: 'SW', name: 'Sarah Wilson', role: 'Staff',      total: 14, overdue: 9,  due: 0,  coming: 5, color: '#055E5A' },
    { initials: 'GK', name: 'Gagan Kambo',  role: 'Admin',      total: 11, overdue: 0,  due: 8,  coming: 3, color: '#D97706' },
    { initials: 'TB', name: 'Tom Bradley',  role: 'Admin',      total: 3,  overdue: 0,  due: 3,  coming: 0, color: '#6B7280' },
])

// ── Walk-ins ───────────────────────────────────────────────────────────────────
const walkinsTab = ref<'all'|'overdue'|'week'|'done'>('all')
const walkins = ref([
    { name: 'Abc Holdings Inc.', sub: 'ServiceEngagement → Tom Bradley', status: 'in_progress', date: 'Mar 3' },
    { name: 'Elena Petrova',     sub: 'CRA Audit Response → Gagan Kambo', status: 'documents_received', date: 'Apr 21' },
    { name: 'Ahmed Ali',         sub: 'Airport → Sarah Wilson', status: 'in_progress', date: 'Apr 23' },
    { name: 'Sandra Lee',        sub: 'Personal T1 → Mike Chen', status: 'filed', date: 'Feb 28' },
    { name: 'Carlos Rivera',     sub: 'Corporations Canada → Tom Bradley', status: 'pending', date: 'Feb 28' },
])

const walkinBadgeClass: Record<string, string> = {
    in_progress:        'badge-teal-mid',
    documents_received: 'badge-info',
    filed:              'badge-success',
    pending:            'badge-warning',
}
const walkinBadgeLabel: Record<string, string> = {
    in_progress:        'In Progress',
    documents_received: 'Documents Received',
    filed:              'Filed',
    pending:            'Pending',
}
</script>

<template>
    <!-- ── Pinned bar ──────────────────────────────────────────────────────── -->
    <div class="pinned-bar">
        <span class="text-xs text-cpa-text-muted">📌 PINNED</span>
        <span class="pinned-tag">⚠ Overdue Payroll</span>
    </div>

    <!-- ── Page header ────────────────────────────────────────────────────── -->
    <div class="page-header">
        <div>
            <h1 class="page-title">Dashboard</h1>
            <p class="page-subtitle">Welcome back, {{ auth.user?.first_name }}</p>
        </div>
        <div class="page-actions">
            <select class="form-input btn-sm w-28">
                <option>All</option>
            </select>
            <div class="relative">
                <input
                    type="text"
                    placeholder="Search clients..."
                    class="form-input btn-sm pl-8 w-48"
                />
                <span class="absolute left-2.5 top-1/2 -translate-y-1/2 text-cpa-text-muted">
                    <svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.35-4.35"/></svg>
                </span>
            </div>
            <button class="btn btn-ghost btn-sm" title="Refresh">
                <RefreshCw :size="14" />
            </button>
            <button class="navbar-new-btn">
                <Plus :size="14" /> New
            </button>
            <button class="btn btn-outline btn-sm">Select widgets</button>
            <button class="btn btn-outline btn-sm">Customize</button>
        </div>
    </div>

    <!-- ── Widget grid ─────────────────────────────────────────────────────── -->
    <div class="widget-grid">

        <!-- ── 1. Urgency Overview ──────────────────────────────────────── -->
        <div class="widget row-span-2">
            <div class="widget-header">
                <div class="widget-title">
                    <div class="widget-title-icon bg-blue-50 text-blue-600">
                        <LayoutDashboard :size="15" />
                    </div>
                    <div>
                        Urgency Overview
                        <div class="widget-subtitle">{{ urgency.total.toLocaleString() }} active filings</div>
                    </div>
                </div>
            </div>
            <div class="widget-body">
                <!-- Donut chart placeholder -->
                <div class="urgency-donut">
                    <svg viewBox="0 0 36 36" class="w-full h-full -rotate-90">
                        <circle cx="18" cy="18" r="15.9" fill="none" stroke="#fee2e2" stroke-width="3"/>
                        <circle cx="18" cy="18" r="15.9" fill="none" stroke="#dc2626" stroke-width="3"
                            stroke-dasharray="87 13" stroke-linecap="round"/>
                        <circle cx="18" cy="18" r="15.9" fill="none" stroke="#fed7aa" stroke-width="3"
                            stroke-dasharray="11 89" stroke-dashoffset="-87" stroke-linecap="round"/>
                        <circle cx="18" cy="18" r="15.9" fill="none" stroke="#fef3c7" stroke-width="3"
                            stroke-dasharray="1 99" stroke-dashoffset="-98" stroke-linecap="round"/>
                        <circle cx="18" cy="18" r="15.9" fill="none" stroke="#dcfce7" stroke-width="3"
                            stroke-dasharray="1 99" stroke-dashoffset="-99" stroke-linecap="round"/>
                    </svg>
                    <div class="urgency-center">
                        <span class="urgency-total">{{ urgency.total.toLocaleString() }}</span>
                        <span class="urgency-label">Active</span>
                    </div>
                </div>

                <!-- Breakdown rows -->
                <div class="space-y-0.5 mt-2">
                    <div class="urgency-row">
                        <span class="urgency-row-label">
                            <span class="urgency-dot bg-red-500" />
                            Overdue
                            <span class="text-xs text-cpa-text-muted">87% of total</span>
                        </span>
                        <span class="urgency-count text-red-600">{{ urgency.overdue.toLocaleString() }}</span>
                    </div>
                    <div class="urgency-row">
                        <span class="urgency-row-label">
                            <span class="urgency-dot bg-orange-400" />
                            Due This Month
                            <span class="text-xs text-cpa-text-muted">11% of total</span>
                        </span>
                        <span class="urgency-count text-orange-500">{{ urgency.thisMonth }}</span>
                    </div>
                    <div class="urgency-row">
                        <span class="urgency-row-label">
                            <span class="urgency-dot bg-yellow-300" />
                            Due Next Month
                            <span class="text-xs text-cpa-text-muted">1% of total</span>
                        </span>
                        <span class="urgency-count text-yellow-600">{{ urgency.nextMonth }}</span>
                    </div>
                    <div class="urgency-row">
                        <span class="urgency-row-label">
                            <span class="urgency-dot bg-green-400" />
                            Coming Up
                            <span class="text-xs text-cpa-text-muted">1% of total</span>
                        </span>
                        <span class="urgency-count text-green-600">{{ urgency.upcoming }}</span>
                    </div>
                </div>
            </div>

            <!-- Walk-ins section in same column -->
            <div class="border-t border-cpa-border mt-2">
                <div class="widget-header">
                    <div class="widget-title">
                        <div class="widget-title-icon bg-indigo-50 text-indigo-600">
                            <Users :size="15" />
                        </div>
                        <div>
                            Walk-ins
                            <div class="widget-subtitle">11 active</div>
                        </div>
                    </div>
                    <div class="flex gap-1.5">
                        <button class="btn btn-ghost btn-xs"><Plus :size="12" /> Add</button>
                        <button class="btn btn-ghost btn-xs">View All</button>
                    </div>
                </div>

                <!-- Tabs -->
                <div class="walkins-tabs">
                    <span
                        v-for="t in ['all','overdue','week','done']"
                        :key="t"
                        class="walkins-tab"
                        :class="{ active: walkinsTab === t }"
                        @click="walkinsTab = t as any"
                    >
                        {{ t === 'all' ? 'All' : t === 'week' ? 'This week' : t.charAt(0).toUpperCase() + t.slice(1) }}
                    </span>
                </div>

                <!-- Walk-in rows -->
                <div>
                    <div
                        v-for="w in walkins"
                        :key="w.name"
                        class="walkin-row"
                    >
                        <div class="flex-1 min-w-0">
                            <div class="walkin-name">{{ w.name }}</div>
                            <div class="walkin-sub truncate">{{ w.sub }}</div>
                        </div>
                        <div class="walkin-badge">
                            <span :class="['badge', walkinBadgeClass[w.status] ?? 'badge-gray']">
                                {{ walkinBadgeLabel[w.status] ?? w.status }}
                            </span>
                            <div class="text-[11px] text-cpa-text-muted text-right mt-0.5">{{ w.date }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- ── 2. Critical Alerts ─────────────────────────────────────── -->
        <div class="widget">
            <div class="widget-header">
                <div class="widget-title">
                    <div class="widget-title-icon bg-red-50 text-red-600">
                        <AlertTriangle :size="15" />
                    </div>
                    <div>
                        Critical Alerts
                        <div class="widget-subtitle">
                            15 clients need attention
                            <span class="text-red-600 font-semibold">(10 overdue)</span>
                            <span class="text-orange-500 font-semibold">(10 payments due)</span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="overflow-y-auto" style="max-height: 320px;">
                <div
                    v-for="alert in alerts"
                    :key="alert.id"
                    class="alert-client"
                >
                    <div class="flex items-center justify-between mb-1.5">
                        <Link :href="`/clients/${alert.id}`" class="alert-client-name hover:text-cpa-medium-dark transition-colors">
                            {{ alert.name }}
                        </Link>
                        <span class="badge badge-danger text-[10px]">{{ alert.count }}</span>
                    </div>
                    <div
                        v-for="(f, fi) in alert.filings"
                        :key="fi"
                        class="alert-filing-row"
                    >
                        <span class="alert-dot bg-red-500" />
                        <span class="truncate">{{ f.label }}</span>
                        <span class="badge badge-danger ml-auto flex-shrink-0">{{ f.days }}d</span>
                    </div>
                    <div v-if="alert.filings.length < alert.count" class="text-[11px] text-cpa-text-muted mt-1 ml-3">
                        + {{ alert.count - alert.filings.length }} more
                    </div>
                </div>
            </div>
        </div>

        <!-- ── 3. Recent Activity ──────────────────────────────────────── -->
        <div class="widget">
            <div class="widget-header">
                <div class="widget-title">
                    <div class="widget-title-icon bg-teal-50 text-teal-700">
                        <Activity :size="15" />
                    </div>
                    <div>
                        Recent Activity
                        <div class="widget-subtitle">Documents &amp; communications overview</div>
                    </div>
                </div>
                <button class="btn btn-outline btn-xs">View All</button>
            </div>

            <div class="widget-body space-y-3">
                <!-- Status tabs -->
                <div class="flex gap-3 text-[11.5px] font-medium">
                    <span class="text-cpa-medium-dark cursor-pointer">{{ recentActivity.received }} received</span>
                    <span class="text-cpa-warning cursor-pointer">{{ recentActivity.review }} in review</span>
                    <span class="text-cpa-text-muted cursor-pointer">{{ recentActivity.sentToday }} sent today</span>
                    <span class="text-cpa-info cursor-pointer">{{ recentActivity.awaiting }} awaiting</span>
                </div>

                <!-- Documents -->
                <div>
                    <div class="flex items-center justify-between mb-1.5">
                        <span class="text-[11px] font-700 uppercase tracking-wider text-cpa-text-secondary">Documents</span>
                        <span class="text-[11px] text-cpa-text-muted">18</span>
                    </div>
                    <div class="space-y-1.5">
                        <div
                            v-for="doc in recentActivity.documents"
                            :key="doc.name"
                            class="flex items-start gap-2"
                        >
                            <div class="flex-1 min-w-0">
                                <div class="text-[11.5px] font-600 text-cpa-text-primary truncate">{{ doc.client }}</div>
                                <div class="text-[11px] text-cpa-text-muted truncate flex items-center gap-1.5">
                                    <FileText :size="11" class="flex-shrink-0" />
                                    {{ doc.name }}
                                    <span v-if="doc.isNew" class="badge badge-teal text-[9.5px] px-1.5">NEW</span>
                                    <span v-if="doc.status" :class="['badge', doc.status === 'approved' ? 'badge-success' : 'badge-gray', 'text-[9.5px] px-1.5']">
                                        {{ doc.status?.toUpperCase() }}
                                    </span>
                                </div>
                            </div>
                            <span class="text-[11px] text-cpa-text-muted flex-shrink-0">{{ doc.daysAgo }}d</span>
                        </div>
                    </div>
                </div>

                <!-- Reminders -->
                <div>
                    <div class="flex items-center justify-between mb-1.5">
                        <span class="text-[11px] font-700 uppercase tracking-wider text-cpa-text-secondary">Reminders</span>
                        <span class="text-[11px] text-cpa-text-muted">1</span>
                    </div>
                    <div
                        v-for="r in recentActivity.reminders"
                        :key="r.subject"
                        class="flex items-start gap-2"
                    >
                        <div class="flex-1 min-w-0">
                            <div class="text-[11.5px] font-600 text-cpa-text-primary">{{ r.subject }}</div>
                            <div class="text-[11px] text-cpa-text-muted">{{ r.client }}</div>
                        </div>
                        <span class="badge badge-warning text-[9.5px]">UNSENT</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- ── 4. Work Queue ───────────────────────────────────────────── -->
        <div class="widget row-span-2" style="grid-column: 4; grid-row: 1 / span 2;">
            <div class="widget-header">
                <div class="widget-title">
                    <div class="widget-title-icon bg-purple-50 text-purple-600">
                        <Briefcase :size="15" />
                    </div>
                    <div>
                        Work Queue
                        <div class="widget-subtitle">
                            1608 filings
                            <span class="text-red-600 font-semibold">(3235 overdue)</span>
                            <span class="text-orange-500 font-semibold">(1554 unassigned)</span>
                        </div>
                    </div>
                </div>
                <button class="btn-icon navbar-icon-btn"><Maximize2 :size="13" /></button>
            </div>

            <!-- Queue filters -->
            <div class="px-3 py-2 border-b border-cpa-border flex gap-2 flex-wrap">
                <input
                    v-model="queueSearch"
                    type="text"
                    placeholder="Search clients..."
                    class="form-input btn-xs flex-1 min-w-32"
                />
                <select v-model="queueFilter" class="form-input btn-xs w-24">
                    <option>All Staff</option>
                </select>
                <select class="form-input btn-xs w-24">
                    <option>All Types</option>
                </select>
                <select class="form-input btn-xs w-24">
                    <option>All Status</option>
                </select>
            </div>

            <!-- Queue header -->
            <div class="queue-row queue-row-header">
                <span>Item / Deadline</span>
                <span>Client</span>
                <span>Type</span>
                <span>Status</span>
                <span>Assigned</span>
            </div>

            <!-- Queue rows -->
            <div class="overflow-y-auto" style="max-height: 480px;">
                <div
                    v-for="(q, i) in workQueue"
                    :key="i"
                    class="queue-row hover:bg-cpa-very-light cursor-pointer transition-colors"
                >
                    <div>
                        <div class="font-600 text-[12px] text-cpa-text-primary">{{ q.name }}</div>
                        <div class="queue-period">{{ q.type }} · {{ q.period }}</div>
                        <div class="text-[11px] text-cpa-text-muted">
                            Period: {{ q.deadline }}
                            <br/>Due: {{ q.due }}
                        </div>
                    </div>
                    <div class="text-[12px] text-cpa-text-primary">{{ q.name }}</div>
                    <div class="text-[12px] text-cpa-text-muted">{{ q.type }}</div>
                    <div>
                        <span class="badge badge-warning text-[10px]">Under Review</span>
                    </div>
                    <div class="flex flex-col gap-0.5">
                        <span class="queue-late">{{ q.late }} LATE</span>
                        <div class="w-5 h-5 rounded-full bg-cpa-medium flex items-center justify-center text-white text-[9px] font-700">
                            {{ q.assignee ? q.assignee.split(' ').map(w => w[0]).join('') : 'UA' }}
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- ── 5. Period-End Pipeline ──────────────────────────────────── -->
        <div class="widget">
            <div class="widget-header">
                <div class="widget-title">
                    <div class="widget-title-icon bg-orange-50 text-orange-600">
                        <TrendingUp :size="15" />
                    </div>
                    <div>
                        Period End Pipeline
                        <div class="widget-subtitle">
                            4 of 1,969 filed
                            <span class="text-red-600 font-semibold">- 678 overdue</span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="widget-body space-y-1.5">
                <div
                    v-for="p in pipeline"
                    :key="p.month + p.year"
                    class="pipeline-row"
                >
                    <span class="pipeline-month">{{ p.month }}<br/><span class="text-[10px]">{{ p.year }}</span></span>
                    <div class="pipeline-bar-wrap">
                        <div
                            class="pipeline-bar"
                            :class="{ overdue: p.overdue > 0 }"
                            :style="{ width: `${(p.total / pipelineMax) * 100}%` }"
                        />
                    </div>
                    <span class="pipeline-count">{{ p.total }}</span>
                    <span class="pipeline-ovd">
                        <span v-if="p.overdue > 0" class="text-red-500 font-600">{{ p.overdue }} ovd</span>
                        <span v-else class="text-cpa-text-muted">—</span>
                    </span>
                </div>
            </div>
        </div>

        <!-- ── 6. Team Productivity ────────────────────────────────────── -->
        <div class="widget">
            <div class="widget-header">
                <div class="widget-title">
                    <div class="widget-title-icon bg-cpa-very-light text-cpa-medium-dark">
                        <Users :size="15" />
                    </div>
                    <div>
                        Team Productivity
                        <div class="widget-subtitle">4 team members</div>
                    </div>
                </div>
            </div>

            <div>
                <div
                    v-for="m in team"
                    :key="m.name"
                    class="team-member"
                >
                    <div class="team-avatar" :style="{ borderColor: m.color }">
                        {{ m.initials }}
                    </div>
                    <div class="flex-1 min-w-0">
                        <div class="team-name">{{ m.name }}</div>
                        <div class="team-role">{{ m.role }}</div>
                    </div>
                    <div class="team-count" :style="{ color: m.color }">{{ m.total }}</div>
                    <div class="team-stat">
                        <div class="team-stat-col">
                            <span class="team-stat-val text-red-500">{{ m.overdue }}</span>
                            <span class="team-stat-lbl">ovd</span>
                        </div>
                        <div class="team-stat-col">
                            <span class="team-stat-val text-orange-500">{{ m.due }}</span>
                            <span class="team-stat-lbl">due</span>
                        </div>
                        <div class="team-stat-col">
                            <span class="team-stat-val text-cpa-medium-dark">{{ m.coming }}</span>
                            <span class="team-stat-lbl">coming</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div><!-- /widget-grid -->
</template>
