<script lang="ts" setup>
import { BlocksIcon } from '@lucide/vue'
</script>

<template>
  <UiSidebarMenu>
    <UiSidebarMenuItem>
      <UiSidebarMenuButton
        size="lg"
        class="justify-start group-data-[collapsible=icon]:justify-center"
        as-child
      >
        <RouterLink to="/admin">
          <div class="flex aspect-square size-8 shrink-0 items-center justify-center rounded-lg bg-sidebar-primary text-sidebar-primary-foreground">
            <BlocksIcon class="size-4" />
          </div>
          <div class="grid flex-1 text-left text-sm leading-tight group-data-[collapsible=icon]:hidden">
            <span class="truncate font-semibold">{{ $t('admin.nav.appTitle') }}</span>
            <span class="truncate text-xs">{{ $t('admin.nav.appSubtitle') }}</span>
          </div>
        </RouterLink>
      </UiSidebarMenuButton>
    </UiSidebarMenuItem>
  </UiSidebarMenu>
</template>
