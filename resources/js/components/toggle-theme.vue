<script setup lang="ts">
import { useI18n } from 'vue-i18n'
import { MoonIcon, SunIcon, SunMoonIcon } from '@lucide/vue'
import { useColorMode } from '@vueuse/core'

const { t } = useI18n()
const mode = useColorMode()
</script>

<template>
  <UiDropdownMenu>
    <UiDropdownMenuTrigger as-child>
      <UiButton variant="outline" size="icon">
        <MoonIcon class=" rotate-0 scale-100 transition-all dark:-rotate-90 dark:scale-0" />
        <SunIcon class="absolute  rotate-90 scale-0 transition-all dark:rotate-0 dark:scale-100" />
        <span class="sr-only">{{ t('admin.theme.colorMode') }}</span>
      </UiButton>
    </UiDropdownMenuTrigger>
    <UiDropdownMenuContent align="end">
      <UiDropdownMenuItem @click="mode = 'light'">
        <SunIcon />
        {{ t('admin.theme.light') }}
      </UiDropdownMenuItem>
      <UiDropdownMenuItem @click="mode = 'dark'">
        <MoonIcon />
        {{ t('admin.theme.dark') }}
      </UiDropdownMenuItem>
      <UiDropdownMenuItem @click="mode = 'auto'">
        <SunMoonIcon />
        {{ t('admin.theme.system') }}
      </UiDropdownMenuItem>
    </UiDropdownMenuContent>
  </UiDropdownMenu>
</template>
