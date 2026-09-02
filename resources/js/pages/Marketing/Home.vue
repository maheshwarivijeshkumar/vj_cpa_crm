<script setup lang="ts">
import MarketingLayout from '@/layouts/MarketingLayout.vue'
import SeoHead from '@/components/ui/SeoHead.vue'
import { Link } from '@inertiajs/vue3'
import {
    ArrowRight, CheckCircle, Users, FileText, Clock,
    Shield, TrendingUp, Bell, BarChart2, Zap,
} from '@lucide/vue'

defineOptions({ layout: MarketingLayout })

defineProps<{ seo?: Record<string, unknown> }>()

const features = [
    { icon: Users,      title: 'Client Management',    desc: 'Full 360° client profiles — contacts, entities, tax IDs, engagement history, and custom fields.' },
    { icon: FileText,   title: 'Filing Engine',         desc: 'Track every filing — T1, T2, GST/HST, payroll and more — with automated deadline calculation.' },
    { icon: Clock,      title: 'Deadline Tracking',     desc: 'Never miss a filing deadline. Smart reminders, colour-coded urgency, and calendar integration.' },
    { icon: Shield,     title: 'Document Management',  desc: 'Secure file storage, version control, e-signature requests, and client document portals.' },
    { icon: TrendingUp, title: 'Workflow Automation',   desc: 'Build repeatable workflows for engagements, onboarding, tax prep, and year-end close.' },
    { icon: BarChart2,  title: 'Reports & Analytics',   desc: 'Practice-wide dashboards, utilisation reports, AR aging, and custom financial statements.' },
]

const stats = [
    { value: '2,500+', label: 'CPA Firms' },
    { value: '1.2M',   label: 'Filings Tracked' },
    { value: '99.9%',  label: 'Uptime SLA' },
    { value: '14-day', label: 'Free Trial' },
]
</script>

