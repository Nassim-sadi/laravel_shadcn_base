import { useMutation, useQuery, useQueryClient } from '@tanstack/vue-query'
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

export function useGetFaqsQuery() {
  const { apiFetch } = useApiFetch()
  return useQuery<IResponse<IFaqsResponse>, Error>({
    queryKey: ['useGetFaqsQuery'],
    queryFn: () => apiFetch('/faqs', { method: 'get' }),
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
    mutationFn: (data) => apiFetch('/faqs', { method: 'post', body: data }),
    onSuccess: () => queryClient.invalidateQueries({ queryKey: ['useGetFaqsQuery'] }),
  })
}

export function useUpdateFaqMutation(id: number) {
  const { apiFetch } = useApiFetch()
  const queryClient = useQueryClient()
  return useMutation<IResponse<IFaq>, Error, Partial<ICreateFaqRequest>>({
    mutationKey: ['useUpdateFaqMutation', id],
    mutationFn: (data) => apiFetch(`/faqs/${id}`, { method: 'put', body: data }),
    onSuccess: () => {
      queryClient.invalidateQueries({ queryKey: ['useGetFaqsQuery'] })
      queryClient.invalidateQueries({ queryKey: ['useGetFaqByIdQuery', id] })
    },
  })
}

export function useDeleteFaqMutation() {
  const { apiFetch } = useApiFetch()
  const queryClient = useQueryClient()
  return useMutation<IResponse<string>, Error, number>({
    mutationKey: ['useDeleteFaqMutation'],
    mutationFn: (id) => apiFetch(`/faqs/${id}`, { method: 'delete' }),
    onSuccess: () => queryClient.invalidateQueries({ queryKey: ['useGetFaqsQuery'] }),
  })
}
