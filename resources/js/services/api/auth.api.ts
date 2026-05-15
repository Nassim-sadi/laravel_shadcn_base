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
  role?: string
}

export interface IAuthResponse {
  user: IUser
  token: string
  token_type: string
}

export function useLoginMutation() {
  const { apiFetch, setToken } = useApiFetch()
  const queryClient = useQueryClient()

  return useMutation<IAuthResponse, Error, ILoginRequest>({
    mutationKey: ['useLoginMutation'],
    mutationFn: async (data: ILoginRequest) => {
      const response = await apiFetch<IAuthResponse>('/login', {
        method: 'post',
        body: data,
      })
      console.log('Login response:', response)
      if (response.token) {
        setToken(response.token)
        console.log('Token set:', response.token)
      }
      return response
    },
    onSuccess: () => {
      queryClient.invalidateQueries({ queryKey: ['useUserQuery'] })
    },
  })
}

export function useRegisterMutation() {
  const { apiFetch, setToken } = useApiFetch()
  const queryClient = useQueryClient()

  return useMutation<IAuthResponse, Error, IRegisterRequest>({
    mutationKey: ['useRegisterMutation'],
    mutationFn: async (data: IRegisterRequest) => {
      const response = await apiFetch<IAuthResponse>('/register', {
        method: 'post',
        body: data,
      })
      if (response.token) {
        setToken(response.token)
      }
      return response
    },
    onSuccess: () => {
      queryClient.invalidateQueries({ queryKey: ['useUserQuery'] })
    },
  })
}

export function useLogoutMutation() {
  const { apiFetch, clearToken } = useApiFetch()
  const queryClient = useQueryClient()

  return useMutation<IResponse<string>, Error>({
    mutationKey: ['useLogoutMutation'],
    mutationFn: async () => {
      const response = await apiFetch<IResponse<string>>('/logout', {
        method: 'post',
      })
      return response
    },
    onSuccess: () => {
      clearToken()
      queryClient.clear()
    },
  })
}

export function useUserQuery(enabled = true) {
  const { apiFetch, getToken } = useApiFetch()

  return useQuery<IResponse<IUser>, Error>({
    queryKey: ['useUserQuery'],
    queryFn: async () => await apiFetch<IResponse<IUser>>('/user', {
      method: 'get',
    }),
    enabled: enabled && !!getToken(),
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

  return useMutation<IResponse<{ message: string; avatar: string; avatar_url: string; data: IUser }>, Error, File>({
    mutationKey: ['useUploadAvatarMutation'],
    mutationFn: async (file: File) => {
      const formData = new FormData()
      formData.append('avatar', file)
      return apiFetch<IResponse<{ message: string; avatar: string; avatar_url: string; data: IUser }>>('/profile/avatar', { method: 'post', body: formData })
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
