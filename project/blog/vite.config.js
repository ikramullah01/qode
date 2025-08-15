import { defineConfig } from "vite";
import laravel from "laravel-vite-plugin";
import vue from "@vitejs/plugin-vue";
import path from 'path';

export default defineConfig({
    server: {
        host: "0.0.0.0", // listen on all interfaces
        port: 5173, // match exposed port
        hmr: {
            host: "localhost", // your machine's host name or IP
        },
    },
    plugins: [
        laravel({
            input: ["resources/sass/app.scss", "resources/js/app.js"],
            refresh: true, // enables hot reload for Blade files
        }),
        vue(),
    ],
    resolve: {
        alias: {
            vue: 'vue/dist/vue.esm-bundler.js',
            '@': path.resolve(__dirname, 'resources/js'),
        },
    },
    define: {
        _VUE_PROD_HYDRATION_MISMATCH_DETAILS_: false,
    },
});
