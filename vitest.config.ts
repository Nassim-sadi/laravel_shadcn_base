import vue from '@vitejs/plugin-vue'
import { fileURLToPath, URL } from 'node:url'
import { defineConfig } from 'vitest/config'

export default defineConfig({
  plugins: [
    vue({
      script: {
        defineModel: true,
        propsDestructure: true,
      },
    }),
  ],
  resolve: {
    alias: {
      '@': fileURLToPath(new URL('./resources/js', import.meta.url)),
    },
  },
  test: {
    environment: 'happy-dom',
    include: ['resources/js/**/*.{test,spec}.{ts,tsx}'],
    exclude: ['node_modules', 'vendor'],
    globals: true,
    setupFiles: ['resources/js/__tests__/setup.ts'],
  },
})
