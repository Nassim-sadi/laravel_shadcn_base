import { useMutation, useQuery, useQueryClient } from '@tanstack/vue-query'
import { useApiFetch } from '@/composables/use-fetch'
import type { IResponse } from '../types/response.type'

export interface IMedia {
  id: number
  name: string
  file_name: string
  original_name: string
  mime_type: string
  extension: string
  size: number
  url: string
  thumbnail_url: string
  alt_text: string | null
  caption: string | null
  description: string | null
  folder: string | null
  width: number | null
  height: number | null
  created_by: number | null
  created_at: string
  updated_at: string
}

export interface IMediaResponse {
  data: IMedia[]
  current_page: number
  last_page: number
  per_page: number
  total: number
}

export function useGetMediaQuery(params?: Record<string, any>) {
  const { apiFetch } = useApiFetch()
  return useQuery<IResponse<IMediaResponse>, Error>({
    queryKey: ['useGetMediaQuery', params],
    queryFn: () => apiFetch('/media', { method: 'get', query: params ? unref(params) : undefined }),
  })
}

export function useUploadMediaMutation() {
  const { apiFetch } = useApiFetch()
  const queryClient = useQueryClient()
  return useMutation<IResponse<IMedia>, Error, FormData>({
    mutationKey: ['useUploadMediaMutation'],
    mutationFn: (formData) => apiFetch('/media', { method: 'post', body: formData, headers: {} }),
    onSuccess: () => queryClient.invalidateQueries({ queryKey: ['useGetMediaQuery'] }),
  })
}

export function useUpdateMediaMutation(id?: number) {
  const { apiFetch } = useApiFetch()
  const queryClient = useQueryClient()
  return useMutation<IResponse<IMedia>, Error, Record<string, any>>({
    mutationKey: ['useUpdateMediaMutation', id],
    mutationFn: (data) => apiFetch(`/media/${id ?? data.id}`, { method: 'put', body: data }),
    onSuccess: () => {
      queryClient.invalidateQueries({ queryKey: ['useGetMediaQuery'] })
    },
  })
}

export function useDeleteMediaMutation() {
  const { apiFetch } = useApiFetch()
  const queryClient = useQueryClient()
  return useMutation<IResponse<string>, Error, number>({
    mutationKey: ['useDeleteMediaMutation'],
    mutationFn: (id) => apiFetch(`/media/${id}`, { method: 'delete' }),
    onSuccess: () => queryClient.invalidateQueries({ queryKey: ['useGetMediaQuery'] }),
  })
}

export function useBulkDeleteMediaMutation() {
  const { apiFetch } = useApiFetch()
  const queryClient = useQueryClient()
  return useMutation<IResponse<any>, Error, number[]>({
    mutationKey: ['useBulkDeleteMediaMutation'],
    mutationFn: (ids) => apiFetch('/media/bulk-delete', { method: 'post', body: { ids } }),
    onSuccess: () => queryClient.invalidateQueries({ queryKey: ['useGetMediaQuery'] }),
  })
}

export function useGetMediaFoldersQuery() {
  const { apiFetch } = useApiFetch()
  return useQuery<IResponse<{ data: string[] }>, Error>({
    queryKey: ['useGetMediaFoldersQuery'],
    queryFn: () => apiFetch('/media/folders', { method: 'get' }),
  })
}

export function useGetMediaTypesQuery() {
  const { apiFetch } = useApiFetch()
  return useQuery<IResponse<{ data: string[] }>, Error>({
    queryKey: ['useGetMediaTypesQuery'],
    queryFn: () => apiFetch('/media/types', { method: 'get' }),
  })
}
