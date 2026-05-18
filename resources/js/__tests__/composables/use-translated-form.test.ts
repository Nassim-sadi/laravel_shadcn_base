import { describe, expect, it, vi } from 'vitest'

vi.mock('@/plugins/i18n', () => ({
  languageMetadata: { value: [
    { code: 'fr', name: 'Français', flag: '🇫🇷', direction: 'ltr' },
    { code: 'en', name: 'English', flag: '🇬🇧', direction: 'ltr' },
    { code: 'ar', name: 'العربية', flag: '🇩🇿', direction: 'rtl' },
  ]},
  appLocale: { value: 'fr' },
  SUPPORTED_LOCALES: new Set(['fr', 'en', 'ar']),
  DEFAULT_LOCALE: 'fr',
  FALLBACK_LOCALE: 'fr',
}))

import { emptyTranslations, withLanguages } from '@/composables/use-translated-form'

describe('emptyTranslations', () => {
  it('returns object with empty strings for all languages', () => {
    const result = emptyTranslations()
    expect(result).toEqual({ fr: '', en: '', ar: '' })
  })
})

describe('withLanguages', () => {
  it('returns translations from provided value', () => {
    const result = withLanguages({ fr: 'Bonjour', en: 'Hello' })
    expect(result.fr).toBe('Bonjour')
    expect(result.en).toBe('Hello')
    expect(result.ar).toBe('')
  })

  it('uses fallback for fr when value is missing fr key', () => {
    const result = withLanguages({ en: 'Hello' }, 'Fallback FR')
    expect(result.fr).toBe('Fallback FR')
    expect(result.en).toBe('Hello')
  })

  it('returns empty strings when no value or fallback given', () => {
    const result = withLanguages()
    expect(result).toEqual({ fr: '', en: '', ar: '' })
  })

  it('handles null value gracefully', () => {
    const result = withLanguages(null, 'Default')
    expect(result.fr).toBe('Default')
    expect(result.en).toBe('')
    expect(result.ar).toBe('')
  })
})
