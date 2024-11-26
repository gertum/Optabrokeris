import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import react from '@vitejs/plugin-react';

export default defineConfig({
    plugins: [
        laravel({
            input: 'resources/js/app.jsx',
            refresh: true,
        }),
        laravel({
            input: 'resources/js/Pages/Jobs/Form.jsx',
            refresh: true,
        }),
        react(),
    ],
});
