<script setup lang="ts">
import MarketingLayout from '@/layouts/MarketingLayout.vue'
import SeoHead from '@/components/ui/SeoHead.vue'
import { Link } from '@inertiajs/vue3'
import { useForm } from '@inertiajs/vue3'
import { computed } from 'vue'
import {
    Play, CheckCircle, Users, FileText, Clock,
    BarChart2, ArrowRight, Workflow,
} from '@lucide/vue'

defineOptions({ layout: MarketingLayout })
defineProps<{ seo?: Record<string, unknown>; status?: string }>()

const form = useForm({
    name:    '',
    email:   '',
    company: '',
    size:    '',
})

function submit() {
    form.post('/demo/request', { onSuccess: () => form.reset() })
}

const namePh    = computed(() => form.errors.name    || 'Your full name')
const emailPh   = computed(() => form.errors.email   || 'Work email address')
const companyPh = computed(() => form.errors.company || 'Firm name')

const features = [
    { icon: Users,      label: 'Client management &amp; 360° profiles' },
    { icon: FileText,   label: 'Filing engine with auto deadlines' },
    { icon: Workflow,   label: 'Workflow &amp; task automation' },
    { icon: Clock,      label: 'Time tracking &amp; capacity planning' },
    { icon: BarChart2,  label: 'Reporting &amp; analytics dashboard' },
    { icon: ArrowRight, label: 'Client portal &amp; e-signatures' },
]

const testimonials = [
    { name: 'Gagan Kambo', role: 'Partner, Kambo & Associates CPA', text: 'We cut our T1 season admin time by 35%. The workflow templates alone were worth the switch.' },
    { name: 'Sarah Chen', role: 'Practice Manager, Chen & Co.', text: 'The client portal eliminated 60% of our document chasing emails. Clients actually use it.' },
    { name: 'Tom Bradley', role: 'Director, Bradley Tax Group', text: 'Finally a platform built for Canadian CPA firms — not a US product with Canadian workarounds.' },
]
</script>

