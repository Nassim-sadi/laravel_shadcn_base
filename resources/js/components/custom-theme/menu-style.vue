<script lang="ts" setup>
import { ChevronRightIcon, LayoutListIcon } from '@lucide/vue'
import { storeToRefs } from 'pinia'

import { useI18n } from 'vue-i18n'
import type { NavigationMode } from '@/stores/sidebar-config'

import { useSidebarConfigStore } from '@/stores/sidebar-config'

const { t } = useI18n()
const sidebarConfigStore = useSidebarConfigStore()
const { navigationMode } = storeToRefs(sidebarConfigStore)

const menuStyles: Array<{
  value: NavigationMode
  label: string
  icon: any
  description: string
}> = [
  {
    value: 'collapsible',
    label: 'admin.theme.collapsible',
    icon: LayoutListIcon,
    description: 'admin.theme.collapsibleDescription',
  },
  {
    value: 'vercel',
    label: 'admin.theme.vercel',
    icon: ChevronRightIcon,
    description: 'admin.theme.vercelDescription',
  },
]

function handleMenuStyleChange(style: NavigationMode) {
  sidebarConfigStore.setNavigationMode(style)
}
</script>

<template>
  <div class="space-y-1.5 pt-6">
    <UiLabel class="text-xs">
      {{ t('admin.theme.menuStyle') }}
    </UiLabel>
    <div class="grid grid-cols-2 gap-2 py-1.5">
      <UiButton
        v-for="style in menuStyles"
        :key="style.value"
        variant="outline"
        class="justify-center h-8 px-3"
        :class="navigationMode === style.value ? 'border-foreground border-2' : ''"
        :title="t(style.description)"
        @click="handleMenuStyleChange(style.value)"
      >
        <component :is="style.icon" class="w-4 h-4" />
        {{ t(style.label) }}
      </UiButton>
    </div>
  </div>
</template>
