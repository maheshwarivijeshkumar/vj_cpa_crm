<script setup lang="ts">
import { Clock } from 'lucide-vue-next'

defineProps<{
  status?: number
  retryAfter?: number
}>()
</script>

<template>
  <div class="min-h-screen bg-cpa-bg flex flex-col items-center justify-center px-4">
    <div class="text-center max-w-md">
      <!-- Icon -->
      <div class="flex items-center justify-center mx-auto mb-6 w-20 h-20 rounded-2xl bg-cpa-very-light">
        <Clock class="size-10 text-cpa-medium-dark" />
      </div>

      <!-- Status code -->
      <p class="text-7xl font-black text-cpa-border leading-none mb-4 select-none">503</p>

      <!-- Heading -->
      <h1 class="text-2xl font-semibold text-cpa-text-primary mb-3">Down for maintenance</h1>

      <!-- Description -->
      <p class="text-sm text-cpa-text-muted leading-relaxed mb-8">
        VJ CPA CRM is currently undergoing scheduled maintenance.
        <template v-if="retryAfter">
          We'll be back in approximately <strong class="text-cpa-text-primary">{{ retryAfter }}</strong> minutes.
        </template>
        <template v-else>
          We'll be back shortly. Thank you for your patience.
        </template>
      </p>

      <!-- Retry -->
      <button
        type="button"
        class="bg-cpa-medium-dark hover:bg-cpa-dark text-white font-medium rounded-lg px-6 py-2.5 text-sm transition-colors"
        @click="() => location.reload()"
      >
        Refresh Page
      </button>

      <!-- Status indicator -->
      <div class="mt-8 flex items-center justify-center gap-2 text-xs text-cpa-text-muted">
        <span class="w-2 h-2 rounded-full bg-cpa-warning animate-pulse" />
        Maintenance in progress
      </div>
    </div>
  </div>
</template>
