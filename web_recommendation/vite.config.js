import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
    plugins: [
        laravel({
            input: ['resources/css/app.css', 'resources/js/app.js'],
            refresh: true,
        }),
    ],
    server: {
        watch: {
            ignored: ['**/storage/framework/views/**'],
        },
    },
    // build: {
    //     // Production optimizations
    //     // minify: 'terser',
    //     // terserOptions: {
    //     //     compress: {
    //     //         drop_console: true,
    //     //         drop_debugger: true,
    //     //     },
    //     // },
    //     // Code splitting
    //     // rollupOptions: {
    //     //     output: {
    //     //         manualChunks: {
    //     //             vendor: ['alpinejs'],
    //     //         },
    //     //         // Asset file naming
    //     //         chunkFileNames: 'js/[name]-[hash].js',
    //     //         entryFileNames: 'js/[name]-[hash].js',
    //     //         assetFileNames: ({ name }) => {
    //     //             if (/\.(css)$/.test(name ?? '')) {
    //     //                 return 'css/[name]-[hash][extname]';
    //     //             }
    //     //             if (/\.(woff|woff2|eot|ttf|otf)$/.test(name ?? '')) {
    //     //                 return 'fonts/[name]-[hash][extname]';
    //     //             }
    //     //             if (/\.(png|jpe?g|gif|svg|webp|ico)$/.test(name ?? '')) {
    //     //                 return 'images/[name]-[hash][extname]';
    //     //             }
    //     //             return 'assets/[name]-[hash][extname]';
    //     //         },
    //     //     },
    //     // },
    //     // Chunk size warnings
    //     chunkSizeWarningLimit: 1000,
    //     // Enable source maps for debugging (disable in production)
    //     sourcemap: false,
    //     // CSS code splitting
    //     cssCodeSplit: true,
    // },
    // // Optimize dependencies
    // optimizeDeps: {
    //     include: ['alpinejs'],
    // },
});
