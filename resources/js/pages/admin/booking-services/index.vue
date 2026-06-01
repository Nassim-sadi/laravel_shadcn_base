<script lang="ts" setup>
import { computed, ref } from 'vue'
import { BasicPage } from '@/components/global-layout'
import { Button } from '@/components/ui/button'
import ConfirmDialog from '@/components/confirm-dialog.vue'
import { hasPermission } from '@/composables/use-role'
import { useTableFilters } from '@/composables/use-table-filters'
import {
  useDeleteBookingServiceMutation,
  useGetBookingServicesQuery,
  useToggleBookingServiceStatusMutation,
} from '@/services/api/booking.api'
import { createColumns, type BookingService } from './columns'
import DataTable from './data-table.vue'
import Form from './partials/Form.vue'

const filters = useTableFilters()
const { data: response, isLoading, refetch } = useGetBookingServicesQuery()
const { mutate: deleteService, isPending: isDeleting } = useDeleteBookingServiceMutation()
const { mutate: toggleStatus } = useToggleBookingServiceStatusMutation()

const services = computed(() => {
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

function openEdit(service: BookingService) {
  editingId.value = service.id
  editingItem.value = service
  showForm.value = true
}

function confirmDelete(id: number) {
  deleteTargetId.value = id
  showDeleteDialog.value = true
}

function handleDelete() {
  if (deleteTargetId.value !== null) {
    deleteService(deleteTargetId.value)
  }
  showDeleteDialog.value = false
  deleteTargetId.value = null
}

function handleToggle(service: BookingService) {
  toggleStatus(service.id)
}

const columns = createColumns(openEdit, confirmDelete, handleToggle)
</script>

<template>
  <BasicPage title="Booking Services" description="Manage your booking services" sticky>
    <template #actions>
      <Button variant="outline" @click="refetch">Refresh</Button>
      <Button v-if="hasPermission('booking_services.create')" @click="openCreate">Create Service</Button>
    </template>

    <DataTable :columns :data="services" :loading="isLoading" :filters />

    <ConfirmDialog v-model:open="showDeleteDialog" :is-loading="isDeleting" cancel-button-text="Cancel" confirm-button-text="Delete" destructive @confirm="handleDelete">
      <template #title>Delete Booking Service</template>
      <template #description>Are you sure you want to delete this service? This cannot be undone.</template>
    </ConfirmDialog>

    <Form v-model:open="showForm" :editing-id="editingId" :item="editingItem" />
  </BasicPage>
</template>
