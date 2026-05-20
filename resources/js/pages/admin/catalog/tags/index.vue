<script lang="ts" setup>
import { computed, ref } from 'vue'
import { BasicPage } from '@/components/global-layout'
import { Button } from '@/components/ui/button'
import ConfirmDialog from '@/components/confirm-dialog.vue'
import { hasPermission } from '@/composables/use-role'
import { useTableFilters } from '@/composables/use-table-filters'
import { useDeleteCatalogTagMutation, useGetCatalogTagsQuery } from '@/services/api/catalog.api'
import { createColumns } from './columns'
import DataTable from './data-table.vue'
import TagForm from '../partials/TagForm.vue'

const filters = useTableFilters()
const { data: response, isLoading, refetch } = useGetCatalogTagsQuery()
const { mutate: deleteTag, isPending: isDeleting } = useDeleteCatalogTagMutation()

const tags = computed(() => {
  const d = response.value as any
  return d?.data ?? []
})

const showForm = ref(false)
const editingItem = ref<any>(null)
const deleteTargetId = ref<number | null>(null)
const showDeleteDialog = ref(false)

function openCreate() {
  editingItem.value = null
  showForm.value = true
}

function confirmDelete(id: number) {
  deleteTargetId.value = id
  showDeleteDialog.value = true
}

function handleDelete() {
  if (deleteTargetId.value !== null) {
    deleteTag(deleteTargetId.value)
  }
  showDeleteDialog.value = false
  deleteTargetId.value = null
}

const columns = createColumns(confirmDelete)
</script>

<template>
  <BasicPage :title="$t('admin.catalog.tags')" :description="''" sticky>
    <template #actions>
      <Button variant="outline" @click="refetch">{{ $t('admin.btn.refresh') }}</Button>
      <Button v-if="hasPermission('catalog.create')" @click="openCreate">{{ $t('admin.catalog.createTag') }}</Button>
    </template>

    <DataTable :columns :data="tags" :loading="isLoading" :filters />

    <ConfirmDialog v-model:open="showDeleteDialog" :is-loading="isDeleting" :cancel-button-text="$t('admin.btn.cancel')" :confirm-button-text="$t('admin.btn.delete')" destructive @confirm="handleDelete">
      <template #title>{{ $t('admin.dialog.deleteTitle', { item: $t('admin.catalog.tags') }) }}</template>
      <template #description>{{ $t('admin.dialog.deleteDescription', { item: $t('admin.catalog.tags').toLowerCase() }) }}</template>
    </ConfirmDialog>

    <TagForm v-model:open="showForm" :editing-id="null" :item="editingItem" />
  </BasicPage>
</template>
