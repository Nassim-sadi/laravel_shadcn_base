<script setup lang="ts">
import type { useTableFilters } from '@/composables/use-table-filters'
import type { Table } from '@tanstack/vue-table'

import { Input } from '@/components/ui/input'

import type { QuoteRequest } from './columns'

defineProps<{
  table: Table<QuoteRequest>
  filters: ReturnType<typeof useTableFilters>
}>()
</script>

<template>
  <Input
    v-model="filters.search.value"
    placeholder="Search quotes..."
    class="h-8 w-[150px] lg:w-[250px]"
  />
</template>
