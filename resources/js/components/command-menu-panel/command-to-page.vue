<script setup lang="ts">
import { useI18n } from 'vue-i18n'
import { useRoute, useRouter } from 'vue-router'

import { useSidebar } from '@/composables/use-sidebar'

import type { NavGroup, NavItem } from '../app-sidebar/types'

import CommandItemHasIcon from './command-item-has-icon.vue'

const { t } = useI18n()
const emit = defineEmits<{
  (e: 'click'): void
}>()

const { navData, otherPages } = useSidebar()

function getFlatNavItems(navData: NavGroup[]): NavItem[] {
  const flatItems: NavItem[] = []
  navData.forEach((group) => {
    group.items.forEach((item) => {
      if (item.items) {
        flatItems.push(...getFlatNavItems([item as unknown as NavGroup]))
      }
      else {
        flatItems.push(item)
      }
    })
  })
  return flatItems
}

const commands = computed(() => {
  const items = getFlatNavItems([...navData.value!, ...otherPages.value!])
  return items.map(item => ({
    ...item,
    translatedTitle: t(item.title),
  }))
})

const router = useRouter()
const route = useRoute()
function commandItemClick(url: string) {
  emit('click')
  if (route.fullPath !== url) {
    router.push(url)
  }
}
</script>

<template>
  <UiCommandGroup :heading="$t('admin.misc.pages')">
    <UiCommandItem
      v-for="command in commands"
      :key="command.title"
      :value="command.translatedTitle"
      @click="commandItemClick(command.url!)"
    >
      <CommandItemHasIcon :name="command.translatedTitle" :icon="command.icon" />
    </UiCommandItem>
  </UiCommandGroup>
</template>
