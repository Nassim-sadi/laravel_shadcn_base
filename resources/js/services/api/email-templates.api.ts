import { useMutation, useQuery, useQueryClient } from '@tanstack/vue-query'
import { useApiFetch } from '@/composables/use-fetch'
import type { IResponse } from '../types/response.type'

export interface IEmailTemplate {
  id: number
  key: string
  name: string
  subject: string
  body: string
  variables?: string[]
  is_active: boolean
  created_at: string
  updated_at: string
}

export interface IEmailTemplatesResponse {
  data: IEmailTemplate[]
  current_page: number
  last_page: number
  per_page: number
  total: number
}

export interface ICreateEmailTemplateRequest {
  key: string
  name: string
  subject: string
  body: string
  variables?: string[]
  is_active?: boolean
}

export function useGetEmailTemplatesQuery() {
  const { apiFetch } = useApiFetch()
  return useQuery<IResponse<IEmailTemplatesResponse>, Error>({
    queryKey: ['useGetEmailTemplatesQuery'],
    queryFn: () => apiFetch('/email-templates', { method: 'get' }),
  })
}

export function useGetEmailTemplateByIdQuery(id: number) {
  const { apiFetch } = useApiFetch()
  return useQuery<IResponse<IEmailTemplate>, Error>({
    queryKey: ['useGetEmailTemplateByIdQuery', id],
    queryFn: () => apiFetch(`/email-templates/${id}`, { method: 'get' }),
  })
}

export function useCreateEmailTemplateMutation() {
  const { apiFetch } = useApiFetch()
  const queryClient = useQueryClient()
  return useMutation<IResponse<IEmailTemplate>, Error, ICreateEmailTemplateRequest>({
    mutationKey: ['useCreateEmailTemplateMutation'],
    mutationFn: (data) => apiFetch('/email-templates', { method: 'post', body: data }),
    onSuccess: () => queryClient.invalidateQueries({ queryKey: ['useGetEmailTemplatesQuery'] }),
  })
}

export function useUpdateEmailTemplateMutation(id: number) {
  const { apiFetch } = useApiFetch()
  const queryClient = useQueryClient()
  return useMutation<IResponse<IEmailTemplate>, Error, Partial<ICreateEmailTemplateRequest>>({
    mutationKey: ['useUpdateEmailTemplateMutation', id],
    mutationFn: (data) => apiFetch(`/email-templates/${id}`, { method: 'put', body: data }),
    onSuccess: () => {
      queryClient.invalidateQueries({ queryKey: ['useGetEmailTemplatesQuery'] })
      queryClient.invalidateQueries({ queryKey: ['useGetEmailTemplateByIdQuery', id] })
    },
  })
}

export function useDeleteEmailTemplateMutation() {
  const { apiFetch } = useApiFetch()
  const queryClient = useQueryClient()
  return useMutation<IResponse<string>, Error, number>({
    mutationKey: ['useDeleteEmailTemplateMutation'],
    mutationFn: (id) => apiFetch(`/email-templates/${id}`, { method: 'delete' }),
    onSuccess: () => queryClient.invalidateQueries({ queryKey: ['useGetEmailTemplatesQuery'] }),
  })
}
