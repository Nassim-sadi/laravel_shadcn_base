<script lang="ts" setup>
import { computed, ref } from 'vue'
import { BasicPage } from '@/components/global-layout'
import { Button } from '@/components/ui/button'
import ConfirmDialog from '@/components/confirm-dialog.vue'
import { hasPermission } from '@/composables/use-role'
import { useTableFilters } from '@/composables/use-table-filters'
import {
  useDeleteCatalogProductMutation,
  useGetCatalogProductsQuery,
  useToggleCatalogProductStatusMutation,
} from '@/services/api/catalog.api'
import { createColumns, type CatalogProduct } from './columns'
import DataTable from './data-table.vue'
import ProductForm from '../partials/ProductForm.vue'

const filters = useTableFilters()
const { data: response, isLoading, refetch } = useGetCatalogProductsQuery(filters.params)
const { mutate: deleteProduct, isPending: isDeleting } = useDeleteCatalogProductMutation()
const { mutate: toggleStatus } = useToggleCatalogProductStatusMutation()

const products = computed(() => {
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

function openEdit(product: CatalogProduct) {
  editingId.value = product.id
  editingItem.value = product
  showForm.value = true
}

function confirmDelete(id: number) {
  deleteTargetId.value = id
  showDeleteDialog.value = true
}

function handleDelete() {
  if (deleteTargetId.value !== null) {
    deleteProduct(deleteTargetId.value)
  }
  showDeleteDialog.value = false
  deleteTargetId.value = null
}

function handleToggle(product: CatalogProduct) {
  toggleStatus(product.id)
}

const columns = createColumns(openEdit, confirmDelete, handleToggle)
</script>

<template>
  <BasicPage :title="$t('admin.catalog.products')" :description="''" sticky>
    <template #actions>
      <Button variant="outline" @click="refetch">{{ $t('admin.btn.refresh') }}</Button>
      <Button v-if="hasPermission('catalog.create')" @click="openCreate">{{ $t('admin.catalog.createProduct') }}</Button>
    </template>

    <DataTable :columns :data="products" :loading="isLoading" :filters :server-pagination="pagination" />

    <ConfirmDialog v-model:open="showDeleteDialog" :is-loading="isDeleting" :cancel-button-text="$t('admin.btn.cancel')" :confirm-button-text="$t('admin.btn.delete')" destructive @confirm="handleDelete">
      <template #title>{{ $t('admin.dialog.deleteTitle', { item: $t('admin.catalog.products') }) }}</template>
      <template #description>{{ $t('admin.dialog.deleteDescription', { item: $t('admin.catalog.products').toLowerCase() }) }}</template>
    </ConfirmDialog>

    <ProductForm v-model:open="showForm" :editing-id="editingId" :item="editingItem" />
  </BasicPage>
</template>
