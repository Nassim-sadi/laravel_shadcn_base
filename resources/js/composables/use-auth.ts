import { storeToRefs } from 'pinia'
import { useRouter } from 'vue-router'

import { useLoginMutation, useLogoutMutation, useUserQuery } from '@/services/api/auth.api'
import { useAuthStore } from '@/stores/auth'

export function useAuth() {
  const router = useRouter()

  const authStore = useAuthStore()
  const { user, isLogin } = storeToRefs(authStore)

  const { data: userData, refetch: refetchUser } = useUserQuery()

  watch(userData, (newUser) => {
    if (newUser?.data) {
      authStore.setUser(newUser.data)
    }
  }, { immediate: true })

  const loginMutation = useLoginMutation()
  const logoutMutation = useLogoutMutation()

  const loading = computed(() => loginMutation.isPending.value || logoutMutation.isPending.value)

  async function logout() {
    await logoutMutation.mutateAsync()
    authStore.clearUser()
    router.push({ path: '/auth/sign-in' })
  }

  function toHome() {
    router.push({ path: '/adminDashboard' })
  }

  async function login(credentials: { email: string, password: string }) {
    const response = await loginMutation.mutateAsync(credentials)
    authStore.setUser(response.user)
    await refetchUser()

    const redirect = router.currentRoute.value.query.redirect as string
    if (!redirect || redirect.startsWith('//')) {
      toHome()
    }
    else {
      router.push(redirect)
    }
  }

  return {
    user,
    isLogin,
    loading,
    logout,
    login,
  }
}
