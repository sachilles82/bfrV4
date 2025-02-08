// import { defineConfig } from 'vite';
// import laravel from 'laravel-vite-plugin';
//
// export default defineConfig({
//     plugins: [
//         laravel({
//             input: [
//                 'resources/css/app.css',
//                 'resources/js/app.js',
//             ],
//             refresh: true,
//         }),
//     ],
// });


import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
    plugins: [
        laravel({
            input: [
                'resources/css/app.css',
                'resources/js/app.js',
            ],
            refresh: true,
        }),
    ],

    build: {
        minify: true,
        sourcemap: false,
        rollupOptions: {
            output: {
                manualChunks: (id) => {
                    // Beispiel: auf 'codemirror' prüfen
                    if (id.includes('codemirror')) {
                        return 'codemirror';
                    }
                    // Beispiel: alles andere aus node_modules in "vendor.js"
                    if (id.includes('node_modules')) {
                        return 'vendor';
                    }
                },
            },
        },
    },
});
