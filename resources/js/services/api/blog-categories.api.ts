import { useMutation, useQuery, useQueryClient } from '@tanstack/vue-query'
import { useApiFetch } from '@/composables/use-fetch'
import type { IResponse } from '../types/response.type'

export interface IBlogCategory {
  id: number
  name: string | Record<string, string>
  slug: string
  description?: string | Record<string, string | null>
  is_published: boolean
  posts_count?: number
  created_at: string
  updated_at: string
}

export interface IBlogCategoriesResponse {
  data: IBlogCategory[]
  current_page: number
  last_page: number
  per_page: number
  total: number
}

export interface ICreateBlogCategoryRequest {
  name: Record<string, string | null>
  slug: string
  description?: Record<string, string | null>
  is_published?: boolean
}

export function useGetBlogCategoriesQuery() {
  const { apiFetch } = useApiFetch()
  return useQuery<IResponse<IBlogCategoriesResponse>, Error>({
    queryKey: ['useGetBlogCategoriesQuery'],
    queryFn: () => apiFetch('/blog-categories', { method: 'get' }),
  })
}

export function useCreateBlogCategoryMutation() {
  const { apiFetch } = useApiFetch()
  const queryClient = useQueryClient()
  return useMutation<IResponse<IBlogCategory>, Error, ICreateBlogCategoryRequest>({
    mutationKey: ['useCreateBlogCategoryMutation'],
    mutationFn: data => apiFetch('/blog-categories', { method: 'post', body: data }),
    onSuccess: () => queryClient.invalidateQueries({ queryKey: ['useGetBlogCategoriesQuery'] }),
  })
}

export function useUpdateBlogCategoryMutation(id?: number) {
  const { apiFetch } = useApiFetch()
  const queryClient = useQueryClient()
  return useMutation<IResponse<IBlogCategory>, Error, Partial<ICreateBlogCategoryRequest> & { id?: number }>({
    mutationKey: ['useUpdateBlogCategoryMutation', id],
    mutationFn: (data) => {
      const catId = data.id ?? id
      const { id: _id, ...body } = data
      return apiFetch(`/blog-categories/${catId}`, { method: 'put', body })
    },
    onSuccess: () => queryClient.invalidateQueries({ queryKey: ['useGetBlogCategoriesQuery'] }),
  })
}

export function useDeleteBlogCategoryMutation() {
  const { apiFetch } = useApiFetch()
  const queryClient = useQueryClient()
  return useMutation<IResponse<string>, Error, number>({
    mutationKey: ['useDeleteBlogCategoryMutation'],
    mutationFn: id => apiFetch(`/blog-categories/${id}`, { method: 'delete' }),
    onSuccess: () => queryClient.invalidateQueries({ queryKey: ['useGetBlogCategoriesQuery'] }),
  })
}
