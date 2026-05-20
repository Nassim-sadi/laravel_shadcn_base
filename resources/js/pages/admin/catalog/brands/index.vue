<script lang="ts" setup>
import { computed, ref } from 'vue'
import { BasicPage } from '@/components/global-layout'
import { Button } from '@/components/ui/button'
import ConfirmDialog from '@/components/confirm-dialog.vue'
import { hasPermission } from '@/composables/use-role'
import { useTableFilters } from '@/composables/use-table-filters'
import {
  useDeleteCatalogBrandMutation,
  useGetCatalogBrandsQuery,
  useToggleCatalogBrandStatusMutation,
} from '@/services/api/catalog.api'
import { createColumns, type CatalogBrand } from './columns'
import DataTable from './data-table.vue'
import BrandForm from '../partials/BrandForm.vue'

const filters = useTableFilters()
const { data: response, isLoading, refetch } = useGetCatalogBrandsQuery()
const { mutate: deleteBrand, isPending: isDeleting } = useDeleteCatalogBrandMutation()
const { mutate: toggleStatus } = useToggleCatalogBrandStatusMutation()

const brands = computed(() => {
  const d = response.value as any
  return d?.data ?? []
})

const showForm = ref(false)
const editingId = ref<number | null>(null)
const editingItem = ref<any>(null)
const deleteTargetId = ref<number | null>(null)
const showDeleteDialog = ref(false)

function openCreate() {
  editingId.value = null
  editingItem.value = null
  showForm.value = true
}

function openEdit(brand: CatalogBrand) {
  editingId.value = brand.id
  editingItem.value = brand
  showForm.value = true
}

function confirmDelete(id: number) {
  deleteTargetId.value = id
  showDeleteDialog.value = true
}

function handleDelete() {
  if (deleteTargetId.value !== null) {
    deleteBrand(deleteTargetId.value)
  }
  showDeleteDialog.value = false
  deleteTargetId.value = null
}

function handleToggle(brand: CatalogBrand) {
  toggleStatus(brand.id)
}

const columns = createColumns(openEdit, confirmDelete, handleToggle)
</script>

<template>
  <BasicPage :title="$t('admin.catalog.brands')" :description="''" sticky>
    <template #actions>
      <Button variant="outline" @click="refetch">{{ $t('admin.btn.refresh') }}</Button>
      <Button v-if="hasPermission('catalog.create')" @click="openCreate">{{ $t('admin.catalog.createBrand') }}</Button>
    </template>

    <DataTable :columns :data="brands" :loading="isLoading" :filters />

    <ConfirmDialog v-model:open="showDeleteDialog" :is-loading="isDeleting" :cancel-button-text="$t('admin.btn.cancel')" :confirm-button-text="$t('admin.btn.delete')" destructive @confirm="handleDelete">
      <template #title>{{ $t('admin.dialog.deleteTitle', { item: $t('admin.catalog.brands') }) }}</template>
      <template #description>{{ $t('admin.dialog.deleteDescription', { item: $t('admin.catalog.brands').toLowerCase() }) }}</template>
    </ConfirmDialog>

    <BrandForm v-model:open="showForm" :editing-id="editingId" :item="editingItem" />
  </BasicPage>
</template>
