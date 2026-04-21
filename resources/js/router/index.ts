import { createRouter, createWebHistory } from 'vue-router'

import { setupRouterGuard } from './guard'

const router = createRouter({
  history: createWebHistory(),
  routes: [
    { path: '/', component: () => import('../pages/index.vue') },
    { path: '/auth/sign-in', component: () => import('../pages/auth/sign-in.vue') },
    { path: '/auth/sign-up', component: () => import('../pages/auth/sign-up.vue') },
    {
      path: '/admin',
      component: () => import('../layouts/default.vue'),
      children: [
        { path: '', component: () => import('../pages/admin/dashboard/index.vue') },
        { path: 'users', component: () => import('../pages/users/index.vue') },
        { path: 'roles', component: () => import('../pages/admin/roles/index.vue') },
        { path: 'permissions', component: () => import('../pages/admin/permissions/index.vue') },
        { path: 'activity-logs', component: () => import('../pages/admin/activity-logs/index.vue') },
        { path: 'settings', component: () => import('../pages/settings/index.vue') },
        { path: 'settings/account', component: () => import('../pages/settings/account.vue') },
        { path: 'settings/appearance', component: () => import('../pages/settings/appearance.vue') },
        { path: 'settings/display', component: () => import('../pages/settings/display.vue') },
        { path: 'settings/notifications', component: () => import('../pages/settings/notifications.vue') },
        { path: 'billing', component: () => import('../pages/billing/index.vue') },
        { path: 'tasks', component: () => import('../pages/tasks/index.vue') },
        { path: 'help-center', component: () => import('../pages/help-center.vue') },
      ],
    },
    { path: '/:pathMatch(.*)*', component: () => import('../pages/errors/404.vue') },
  ],
  scrollBehavior() {
    return { left: 0, top: 0, behavior: 'smooth' }
  },
})

setupRouterGuard(router)

export default router