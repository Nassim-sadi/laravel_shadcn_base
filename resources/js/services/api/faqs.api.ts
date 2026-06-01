import type { Ref } from 'vue'
import { useMutation, useQuery, useQueryClient } from '@tanstack/vue-query'
import { toast } from 'vue-sonner'

import { useApiFetch } from '@/composables/use-fetch'

import type { IResponse } from '../types/response.type'

export type TranslatedValue = Record<string, string>

export interface IFaq {
  id: number
  question: string
  question_translations: TranslatedValue
  answer: string
  answer_translations: TranslatedValue
  category?: string
  order: number
  is_active: boolean
  seo_title?: string
  seo_title_translations?: TranslatedValue
  seo_description?: string
  seo_description_translations?: TranslatedValue
  created_at: string
  updated_at: string
}

export interface IFaqsResponse {
  data: IFaq[]
  current_page: number
  last_page: number
  per_page: number
  total: number
}

export interface ICreateFaqRequest {
  question: TranslatedValue
  answer: TranslatedValue
  category?: string
  order?: number
  is_active?: boolean
  seo_title?: TranslatedValue
  seo_description?: TranslatedValue
}

export interface FaqFilters {
  search?: string
  is_active?: string
  category?: string
  page?: number
  per_page?: number
  sort_by?: string
  sort_order?: string
}

function buildUrl(path: string, params?: FaqFilters): string {
  if (!params) return path
  const searchParams = new URLSearchParams()
  if (params.search) searchParams.set('search', params.search)
  if (params.is_active !== undefined) searchParams.set('is_active', params.is_active)
  if (params.category) searchParams.set('category', params.category)
  if (params.page) searchParams.set('page', String(params.page))
  if (params.per_page) searchParams.set('per_page', String(params.per_page))
  if (params.sort_by) searchParams.set('sort_by', params.sort_by)
  if (params.sort_order) searchParams.set('sort_order', params.sort_order)
  const qs = searchParams.toString()
  return qs ? `${path}?${qs}` : path
}

export function useGetFaqsQuery(params?: Ref<FaqFilters>) {
  const { apiFetch } = useApiFetch()
  return useQuery<IResponse<IFaqsResponse>, Error>({
    queryKey: ['useGetFaqsQuery', params?.value],
    queryFn: () => apiFetch(buildUrl('/faqs', params?.value), { method: 'get' }),
  })
}

export function useGetFaqByIdQuery(id: number) {
  const { apiFetch } = useApiFetch()
  return useQuery<IResponse<IFaq>, Error>({
    queryKey: ['useGetFaqByIdQuery', id],
    queryFn: () => apiFetch(`/faqs/${id}`, { method: 'get' }),
  })
}

export function useCreateFaqMutation() {
  const { apiFetch } = useApiFetch()
  const queryClient = useQueryClient()
  return useMutation<IResponse<IFaq>, Error, ICreateFaqRequest>({
    mutationKey: ['useCreateFaqMutation'],
    mutationFn: data => apiFetch('/faqs', { method: 'post', body: data }),
    onSuccess: () => {
      queryClient.invalidateQueries({ queryKey: ['useGetFaqsQuery'] })
      toast.success('FAQ created')
    },
    onError: (error) => {
      toast.error(error?.message ?? 'Failed to create FAQ')
    },
  })
}

export function useUpdateFaqMutation(id: number) {
  const { apiFetch } = useApiFetch()
  const queryClient = useQueryClient()
  return useMutation<IResponse<IFaq>, Error, Partial<ICreateFaqRequest>>({
    mutationKey: ['useUpdateFaqMutation', id],
    mutationFn: data => apiFetch(`/faqs/${id}`, { method: 'put', body: data }),
    onSuccess: () => {
      queryClient.invalidateQueries({ queryKey: ['useGetFaqsQuery'] })
      queryClient.invalidateQueries({ queryKey: ['useGetFaqByIdQuery', id] })
      toast.success('FAQ updated')
    },
    onError: (error) => {
      toast.error(error?.message ?? 'Failed to update FAQ')
    },
  })
}

export function useDeleteFaqMutation() {
  const { apiFetch } = useApiFetch()
  const queryClient = useQueryClient()
  return useMutation<IResponse<string>, Error, number>({
    mutationKey: ['useDeleteFaqMutation'],
    mutationFn: id => apiFetch(`/faqs/${id}`, { method: 'delete' }),
    onSuccess: () => {
      queryClient.invalidateQueries({ queryKey: ['useGetFaqsQuery'] })
      toast.success('FAQ deleted')
    },
    onError: (error) => {
      toast.error(error?.message ?? 'Failed to delete FAQ')
    },
  })
}

export function useToggleFaqStatusMutation() {
  const { apiFetch } = useApiFetch()
  const queryClient = useQueryClient()
  return useMutation<IResponse<{ is_active: boolean }>, Error, number>({
    mutationKey: ['useToggleFaqStatusMutation'],
    mutationFn: id => apiFetch(`/faqs/${id}/toggle-status`, { method: 'post' }),
    onSuccess: () => {
      queryClient.invalidateQueries({ queryKey: ['useGetFaqsQuery'] })
      toast.success('FAQ status updated')
    },
    onError: (error) => {
      toast.error(error?.message ?? 'Failed to update FAQ status')
    },
  })
}
