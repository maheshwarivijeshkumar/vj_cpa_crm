<script setup lang="ts">
import { Link } from '@inertiajs/vue3'

/**
 * Pagination component — renders Laravel paginator links.
 * Accepts the `links` array from any Laravel paginated response,
 * plus total count and per-page for the summary line.
 */
interface PaginationLink {
    url: string | null
    label: string
    active: boolean
}

const props = defineProps<{
    links: PaginationLink[]
    total?: number
    perPage?: number
}>()

/** Strip HTML entities from Laravel's default prev/next labels */
function cleanLabel(label: string): string {
    return label
        .replace(/&laquo;\s*/g, '← ')
        .replace(/\s*&raquo;/g, ' →')
        .replace(/&amp;/g, '&')
        .replace(/&lt;/g, '<')
        .replace(/&gt;/g, '>')
}

/** True only for the numbered page links (not prev/next) */
function isPageNumber(label: string): boolean {
    return /^\d+$/.test(label)
}

const visibleLinks = props.links.filter((l) => l.url !== null || l.active)
</script>

<template>
    <div class="flex items-center justify-between gap-4 flex-wrap">

        <!-- Summary -->
        <p v-if="total !== undefined" class="text-xs text-cpa-text-muted">
            {{ total.toLocaleString() }} {{ total === 1 ? 'record' : 'records' }}
        </p>
        <div v-else />

        <!-- Links -->
        <nav aria-label="Pagination" class="flex items-center gap-1">
            <template v-for="link in links" :key="link.label">

                <!-- Disabled (null url and not active) -->
                <span
                    v-if="!link.url && !link.active"
                    class="px-2 py-1 text-xs text-cpa-text-muted rounded-lg cursor-default select-none"
                    aria-hidden="true"
                >
                    {{ cleanLabel(link.label) }}
                </span>

                <!-- Active page -->
                <span
                    v-else-if="link.active"
                    class="px-3 py-1 text-xs font-semibold bg-cpa-medium-dark text-white rounded-lg cursor-default select-none"
                    aria-current="page"
                >
                    {{ cleanLabel(link.label) }}
                </span>

                <!-- Navigable link -->
                <Link
                    v-else
                    :href="link.url!"
                    class="px-3 py-1 text-xs text-cpa-text-secondary hover:text-cpa-text-primary hover:bg-cpa-very-light rounded-lg transition-colors"
                    preserve-scroll
                >
                    {{ cleanLabel(link.label) }}
                </Link>

            </template>
        </nav>
    </div>
</template>
