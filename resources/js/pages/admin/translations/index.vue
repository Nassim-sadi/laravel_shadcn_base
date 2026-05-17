<script setup lang="ts">
import { CheckIcon, Loader2Icon, SearchIcon, Settings2Icon } from '@lucide/vue'
import { useDebounceFn, useLocalStorage } from '@vueuse/core'
import { useI18n } from 'vue-i18n'
import { toast } from 'vue-sonner'

import type { LanguageMeta } from '@/plugins/i18n'

import { useTranslationsApi } from '@/services/api/translations.api'

const { t } = useI18n()

const translationsApi = useTranslationsApi()

const languages = ref<LanguageMeta[]>([])
const activeLocale = ref('fr')
const fallbackLocale = ref('fr')
const activeFileTab = ref('')
const search = ref('')
const showMissingOnly = ref(false)
const loading = ref(true)
const saving = ref(false)
const saved = ref(false)
const localeTranslations = ref<Record<string, string | null>>({})
const fallbackLocaleTranslations = ref<Record<string, string | null>>({})
const allAvailableNamespaces = ref<string[] | null>(null)
const showNamespaceFilter = ref(false)

// Persist toggled-off namespaces per locale
const hiddenNamespaces = useLocalStorage<Record<string, string[]>>('ns-translation-hidden-tabs', {})

function fileNameForKey(key: string): string {
  const parts = key.split('.')
  if (parts.length === 1)
    return 'common'
  if (parts[0] === 'admin')
    return `admin.${parts[1]}`
  return parts[0]
}

const allKeys = computed(() => {
  const set = new Set([
    ...Object.keys(fallbackLocaleTranslations.value),
    ...Object.keys(localeTranslations.value),
  ])
  return Array.from(set).sort()
})

const allFileTabs = computed(() => {
  const groups = new Set<string>()
  for (const key of allKeys.value) {
    groups.add(fileNameForKey(key))
  }
  return Array.from(groups).sort().map((file) => {
    const parts = file.split('.')
    const label = parts.map(p => p.charAt(0).toUpperCase() + p.slice(1)).join(' > ')
    return { file, label }
  })
})

const fileTabs = computed(() => {
  let tabs = allFileTabs.value
  const hidden = hiddenNamespaces.value[activeLocale.value]
  if (hidden?.length) {
    tabs = tabs.filter(t => !hidden.includes(t.file))
  }
  return tabs
})

const displayedFallback = computed(() => {
  return activeLocale.value === fallbackLocale.value
    ? localeTranslations.value
    : fallbackLocaleTranslations.value
})

const visibleKeys = computed(() => {
  return allKeys.value.filter((key) => {
    if (fileNameForKey(key) !== activeFileTab.value)
      return false
    const matchesSearch = key.toLowerCase().includes(search.value.toLowerCase())
      || String(localeTranslations.value[key] ?? '').toLowerCase().includes(search.value.toLowerCase())
      || String(displayedFallback.value[key] ?? '').toLowerCase().includes(search.value.toLowerCase())
    const isMissing = !localeTranslations.value[key]
    return matchesSearch && (!showMissingOnly.value || isMissing)
  })
})

async function loadLocale(locale: string) {
  loading.value = true
  activeLocale.value = locale

  const [fallbackRes, localeRes] = await Promise.all([
    translationsApi.getAdminTranslations(fallbackLocale.value),
    translationsApi.getAdminTranslations(locale),
  ])

  fallbackLocaleTranslations.value = fallbackRes.data
  localeTranslations.value = { ...localeRes.data }
  loading.value = false
}

const saveTranslations = useDebounceFn(async () => {
  saving.value = true
  saved.value = false

  try {
    const response = await translationsApi.updateAdminTranslations(activeLocale.value, localeTranslations.value)
    localeTranslations.value = { ...response.data }
    if (activeLocale.value === fallbackLocale.value) {
      fallbackLocaleTranslations.value = { ...response.data }
    }
    saved.value = true
  }
  catch {
    toast.error(t('admin.toast.saveError'))
  }
  finally {
    saving.value = false
  }
}, 800)

function toggleNamespace(file: string) {
  const key = activeLocale.value
  if (!hiddenNamespaces.value[key]) {
    hiddenNamespaces.value[key] = []
  }
  const idx = hiddenNamespaces.value[key].indexOf(file)
  if (idx > -1) {
    hiddenNamespaces.value[key].splice(idx, 1)
  }
  else {
    hiddenNamespaces.value[key].push(file)
  }
  // ensure active tab is still visible
  if (fileTabs.value.length > 0 && !fileTabs.value.some(t => t.file === activeFileTab.value)) {
    activeFileTab.value = fileTabs.value[0].file
  }
}

