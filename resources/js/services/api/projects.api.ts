import { useMutation, useQuery, useQueryClient } from '@tanstack/vue-query'

import { useApiFetch } from '@/composables/use-fetch'

import type { IResponse } from '../types/response.type'

export interface IProject {
  id: number
  title: string
  title_translations: Record<string, string | null>
  description?: string
  description_translations?: Record<string, string | null>
  client?: string
  client_translations?: Record<string, string | null>
  image?: string
  image_id?: number | null
  image_url?: string | null
  image_thumbnail_url?: string | null
  url?: string
  technologies?: string[]
  order: number
  is_active: boolean
  seo_title?: string
  seo_title_translations?: Record<string, string | null>
  seo_description?: string
  seo_description_translations?: Record<string, string | null>
  seo_keywords?: string
  seo_keywords_translations?: Record<string, string | null>
  created_at: string
  updated_at: string
}

export interface IProjectsResponse {
  data: IProject[]
  current_page: number
  last_page: number
  per_page: number
  total: number
}

export interface ICreateProjectRequest {
  title: Record<string, string | null>
  description?: Record<string, string | null>
  client?: Record<string, string | null>
  image_id?: number | null
  url?: string
  technologies?: string[]
  order?: number
  is_active?: boolean
  seo_title?: Record<string, string | null>
  seo_description?: Record<string, string | null>
  seo_keywords?: Record<string, string | null>
}

export function useGetProjectsQuery() {
  const { apiFetch } = useApiFetch()
  return useQuery<IResponse<IProjectsResponse>, Error>({
    queryKey: ['useGetProjectsQuery'],
    queryFn: () => apiFetch('/projects', { method: 'get' }),
  })
}

export function useGetProjectByIdQuery(id: number) {
  const { apiFetch } = useApiFetch()
  return useQuery<IResponse<IProject>, Error>({
    queryKey: ['useGetProjectByIdQuery', id],
    queryFn: () => apiFetch(`/projects/${id}`, { method: 'get' }),
  })
}

export function useCreateProjectMutation() {
  const { apiFetch } = useApiFetch()
  const queryClient = useQueryClient()
  return useMutation<IResponse<IProject>, Error, ICreateProjectRequest>({
    mutationKey: ['useCreateProjectMutation'],
    mutationFn: data => apiFetch('/projects', { method: 'post', body: data }),
    onSuccess: () => queryClient.invalidateQueries({ queryKey: ['useGetProjectsQuery'] }),
  })
}

export function useUpdateProjectMutation(id?: number) {
  const { apiFetch } = useApiFetch()
  const queryClient = useQueryClient()
  return useMutation<IResponse<IProject>, Error, Partial<ICreateProjectRequest> & { id?: number }>({
    mutationKey: ['useUpdateProjectMutation', id],
    mutationFn: (data) => {
      const projectId = data.id ?? id
      const { id: _id, ...body } = data

      return apiFetch(`/projects/${projectId}`, { method: 'put', body })
    },
    onSuccess: () => {
      queryClient.invalidateQueries({ queryKey: ['useGetProjectsQuery'] })
      queryClient.invalidateQueries({ queryKey: ['useGetProjectByIdQuery', id] })
    },
  })
}

export function useDeleteProjectMutation() {
  const { apiFetch } = useApiFetch()
  const queryClient = useQueryClient()
  return useMutation<IResponse<string>, Error, number>({
    mutationKey: ['useDeleteProjectMutation'],
    mutationFn: id => apiFetch(`/projects/${id}`, { method: 'delete' }),
    onSuccess: () => queryClient.invalidateQueries({ queryKey: ['useGetProjectsQuery'] }),
  })
}
