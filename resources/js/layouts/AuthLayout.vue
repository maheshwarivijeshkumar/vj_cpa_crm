<script setup lang="ts">
// Auth layout — split panel: brand left, form right.
// Used by Login, Register, ForgotPassword, ResetPassword, etc.
// Page declares:  defineOptions({ layout: AuthLayout })
</script>

<template>
    <div class="auth-shell">
        <!-- Brand panel (desktop only) -->
        <aside class="auth-brand">
            <div class="auth-brand-logo">
                <div class="auth-brand-mark">CPA</div>
                <div>
                    <div class="auth-brand-name">VJ CPA CRM</div>
                    <div class="auth-brand-tagline">Enterprise Practice Management</div>
                </div>
            </div>

            <div class="auth-brand-features">
                <div class="auth-brand-feature">
                    <span class="auth-brand-feature-dot" />
                    Multi-client CPA practice management
                </div>
                <div class="auth-brand-feature">
                    <span class="auth-brand-feature-dot" />
                    Filings, deadlines & tax tracking
                </div>
                <div class="auth-brand-feature">
                    <span class="auth-brand-feature-dot" />
                    Secure document management
                </div>
                <div class="auth-brand-feature">
                    <span class="auth-brand-feature-dot" />
                    Invoicing, payments & accounting
                </div>
                <div class="auth-brand-feature">
                    <span class="auth-brand-feature-dot" />
                    Client portal & communications
                </div>
            </div>

            <p class="auth-brand-footer">
                © {{ new Date().getFullYear() }} VJ CPA CRM. All rights reserved.
            </p>
        </aside>

        <!-- Form panel -->
        <div class="auth-form-area">
            <slot />
        </div>
    </div>
</template>
