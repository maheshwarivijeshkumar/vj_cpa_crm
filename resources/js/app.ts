import '../css/app.css'

import { createApp, h, DefineComponent } from 'vue'
import { createInertiaApp, usePage } from '@inertiajs/vue3'
import { createPinia }  from 'pinia'
import { resolvePageComponent } from 'laravel-vite-plugin/inertia-helpers'
import { ZiggyVue } from 'ziggy-js'

// ─── App name ─────────────────────────────────────────────────────────────────
const appName = import.meta.env.VITE_APP_NAME || 'VJ CPA CRM'

// ─── Layout resolution ────────────────────────────────────────────────────────
// Pages declare their layout via:
//   defineOptions({ layout: AppLayout })   (most pages)
//   defineOptions({ layout: AuthLayout })  (auth pages)
//   defineOptions({ layout: null })        (no layout)
//
// The default layout (when not declared) is AppLayout.

async function resolveLayout(page: DefineComponent) {
    // If the page explicitly sets layout (including null), honour it
    if (page.layout !== undefined) return

    // Default: authenticated app shell
    const { default: AppLayout } = await import('@/layouts/AppLayout.vue')
    page.layout = AppLayout
}

// ─── Create Inertia app ───────────────────────────────────────────────────────
createInertiaApp({
    title: (title) => title ? `${title} — ${appName}` : appName,

    // Resolve page components from pages/ directory
    resolve: async (name) => {
        const page = await resolvePageComponent(
            `./pages/${name}.vue`,
            import.meta.glob<DefineComponent>('./pages/**/*.vue'),
        )
        await resolveLayout(page.default)
        return page
    },

    // Progress bar colours from our brand palette
    progress: {
        color:           '#48BCB9',   // --cpa-medium
        includeCSS:      true,
        showSpinner:     false,
    },

    setup({ el, App, props, plugin }) {
        const pinia = createPinia()

        createApp({ render: () => h(App, props) })
            .use(plugin)
            .use(pinia)
            .use(ZiggyVue)
            .mount(el)
    },
})
