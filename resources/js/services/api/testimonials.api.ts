import { useMutation, useQuery, useQueryClient } from '@tanstack/vue-query'
import { useApiFetch } from '@/composables/use-fetch'
import type { IResponse } from '../types/response.type'

export type TranslatedValue = Record<string, string>

export interface ITestimonial {
  id: number
  name: string
  name_translations: TranslatedValue
  position?: string
  position_translations?: TranslatedValue
  company?: string
  company_translations?: TranslatedValue
  content: string
  content_translations: TranslatedValue
  image?: string
  image_id?: number | null
  image_url?: string | null
  image_thumbnail_url?: string | null
  rating: number
  is_active: boolean
  order: number
  seo_title?: string
  seo_title_translations?: TranslatedValue
  seo_description?: string
  seo_description_translations?: TranslatedValue
  created_at: string
  updated_at: string
}

export interface ITestimonialsResponse {
  data: ITestimonial[]
  current_page: number
  last_page: number
  per_page: number
  total: number
}

export interface ICreateTestimonialRequest {
  name: TranslatedValue
  position?: TranslatedValue
  company?: TranslatedValue
  content: TranslatedValue
  image?: File
  image_id?: number | null
  rating?: number
  order?: number
  is_active?: boolean
  seo_title?: TranslatedValue
  seo_description?: TranslatedValue
}

export function useGetTestimonialsQuery() {
  const { apiFetch } = useApiFetch()
  return useQuery<IResponse<ITestimonialsResponse>, Error>({
    queryKey: ['useGetTestimonialsQuery'],
    queryFn: () => apiFetch('/testimonials', { method: 'get' }),
  })
}

export function useGetTestimonialByIdQuery(id: number) {
  const { apiFetch } = useApiFetch()
  return useQuery<IResponse<ITestimonial>, Error>({
    queryKey: ['useGetTestimonialByIdQuery', id],
    queryFn: () => apiFetch(`/testimonials/${id}`, { method: 'get' }),
  })
}

export function useCreateTestimonialMutation() {
  const { apiFetch } = useApiFetch()
  const queryClient = useQueryClient()
  return useMutation<IResponse<ITestimonial>, Error, ICreateTestimonialRequest>({
    mutationKey: ['useCreateTestimonialMutation'],
    mutationFn: (data) => apiFetch('/testimonials', { method: 'post', body: data }),
    onSuccess: () => queryClient.invalidateQueries({ queryKey: ['useGetTestimonialsQuery'] }),
  })
}

export function useUpdateTestimonialMutation(id: number) {
  const { apiFetch } = useApiFetch()
  const queryClient = useQueryClient()
  return useMutation<IResponse<ITestimonial>, Error, Partial<ICreateTestimonialRequest>>({
    mutationKey: ['useUpdateTestimonialMutation', id],
    mutationFn: (data) => apiFetch(`/testimonials/${id}`, { method: 'put', body: data }),
    onSuccess: () => {
      queryClient.invalidateQueries({ queryKey: ['useGetTestimonialsQuery'] })
      queryClient.invalidateQueries({ queryKey: ['useGetTestimonialByIdQuery', id] })
    },
  })
}

export function useDeleteTestimonialMutation() {
  const { apiFetch } = useApiFetch()
  const queryClient = useQueryClient()
  return useMutation<IResponse<string>, Error, number>({
    mutationKey: ['useDeleteTestimonialMutation'],
    mutationFn: (id) => apiFetch(`/testimonials/${id}`, { method: 'delete' }),
    onSuccess: () => queryClient.invalidateQueries({ queryKey: ['useGetTestimonialsQuery'] }),
  })
}
