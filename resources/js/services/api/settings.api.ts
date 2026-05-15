import { useMutation, useQuery, useQueryClient } from '@tanstack/vue-query'
import { useApiFetch } from '@/composables/use-fetch'
import type { IResponse } from '../types/response.type'

export interface ISetting {
  id: number
  key: string
  group: string
  name: string
  value: string | null
  default_value: string | null
  type: string
  description?: string
  is_public: boolean
  created_at: string
  updated_at: string
}

export interface ISettingsResponse {
  data: ISetting[]
  current_page: number
  last_page: number
  per_page: number
  total: number
}

export interface ICreateSettingRequest {
  key: string
  group: string
  name: string
  value?: string
  default_value?: string
  type: 'string' | 'integer' | 'boolean' | 'json' | 'array'
  description?: string
  is_public?: boolean
}

export function useGetSettingsQuery() {
  const { apiFetch } = useApiFetch()
  return useQuery<IResponse<ISettingsResponse>, Error>({
    queryKey: ['useGetSettingsQuery'],
    queryFn: () => apiFetch('/settings', { method: 'get' }),
  })
}

export function useGetSettingByKeyQuery(key: string) {
  const { apiFetch } = useApiFetch()
  return useQuery<IResponse<{ key: string; value: string; type: string }>, Error>({
    queryKey: ['useGetSettingByKeyQuery', key],
    queryFn: () => apiFetch(`/settings/value/${key}`, { method: 'get' }),
  })
}

export function useCreateSettingMutation() {
  const { apiFetch } = useApiFetch()
  const queryClient = useQueryClient()
  return useMutation<IResponse<ISetting>, Error, ICreateSettingRequest>({
    mutationKey: ['useCreateSettingMutation'],
    mutationFn: (data) => apiFetch('/settings', { method: 'post', body: data }),
    onSuccess: () => queryClient.invalidateQueries({ queryKey: ['useGetSettingsQuery'] }),
  })
}

export function useUpdateSettingMutation(id: number) {
  const { apiFetch } = useApiFetch()
  const queryClient = useQueryClient()
  return useMutation<IResponse<ISetting>, Error, Partial<ICreateSettingRequest>>({
    mutationKey: ['useUpdateSettingMutation', id],
    mutationFn: (data) => apiFetch(`/settings/${id}`, { method: 'put', body: data }),
    onSuccess: () => {
      queryClient.invalidateQueries({ queryKey: ['useGetSettingsQuery'] })
    },
  })
}

export function useDeleteSettingMutation() {
  const { apiFetch } = useApiFetch()
  const queryClient = useQueryClient()
  return useMutation<IResponse<string>, Error, number>({
    mutationKey: ['useDeleteSettingMutation'],
    mutationFn: (id) => apiFetch(`/settings/${id}`, { method: 'delete' }),
    onSuccess: () => queryClient.invalidateQueries({ queryKey: ['useGetSettingsQuery'] }),
  })
}
