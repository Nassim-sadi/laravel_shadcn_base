import { defineStore } from 'pinia'

import type { IUser } from '@/services/api/auth.api'

export const useAuthStore = defineStore('auth', () => {
  const user = ref<IUser | null>(null)
  const isAuthenticated = computed(() => !!user.value)
  const isLogin = computed(() => !!user.value)

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
    setUser,
    clearUser,
  }
}, {
  persist: true,
})
