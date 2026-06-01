import type { Ref } from 'vue'
import { useMutation, useQuery, useQueryClient } from '@tanstack/vue-query'
import { toast } from 'vue-sonner'

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

export interface TestimonialFilters {
  search?: string
  is_active?: string
  page?: number
  per_page?: number
  sort_by?: string
  sort_order?: string
}

function buildUrl(path: string, params?: TestimonialFilters): string {
  if (!params) return path
  const searchParams = new URLSearchParams()
  if (params.search) searchParams.set('search', params.search)
  if (params.is_active !== undefined) searchParams.set('is_active', params.is_active)
  if (params.page) searchParams.set('page', String(params.page))
  if (params.per_page) searchParams.set('per_page', String(params.per_page))
  if (params.sort_by) searchParams.set('sort_by', params.sort_by)
  if (params.sort_order) searchParams.set('sort_order', params.sort_order)
  const qs = searchParams.toString()
  return qs ? `${path}?${qs}` : path
}

export function useGetTestimonialsQuery(params?: Ref<TestimonialFilters>) {
  const { apiFetch } = useApiFetch()
  return useQuery<IResponse<ITestimonialsResponse>, Error>({
    queryKey: ['useGetTestimonialsQuery', params?.value],
    queryFn: () => apiFetch(buildUrl('/testimonials', params?.value), { method: 'get' }),
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
    mutationFn: data => apiFetch('/testimonials', { method: 'post', body: data }),
    onSuccess: () => {
      queryClient.invalidateQueries({ queryKey: ['useGetTestimonialsQuery'] })
      toast.success('Testimonial created')
    },
    onError: (error) => {
      toast.error(error?.message ?? 'Failed to create testimonial')
    },
  })
}

export function useUpdateTestimonialMutation(id: number) {
  const { apiFetch } = useApiFetch()
  const queryClient = useQueryClient()
  return useMutation<IResponse<ITestimonial>, Error, Partial<ICreateTestimonialRequest>>({
    mutationKey: ['useUpdateTestimonialMutation', id],
    mutationFn: data => apiFetch(`/testimonials/${id}`, { method: 'put', body: data }),
    onSuccess: () => {
      queryClient.invalidateQueries({ queryKey: ['useGetTestimonialsQuery'] })
      queryClient.invalidateQueries({ queryKey: ['useGetTestimonialByIdQuery', id] })
      toast.success('Testimonial updated')
    },
    onError: (error) => {
      toast.error(error?.message ?? 'Failed to update testimonial')
    },
  })
}

export function useDeleteTestimonialMutation() {
  const { apiFetch } = useApiFetch()
  const queryClient = useQueryClient()
  return useMutation<IResponse<string>, Error, number>({
    mutationKey: ['useDeleteTestimonialMutation'],
    mutationFn: id => apiFetch(`/testimonials/${id}`, { method: 'delete' }),
    onSuccess: () => {
      queryClient.invalidateQueries({ queryKey: ['useGetTestimonialsQuery'] })
      toast.success('Testimonial deleted')
    },
    onError: (error) => {
      toast.error(error?.message ?? 'Failed to delete testimonial')
    },
  })
}

export function useToggleTestimonialStatusMutation() {
  const { apiFetch } = useApiFetch()
  const queryClient = useQueryClient()
  return useMutation<IResponse<{ is_active: boolean }>, Error, number>({
    mutationKey: ['useToggleTestimonialStatusMutation'],
    mutationFn: id => apiFetch(`/testimonials/${id}/toggle-status`, { method: 'post' }),
    onSuccess: () => {
      queryClient.invalidateQueries({ queryKey: ['useGetTestimonialsQuery'] })
      toast.success('Testimonial status updated')
    },
    onError: (error) => {
      toast.error(error?.message ?? 'Failed to update testimonial status')
    },
  })
}
