<script setup lang="ts">
import { computed } from 'vue'

const props = defineProps<{
    status: string
    label?: string
    dot?: boolean
}>()

const statusMap: Record<string, string> = {
    // Green — complete / paid / active
    active:      'badge-success',
    paid:        'badge-success',
    completed:   'badge-success',
    accepted:    'badge-success',
    submitted:   'badge-success',
    approved:    'badge-success',
    verified:    'badge-success',
    published:   'badge-success',
    posted:      'badge-success',

    // Teal — in progress / draft
    draft:       'badge-teal',
    in_progress: 'badge-teal-mid',
    under_review:'badge-teal-mid',
    open:        'badge-teal',
    assigned:    'badge-teal',

    // Yellow — pending / waiting
    pending:        'badge-warning',
    sent:           'badge-warning',
    waiting:        'badge-warning',
    partially_paid: 'badge-warning',
    invited:        'badge-warning',
    trial:          'badge-warning',
    review:         'badge-warning',

    // Red — overdue / failed / rejected
    overdue:    'badge-danger',
    rejected:   'badge-danger',
    failed:     'badge-danger',
    cancelled:  'badge-danger',
    voided:     'badge-danger',
    suspended:  'badge-danger',
    locked:     'badge-danger',

    // Gray — archived / inactive
    archived:  'badge-gray',
    inactive:  'badge-gray',
    closed:    'badge-gray',
    expired:   'badge-gray',
}

const badgeClass = computed(() =>
    statusMap[props.status?.toLowerCase()] ?? 'badge-gray',
)

const displayLabel = computed(() =>
    props.label ?? props.status?.replace(/_/g, ' '),
)
</script>

<template>
    <span :class="['badge', badgeClass]">
        <span
            v-if="dot"
            class="inline-block w-1.5 h-1.5 rounded-full bg-current opacity-70"
        />
        {{ displayLabel }}
    </span>
</template>
