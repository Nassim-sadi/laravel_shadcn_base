import type { Ref } from 'vue'
import { useMutation, useQuery, useQueryClient } from '@tanstack/vue-query'

import { useApiFetch } from '@/composables/use-fetch'

import type { IResponse } from '../types/response.type'

export interface IService {
  id: number
  title: string
  title_translations: Record<string, string | null>
  description?: string
  description_translations?: Record<string, string | null>
  icon?: string
  image?: string
  image_id?: number | null
  image_url?: string | null
  image_thumbnail_url?: string | null
  url?: string
  order: number
  is_active: boolean
  seo_title?: string
  seo_title_translations?: Record<string, string | null>
  seo_description?: string
  seo_description_translations?: Record<string, string | null>
  seo_keywords?: string
  seo_keywords_translations?: Record<string, string | null>
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
  title: Record<string, string | null>
  description?: Record<string, string | null>
  icon?: string
  image_id?: number | null
  url?: string
  order?: number
  is_active?: boolean
  seo_title?: Record<string, string | null>
  seo_description?: Record<string, string | null>
  seo_keywords?: Record<string, string | null>
}

export interface ServiceFilters {
  search?: string
  is_active?: string
  icon?: string
  page?: number
  per_page?: number
  sort_by?: string
  sort_order?: string
}

function buildUrl(path: string, params?: ServiceFilters): string {
  if (!params) return path
  const searchParams = new URLSearchParams()
  if (params.search) searchParams.set('search', params.search)
  if (params.is_active !== undefined) searchParams.set('is_active', params.is_active)
  if (params.icon) searchParams.set('icon', params.icon)
  if (params.page) searchParams.set('page', String(params.page))
  if (params.per_page) searchParams.set('per_page', String(params.per_page))
  if (params.sort_by) searchParams.set('sort_by', params.sort_by)
  if (params.sort_order) searchParams.set('sort_order', params.sort_order)
  const qs = searchParams.toString()
  return qs ? `${path}?${qs}` : path
}

export function useGetServicesQuery(params?: Ref<ServiceFilters>) {
  const { apiFetch } = useApiFetch()
  return useQuery<IResponse<IServicesResponse>, Error>({
    queryKey: ['useGetServicesQuery', params?.value],
    queryFn: () => apiFetch(buildUrl('/services', params?.value), { method: 'get' }),
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
    mutationFn: data => apiFetch('/services', { method: 'post', body: data }),
    onSuccess: () => queryClient.invalidateQueries({ queryKey: ['useGetServicesQuery'] }),
  })
}

export function useUpdateServiceMutation(id?: number) {
  const { apiFetch } = useApiFetch()
  const queryClient = useQueryClient()
  return useMutation<IResponse<IService>, Error, Partial<ICreateServiceRequest> & { id?: number }>({
    mutationKey: ['useUpdateServiceMutation', id],
    mutationFn: (data) => {
      const serviceId = data.id ?? id
      const { id: _id, ...body } = data

      return apiFetch(`/services/${serviceId}`, { method: 'put', body })
    },
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
    mutationFn: id => apiFetch(`/services/${id}`, { method: 'delete' }),
    onSuccess: () => queryClient.invalidateQueries({ queryKey: ['useGetServicesQuery'] }),
  })
}

export function useToggleServiceStatusMutation() {
  const { apiFetch } = useApiFetch()
  const queryClient = useQueryClient()
  return useMutation<IResponse<{ is_active: boolean }>, Error, number>({
    mutationKey: ['useToggleServiceStatusMutation'],
    mutationFn: id => apiFetch(`/services/${id}/toggle-status`, { method: 'post' }),
    onSuccess: () => queryClient.invalidateQueries({ queryKey: ['useGetServicesQuery'] }),
  })
}