function updateTranslation(key: string, value: string) {
  localeTranslations.value[key] = value
  void saveTranslations()
}

onMounted(async () => {
  const localization = await translationsApi.getLocalization()
  languages.value = localization.languages
  activeLocale.value = localization.default_locale
  fallbackLocale.value = localization.fallback_locale
  allAvailableNamespaces.value = localization.enabled_translation_namespaces

  await loadLocale(activeLocale.value)

  // Apply env-level namespace filtering
  if (allAvailableNamespaces.value && allAvailableNamespaces.value.length > 0) {
    const key = activeLocale.value
    if (!hiddenNamespaces.value[key]) {
      hiddenNamespaces.value[key] = []
    }
    for (const tab of allFileTabs.value) {
      if (!allAvailableNamespaces.value.includes(tab.file) && !hiddenNamespaces.value[key].includes(tab.file)) {
        hiddenNamespaces.value[key].push(tab.file)
      }
    }
  }

  if (fileTabs.value.length > 0) {
    activeFileTab.value = fileTabs.value[0].file
  }
})

watch(activeLocale, () => {
  // remove stale locale entries from hiddenNamespaces
  const valid = Object.keys(hiddenNamespaces.value).filter(k =>
    languages.value.some(l => l.code === k),
  )
  for (const k of Object.keys(hiddenNamespaces.value)) {
    if (!valid.includes(k)) {
      delete hiddenNamespaces.value[k]
    }
  }
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
      <UiTabs :model-value="activeLocale" @update:model-value="l => loadLocale(String(l))">
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
          <SearchIcon class="absolute start-3 top-1/2 size-4 -translate-y-1/2 text-muted-foreground" />
          <UiInput v-model="search" class="pl-9 sm:w-72" :placeholder="$t('admin.misc.searchKeysOrValues')" />
        </div>
        <label class="flex h-10 items-center gap-2 rounded-md border px-3 text-sm">
          <UiCheckbox v-model:checked="showMissingOnly" />
          {{ $t('admin.label.missingOnly') }}
        </label>
      </div>
    </div>

    <div v-if="fileTabs.length > 0" class="flex items-center gap-2">
      <UiTabs :model-value="activeFileTab" @update:model-value="v => activeFileTab = String(v)">
        <UiTabsList class="flex-wrap">
          <UiTabsTrigger v-for="tab in fileTabs" :key="tab.file" :value="tab.file" class="text-xs">
            {{ tab.label }}
          </UiTabsTrigger>
        </UiTabsList>
      </UiTabs>

      <div class="relative">
        <UiButton variant="outline" size="icon" class="size-9 shrink-0" @click="showNamespaceFilter = !showNamespaceFilter">
          <Settings2Icon class="size-4" />
        </UiButton>
        <div
          v-if="showNamespaceFilter"
          class="absolute end-0 top-full z-50 mt-1 w-56 rounded-lg border bg-popover p-2 shadow-md"
        >
          <div class="mb-1 px-2 py-1 text-xs font-medium text-muted-foreground">
            Toggle tabs
          </div>
          <label
            v-for="tab in allFileTabs"
            :key="tab.file"
            class="flex cursor-pointer items-center gap-2 rounded px-2 py-1 text-sm hover:bg-accent"
          >
            <UiCheckbox
              :checked="!hiddenNamespaces[activeLocale]?.includes(tab.file)"
              @update:model-value="toggleNamespace(tab.file)"
            />
            {{ tab.label }}
          </label>
        </div>
      </div>
    </div>

    <div class="overflow-hidden rounded-lg border">
      <div class="grid grid-cols-[minmax(220px,0.9fr)_minmax(260px,1fr)_minmax(260px,1fr)] border-b bg-muted/40 px-4 py-3 text-sm font-medium">
        <div>{{ $t('admin.label.key') }}</div>
        <div>{{ $t('admin.misc.localeValue', { locale: activeLocale.toUpperCase() }) }}</div>
        <div>{{ $t('admin.misc.fallbackValue') }} ({{ fallbackLocale.toUpperCase() }})</div>
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
          <div class="min-h-10 rounded-md px-3 py-2 text-sm" :class="[activeLocale === fallbackLocale ? 'bg-muted/20 text-muted-foreground/60' : 'bg-muted/50 text-muted-foreground']">
            {{ displayedFallback[key] || '-' }}
          </div>
        </div>
      </template>
    </div>
  </div>
</template>
