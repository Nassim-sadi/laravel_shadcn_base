import { useMutation, useQuery, useQueryClient } from '@tanstack/vue-query'
import { toast } from 'vue-sonner'
import { useApiFetch } from '@/composables/use-fetch'

export interface IBlogTag {
  id: number
  name: string
  slug: string
  posts_count?: number
  created_at: string
  updated_at: string
}

export function useGetBlogTagsQuery() {
  const { apiFetch } = useApiFetch()
  return useQuery<IBlogTag[], Error>({
    queryKey: ['useGetBlogTagsQuery'],
    queryFn: () => apiFetch('/blog-tags', { method: 'get' }),
  })
}

export function useCreateBlogTagMutation() {
  const { apiFetch } = useApiFetch()
  const queryClient = useQueryClient()
  return useMutation<IBlogTag, Error, { name: string; slug: string }>({
    mutationKey: ['useCreateBlogTagMutation'],
    mutationFn: data => apiFetch('/blog-tags', { method: 'post', body: data }),
    onSuccess: () => {
      queryClient.invalidateQueries({ queryKey: ['useGetBlogTagsQuery'] })
      toast.success('Blog tag created')
    },
    onError: (error) => {
      toast.error(error?.message ?? 'Failed to create blog tag')
    },
  })
}

export function useDeleteBlogTagMutation() {
  const { apiFetch } = useApiFetch()
  const queryClient = useQueryClient()
  return useMutation<{ message: string }, Error, number>({
    mutationKey: ['useDeleteBlogTagMutation'],
    mutationFn: id => apiFetch(`/blog-tags/${id}`, { method: 'delete' }),
    onSuccess: () => {
      queryClient.invalidateQueries({ queryKey: ['useGetBlogTagsQuery'] })
      toast.success('Blog tag deleted')
    },
    onError: (error) => {
      toast.error(error?.message ?? 'Failed to delete blog tag')
    },
  })
}
