<script setup lang="ts">
// Guest / minimal layout — used for marketing pages, public portal, error pages.
// Page declares:  defineOptions({ layout: GuestLayout })
</script>

<template>
    <div class="min-h-screen flex flex-col bg-cpa-bg">
        <!-- Minimal header -->
        <header class="bg-cpa-dark px-6 h-16 flex items-center">
            <a href="/" class="flex items-center gap-3 no-underline">
                <div class="sidebar-logo-mark">CPA</div>
                <span class="text-white font-semibold text-[15px]">VJ CPA CRM</span>
            </a>
        </header>

        <main class="flex-1">
            <slot />
        </main>

        <footer class="border-t border-cpa-border py-4 px-6 text-xs text-cpa-text-muted flex justify-between flex-wrap gap-2">
            <span>© {{ new Date().getFullYear() }} VJ CPA CRM. All rights reserved.</span>
            <a href="mailto:support@cpacrm.com" class="text-cpa-medium-dark hover:underline">
                support@cpacrm.com
            </a>
        </footer>
    </div>
</template>
