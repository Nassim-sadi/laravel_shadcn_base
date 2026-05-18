import { useColorMode } from '@vueuse/core'
import { storeToRefs } from 'pinia'
import { watch } from 'vue'

import { getThemeById } from '@/lib/themes'
import { useThemeStore } from '@/stores/theme'

function applyThemeVariables(themeId: string, mode: 'light' | 'dark') {
  const theme = getThemeById(themeId)
  if (!theme) return

  const colors = mode === 'dark' ? theme.colors.dark : theme.colors.light

  Object.entries(colors).forEach(([key, value]) => {
    document.documentElement.style.setProperty(key, value)
  })

  if (theme.fonts) {
    if (theme.fonts.sans) document.documentElement.style.setProperty('--font-sans', theme.fonts.sans)
    if (theme.fonts.mono) document.documentElement.style.setProperty('--font-mono', theme.fonts.mono)
    if (theme.fonts.serif) document.documentElement.style.setProperty('--font-serif', theme.fonts.serif)
  }
}

export function useSystemTheme() {
  const themeStore = useThemeStore()
  const { setTheme, setRadius } = themeStore
  const { themeId, radius } = storeToRefs(themeStore)
  const mode = useColorMode()

  if (typeof document !== 'undefined') {
    watch(
      [themeId, mode],
      ([id, currentMode]) => {
        const colorMode = currentMode === 'auto'
          ? (window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light')
          : currentMode
        applyThemeVariables(id, colorMode)
      },
      { immediate: true },
    )

    watch(radius, (r) => {
      document.documentElement.style.setProperty('--radius', `${r}rem`)
    }, { immediate: true })
  }

  return { themeId, radius, setTheme, setRadius }
}