<template>
    <SeoHead :seo="(seo as any)" />

    <!-- ── Hero ──────────────────────────────────────────────────────────── -->
    <section class="hero-section">
        <div class="marketing-container">
            <div class="hero-badge">
                <Zap :size="12" class="text-cpa-medium" />
                Practice management, reimagined
            </div>

            <h1 class="hero-title">
                Everything your CPA firm<br class="hidden sm:block" />
                needs in one place
            </h1>

            <p class="hero-subtitle">
                Manage clients, track filings, automate workflows, send invoices,
                and collaborate with your team — all from a single, beautifully
                designed platform built for accounting professionals.
            </p>

            <div class="hero-actions">
                <Link href="/register" class="hero-btn-primary">
                    Start your free 14-day trial
                    <ArrowRight :size="16" />
                </Link>
                <Link href="/features" class="hero-btn-secondary">
                    See all features
                </Link>
            </div>

            <p class="hero-caption">
                No credit card required · Cancel anytime · Free onboarding call included
            </p>

            <!-- Dashboard preview mockup -->
            <div class="hero-preview">
                <div class="hero-preview-bar">
                    <span class="hero-preview-dot bg-red-400" />
                    <span class="hero-preview-dot bg-yellow-400" />
                    <span class="hero-preview-dot bg-green-400" />
                    <span class="ml-3 text-xs text-gray-400">dashboard.cpacrm.com</span>
                </div>
                <div class="hero-preview-body">
                    <!-- Mini dashboard skeleton -->
                    <div class="flex gap-3 p-4 border-b border-gray-100">
                        <div class="w-24 h-3 bg-gray-100 rounded animate-pulse" />
                        <div class="w-16 h-3 bg-cpa-very-light rounded animate-pulse" />
                        <div class="w-20 h-3 bg-gray-100 rounded animate-pulse" />
                    </div>
                    <div class="grid grid-cols-4 gap-3 p-4">
                        <div v-for="i in 4" :key="i" class="bg-white border border-gray-100 rounded-lg p-3">
                            <div class="w-8 h-8 rounded-md mb-2" :class="['bg-cpa-very-light','bg-red-50','bg-amber-50','bg-green-50'][i-1]" />
                            <div class="w-12 h-5 bg-gray-200 rounded mb-1 animate-pulse" />
                            <div class="w-20 h-2.5 bg-gray-100 rounded animate-pulse" />
                        </div>
                    </div>
                    <div class="px-4 pb-4 grid grid-cols-3 gap-3">
                        <div class="col-span-2 bg-white border border-gray-100 rounded-lg p-3 space-y-2">
                            <div class="w-32 h-3 bg-gray-200 rounded animate-pulse" />
                            <div v-for="j in 4" :key="j" class="flex items-center gap-2">
                                <div class="w-2 h-2 rounded-full" :class="['bg-red-400','bg-amber-400','bg-green-400','bg-cpa-medium'][j-1]" />
                                <div class="flex-1 h-2.5 bg-gray-100 rounded animate-pulse" />
                                <div class="w-8 h-2.5 bg-gray-100 rounded animate-pulse" />
                            </div>
                        </div>
                        <div class="bg-white border border-gray-100 rounded-lg p-3 space-y-2">
                            <div class="w-24 h-3 bg-gray-200 rounded animate-pulse" />
                            <div v-for="k in 3" :key="k" class="flex items-center gap-2">
                                <div class="w-6 h-6 rounded-full bg-cpa-very-light animate-pulse" />
                                <div class="flex-1 h-2.5 bg-gray-100 rounded animate-pulse" />
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- ── Stats ─────────────────────────────────────────────────────────── -->
    <section class="stats-section">
        <div class="marketing-container">
            <div class="stats-grid">
                <div v-for="stat in stats" :key="stat.label" class="stat-item">
                    <div class="stat-value">{{ stat.value }}</div>
                    <div class="stat-label">{{ stat.label }}</div>
                </div>
            </div>
        </div>
    </section>

    <!-- ── Features grid ─────────────────────────────────────────────────── -->
    <section class="features-section">
        <div class="marketing-container">
            <div class="section-eyebrow">Built for CPA practices</div>
            <h2 class="section-title">One platform, everything you need</h2>
            <p class="section-subtitle">
                From new client onboarding to year-end filing, VJ CPA CRM covers
                the complete practice management lifecycle.
            </p>

            <div class="features-grid">
                <div
                    v-for="feat in features"
                    :key="feat.title"
                    class="feature-card"
                >
                    <div class="feature-icon">
                        <component :is="feat.icon" :size="22" />
                    </div>
                    <h3 class="feature-title">{{ feat.title }}</h3>
                    <p class="feature-desc">{{ feat.desc }}</p>
                </div>
            </div>

            <div class="text-center mt-10">
                <Link href="/features" class="marketing-link-btn">
                    See all features <ArrowRight :size="14" class="inline ml-1" />
                </Link>
            </div>
        </div>
    </section>

    <!-- ── Social proof / how it works ───────────────────────────────────── -->
    <section class="workflow-section">
        <div class="marketing-container">
            <div class="workflow-grid">
                <div class="workflow-content">
                    <div class="section-eyebrow">How it works</div>
                    <h2 class="section-title text-left">From intake to filing,<br/>in one clean flow</h2>
                    <div class="workflow-steps">
                        <div v-for="(step, i) in [
                            { title: 'Add your clients',         desc: 'Import from CSV or add individually. Full profiles in minutes.' },
                            { title: 'Configure your services',  desc: 'Set up tax types, deadlines, and billing for each client.' },
                            { title: 'Track & automate',         desc: 'Workflows auto-generate tasks. Smart reminders keep your team on schedule.' },
                            { title: 'Invoice & get paid',       desc: 'Send invoices, track payments, and reconcile from one place.' },
                        ]" :key="step.title" class="workflow-step">
                            <div class="workflow-step-num">{{ i + 1 }}</div>
                            <div>
                                <h4 class="workflow-step-title">{{ step.title }}</h4>
                                <p class="workflow-step-desc">{{ step.desc }}</p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="workflow-visual">
                    <div class="workflow-card">
                        <div v-for="check in [
                            'Client onboarded & verified',
                            'T1 filing assigned to accountant',
                            'Documents requested from client',
                            'Review completed — filed on time',
                        ]" :key="check" class="workflow-check-row">
                            <CheckCircle :size="16" class="text-cpa-success flex-shrink-0 mt-0.5" />
                            <span>{{ check }}</span>
                        </div>
                        <div class="workflow-progress">
                            <div class="workflow-progress-label">
                                <span>Filing completion</span>
                                <span class="font-semibold text-cpa-medium-dark">94%</span>
                            </div>
                            <div class="workflow-progress-bar">
                                <div class="workflow-progress-fill" style="width:94%" />
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- ── CTA banner ────────────────────────────────────────────────────── -->
    <section class="cta-section">
        <div class="marketing-container text-center">
            <h2 class="cta-title">Ready to run a better practice?</h2>
            <p class="cta-subtitle">
                Join thousands of CPA firms using VJ CPA CRM to save time,<br class="hidden sm:block" />
                reduce errors, and grow their practice.
            </p>
            <div class="flex flex-col sm:flex-row gap-3 justify-center mt-8">
                <Link href="/register" class="hero-btn-primary text-base px-8 py-3.5">
                    Start free — 14 days, no card
                    <ArrowRight :size="16" />
                </Link>
                <Link href="/contact" class="hero-btn-secondary text-base px-8 py-3.5">
                    Talk to sales
                </Link>
            </div>
        </div>
    </section>
