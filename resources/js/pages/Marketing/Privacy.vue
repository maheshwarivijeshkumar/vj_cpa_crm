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
            <h1 class="legal-title">Privacy Policy</h1>
            <p class="legal-meta">Last updated: {{ lastUpdated }}</p>
        </div>
    </section>

    <section class="legal-body">
        <div class="legal-container">
            <div class="legal-content">

                <p class="lead">
                    VJ CPA CRM ("we", "our", or "us") is committed to protecting your privacy.
                    This Privacy Policy explains how we collect, use, disclose, and safeguard your
                    information when you use our platform.
                </p>

                <h2>1. Information We Collect</h2>
                <p>We collect information you provide directly, including:</p>
                <ul>
                    <li>Account registration details (name, email, firm name)</li>
                    <li>Client data entered into the platform</li>
                    <li>Documents and files uploaded to the system</li>
                    <li>Payment and billing information (processed by our payment provider)</li>
                    <li>Communications sent through the platform</li>
                </ul>

                <p>We also collect information automatically:</p>
                <ul>
                    <li>Log data (IP address, browser type, pages visited, time/date)</li>
                    <li>Device information</li>
                    <li>Cookies and similar tracking technologies</li>
                </ul>

                <h2>2. How We Use Your Information</h2>
                <p>We use the information we collect to:</p>
                <ul>
                    <li>Provide, maintain, and improve our services</li>
                    <li>Process transactions and send related information</li>
                    <li>Send administrative messages, updates, and security alerts</li>
                    <li>Respond to comments and questions</li>
                    <li>Monitor and analyse usage trends to improve the platform</li>
                    <li>Detect and prevent fraudulent transactions and other illegal activities</li>
                    <li>Comply with legal obligations</li>
                </ul>

                <h2>3. Data Sharing and Disclosure</h2>
                <p>We do not sell, trade, or rent your personal information to third parties. We may share your information with:</p>
                <ul>
                    <li><strong>Service providers</strong> who assist in operating our platform (hosting, email delivery, payment processing)</li>
                    <li><strong>Legal authorities</strong> when required by law or to protect our rights</li>
                    <li><strong>Business transfers</strong> in connection with a merger, acquisition, or sale of assets</li>
                </ul>

                <h2>4. Data Security</h2>
                <p>
                    We implement industry-standard security measures including encryption at rest and in transit,
                    multi-tenant data isolation, audit logging, and access controls. However, no method of
                    transmission over the internet is 100% secure.
                </p>

                <h2>5. Data Retention</h2>
                <p>
                    We retain your data for as long as your account is active or as needed to provide services.
                    Upon account termination, data is retained for 90 days before permanent deletion, unless
                    required by law.
                </p>

                <h2>6. Your Rights</h2>
                <p>Depending on your location, you may have the right to:</p>
                <ul>
                    <li>Access the personal data we hold about you</li>
                    <li>Request correction of inaccurate data</li>
                    <li>Request deletion of your personal data</li>
                    <li>Object to or restrict processing</li>
                    <li>Data portability</li>
                </ul>
                <p>To exercise these rights, contact us at <a href="mailto:privacy@cpacrm.com">privacy@cpacrm.com</a>.</p>

                <h2>7. Cookies</h2>
                <p>
                    We use cookies to maintain your session, remember your preferences, and analyse platform usage.
                    You can disable cookies in your browser settings, though this may affect platform functionality.
                </p>

                <h2>8. Changes to This Policy</h2>
                <p>
                    We may update this Privacy Policy from time to time. We will notify you of significant changes
                    by email or through a notice on the platform. Your continued use after changes constitutes acceptance.
                </p>

                <h2>9. Contact Us</h2>
                <p>
                    For privacy-related questions, contact:<br />
                    <a href="mailto:privacy@cpacrm.com">privacy@cpacrm.com</a><br />
                    VJ CPA CRM, Brampton, ON, Canada
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
