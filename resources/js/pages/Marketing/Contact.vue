<script setup lang="ts">
import MarketingLayout from '@/layouts/MarketingLayout.vue'
import SeoHead from '@/components/ui/SeoHead.vue'
import { useForm } from '@inertiajs/vue3'
import { Mail, Phone, MapPin, CheckCircle, Send } from '@lucide/vue'
import { computed } from 'vue'

defineOptions({ layout: MarketingLayout })

const props = defineProps<{
    seo?:    Record<string, unknown>
    status?: string
}>()

const form = useForm({
    name:    '',
    email:   '',
    company: '',
    subject: '',
    message: '',
})

function submit() {
    form.post('/contact', { onSuccess: () => form.reset() })
}

const namePh    = computed(() => form.errors.name    || 'Your full name')
const emailPh   = computed(() => form.errors.email   || 'you@yourfirm.com')
const subjectPh = computed(() => form.errors.subject || 'How can we help?')
const messagePh = computed(() => form.errors.message || 'Tell us about your firm and what you need...')
</script>

<template>
    <SeoHead :seo="(seo as any)" />

    <section class="page-hero">
        <div class="marketing-container text-center">
            <div class="section-eyebrow">Contact</div>
            <h1 class="page-hero-title">Get in touch</h1>
            <p class="page-hero-subtitle">
                Questions, demos, or just want to say hello — we're here.
            </p>
        </div>
    </section>

    <section class="contact-section">
        <div class="marketing-container">
            <div class="contact-grid">

                <!-- Info column -->
                <div class="contact-info">
                    <h2 class="contact-info-title">Let's talk</h2>
                    <p class="contact-info-body">
                        Whether you're evaluating VJ CPA CRM, need a product demo, or have a support
                        question — our team typically responds within one business day.
                    </p>

                    <div class="contact-details">
                        <div class="contact-detail">
                            <div class="contact-detail-icon"><Mail :size="18" /></div>
                            <div>
                                <div class="contact-detail-label">Email</div>
                                <a href="mailto:support@cpacrm.com" class="contact-detail-value">support@cpacrm.com</a>
                            </div>
                        </div>
                        <div class="contact-detail">
                            <div class="contact-detail-icon"><Phone :size="18" /></div>
                            <div>
                                <div class="contact-detail-label">Phone</div>
                                <a href="tel:+14165550100" class="contact-detail-value">+1 (416) 555-0100</a>
                            </div>
                        </div>
                        <div class="contact-detail">
                            <div class="contact-detail-icon"><MapPin :size="18" /></div>
                            <div>
                                <div class="contact-detail-label">Office</div>
                                <span class="contact-detail-value">Brampton, ON, Canada</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Form column -->
                <div class="contact-form-card">
                    <!-- Success state -->
                    <div v-if="status === 'sent'" class="contact-success">
                        <CheckCircle :size="32" class="text-cpa-success" />
                        <h3 class="contact-success-title">Message sent!</h3>
                        <p class="contact-success-body">
                            Thanks for reaching out. We'll reply within one business day.
                        </p>
                    </div>

                    <form v-else @submit.prevent="submit" class="contact-form" novalidate>
                        <div class="form-row">
                            <div class="form-group">
                                <label class="form-label required">Full name</label>
                                <input
                                    v-model="form.name" type="text" autocomplete="name"
                                    :placeholder="namePh" :class="['form-input', { error: form.errors.name }]"
                                    :aria-invalid="!!form.errors.name"
                                />
                            </div>
                            <div class="form-group">
                                <label class="form-label required">Email</label>
                                <input
                                    v-model="form.email" type="email" autocomplete="email"
                                    :placeholder="emailPh" :class="['form-input', { error: form.errors.email }]"
                                    :aria-invalid="!!form.errors.email"
                                />
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Company / Firm name</label>
                            <input v-model="form.company" type="text" placeholder="Kambo & Associates CPA" class="form-input" />
                        </div>
                        <div class="form-group">
                            <label class="form-label required">Subject</label>
                            <input
                                v-model="form.subject" type="text"
                                :placeholder="subjectPh" :class="['form-input', { error: form.errors.subject }]"
                                :aria-invalid="!!form.errors.subject"
                            />
                        </div>
                        <div class="form-group">
                            <label class="form-label required">Message</label>
                            <textarea
                                v-model="form.message" rows="5"
                                :placeholder="messagePh"
                                :class="['form-input resize-none', { error: form.errors.message }]"
                                :aria-invalid="!!form.errors.message"
                            />
                        </div>
                        <button type="submit" class="contact-submit" :disabled="form.processing">
                            <span v-if="form.processing" class="inline-block w-4 h-4 border-2 border-white/40 border-t-white rounded-full animate-spin" />
                            <Send v-else :size="15" />
                            {{ form.processing ? 'Sending…' : 'Send message' }}
                        </button>
                    </form>
                </div>

            </div>
        </div>
    </section>
