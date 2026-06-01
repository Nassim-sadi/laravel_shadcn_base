import type { Ref } from 'vue'
import { useMutation, useQuery, useQueryClient } from '@tanstack/vue-query'
import { toast } from 'vue-sonner'
import { useApiFetch } from '@/composables/use-fetch'
import type { IResponse } from '../types/response.type'

export interface IBookingService {
  id: number
  name: string
  name_translations: Record<string, string | null>
  description?: string
  description_translations?: Record<string, string | null>
  duration_minutes: number
  price?: string
  is_active: boolean
  order: number
  availability_rules?: Array<{ id: number; day_of_week: number; start_time: string; end_time: string; is_active: boolean }>
  time_blocks?: Array<{ id: number; date: string; start_time?: string; end_time?: string; type: string; reason?: string }>
  bookings_count?: number
  created_at: string
  updated_at: string
}

export interface IBooking {
  id: number
  booking_service_id: number
  service?: IBookingService
  date: string
  start_time: string
  end_time: string
  customer_name: string
  customer_phone: string
  notes?: string
  status: string
  confirmations?: Array<{ id: number; action: string; notes?: string; user?: string; created_at: string }>
  reschedules?: Array<{ id: number; old_date: string; old_start_time: string; new_date: string; new_start_time: string; reason?: string; created_at: string }>
  created_at: string
  updated_at: string
}

export interface IBookingFilters {
  search?: string
  date?: string
  from_date?: string
  to_date?: string
  status?: string
  booking_service_id?: string
  calendar?: string
  page?: number
  per_page?: number
}

function buildUrl(path: string, params?: Record<string, any>): string {
  if (!params) return path
  const searchParams = new URLSearchParams()
  Object.entries(params).forEach(([key, value]) => {
    if (value !== undefined && value !== null && value !== '') {
      searchParams.set(key, String(value))
    }
  })
  const qs = searchParams.toString()
  return qs ? `${path}?${qs}` : path
}

// Booking Services
export function useGetBookingServicesQuery() {
  const { apiFetch } = useApiFetch()
  return useQuery<IResponse<any>, Error>({
    queryKey: ['useGetBookingServicesQuery'],
    queryFn: () => apiFetch('/booking-services', { method: 'get' }),
  })
}

export function useGetAllBookingServicesQuery() {
  const { apiFetch } = useApiFetch()
  return useQuery<IResponse<any>, Error>({
    queryKey: ['useGetAllBookingServicesQuery'],
    queryFn: () => apiFetch('/booking-services/all', { method: 'get' }),
  })
}

export function useCreateBookingServiceMutation() {
  const { apiFetch } = useApiFetch()
  const queryClient = useQueryClient()
  return useMutation<IResponse<IBookingService>, Error, any>({
    mutationKey: ['useCreateBookingServiceMutation'],
    mutationFn: data => apiFetch('/booking-services', { method: 'post', body: data }),
    onSuccess: () => {
      queryClient.invalidateQueries({ queryKey: ['useGetBookingServicesQuery'] })
      toast.success('Booking service created')
    },
    onError: (error) => {
      toast.error(error?.message ?? 'Failed to create booking service')
    },
  })
}

export function useUpdateBookingServiceMutation() {
  const { apiFetch } = useApiFetch()
  const queryClient = useQueryClient()
  return useMutation<IResponse<IBookingService>, Error, any>({
    mutationKey: ['useUpdateBookingServiceMutation'],
    mutationFn: ({ id, ...data }: { id: number } & any) => apiFetch(`/booking-services/${id}`, { method: 'put', body: data }),
    onSuccess: () => {
      queryClient.invalidateQueries({ queryKey: ['useGetBookingServicesQuery'] })
      toast.success('Booking service updated')
    },
    onError: (error) => {
      toast.error(error?.message ?? 'Failed to update booking service')
    },
  })
}

export function useDeleteBookingServiceMutation() {
  const { apiFetch } = useApiFetch()
  const queryClient = useQueryClient()
  return useMutation<IResponse<string>, Error, number>({
    mutationKey: ['useDeleteBookingServiceMutation'],
    mutationFn: id => apiFetch(`/booking-services/${id}`, { method: 'delete' }),
    onSuccess: () => {
      queryClient.invalidateQueries({ queryKey: ['useGetBookingServicesQuery'] })
      toast.success('Booking service deleted')
    },
    onError: (error) => {
      toast.error(error?.message ?? 'Failed to delete booking service')
    },
  })
}

export function useToggleBookingServiceStatusMutation() {
  const { apiFetch } = useApiFetch()
  const queryClient = useQueryClient()
  return useMutation<IResponse<any>, Error, number>({
    mutationKey: ['useToggleBookingServiceStatusMutation'],
    mutationFn: id => apiFetch(`/booking-services/${id}/toggle-status`, { method: 'post' }),
    onSuccess: () => {
      queryClient.invalidateQueries({ queryKey: ['useGetBookingServicesQuery'] })
      toast.success('Booking service status updated')
    },
    onError: (error) => {
      toast.error(error?.message ?? 'Failed to update booking service status')
    },
  })
}

