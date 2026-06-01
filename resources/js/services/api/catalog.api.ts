import type { Ref } from 'vue'
import { useMutation, useQuery, useQueryClient } from '@tanstack/vue-query'
import { toast } from 'vue-sonner'
import { useApiFetch } from '@/composables/use-fetch'
import type { IResponse } from '../types/response.type'

export interface ICatalogCategory {
  id: number
  name: string
  name_translations: Record<string, string | null>
  slug: string
  description?: string
  description_translations?: Record<string, string | null>
  image_id?: number | null
  image_url?: string | null
  image_thumbnail_url?: string | null
  parent_id?: number | null
  parent?: ICatalogCategory
  children?: ICatalogCategory[]
  order: number
  is_active: boolean
  created_at: string
  updated_at: string
}

export interface ICatalogProduct {
  id: number
  name: string
  name_translations: Record<string, string | null>
  slug: string
  description?: string
  description_translations?: Record<string, string | null>
  body?: string
  body_translations?: Record<string, string | null>
  sku?: string
  price_display?: string
  badges: string[]
  category_id?: number | null
  category?: ICatalogCategory
  brand_id?: number | null
  brand?: ICatalogBrand
  media?: ICatalogProductMedia[]
  tags?: ICatalogTag[]
  is_active: boolean
  order: number
  created_at: string
  updated_at: string
}

export interface ICatalogProductMedia {
  id: number
  media_id?: number | null
  type: 'image' | 'video'
  video_url?: string
  thumbnail_url?: string
  image_url?: string
  image_thumbnail_url?: string
  order: number
}

export interface ICatalogTag {
  id: number
  name: string
  name_translations: Record<string, string | null>
  slug: string
  products_count?: number
}

export interface ICatalogMarqueeItem {
  id: number
  image_url?: string
  image_thumbnail_url?: string
  text?: Record<string, string | null>
  position: number
  order: number
  is_active: boolean
}

export interface IQuoteRequest {
  id: number
  name: string
  email: string
  phone?: string
  message?: string
  product_id?: number | null
  product?: ICatalogProduct
  is_read: boolean
  replied_at?: string
  reply?: string
  created_at: string
}

export interface ICategoryFilters {
  search?: string
  is_active?: string
  parent_id?: string
  page?: number
  per_page?: number
  sort_by?: string
  sort_order?: string
}

export interface IProductFilters {
  search?: string
  category_id?: string
  is_active?: string
  tag?: string
  page?: number
  per_page?: number
  sort_by?: string
  sort_order?: string
}

export interface IQuoteFilters {
  search?: string
  is_read?: string
  product_id?: string
  page?: number
  per_page?: number
}

function buildUrl(path: string, params?: Record<string, any>): string {
  if (!params) return path
  const searchParams = new URLSearchParams()
  Object.entries(params).forEach(([key, value]) => {
    if (value !== undefined && value !== null && value !== '') {
      searchParams.set(key, String(value))
    }
  })
  const qs = searchParams.toString()
  return qs ? `${path}?${qs}` : path
}

// Categories
export function useGetCatalogCategoriesQuery(params?: Ref<ICategoryFilters>) {
  const { apiFetch } = useApiFetch()
  return useQuery<IResponse<any>, Error>({
    queryKey: ['useGetCatalogCategoriesQuery', params?.value],
    queryFn: () => apiFetch(buildUrl('/catalog-categories', params?.value), { method: 'get' }),
  })
}

export function useGetAllCatalogCategoriesQuery() {
  const { apiFetch } = useApiFetch()
  return useQuery<IResponse<any>, Error>({
    queryKey: ['useGetAllCatalogCategoriesQuery'],
    queryFn: () => apiFetch('/catalog-categories/all', { method: 'get' }),
  })
}

export function useCreateCatalogCategoryMutation() {
  const { apiFetch } = useApiFetch()
  const queryClient = useQueryClient()
  return useMutation<IResponse<ICatalogCategory>, Error, any>({
    mutationKey: ['useCreateCatalogCategoryMutation'],
    mutationFn: data => apiFetch('/catalog-categories', { method: 'post', body: data }),
    onSuccess: () => {
      queryClient.invalidateQueries({ queryKey: ['useGetCatalogCategoriesQuery'] })
      toast.success('Category created')
    },
    onError: (error) => {
      toast.error(error?.message ?? 'Failed to create category')
    },
  })
}

export function useUpdateCatalogCategoryMutation() {
  const { apiFetch } = useApiFetch()
  const queryClient = useQueryClient()
  return useMutation<IResponse<ICatalogCategory>, Error, any>({
    mutationKey: ['useUpdateCatalogCategoryMutation'],
    mutationFn: ({ id, ...data }: { id: number } & any) => apiFetch(`/catalog-categories/${id}`, { method: 'put', body: data }),
    onSuccess: () => {
      queryClient.invalidateQueries({ queryKey: ['useGetCatalogCategoriesQuery'] })
      toast.success('Category updated')
    },
    onError: (error) => {
      toast.error(error?.message ?? 'Failed to update category')
    },
  })
}

