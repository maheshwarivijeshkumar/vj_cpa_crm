<script setup lang="ts">
import { Link } from '@inertiajs/vue3'
import { AlertTriangle } from 'lucide-vue-next'

defineProps<{
  status?: number
}>()
</script>

<template>
  <div class="min-h-screen bg-cpa-bg flex flex-col items-center justify-center px-4">
    <div class="text-center max-w-md">
      <!-- Icon -->
      <div class="flex items-center justify-center mx-auto mb-6 w-20 h-20 rounded-2xl bg-cpa-warning-bg">
        <AlertTriangle class="size-10 text-cpa-warning" />
      </div>

      <!-- Status code -->
      <p class="text-7xl font-black text-cpa-border leading-none mb-4 select-none">500</p>

      <!-- Heading -->
      <h1 class="text-2xl font-semibold text-cpa-text-primary mb-3">Something went wrong</h1>

      <!-- Description -->
      <p class="text-sm text-cpa-text-muted leading-relaxed mb-8">
        An unexpected error occurred on the server. Our team has been notified.
        Please try again in a moment — if the problem persists, contact support.
      </p>

      <!-- Actions -->
      <div class="flex flex-col sm:flex-row items-center justify-center gap-3">
        <button
          type="button"
          class="w-full sm:w-auto bg-cpa-medium-dark hover:bg-cpa-dark text-white font-medium rounded-lg px-6 py-2.5 text-sm transition-colors"
          @click="() => location.reload()"
        >
          Try Again
        </button>
        <Link
          :href="route('dashboard')"
          class="w-full sm:w-auto border border-cpa-border hover:border-cpa-medium text-cpa-text-primary hover:text-cpa-dark rounded-lg px-6 py-2.5 text-sm transition-colors text-center"
        >
          Go to Dashboard
        </Link>
      </div>
    </div>
  </div>
</template>
