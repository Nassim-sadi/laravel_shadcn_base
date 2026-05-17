<script lang="ts" setup>
import AppIdentity from './app-identity.vue'
import { sidebarData } from './data/sidebar-data'
import NavFooter from './nav-footer.vue'
import NavTeam from './nav-team.vue'
</script>

<template>
  <UiSidebar collapsible="icon" class="z-50">
    <UiSidebarHeader>
      <AppIdentity />
    </UiSidebarHeader>

    <UiSidebarContent>
      <NavTeam :nav-main="sidebarData.navMain" />
    </UiSidebarContent>

    <UiSidebarFooter>
      <NavFooter :user="sidebarData.user" />
    </UiSidebarFooter>

    <UiSidebarRail />
  </UiSidebar>
</template>
