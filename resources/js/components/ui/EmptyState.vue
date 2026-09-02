<script setup lang="ts">
import { type Component } from 'vue'

withDefaults(defineProps<{
    title?: string
    description?: string
    icon?: Component
    actionLabel?: string
    actionPermission?: string
}>(), {
    title:       'Nothing here yet',
    description: 'Get started by creating the first record.',
})

const emit = defineEmits<{
    action: []
}>()
</script>

<template>
    <div class="empty-state">
        <div class="empty-state-icon">
            <component :is="icon" v-if="icon" :size="26" />
            <!-- Default icon if none provided -->
            <svg v-else xmlns="http://www.w3.org/2000/svg" width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                <rect x="2" y="5" width="20" height="14" rx="2"/>
                <path d="M2 10h20"/>
            </svg>
        </div>

        <h3 class="empty-state-title">{{ title }}</h3>

        <p class="empty-state-description">{{ description }}</p>

        <slot name="action">
            <button
                v-if="actionLabel"
                class="btn btn-primary btn-sm mt-1"
                @click="emit('action')"
            >
                {{ actionLabel }}
            </button>
        </slot>
    </div>
</template>
