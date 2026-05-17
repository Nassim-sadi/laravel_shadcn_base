import { useMutation, useQuery, useQueryClient } from '@tanstack/vue-query'
import { useApiFetch } from '@/composables/use-fetch'
import type { IResponse } from '../types/response.type'

export interface IBlogPost {
  id: number
  title: string | Record<string, string>
  slug: string
  excerpt?: string | Record<string, string | null>
  body?: string | Record<string, string | null>
  is_published: boolean
  featured: boolean
  category_id: number | null
  category?: { id: number; name?: string | Record<string, string> }
  tag_ids?: number[]
  tags?: { id: number; name: string }[]
  image?: any
  image_id?: number | null
  image_url?: string | null
  image_thumbnail_url?: string | null
  author?: { id: number; name: string }
  user_id: number
  created_at: string
  updated_at: string
}

export interface IBlogPostsResponse {
  data: IBlogPost[]
  current_page: number
  last_page: number
  per_page: number
  total: number
}

export interface ICreateBlogPostRequest {
  title: Record<string, string | null>
  slug: string
  excerpt?: Record<string, string | null>
  body?: Record<string, string | null>
  is_published?: boolean
  featured?: boolean
  category_id?: number | null
  tag_ids?: number[]
}

export function useGetBlogPostsQuery() {
  const { apiFetch } = useApiFetch()
  return useQuery<IResponse<IBlogPostsResponse>, Error>({
    queryKey: ['useGetBlogPostsQuery'],
    queryFn: () => apiFetch('/blog-posts', { method: 'get' }),
  })
}

export function useGetBlogPostByIdQuery(id: number) {
  const { apiFetch } = useApiFetch()
  return useQuery<IResponse<IBlogPost>, Error>({
    queryKey: ['useGetBlogPostByIdQuery', id],
    queryFn: () => apiFetch(`/blog-posts/${id}`, { method: 'get' }),
  })
}

export function useCreateBlogPostMutation() {
  const { apiFetch } = useApiFetch()
  const queryClient = useQueryClient()
  return useMutation<IResponse<IBlogPost>, Error, ICreateBlogPostRequest>({
    mutationKey: ['useCreateBlogPostMutation'],
    mutationFn: data => apiFetch('/blog-posts', { method: 'post', body: data }),
    onSuccess: () => queryClient.invalidateQueries({ queryKey: ['useGetBlogPostsQuery'] }),
  })
}

export function useUpdateBlogPostMutation(id?: number) {
  const { apiFetch } = useApiFetch()
  const queryClient = useQueryClient()
  return useMutation<IResponse<IBlogPost>, Error, Partial<ICreateBlogPostRequest> & { id?: number }>({
    mutationKey: ['useUpdateBlogPostMutation', id],
    mutationFn: (data) => {
      const postId = data.id ?? id
      const { id: _id, ...body } = data
      return apiFetch(`/blog-posts/${postId}`, { method: 'put', body })
    },
    onSuccess: () => {
      queryClient.invalidateQueries({ queryKey: ['useGetBlogPostsQuery'] })
      queryClient.invalidateQueries({ queryKey: ['useGetBlogPostByIdQuery', id] })
    },
  })
}

export function useDeleteBlogPostMutation() {
  const { apiFetch } = useApiFetch()
  const queryClient = useQueryClient()
  return useMutation<IResponse<string>, Error, number>({
    mutationKey: ['useDeleteBlogPostMutation'],
    mutationFn: id => apiFetch(`/blog-posts/${id}`, { method: 'delete' }),
    onSuccess: () => queryClient.invalidateQueries({ queryKey: ['useGetBlogPostsQuery'] }),
  })
}
