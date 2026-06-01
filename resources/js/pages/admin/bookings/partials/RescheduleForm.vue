<script setup lang="ts">
import { useVuelidate } from '@vuelidate/core'
import { computed, ref, watch } from 'vue'

import { Button } from '@/components/ui/button'
import { Sheet, SheetContent, SheetFooter, SheetHeader, SheetTitle } from '@/components/ui/sheet'
import { Textarea } from '@/components/ui/textarea'
import ConfirmDialog from '@/components/confirm-dialog.vue'

import { useRescheduleBookingMutation } from '@/services/api/booking.api'

const props = defineProps<{
  booking: any
  open?: boolean
}>()

const open = defineModel<boolean>('open', { default: false })

const { mutateAsync: rescheduleBooking } = useRescheduleBookingMutation()
const showUnsavedDialog = ref(false)

const form = ref({
  date: '',
  start_time: '',
  notes: '',
})

const rules = computed(() => ({
  date: { required: (v: string) => !!v },
  start_time: { required: (v: string) => !!v },
}))

const v$ = useVuelidate(rules, form)

watch(() => props.open, (isOpen) => {
  if (isOpen && props.booking) {
    form.value = {
      date: props.booking.date,
      start_time: props.booking.start_time,
      notes: '',
    }
    v$.value.$reset()
  }
})

function handleSheetClose(isOpen: boolean) {
  if (!isOpen) {
    showUnsavedDialog.value = true
    return
  }
  open.value = isOpen
}

async function save() {
  const isValid = await v$.value.$validate()
  if (!isValid) return

  try {
    await rescheduleBooking({ id: props.booking.id, ...form.value })
    open.value = false
  } catch {
    // Toast is handled in booking.api.ts
  }
}

function forceClose() {
  showUnsavedDialog.value = false
  open.value = false
}
</script>

<template>
  <Sheet :open="open" @update:open="handleSheetClose">
    <SheetContent side="right" class="w-full sm:max-w-md">
      <SheetHeader>
        <SheetTitle>Reschedule Booking</SheetTitle>
      </SheetHeader>
      <div class="space-y-4 px-6 py-4">
        <div class="admin-form-field">
          <label class="text-sm font-medium">Date</label>
          <Input v-model="form.date" type="date" :class="{ 'border-destructive': v$.date?.$error }" />
        </div>
        <div class="admin-form-field">
          <label class="text-sm font-medium">Start Time</label>
          <Input v-model="form.start_time" type="time" :class="{ 'border-destructive': v$.start_time?.$error }" />
        </div>
        <div class="admin-form-field">
          <label class="text-sm font-medium">Notes</label>
          <Textarea v-model="form.notes" rows="3" placeholder="Optional reason for rescheduling..." />
        </div>
      </div>
      <SheetFooter>
        <Button variant="outline" @click="handleSheetClose(false)">Cancel</Button>
        <Button @click="save">Reschedule</Button>
      </SheetFooter>
    </SheetContent>
  </Sheet>

  <ConfirmDialog v-model:open="showUnsavedDialog" cancel-button-text="Stay" confirm-button-text="Discard" destructive @confirm="forceClose">
    <template #title>Unsaved changes</template>
    <template #description>You have unsaved changes. Are you sure you want to discard them?</template>
  </ConfirmDialog>
</template>

<script lang="ts">
import { Input } from '@/components/ui/input'
</script>
