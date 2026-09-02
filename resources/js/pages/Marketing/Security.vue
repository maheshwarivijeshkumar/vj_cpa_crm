<script setup lang="ts">
import MarketingLayout from '@/layouts/MarketingLayout.vue'
import SeoHead from '@/components/ui/SeoHead.vue'
import { Link } from '@inertiajs/vue3'
import {
    Shield, Lock, Server, Eye, CheckCircle,
    Users, FileText, RefreshCw, ArrowRight,
} from '@lucide/vue'

defineOptions({ layout: MarketingLayout })
defineProps<{ seo?: Record<string, unknown> }>()

const pillars = [
    {
        icon: Lock,
        title: 'End-to-end encryption',
        desc: 'All data is encrypted in transit (TLS 1.3) and at rest (AES-256). Every file download uses a signed URL with a short expiry — raw storage paths are never exposed.',
    },
    {
        icon: Server,
        title: 'Multi-tenant isolation',
        desc: 'Every accounting firm is a completely separate tenant. Eloquent global scopes enforce data isolation at the ORM layer, ensuring one firm\'s data can never leak to another.',
    },
    {
        icon: Users,
        title: 'Role-based access control',
        desc: 'Granular RBAC with platform-level roles, firm-level roles, and per-user permission overrides. Every API endpoint enforces a Policy authorization check — the frontend is not trusted.',
    },
    {
        icon: Eye,
        title: 'Immutable audit trail',
        desc: 'Every sensitive action — login, logout, create, update, delete, financial posting, document access — is written to an append-only audit log that cannot be modified or deleted.',
    },
    {
        icon: Shield,
        title: 'Two-factor authentication',
        desc: 'TOTP-based 2FA with recovery codes is available on all accounts. Platform admins can enforce 2FA for all users in a tenant.',
    },
    {
        icon: RefreshCw,
        title: 'Automatic session management',
        desc: 'Sessions expire after configurable inactivity periods. Users can view all active sessions and force-logout any device from their security settings.',
    },
    {
        icon: FileText,
        title: 'Financial data integrity',
        desc: 'Posted journal entries, payments, and invoices are immutable. Financial history is reversed or voided — never deleted. Double-entry accounting enforces debit = credit on every posting.',
    },
    {
        icon: CheckCircle,
        title: 'Login rate limiting',
        desc: 'Failed login attempts are rate-limited per email and IP address. Accounts are temporarily locked after 5 consecutive failures. All attempts are logged.',
    },
]

const compliance = [
    { label: 'PIPEDA compliant',               desc: 'We comply with Canada\'s Personal Information Protection and Electronic Documents Act.' },
    { label: 'Data residency — Canada',         desc: 'All data for Canadian clients is stored on Canadian infrastructure by default.' },
    { label: '99.9% uptime SLA',                desc: 'We maintain a 99.9% monthly uptime commitment backed by our Terms of Service.' },
    { label: 'SOC 2 Type II (in progress)',     desc: 'We are currently pursuing SOC 2 Type II certification. Report available to enterprise customers on request.' },
    { label: 'Vulnerability disclosure',        desc: 'We operate a responsible disclosure program. Security researchers can report issues to security@cpacrm.com.' },
    { label: 'Penetration testing',             desc: 'Annual third-party penetration tests are conducted by an independent security firm.' },
]
</script>

