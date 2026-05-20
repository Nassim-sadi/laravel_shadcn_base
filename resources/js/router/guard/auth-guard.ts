import type { Router } from 'vue-router'

import { $fetch } from 'ofetch'

import { hasPermission, hasRole } from '@/composables/use-role'
import { API_BASE_URL } from '@/constants/app-config'
import pinia from '@/plugins/pinia/setup'
import { useAuthStore } from '@/stores/auth'
import type { IUser } from '@/services/api/auth.api'

let authPromise: Promise<void> | null = null

async function initAuth(): Promise<void> {
  const authStore = useAuthStore(pinia)

  try {
    const response = await $fetch<{ data: IUser }>('/user', {
      baseURL: API_BASE_URL,
      credentials: 'include',
      headers: { Accept: 'application/json' },
    })
    authStore.setUser(response.data)
  }
  catch {
    authStore.clearUser()
    authPromise = null
  }
}

export function setupAuthGuard(router: Router) {
  router.beforeEach(async (to) => {
    if (!authPromise) {
      authPromise = initAuth()
    }
    await authPromise

    const authStore = useAuthStore(pinia)
    const isLoggedIn = !!authStore.user

    const isAuthPage = to.path.startsWith('/auth')
    const isVerifyPage = to.path === '/email/verify'
    const isLandingPage = to.path === '/'
    const isAdminPage = to.path.startsWith('/admin')

    if (isLandingPage) return true

    if (isAuthPage || isVerifyPage) {
      if (isLoggedIn && isAuthPage) return { path: '/admin' }
      return true
    }

    if (isAdminPage && !isLoggedIn) {
      return { path: '/auth/login', query: { redirect: to.fullPath } }
    }

    if (isLoggedIn && !authStore.user?.email_verified_at) {
      return { path: '/email/verify' }
    }

    const requiredRole = to.meta.requiredRole
    if (requiredRole && !hasRole(requiredRole)) return { path: '/admin' }

    const requiredPermission = to.meta.requiredPermission
    if (requiredPermission && !hasPermission(requiredPermission)) return { path: '/admin' }
  })
}
