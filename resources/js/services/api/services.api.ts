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
    mutationFn: (id) => apiFetch(`/services/${id}`, { method: 'delete' }),
    onSuccess: () => queryClient.invalidateQueries({ queryKey: ['useGetServicesQuery'] }),
  })
}
