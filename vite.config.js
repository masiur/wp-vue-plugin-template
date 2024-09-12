import { fileURLToPath, URL } from 'node:url'

import { defineConfig } from 'vite'
import vue from '@vitejs/plugin-vue'


// Determine the base URL dynamically based on the environment
const base = process.env.NODE_ENV === 'production'
    ? '/wp-content/plugins/wpplug/' // Replace 'your-plugin-folder' with your actual plugin directory name
    : '/';
// https://vitejs.dev/config/

const inputs = [
  'src/main.js',
  'src/assets/main.css',
]
export default defineConfig({
  plugins: [
    vue(),
  ],
  base,
  resolve: {
    alias: {
      '@': fileURLToPath(new URL('./src', import.meta.url))
    }
  },
  server: {
    host: 'localhost',
    port: 5170,
    open: false, // Opens the browser automatically
    hmr: {
      protocol: 'ws',
      host: 'localhost',
      clientPort: 5170,
    },
    headers: {
      'Cache-Control': 'no-store',
    },
  },

  // Build options for production
  build: {
    manifest: true,
    outDir: 'dist',
    publicDir: 'public',
    rollupOptions: {
      input: inputs,

      output: {
          chunkFileNames: '[name].js',
          entryFileNames: '[name].js',

      },
    },
  },
})
