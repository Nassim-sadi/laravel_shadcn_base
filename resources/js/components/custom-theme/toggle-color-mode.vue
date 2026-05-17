<script lang="ts" setup>
import type { BasicColorSchema } from '@vueuse/core'
import type { Component } from 'vue'

import { useI18n } from 'vue-i18n'
import { MoonIcon, SunIcon, SunMoonIcon } from '@lucide/vue'
import { useColorMode } from '@vueuse/core'

const { t } = useI18n()
const mode = useColorMode()

const colorModes: {
  colorMode: BasicColorSchema
  icon: Component
  label: string
}[] = [
  { colorMode: 'light', icon: SunIcon, label: 'admin.theme.light' },
  { colorMode: 'dark', icon: MoonIcon, label: 'admin.theme.dark' },
  { colorMode: 'auto', icon: SunMoonIcon, label: 'admin.theme.system' },
]

function setColorMode(colorMode: BasicColorSchema) {
  mode.value = colorMode
}
</script>

<template>
  <div class="space-y-1.5 pt-6">
    <UiLabel class="text-xs">
      {{ t('admin.theme.colorMode') }}
    </UiLabel>
    <div class="grid grid-cols-3 gap-2 py-1.5">
      <UiButton
        v-for="item in colorModes" :key="item.colorMode"
        variant="outline"
        class="justify-center items-center h-8 px-3"
        :class="item.colorMode === mode ? 'border-foreground border-2' : ''"
        @click="setColorMode(item.colorMode)"
      >
        <component :is="item.icon" />
        <span class="text-xs">{{ t(item.label) }}</span>
      </UiButton>
    </div>
  </div>
</template>
