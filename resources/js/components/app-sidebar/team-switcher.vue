<script lang="ts" setup>
import { ChevronsUpDownIcon, FolderIcon } from '@lucide/vue'

import { useSidebar } from '@/components/ui/sidebar'

import type { Team } from './types'

const { teams = [] } = defineProps<{
  teams?: Team[]
}>()

const { isMobile, open, isRtl } = useSidebar()

const activeTeam = computed(() => teams[0] ?? { name: 'Admin', logo: FolderIcon, plan: 'Admin' })
</script>

<template>
  <UiSidebarMenu>
    <UiSidebarMenuItem>
      <UiDropdownMenu>
        <UiDropdownMenuTrigger as-child>
          <UiSidebarMenuButton
            size="lg"
            class="justify-start data-[state=open]:bg-sidebar-accent data-[state=open]:text-sidebar-accent-foreground group-data-[collapsible=icon]:justify-center"
          >
            <div
              class="flex items-center justify-center rounded-lg aspect-square size-8 shrink-0 bg-sidebar-primary text-sidebar-primary-foreground"
            >
              <component :is="activeTeam.logo" class="size-4" />
            </div>
            <div class="grid flex-1 text-sm leading-tight text-left group-data-[collapsible=icon]:hidden">
              <span class="font-semibold truncate">{{ activeTeam.name }}</span>
              <span class="text-xs truncate">{{ activeTeam.plan }}</span>
            </div>
            <ChevronsUpDownIcon v-if="teams.length > 1" class="ms-auto group-data-[collapsible=icon]:hidden" />
          </UiSidebarMenuButton>
        </UiDropdownMenuTrigger>
        <UiDropdownMenuContent
          v-if="teams.length > 1"
          class="w-(--radix-dropdown-menu-trigger-width) min-w-56 rounded-lg"
          align="start"
          :side="(isMobile || open) ? 'bottom' : (isRtl ? 'left' : 'right')"
          :side-offset="4"
        >
          <UiDropdownMenuLabel class="text-xs text-muted-foreground">
            Teams
          </UiDropdownMenuLabel>
          <UiDropdownMenuItem
            v-for="team in teams"
            :key="team.name"
            class="gap-2 p-2"
          >
            <div class="flex items-center justify-center border rounded-sm size-6">
              <component :is="team.logo" class="size-4 shrink-0" />
            </div>
            {{ team.name }}
          </UiDropdownMenuItem>
        </UiDropdownMenuContent>
      </UiDropdownMenu>
    </UiSidebarMenuItem>
  </UiSidebarMenu>
</template>
