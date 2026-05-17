<script lang="ts" setup>
import { useSidebarConfigStore } from '@/stores/sidebar-config'

import type { NavGroup } from './types'

import NavTeamCollapsible from './nav-team-collapsible.vue'
import NavTeamVercel from './nav-team-vercel.vue'

const { navMain } = defineProps<{
  navMain: NavGroup[]
}>()

const sidebarConfig = useSidebarConfigStore()

const effectiveMode = computed(() => sidebarConfig.navigationMode)
</script>

<template>
  <NavTeamVercel v-if="effectiveMode === 'vercel'" :nav-main="navMain" />
  <NavTeamCollapsible v-else :nav-main="navMain" />
</template>
