<script setup lang="ts">
import type { DataTableProps } from '@/components/data-table'
import type { ServerPagination } from '@/components/data-table/types'
import type { useTableFilters } from '@/composables/use-table-filters'

import { DataTable, useGenerateVueTable } from '@/components/data-table'

import type { CatalogCategory } from './columns'

import DataTableToolbar from './data-table-toolbar.vue'

const props = defineProps<DataTableProps<CatalogCategory> & {
  serverPagination?: ServerPagination
  filters: ReturnType<typeof useTableFilters>
}>()

const table = useGenerateVueTable<CatalogCategory>(props)
</script>

<template>
  <DataTable :columns :data :loading :table :server-pagination>
    <template #toolbar>
      <DataTableToolbar :table :filters class="w-full overflow-x-auto" />
    </template>
  </DataTable>
</template>
