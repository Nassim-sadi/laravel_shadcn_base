import { useMutation, useQuery } from '@tanstack/vue-query'

import { useApiFetch } from '@/composables/use-fetch'

import type { IResponse } from '../types/response.type'

export interface IPermission {
  id: number
  name: string
  guard_name: string
  description?: string
  created_at?: string
  updated_at?: string
}

export function useGetPermissionsQuery() {
  const { apiFetch } = useApiFetch()

  return useQuery<IResponse<IPermission[]>, Error>({
    queryKey: ['useGetPermissionsQuery'],
    queryFn: async () => await apiFetch<IResponse<IPermission[]>>('/permissions', {
      method: 'get',
    }),
  })
}

export function useSyncPermissionsMutation() {
  const { apiFetch } = useApiFetch()

  return useMutation<IResponse<boolean>, Error, { permissions: string[] }>({
    mutationKey: ['useSyncPermissionsMutation'],
    mutationFn: async (data: { permissions: string[] }) => await apiFetch<IResponse<boolean>>('/permissions/sync', {
      method: 'post',
      body: data,
    }),
  })
}