<template>
    <SeoHead :seo="(seo as any)" />

    <!-- ── Hero ── -->
    <section class="security-hero">
        <div class="marketing-container text-center">
            <div class="security-hero-icon">
                <Shield :size="36" class="text-cpa-medium-dark" />
            </div>
            <div class="section-eyebrow">Security</div>
            <h1 class="security-title">Your clients' data is safe with us</h1>
            <p class="security-subtitle">
                CPA firms are entrusted with sensitive financial and personal information.
                We take that responsibility seriously — security is built into every layer of our platform,
                not bolted on as an afterthought.
            </p>
            <Link href="/register" class="hero-btn-primary">
                Start free trial <ArrowRight :size="15" />
            </Link>
        </div>
    </section>

    <!-- ── Security pillars ── -->
    <section class="pillars-section">
        <div class="marketing-container">
            <div class="section-eyebrow text-center">Security architecture</div>
            <h2 class="section-title text-center mb-10">Built secure from the ground up</h2>
            <div class="pillars-grid">
                <div v-for="p in pillars" :key="p.title" class="pillar-card">
                    <div class="pillar-icon">
                        <component :is="p.icon" :size="20" />
                    </div>
                    <h3 class="pillar-title">{{ p.title }}</h3>
                    <p class="pillar-desc">{{ p.desc }}</p>
                </div>
            </div>
        </div>
    </section>

    <!-- ── Compliance ── -->
    <section class="compliance-section">
        <div class="marketing-container">
            <div class="compliance-grid">
                <div>
                    <div class="section-eyebrow">Compliance & transparency</div>
                    <h2 class="section-title">Standards you can rely on</h2>
                    <p class="compliance-body">
                        We hold ourselves to the highest standards of data protection and
                        regulatory compliance. Our security posture is transparent — if you
                        need documentation for a client audit or due diligence review, we
                        will provide it.
                    </p>
                    <a href="mailto:security@cpacrm.com" class="compliance-contact">
                        Contact our security team <ArrowRight :size="14" class="inline ml-1" />
                    </a>
                </div>
                <div class="compliance-list">
                    <div v-for="c in compliance" :key="c.label" class="compliance-item">
                        <CheckCircle :size="16" class="text-cpa-success flex-shrink-0 mt-0.5" />
                        <div>
                            <p class="compliance-item-label">{{ c.label }}</p>
                            <p class="compliance-item-desc">{{ c.desc }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- ── Responsible disclosure ── -->
    <section class="disclosure-section">
        <div class="marketing-container">
            <div class="disclosure-card">
                <Shield :size="28" class="text-cpa-medium-dark flex-shrink-0" />
                <div>
                    <h3 class="disclosure-title">Responsible disclosure policy</h3>
                    <p class="disclosure-body">
                        If you discover a security vulnerability in VJ CPA CRM, we encourage responsible
                        disclosure. Email us at
                        <a href="mailto:security@cpacrm.com" class="text-cpa-medium-dark hover:underline">security@cpacrm.com</a>
                        with a description of the issue. We will acknowledge your report within 48 hours,
                        investigate promptly, and keep you updated on our progress. We do not take legal action
                        against researchers who follow responsible disclosure guidelines.
                    </p>
                </div>
            </div>
        </div>
    </section>

    <!-- ── CTA ── -->
    <section class="cta-section">
        <div class="marketing-container text-center">
            <h2 class="cta-title">Ready to get started securely?</h2>
            <p class="cta-subtitle">Your data, your clients' data — protected at every layer.</p>
            <div class="flex flex-col sm:flex-row gap-3 justify-center mt-8">
                <Link href="/register" class="hero-btn-white">Start free trial <ArrowRight :size="15" /></Link>
                <Link href="/contact"  class="hero-btn-outline-white">Talk to us</Link>
            </div>
        </div>
    </section>
</template>

<style scoped>
.marketing-container { max-width: 1200px; margin: 0 auto; padding: 0 1.5rem; }
.section-eyebrow { font-size: 12px; font-weight: 700; letter-spacing: 1.2px; text-transform: uppercase; color: #1D9792; margin-bottom: .5rem; }
.section-title { font-size: clamp(1.5rem, 3vw, 2.25rem); font-weight: 800; color: #0D2B2A; letter-spacing: -.5px; line-height: 1.25; margin-bottom: .75rem; }

/* Hero */
.security-hero { padding: 5rem 0 3.5rem; background: linear-gradient(160deg, #F4FAFA 0%, #E6F5F4 60%, #fff 100%); }
.security-hero-icon { width: 72px; height: 72px; background: #E6F5F4; border-radius: 18px; display: flex; align-items: center; justify-content: center; margin: 0 auto 1.25rem; }
.security-title { font-size: clamp(1.75rem, 4vw, 3rem); font-weight: 800; color: #0D2B2A; letter-spacing: -1px; margin-bottom: .75rem; }
.security-subtitle { font-size: 1.05rem; color: #4D7374; max-width: 640px; margin: 0 auto 2rem; line-height: 1.75; }

.hero-btn-primary { display: inline-flex; align-items: center; gap: 8px; background: #1D9792; color: #fff; font-size: 15px; font-weight: 600; padding: 12px 24px; border-radius: 9px; text-decoration: none; transition: background .15s; border: none; cursor: pointer; }
.hero-btn-primary:hover { background: #055E5A; }

/* Pillars */
.pillars-section { padding: 5rem 0; background: #fff; }
.pillars-grid { display: grid; grid-template-columns: repeat(4, 1fr); gap: 1.25rem; }
@media (max-width: 1100px) { .pillars-grid { grid-template-columns: repeat(2, 1fr); } }
@media (max-width: 600px)  { .pillars-grid { grid-template-columns: 1fr; } }

.pillar-card { background: #F4FAFA; border: 1px solid #E6F5F4; border-radius: 12px; padding: 1.5rem; }
.pillar-icon { width: 42px; height: 42px; background: #E6F5F4; border-radius: 10px; display: flex; align-items: center; justify-content: center; color: #1D9792; margin-bottom: .875rem; }
.pillar-title { font-size: 14.5px; font-weight: 700; color: #0D2B2A; margin-bottom: .4rem; }
.pillar-desc { font-size: 13px; color: #6B7280; line-height: 1.65; }

/* Compliance */
.compliance-section { padding: 5rem 0; background: #F4FAFA; }
.compliance-grid { display: grid; grid-template-columns: 1fr 1.5fr; gap: 4rem; align-items: start; }
@media (max-width: 800px) { .compliance-grid { grid-template-columns: 1fr; } }
.compliance-body { font-size: 1rem; color: #6B7280; line-height: 1.75; margin-bottom: 1.25rem; }
.compliance-contact { display: inline-flex; align-items: center; font-size: 14px; font-weight: 600; color: #1D9792; text-decoration: none; }
.compliance-contact:hover { color: #055E5A; }
.compliance-list { display: flex; flex-direction: column; gap: 1rem; }
.compliance-item { display: flex; align-items: flex-start; gap: 12px; background: #fff; border: 1px solid #E6F5F4; border-radius: 10px; padding: 1rem 1.125rem; }
.compliance-item-label { font-size: 14px; font-weight: 700; color: #0D2B2A; margin-bottom: 3px; }
.compliance-item-desc { font-size: 13px; color: #6B7280; line-height: 1.55; }

/* Responsible disclosure */
.disclosure-section { padding: 3rem 0 4rem; background: #fff; }
.disclosure-card { display: flex; align-items: flex-start; gap: 1.25rem; background: #F4FAFA; border: 1px solid #D4ECEA; border-radius: 14px; padding: 1.75rem 2rem; }
.disclosure-title { font-size: 15.5px; font-weight: 700; color: #0D2B2A; margin-bottom: .5rem; }
.disclosure-body { font-size: 14px; color: #4B5563; line-height: 1.7; }

/* CTA */
.cta-section { padding: 5rem 0; background: linear-gradient(135deg, #055E5A 0%, #1D9792 100%); }
.cta-title { font-size: clamp(1.75rem, 4vw, 2.5rem); font-weight: 800; color: #fff; margin-bottom: .75rem; }
.cta-subtitle { font-size: 1.05rem; color: rgba(255,255,255,.8); line-height: 1.7; }
.hero-btn-white { display: inline-flex; align-items: center; gap: 8px; background: #fff; color: #055E5A; font-size: 15px; font-weight: 600; padding: 12px 24px; border-radius: 9px; text-decoration: none; transition: background .15s; }
.hero-btn-white:hover { background: #E6F5F4; }
.hero-btn-outline-white { display: inline-flex; align-items: center; gap: 6px; background: rgba(255,255,255,.1); color: #fff; font-size: 15px; font-weight: 500; padding: 12px 24px; border-radius: 9px; text-decoration: none; border: 1.5px solid rgba(255,255,255,.3); transition: background .15s; }
.hero-btn-outline-white:hover { background: rgba(255,255,255,.2); }
</style>