// Bookings
export function useGetBookingsQuery(params?: Ref<IBookingFilters>) {
  const { apiFetch } = useApiFetch()
  return useQuery<IResponse<any>, Error>({
    queryKey: ['useGetBookingsQuery', params?.value],
    queryFn: () => apiFetch(buildUrl('/bookings', params?.value), { method: 'get' }),
  })
}

export function useConfirmBookingMutation() {
  const { apiFetch } = useApiFetch()
  const queryClient = useQueryClient()
  return useMutation<IResponse<IBooking>, Error, number>({
    mutationKey: ['useConfirmBookingMutation'],
    mutationFn: id => apiFetch(`/bookings/${id}/confirm`, { method: 'post' }),
    onSuccess: () => {
      queryClient.invalidateQueries({ queryKey: ['useGetBookingsQuery'] })
      toast.success('Booking confirmed')
    },
    onError: (error) => {
      toast.error(error?.message ?? 'Failed to confirm booking')
    },
  })
}

export function useCancelBookingMutation() {
  const { apiFetch } = useApiFetch()
  const queryClient = useQueryClient()
  return useMutation<IResponse<IBooking>, Error, number>({
    mutationKey: ['useCancelBookingMutation'],
    mutationFn: id => apiFetch(`/bookings/${id}/cancel`, { method: 'post' }),
    onSuccess: () => {
      queryClient.invalidateQueries({ queryKey: ['useGetBookingsQuery'] })
      toast.success('Booking cancelled')
    },
    onError: (error) => {
      toast.error(error?.message ?? 'Failed to cancel booking')
    },
  })
}

export function useCompleteBookingMutation() {
  const { apiFetch } = useApiFetch()
  const queryClient = useQueryClient()
  return useMutation<IResponse<IBooking>, Error, number>({
    mutationKey: ['useCompleteBookingMutation'],
    mutationFn: id => apiFetch(`/bookings/${id}/complete`, { method: 'post' }),
    onSuccess: () => {
      queryClient.invalidateQueries({ queryKey: ['useGetBookingsQuery'] })
      toast.success('Booking completed')
    },
    onError: (error) => {
      toast.error(error?.message ?? 'Failed to complete booking')
    },
  })
}

export function useRescheduleBookingMutation() {
  const { apiFetch } = useApiFetch()
  const queryClient = useQueryClient()
  return useMutation<IResponse<IBooking>, Error, { id: number; date: string; start_time: string; reason?: string }>({
    mutationKey: ['useRescheduleBookingMutation'],
    mutationFn: ({ id, ...data }) => apiFetch(`/bookings/${id}/reschedule`, { method: 'post', body: data }),
    onSuccess: () => {
      queryClient.invalidateQueries({ queryKey: ['useGetBookingsQuery'] })
      toast.success('Booking rescheduled')
    },
    onError: (error) => {
      toast.error(error?.message ?? 'The new time slot is not available.')
    },
  })
}

export function useDeleteBookingMutation() {
  const { apiFetch } = useApiFetch()
  const queryClient = useQueryClient()
  return useMutation<IResponse<string>, Error, number>({
    mutationKey: ['useDeleteBookingMutation'],
    mutationFn: id => apiFetch(`/bookings/${id}`, { method: 'delete' }),
    onSuccess: () => {
      queryClient.invalidateQueries({ queryKey: ['useGetBookingsQuery'] })
      toast.success('Booking deleted')
    },
    onError: (error) => {
      toast.error(error?.message ?? 'Failed to delete booking')
    },
  })
}

export function useBulkDeleteBookingsMutation() {
  const { apiFetch } = useApiFetch()
  const queryClient = useQueryClient()
  return useMutation<IResponse<any>, Error, number[]>({
    mutationKey: ['useBulkDeleteBookingsMutation'],
    mutationFn: ids => apiFetch('/bookings/bulk-delete', { method: 'post', body: { ids } }),
    onSuccess: () => {
      queryClient.invalidateQueries({ queryKey: ['useGetBookingsQuery'] })
      toast.success('Bookings deleted')
    },
    onError: (error) => {
      toast.error(error?.message ?? 'Failed to delete bookings')
    },
  })
}

// Availability
export function useGetAvailabilityQuery(serviceId: Ref<number | null>, date: Ref<string>) {
  const { apiFetch } = useApiFetch()
  return useQuery<IResponse<any>, Error>({
    queryKey: ['useGetAvailabilityQuery', serviceId.value, date.value],
    queryFn: () => apiFetch(`/booking-services/${serviceId.value}/availability?date=${date.value}`, { method: 'get' }),
    enabled: () => !!serviceId.value && !!date.value,
  })
}
