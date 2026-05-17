<script setup lang="ts">
import { useI18n } from 'vue-i18n'
import { MoonIcon, SunIcon, SunMoonIcon } from '@lucide/vue'
import { useColorMode } from '@vueuse/core'

import CommandItemHasIcon from './command-item-has-icon.vue'

const { t } = useI18n()
defineEmits<{
  (e: 'click'): void
}>()

const mode = useColorMode()
</script>

<template>
  <UiCommandGroup :heading="t('admin.theme.colorMode')">
    <UiCommandItem value="light" @click="mode = 'light', $emit('click')">
      <CommandItemHasIcon :name="t('admin.theme.light')" :icon="SunIcon" />
    </UiCommandItem>
    <UiCommandItem value="dark" @click="mode = 'dark', $emit('click')">
      <CommandItemHasIcon :name="t('admin.theme.dark')" :icon="MoonIcon" />
    </UiCommandItem>
    <UiCommandItem value="system" @click="mode = 'auto', $emit('click')">
      <CommandItemHasIcon :name="t('admin.theme.system')" :icon="SunMoonIcon" />
    </UiCommandItem>
  </UiCommandGroup>
</template>
