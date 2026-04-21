import { useQuery } from '@tanstack/vue-query'

import { useApiFetch } from '@/composables/use-fetch'

import type { IResponse } from '../types/response.type'

export interface IActivityLog {
  id: number
  log_name: string
  description: string
  subject_type?: string
  subject_id?: number
  event: string | null
  properties?: Record<string, unknown>
  causer?: {
    id: number
    name: string
    email: string
  }
  created_at: string
  updated_at?: string
}

export interface IActivityLogsResponse {
  data: IActivityLog[]
  current_page: number
  last_page: number
  per_page: number
  total: number
}

export function useGetActivityLogsQuery(options?: {
  log_name?: string
  event?: string
  causer_id?: number
  search?: string
}) {
  const { apiFetch } = useApiFetch()

  return useQuery<IResponse<IActivityLogsResponse>, Error>({
    queryKey: ['useGetActivityLogsQuery', options],
    queryFn: async () => {
      const params = new URLSearchParams()
      if (options?.log_name) params.append('log_name', options.log_name)
      if (options?.event) params.append('event', options.event)
      if (options?.causer_id) params.append('causer_id', String(options.causer_id))
      if (options?.search) params.append('search', options.search)

      const queryString = params.toString()
      const url = `/activity-logs${queryString ? `?${queryString}` : ''}`
      return await apiFetch<IResponse<IActivityLogsResponse>>(url, { method: 'get' })
    },
  })
}

export function useGetActivityLogByIdQuery(id: number) {
  const { apiFetch } = useApiFetch()

  return useQuery<IResponse<IActivityLog>, Error>({
    queryKey: ['useGetActivityLogByIdQuery', id],
    queryFn: async () => apiFetch<IResponse<IActivityLog>>(`/activity-logs/${id}`, { method: 'get' }),
  })
}

export function useGetLogNamesQuery() {
  const { apiFetch } = useApiFetch()

  return useQuery<IResponse<string[]>, Error>({
    queryKey: ['useGetLogNamesQuery'],
    queryFn: async () => apiFetch<IResponse<string[]>>('/activity-logs/log-names', { method: 'get' }),
  })
}

export function useGetEventsQuery() {
  const { apiFetch } = useApiFetch()

  return useQuery<IResponse<string[]>, Error>({
    queryKey: ['useGetEventsQuery'],
    queryFn: async () => apiFetch<IResponse<string[]>>('/activity-logs/events', { method: 'get' }),
  })
}