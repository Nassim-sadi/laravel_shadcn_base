import { ofetch } from 'ofetch'
import { useMutation, useQuery, useQueryClient } from '@tanstack/vue-query'

import { useApiFetch } from '@/composables/use-fetch'

import type { IResponse } from '../types/response.type'

export interface IUser {
  id: number
  name: string
  email: string
  role: string
  is_active: boolean
  locale: string
  avatar: string | null
  avatar_url?: string | null
  email_verified_at: string | null
  roles: string[]
  permissions: string[]
}

export interface ILoginRequest {
  email: string
  password: string
}

export interface IRegisterRequest {
  name: string
  email: string
  password: string
  password_confirmation: string
}

function getXSRFToken(): string | undefined {
  const match = document.cookie.match(/(?:^|;\s*)XSRF-TOKEN=([^;]*)/)
  if (!match) return undefined
  try {
    return decodeURIComponent(match[1])
  }
  catch {
    return match[1]
  }
}

const csrfFetch = ofetch.create({
  credentials: 'include',
  onRequest({ options }) {
    const token = getXSRFToken()
    if (token) {
      if (options.headers instanceof Headers) {
        options.headers.set('X-XSRF-TOKEN', token)
      } else if (options.headers) {
        (options.headers as Record<string, string>)['X-XSRF-TOKEN'] = token
      }
    }
  },
})

async function fetchCsrfCookie(): Promise<void> {
  await csrfFetch('/sanctum/csrf-cookie')
}

export function useLoginMutation() {
  const queryClient = useQueryClient()

  return useMutation<IUser, Error, ILoginRequest>({
    mutationKey: ['useLoginMutation'],
    mutationFn: async (data: ILoginRequest) => {
      await fetchCsrfCookie()
      const response = await csrfFetch<{ user: IUser }>('/login', {
        method: 'post',
        body: data,
        headers: { Accept: 'application/json' },
      })
      return response.user
    },
    onSuccess: () => {
      queryClient.invalidateQueries({ queryKey: ['useUserQuery'] })
    },
  })
}

export function useRegisterMutation() {
  const queryClient = useQueryClient()

  return useMutation<IUser, Error, IRegisterRequest>({
    mutationKey: ['useRegisterMutation'],
    mutationFn: async (data: IRegisterRequest) => {
      await fetchCsrfCookie()
      const response = await csrfFetch<{ user: IUser }>('/register', {
        method: 'post',
        body: data,
        headers: { Accept: 'application/json' },
      })
      return response.user
    },
    onSuccess: () => {
      queryClient.invalidateQueries({ queryKey: ['useUserQuery'] })
    },
  })
}

export function useLogoutMutation() {
  const queryClient = useQueryClient()

  return useMutation<void, Error>({
    mutationKey: ['useLogoutMutation'],
    mutationFn: async () => {
      await csrfFetch('/logout', {
        method: 'post',
        headers: { Accept: 'application/json' },
      })
    },
    onSuccess: () => {
      queryClient.clear()
    },
  })
}

export function useUserQuery(enabled = true) {
  const { apiFetch } = useApiFetch()

  return useQuery<IResponse<IUser>, Error>({
    queryKey: ['useUserQuery'],
    queryFn: async () => await apiFetch<IResponse<IUser>>('/user', {
      method: 'get',
    }),
    enabled,
    staleTime: 5 * 60 * 1000,
  })
}

export interface IUpdateProfileRequest {
  name?: string
  locale?: string
}

export function useUpdateProfileMutation() {
  const { apiFetch } = useApiFetch()
  const queryClient = useQueryClient()

  return useMutation<IResponse<IUser>, Error, IUpdateProfileRequest>({
    mutationKey: ['useUpdateProfileMutation'],
    mutationFn: async (data: IUpdateProfileRequest) => await apiFetch<IResponse<IUser>>('/profile', {
      method: 'put',
      body: data,
    }),
    onSuccess: () => {
      queryClient.invalidateQueries({ queryKey: ['useUserQuery'] })
    },
  })
}

export interface IChangePasswordRequest {
  current_password: string
  password: string
  password_confirmation: string
}

export function useChangePasswordMutation() {
  const { apiFetch } = useApiFetch()

  return useMutation<IResponse<string>, Error, IChangePasswordRequest>({
    mutationKey: ['useChangePasswordMutation'],
    mutationFn: async (data: IChangePasswordRequest) => await apiFetch<IResponse<string>>('/change-password', {
      method: 'post',
      body: data,
    }),
  })
}

export function useUploadAvatarMutation() {
  const { apiFetch } = useApiFetch()
  const queryClient = useQueryClient()

  return useMutation<IResponse<{ message: string, avatar: string, avatar_url: string, data: IUser }>, Error, File>({
    mutationKey: ['useUploadAvatarMutation'],
    mutationFn: async (file: File) => {
      const formData = new FormData()
      formData.append('avatar', file)
      return apiFetch<IResponse<{ message: string, avatar: string, avatar_url: string, data: IUser }>>('/profile/avatar', { method: 'post', body: formData })
    },
    onSuccess: () => {
      queryClient.invalidateQueries({ queryKey: ['useUserQuery'] })
    },
  })
}

export function useDeleteAvatarMutation() {
  const { apiFetch } = useApiFetch()
  const queryClient = useQueryClient()

  return useMutation<IResponse<string>, Error>({
    mutationKey: ['useDeleteAvatarMutation'],
    mutationFn: async () => apiFetch<IResponse<string>>('/profile/avatar', { method: 'delete' }),
    onSuccess: () => {
      queryClient.invalidateQueries({ queryKey: ['useUserQuery'] })
    },
  })
}
