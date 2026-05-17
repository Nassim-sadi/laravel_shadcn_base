<script lang="ts" setup>
import { useI18n } from 'vue-i18n'
import { storeToRefs } from 'pinia'

import type { ShadowPreset } from '@/constants/themes'
import { SHADOW_PRESETS } from '@/constants/themes'
import { useThemeStore } from '@/stores/theme'

const { t } = useI18n()
const themeStore = useThemeStore()
const { setShadowPreset } = themeStore
const { shadowPreset } = storeToRefs(themeStore)

function applyShadow(preset: ShadowPreset) {
  const p = SHADOW_PRESETS.find(sp => sp.value === preset)
  if (!p) return
  setShadowPreset(preset)
  document.documentElement.style.setProperty('--shadow-sm', p.shadows.sm)
  document.documentElement.style.setProperty('--shadow-md', p.shadows.md)
  document.documentElement.style.setProperty('--shadow-lg', p.shadows.lg)
}

watchEffect(() => {
  applyShadow(shadowPreset.value)
})
</script>

<template>
  <div class="space-y-1.5 pt-6">
    <UiLabel class="text-xs">
      {{ t('admin.theme.shadow') }}
    </UiLabel>
    <div class="grid grid-cols-3 gap-2 py-1.5">
      <UiButton
        v-for="preset in SHADOW_PRESETS"
        :key="preset.value"
        variant="outline"
        class="justify-center h-8 px-3"
        :class="shadowPreset === preset.value ? 'border-foreground border-2' : ''"
        @click="applyShadow(preset.value)"
      >
        <span class="text-xs">{{ t(preset.label) }}</span>
      </UiButton>
    </div>
  </div>
</template>
