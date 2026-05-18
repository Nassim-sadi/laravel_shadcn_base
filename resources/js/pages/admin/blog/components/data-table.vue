<script setup lang="ts">
import type { DataTableProps } from '@/components/data-table'
import type { ServerPagination } from '@/components/data-table/types'
import type { useTableFilters } from '@/composables/use-table-filters'

import { DataTable, useGenerateVueTable } from '@/components/data-table'

import type { BlogPost } from './columns'

import DataTableToolbar from './data-table-toolbar.vue'

const props = defineProps<DataTableProps<BlogPost> & {
  serverPagination?: ServerPagination
  filters: ReturnType<typeof useTableFilters>
}>()

const table = useGenerateVueTable<BlogPost>(props)
</script>

<template>
  <DataTable :columns :data :loading :table :server-pagination>
    <template #toolbar>
      <DataTableToolbar :table :filters class="w-full overflow-x-auto" />
    </template>
  </DataTable>
</template>
