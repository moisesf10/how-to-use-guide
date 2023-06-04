import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
//import path from 'path';
import {viteStaticCopy} from "vite-plugin-static-copy";


const inputFiles = [
    'resources/scss/admin/style.scss',
    'resources/css/site/theme.css',
    'resources/css/site/user.css',
    //'resources/js/functions.js',
    'resources/js/app.js',
    'resources/js/process-assets.js',
    'resources/js/admin/sortable.js',
]

export default defineConfig({
    plugins: [
        laravel({
            input: inputFiles,
            refresh: true,
        }),
        viteStaticCopy({
            targets: [
                {
                    src: 'resources/plugins',
                    dest: ''
                },
                {
                    src: 'resources/js/functions.js',
                    dest: 'assets'
                },
            ]
        })
        /*viteStaticCopy({
            targets: [
                {
                    src: 'resources/css',
                    dest: ''
                },
                {
                    src: 'resources/js',
                    dest: ''
                },
                {
                    src: 'resources/plugins',
                    dest: ''
                },
                {
                    src: 'resources/img',
                    dest: '../'
                },

            ]
        })*/
    ],
});