</template>

<style scoped>
.marketing-container {
    max-width: 1200px;
    margin: 0 auto;
    padding: 0 1.5rem;
}

/* ── Hero ── */
.hero-section {
    background: linear-gradient(160deg, #F4FAFA 0%, #E6F5F4 50%, #fff 100%);
    padding: 5rem 0 3rem;
    overflow: hidden;
}
.hero-badge {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    background: #C5E8E5;
    color: #055E5A;
    font-size: 12px;
    font-weight: 600;
    letter-spacing: .3px;
    padding: 5px 12px;
    border-radius: 9999px;
    margin-bottom: 1.5rem;
}
.hero-title {
    font-size: clamp(2rem, 5vw, 3.5rem);
    font-weight: 800;
    color: #0D2B2A;
    line-height: 1.15;
    letter-spacing: -1px;
    margin-bottom: 1.25rem;
}
.hero-subtitle {
    font-size: clamp(1rem, 2vw, 1.2rem);
    color: #4D7374;
    max-width: 600px;
    line-height: 1.7;
    margin-bottom: 2rem;
}
.hero-actions {
    display: flex;
    flex-wrap: wrap;
    gap: 12px;
    align-items: center;
    margin-bottom: 1rem;
}
.hero-btn-primary {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    background: #1D9792;
    color: #fff;
    font-size: 15px;
    font-weight: 600;
    padding: 12px 24px;
    border-radius: 9px;
    text-decoration: none;
    transition: background .15s;
    border: none;
    cursor: pointer;
}
.hero-btn-primary:hover { background: #055E5A; }
.hero-btn-secondary {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    background: #fff;
    color: #374151;
    font-size: 15px;
    font-weight: 500;
    padding: 12px 24px;
    border-radius: 9px;
    text-decoration: none;
    border: 1.5px solid #D4ECEA;
    transition: border-color .15s, color .15s;
    cursor: pointer;
}
.hero-btn-secondary:hover { border-color: #1D9792; color: #055E5A; }
.hero-caption {
    font-size: 12.5px;
    color: #9CA3AF;
    margin-top: .5rem;
}

/* Hero preview */
.hero-preview {
    margin-top: 3.5rem;
    background: #fff;
    border: 1px solid #E5E7EB;
    border-radius: 14px;
    box-shadow: 0 20px 60px rgba(2,62,60,.1);
    overflow: hidden;
    max-width: 900px;
}
.hero-preview-bar {
    display: flex;
    align-items: center;
    gap: 6px;
    padding: 10px 14px;
    background: #F9FAFB;
    border-bottom: 1px solid #F3F4F6;
}
.hero-preview-dot {
    width: 11px;
    height: 11px;
    border-radius: 50%;
    display: inline-block;
}
.hero-preview-body {
    background: #F9FAFB;
}

/* ── Stats ── */
.stats-section {
    border-top: 1px solid #E6F5F4;
    border-bottom: 1px solid #E6F5F4;
    background: #fff;
    padding: 2.5rem 0;
}
.stats-grid {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 1rem;
    text-align: center;
}
@media (max-width: 600px) { .stats-grid { grid-template-columns: repeat(2, 1fr); } }
.stat-value {
    font-size: 2rem;
    font-weight: 800;
    color: #055E5A;
    line-height: 1;
}
.stat-label {
    font-size: 13px;
    color: #6B9294;
    margin-top: 4px;
}

/* ── Section commons ── */
.section-eyebrow {
    font-size: 12px;
    font-weight: 700;
    letter-spacing: 1.2px;
    text-transform: uppercase;
    color: #1D9792;
    margin-bottom: .5rem;
}
.section-title {
    font-size: clamp(1.5rem, 3vw, 2.25rem);
    font-weight: 800;
    color: #0D2B2A;
    letter-spacing: -.5px;
    line-height: 1.25;
    margin-bottom: .75rem;
}
.section-subtitle {
    font-size: 1rem;
    color: #6B7280;
    max-width: 560px;
    line-height: 1.7;
    margin-bottom: 2.5rem;
}

/* ── Features ── */
.features-section {
    padding: 5rem 0;
    background: #fff;
}
.features-grid {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 1.5rem;
}
@media (max-width: 900px) { .features-grid { grid-template-columns: repeat(2, 1fr); } }
@media (max-width: 600px) { .features-grid { grid-template-columns: 1fr; } }

.feature-card {
    background: #F4FAFA;
    border: 1px solid #E6F5F4;
    border-radius: 12px;
    padding: 1.5rem;
    transition: box-shadow .15s, border-color .15s, transform .15s;
}
.feature-card:hover {
    box-shadow: 0 6px 24px rgba(2,62,60,.08);
    border-color: #8CD3CF;
    transform: translateY(-2px);
}
.feature-icon {
    width: 44px;
    height: 44px;
    background: #E6F5F4;
    border-radius: 10px;
    display: flex;
    align-items: center;
    justify-content: center;
    color: #1D9792;
    margin-bottom: 1rem;
}
.feature-title {
    font-size: 15px;
    font-weight: 700;
    color: #0D2B2A;
    margin-bottom: .4rem;
}
.feature-desc { font-size: 13.5px; color: #6B7280; line-height: 1.6; }

.marketing-link-btn {
    font-size: 14px;
    font-weight: 600;
    color: #1D9792;
    text-decoration: none;
    transition: color .12s;
}
.marketing-link-btn:hover { color: #055E5A; }

/* ── Workflow ── */
.workflow-section {
    padding: 5rem 0;
    background: #F4FAFA;
}
.workflow-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 4rem;
    align-items: center;
}
@media (max-width: 800px) { .workflow-grid { grid-template-columns: 1fr; gap: 2.5rem; } }

.workflow-steps { display: flex; flex-direction: column; gap: 1.5rem; margin-top: 1.5rem; }
.workflow-step  { display: flex; gap: 1rem; align-items: flex-start; }
.workflow-step-num {
    width: 32px; height: 32px;
    background: #1D9792; color: #fff;
    border-radius: 50%;
    display: flex; align-items: center; justify-content: center;
    font-size: 13px; font-weight: 700; flex-shrink: 0;
}
.workflow-step-title { font-size: 14.5px; font-weight: 700; color: #0D2B2A; margin-bottom: 3px; }
.workflow-step-desc  { font-size: 13.5px; color: #6B7280; line-height: 1.6; }

.workflow-card {
    background: #fff;
    border: 1px solid #D4ECEA;
    border-radius: 14px;
    padding: 1.75rem;
    box-shadow: 0 8px 32px rgba(2,62,60,.08);
}
.workflow-check-row {
    display: flex; align-items: flex-start; gap: 10px;
    font-size: 13.5px; color: #374151;
    padding: 7px 0;
    border-bottom: 1px solid #F0FAF9;
}
.workflow-check-row:last-of-type { border-bottom: none; }
.workflow-progress { margin-top: 1.25rem; }
.workflow-progress-label {
    display: flex; justify-content: space-between;
    font-size: 12.5px; color: #6B7280; margin-bottom: 6px;
}
.workflow-progress-bar {
    height: 8px; background: #E6F5F4; border-radius: 9999px; overflow: hidden;
}
.workflow-progress-fill {
    height: 100%; background: #1D9792; border-radius: 9999px; transition: width .6s ease;
}

/* ── CTA ── */
.cta-section {
    padding: 5rem 0;
    background: linear-gradient(135deg, #055E5A 0%, #1D9792 100%);
    color: #fff;
}
.cta-title    { font-size: clamp(1.75rem, 4vw, 2.5rem); font-weight: 800; color: #fff; margin-bottom: .75rem; }
.cta-subtitle { font-size: 1.05rem; color: rgba(255,255,255,.8); line-height: 1.7; }
.cta-section .hero-btn-primary  { background: #fff; color: #055E5A; }
.cta-section .hero-btn-primary:hover { background: #E6F5F4; }
.cta-section .hero-btn-secondary { background: rgba(255,255,255,.1); color: #fff; border-color: rgba(255,255,255,.3); }
.cta-section .hero-btn-secondary:hover { background: rgba(255,255,255,.2); border-color: rgba(255,255,255,.6); }
</style>
