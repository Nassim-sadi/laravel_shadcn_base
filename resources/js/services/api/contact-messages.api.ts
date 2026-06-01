import type { Ref } from 'vue'
import { useMutation, useQuery, useQueryClient } from '@tanstack/vue-query'
import { toast } from 'vue-sonner'

import { useApiFetch } from '@/composables/use-fetch'

import type { IResponse } from '../types/response.type'

export interface IContactMessage {
  id: number
  name: string
  email: string
  phone?: string
  subject: string
  message: string
  is_read: boolean
  replied_at?: string
  reply?: string
  created_at: string
  updated_at: string
}

export interface IContactMessagesResponse {
  data: IContactMessage[]
  current_page: number
  last_page: number
  per_page: number
  total: number
}

export interface ContactMessageFilters {
  search?: string
  is_read?: string
  from_date?: string
  to_date?: string
  page?: number
  per_page?: number
  sort_by?: string
  sort_order?: string
}

function buildUrl(path: string, params?: ContactMessageFilters): string {
  if (!params) return path
  const searchParams = new URLSearchParams()
  if (params.search) searchParams.set('search', params.search)
  if (params.is_read !== undefined) searchParams.set('is_read', params.is_read)
  if (params.from_date) searchParams.set('from_date', params.from_date)
  if (params.to_date) searchParams.set('to_date', params.to_date)
  if (params.page) searchParams.set('page', String(params.page))
  if (params.per_page) searchParams.set('per_page', String(params.per_page))
  if (params.sort_by) searchParams.set('sort_by', params.sort_by)
  if (params.sort_order) searchParams.set('sort_order', params.sort_order)
  const qs = searchParams.toString()
  return qs ? `${path}?${qs}` : path
}

export function useCreateContactMessageMutation() {
  const { apiFetch } = useApiFetch()
  const queryClient = useQueryClient()
  return useMutation<IResponse<IContactMessage>, Error, {
    name: string
    email: string
    phone?: string
    subject: string
    message: string
  }>({
    mutationKey: ['useCreateContactMessageMutation'],
    mutationFn: data => apiFetch('/contact-messages', { method: 'post', body: data }),
    onSuccess: () => {
      queryClient.invalidateQueries({ queryKey: ['useGetContactMessagesQuery'] })
      toast.success('Message sent')
    },
    onError: (error) => {
      toast.error(error?.message ?? 'Failed to send message')
    },
  })
}

export function useGetContactMessagesQuery(params?: Ref<ContactMessageFilters>) {
  const { apiFetch } = useApiFetch()
  return useQuery<IResponse<IContactMessagesResponse>, Error>({
    queryKey: ['useGetContactMessagesQuery', params?.value],
    queryFn: () => apiFetch(buildUrl('/contact-messages', params?.value), { method: 'get' }),
  })
}

export function useGetContactMessageByIdQuery(id: number) {
  const { apiFetch } = useApiFetch()
  return useQuery<IResponse<IContactMessage>, Error>({
    queryKey: ['useGetContactMessageByIdQuery', id],
    queryFn: () => apiFetch(`/contact-messages/${id}`, { method: 'get' }),
  })
}

export function useUpdateContactMessageMutation(id: number) {
  const { apiFetch } = useApiFetch()
  const queryClient = useQueryClient()
  return useMutation<IResponse<IContactMessage>, Error, Partial<{
    is_read: boolean
    reply: string
    replied_at: string
  }>>({
    mutationKey: ['useUpdateContactMessageMutation', id],
    mutationFn: data => apiFetch(`/contact-messages/${id}`, { method: 'put', body: data }),
    onSuccess: () => {
      queryClient.invalidateQueries({ queryKey: ['useGetContactMessagesQuery'] })
      queryClient.invalidateQueries({ queryKey: ['useGetContactMessageByIdQuery', id] })
      toast.success('Message updated')
    },
    onError: (error) => {
      toast.error(error?.message ?? 'Failed to update message')
    },
  })
}

export function useDeleteContactMessageMutation() {
  const { apiFetch } = useApiFetch()
  const queryClient = useQueryClient()
  return useMutation<IResponse<string>, Error, number>({
    mutationKey: ['useDeleteContactMessageMutation'],
    mutationFn: id => apiFetch(`/contact-messages/${id}`, { method: 'delete' }),
    onSuccess: () => {
      queryClient.invalidateQueries({ queryKey: ['useGetContactMessagesQuery'] })
      toast.success('Message deleted')
    },
    onError: (error) => {
      toast.error(error?.message ?? 'Failed to delete message')
    },
  })
}

export function useToggleContactMessageStatusMutation() {
  const { apiFetch } = useApiFetch()
  const queryClient = useQueryClient()
  return useMutation<IResponse<{ is_read: boolean }>, Error, number>({
    mutationKey: ['useToggleContactMessageStatusMutation'],
    mutationFn: id => apiFetch(`/contact-messages/${id}/toggle-status`, { method: 'post' }),
    onSuccess: () => {
      queryClient.invalidateQueries({ queryKey: ['useGetContactMessagesQuery'] })
      toast.success('Message status updated')
    },
    onError: (error) => {
      toast.error(error?.message ?? 'Failed to update message status')
    },
  })
}
