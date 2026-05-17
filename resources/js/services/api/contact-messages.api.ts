import { useMutation, useQuery, useQueryClient } from '@tanstack/vue-query'

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
    onSuccess: () => queryClient.invalidateQueries({ queryKey: ['useGetContactMessagesQuery'] }),
  })
}

export function useGetContactMessagesQuery() {
  const { apiFetch } = useApiFetch()
  return useQuery<IResponse<IContactMessagesResponse>, Error>({
    queryKey: ['useGetContactMessagesQuery'],
    queryFn: () => apiFetch('/contact-messages', { method: 'get' }),
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
    },
  })
}

export function useDeleteContactMessageMutation() {
  const { apiFetch } = useApiFetch()
  const queryClient = useQueryClient()
  return useMutation<IResponse<string>, Error, number>({
    mutationKey: ['useDeleteContactMessageMutation'],
    mutationFn: id => apiFetch(`/contact-messages/${id}`, { method: 'delete' }),
    onSuccess: () => queryClient.invalidateQueries({ queryKey: ['useGetContactMessagesQuery'] }),
  })
}
