<script setup lang="ts">
import MarketingLayout from '@/layouts/MarketingLayout.vue'
import SeoHead from '@/components/ui/SeoHead.vue'
import { Link } from '@inertiajs/vue3'
import { CheckCircle, ArrowRight, Zap, Shield, Building2 } from '@lucide/vue'

defineOptions({ layout: MarketingLayout })
defineProps<{ seo?: Record<string, unknown> }>()

const plans = [
    {
        name: 'Starter',
        icon: Zap,
        price: 49,
        period: '/month',
        desc: 'Perfect for solo practitioners and small firms just getting started.',
        cta: 'Start free trial',
        href: '/register',
        highlight: false,
        features: [
            'Up to 50 active clients',
            'Filing & deadline tracking',
            'Document storage (10 GB)',
            'Client portal',
            'Basic invoicing',
            'Email support',
        ],
    },
    {
        name: 'Professional',
        icon: Shield,
        price: 99,
        period: '/month',
        desc: 'The most popular plan for growing CPA practices.',
        cta: 'Start free trial',
        href: '/register',
        highlight: true,
        badge: 'Most popular',
        features: [
            'Up to 250 active clients',
            'Everything in Starter',
            'Workflow & task automation',
            'E-signatures',
            'Full accounting module',
            'Time tracking',
            'Document storage (100 GB)',
            'Priority email & chat support',
        ],
    },
    {
        name: 'Enterprise',
        icon: Building2,
        price: 199,
        period: '/month',
        desc: 'For large firms and multi-office practices that need full control.',
        cta: 'Contact sales',
        href: '/contact',
        highlight: false,
        features: [
            'Unlimited clients',
            'Everything in Professional',
            'Multi-office management',
            'Custom roles & permissions',
            'AI assistant',
            'API & webhook access',
            'Unlimited document storage',
            'Dedicated onboarding & SLA',
        ],
    },
]

const faqs = [
    { q: 'Is there a free trial?', a: 'Yes — every plan includes a full 14-day free trial. No credit card is required to start.' },
    { q: 'Can I change plans later?', a: 'Absolutely. You can upgrade or downgrade at any time. Changes take effect at the next billing cycle.' },
    { q: 'Are there setup fees?', a: 'None. We include a free onboarding call and data migration assistance for all plans.' },
    { q: 'How is billing handled?', a: 'Plans are billed monthly or annually (annual saves 20%). We accept all major credit cards.' },
    { q: 'Is my data secure?', a: 'All data is encrypted at rest and in transit. We use per-tenant isolation so your data is never shared.' },
    { q: 'Do you offer discounts for non-profits?', a: 'Yes — contact us for special non-profit and educational pricing.' },
]
</script>

<template>
    <SeoHead :seo="(seo as any)" />

    <!-- Hero -->
    <section class="page-hero">
        <div class="marketing-container text-center">
            <div class="section-eyebrow">Pricing</div>
            <h1 class="page-hero-title">Simple, honest pricing</h1>
            <p class="page-hero-subtitle">
                No hidden fees. No per-user charges. Start free for 14 days.
            </p>
        </div>
    </section>

    <!-- Plans -->
    <section class="plans-section">
        <div class="marketing-container">
            <div class="plans-grid">
                <div
                    v-for="plan in plans"
                    :key="plan.name"
                    class="plan-card"
                    :class="{ 'plan-card-highlight': plan.highlight }"
                >
                    <div v-if="plan.badge" class="plan-badge">{{ plan.badge }}</div>

                    <div class="plan-icon">
                        <component :is="plan.icon" :size="20" />
                    </div>

                    <h2 class="plan-name">{{ plan.name }}</h2>
                    <p class="plan-desc">{{ plan.desc }}</p>

                    <div class="plan-price">
                        <span class="plan-price-currency">CA$</span>
                        <span class="plan-price-amount">{{ plan.price }}</span>
                        <span class="plan-price-period">{{ plan.period }}</span>
                    </div>

                    <Link :href="plan.href" class="plan-cta" :class="{ 'plan-cta-highlight': plan.highlight }">
                        {{ plan.cta }} <ArrowRight :size="14" />
                    </Link>

                    <ul class="plan-features">
                        <li v-for="feat in plan.features" :key="feat" class="plan-feature">
                            <CheckCircle :size="14" class="flex-shrink-0 mt-0.5"
                                :class="plan.highlight ? 'text-cpa-medium' : 'text-cpa-success'" />
                            {{ feat }}
                        </li>
                    </ul>
                </div>
            </div>
            <p class="plans-note">All prices in Canadian dollars. Annual billing saves 20%.</p>
        </div>
    </section>

    <!-- FAQ -->
    <section class="faq-section">
        <div class="marketing-container">
            <h2 class="section-title text-center mb-10">Frequently asked questions</h2>
            <div class="faq-grid">
                <div v-for="faq in faqs" :key="faq.q" class="faq-item">
                    <h3 class="faq-q">{{ faq.q }}</h3>
                    <p class="faq-a">{{ faq.a }}</p>
                </div>
            </div>
        </div>
    </section>

    <!-- CTA -->
    <section class="cta-section">
        <div class="marketing-container text-center">
            <h2 class="cta-title">Still have questions?</h2>
            <p class="cta-subtitle">Talk to our team. We'll help you find the right plan for your firm.</p>
            <div class="flex flex-col sm:flex-row gap-3 justify-center mt-8">
                <Link href="/register" class="hero-btn-primary">Start 14-day free trial <ArrowRight :size="15" /></Link>
                <Link href="/contact"  class="hero-btn-secondary">Contact sales</Link>
            </div>
        </div>
    </section>
</template>

