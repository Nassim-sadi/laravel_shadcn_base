<script lang="ts" setup>
import { computed, ref } from 'vue'

import ConfirmDialog from '@/components/confirm-dialog.vue'
import { BasicPage } from '@/components/global-layout'
import { Button } from '@/components/ui/button'
import { useDeleteFaqMutation, useGetFaqsQuery } from '@/services/api/faqs.api'
import { hasPermission } from '@/composables/use-role'
import { useTableFilters } from '@/composables/use-table-filters'
import Form from './partials/Form.vue'

import { createColumns, type Faq } from './components/columns'
import DataTable from './components/data-table.vue'

const filters = useTableFilters()

const { data: response, isLoading, refetch } = useGetFaqsQuery(filters.params)

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

const showSheet = ref(false)
const editingId = ref<number | null>(null)
const editingItem = ref<any>(null)
const deleteTargetId = ref<number | null>(null)
const showDeleteDialog = ref(false)
const { mutate: deleteItem, isPending: isDeleting } = useDeleteFaqMutation()

function openCreate() {
  editingId.value = null
  editingItem.value = null
  showSheet.value = true
}

function openEdit(item: Faq) {
  editingId.value = item.id
  editingItem.value = item
  showSheet.value = true
}

function confirmDelete(id: number) {
  deleteTargetId.value = id
  showDeleteDialog.value = true
}

function handleDelete() {
  if (deleteTargetId.value !== null) {
    deleteItem(deleteTargetId.value)
  }
  showDeleteDialog.value = false
  deleteTargetId.value = null
}

const columns = createColumns(openEdit, confirmDelete)
</script>

<template>
  <BasicPage :title="$t('admin.page.faqs.title')" :description="$t('admin.page.faqs.description')" sticky>
    <template #actions>
      <Button variant="outline" @click="refetch">
        {{ $t('admin.btn.refresh') }}
      </Button>
      <Button v-if="hasPermission('faqs.create')" @click="openCreate">
        {{ $t('admin.sheet.createFaq') }}
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

    <ConfirmDialog
      v-model:open="showDeleteDialog"
      :is-loading="isDeleting"
      :cancel-button-text="$t('admin.btn.cancel')"
      :confirm-button-text="$t('admin.btn.delete')"
      destructive
      @confirm="handleDelete"
    >
      <template #title>
        {{ $t('admin.dialog.deleteTitle', { item: $t('admin.nav.faqs') }) }}
      </template>
      <template #description>
        {{ $t('admin.dialog.deleteDescription', { item: $t('admin.nav.faqs').toLowerCase() }) }}
      </template>
    </ConfirmDialog>

    <Form v-model:open="showSheet" :editingId="editingId" :item="editingItem" />
  </BasicPage>
</template>
