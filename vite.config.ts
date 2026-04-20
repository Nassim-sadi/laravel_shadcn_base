import type { PluginOption } from 'vite'

import tailwindcss from '@tailwindcss/vite'
import vue from '@vitejs/plugin-vue'
import { fileURLToPath, URL } from 'node:url'
import { visualizer } from 'rollup-plugin-visualizer'
import AutoImport from 'unplugin-auto-import/vite'
import Component from 'unplugin-vue-components/vite'
import { defineConfig } from 'vite'
import laravel from 'laravel-vite-plugin'

export default defineConfig({
  plugins: [
    laravel({
      input: ['resources/js/main.ts', 'resources/css/app.css'],
      refresh: ['resources/views/**'],
    }),
    vue({
      script: {
        defineModel: true,
        propsDestructure: true,
      },
    }),
    tailwindcss(),
    visualizer({ gzipSize: true, brotliSize: true }) as PluginOption,
    AutoImport({
      include: [
        /\.[tj]sx?$/,
        /\.vue$/,
      ],
      imports: [
        'vue',
      ],
      dirs: [
        'resources/js/composables/**/*.ts',
        'resources/js/constants/**/*.ts',
        'resources/js/stores/**/*.ts',
      ],
      defaultExportByFilename: true,
      dts: 'resources/js/types/auto-import.d.ts',
    }),
    Component({
      dirs: [
        'resources/js/components',
      ],
      collapseSamePrefixes: true,
      directoryAsNamespace: true,
      dts: 'resources/js/types/auto-import-components.d.ts',
    }),
  ],
  resolve: {
    alias: {
      '@': fileURLToPath(new URL('./resources/js', import.meta.url)),
    },
  },
})