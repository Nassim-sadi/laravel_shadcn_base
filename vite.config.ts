import type { PluginOption } from 'vite'

import tailwindcss from '@tailwindcss/vite'
import vue from '@vitejs/plugin-vue'
import vueJsx from '@vitejs/plugin-vue-jsx'
import { fileURLToPath, URL } from 'node:url'
import { visualizer } from 'rollup-plugin-visualizer'
import AutoImport from 'unplugin-auto-import/vite'
import Component from 'unplugin-vue-components/vite'
import { defineConfig } from 'vite'
import vueDevTools from 'vite-plugin-vue-devtools'
import Layouts from 'vite-plugin-vue-layouts'
import { VueRouterAutoImports } from 'vue-router/unplugin'
import VueRouter from 'vue-router/vite'
import laravel from 'laravel-vite-plugin'

const RouteGenerateExclude = ['**/components/**', '**/layouts/**', '**/data/**', '**/types/**']

export default defineConfig({
  plugins: [
    laravel({
      input: ['resources/js/src/main.ts'],
      refresh: ['resources/views/**'],
    }),
    VueRouter({
      exclude: RouteGenerateExclude,
      dts: 'resources/js/src/types/route-map.d.ts',
    }),
    vue(),
    vueJsx(),
    vueDevTools(),
    tailwindcss(),
    visualizer({ gzipSize: true, brotliSize: true }) as PluginOption,
    Layouts({
      defaultLayout: 'default',
    }),
    AutoImport({
      include: [
        /\.[tj]sx?$/,
        /\.vue$/,
      ],
      imports: [
        'vue',
        VueRouterAutoImports,
      ],
      dirs: [
        'resources/js/src/composables/**/*.ts',
        'resources/js/src/constants/**/*.ts',
        'resources/js/src/stores/**/*.ts',
      ],
      defaultExportByFilename: true,
      dts: 'resources/js/src/types/auto-import.d.ts',
    }),
    Component({
      dirs: [
        'resources/js/src/components',
      ],
      collapseSamePrefixes: true,
      directoryAsNamespace: true,
      dts: 'resources/js/src/types/auto-import-components.d.ts',
    }),
  ],
  resolve: {
    alias: {
      '@': fileURLToPath(new URL('./resources/js/src', import.meta.url)),
    },
  },
})