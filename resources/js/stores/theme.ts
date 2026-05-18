import { defineStore } from 'pinia'

export const useThemeStore = defineStore('theme', () => {
  const themeId = ref<string>('amethyst-haze')
  function setTheme(id: string) {
    themeId.value = id
  }

  const radius = ref(0.5)
  function setRadius(value: number) {
    radius.value = value
  }

  return { themeId, setTheme, radius, setRadius }
}, {
  persist: true,
})
