import { createRouter, createWebHistory } from 'vue-router'

import { setupRouterGuard } from './guard'

const router = createRouter({
  history: createWebHistory(),
  routes: [
    { path: '/', redirect: '/admin' },
    { path: '/auth/login', component: () => import('../pages/auth/sign-in.vue') },
    { path: '/auth/register', component: () => import('../pages/auth/sign-up.vue') },
    {
      path: '/admin',
      component: () => import('../layouts/default.vue'),
      children: [
        { path: '', component: () => import('../pages/admin/dashboard/index.vue') },
        { path: 'users', component: () => import('../pages/admin/users/index.vue') },
        { path: 'roles', component: () => import('../pages/admin/roles/index.vue') },
        { path: 'permissions', component: () => import('../pages/admin/permissions/index.vue') },
        { path: 'activity-logs', component: () => import('../pages/admin/activity-logs/index.vue') },
        { path: 'settings', redirect: { path: '/admin/settings/account' } },
        { path: 'settings/account', component: () => import('../pages/settings/components/account-form.vue') },
        { path: 'translations', component: () => import('../pages/admin/translations/index.vue') },
        { path: 'services', component: () => import('../pages/admin/services/index.vue') },
        { path: 'projects', component: () => import('../pages/admin/projects/index.vue') },
        { path: 'testimonials', component: () => import('../pages/admin/testimonials/index.vue') },
        { path: 'faqs', component: () => import('../pages/admin/faqs/index.vue') },
        { path: 'contact-messages', component: () => import('../pages/admin/contact-messages/index.vue') },
        { path: 'email-templates', component: () => import('../pages/admin/email-templates/index.vue') },
        { path: 'media', component: () => import('../admin/views/media/Index.vue') },
      ],
    },
  ],
  scrollBehavior() {
    return { left: 0, top: 0, behavior: 'smooth' }
  },
})

setupRouterGuard(router)

export default router
