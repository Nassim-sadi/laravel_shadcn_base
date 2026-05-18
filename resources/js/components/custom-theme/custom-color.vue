<script lang="ts" setup>
import { storeToRefs } from 'pinia'
import { useI18n } from 'vue-i18n'

import { themes } from '@/lib/themes'
import { useThemeStore } from '@/stores/theme'

const { t } = useI18n()
const themeStore = useThemeStore()
const { setTheme } = themeStore
const { themeId: current } = storeToRefs(themeStore)
</script>

<template>
  <div class="space-y-1.5 pt-6">
    <UiLabel class="text-xs">
      {{ t('admin.theme.color') }}
    </UiLabel>
    <div class="grid grid-cols-2 gap-2 py-1.5">
      <UiButton
        v-for="theme in themes"
        :key="theme.id"
        variant="outline"
        class="justify-center h-8 px-3"
        :class="current === theme.id ? 'border-foreground border-2' : ''"
        @click="setTheme(theme.id)"
      >
        <span
          class="size-2 rounded-full"
          :style="{ backgroundColor: theme.colors.light['--primary'] }"
        />
        <span class="text-xs truncate ms-1.5">{{ theme.name }}</span>
      </UiButton>
    </div>
  </div>
</template>
