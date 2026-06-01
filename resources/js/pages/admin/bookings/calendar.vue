<script lang="ts" setup>
import { computed, ref, watch } from 'vue'
import FullCalendar from '@fullcalendar/vue3'
import dayGridPlugin from '@fullcalendar/daygrid'
import timeGridPlugin from '@fullcalendar/timegrid'
import interactionPlugin from '@fullcalendar/interaction'
import { BasicPage } from '@/components/global-layout'
import { Button } from '@/components/ui/button'
import { useGetBookingsQuery } from '@/services/api/booking.api'
import type { Booking } from './columns'
import ViewBooking from './partials/ViewBooking.vue'
import RescheduleForm from './partials/RescheduleForm.vue'

const { data: response, isLoading, refetch } = useGetBookingsQuery({ per_page: 1000 } as any)

const bookings = computed(() => {
  const d = response.value as any
  return d?.data ?? []
})

const selectedBooking = ref<Booking | null>(null)
const showDetails = ref(false)
const showReschedule = ref(false)
const rescheduleBooking = ref<Booking | null>(null)

function handleEventClick(info: any) {
  const id = parseInt(info.event.id, 10)
  const booking = bookings.value.find((b: Booking) => b.id === id)
  if (booking) {
    selectedBooking.value = booking
    showDetails.value = true
  }
}

function openReschedule(booking: Booking) {
  showDetails.value = false
  rescheduleBooking.value = booking
  showReschedule.value = true
}

function closeDetails() {
  showDetails.value = false
  setTimeout(() => { selectedBooking.value = null }, 200)
}

watch(bookings, (list) => {
  if (selectedBooking.value) {
    const updated = list.find((b: Booking) => b.id === selectedBooking.value!.id)
    if (updated) selectedBooking.value = updated
  }
})

const eventTimeFormat = {
  hour: '2-digit',
  minute: '2-digit',
  hour12: false,
}

const calendarOptions = computed(() => ({
  plugins: [dayGridPlugin, timeGridPlugin, interactionPlugin],
  initialView: 'timeGridWeek',
  headerToolbar: {
    left: 'prev,next today',
    center: 'title',
    right: 'dayGridMonth,timeGridWeek,timeGridDay',
  },
  events: bookings.value.map((booking: any) => {
    const statusColors: Record<string, string> = {
      pending: '#eab308',
      confirmed: '#3b82f6',
      completed: '#22c55e',
      cancelled: '#ef4444',
      rescheduled: '#a855f7',
    }
    return {
      id: booking.id.toString(),
      title: `${booking.customer_name} - ${booking.service?.name}`,
      start: `${booking.date}T${booking.start_time}`,
      end: `${booking.date}T${booking.end_time}`,
      backgroundColor: statusColors[booking.status] || '#6b7280',
      borderColor: statusColors[booking.status] || '#6b7280',
      extendedProps: { status: booking.status, phone: booking.customer_phone },
    }
  }),
  editable: false,
  selectable: true,
  eventClick: handleEventClick,
  height: 'auto',
  dayMaxEvents: 2,
  eventTimeFormat,
  slotMinTime: '06:00:00',
  slotMaxTime: '22:00:00',
}))
</script>

<template>
  <BasicPage title="Booking Calendar" description="View all bookings on calendar" sticky>
    <template #actions>
      <Button variant="outline" @click="refetch">Refresh</Button>
    </template>

    <div v-if="isLoading" class="flex justify-center py-8">Loading...</div>
    <FullCalendar
      v-else
      :options="calendarOptions"
      class="bg-card rounded-lg p-4 booking-calendar"
    />

    <ViewBooking
      v-model:open="showDetails"
      :booking="selectedBooking"
      @open-reschedule="openReschedule"
      @close="closeDetails"
    />

    <RescheduleForm v-model:open="showReschedule" :booking="rescheduleBooking" />
  </BasicPage>
</template>

<style scoped>
.booking-calendar :deep(.fc-daygrid-event) {
  font-size: 0.7rem;
  padding: 1px 4px;
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
}

.booking-calendar :deep(.fc-timegrid-slot) {
  height: 2rem;
}

.booking-calendar :deep(.fc-daygrid-day-frame) {
  min-height: 6rem;
}

.booking-calendar :deep(.fc-daygrid-more-link) {
  font-size: 0.7rem;
}
</style>
