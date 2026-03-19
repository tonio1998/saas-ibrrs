import { defineConfig } from 'vite'
import laravel from 'laravel-vite-plugin'

export default defineConfig({
    server: {
        host: 'localhost',
        cors: true,
        hmr: {
            host: 'saas-ibrss.dev.com'
        }
    },
    plugins: [
        laravel({
            input: ['resources/css/app.css','resources/js/app.js'],
            refresh: true,
        }),
    ],
})
