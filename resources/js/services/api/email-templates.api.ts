import type { Ref } from 'vue'
import { useMutation, useQuery, useQueryClient } from '@tanstack/vue-query'
import { toast } from 'vue-sonner'

import { useApiFetch } from '@/composables/use-fetch'

import type { IResponse } from '../types/response.type'

export type TranslatedValue = Record<string, string>

export interface IEmailTemplate {
  id: number
  key: string
  name: string
  name_translations: TranslatedValue
  subject: string
  subject_translations: TranslatedValue
  body: string
  body_translations: TranslatedValue
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
  name: TranslatedValue
  subject: TranslatedValue
  body: TranslatedValue
  variables?: string[]
  is_active?: boolean
}

export interface EmailTemplateFilters {
  search?: string
  is_active?: string
  page?: number
  per_page?: number
  sort_by?: string
  sort_order?: string
}

function buildUrl(path: string, params?: EmailTemplateFilters): string {
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

export function useGetEmailTemplatesQuery(params?: Ref<EmailTemplateFilters>) {
  const { apiFetch } = useApiFetch()
  return useQuery<IResponse<IEmailTemplatesResponse>, Error>({
    queryKey: ['useGetEmailTemplatesQuery', params?.value],
    queryFn: () => apiFetch(buildUrl('/email-templates', params?.value), { method: 'get' }),
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
    mutationFn: data => apiFetch('/email-templates', { method: 'post', body: data }),
    onSuccess: () => {
      queryClient.invalidateQueries({ queryKey: ['useGetEmailTemplatesQuery'] })
      toast.success('Email template created')
    },
    onError: (error) => {
      toast.error(error?.message ?? 'Failed to create email template')
    },
  })
}

export function useUpdateEmailTemplateMutation(id: number) {
  const { apiFetch } = useApiFetch()
  const queryClient = useQueryClient()
  return useMutation<IResponse<IEmailTemplate>, Error, Partial<ICreateEmailTemplateRequest>>({
    mutationKey: ['useUpdateEmailTemplateMutation', id],
    mutationFn: data => apiFetch(`/email-templates/${id}`, { method: 'put', body: data }),
    onSuccess: () => {
      queryClient.invalidateQueries({ queryKey: ['useGetEmailTemplatesQuery'] })
      queryClient.invalidateQueries({ queryKey: ['useGetEmailTemplateByIdQuery', id] })
      toast.success('Email template updated')
    },
    onError: (error) => {
      toast.error(error?.message ?? 'Failed to update email template')
    },
  })
}

export function useDeleteEmailTemplateMutation() {
  const { apiFetch } = useApiFetch()
  const queryClient = useQueryClient()
  return useMutation<IResponse<string>, Error, number>({
    mutationKey: ['useDeleteEmailTemplateMutation'],
    mutationFn: id => apiFetch(`/email-templates/${id}`, { method: 'delete' }),
    onSuccess: () => {
      queryClient.invalidateQueries({ queryKey: ['useGetEmailTemplatesQuery'] })
      toast.success('Email template deleted')
    },
    onError: (error) => {
      toast.error(error?.message ?? 'Failed to delete email template')
    },
  })
}

export function useToggleEmailTemplateStatusMutation() {
  const { apiFetch } = useApiFetch()
  const queryClient = useQueryClient()
  return useMutation<IResponse<{ is_active: boolean }>, Error, number>({
    mutationKey: ['useToggleEmailTemplateStatusMutation'],
    mutationFn: id => apiFetch(`/email-templates/${id}/toggle-status`, { method: 'post' }),
    onSuccess: () => {
      queryClient.invalidateQueries({ queryKey: ['useGetEmailTemplatesQuery'] })
      toast.success('Email template status updated')
    },
    onError: (error) => {
      toast.error(error?.message ?? 'Failed to update email template status')
    },
  })
}
