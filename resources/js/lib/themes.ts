import amethystHaze from '@/themes/amethyst-haze.json'
import neutral from '@/themes/neutral.json'
import amberWarmth from '@/themes/amber-warmth.json'
import terracotta from '@/themes/terracotta.json'
import brutalist from '@/themes/brutalist.json'
import rosePop from '@/themes/rose-pop.json'
import emeraldBreeze from '@/themes/emerald-breeze.json'
import sageGarden from '@/themes/sage-garden.json'
import retroPop from '@/themes/retro-pop.json'
import mintFresh from '@/themes/mint-fresh.json'
import twilightBlue from '@/themes/twilight-blue.json'
import oceanBreeze from '@/themes/ocean-breeze.json'
import violetDream from '@/themes/violet-dream.json'

export interface ThemeColors {
  [key: string]: string
}

export interface Theme {
  id: string
  name: string
  colors: {
    light: ThemeColors
    dark: ThemeColors
  }
  fonts?: {
    sans?: string
    mono?: string
    serif?: string
  }
}

export const themes: Theme[] = [
  amethystHaze as Theme,
  neutral as Theme,
  amberWarmth as Theme,
  terracotta as Theme,
  brutalist as Theme,
  rosePop as Theme,
  emeraldBreeze as Theme,
  sageGarden as Theme,
  retroPop as Theme,
  mintFresh as Theme,
  twilightBlue as Theme,
  oceanBreeze as Theme,
  violetDream as Theme,
]

export function getThemeById(id: string): Theme | undefined {
  return themes.find(t => t.id === id)
}
