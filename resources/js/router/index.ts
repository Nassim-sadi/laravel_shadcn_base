import { createRouter, createWebHistory } from 'vue-router'

import { setupRouterGuard } from './guard'

const router = createRouter({
  history: createWebHistory(),
  routes: [
    { path: '/', component: () => import('../pages/index.vue') },
    { path: '/auth/login', component: () => import('../pages/auth/sign-in.vue') },
    { path: '/auth/register', component: () => import('../pages/auth/sign-up.vue') },
    {
      path: '/admin',
      component: () => import('../layouts/default.vue'),
      children: [
        { path: '', component: () => import('../pages/admin/dashboard/index.vue') },
        { path: 'users', component: () => import('../pages/users/index.vue') },
        { path: 'roles', component: () => import('../pages/admin/roles/index.vue') },
        { path: 'permissions', component: () => import('../pages/admin/permissions/index.vue') },
        { path: 'activity-logs', component: () => import('../pages/admin/activity-logs/index.vue') },
        { path: 'settings', component: () => import('../pages/settings/components/account-form.vue') },
      ],
    },
  ],
  scrollBehavior() {
    return { left: 0, top: 0, behavior: 'smooth' }
  },
})

setupRouterGuard(router)

export default router