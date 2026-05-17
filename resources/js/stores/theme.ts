import { defineStore } from 'pinia'

import type { HoverPreset, Radius, ShadowPreset, Theme } from '@/constants/themes'

export const useThemeStore = defineStore('system-config', () => {
  const radius = ref(0.5)
  function setRadius(newRadius: Radius) {
    radius.value = newRadius
  }
  const theme = ref<Theme>('zinc')
  function setTheme(newTheme: Theme) {
    theme.value = newTheme
  }

  const shadowPreset = ref<ShadowPreset>('medium')
  function setShadowPreset(preset: ShadowPreset) {
    shadowPreset.value = preset
  }

  const hoverPreset = ref<HoverPreset>('medium')
  function setHoverPreset(preset: HoverPreset) {
    hoverPreset.value = preset
  }

  return {
    radius,
    setRadius,
    theme,
    setTheme,
    shadowPreset,
    setShadowPreset,
    hoverPreset,
    setHoverPreset,
  }
}, {
  persist: true,
})
