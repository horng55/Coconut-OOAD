import {defineConfig} from 'vite';
import laravel from 'laravel-vite-plugin';
import vue from '@vitejs/plugin-vue';
import * as path from 'path';

export default defineConfig({
    plugins: [
        laravel({
            input: [
                'resources/css/app.css',
                'resources/sass/app.scss',
                'resources/js/app.js',
            ],
            refresh: true,
        }),
        vue({
            template: {
                transformAssetUrls: {
                    includeAbsolute: false,
                },
            },
        }),
    ],
    resolve: {
        alias: {
            crypto: 'crypto-browserify',
            stream: 'stream-browserify',
            buffer: 'buffer',
            process: 'process/browser',
            '~bootstrap': path.resolve(__dirname, 'node_modules/bootstrap'),
            '@': path.resolve(__dirname, 'resources/js'),
        },
    },
    define: {
        global: 'window',
        'process.env': {},
    },
    optimizeDeps: {
        include: [
            'buffer',
            'process',
            'crypto-browserify',
            'stream-browserify',
        ],
    },
});