</template>

<style scoped>
.marketing-container { max-width: 1200px; margin: 0 auto; padding: 0 1.5rem; }
.section-eyebrow { font-size: 12px; font-weight: 700; letter-spacing: 1.2px; text-transform: uppercase; color: #1D9792; margin-bottom: .5rem; }
.page-hero { padding: 5rem 0 3rem; background: linear-gradient(160deg, #F4FAFA 0%, #E6F5F4 60%, #fff 100%); }
.page-hero-title { font-size: clamp(2rem, 4vw, 3rem); font-weight: 800; color: #0D2B2A; letter-spacing: -1px; margin-bottom: .75rem; }
.page-hero-subtitle { font-size: 1.1rem; color: #4D7374; max-width: 500px; margin: 0 auto; line-height: 1.7; }
.contact-section { padding: 4rem 0 5rem; background: #fff; }
.contact-grid { display: grid; grid-template-columns: 1fr 2fr; gap: 4rem; align-items: start; }
@media (max-width: 800px) { .contact-grid { grid-template-columns: 1fr; gap: 2.5rem; } }
.contact-info-title { font-size: 1.4rem; font-weight: 800; color: #0D2B2A; margin-bottom: .75rem; }
.contact-info-body { font-size: 14px; color: #6B7280; line-height: 1.7; margin-bottom: 2rem; }
.contact-details { display: flex; flex-direction: column; gap: 1.25rem; }
.contact-detail { display: flex; gap: 12px; align-items: flex-start; }
.contact-detail-icon { width: 36px; height: 36px; background: #E6F5F4; border-radius: 9px; display: flex; align-items: center; justify-content: center; color: #1D9792; flex-shrink: 0; }
.contact-detail-label { font-size: 11px; font-weight: 600; text-transform: uppercase; letter-spacing: .6px; color: #9CA3AF; margin-bottom: 2px; }
.contact-detail-value { font-size: 14px; color: #374151; text-decoration: none; }
a.contact-detail-value:hover { color: #1D9792; }
.contact-form-card { background: #F4FAFA; border: 1px solid #E6F5F4; border-radius: 16px; padding: 2rem; }
.contact-form { display: flex; flex-direction: column; gap: 1rem; }
.form-row { display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; }
@media (max-width: 560px) { .form-row { grid-template-columns: 1fr; } }
.form-group { display: flex; flex-direction: column; gap: 4px; }
.form-label { font-size: 12.5px; font-weight: 600; color: #374151; }
.form-label.required::after { content: ' *'; color: #DC2626; }
.form-input { width: 100%; padding: 9px 12px; border: 1.5px solid #D4ECEA; border-radius: 8px; background: #fff; font-size: 13.5px; color: #0D2B2A; outline: none; transition: border-color .12s, box-shadow .12s; font-family: inherit; }
.form-input::placeholder { color: #9CA3AF; }
.form-input:focus { border-color: #1D9792; box-shadow: 0 0 0 3px rgba(29,151,146,.12); }
.form-input.error { border-color: #DC2626; }
.form-input.error::placeholder { color: #DC2626; font-weight: 500; }
.contact-submit { display: flex; align-items: center; justify-content: center; gap: 8px; background: #1D9792; color: #fff; font-size: 14.5px; font-weight: 600; padding: 11px 24px; border-radius: 9px; border: none; cursor: pointer; width: 100%; transition: background .15s; }
.contact-submit:hover { background: #055E5A; }
.contact-submit:disabled { opacity: .6; pointer-events: none; }
.contact-success { text-align: center; padding: 2.5rem 1rem; display: flex; flex-direction: column; align-items: center; gap: 1rem; }
.contact-success-title { font-size: 1.2rem; font-weight: 700; color: #0D2B2A; }
.contact-success-body { font-size: 14px; color: #6B7280; line-height: 1.6; max-width: 320px; }
</style>