export function useDeleteCatalogCategoryMutation() {
  const { apiFetch } = useApiFetch()
  const queryClient = useQueryClient()
  return useMutation<IResponse<string>, Error, number>({
    mutationKey: ['useDeleteCatalogCategoryMutation'],
    mutationFn: id => apiFetch(`/catalog-categories/${id}`, { method: 'delete' }),
    onSuccess: () => {
      queryClient.invalidateQueries({ queryKey: ['useGetCatalogCategoriesQuery'] })
      toast.success('Category deleted')
    },
    onError: (error) => {
      toast.error(error?.message ?? 'Failed to delete category')
    },
  })
}

export function useToggleCatalogCategoryStatusMutation() {
  const { apiFetch } = useApiFetch()
  const queryClient = useQueryClient()
  return useMutation<IResponse<any>, Error, number>({
    mutationKey: ['useToggleCatalogCategoryStatusMutation'],
    mutationFn: id => apiFetch(`/catalog-categories/${id}/toggle-status`, { method: 'post' }),
    onSuccess: () => {
      queryClient.invalidateQueries({ queryKey: ['useGetCatalogCategoriesQuery'] })
      toast.success('Category status updated')
    },
    onError: (error) => {
      toast.error(error?.message ?? 'Failed to update category status')
    },
  })
}

// Products
export function useGetCatalogProductsQuery(params?: Ref<IProductFilters>) {
  const { apiFetch } = useApiFetch()
  return useQuery<IResponse<any>, Error>({
    queryKey: ['useGetCatalogProductsQuery', params?.value],
    queryFn: () => apiFetch(buildUrl('/catalog-products', params?.value), { method: 'get' }),
  })
}

export function useCreateCatalogProductMutation() {
  const { apiFetch } = useApiFetch()
  const queryClient = useQueryClient()
  return useMutation<IResponse<ICatalogProduct>, Error, any>({
    mutationKey: ['useCreateCatalogProductMutation'],
    mutationFn: data => apiFetch('/catalog-products', { method: 'post', body: data }),
    onSuccess: () => {
      queryClient.invalidateQueries({ queryKey: ['useGetCatalogProductsQuery'] })
      toast.success('Product created')
    },
    onError: (error) => {
      toast.error(error?.message ?? 'Failed to create product')
    },
  })
}

export function useUpdateCatalogProductMutation() {
  const { apiFetch } = useApiFetch()
  const queryClient = useQueryClient()
  return useMutation<IResponse<ICatalogProduct>, Error, any>({
    mutationKey: ['useUpdateCatalogProductMutation'],
    mutationFn: ({ id, ...data }: { id: number } & any) => apiFetch(`/catalog-products/${id}`, { method: 'put', body: data }),
    onSuccess: () => {
      queryClient.invalidateQueries({ queryKey: ['useGetCatalogProductsQuery'] })
      toast.success('Product updated')
    },
    onError: (error) => {
      toast.error(error?.message ?? 'Failed to update product')
    },
  })
}

export function useDeleteCatalogProductMutation() {
  const { apiFetch } = useApiFetch()
  const queryClient = useQueryClient()
  return useMutation<IResponse<string>, Error, number>({
    mutationKey: ['useDeleteCatalogProductMutation'],
    mutationFn: id => apiFetch(`/catalog-products/${id}`, { method: 'delete' }),
    onSuccess: () => {
      queryClient.invalidateQueries({ queryKey: ['useGetCatalogProductsQuery'] })
      toast.success('Product deleted')
    },
    onError: (error) => {
      toast.error(error?.message ?? 'Failed to delete product')
    },
  })
}

export function useToggleCatalogProductStatusMutation() {
  const { apiFetch } = useApiFetch()
  const queryClient = useQueryClient()
  return useMutation<IResponse<any>, Error, number>({
    mutationKey: ['useToggleCatalogProductStatusMutation'],
    mutationFn: id => apiFetch(`/catalog-products/${id}/toggle-status`, { method: 'post' }),
    onSuccess: () => {
      queryClient.invalidateQueries({ queryKey: ['useGetCatalogProductsQuery'] })
      toast.success('Product status updated')
    },
    onError: (error) => {
      toast.error(error?.message ?? 'Failed to update product status')
    },
  })
}

// Tags
export function useGetCatalogTagsQuery() {
  const { apiFetch } = useApiFetch()
  return useQuery<IResponse<any>, Error>({
    queryKey: ['useGetCatalogTagsQuery'],
    queryFn: () => apiFetch('/catalog-tags', { method: 'get' }),
  })
}

