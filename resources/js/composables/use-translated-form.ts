import { languageMetadata } from '@/plugins/i18n'

export type TranslatedValue = Record<string, string>

export function emptyTranslations(): TranslatedValue {
  return Object.fromEntries(languageMetadata.value.map(language => [language.code, '']))
}

export function withLanguages(value?: TranslatedValue | null, fallback?: string | null): TranslatedValue {
  return Object.fromEntries(languageMetadata.value.map((language) => {
    return [language.code, value?.[language.code] ?? (language.code === 'fr' ? fallback ?? '' : '')]
  }))
}
