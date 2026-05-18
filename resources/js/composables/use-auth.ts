import { storeToRefs } from 'pinia'
import { useRoute, useRouter } from 'vue-router'

import { useLoginMutation, useLogoutMutation, useUserQuery } from '@/services/api/auth.api'
import { useAuthStore } from '@/stores/auth'

export function useAuth() {
  const route = useRoute()
  const router = useRouter()

  const authStore = useAuthStore()
  const { user, isLogin } = storeToRefs(authStore)

  const isAuthPage = computed(() => route.path.startsWith('/auth/'))

  const { data: userData, refetch: refetchUser } = useUserQuery(!isAuthPage.value)

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
    router.push({ path: '/auth/login' })
  }

  function toHome() {
    router.push({ path: '/admin' })
  }

  async function login(credentials: { email: string, password: string }) {
    const userData = await loginMutation.mutateAsync(credentials)
    authStore.setUser(userData)
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
