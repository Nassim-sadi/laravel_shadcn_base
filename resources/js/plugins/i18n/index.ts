import { useStorage } from '@vueuse/core'

export type Language = string

export interface LanguageMeta {
  code: Language
  name: string
  flag: string
  direction: 'ltr' | 'rtl'
}

export const SUPPORTED_LOCALES = new Set<Language>([
  'fr',
  'en',
  'ar',
])

export const DEFAULT_LOCALE: Language = 'fr'
export const FALLBACK_LOCALE: Language = 'fr'
export const languageMetadata = ref<LanguageMeta[]>([
  { code: 'fr', name: 'Français', flag: '🇫🇷', direction: 'ltr' },
  { code: 'en', name: 'English', flag: '🇬🇧', direction: 'ltr' },
  { code: 'ar', name: 'العربية', flag: '🇩🇿', direction: 'rtl' },
])

export const appLocale = useStorage<Language>('app-locale', DEFAULT_LOCALE)

export function setLanguageConfig(languages: LanguageMeta[], defaultLocale: string) {
  SUPPORTED_LOCALES.clear()
  languages.forEach(language => SUPPORTED_LOCALES.add(language.code))
  languageMetadata.value = languages

  if (!SUPPORTED_LOCALES.has(appLocale.value)) {
    appLocale.value = defaultLocale
  }
}

export function activeLanguageDirection(locale = appLocale.value) {
  return languageMetadata.value.find(language => language.code === locale)?.direction ?? 'ltr'
}

watch(appLocale, (newLocale) => {
  if (!SUPPORTED_LOCALES.has(newLocale)) {
    appLocale.value = DEFAULT_LOCALE
  }

  document.documentElement.lang = appLocale.value
  document.documentElement.dir = activeLanguageDirection(appLocale.value)
}, { immediate: true })
