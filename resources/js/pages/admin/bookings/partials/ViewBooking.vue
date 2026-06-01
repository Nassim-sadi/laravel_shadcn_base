<script setup lang="ts">
import type { IBooking } from '@/services/api/booking.api'
import { Badge } from '@/components/ui/badge'
import { Button } from '@/components/ui/button'
import { Sheet, SheetContent, SheetFooter, SheetHeader, SheetTitle } from '@/components/ui/sheet'
import {
  CheckCircleIcon,
  XCircleIcon,
  PencilIcon,
  Trash2Icon,
} from '@lucide/vue'
import {
  useConfirmBookingMutation,
  useCancelBookingMutation,
  useCompleteBookingMutation,
  useDeleteBookingMutation,
} from '@/services/api/booking.api'
import ConfirmDialog from '@/components/confirm-dialog.vue'
import { ref } from 'vue'

const props = defineProps<{
  booking: IBooking | null
  open?: boolean
}>()

const emit = defineEmits<{
  openReschedule: [booking: IBooking]
  close: []
}>()

const open = defineModel<boolean>('open', { default: false })

const showConfirmDialog = ref(false)
const showCompleteDialog = ref(false)
const showCancelDialog = ref(false)
const showDeleteDialog = ref(false)

const { mutate: confirmBooking, isPending: isConfirming } = useConfirmBookingMutation()
const { mutate: cancelBooking, isPending: isCancelling } = useCancelBookingMutation()
const { mutate: completeBooking, isPending: isCompleting } = useCompleteBookingMutation()
const { mutate: deleteBooking, isPending: isDeleting } = useDeleteBookingMutation()

const statusConfig: Record<string, { label: string; class: string }> = {
  pending: { label: 'Pending', class: 'bg-yellow-500/15 text-yellow-700 border-yellow-200' },
  confirmed: { label: 'Confirmed', class: 'bg-blue-500/15 text-blue-700 border-blue-200' },
  completed: { label: 'Completed', class: 'bg-green-500/15 text-green-700 border-green-200' },
  cancelled: { label: 'Cancelled', class: 'bg-red-500/15 text-red-700 border-red-200' },
  rescheduled: { label: 'Rescheduled', class: 'bg-purple-500/15 text-purple-700 border-purple-200' },
}

function handleConfirm() {
  if (!props.booking) return
  confirmBooking(props.booking.id, { onSuccess: () => { showConfirmDialog.value = false; open.value = false } })
}

function handleComplete() {
  if (!props.booking) return
  completeBooking(props.booking.id, { onSuccess: () => { showCompleteDialog.value = false; open.value = false } })
}

function handleCancel() {
  if (!props.booking) return
  cancelBooking(props.booking.id, { onSuccess: () => { showCancelDialog.value = false; open.value = false } })
}

function handleDelete() {
  if (!props.booking) return
  deleteBooking(props.booking.id, { onSuccess: () => { showDeleteDialog.value = false; open.value = false } })
}
</script>

