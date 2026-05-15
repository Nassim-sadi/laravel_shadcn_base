import type { App } from 'vue'

import { createI18n } from 'vue-i18n'

import type { Language } from '.'

import { appLocale, DEFAULT_LOCALE } from '.'
import en from './en.json'
import zh from './zh.json'
import fr from './fr.json'
import ar from './ar.json'

export function setupI18n(app: App) {
  const i18n = createI18n({
    legacy: false,
    locale: appLocale.value,
    fallbackLocale: DEFAULT_LOCALE,
    messages: <Record<Language, Record<string, any>>>{
      zh,
      en,
      fr,
      ar,
    },
  })
  app.use(i18n)
}
