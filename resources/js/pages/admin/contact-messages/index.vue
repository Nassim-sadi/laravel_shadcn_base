<script lang="ts" setup>
import { computed } from 'vue'

import { BasicPage } from '@/components/global-layout'
import { Button } from '@/components/ui/button'
import { useGetContactMessagesQuery } from '@/services/api/contact-messages.api'
import { useTableFilters } from '@/composables/use-table-filters'

import { createColumns } from './components/columns'
import DataTable from './components/data-table.vue'

const filters = useTableFilters()

const { data: response, isLoading, refetch } = useGetContactMessagesQuery(filters.params)

const items = computed(() => {
  const d = response.value as any
  return d?.data ?? []
})

const pagination = computed(() => ({
  page: filters.page.value,
  pageSize: filters.pageSize.value,
  total: (response.value as any)?.total ?? 0,
  onPageChange: (page: number) => { filters.page.value = page },
  onPageSizeChange: (pageSize: number) => { filters.pageSize.value = pageSize; filters.page.value = 1 },
}))

const columns = createColumns()
</script>

<template>
  <BasicPage :title="$t('admin.page.contactMessages.title')" :description="$t('admin.page.contactMessages.description')" sticky>
    <template #actions>
      <Button variant="outline" @click="refetch">
        {{ $t('admin.btn.refresh') }}
      </Button>
    </template>
    <div class="overflow-x-auto">
      <DataTable
        :loading="isLoading"
        :data="items"
        :columns="columns"
        :server-pagination="pagination"
        :filters
        @refresh="refetch"
      />
    </div>
  </BasicPage>
</template>
