import { useMutation, useQuery, useQueryClient } from '@tanstack/vue-query'
import { useApiFetch } from '@/composables/use-fetch'
import type { IResponse } from '../types/response.type'

export interface IProject {
  id: number
  title: string
  description?: string
  client?: string
  image?: string
  url?: string
  technologies?: string[]
  order: number
  is_active: boolean
  seo_title?: string
  seo_description?: string
  seo_keywords?: string
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
  title: string
  description?: string
  client?: string
  url?: string
  technologies?: string[]
  order?: number
  is_active?: boolean
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
    mutationFn: (data) => apiFetch('/projects', { method: 'post', body: data }),
    onSuccess: () => queryClient.invalidateQueries({ queryKey: ['useGetProjectsQuery'] }),
  })
}

export function useUpdateProjectMutation(id: number) {
  const { apiFetch } = useApiFetch()
  const queryClient = useQueryClient()
  return useMutation<IResponse<IProject>, Error, Partial<ICreateProjectRequest>>({
    mutationKey: ['useUpdateProjectMutation', id],
    mutationFn: (data) => apiFetch(`/projects/${id}`, { method: 'put', body: data }),
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
    mutationFn: (id) => apiFetch(`/projects/${id}`, { method: 'delete' }),
    onSuccess: () => queryClient.invalidateQueries({ queryKey: ['useGetProjectsQuery'] }),
  })
}
