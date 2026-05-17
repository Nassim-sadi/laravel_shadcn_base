import { useQuery } from '@tanstack/vue-query'

import { useApiFetch } from '@/composables/use-fetch'

import type { IResponse } from '../types/response.type'

export interface IActivityLog {
  id: number
  user_id?: number
  event: string | null
  subject_type?: string
  subject_id?: number
  description: string
  properties?: Record<string, unknown>
  ip_address?: string
  user_agent?: string
  user?: {
    id: number
    name: string
    email: string
  }
  created_at: string
  updated_at?: string
}

export interface IActivityLogsResponse {
  data: IActivityLog[]
  meta?: {
    current_page: number
    last_page: number
    per_page: number
    total: number
  }
}

export function useGetActivityLogsQuery(options?: {
  event?: string
  user_id?: number
  search?: string
}) {
  const { apiFetch } = useApiFetch()

  return useQuery<IActivityLogsResponse, Error>({
    queryKey: ['useGetActivityLogsQuery', options],
    // console log the options to see what is being passed to the query function
    
    queryFn: async () => {
      const params = new URLSearchParams()
      if (options?.event)
        params.append('event', options.event)
      if (options?.user_id)
        params.append('user_id', String(options.user_id))
      if (options?.search)
        params.append('search', options.search)

      const queryString = params.toString()
      const url = `/activity-logs${queryString ? `?${queryString}` : ''}`
      const response = await apiFetch<IActivityLogsResponse>(url, { method: 'get' })
    
      return response;
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
