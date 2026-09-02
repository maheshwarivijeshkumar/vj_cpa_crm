import inertia from '@inertiajs/vite'
import tailwindcss from '@tailwindcss/vite'
import vue from '@vitejs/plugin-vue'
import laravel from 'laravel-vite-plugin'
import { defineConfig } from 'vite'
import { resolve } from 'path'

export default defineConfig({
    plugins: [
        laravel({
            input: ['resources/css/app.css', 'resources/js/app.ts'],
            refresh: true,
        }),
        inertia(),
        tailwindcss(),
        vue({
            template: {
                transformAssetUrls: {
                    base: null,
                    includeAbsolute: false,
                },
            },
        }),
        // NOTE: @laravel/vite-plugin-wayfinder disabled until DB is migrated.
        // Re-enable after running: php artisan migrate && php artisan wayfinder:generate
    ],

    resolve: {
        alias: {
            '@': resolve(__dirname, 'resources/js'),
        },
    },

    optimizeDeps: {
        include: [
            'vue',
            'pinia',
            '@vue/devtools-api',
            '@lucide/vue',
            'ziggy-js',
            '@inertiajs/vue3',
            '@inertiajs/core',
        ],
    },

    build: {
        target: 'es2020',
        sourcemap: false,
    },

    server: {
        watch: {
            ignored: [
                '**/.agents/**',
                '**/.claude/**',
                '**/.cursor/**',
                '**/.kiro/**',
                '**/.junie/**',
                '**/vendor/**',
                '**/storage/**',
            ],
        },
    },
})
