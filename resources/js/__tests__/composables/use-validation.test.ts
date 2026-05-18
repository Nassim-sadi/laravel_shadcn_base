import { describe, expect, it, vi } from 'vitest'

vi.mock('@/plugins/i18n', () => ({
  languageMetadata: { value: [{ code: 'fr', name: 'Français', flag: '🇫🇷', direction: 'ltr' }] },
  appLocale: { value: 'fr' },
  SUPPORTED_LOCALES: new Set(['fr', 'en', 'ar']),
  DEFAULT_LOCALE: 'fr',
  FALLBACK_LOCALE: 'fr',
}))

import { translatedRequired } from '@/composables/use-validation'

describe('translatedRequired', () => {
  it('returns true when default locale translation is filled', () => {
    const rule = translatedRequired()
    expect(rule.$validator({ fr: 'Hello' }, {} as any, {} as any)).toBe(true)
  })

  it('returns false when default locale translation is empty', () => {
    const rule = translatedRequired()
    expect(rule.$validator({ fr: '' }, {} as any, {} as any)).toBe(false)
  })

  it('returns false when default locale translation is only whitespace', () => {
    const rule = translatedRequired()
    expect(rule.$validator({ fr: '   ' }, {} as any, {} as any)).toBe(false)
  })

  it('returns false when value is null', () => {
    const rule = translatedRequired()
    expect(rule.$validator(null, {} as any, {} as any)).toBe(false)
  })

  it('returns false when value is undefined', () => {
    const rule = translatedRequired()
    expect(rule.$validator(undefined, {} as any, {} as any)).toBe(false)
  })
})
