<script setup lang="ts">
withDefaults(defineProps<{
    /** Layout preset */
    type?: 'table' | 'card' | 'stat' | 'form' | 'text' | 'list'
    /** Number of rows (for table/list/form) */
    rows?: number
    /** Number of columns (for stat layout) */
    cols?: number
}>(), {
    type: 'table',
    rows: 5,
    cols: 4,
})
</script>

<template>
    <!-- TABLE skeleton -->
    <div v-if="type === 'table'" class="data-table-wrapper">
        <!-- Toolbar -->
        <div class="data-table-toolbar">
            <div class="skeleton h-8 w-56 rounded-lg" />
            <div class="skeleton h-8 w-24 rounded-lg ml-auto" />
            <div class="skeleton h-8 w-24 rounded-lg" />
        </div>
        <!-- Head row -->
        <div class="flex gap-3 px-3.5 py-2.5 border-b border-cpa-border bg-cpa-bg">
            <div v-for="i in cols" :key="i" class="skeleton h-3 flex-1 rounded" />
        </div>
        <!-- Body rows -->
        <div v-for="r in rows" :key="r" class="flex gap-3 px-3.5 py-3.5 border-b border-cpa-border last:border-none">
            <div v-for="i in cols" :key="i" class="skeleton h-4 flex-1 rounded" :style="{ opacity: 1 - (r - 1) * 0.1 }" />
        </div>
        <!-- Footer -->
        <div class="data-table-footer">
            <div class="skeleton h-3 w-32 rounded" />
            <div class="skeleton h-7 w-40 rounded" />
        </div>
    </div>

    <!-- STAT cards skeleton -->
    <div v-else-if="type === 'stat'" :class="`grid grid-cols-${cols} gap-4`">
        <div v-for="i in cols" :key="i" class="card p-5 flex items-center justify-between">
            <div class="flex flex-col gap-2 flex-1">
                <div class="skeleton h-3 w-24 rounded" />
                <div class="skeleton h-7 w-16 rounded" />
                <div class="skeleton h-3 w-20 rounded" />
            </div>
            <div class="skeleton w-11 h-11 rounded-xl flex-shrink-0" />
        </div>
    </div>

    <!-- CARD skeleton -->
    <div v-else-if="type === 'card'" class="card">
        <div class="card-header">
            <div class="skeleton h-4 w-40 rounded" />
            <div class="skeleton h-8 w-24 rounded-lg ml-auto" />
        </div>
        <div class="card-body flex flex-col gap-3">
            <div class="skeleton h-4 w-full rounded" />
            <div class="skeleton h-4 w-4/5 rounded" />
            <div class="skeleton h-4 w-3/5 rounded" />
        </div>
    </div>

    <!-- FORM skeleton -->
    <div v-else-if="type === 'form'" class="flex flex-col gap-5">
        <div v-for="r in rows" :key="r" class="form-group">
            <div class="skeleton h-3.5 w-28 rounded" />
            <div class="skeleton h-9 w-full rounded-lg" />
        </div>
    </div>

    <!-- LIST skeleton -->
    <div v-else-if="type === 'list'" class="flex flex-col gap-2">
        <div
            v-for="r in rows"
            :key="r"
            class="flex items-center gap-3 p-3 rounded-lg border border-cpa-border bg-cpa-white"
            :style="{ opacity: 1 - (r - 1) * 0.12 }"
        >
            <div class="skeleton w-9 h-9 rounded-full flex-shrink-0" />
            <div class="flex flex-col gap-1.5 flex-1">
                <div class="skeleton h-3.5 w-40 rounded" />
                <div class="skeleton h-3 w-24 rounded" />
            </div>
            <div class="skeleton h-6 w-16 rounded-full" />
        </div>
    </div>

    <!-- TEXT skeleton -->
    <div v-else class="flex flex-col gap-2">
        <div v-for="r in rows" :key="r" class="skeleton h-4 rounded" :style="{ width: `${85 - (r % 3) * 15}%` }" />
    </div>
</template>
