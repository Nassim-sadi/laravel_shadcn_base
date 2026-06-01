import { createRouter, createWebHistory } from 'vue-router'

import { useModules } from '@/composables/use-modules'
import { setupRouterGuard } from './guard'

const { isEnabled } = useModules()

const router = createRouter({
  history: createWebHistory(),
  routes: [
    { path: '/', redirect: '/admin' },
    { path: '/auth/login', component: () => import('../pages/auth/sign-in.vue') },
    { path: '/auth/register', component: () => import('../pages/auth/sign-up.vue') },
    { path: '/auth/forgot-password', component: () => import('../pages/auth/forgot-password.vue') },
    { path: '/auth/reset-password', component: () => import('../pages/auth/reset-password.vue') },
    { path: '/email/verify', component: () => import('../pages/auth/verify-email.vue') },
    {
      path: '/admin',
      component: () => import('../layouts/default.vue'),
      children: [
        { path: '', component: () => import('../pages/admin/dashboard/index.vue') },
        { path: 'users', component: () => import('../pages/admin/users/index.vue'), meta: { requiredPermission: 'users.view' } },
        { path: 'roles', component: () => import('../pages/admin/roles/index.vue'), meta: { requiredPermission: 'roles.view' } },
        { path: 'settings', redirect: { path: '/admin/settings/account' } },
        { path: 'settings/account', component: () => import('../pages/settings/components/account-form.vue') },
        { path: 'settings/security', component: () => import('../pages/settings/components/two-factor-form.vue') },
        { path: 'settings/ai', component: () => import('../pages/settings/components/ai-settings-form.vue'), meta: { requiredPermission: 'settings.view' } },
        { path: 'translations', component: () => import('../pages/admin/translations/index.vue'), meta: { module: 'translations', requiredPermission: 'settings.view' } },
        { path: 'services', component: () => import('../pages/admin/services/index.vue'), meta: { module: 'services', requiredPermission: 'services.view' } },
        { path: 'projects', component: () => import('../pages/admin/projects/index.vue'), meta: { module: 'projects', requiredPermission: 'projects.view' } },
        { path: 'testimonials', component: () => import('../pages/admin/testimonials/index.vue'), meta: { module: 'testimonials', requiredPermission: 'testimonials.view' } },
        { path: 'faqs', component: () => import('../pages/admin/faqs/index.vue'), meta: { module: 'faqs', requiredPermission: 'faqs.view' } },
        { path: 'contact-messages', component: () => import('../pages/admin/contact-messages/index.vue'), meta: { module: 'contact', requiredPermission: 'contact-messages.view' } },
        { path: 'email-templates', component: () => import('../pages/admin/email-templates/index.vue'), meta: { module: 'email_templates', requiredPermission: 'email-templates.view' } },
        { path: 'media', component: () => import('../admin/views/media/Index.vue'), meta: { module: 'media', requiredPermission: 'media.view' } },
        { path: 'blog', component: () => import('../pages/admin/blog/Index.vue'), meta: { module: 'blog', requiredPermission: 'blogs.view' } },
        { path: 'activity-logs', component: () => import('../pages/admin/activity-logs/index.vue'), meta: { module: 'activity_logs', requiredPermission: 'logs.view' } },
        { path: 'catalog/products', component: () => import('../pages/admin/catalog/products/index.vue'), meta: { module: 'catalog', requiredPermission: 'catalog.view' } },
        { path: 'catalog/categories', component: () => import('../pages/admin/catalog/categories/index.vue'), meta: { module: 'catalog', requiredPermission: 'catalog.view' } },
        { path: 'catalog/tags', component: () => import('../pages/admin/catalog/tags/index.vue'), meta: { module: 'catalog', requiredPermission: 'catalog.view' } },
        { path: 'catalog/brands', component: () => import('../pages/admin/catalog/brands/index.vue'), meta: { module: 'catalog', requiredPermission: 'catalog.view' } },
        { path: 'catalog/marquee', component: () => import('../pages/admin/catalog/marquee/index.vue'), meta: { module: 'catalog', requiredPermission: 'catalog.view' } },
        { path: 'catalog/quotes', component: () => import('../pages/admin/catalog/quotes/index.vue'), meta: { module: 'catalog', requiredPermission: 'catalog.view' } },
        { path: 'booking-services', component: () => import('../pages/admin/booking-services/index.vue'), meta: { module: 'booking', requiredPermission: 'booking_services.view' } },
        { path: 'bookings', component: () => import('../pages/admin/bookings/index.vue'), meta: { module: 'booking', requiredPermission: 'booking.view' } },
        { path: 'bookings/calendar', component: () => import('../pages/admin/bookings/calendar.vue'), meta: { module: 'booking', requiredPermission: 'booking.view' } },
        { path: 'bookings/settings', component: () => import('../pages/admin/bookings/settings.vue'), meta: { module: 'booking', requiredPermission: 'settings.view' } },
        { path: ':pathMatch(.*)*', redirect: '/admin' },
      ],
    },
  ],
  scrollBehavior() {
    return { left: 0, top: 0, behavior: 'smooth' }
  },
})

setupRouterGuard(router)

export default router