<template>
    <SeoHead :seo="(seo as any)" />

    <!-- ── Hero ── -->
    <section class="demo-hero">
        <div class="marketing-container">
            <div class="demo-hero-grid">

                <!-- Left: copy -->
                <div class="demo-hero-copy">
                    <div class="section-eyebrow">Product Demo</div>
                    <h1 class="demo-title">See VJ CPA CRM in action</h1>
                    <p class="demo-subtitle">
                        Watch how thousands of CPA firms manage clients, track filings, automate
                        workflows, and grow their practice — all from one platform.
                    </p>

                    <!-- Feature list -->
                    <ul class="demo-feature-list">
                        <li v-for="f in features" :key="f.label" class="demo-feature-item">
                            <CheckCircle :size="15" class="text-cpa-success flex-shrink-0 mt-0.5" />
                            <span v-html="f.label" />
                        </li>
                    </ul>

                    <!-- Video embed placeholder -->
                    <div class="demo-video">
                        <div class="demo-video-inner">
                            <!-- Replace src with real Loom/Wistia/YouTube embed URL -->
                            <div class="demo-video-placeholder">
                                <button class="demo-play-btn" aria-label="Play demo video">
                                    <Play :size="28" class="ml-1" />
                                </button>
                                <p class="demo-video-label">5-minute product walkthrough</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Right: request form -->
                <div class="demo-form-card">
                    <!-- Success state -->
                    <div v-if="status === 'sent'" class="demo-success">
                        <CheckCircle :size="36" class="text-cpa-success" />
                        <h3>Demo request received!</h3>
                        <p>Our team will reach out within one business day to schedule your personalised walkthrough.</p>
                        <Link href="/" class="demo-success-link">Back to home</Link>
                    </div>

                    <template v-else>
                        <h2 class="demo-form-title">Request a personal demo</h2>
                        <p class="demo-form-subtitle">
                            Get a 30-minute live walkthrough with a product specialist tailored to your firm's needs.
                        </p>

                        <form @submit.prevent="submit" class="demo-form" novalidate>
                            <div class="form-group">
                                <label class="form-label required">Full name</label>
                                <input
                                    v-model="form.name" type="text" autocomplete="name"
                                    :placeholder="namePh"
                                    :class="['form-input', { error: form.errors.name }]"
                                />
                            </div>
                            <div class="form-group">
                                <label class="form-label required">Work email</label>
                                <input
                                    v-model="form.email" type="email" autocomplete="email"
                                    :placeholder="emailPh"
                                    :class="['form-input', { error: form.errors.email }]"
                                />
                            </div>
                            <div class="form-group">
                                <label class="form-label required">Firm name</label>
                                <input
                                    v-model="form.company" type="text" autocomplete="organization"
                                    :placeholder="companyPh"
                                    :class="['form-input', { error: form.errors.company }]"
                                />
                            </div>
                            <div class="form-group">
                                <label class="form-label">Team size</label>
                                <select v-model="form.size" class="form-input">
                                    <option value="">Select team size…</option>
                                    <option value="1">Solo practitioner</option>
                                    <option value="2-5">2–5 staff</option>
                                    <option value="6-15">6–15 staff</option>
                                    <option value="16-30">16–30 staff</option>
                                    <option value="31+">31+ staff</option>
                                </select>
                            </div>
                            <button type="submit" class="demo-submit" :disabled="form.processing">
                                <span v-if="form.processing" class="inline-block w-4 h-4 border-2 border-white/40 border-t-white rounded-full animate-spin" />
                                {{ form.processing ? 'Sending…' : 'Request my demo' }}
                                <ArrowRight v-if="!form.processing" :size="15" />
                            </button>
                        </form>

                        <p class="demo-form-note">
                            Or <Link href="/register" class="text-cpa-medium-dark hover:underline font-medium">start a free 14-day trial</Link> right now — no demo needed.
                        </p>
                    </template>
                </div>

            </div>
        </div>
    </section>

    <!-- ── Testimonials ── -->
    <section class="testimonials-section">
        <div class="marketing-container">
            <div class="section-eyebrow text-center">What firms say</div>
            <h2 class="section-title text-center mb-10">Trusted by CPA firms across Canada</h2>
            <div class="testimonials-grid">
                <div v-for="t in testimonials" :key="t.name" class="testimonial-card">
                    <p class="testimonial-text">"{{ t.text }}"</p>
                    <div class="testimonial-author">
                        <div class="testimonial-avatar">{{ t.name.split(' ').map(n=>n[0]).join('') }}</div>
                        <div>
                            <p class="testimonial-name">{{ t.name }}</p>
                            <p class="testimonial-role">{{ t.role }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- ── Bottom CTA ── -->
    <section class="cta-section">
        <div class="marketing-container text-center">
            <h2 class="cta-title">Not ready for a demo?</h2>
            <p class="cta-subtitle">Try VJ CPA CRM free for 14 days. Full access, no credit card.</p>
            <div class="flex flex-col sm:flex-row gap-3 justify-center mt-8">
                <Link href="/register" class="hero-btn-white">Start free trial <ArrowRight :size="15" /></Link>
                <Link href="/pricing"  class="hero-btn-outline-white">View pricing</Link>
            </div>
        </div>
    </section>
</template>

<style scoped>
.marketing-container { max-width: 1200px; margin: 0 auto; padding: 0 1.5rem; }
.section-eyebrow { font-size: 12px; font-weight: 700; letter-spacing: 1.2px; text-transform: uppercase; color: #1D9792; margin-bottom: .5rem; }
.section-title { font-size: clamp(1.5rem, 3vw, 2.25rem); font-weight: 800; color: #0D2B2A; letter-spacing: -.5px; line-height: 1.25; margin-bottom: .75rem; }

/* Hero */
.demo-hero { padding: 5rem 0 3.5rem; background: linear-gradient(160deg, #F4FAFA 0%, #E6F5F4 50%, #fff 100%); }
.demo-hero-grid { display: grid; grid-template-columns: 1fr 400px; gap: 4rem; align-items: start; }
@media (max-width: 900px) { .demo-hero-grid { grid-template-columns: 1fr; } }

.demo-title { font-size: clamp(1.75rem, 4vw, 2.75rem); font-weight: 800; color: #0D2B2A; letter-spacing: -1px; margin-bottom: .75rem; line-height: 1.15; }
.demo-subtitle { font-size: 1.05rem; color: #4D7374; line-height: 1.7; margin-bottom: 1.5rem; }

.demo-feature-list { list-style: none; padding: 0; margin: 0 0 2rem; display: flex; flex-direction: column; gap: 8px; }
.demo-feature-item { display: flex; align-items: flex-start; gap: 8px; font-size: 14px; color: #374151; }

.demo-video { border-radius: 14px; overflow: hidden; box-shadow: 0 8px 32px rgba(2,62,60,.1); }
.demo-video-inner { aspect-ratio: 16/9; background: linear-gradient(135deg, #055E5A 0%, #1D9792 100%); position: relative; }
.demo-video-placeholder { position: absolute; inset: 0; display: flex; flex-direction: column; align-items: center; justify-content: center; gap: .75rem; }
.demo-play-btn { width: 68px; height: 68px; background: rgba(255,255,255,.15); backdrop-filter: blur(8px); border: 2px solid rgba(255,255,255,.4); border-radius: 50%; display: flex; align-items: center; justify-content: center; color: #fff; cursor: pointer; transition: background .15s, transform .15s; }
.demo-play-btn:hover { background: rgba(255,255,255,.25); transform: scale(1.06); }
.demo-video-label { font-size: 13.5px; font-weight: 500; color: rgba(255,255,255,.8); }

/* Form card */
.demo-form-card { background: #fff; border: 1px solid #D4ECEA; border-radius: 16px; padding: 2rem; box-shadow: 0 4px 24px rgba(2,62,60,.07); }
.demo-form-title { font-size: 1.2rem; font-weight: 800; color: #0D2B2A; margin-bottom: .35rem; }
.demo-form-subtitle { font-size: 13px; color: #6B7280; line-height: 1.6; margin-bottom: 1.25rem; }
.demo-form { display: flex; flex-direction: column; gap: .875rem; }
.form-group { display: flex; flex-direction: column; gap: 4px; }
.form-label { font-size: 12.5px; font-weight: 600; color: #374151; }
.form-label.required::after { content: ' *'; color: #DC2626; }
.form-input { width: 100%; padding: 9px 12px; border: 1.5px solid #D4ECEA; border-radius: 8px; background: #fff; font-size: 13.5px; color: #0D2B2A; outline: none; transition: border-color .12s, box-shadow .12s; font-family: inherit; }
.form-input::placeholder { color: #9CA3AF; }
.form-input:focus { border-color: #1D9792; box-shadow: 0 0 0 3px rgba(29,151,146,.12); }
.form-input.error { border-color: #DC2626; }
.form-input.error::placeholder { color: #DC2626; font-weight: 500; }
.demo-submit { display: flex; align-items: center; justify-content: center; gap: 8px; background: #1D9792; color: #fff; font-size: 14.5px; font-weight: 600; padding: 11px; border-radius: 9px; border: none; cursor: pointer; width: 100%; transition: background .15s; margin-top: .25rem; }
.demo-submit:hover { background: #055E5A; }
.demo-submit:disabled { opacity: .6; pointer-events: none; }
.demo-form-note { font-size: 12.5px; color: #9CA3AF; text-align: center; margin-top: .875rem; }

.demo-success { display: flex; flex-direction: column; align-items: center; gap: .875rem; padding: 2rem 0; text-align: center; }
.demo-success h3 { font-size: 1.1rem; font-weight: 700; color: #0D2B2A; }
.demo-success p { font-size: 13.5px; color: #6B7280; line-height: 1.6; max-width: 300px; }
.demo-success-link { font-size: 13.5px; font-weight: 600; color: #1D9792; text-decoration: none; }

/* Testimonials */
.testimonials-section { padding: 5rem 0; background: #F4FAFA; }
.testimonials-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 1.5rem; }
@media (max-width: 800px) { .testimonials-grid { grid-template-columns: 1fr; } }
.testimonial-card { background: #fff; border: 1px solid #E6F5F4; border-radius: 14px; padding: 1.75rem; }
.testimonial-text { font-size: 14.5px; color: #374151; line-height: 1.75; font-style: italic; margin-bottom: 1.25rem; }
.testimonial-author { display: flex; align-items: center; gap: 10px; }
.testimonial-avatar { width: 40px; height: 40px; border-radius: 50%; background: #1D9792; color: #fff; font-size: 13px; font-weight: 700; display: flex; align-items: center; justify-content: center; flex-shrink: 0; }
.testimonial-name { font-size: 13.5px; font-weight: 700; color: #0D2B2A; }
.testimonial-role { font-size: 12px; color: #9CA3AF; }

/* CTA */
.cta-section { padding: 5rem 0; background: linear-gradient(135deg, #055E5A 0%, #1D9792 100%); }
.cta-title { font-size: clamp(1.75rem, 4vw, 2.5rem); font-weight: 800; color: #fff; margin-bottom: .75rem; }
.cta-subtitle { font-size: 1.05rem; color: rgba(255,255,255,.8); line-height: 1.7; }
.hero-btn-white { display: inline-flex; align-items: center; gap: 8px; background: #fff; color: #055E5A; font-size: 15px; font-weight: 600; padding: 12px 24px; border-radius: 9px; text-decoration: none; transition: background .15s; border: none; cursor: pointer; }
.hero-btn-white:hover { background: #E6F5F4; }
.hero-btn-outline-white { display: inline-flex; align-items: center; gap: 6px; background: rgba(255,255,255,.1); color: #fff; font-size: 15px; font-weight: 500; padding: 12px 24px; border-radius: 9px; text-decoration: none; border: 1.5px solid rgba(255,255,255,.3); transition: background .15s; cursor: pointer; }
.hero-btn-outline-white:hover { background: rgba(255,255,255,.2); }
</style>
