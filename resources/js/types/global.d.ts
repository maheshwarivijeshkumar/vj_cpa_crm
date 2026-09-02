import type { PageProps } from './inertia'
import type { Page, Router } from '@inertiajs/core'

// ─── Vite env ─────────────────────────────────────────────────────────────────
declare module 'vite/client' {
    interface ImportMetaEnv {
        readonly VITE_APP_NAME: string
        readonly VITE_APP_URL: string
        [key: string]: string | boolean | undefined
    }
    interface ImportMeta {
        readonly env: ImportMetaEnv
        readonly glob: <T>(pattern: string) => Record<string, () => Promise<T>>
    }
}

// ─── Inertia shared props ─────────────────────────────────────────────────────
declare module '@inertiajs/core' {
    export interface PageProps extends PageProps {}
}

// ─── Vue global ───────────────────────────────────────────────────────────────
declare module 'vue' {
    interface ComponentCustomProperties {
        $page: Page<PageProps>
        $inertia: typeof Router
    }
}
