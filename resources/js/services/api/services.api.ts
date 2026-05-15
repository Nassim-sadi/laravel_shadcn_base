import { useMutation, useQuery, useQueryClient } from '@tanstack/vue-query'
import { useApiFetch } from '@/composables/use-fetch'
import type { IResponse } from '../types/response.type'

export interface IService {
  id: number
  title: string
  description?: string
  icon?: string
  image?: string
  url?: string
  order: number
  is_active: boolean
  seo_title?: string
  seo_description?: string
  seo_keywords?: string
  created_at: string
  updated_at: string
}

export interface IServicesResponse {
  data: IService[]
  current_page: number
  last_page: number
  per_page: number
  total: number
}

export interface ICreateServiceRequest {
  title: string
  description?: string
  icon?: string
  url?: string
  order?: number
  is_active?: boolean
}

export function useGetServicesQuery() {
  const { apiFetch } = useApiFetch()
  return useQuery<IResponse<IServicesResponse>, Error>({
    queryKey: ['useGetServicesQuery'],
    queryFn: () => apiFetch('/services', { method: 'get' }),
  })
}

export function useGetServiceByIdQuery(id: number) {
  const { apiFetch } = useApiFetch()
  return useQuery<IResponse<IService>, Error>({
    queryKey: ['useGetServiceByIdQuery', id],
    queryFn: () => apiFetch(`/services/${id}`, { method: 'get' }),
  })
}

export function useCreateServiceMutation() {
  const { apiFetch } = useApiFetch()
  const queryClient = useQueryClient()
  return useMutation<IResponse<IService>, Error, ICreateServiceRequest>({
    mutationKey: ['useCreateServiceMutation'],
    mutationFn: (data) => apiFetch('/services', { method: 'post', body: data }),
    onSuccess: () => queryClient.invalidateQueries({ queryKey: ['useGetServicesQuery'] }),
  })
}

export function useUpdateServiceMutation(id: number) {
  const { apiFetch } = useApiFetch()
  const queryClient = useQueryClient()
  return useMutation<IResponse<IService>, Error, Partial<ICreateServiceRequest>>({
    mutationKey: ['useUpdateServiceMutation', id],
    mutationFn: (data) => apiFetch(`/services/${id}`, { method: 'put', body: data }),
    onSuccess: () => {
      queryClient.invalidateQueries({ queryKey: ['useGetServicesQuery'] })
      queryClient.invalidateQueries({ queryKey: ['useGetServiceByIdQuery', id] })
    },
  })
}

export function useDeleteServiceMutation() {
  const { apiFetch } = useApiFetch()
  const queryClient = useQueryClient()
  return useMutation<IResponse<string>, Error, number>({
    mutationKey: ['useDeleteServiceMutation'],
    mutationFn: (id) => apiFetch(`/services/${id}`, { method: 'delete' }),
    onSuccess: () => queryClient.invalidateQueries({ queryKey: ['useGetServicesQuery'] }),
  })
}
