<script setup lang="ts">
import MarketingLayout from '@/layouts/MarketingLayout.vue'
import SeoHead from '@/components/ui/SeoHead.vue'

defineOptions({ layout: MarketingLayout })
defineProps<{ seo?: Record<string, unknown> }>()

const lastUpdated = 'September 1, 2026'
</script>

<template>
    <SeoHead :seo="(seo as any)" />

    <section class="legal-hero">
        <div class="legal-container">
            <div class="section-eyebrow">Legal</div>
            <h1 class="legal-title">Terms of Service</h1>
            <p class="legal-meta">Last updated: {{ lastUpdated }}</p>
        </div>
    </section>

    <section class="legal-body">
        <div class="legal-container">
            <div class="legal-content">

                <p class="lead">
                    Please read these Terms of Service carefully before using VJ CPA CRM.
                    By accessing or using our platform, you agree to be bound by these terms.
                </p>

                <h2>1. Acceptance of Terms</h2>
                <p>
                    By creating an account or using VJ CPA CRM, you agree to these Terms of Service
                    and our Privacy Policy. If you do not agree, do not use the platform.
                </p>

                <h2>2. Description of Service</h2>
                <p>
                    VJ CPA CRM is a cloud-based practice management platform for accounting firms.
                    We provide tools for client management, filing tracking, document management,
                    workflow automation, invoicing, and accounting.
                </p>

                <h2>3. Account Registration</h2>
                <p>
                    You must provide accurate and complete information when creating an account.
                    You are responsible for maintaining the security of your account credentials and
                    for all activity that occurs under your account.
                </p>

                <h2>4. Acceptable Use</h2>
                <p>You agree not to:</p>
                <ul>
                    <li>Use the platform for any unlawful purpose</li>
                    <li>Upload malicious code or attempt to compromise security</li>
                    <li>Resell or sublicense the platform without written permission</li>
                    <li>Reverse engineer or copy any part of the platform</li>
                    <li>Use the platform to store or transmit infringing, harmful, or illegal content</li>
                </ul>

                <h2>5. Data Ownership</h2>
                <p>
                    You retain all ownership rights to data you upload or create within the platform.
                    By using the platform, you grant us a limited license to store and process your
                    data solely to provide the service.
                </p>

                <h2>6. Payment Terms</h2>
                <p>
                    Subscriptions are billed in advance on a monthly or annual basis.
                    All fees are non-refundable except as required by law.
                    We reserve the right to change pricing with 30 days' notice.
                </p>

                <h2>7. Free Trial</h2>
                <p>
                    We offer a 14-day free trial with full platform access. No credit card is required.
                    At the end of the trial, you must subscribe to continue using the platform or your
                    account will be suspended (data retained for 30 days).
                </p>

                <h2>8. Termination</h2>
                <p>
                    You may cancel your subscription at any time. We may suspend or terminate accounts
                    that violate these terms. Upon termination, your right to use the platform ceases
                    immediately.
                </p>

                <h2>9. Limitation of Liability</h2>
                <p>
                    To the maximum extent permitted by law, VJ CPA CRM shall not be liable for any
                    indirect, incidental, special, or consequential damages arising from your use of
                    the platform.
                </p>

                <h2>10. Governing Law</h2>
                <p>
                    These terms are governed by the laws of the Province of Ontario and the federal
                    laws of Canada applicable therein.
                </p>

                <h2>11. Contact</h2>
                <p>
                    Questions about these Terms? Contact us at
                    <a href="mailto:legal@cpacrm.com">legal@cpacrm.com</a>.
                </p>

            </div>
        </div>
    </section>
</template>

<style scoped>
.legal-hero { padding: 4rem 0 2rem; background: #F4FAFA; border-bottom: 1px solid #E6F5F4; }
.legal-container { max-width: 780px; margin: 0 auto; padding: 0 1.5rem; }
.section-eyebrow { font-size: 11px; font-weight: 700; letter-spacing: 1.2px; text-transform: uppercase; color: #1D9792; margin-bottom: .5rem; }
.legal-title { font-size: 2rem; font-weight: 800; color: #0D2B2A; letter-spacing: -.5px; margin-bottom: .25rem; }
.legal-meta { font-size: 13px; color: #9CA3AF; }
.legal-body { padding: 3rem 0 5rem; background: #fff; }
.legal-content { font-size: 14.5px; color: #374151; line-height: 1.8; }
.legal-content .lead { font-size: 1rem; color: #4B5563; margin-bottom: 2rem; }
.legal-content h2 { font-size: 1.1rem; font-weight: 700; color: #0D2B2A; margin: 2rem 0 .6rem; }
.legal-content p { margin-bottom: 1rem; }
.legal-content ul { margin: 0 0 1rem 1.25rem; list-style: disc; }
.legal-content li { margin-bottom: .4rem; }
.legal-content a { color: #1D9792; text-decoration: underline; }
.legal-content a:hover { color: #055E5A; }
</style>
