<script setup lang="ts">
import MarketingLayout from '@/layouts/MarketingLayout.vue'
import SeoHead from '@/components/ui/SeoHead.vue'
import { Link } from '@inertiajs/vue3'
import { ArrowRight, Heart, Target, Users } from '@lucide/vue'

defineOptions({ layout: MarketingLayout })
defineProps<{ seo?: Record<string, unknown> }>()

const values = [
    { icon: Heart,  title: 'Built for accountants', desc: 'Every feature was designed in consultation with practicing CPAs. We understand the workflows because we have lived them.' },
    { icon: Target, title: 'Accuracy first',         desc: 'Financial data demands precision. We enforce double-entry accounting, immutable audit trails, and validated inputs throughout.' },
    { icon: Users,  title: 'Firm-first mindset',    desc: 'We build for the team, not just the individual. Capacity planning, role-based access, and multi-office support are core — not afterthoughts.' },
]
</script>

<template>
    <SeoHead :seo="(seo as any)" />

    <section class="page-hero">
        <div class="marketing-container text-center">
            <div class="section-eyebrow">About</div>
            <h1 class="page-hero-title">Built by accountants,<br/>for accountants</h1>
            <p class="page-hero-subtitle">
                VJ CPA CRM started as an internal tool for a multi-partner accounting firm.
                After years of frustration with disconnected spreadsheets, expensive legacy software,
                and tools that didn't understand how CPA practices actually work — we built our own.
            </p>
        </div>
    </section>

    <!-- Mission -->
    <section class="about-section">
        <div class="marketing-container about-grid">
            <div>
                <div class="section-eyebrow">Our mission</div>
                <h2 class="section-title">Make practice management effortless</h2>
                <p class="about-body">
                    CPA firms deserve software that works the way they work. Not generic project
                    management tools retrofitted for accounting. Not expensive enterprise systems
                    that take six months to implement.
                </p>
                <p class="about-body">
                    VJ CPA CRM is purpose-built for accounting practices — with filing engines,
                    tax profiles, engagement letters, client portals, and double-entry accounting
                    built in from the ground up.
                </p>
            </div>
            <div class="about-stat-box">
                <div class="about-stat" v-for="s in [
                    { val: '2019', label: 'Founded' },
                    { val: '2,500+', label: 'Active firms' },
                    { val: '1.2M', label: 'Filings tracked' },
                    { val: 'Canada', label: 'Headquartered' },
                ]" :key="s.label">
                    <div class="about-stat-val">{{ s.val }}</div>
                    <div class="about-stat-label">{{ s.label }}</div>
                </div>
            </div>
        </div>
    </section>

    <!-- Values -->
    <section class="values-section">
        <div class="marketing-container">
            <div class="section-eyebrow text-center">Our values</div>
            <h2 class="section-title text-center mb-10">How we think about building software</h2>
            <div class="values-grid">
                <div v-for="v in values" :key="v.title" class="value-card">
                    <div class="value-icon">
                        <component :is="v.icon" :size="22" />
                    </div>
                    <h3 class="value-title">{{ v.title }}</h3>
                    <p class="value-desc">{{ v.desc }}</p>
                </div>
            </div>
        </div>
    </section>

    <!-- CTA -->
    <section class="cta-section">
        <div class="marketing-container text-center">
            <h2 class="cta-title">Join thousands of CPA firms</h2>
            <p class="cta-subtitle">Start your free trial today. No credit card needed.</p>
            <div class="flex flex-col sm:flex-row gap-3 justify-center mt-8">
                <Link href="/register" class="hero-btn-primary">Start free trial <ArrowRight :size="15" /></Link>
                <Link href="/contact"  class="hero-btn-secondary">Get in touch</Link>
            </div>
        </div>
    </section>
</template>

<style scoped>
.marketing-container { max-width: 1200px; margin: 0 auto; padding: 0 1.5rem; }
.section-eyebrow { font-size: 12px; font-weight: 700; letter-spacing: 1.2px; text-transform: uppercase; color: #1D9792; margin-bottom: .5rem; }
.section-title { font-size: clamp(1.5rem, 3vw, 2.25rem); font-weight: 800; color: #0D2B2A; letter-spacing: -.5px; line-height: 1.25; margin-bottom: .75rem; }
.page-hero { padding: 5rem 0 3.5rem; background: linear-gradient(160deg, #F4FAFA 0%, #E6F5F4 60%, #fff 100%); }
.page-hero-title { font-size: clamp(2rem, 4vw, 3rem); font-weight: 800; color: #0D2B2A; letter-spacing: -1px; margin-bottom: .75rem; }
.page-hero-subtitle { font-size: 1.1rem; color: #4D7374; max-width: 620px; margin: 0 auto; line-height: 1.7; }
.about-section { padding: 5rem 0; background: #fff; }
.about-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 4rem; align-items: center; }
@media (max-width: 800px) { .about-grid { grid-template-columns: 1fr; } }
.about-body { font-size: 1rem; color: #4B5563; line-height: 1.75; margin-bottom: 1rem; }
.about-stat-box { display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem; }
.about-stat { background: #F4FAFA; border: 1px solid #E6F5F4; border-radius: 12px; padding: 1.5rem; text-align: center; }
.about-stat-val { font-size: 2rem; font-weight: 800; color: #055E5A; }
.about-stat-label { font-size: 12.5px; color: #6B9294; margin-top: 4px; }
.values-section { padding: 5rem 0; background: #F4FAFA; }
.values-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 1.5rem; }
@media (max-width: 800px) { .values-grid { grid-template-columns: 1fr; } }
.value-card { background: #fff; border: 1px solid #E6F5F4; border-radius: 14px; padding: 1.75rem; }
.value-icon { width: 44px; height: 44px; background: #E6F5F4; border-radius: 10px; display: flex; align-items: center; justify-content: center; color: #1D9792; margin-bottom: 1rem; }
.value-title { font-size: 15px; font-weight: 700; color: #0D2B2A; margin-bottom: .4rem; }
.value-desc { font-size: 13.5px; color: #6B7280; line-height: 1.65; }
.cta-section { padding: 5rem 0; background: linear-gradient(135deg, #055E5A 0%, #1D9792 100%); }
.cta-title { font-size: clamp(1.75rem, 4vw, 2.5rem); font-weight: 800; color: #fff; margin-bottom: .75rem; }
.cta-subtitle { font-size: 1.05rem; color: rgba(255,255,255,.8); line-height: 1.7; }
.hero-btn-primary { display: inline-flex; align-items: center; gap: 8px; background: #fff; color: #055E5A; font-size: 15px; font-weight: 600; padding: 12px 24px; border-radius: 9px; text-decoration: none; transition: background .15s; border: none; cursor: pointer; }
.hero-btn-primary:hover { background: #E6F5F4; }
.hero-btn-secondary { display: inline-flex; align-items: center; gap: 6px; background: rgba(255,255,255,.1); color: #fff; font-size: 15px; font-weight: 500; padding: 12px 24px; border-radius: 9px; text-decoration: none; border: 1.5px solid rgba(255,255,255,.3); cursor: pointer; }
.hero-btn-secondary:hover { background: rgba(255,255,255,.2); }
</style>
