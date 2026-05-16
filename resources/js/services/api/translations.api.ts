import type { LanguageMeta } from '@/plugins/i18n'

import { useApiFetch } from '@/composables/use-fetch'

export interface LocalizationPayload {
  languages: LanguageMeta[]
  default_locale: string
  fallback_locale: string
  enabled_translation_namespaces: string[] | null
}

export function useTranslationsApi() {
  const { apiFetch } = useApiFetch()

  const getLocalization = () => apiFetch<LocalizationPayload>('/localization')

  const getAdminTranslations = (locale: string) => {
    return apiFetch<{ data: Record<string, string | null> }>(`/admin/translations/${locale}`)
  }

  const updateAdminTranslations = (locale: string, translations: Record<string, string | null>) => {
    return apiFetch<{ message: string, data: Record<string, string | null> }>(`/admin/translations/${locale}`, {
      method: 'PUT',
      body: { translations },
    })
  }

  return {
    getLocalization,
    getAdminTranslations,
    updateAdminTranslations,
  }
}
