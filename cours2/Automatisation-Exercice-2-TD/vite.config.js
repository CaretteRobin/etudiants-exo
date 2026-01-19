import { defineConfig } from 'vite';

export default defineConfig({
    root: './assets',
    base: '/build/',
    server: {
        host: '0.0.0.0',
        port: 3000,
        strictPort: true,
    },
    build: {
        manifest: true,
        outDir: '../public/build',
        emptyOutDir: true,
        rollupOptions: {
            input: {
                app: './assets/app.js',
            },
        },
    },
});
