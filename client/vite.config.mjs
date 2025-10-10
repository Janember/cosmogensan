import { defineConfig } from 'vite'
import vue from '@vitejs/plugin-vue'
import tailwindcss from '@tailwindcss/vite'

export default defineConfig({
  plugins: [
    vue({
      template: {
        compilerOptions: {
          isCustomElement: (tag) => tag === 'model-viewer',
        },
      },
    }),
    tailwindcss(),
  ],
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
