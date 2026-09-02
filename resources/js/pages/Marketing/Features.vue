<script setup lang="ts">
import MarketingLayout from '@/layouts/MarketingLayout.vue'
import SeoHead from '@/components/ui/SeoHead.vue'
import { Link } from '@inertiajs/vue3'
import {
    Users, FileText, Clock, Shield, Workflow, BarChart2,
    CreditCard, MessageSquare, Bell, Bot, CheckCircle, ArrowRight,
    FolderOpen, Building2, Calendar, TrendingUp,
} from '@lucide/vue'

defineOptions({ layout: MarketingLayout })
defineProps<{ seo?: Record<string, unknown> }>()

const featureGroups = [
    {
        id: 'crm',
        eyebrow: 'CRM & Client Management',
        title: 'Know every client, inside and out',
        desc: 'Centralise every client relationship — contacts, entities, tax identifiers, communication history, and health scores — in one place.',
        icon: Users,
        color: 'blue',
        items: [
            'Full client profiles with 360° view',
            'Business & personal/family client types',
            'Linked contacts, related entities & dependants',
            'Custom fields and tagging',
            'Client health scores & risk ratings',
            'CSV import & bulk actions',
        ],
    },
    {
        id: 'filings',
        eyebrow: 'Filing & Tax Engine',
        title: 'Track every deadline, automatically',
        desc: 'The filing engine generates deadlines, tracks statuses, and alerts your team before anything falls through the cracks.',
        icon: FileText,
        color: 'teal',
        items: [
            'T1, T2, T3, T4, GST/HST, Payroll & more',
            'Multi-jurisdiction support',
            'Automated deadline generation with holiday rules',
            'Filing status pipeline (Assigned → Filed → Accepted)',
            'Bulk filing assignment',
            'Filing history & version tracking',
        ],
    },
    {
        id: 'workflows',
        eyebrow: 'Workflow & Task Automation',
        title: 'Build once, run every time',
        desc: 'Create reusable workflow templates that auto-generate tasks, assign them to team members, and track completion.',
        icon: Workflow,
        color: 'purple',
        items: [
            'Drag-and-drop workflow builder',
            'Trigger-based automation (filing created, document received)',
            'Task dependencies, checklists & recurrence',
            'Team capacity planning & workload view',
            'Time tracking per task & engagement',
            'Kanban and list views',
        ],
    },
    {
        id: 'documents',
        eyebrow: 'Document Management',
        title: 'A secure vault for every file',
        desc: 'Store, organise, and share documents with bank-level encryption. Clients upload directly through the portal.',
        icon: FolderOpen,
        color: 'orange',
        items: [
            'Unlimited secure document storage',
            'Version control & audit trail',
            'Document request checklists',
            'E-signature workflows (multi-signer)',
            'Retention policies & legal hold',
            'Signed URL delivery — never expose raw paths',
        ],
    },
    {
        id: 'billing',
        eyebrow: 'Invoicing & Accounting',
        title: 'Get paid faster, reconcile easier',
        desc: 'Full-cycle billing from proposal to payment — with double-entry accounting under the hood.',
        icon: CreditCard,
        color: 'green',
        items: [
            'Proposal & engagement letter generation',
            'Invoices with recurring billing',
            'Multi-currency payments & AR aging',
            'Double-entry general ledger',
            'Bank reconciliation',
            'P&L, Balance Sheet & Cash Flow reports',
        ],
    },
    {
        id: 'portal',
        eyebrow: 'Client Portal',
        title: 'Clients stay in the loop, always',
        desc: 'Give every client their own secure portal to upload documents, review filings, pay invoices, and message your team.',
        icon: Shield,
        color: 'teal',
        items: [
            'Branded client portal',
            'Secure messaging & notifications',
            'Document upload & download',
            'Invoice view & payment',
            'Filing status visibility',
            'Appointment booking',
        ],
    },
]
</script>

