import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import tailwindcss from '@tailwindcss/vite';
import { resolve } from 'path';

import vue from '@vitejs/plugin-vue';
export default defineConfig({
    resolve: {
        alias: {
            '@/': resolve(__dirname, 'resources/js')
        }
    },
    plugins: [
        
        laravel({
            input: ['resources/css/app.css', 'resources/js/app.js'],
            refresh: true,
        }),
        tailwindcss(), 
        vue({
            template: {
                transformAssetUrls: {
                    base: null,
                    includeAbsolute: false,
                },
            },
        })
    ],
});