export function useCreateCatalogTagMutation() {
  const { apiFetch } = useApiFetch()
  const queryClient = useQueryClient()
  return useMutation<IResponse<ICatalogTag>, Error, any>({
    mutationKey: ['useCreateCatalogTagMutation'],
    mutationFn: data => apiFetch('/catalog-tags', { method: 'post', body: data }),
    onSuccess: () => {
      queryClient.invalidateQueries({ queryKey: ['useGetCatalogTagsQuery'] })
      toast.success('Tag created')
    },
    onError: (error) => {
      toast.error(error?.message ?? 'Failed to create tag')
    },
  })
}

export function useDeleteCatalogTagMutation() {
  const { apiFetch } = useApiFetch()
  const queryClient = useQueryClient()
  return useMutation<IResponse<string>, Error, number>({
    mutationKey: ['useDeleteCatalogTagMutation'],
    mutationFn: id => apiFetch(`/catalog-tags/${id}`, { method: 'delete' }),
    onSuccess: () => {
      queryClient.invalidateQueries({ queryKey: ['useGetCatalogTagsQuery'] })
      toast.success('Tag deleted')
    },
    onError: (error) => {
      toast.error(error?.message ?? 'Failed to delete tag')
    },
  })
}

// Marquee
export function useGetCatalogMarqueeQuery() {
  const { apiFetch } = useApiFetch()
  return useQuery<IResponse<any>, Error>({
    queryKey: ['useGetCatalogMarqueeQuery'],
    queryFn: () => apiFetch('/catalog-marquee', { method: 'get' }),
  })
}

export function useCreateCatalogMarqueeItemMutation() {
  const { apiFetch } = useApiFetch()
  const queryClient = useQueryClient()
  return useMutation<IResponse<ICatalogMarqueeItem>, Error, any>({
    mutationKey: ['useCreateCatalogMarqueeItemMutation'],
    mutationFn: data => apiFetch('/catalog-marquee', { method: 'post', body: data }),
    onSuccess: () => {
      queryClient.invalidateQueries({ queryKey: ['useGetCatalogMarqueeQuery'] })
      toast.success('Marquee item created')
    },
    onError: (error) => {
      toast.error(error?.message ?? 'Failed to create marquee item')
    },
  })
}

export function useUpdateCatalogMarqueeItemMutation() {
  const { apiFetch } = useApiFetch()
  const queryClient = useQueryClient()
  return useMutation<IResponse<ICatalogMarqueeItem>, Error, any>({
    mutationKey: ['useUpdateCatalogMarqueeItemMutation'],
    mutationFn: ({ id, ...data }: { id: number } & any) => apiFetch(`/catalog-marquee/${id}`, { method: 'put', body: data }),
    onSuccess: () => {
      queryClient.invalidateQueries({ queryKey: ['useGetCatalogMarqueeQuery'] })
      toast.success('Marquee item updated')
    },
    onError: (error) => {
      toast.error(error?.message ?? 'Failed to update marquee item')
    },
  })
}

export function useDeleteCatalogMarqueeItemMutation() {
  const { apiFetch } = useApiFetch()
  const queryClient = useQueryClient()
  return useMutation<IResponse<string>, Error, number>({
    mutationKey: ['useDeleteCatalogMarqueeItemMutation'],
    mutationFn: id => apiFetch(`/catalog-marquee/${id}`, { method: 'delete' }),
    onSuccess: () => {
      queryClient.invalidateQueries({ queryKey: ['useGetCatalogMarqueeQuery'] })
      toast.success('Marquee item deleted')
    },
    onError: (error) => {
      toast.error(error?.message ?? 'Failed to delete marquee item')
    },
  })
}

// Quote Requests
export function useGetQuoteRequestsQuery(params?: Ref<IQuoteFilters>) {
  const { apiFetch } = useApiFetch()
  return useQuery<IResponse<any>, Error>({
    queryKey: ['useGetQuoteRequestsQuery', params?.value],
    queryFn: () => apiFetch(buildUrl('/quote-requests', params?.value), { method: 'get' }),
  })
}

export function useReplyQuoteRequestMutation() {
  const { apiFetch } = useApiFetch()
  const queryClient = useQueryClient()
  return useMutation<IResponse<IQuoteRequest>, Error, { id: number; reply: string }>({
    mutationKey: ['useReplyQuoteRequestMutation'],
    mutationFn: ({ id, reply }) => apiFetch(`/quote-requests/${id}/reply`, { method: 'post', body: { reply } }),
    onSuccess: () => {
      queryClient.invalidateQueries({ queryKey: ['useGetQuoteRequestsQuery'] })
      toast.success('Reply sent')
    },
    onError: (error) => {
      toast.error(error?.message ?? 'Failed to send reply')
    },
  })
}

