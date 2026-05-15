import { useMutation, useQuery, useQueryClient } from '@tanstack/vue-query'
import { useApiFetch } from '@/composables/use-fetch'
import type { IResponse } from '../types/response.type'

export interface ITestimonial {
  id: number
  name: string
  position?: string
  company?: string
  content: string
  image?: string
  rating: number
  is_active: boolean
  order: number
  seo_title?: string
  seo_description?: string
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
  name: string
  position?: string
  company?: string
  content: string
  rating?: number
  order?: number
  is_active?: boolean
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
