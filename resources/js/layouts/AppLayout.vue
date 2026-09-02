<script setup lang="ts">
import AppNavbar from '@/components/layout/AppNavbar.vue'
import ToastContainer from '@/components/ui/ToastContainer.vue'
import { useFlash } from '@/composables/useFlash'

// Auto-convert Inertia flash → toasts
useFlash()
</script>

<template>
    <div class="app-shell">
        <!-- Horizontal top navbar -->
        <AppNavbar />

        <!-- Page content -->
        <main class="app-content">
            <slot />
        </main>

        <!-- Toast notifications -->
        <ToastContainer />
    </div>
</template>
