export const THEMES = ['zinc', 'red', 'rose', 'orange', 'green', 'blue', 'yellow', 'violet'] as const
export type Theme = typeof THEMES[number]

export const THEME_PRIMARY_COLORS: { theme: Theme, primaryColor: string }[] = [
  { theme: 'zinc', primaryColor: 'oklch(44.2% 0.017 285.786)' },
  { theme: 'red', primaryColor: 'oklch(57.7% 0.245 27.325)' },
  { theme: 'rose', primaryColor: 'oklch(0.645 0.246 16.439)' },
  { theme: 'orange', primaryColor: 'oklch(0.705 0.213 47.604)' },
  { theme: 'green', primaryColor: 'oklch(0.723 0.219 149.579)' },
  { theme: 'blue', primaryColor: 'oklch(48.8% 0.243 264.376)' },
  { theme: 'yellow', primaryColor: 'oklch(68.1% 0.162 75.834)' },
  { theme: 'violet', primaryColor: 'oklch(0.606 0.25 292.717)' },
] as const

export type Radius = typeof RADIUS[number]
export const RADIUS = [0, 0.25, 0.5, 0.75, 1] as const

export type ShadowPreset = 'subtle' | 'medium' | 'prominent'
export const SHADOW_PRESETS: { value: ShadowPreset, label: string, shadows: { sm: string, md: string, lg: string } }[] = [
  { value: 'subtle', label: 'admin.theme.shadowSubtle', shadows: { sm: '0 1px 2px 0 rgb(0 0 0 / 0.03)', md: '0 4px 6px -1px rgb(0 0 0 / 0.05)', lg: '0 10px 15px -3px rgb(0 0 0 / 0.05)' } },
  { value: 'medium', label: 'admin.theme.shadowMedium', shadows: { sm: '0 1px 2px 0 rgb(0 0 0 / 0.05)', md: '0 4px 6px -1px rgb(0 0 0 / 0.1)', lg: '0 10px 15px -3px rgb(0 0 0 / 0.1)' } },
  { value: 'prominent', label: 'admin.theme.shadowProminent', shadows: { sm: '0 1px 3px 0 rgb(0 0 0 / 0.08)', md: '0 6px 10px -2px rgb(0 0 0 / 0.15)', lg: '0 12px 20px -4px rgb(0 0 0 / 0.15)' } },
] as const

export type HoverPreset = 'subtle' | 'medium' | 'strong'
export const HOVER_PRESETS: { value: HoverPreset, label: string, overlay: number }[] = [
  { value: 'subtle', label: 'admin.theme.hoverSubtle', overlay: 0.03 },
  { value: 'medium', label: 'admin.theme.hoverMedium', overlay: 0.05 },
  { value: 'strong', label: 'admin.theme.hoverStrong', overlay: 0.1 },
] as const