<style scoped>
.marketing-container { max-width: 1200px; margin: 0 auto; padding: 0 1.5rem; }
.section-eyebrow { font-size: 12px; font-weight: 700; letter-spacing: 1.2px; text-transform: uppercase; color: #1D9792; margin-bottom: .5rem; }
.section-title { font-size: clamp(1.5rem, 3vw, 2.25rem); font-weight: 800; color: #0D2B2A; letter-spacing: -.5px; line-height: 1.25; margin-bottom: .75rem; }

.page-hero { padding: 5rem 0 3rem; background: linear-gradient(160deg, #F4FAFA 0%, #E6F5F4 60%, #fff 100%); }
.page-hero-title { font-size: clamp(2rem, 4vw, 3rem); font-weight: 800; color: #0D2B2A; letter-spacing: -1px; margin-bottom: .75rem; }
.page-hero-subtitle { font-size: 1.1rem; color: #4D7374; max-width: 500px; margin: 0 auto; line-height: 1.7; }

.plans-section { padding: 4rem 0 3rem; background: #fff; }
.plans-grid {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 1.5rem;
    align-items: start;
}
@media (max-width: 900px) { .plans-grid { grid-template-columns: 1fr; max-width: 420px; margin: 0 auto; } }

.plan-card {
    background: #fff;
    border: 1.5px solid #E5E7EB;
    border-radius: 16px;
    padding: 2rem;
    position: relative;
    transition: box-shadow .15s;
}
.plan-card:hover { box-shadow: 0 8px 32px rgba(2,62,60,.08); }
.plan-card-highlight {
    border-color: #1D9792;
    box-shadow: 0 0 0 3px rgba(29,151,146,.12), 0 8px 32px rgba(2,62,60,.1);
    background: linear-gradient(180deg, #F4FAFA 0%, #fff 100%);
}
.plan-badge {
    position: absolute; top: -12px; left: 50%; transform: translateX(-50%);
    background: #1D9792; color: #fff;
    font-size: 11px; font-weight: 700; padding: 4px 12px; border-radius: 9999px;
    white-space: nowrap;
}
.plan-icon {
    width: 40px; height: 40px; background: #E6F5F4;
    border-radius: 10px; display: flex; align-items: center; justify-content: center;
    color: #1D9792; margin-bottom: .875rem;
}
.plan-card-highlight .plan-icon { background: #C5E8E5; }
.plan-name { font-size: 20px; font-weight: 800; color: #0D2B2A; margin-bottom: .25rem; }
.plan-desc { font-size: 13px; color: #6B7280; line-height: 1.5; margin-bottom: 1.25rem; }
.plan-price {
    display: flex; align-items: baseline; gap: 4px;
    margin-bottom: 1.25rem;
}
.plan-price-currency { font-size: 16px; font-weight: 600; color: #374151; margin-top: 4px; }
.plan-price-amount { font-size: 3rem; font-weight: 800; color: #0D2B2A; line-height: 1; }
.plan-price-period { font-size: 14px; color: #9CA3AF; }

.plan-cta {
    display: flex; align-items: center; justify-content: center; gap: 6px;
    width: 100%; padding: 10px;
    border-radius: 8px; font-size: 14px; font-weight: 600;
    text-decoration: none; transition: background .15s;
    background: #F3F4F6; color: #374151; border: none; cursor: pointer;
    margin-bottom: 1.5rem;
}
.plan-cta:hover { background: #E6F5F4; color: #055E5A; }
.plan-cta-highlight { background: #1D9792; color: #fff; }
.plan-cta-highlight:hover { background: #055E5A; color: #fff; }

.plan-features { list-style: none; padding: 0; margin: 0; display: flex; flex-direction: column; gap: 8px; }
.plan-feature { display: flex; align-items: flex-start; gap: 8px; font-size: 13px; color: #374151; }

.plans-note { text-align: center; font-size: 12.5px; color: #9CA3AF; margin-top: 1.5rem; }

.faq-section { padding: 5rem 0; background: #F4FAFA; }
.faq-grid { display: grid; grid-template-columns: repeat(2, 1fr); gap: 2rem; }
@media (max-width: 700px) { .faq-grid { grid-template-columns: 1fr; } }
.faq-item { padding: 1.5rem; background: #fff; border-radius: 12px; border: 1px solid #E6F5F4; }
.faq-q { font-size: 14.5px; font-weight: 700; color: #0D2B2A; margin-bottom: .5rem; }
.faq-a { font-size: 13.5px; color: #6B7280; line-height: 1.65; }

.cta-section { padding: 5rem 0; background: linear-gradient(135deg, #055E5A 0%, #1D9792 100%); }
.cta-title { font-size: clamp(1.75rem, 4vw, 2.5rem); font-weight: 800; color: #fff; margin-bottom: .75rem; }
.cta-subtitle { font-size: 1.05rem; color: rgba(255,255,255,.8); line-height: 1.7; }
.hero-btn-primary { display: inline-flex; align-items: center; gap: 8px; background: #fff; color: #055E5A; font-size: 15px; font-weight: 600; padding: 12px 24px; border-radius: 9px; text-decoration: none; transition: background .15s; border: none; cursor: pointer; }
.hero-btn-primary:hover { background: #E6F5F4; }
.hero-btn-secondary { display: inline-flex; align-items: center; gap: 6px; background: rgba(255,255,255,.1); color: #fff; font-size: 15px; font-weight: 500; padding: 12px 24px; border-radius: 9px; text-decoration: none; border: 1.5px solid rgba(255,255,255,.3); transition: background .15s; cursor: pointer; }
.hero-btn-secondary:hover { background: rgba(255,255,255,.2); }
</style>
