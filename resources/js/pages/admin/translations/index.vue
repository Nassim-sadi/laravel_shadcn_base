<script setup lang="ts">
import { useDebounceFn } from '@vueuse/core'
import { CheckIcon, Loader2Icon, SearchIcon } from '@lucide/vue'
import { toast } from 'vue-sonner'

import type { LanguageMeta } from '@/plugins/i18n'
import { useI18n } from 'vue-i18n'

import { useTranslationsApi } from '@/services/api/translations.api'

const { t } = useI18n()

const translationsApi = useTranslationsApi()

const languages = ref<LanguageMeta[]>([])
const activeLocale = ref('fr')
const fallbackLocale = ref('fr')
const search = ref('')
const showMissingOnly = ref(false)
const loading = ref(true)
const saving = ref(false)
const saved = ref(false)
const fallbackTranslations = ref<Record<string, string | null>>({})
const localeTranslations = ref<Record<string, string | null>>({})

const visibleKeys = computed(() => {
  const allKeys = new Set([
    ...Object.keys(fallbackTranslations.value),
    ...Object.keys(localeTranslations.value),
  ])

  return Array.from(allKeys)
    .sort()
    .filter((key) => {
      const matchesSearch = key.toLowerCase().includes(search.value.toLowerCase())
        || String(localeTranslations.value[key] ?? '').toLowerCase().includes(search.value.toLowerCase())
        || String(fallbackTranslations.value[key] ?? '').toLowerCase().includes(search.value.toLowerCase())
      const isMissing = !localeTranslations.value[key]

      return matchesSearch && (!showMissingOnly.value || isMissing)
    })
})

async function loadLocale(locale: string) {
  loading.value = true
  activeLocale.value = locale

  const [fallbackResponse, localeResponse] = await Promise.all([
    translationsApi.getAdminTranslations(fallbackLocale.value),
    translationsApi.getAdminTranslations(locale),
  ])

  fallbackTranslations.value = fallbackResponse.data
  localeTranslations.value = { ...localeResponse.data }
  loading.value = false
}

const saveTranslations = useDebounceFn(async () => {
  saving.value = true
  saved.value = false

  try {
    const response = await translationsApi.updateAdminTranslations(activeLocale.value, localeTranslations.value)
    localeTranslations.value = { ...response.data }
    saved.value = true
  }
  catch {
    toast.error(t('admin.toast.saveError'))
  }
  finally {
    saving.value = false
  }
}, 800)

function updateTranslation(key: string, value: string) {
  localeTranslations.value[key] = value
  void saveTranslations()
}

onMounted(async () => {
  const localization = await translationsApi.getLocalization()
  languages.value = localization.languages
  activeLocale.value = localization.default_locale
  fallbackLocale.value = localization.fallback_locale
  await loadLocale(activeLocale.value)
})
</script>

<template>
  <div class="space-y-6 p-6">
    <div class="flex flex-col gap-3 sm:flex-row sm:items-end sm:justify-between">
      <div>
        <h1 class="text-2xl font-semibold tracking-tight">
          {{ $t('admin.page.translations.title') }}
        </h1>
        <p class="text-sm text-muted-foreground">
          {{ $t('admin.page.translations.description') }}
        </p>
      </div>

      <div class="flex items-center gap-2 text-sm text-muted-foreground">
        <Loader2Icon v-if="saving" class="size-4 animate-spin" />
        <CheckIcon v-else-if="saved" class="size-4 text-emerald-600" />
        <span>{{ saving ? $t('admin.misc.saving') : saved ? $t('admin.misc.saved') : $t('admin.misc.liveSave') }}</span>
      </div>
    </div>

    <div class="flex flex-col gap-3 lg:flex-row lg:items-center lg:justify-between">
      <UiTabs :model-value="activeLocale" @update:model-value="value => loadLocale(String(value))">
        <UiTabsList>
          <UiTabsTrigger
            v-for="language in languages"
            :key="language.code"
            :value="language.code"
          >
            <span>{{ language.flag }}</span>
            <span>{{ language.name }}</span>
          </UiTabsTrigger>
        </UiTabsList>
      </UiTabs>

      <div class="flex flex-col gap-2 sm:flex-row">
        <div class="relative">
          <SearchIcon class="absolute left-3 top-1/2 size-4 -translate-y-1/2 text-muted-foreground" />
          <UiInput v-model="search" class="pl-9 sm:w-72" :placeholder="$t('admin.misc.searchKeysOrValues')" />
        </div>
        <label class="flex h-10 items-center gap-2 rounded-md border px-3 text-sm">
          <UiCheckbox v-model:checked="showMissingOnly" />
          {{ $t('admin.label.missingOnly') }}
        </label>
      </div>
    </div>

    <div class="overflow-hidden rounded-lg border">
      <div class="grid grid-cols-[minmax(220px,0.9fr)_minmax(260px,1fr)_minmax(260px,1fr)] border-b bg-muted/40 px-4 py-3 text-sm font-medium">
        <div>{{ $t('admin.label.key') }}</div>
        <div>{{ $t('admin.misc.localeValue', { locale: activeLocale.toUpperCase() }) }}</div>
        <div>{{ $t('admin.misc.frenchFallback') }}</div>
      </div>

      <div v-if="loading" class="p-6 text-sm text-muted-foreground">
        {{ $t('admin.misc.loadingTranslations') }}
      </div>

      <div v-else-if="visibleKeys.length === 0" class="p-6 text-sm text-muted-foreground">
        {{ $t('admin.empty.translations') }}
      </div>

      <template v-else>
        <div
          v-for="key in visibleKeys"
          :key="key"
          class="grid grid-cols-[minmax(220px,0.9fr)_minmax(260px,1fr)_minmax(260px,1fr)] gap-4 border-b px-4 py-3 last:border-b-0"
        >
          <div class="break-all font-mono text-xs text-muted-foreground">
            {{ key }}
          </div>
          <UiInput
            :model-value="localeTranslations[key] ?? ''"
            :class="{ 'border-amber-400': activeLocale !== fallbackLocale && !localeTranslations[key] }"
            @update:model-value="value => updateTranslation(key, String(value))"
          />
          <div class="min-h-10 rounded-md bg-muted/50 px-3 py-2 text-sm text-muted-foreground">
            {{ fallbackTranslations[key] || '-' }}
          </div>
        </div>
      </template>
    </div>
  </div>
</template>
