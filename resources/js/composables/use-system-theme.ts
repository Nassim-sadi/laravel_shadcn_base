import { storeToRefs } from 'pinia'
import { watch } from 'vue'

import { HOVER_PRESETS, SHADOW_PRESETS, THEMES } from '@/constants/themes'
import { useThemeStore } from '@/stores/theme'

export function useSystemTheme() {
  const themeStore = useThemeStore()
  const { setTheme, setRadius, setShadowPreset, setHoverPreset } = themeStore
  const { theme, radius, shadowPreset, hoverPreset } = storeToRefs(themeStore)

  if (typeof document !== 'undefined') {
    watch(theme, (theme) => {
      document.documentElement.classList.remove(...THEMES.map(t => `theme-${t}`))
      document.documentElement.classList.add(`theme-${theme}`)
    }, { immediate: true })

    watch(radius, (radius) => {
      document.documentElement.style.setProperty('--radius', `${radius}rem`)
    }, { immediate: true })

    watch(shadowPreset, (preset) => {
      const p = SHADOW_PRESETS.find(sp => sp.value === preset)
      if (p) {
        document.documentElement.style.setProperty('--shadow-sm', p.shadows.sm)
        document.documentElement.style.setProperty('--shadow-md', p.shadows.md)
        document.documentElement.style.setProperty('--shadow-lg', p.shadows.lg)
      }
    }, { immediate: true })

    watch(hoverPreset, (preset) => {
      const p = HOVER_PRESETS.find(hp => hp.value === preset)
      if (p) {
        document.documentElement.style.setProperty('--hover-overlay', String(p.overlay))
      }
    }, { immediate: true })
  }

  return {
    theme,
    radius,
    shadowPreset,
    hoverPreset,
    setTheme,
    setRadius,
    setShadowPreset,
    setHoverPreset,
  }
}
