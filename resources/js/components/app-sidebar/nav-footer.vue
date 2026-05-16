<script setup lang="ts">
import { BadgeCheckIcon, BellIcon, ChevronsUpDownIcon, LogOutIcon, UserRoundCogIcon } from '@lucide/vue'

import { useSidebar } from '@/components/ui/sidebar'
import { useAuth } from '@/composables/use-auth'

import type { User } from './types'

const { user } = defineProps<
  { user: User }
>()

const { logout } = useAuth()
const { isMobile, open, isRtl } = useSidebar()
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
            <UiAvatar class="size-8 shrink-0 rounded-lg">
              <UiAvatarImage :src="user.avatar ?? ''" :alt="user.name" />
              <UiAvatarFallback class="rounded-lg">
                {{ user.name?.charAt(0) ?? 'A' }}
              </UiAvatarFallback>
            </UiAvatar>
            <div class="grid flex-1 text-sm leading-tight text-left group-data-[collapsible=icon]:hidden">
              <span class="font-semibold truncate">{{ user.name }}</span>
              <span class="text-xs truncate">{{ user.email }}</span>
            </div>
            <ChevronsUpDownIcon class="ms-auto size-4 group-data-[collapsible=icon]:hidden" />
          </UiSidebarMenuButton>
        </UiDropdownMenuTrigger>
        <UiDropdownMenuContent
          class="w-(--radix-dropdown-menu-trigger-width) min-w-56 rounded-lg"
          :side="(isMobile || open) ? 'bottom' : (isRtl ? 'left' : 'right')"
          align="start"
          :side-offset="4"
        >
          <UiDropdownMenuLabel class="p-0 font-normal">
            <div class="flex items-center gap-2 px-1 py-1.5 text-left text-sm">
              <UiAvatar class="size-8 rounded-lg">
                <UiAvatarImage :src="user.avatar ?? ''" :alt="user.name" />
                <UiAvatarFallback class="rounded-lg">
                  {{ user.name?.charAt(0) ?? 'A' }}
                </UiAvatarFallback>
              </UiAvatar>
              <div class="grid flex-1 text-sm leading-tight text-left">
                <span class="font-semibold truncate">{{ user.name }}</span>
                <span class="text-xs truncate">{{ user.email }}</span>
              </div>
            </div>
          </UiDropdownMenuLabel>

          <UiDropdownMenuSeparator />
          <UiDropdownMenuGroup>
            <UiDropdownMenuItem @click="$router.push('/admin/settings')">
              <UserRoundCogIcon />
              {{ $t('admin.nav.profile') }}
            </UiDropdownMenuItem>
            <UiDropdownMenuItem @click="$router.push('/admin/settings/account')">
              <BadgeCheckIcon />
              {{ $t('admin.nav.account') }}
            </UiDropdownMenuItem>
            <UiDropdownMenuItem @click="$router.push('/admin/settings/notifications')">
              <BellIcon />
              {{ $t('admin.nav.notifications') }}
            </UiDropdownMenuItem>
          </UiDropdownMenuGroup>

          <UiDropdownMenuSeparator />
          <UiDropdownMenuItem @click="logout">
            <LogOutIcon />
            {{ $t('admin.nav.logout') }}
          </UiDropdownMenuItem>
        </UiDropdownMenuContent>
      </UiDropdownMenu>
    </UiSidebarMenuItem>
  </UiSidebarMenu>
</template>
