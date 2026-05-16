import { helpers } from '@vuelidate/validators'
import { languageMetadata } from '@/plugins/i18n'
import type { TranslatedValue } from '@/composables/use-translated-form'

export function translatedRequired() {
  const defaultCode = languageMetadata.value[0]?.code ?? 'fr'
  return helpers.withMessage(
    `The ${defaultCode} translation is required`,
    (v: TranslatedValue) => !!v?.[defaultCode]?.trim(),
  )
}
