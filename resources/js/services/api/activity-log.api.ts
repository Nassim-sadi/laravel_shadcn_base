import { useQuery } from '@tanstack/vue-query'

import { useApiFetch } from '@/composables/use-fetch'

import type { IResponse } from '../types/response.type'

export interface IActivityLog {
  id: number
  log_name: string
  description: string
  subject_type?: string
  subject_id?: number
  event: string
  properties?: Record<string, unknown>
  causer_type?: string
  causer_id?: number
  created_at: string
  updated_at?: string
}

export function useGetActivityLogsQuery(options?: {
  logName?: string
  limit?: number
  page?: number
}) {
  const { apiFetch } = useApiFetch()
  const params = new URLSearchParams()
  if (options?.logName)
    params.append('log_name', options.logName)
  if (options?.limit)
    params.append('limit', String(options.limit))
  if (options?.page)
    params.append('page', String(options.page))

  return useQuery<IResponse<IActivityLog[]>, Error>({
    queryKey: ['useGetActivityLogsQuery', options],
    queryFn: async () => {
      const queryString = params.toString()
      const url = `/activity-logs${queryString ? `?${queryString}` : ''}`
      return await apiFetch<IResponse<IActivityLog[]>>(url, {
        method: 'get',
      })
    },
  })
}