<template>
  <Sheet :open="open" @update:open="(v: boolean) => { if (!v) emit('close') }">
    <SheetContent side="right" class="w-full sm:max-w-md">
      <SheetHeader>
        <SheetTitle>Booking Details</SheetTitle>
      </SheetHeader>

      <div v-if="booking" class="space-y-6 px-6 py-4">
        <div class="flex items-center justify-between">
          <Badge variant="outline" :class="statusConfig[booking.status]?.class">
            {{ statusConfig[booking.status]?.label || booking.status }}
          </Badge>
          <span class="text-xs text-muted-foreground">#{{ booking.id }}</span>
        </div>

        <div class="space-y-4">
          <div>
            <label class="text-xs font-medium text-muted-foreground uppercase tracking-wider">Customer</label>
            <p class="text-sm font-medium mt-1">{{ booking.customer_name }}</p>
          </div>
          <div>
            <label class="text-xs font-medium text-muted-foreground uppercase tracking-wider">Phone</label>
            <p class="text-sm mt-1">{{ booking.customer_phone }}</p>
          </div>
          <div>
            <label class="text-xs font-medium text-muted-foreground uppercase tracking-wider">Service</label>
            <p class="text-sm mt-1">{{ booking.service?.name || '—' }}</p>
            <p v-if="booking.service?.duration_minutes" class="text-xs text-muted-foreground mt-0.5">
              {{ booking.service.duration_minutes }} min
            </p>
          </div>
          <div class="grid grid-cols-2 gap-4">
            <div>
              <label class="text-xs font-medium text-muted-foreground uppercase tracking-wider">Date</label>
              <p class="text-sm mt-1">{{ new Date(booking.date).toLocaleDateString() }}</p>
            </div>
            <div>
              <label class="text-xs font-medium text-muted-foreground uppercase tracking-wider">Time</label>
              <p class="text-sm mt-1">{{ booking.start_time }} → {{ booking.end_time }}</p>
            </div>
          </div>
          <div v-if="booking.notes">
            <label class="text-xs font-medium text-muted-foreground uppercase tracking-wider">Notes</label>
            <p class="text-sm mt-1 text-muted-foreground">{{ booking.notes }}</p>
          </div>
          <div>
            <label class="text-xs font-medium text-muted-foreground uppercase tracking-wider">Created</label>
            <p class="text-sm mt-1 text-muted-foreground">{{ new Date(booking.created_at).toLocaleString() }}</p>
          </div>
        </div>
      </div>

      <div v-else class="flex items-center justify-center py-12 text-muted-foreground">
        No booking selected
      </div>

      <SheetFooter v-if="booking" class="flex-col gap-2 border-t pt-4">
        <div class="flex flex-wrap gap-2">
          <Button v-if="booking.status === 'pending'" size="sm" @click="showConfirmDialog = true">
            <CheckCircleIcon class="size-4 mr-1.5 text-green-600" />
            Confirm
          </Button>
          <Button v-if="booking.status === 'confirmed'" size="sm" @click="showCompleteDialog = true">
            <CheckCircleIcon class="size-4 mr-1.5 text-blue-600" />
            Complete
          </Button>
          <Button v-if="booking.status !== 'cancelled'" size="sm" variant="outline" @click="showCancelDialog = true">
            <XCircleIcon class="size-4 mr-1.5 text-red-600" />
            Cancel
          </Button>
          <Button size="sm" variant="outline" @click="emit('openReschedule', booking)">
            <PencilIcon class="size-4 mr-1.5" />
            Reschedule
          </Button>
          <Button size="sm" variant="outline" class="text-destructive" @click="showDeleteDialog = true">
            <Trash2Icon class="size-4 mr-1.5" />
            Delete
          </Button>
        </div>
      </SheetFooter>
    </SheetContent>
  </Sheet>

  <ConfirmDialog
    v-model:open="showConfirmDialog"
    :is-loading="isConfirming"
    cancel-button-text="Cancel"
    confirm-button-text="Confirm"
    @confirm="handleConfirm"
  >
    <template #title>Confirm Booking</template>
    <template #description>Are you sure you want to confirm this booking?</template>
  </ConfirmDialog>

  <ConfirmDialog
    v-model:open="showCompleteDialog"
    :is-loading="isCompleting"
    cancel-button-text="Cancel"
    confirm-button-text="Complete"
    @confirm="handleComplete"
  >
    <template #title>Complete Booking</template>
    <template #description>Are you sure you want to mark this booking as complete?</template>
  </ConfirmDialog>

  <ConfirmDialog
    v-model:open="showCancelDialog"
    :is-loading="isCancelling"
    cancel-button-text="Cancel"
    confirm-button-text="Cancel Booking"
    destructive
    @confirm="handleCancel"
  >
    <template #title>Cancel Booking</template>
    <template #description>Are you sure you want to cancel this booking?</template>
  </ConfirmDialog>

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
</template>
