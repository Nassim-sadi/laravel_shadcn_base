<script setup lang="ts">
import { BasicPage, TwoColAside, TwoColLayout } from '@/components/global-layout'

const settingsNavItems = [
  { title: 'Profile', url: '/adminDashboard/settings' },
  { title: 'Account', url: '/adminDashboard/settings/account' },
  { title: 'Appearance', url: '/adminDashboard/settings/appearance' },
  { title: 'Notifications', url: '/adminDashboard/settings/notifications' },
  { title: 'Display', url: '/adminDashboard/settings/display' },
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