<template>
    <SeoHead :seo="(seo as any)" />

    <!-- ── Hero ── -->
    <section class="page-hero">
        <div class="marketing-container text-center">
            <div class="section-eyebrow">Features</div>
            <h1 class="page-hero-title">Everything a CPA firm needs</h1>
            <p class="page-hero-subtitle">
                VJ CPA CRM is the only platform that combines client management, filing tracking,
                workflows, documents, billing, and accounting — built specifically for accounting practices.
            </p>
            <Link href="/register" class="hero-btn-primary">
                Start free trial <ArrowRight :size="15" />
            </Link>
        </div>
    </section>

    <!-- ── Feature groups ── -->
    <section v-for="(group, i) in featureGroups" :key="group.id"
        :class="['feature-group', i % 2 === 1 ? 'feature-group-alt' : '']"
    >
        <div class="marketing-container">
            <div class="feature-group-grid">
                <div class="feature-group-content" :class="i % 2 === 1 ? 'order-2' : ''">
                    <div class="section-eyebrow">{{ group.eyebrow }}</div>
                    <h2 class="section-title">{{ group.title }}</h2>
                    <p class="feature-group-desc">{{ group.desc }}</p>
                    <ul class="feature-list">
                        <li v-for="item in group.items" :key="item" class="feature-list-item">
                            <CheckCircle :size="15" class="text-cpa-success flex-shrink-0 mt-0.5" />
                            {{ item }}
                        </li>
                    </ul>
                </div>
                <div class="feature-group-visual" :class="i % 2 === 1 ? 'order-1' : ''">
                    <div class="feature-visual-card">
                        <component :is="group.icon" :size="48" class="text-cpa-medium-dark opacity-20" />
                        <div class="mt-4 space-y-2">
                            <div v-for="item in group.items.slice(0,4)" :key="item"
                                class="flex items-center gap-2 text-xs text-gray-500"
                            >
                                <div class="w-1.5 h-1.5 rounded-full bg-cpa-medium flex-shrink-0" />
                                {{ item }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- ── Bottom CTA ── -->
    <section class="cta-section-light">
        <div class="marketing-container text-center">
            <h2 class="section-title">Ready to see it in action?</h2>
            <p class="section-subtitle mx-auto">
                Start your 14-day free trial. No credit card required. Full access from day one.
            </p>
            <div class="flex flex-col sm:flex-row gap-3 justify-center">
                <Link href="/register" class="hero-btn-primary">Start free trial <ArrowRight :size="15" /></Link>
                <Link href="/contact"  class="hero-btn-secondary">Request a demo</Link>
            </div>
        </div>
    </section>
</template>

<style scoped>
.marketing-container { max-width: 1200px; margin: 0 auto; padding: 0 1.5rem; }

.page-hero {
    padding: 5rem 0 3.5rem;
    background: linear-gradient(160deg, #F4FAFA 0%, #E6F5F4 60%, #fff 100%);
}
.page-hero-title {
    font-size: clamp(2rem, 4vw, 3rem);
    font-weight: 800;
    color: #0D2B2A;
    letter-spacing: -1px;
    margin-bottom: .75rem;
}
.page-hero-subtitle {
    font-size: 1.1rem;
    color: #4D7374;
    max-width: 620px;
    margin: 0 auto 2rem;
    line-height: 1.7;
}
.section-eyebrow {
    font-size: 12px; font-weight: 700; letter-spacing: 1.2px;
    text-transform: uppercase; color: #1D9792; margin-bottom: .5rem;
}
.section-title {
    font-size: clamp(1.4rem, 2.5vw, 2rem);
    font-weight: 800; color: #0D2B2A; letter-spacing: -.5px;
    line-height: 1.25; margin-bottom: .75rem;
}

.feature-group { padding: 5rem 0; background: #fff; }
.feature-group-alt { background: #F4FAFA; }

.feature-group-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 4rem;
    align-items: center;
}
@media (max-width: 800px) {
    .feature-group-grid { grid-template-columns: 1fr; gap: 2rem; }
    .order-1, .order-2 { order: unset !important; }
}

.feature-group-desc { font-size: 1rem; color: #6B7280; line-height: 1.7; margin-bottom: 1.5rem; }

.feature-list { list-style: none; padding: 0; margin: 0; display: flex; flex-direction: column; gap: 8px; }
.feature-list-item { display: flex; align-items: flex-start; gap: 8px; font-size: 13.5px; color: #374151; }

.feature-visual-card {
    background: #fff;
    border: 1px solid #D4ECEA;
    border-radius: 16px;
    padding: 2rem;
    box-shadow: 0 8px 32px rgba(2,62,60,.07);
    display: flex;
    flex-direction: column;
    align-items: center;
    min-height: 280px;
    justify-content: center;
}
.feature-group-alt .feature-visual-card { background: #fff; }

.hero-btn-primary {
    display: inline-flex; align-items: center; gap: 8px;
    background: #1D9792; color: #fff; font-size: 15px;
    font-weight: 600; padding: 12px 24px; border-radius: 9px;
    text-decoration: none; transition: background .15s; border: none; cursor: pointer;
}
.hero-btn-primary:hover { background: #055E5A; }
.hero-btn-secondary {
    display: inline-flex; align-items: center; gap: 6px;
    background: #fff; color: #374151; font-size: 15px; font-weight: 500;
    padding: 12px 24px; border-radius: 9px; text-decoration: none;
    border: 1.5px solid #D4ECEA; transition: border-color .15s, color .15s; cursor: pointer;
}
.hero-btn-secondary:hover { border-color: #1D9792; color: #055E5A; }

.cta-section-light { padding: 5rem 0; background: #E6F5F4; }
.section-subtitle { font-size: 1rem; color: #6B7280; max-width: 560px; line-height: 1.7; margin-bottom: 2rem; }
</style>
