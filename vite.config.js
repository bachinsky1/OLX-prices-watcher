import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import vue from "@vitejs/plugin-vue";
import tailwindcss from "tailwindcss";
import autoprefixer from "autoprefixer";

export default defineConfig({
    define: {
        __VUE_PROD_DEVTOOLS__: false,
        __VUE_PROD_HYDRATION_MISMATCH_DETAILS__: true,
    },
    plugins: [
        vue({
            template: {
                transformAssetUrls: { 
                    base: null, 
                    includeAbsolute: false,
                },
            },
        }),
        laravel({
            input: [
                'resources/css/app.css', 
                'resources/js/app.js',
            ],
            refresh: true,
        }),
    ],
    css: {
        postcss: {
            plugins: [tailwindcss, autoprefixer],
        },
    },
    build: {
        minify: true,
        sourcemap: true,
    },
});
