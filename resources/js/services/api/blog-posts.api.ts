import type { Ref } from 'vue'
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

export interface BlogPostFilters {
  search?: string
  is_published?: string
  page?: number
  per_page?: number
  sort_by?: string
  sort_order?: string
}

function buildUrl(path: string, params?: BlogPostFilters): string {
  if (!params) return path
  const searchParams = new URLSearchParams()
  if (params.search) searchParams.set('search', params.search)
  if (params.is_published !== undefined) searchParams.set('is_published', params.is_published)
  if (params.page) searchParams.set('page', String(params.page))
  if (params.per_page) searchParams.set('per_page', String(params.per_page))
  if (params.sort_by) searchParams.set('sort_by', params.sort_by)
  if (params.sort_order) searchParams.set('sort_order', params.sort_order)
  const qs = searchParams.toString()
  return qs ? `${path}?${qs}` : path
}

export function useGetBlogPostsQuery(params?: Ref<BlogPostFilters>) {
  const { apiFetch } = useApiFetch()
  return useQuery<IResponse<IBlogPostsResponse>, Error>({
    queryKey: ['useGetBlogPostsQuery', params?.value],
    queryFn: () => apiFetch(buildUrl('/blog-posts', params?.value), { method: 'get' }),
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

export function useToggleBlogPostStatusMutation() {
  const { apiFetch } = useApiFetch()
  const queryClient = useQueryClient()
  return useMutation<IResponse<{ is_published: boolean }>, Error, number>({
    mutationKey: ['useToggleBlogPostStatusMutation'],
    mutationFn: id => apiFetch(`/blog-posts/${id}/toggle-status`, { method: 'post' }),
    onSuccess: () => queryClient.invalidateQueries({ queryKey: ['useGetBlogPostsQuery'] }),
  })
}
