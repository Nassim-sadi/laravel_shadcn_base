import { useMutation, useQuery, useQueryClient } from '@tanstack/vue-query'
import { toast } from 'vue-sonner'

import { useApiFetch } from '@/composables/use-fetch'

import type { IResponse } from '../types/response.type'
import type { IUser } from './auth.api'

export interface IUsersResponse {
  data: IUser[]
  current_page: number
  last_page: number
  per_page: number
  total: number
}

export interface ICreateUserRequest {
  name: string
  email: string
  password: string
  role?: string
  locale?: string
  is_active?: boolean
}

export interface IUpdateUserRequest {
  name?: string
  email?: string
  password?: string
  role?: string
  locale?: string
  is_active?: boolean
}

export interface IInviteUserRequest {
  email: string
  name: string
  role?: string
}

export function useGetUsersQuery() {
  const { apiFetch } = useApiFetch()

  return useQuery({
    queryKey: ['useGetUsersQuery'],
    queryFn: () => apiFetch('users', { method: 'get' }),
  })
}

export function useGetUserByIdQuery(id: number) {
  const { apiFetch } = useApiFetch()

  return useQuery<IResponse<IUser>, Error>({
    queryKey: ['useGetUserByIdQuery', id],
    queryFn: async () => apiFetch<IResponse<IUser>>(`/users/${id}`, { method: 'get' }),
  })
}

export function useCreateUserMutation() {
  const { apiFetch } = useApiFetch()
  const queryClient = useQueryClient()

  return useMutation<IResponse<IUser>, Error, ICreateUserRequest>({
    mutationKey: ['useCreateUserMutation'],
    mutationFn: async (data: ICreateUserRequest) => apiFetch<IResponse<IUser>>('/users', { method: 'post', body: data }),
    onSuccess: () => {
      queryClient.invalidateQueries({ queryKey: ['useGetUsersQuery'] })
      toast.success('User created')
    },
    onError: (error) => {
      toast.error(error?.message ?? 'Failed to create user')
    },
  })
}

export function useUpdateUserMutation(id: number) {
  const { apiFetch } = useApiFetch()
  const queryClient = useQueryClient()

  return useMutation<IResponse<IUser>, Error, IUpdateUserRequest>({
    mutationKey: ['useUpdateUserMutation', id],
    mutationFn: async (data: IUpdateUserRequest) => apiFetch<IResponse<IUser>>(`/users/${id}`, { method: 'put', body: data }),
    onSuccess: () => {
      queryClient.invalidateQueries({ queryKey: ['useGetUsersQuery'] })
      queryClient.invalidateQueries({ queryKey: ['useGetUserByIdQuery', id] })
      toast.success('User updated')
    },
    onError: (error) => {
      toast.error(error?.message ?? 'Failed to update user')
    },
  })
}

export function useDeleteUserMutation() {
  const { apiFetch } = useApiFetch()
  const queryClient = useQueryClient()

  return useMutation<IResponse<string>, Error, number>({
    mutationKey: ['useDeleteUserMutation'],
    mutationFn: async (id: number) => apiFetch<IResponse<string>>(`/users/${id}`, { method: 'delete' }),
    onSuccess: () => {
      queryClient.invalidateQueries({ queryKey: ['useGetUsersQuery'] })
      toast.success('User deleted')
    },
    onError: (error) => {
      toast.error(error?.message ?? 'Failed to delete user')
    },
  })
}

export function useInviteUserMutation() {
  const { apiFetch } = useApiFetch()
  const queryClient = useQueryClient()

  return useMutation<IResponse<{ message: string, temporary_password: string }>, Error, IInviteUserRequest>({
    mutationKey: ['useInviteUserMutation'],
    mutationFn: async (data: IInviteUserRequest) => apiFetch<IResponse<{ message: string, temporary_password: string }>>('/users/invite', { method: 'post', body: data }),
    onSuccess: () => {
      queryClient.invalidateQueries({ queryKey: ['useGetUsersQuery'] })
      toast.success('Invitation sent')
    },
    onError: (error) => {
      toast.error(error?.message ?? 'Failed to send invitation')
    },
  })
}

export function useAssignRoleMutation() {
  const { apiFetch } = useApiFetch()
  const queryClient = useQueryClient()

  return useMutation<IResponse<IUser>, Error, { userId: number, role: string }>({
    mutationKey: ['useAssignRoleMutation'],
    mutationFn: async ({ userId, role }) => apiFetch<IResponse<IUser>>(`/users/${userId}/assign-role`, { method: 'post', body: { role } }),
    onSuccess: () => {
      queryClient.invalidateQueries({ queryKey: ['useGetUsersQuery'] })
      toast.success('Role assigned')
    },
    onError: (error) => {
      toast.error(error?.message ?? 'Failed to assign role')
    },
  })
}

export function useGivePermissionMutation() {
  const { apiFetch } = useApiFetch()
  const queryClient = useQueryClient()

  return useMutation<IResponse<IUser>, Error, { userId: number, permission: string }>({
    mutationKey: ['useGivePermissionMutation'],
    mutationFn: async ({ userId, permission }) => apiFetch<IResponse<IUser>>(`/users/${userId}/give-permission`, { method: 'post', body: { permission } }),
    onSuccess: () => {
      queryClient.invalidateQueries({ queryKey: ['useGetUsersQuery'] })
      toast.success('Permission added')
    },
    onError: (error) => {
      toast.error(error?.message ?? 'Failed to add permission')
    },
  })
}

export function useRevokePermissionMutation() {
  const { apiFetch } = useApiFetch()
  const queryClient = useQueryClient()

  return useMutation<IResponse<IUser>, Error, { userId: number, permission: string }>({
    mutationKey: ['useRevokePermissionMutation'],
    mutationFn: async ({ userId, permission }) => apiFetch<IResponse<IUser>>(`/users/${userId}/revoke-permission`, { method: 'post', body: { permission } }),
    onSuccess: () => {
      queryClient.invalidateQueries({ queryKey: ['useGetUsersQuery'] })
      toast.success('Permission revoked')
    },
    onError: (error) => {
      toast.error(error?.message ?? 'Failed to revoke permission')
    },
  })
}

export function useUploadAvatarMutation() {
  const { apiFetch } = useApiFetch()
  const queryClient = useQueryClient()

  return useMutation<IResponse<{ message: string, avatar: string }>, Error, { userId: number, file: File }>({
    mutationKey: ['useUploadAvatarMutation'],
    mutationFn: async ({ userId, file }) => {
      const formData = new FormData()
      formData.append('avatar', file)
      return apiFetch<IResponse<{ message: string, avatar: string }>>(`/users/${userId}/avatar`, { method: 'post', body: formData })
    },
    onSuccess: () => {
      queryClient.invalidateQueries({ queryKey: ['useGetUserByIdQuery'] })
      toast.success('Avatar uploaded')
    },
    onError: (error) => {
      toast.error(error?.message ?? 'Failed to upload avatar')
    },
  })
}

export function useDeleteAvatarMutation() {
  const { apiFetch } = useApiFetch()
  const queryClient = useQueryClient()

  return useMutation<IResponse<string>, Error, number>({
    mutationKey: ['useDeleteAvatarMutation'],
    mutationFn: async (id: number) => apiFetch<IResponse<string>>(`/users/${id}/avatar`, { method: 'delete' }),
    onSuccess: () => {
      queryClient.invalidateQueries({ queryKey: ['useGetUserByIdQuery'] })
      toast.success('Avatar deleted')
    },
    onError: (error) => {
      toast.error(error?.message ?? 'Failed to delete avatar')
    },
  })
}
