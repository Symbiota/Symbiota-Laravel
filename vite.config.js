import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import tailwindcss from '@tailwindcss/vite'

export default defineConfig(() => {
    return {
        plugins: [
            tailwindcss(),
            laravel({
                input: [
                    'resources/js/app.js',
                    'resources/js/htmx.js',
                    'resources/js/editor.js',
                    'resources/js/leaflet.js',
                    'resources/js/chart.js',
                    'resources/css/app.css'
                ],
                refresh: true,
            }),
        ],
    }
});
