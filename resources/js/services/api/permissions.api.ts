import { useQuery } from '@tanstack/vue-query'

import { useApiFetch } from '@/composables/use-fetch'

import type { IResponse } from '../types/response.type'

export interface IPermission {
  id: number
  name: string
  guard_name: string
  group?: string
  description?: string
  created_at?: string
  updated_at?: string
}

export function useGetAllPermissionsQuery() {
  const { apiFetch } = useApiFetch()

  return useQuery<IResponse<Record<string, IPermission[]>>, Error>({
    queryKey: ['useGetAllPermissionsQuery'],
    queryFn: async () => apiFetch<IResponse<Record<string, IPermission[]>>>('/permissions/all', { method: 'get' }),
  })
}
