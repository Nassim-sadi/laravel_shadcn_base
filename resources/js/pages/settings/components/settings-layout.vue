<script setup lang="ts">
import { BasicPage, TwoColAside, TwoColLayout } from '@/components/global-layout'

const settingsNavItems = [
  { title: 'Profile', url: '/admin/settings' },
  { title: 'Account', url: '/admin/settings/account' },
  { title: 'Appearance', url: '/admin/settings/appearance' },
  { title: 'Notifications', url: '/admin/settings/notifications' },
  { title: 'Display', url: '/admin/settings/display' },
]
</script>

<template>
  <BasicPage
    title="Settings"
    description="Manage your store settings."
  >
    <TwoColLayout>
      <template #aside>
        <TwoColAside :nav="settingsNavItems" />
      </template>

      <slot />
    </TwoColLayout>
  </BasicPage>
</template>
