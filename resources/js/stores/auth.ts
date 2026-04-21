import { defineStore } from 'pinia'

import type { IUser } from '@/services/api/auth.api'

export const useAuthStore = defineStore('auth', () => {
  const user = ref<IUser | null>(null)
  const isAuthenticated = computed(() => !!user.value)
  const isLogin = computed(() => !!user.value)

  const roles = computed(() => user.value?.roles ?? [])
  const permissions = computed(() => user.value?.permissions ?? [])

  function hasRole(role: string): boolean {
    return roles.value.includes(role)
  }

  function hasPermission(permission: string): boolean {
    return permissions.value.includes(permission)
  }

  const setUser = (newUser: IUser | null) => {
    user.value = newUser
  }

  const clearUser = () => {
    user.value = null
  }

  return {
    user,
    isAuthenticated,
    isLogin,
    roles,
    permissions,
    hasRole,
    hasPermission,
    setUser,
    clearUser,
  }
}, {
  persist: true,
})
