import type { Router } from 'vue-router'

import { storeToRefs } from 'pinia'

import { hasPermission, hasRole } from '@/composables/use-role'
import pinia from '@/plugins/pinia/setup'
import { useAuthStore } from '@/stores/auth'

export function setupAuthGuard(router: Router) {
  router.beforeEach((to) => {
    const authStore = useAuthStore(pinia)
    const { isLogin } = storeToRefs(authStore)

    const isAuthPage = to.path.startsWith('/auth')
    const isLandingPage = to.path === '/'
    const isAdminPage = to.path.startsWith('/admin')

    if (isLandingPage) {
      return true
    }

    if (isAuthPage) {
      if (isLogin.value) {
        return { path: '/admin' }
      }
      return true
    }

    if (isAdminPage && !isLogin.value) {
      return {
        path: '/auth/login',
        query: { redirect: to.fullPath },
      }
    }

    const requiredRole = to.meta.requiredRole as string | undefined
    if (requiredRole && !hasRole(requiredRole)) {
      return { path: '/admin' }
    }

    const requiredPermission = to.meta.requiredPermission as string | undefined
    if (requiredPermission && !hasPermission(requiredPermission)) {
      return { path: '/admin' }
    }
  })
}
