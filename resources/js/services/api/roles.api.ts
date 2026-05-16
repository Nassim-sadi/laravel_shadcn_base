import { useMutation, useQuery, useQueryClient } from '@tanstack/vue-query'

import { useApiFetch } from '@/composables/use-fetch'

import type { IResponse } from '../types/response.type'

export interface IRole {
  id: number
  name: string
  guard_name: string
  permissions: string[]
  description?: string
  created_at?: string
  updated_at?: string
}

export function useGetRolesQuery() {
  const { apiFetch } = useApiFetch()

  return useQuery<IResponse<IRole[]>, Error>({
    queryKey: ['useGetRolesQuery'],
    queryFn: async () => await apiFetch<IResponse<IRole[]>>('/roles', {
      method: 'get',
    }),
  })
}

export function useGetRoleByIdQuery(id: number) {
  const { apiFetch } = useApiFetch()

  return useQuery<IResponse<IRole>, Error>({
    queryKey: ['useGetRoleByIdQuery', id],
    queryFn: async () => await apiFetch<IResponse<IRole>>(`/roles/${id}`, {
      method: 'get',
    }),
  })
}

export function useCreateRoleMutation() {
  const { apiFetch } = useApiFetch()
  const queryClient = useQueryClient()

  return useMutation<IResponse<IRole>, Error, Partial<IRole>>({
    mutationKey: ['useCreateRoleMutation'],
    mutationFn: async (data: Partial<IRole>) => await apiFetch<IResponse<IRole>>('/roles', {
      method: 'post',
      body: data,
    }),
    onSuccess: () => {
      queryClient.invalidateQueries({ queryKey: ['useGetRolesQuery'] })
    },
  })
}

export function useUpdateRoleMutation(id?: number) {
  const { apiFetch } = useApiFetch()
  const queryClient = useQueryClient()

  return useMutation<IResponse<IRole>, Error, Partial<IRole> & { id?: number }>({
    mutationKey: ['useUpdateRoleMutation', id],
    mutationFn: async (data) => {
      const roleId = data.id ?? id
      const { id: _id, ...body } = data
      return await apiFetch<IResponse<IRole>>(`/roles/${roleId}`, {
        method: 'put',
        body,
      })
    },
    onSuccess: (_, data) => {
      const roleId = data.id ?? id
      queryClient.invalidateQueries({ queryKey: ['useGetRolesQuery'] })
      queryClient.invalidateQueries({ queryKey: ['useGetRoleByIdQuery', roleId] })
    },
  })
}

export function useDeleteRoleMutation() {
  const { apiFetch } = useApiFetch()
  const queryClient = useQueryClient()

  return useMutation<IResponse<boolean>, Error, number>({
    mutationKey: ['useDeleteRoleMutation'],
    mutationFn: async (id: number) => await apiFetch<IResponse<boolean>>(`/roles/${id}`, {
      method: 'delete',
    }),
    onSuccess: () => {
      queryClient.invalidateQueries({ queryKey: ['useGetRolesQuery'] })
    },
  })
}
