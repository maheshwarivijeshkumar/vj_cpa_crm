<script setup lang="ts">
import { Link } from '@inertiajs/vue3'
import { ShieldOff } from 'lucide-vue-next'

defineProps<{
  status?: number
  message?: string
}>()
</script>

<template>
  <div class="min-h-screen bg-cpa-bg flex flex-col items-center justify-center px-4">
    <div class="text-center max-w-md">
      <!-- Icon -->
      <div class="flex items-center justify-center mx-auto mb-6 w-20 h-20 rounded-2xl bg-cpa-danger-bg">
        <ShieldOff class="size-10 text-cpa-danger" />
      </div>

      <!-- Status code -->
      <p class="text-7xl font-black text-cpa-border leading-none mb-4 select-none">403</p>

      <!-- Heading -->
      <h1 class="text-2xl font-semibold text-cpa-text-primary mb-3">Access denied</h1>

      <!-- Description -->
      <p class="text-sm text-cpa-text-muted leading-relaxed mb-8">
        {{ message || "You don't have permission to view this page. Contact your administrator if you believe this is an error." }}
      </p>

      <!-- Actions -->
      <div class="flex flex-col sm:flex-row items-center justify-center gap-3">
        <Link
          :href="route('dashboard')"
          class="w-full sm:w-auto bg-cpa-medium-dark hover:bg-cpa-dark text-white font-medium rounded-lg px-6 py-2.5 text-sm transition-colors text-center"
        >
          Go to Dashboard
        </Link>
        <button
          type="button"
          class="w-full sm:w-auto border border-cpa-border hover:border-cpa-medium text-cpa-text-primary hover:text-cpa-dark rounded-lg px-6 py-2.5 text-sm transition-colors"
          @click="() => history.back()"
        >
          Go Back
        </button>
      </div>
    </div>
  </div>
</template>
