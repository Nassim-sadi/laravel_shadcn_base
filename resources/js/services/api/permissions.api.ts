import { useMutation, useQuery, useQueryClient } from '@tanstack/vue-query'

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

export interface IPermissionsResponse {
  data: IPermission[]
  current_page: number
  last_page: number
  per_page: number
  total: number
}

export function useGetPermissionsQuery() {
  const { apiFetch } = useApiFetch()

  return useQuery<IResponse<IPermissionsResponse>, Error>({
    queryKey: ['useGetPermissionsQuery'],
    queryFn: async () => await apiFetch<IResponse<IPermissionsResponse>>('/permissions', { method: 'get' }),
  })
}

export function useGetPermissionByIdQuery(id: number) {
  const { apiFetch } = useApiFetch()

  return useQuery<IResponse<IPermission>, Error>({
    queryKey: ['useGetPermissionByIdQuery', id],
    queryFn: async () => await apiFetch<IResponse<IPermission>>(`/permissions/${id}`, { method: 'get' }),
  })
}

export function useCreatePermissionMutation() {
  const { apiFetch } = useApiFetch()
  const queryClient = useQueryClient()

  return useMutation<IResponse<IPermission>, Error, Partial<IPermission>>({
    mutationKey: ['useCreatePermissionMutation'],
    mutationFn: async (data: Partial<IPermission>) => apiFetch<IResponse<IPermission>>('/permissions', { method: 'post', body: data }),
    onSuccess: () => {
      queryClient.invalidateQueries({ queryKey: ['useGetPermissionsQuery'] })
    },
  })
}

export function useUpdatePermissionMutation(id?: number) {
  const { apiFetch } = useApiFetch()
  const queryClient = useQueryClient()

  return useMutation<IResponse<IPermission>, Error, Partial<IPermission> & { id?: number }>({
    mutationKey: ['useUpdatePermissionMutation', id],
    mutationFn: async (data) => {
      const permissionId = data.id ?? id
      const { id: _id, ...body } = data
      return apiFetch<IResponse<IPermission>>(`/permissions/${permissionId}`, { method: 'put', body })
    },
    onSuccess: () => {
      queryClient.invalidateQueries({ queryKey: ['useGetPermissionsQuery'] })
    },
  })
}

export function useDeletePermissionMutation() {
  const { apiFetch } = useApiFetch()
  const queryClient = useQueryClient()

  return useMutation<IResponse<boolean>, Error, number>({
    mutationKey: ['useDeletePermissionMutation'],
    mutationFn: async (id: number) => apiFetch<IResponse<boolean>>(`/permissions/${id}`, { method: 'delete' }),
    onSuccess: () => {
      queryClient.invalidateQueries({ queryKey: ['useGetPermissionsQuery'] })
    },
  })
}

export function useGetPermissionGroupsQuery() {
  const { apiFetch } = useApiFetch()

  return useQuery<IResponse<string[]>, Error>({
    queryKey: ['useGetPermissionGroupsQuery'],
    queryFn: async () => apiFetch<IResponse<string[]>>('/permissions/groups', { method: 'get' }),
  })
}

export function useGetAllPermissionsQuery() {
  const { apiFetch } = useApiFetch()

  return useQuery<IResponse<Record<string, IPermission[]>>, Error>({
    queryKey: ['useGetAllPermissionsQuery'],
    queryFn: async () => apiFetch<IResponse<Record<string, IPermission[]>>>('/permissions/all', { method: 'get' }),
  })
}