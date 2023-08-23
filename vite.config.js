import { defineConfig, loadEnv } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig(({mode}) =>{
    const env = loadEnv(mode, process.cwd(), '');
    return {
        plugins: [
            laravel({
                input: [
                    'resources/js/app.js',
                    env.VITE_CSS_TARGET?
                        env.VITE_CSS_TARGET:
                        'resources/css/app.css',
                ],
                refresh: true,
            }),
        ],
    }
});
