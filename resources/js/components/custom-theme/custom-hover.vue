<script lang="ts" setup>
import { useI18n } from 'vue-i18n'
import { storeToRefs } from 'pinia'

import type { HoverPreset } from '@/constants/themes'
import { HOVER_PRESETS } from '@/constants/themes'
import { useThemeStore } from '@/stores/theme'

const { t } = useI18n()
const themeStore = useThemeStore()
const { setHoverPreset } = themeStore
const { hoverPreset } = storeToRefs(themeStore)

function applyHover(preset: HoverPreset) {
  const p = HOVER_PRESETS.find(hp => hp.value === preset)
  if (!p) return
  setHoverPreset(preset)
  document.documentElement.style.setProperty('--hover-overlay', String(p.overlay))
}

watchEffect(() => {
  applyHover(hoverPreset.value)
})
</script>

<template>
  <div class="space-y-1.5 pt-6">
    <UiLabel class="text-xs">
      {{ t('admin.theme.hover') }}
    </UiLabel>
    <div class="grid grid-cols-3 gap-2 py-1.5">
      <UiButton
        v-for="preset in HOVER_PRESETS"
        :key="preset.value"
        variant="outline"
        class="justify-center h-8 px-3"
        :class="hoverPreset === preset.value ? 'border-foreground border-2' : ''"
        @click="applyHover(preset.value)"
      >
        <span class="text-xs">{{ t(preset.label) }}</span>
      </UiButton>
    </div>
  </div>
</template>
