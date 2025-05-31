import { defineConfig } from 'vite'
import vue from '@vitejs/plugin-vue'

export default defineConfig({
  plugins: [vue()],
  server: {
    port: 5173,
    proxy: {
      '/api': 'http://localhost/leopando/server/api',
    },
  },
  resolve: {
    alias: {
      '@': '/src', 
    },
  },
  build: {
    outDir: 'dist',
    rollupOptions: {
      input: '/index.html', 
    },
  },
})
