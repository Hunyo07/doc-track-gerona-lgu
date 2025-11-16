import { defineConfig } from "vite";
import laravel from "laravel-vite-plugin";
import vue from "@vitejs/plugin-vue"; // 1. Import the Vue plugin
import { fileURLToPath, URL } from "node:url"; // ✅ built-in in Node

export default defineConfig({
    plugins: [
        laravel({
            input: ["resources/css/app.css", "resources/js/app.js"],
            refresh: true,
        }),
        vue({
            // 2. Add the Vue plugin here
            template: {
                transformAssetUrls: {
                    base: null,
                    includeAbsolute: false,
                },
            },
        }),
    ],
    resolve: {
        alias: {
            "@": fileURLToPath(new URL("./src", import.meta.url)),
            vue: "vue/dist/vue.esm-bundler.js", // ✅ Fix for runtime template warning
        },
    },
    // Proxy API calls during development to the Laravel backend
    server: {
        host: "0.0.0.0'",
        port: 5173,
        hmr: {
            host: "0.0.0.0'",
            protocol: "http",
        },
        proxy: {
            "/api": {
                target: "http://127.0.0.1:8000",
                changeOrigin: true,
                secure: false,
            },
        },
    },
});
