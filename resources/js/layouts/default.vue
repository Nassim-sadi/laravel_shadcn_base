<script setup lang="ts">
import { useCookies } from '@vueuse/integrations/useCookies'
import { watch } from 'vue'

import AppSidebar from '@/components/app-sidebar/index.vue'
import CommandMenuPanel from '@/components/command-menu-panel/index.vue'
import ThemePopover from '@/components/custom-theme/theme-popover.vue'
import LanguageChange from '@/components/language-change.vue'
import ToggleTheme from '@/components/toggle-theme.vue'
import { SIDEBAR_COOKIE_NAME } from '@/components/ui/sidebar/utils'
import { useUserQuery } from '@/services/api/auth.api'
import { useAuthStore } from '@/stores/auth'
const defaultOpen = useCookies([SIDEBAR_COOKIE_NAME])
const authStore = useAuthStore()

const { data: userData } = useUserQuery()
watch(userData, (user) => {
  if (user?.data) authStore.setUser(user.data)
}, { immediate: true })
</script>

<template>
  <UiSidebarProvider :default-open="defaultOpen.get(SIDEBAR_COOKIE_NAME)">
    <AppSidebar />
    <UiSidebarInset class="w-full max-w-full peer-data-[state=collapsed]:w-[calc(100%-var(--sidebar-width-icon)-1rem)] peer-data-[state=expanded]:w-[calc(100%-var(--sidebar-width))]">
      <header
        class="flex items-center gap-3 sm:gap-4 h-16 p-4 shrink-0 transition-[width,height] ease-linear"
      >
        <UiSidebarTrigger class="-ms-1" />
        <UiSeparator orientation="vertical" class="h-6" />
        <CommandMenuPanel />
        <div class="flex-1" />
        <div class="ms-auto flex items-center space-x-4">
          <LanguageChange />
          <ToggleTheme />
          <ThemePopover />
        </div>
      </header>

      <main
        class="p-4 grow"
      >
        <router-view />
      </main>
    </UiSidebarInset>
  </UiSidebarProvider>
</template>