export function useDeleteQuoteRequestMutation() {
  const { apiFetch } = useApiFetch()
  const queryClient = useQueryClient()
  return useMutation<IResponse<string>, Error, number>({
    mutationKey: ['useDeleteQuoteRequestMutation'],
    mutationFn: id => apiFetch(`/quote-requests/${id}`, { method: 'delete' }),
    onSuccess: () => {
      queryClient.invalidateQueries({ queryKey: ['useGetQuoteRequestsQuery'] })
      toast.success('Quote request deleted')
    },
    onError: (error) => {
      toast.error(error?.message ?? 'Failed to delete quote request')
    },
  })
}

export function useBulkDeleteQuoteRequestsMutation() {
  const { apiFetch } = useApiFetch()
  const queryClient = useQueryClient()
  return useMutation<IResponse<any>, Error, number[]>({
    mutationKey: ['useBulkDeleteQuoteRequestsMutation'],
    mutationFn: ids => apiFetch('/quote-requests/bulk-delete', { method: 'post', body: { ids } }),
    onSuccess: () => {
      queryClient.invalidateQueries({ queryKey: ['useGetQuoteRequestsQuery'] })
      toast.success('Quote requests deleted')
    },
    onError: (error) => {
      toast.error(error?.message ?? 'Failed to delete quote requests')
    },
  })
}

export interface ICatalogBrand {
  id: number
  name: string
  name_translations: Record<string, string | null>
  slug: string
  logo_id?: number | null
  logo_url?: string | null
  description?: string
  website?: string
  is_active: boolean
  order: number
  products_count?: number
  created_at: string
  updated_at: string
}

export function useGetCatalogBrandsQuery() {
  const { apiFetch } = useApiFetch()
  return useQuery<IResponse<any>, Error>({
    queryKey: ['useGetCatalogBrandsQuery'],
    queryFn: () => apiFetch('/catalog-brands', { method: 'get' }),
  })
}

export function useGetAllCatalogBrandsQuery() {
  const { apiFetch } = useApiFetch()
  return useQuery<IResponse<any>, Error>({
    queryKey: ['useGetAllCatalogBrandsQuery'],
    queryFn: () => apiFetch('/catalog-brands/all', { method: 'get' }),
  })
}

export function useCreateCatalogBrandMutation() {
  const { apiFetch } = useApiFetch()
  const queryClient = useQueryClient()
  return useMutation<IResponse<ICatalogBrand>, Error, any>({
    mutationKey: ['useCreateCatalogBrandMutation'],
    mutationFn: data => apiFetch('/catalog-brands', { method: 'post', body: data }),
    onSuccess: () => {
      queryClient.invalidateQueries({ queryKey: ['useGetCatalogBrandsQuery'] })
      toast.success('Brand created')
    },
    onError: (error) => {
      toast.error(error?.message ?? 'Failed to create brand')
    },
  })
}

export function useUpdateCatalogBrandMutation() {
  const { apiFetch } = useApiFetch()
  const queryClient = useQueryClient()
  return useMutation<IResponse<ICatalogBrand>, Error, any>({
    mutationKey: ['useUpdateCatalogBrandMutation'],
    mutationFn: ({ id, ...data }: { id: number } & any) => apiFetch(`/catalog-brands/${id}`, { method: 'put', body: data }),
    onSuccess: () => {
      queryClient.invalidateQueries({ queryKey: ['useGetCatalogBrandsQuery'] })
      toast.success('Brand updated')
    },
    onError: (error) => {
      toast.error(error?.message ?? 'Failed to update brand')
    },
  })
}

export function useDeleteCatalogBrandMutation() {
  const { apiFetch } = useApiFetch()
  const queryClient = useQueryClient()
  return useMutation<IResponse<string>, Error, number>({
    mutationKey: ['useDeleteCatalogBrandMutation'],
    mutationFn: id => apiFetch(`/catalog-brands/${id}`, { method: 'delete' }),
    onSuccess: () => {
      queryClient.invalidateQueries({ queryKey: ['useGetCatalogBrandsQuery'] })
      toast.success('Brand deleted')
    },
    onError: (error) => {
      toast.error(error?.message ?? 'Failed to delete brand')
    },
  })
}

export function useToggleCatalogBrandStatusMutation() {
  const { apiFetch } = useApiFetch()
  const queryClient = useQueryClient()
  return useMutation<IResponse<any>, Error, number>({
    mutationKey: ['useToggleCatalogBrandStatusMutation'],
    mutationFn: id => apiFetch(`/catalog-brands/${id}/toggle-status`, { method: 'post' }),
    onSuccess: () => {
      queryClient.invalidateQueries({ queryKey: ['useGetCatalogBrandsQuery'] })
      toast.success('Brand status updated')
    },
    onError: (error) => {
      toast.error(error?.message ?? 'Failed to update brand status')
    },
  })
}
