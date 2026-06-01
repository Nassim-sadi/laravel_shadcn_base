<script lang="ts" setup>
import { computed, ref } from 'vue'

import ConfirmDialog from '@/components/confirm-dialog.vue'
import { BasicPage } from '@/components/global-layout'
import { Button } from '@/components/ui/button'
import {
  useCancelBookingMutation,
  useCompleteBookingMutation,
  useConfirmBookingMutation,
  useDeleteBookingMutation,
  useGetBookingsQuery,
} from '@/services/api/booking.api'
import { useTableFilters } from '@/composables/use-table-filters'
import RescheduleForm from './partials/RescheduleForm.vue'

import { createColumns, type Booking } from './columns'
import DataTable from './components/data-table.vue'

const filters = useTableFilters()
const { data: response, isLoading, refetch } = useGetBookingsQuery(filters.params)
const { mutate: deleteBooking, isPending: isDeleting } = useDeleteBookingMutation()
const { mutate: confirmBooking } = useConfirmBookingMutation()
const { mutate: cancelBooking } = useCancelBookingMutation()
const { mutate: completeBooking } = useCompleteBookingMutation()

const bookings = computed(() => {
  const d = response.value as any
  return d?.data ?? []
})

const pagination = computed(() => ({
  page: filters.page.value,
  pageSize: filters.pageSize.value,
  total: (response.value as any)?.meta?.total ?? 0,
  onPageChange: (page: number) => { filters.page.value = page },
  onPageSizeChange: (pageSize: number) => { filters.pageSize.value = pageSize; filters.page.value = 1 },
}))

const showReschedule = ref(false)
const rescheduleBooking = ref<Booking | null>(null)
const deleteTargetId = ref<number | null>(null)
const showDeleteDialog = ref(false)

function openReschedule(booking: Booking) {
  rescheduleBooking.value = booking
  showReschedule.value = true
}

function confirmDelete(id: number) {
  deleteTargetId.value = id
  showDeleteDialog.value = true
}

function handleDelete() {
  if (deleteTargetId.value !== null) {
    deleteBooking(deleteTargetId.value)
  }
  showDeleteDialog.value = false
  deleteTargetId.value = null
}

const columns = createColumns(
  (id) => { confirmBooking(id) },
  (id) => { completeBooking(id) },
  (id) => { cancelBooking(id) },
  openReschedule,
  confirmDelete,
)
</script>

<template>
  <BasicPage title="Bookings" description="Manage all bookings" sticky>
    <template #actions>
      <Button variant="outline" @click="refetch">
        Refresh
      </Button>
    </template>
    <div class="overflow-x-auto">
      <DataTable
        :loading="isLoading"
        :data="bookings"
        :columns="columns"
        :server-pagination="pagination"
        :filters
        @refresh="refetch"
      />
    </div>

    <ConfirmDialog
      v-model:open="showDeleteDialog"
      :is-loading="isDeleting"
      cancel-button-text="Cancel"
      confirm-button-text="Delete"
      destructive
      @confirm="handleDelete"
    >
      <template #title>Delete Booking</template>
      <template #description>Are you sure you want to delete this booking?</template>
    </ConfirmDialog>

    <RescheduleForm v-model:open="showReschedule" :booking="rescheduleBooking" />
  </BasicPage>
</template>
