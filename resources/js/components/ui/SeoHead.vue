<script setup lang="ts">
/**
 * SeoHead — renders all SEO meta tags into <head> via Inertia's <Head>.
 *
 * Usage:
 *   <SeoHead :seo="seo" />
 *   Where seo comes from $page.props.seo or a page-specific override.
 *
 * The backend populates $page.props.seo with SeoService::make().
 * Individual pages can pass their own seo object to override.
 */
import { Head } from '@inertiajs/vue3'
import { computed } from 'vue'
import { usePage } from '@inertiajs/vue3'

interface SeoOg {
    title?: string
    description?: string
    image?: string
    type?: string
    url?: string
    site_name?: string
}

interface SeoTwitter {
    card?: string
    title?: string
    description?: string
    image?: string
    site?: string
}

interface SeoData {
    title?: string
    description?: string
    keywords?: string
    canonical?: string
    robots?: string
    og?: SeoOg
    twitter?: SeoTwitter
    schema?: string | null
}

const props = withDefaults(defineProps<{
    seo?: SeoData
}>(), {
    seo: undefined,
})

const page = usePage()

// Merge page-level seo with props (props override page-level)
const resolved = computed<SeoData>(() => {
    const pageSeo = (page.props as any).seo as SeoData ?? {}
    return { ...pageSeo, ...(props.seo ?? {}) }
})

const title       = computed(() => resolved.value.title ?? '')
const description = computed(() => resolved.value.description ?? '')
const keywords    = computed(() => resolved.value.keywords ?? '')
const canonical   = computed(() => resolved.value.canonical ?? '')
const robots      = computed(() => resolved.value.robots ?? 'index,follow')
const og          = computed(() => resolved.value.og ?? {})
const twitter     = computed(() => resolved.value.twitter ?? {})
const schema      = computed(() => resolved.value.schema ?? null)
</script>

<template>
    <Head :title="title">
        <!-- Primary -->
        <meta v-if="description" name="description" :content="description" />
        <meta v-if="keywords" name="keywords" :content="keywords" />
        <meta name="robots" :content="robots" />
        <link v-if="canonical" rel="canonical" :href="canonical" />

        <!-- Open Graph -->
        <meta property="og:title"       :content="og.title       || title" />
        <meta property="og:description" :content="og.description || description" />
        <meta v-if="og.image"           property="og:image"       :content="og.image" />
        <meta property="og:type"        :content="og.type        || 'website'" />
        <meta v-if="canonical"          property="og:url"         :content="canonical" />
        <meta v-if="og.site_name"       property="og:site_name"   :content="og.site_name" />

        <!-- Twitter Card -->
        <meta name="twitter:card"        :content="twitter.card        || 'summary_large_image'" />
        <meta name="twitter:title"       :content="twitter.title       || title" />
        <meta name="twitter:description" :content="twitter.description || description" />
        <meta v-if="twitter.image"  name="twitter:image" :content="twitter.image" />
        <meta v-if="twitter.site"   name="twitter:site"  :content="twitter.site" />

        <!-- JSON-LD Structured Data -->
        <component
            v-if="schema"
            :is="'script'"
            type="application/ld+json"
            v-html="schema"
        />
    </Head>
</template>
