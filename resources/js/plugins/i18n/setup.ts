import type { App } from 'vue'

import { ofetch } from 'ofetch'
import { createI18n } from 'vue-i18n'

import type { Language, LanguageMeta } from '.'

import { appLocale, DEFAULT_LOCALE, FALLBACK_LOCALE, setLanguageConfig } from '.'
import { API_BASE_URL } from '@/constants/app-config'

interface LocalizationResponse {
  languages: LanguageMeta[]
  default_locale: string
  fallback_locale: string
}

function unflattenTranslations(translations: Record<string, string | null>) {
  return Object.entries(translations).reduce<Record<string, any>>((messages, [key, value]) => {
    const segments = key.split('.')
    let cursor = messages

    segments.forEach((segment, index) => {
      if (index === segments.length - 1) {
        cursor[segment] = value ?? ''
        return
      }

      cursor[segment] ??= {}
      cursor = cursor[segment]
    })

    return messages
  }, {})
}

async function loadLocalization() {
  try {
    return await ofetch<LocalizationResponse>('/localization', {
      baseURL: API_BASE_URL,
    })
  }
  catch {
    return {
      languages: [
        { code: 'fr', name: 'Français', flag: '🇫🇷', direction: 'ltr' },
        { code: 'en', name: 'English', flag: '🇬🇧', direction: 'ltr' },
        { code: 'ar', name: 'العربية', flag: '🇩🇿', direction: 'rtl' },
      ],
      default_locale: DEFAULT_LOCALE,
      fallback_locale: FALLBACK_LOCALE,
    } satisfies LocalizationResponse
  }
}

async function loadMessages(locales: string[]) {
  const entries = await Promise.all(locales.map(async (locale) => {
    try {
      const translations = await ofetch<Record<string, string | null>>(`/translations/${locale}`, {
        baseURL: API_BASE_URL,
      })

      return [locale, unflattenTranslations(translations)] as const
    }
    catch {
      return [locale, {}] as const
    }
  }))

  return Object.fromEntries(entries) as Record<Language, Record<string, any>>
}

export async function setupI18n(app: App) {
  const localization = await loadLocalization()
  setLanguageConfig(localization.languages, localization.default_locale)
  const messages = await loadMessages(localization.languages.map(language => language.code))

  const i18n = createI18n({
    legacy: false,
    locale: appLocale.value,
    fallbackLocale: localization.fallback_locale,
    messages,
  })
  app.use(i18n)
}
