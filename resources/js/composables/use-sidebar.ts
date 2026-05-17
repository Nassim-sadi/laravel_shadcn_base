import { BriefcaseIcon, FileTextIcon, FolderKanbanIcon, HelpCircleIcon, ImageIcon, LanguagesIcon, LayoutDashboardIcon, MailIcon, ScrollTextIcon, SettingsIcon, ShieldCheckIcon, StarIcon, UserIcon, UsersIcon } from '@lucide/vue'
import { computed } from 'vue'

import { hasPermission, isSuperAdmin } from '@/composables/use-role'
import type { NavGroup } from '@/components/app-sidebar/types'

const activeModules: string[] = (window as any).activeModules ?? []

function isEnabled(module: string): boolean {
  return activeModules.includes(module)
}

function isVisible(module: string | null, permission: string): boolean {
  if (module && !isEnabled(module)) return false
  if (isSuperAdmin()) return true
  return hasPermission(permission)
}

function navItem(title: string, url: string, icon: any) {
  return { title, url, icon }
}

export function useSidebar() {
  const settingsNavItems = [
    { title: 'admin.nav.profile', url: '/admin/settings', icon: UserIcon },
    { title: 'admin.nav.account', url: '/admin/settings/account', icon: UserIcon },
  ]

  const navData = computed<NavGroup[]>(() => [
    {
      title: 'admin.nav.group.content',
      items: [
        navItem('admin.nav.dashboard', '/admin', LayoutDashboardIcon),
        ...(isVisible('services', 'services.view') ? [navItem('admin.nav.services', '/admin/services', BriefcaseIcon)] : []),
        ...(isVisible('projects', 'projects.view') ? [navItem('admin.nav.projects', '/admin/projects', FolderKanbanIcon)] : []),
        ...(isVisible('testimonials', 'testimonials.view') ? [navItem('admin.nav.testimonials', '/admin/testimonials', StarIcon)] : []),
        ...(isVisible('faqs', 'faqs.view') ? [navItem('admin.nav.faqs', '/admin/faqs', HelpCircleIcon)] : []),
        ...(isVisible('media', 'media.view') ? [navItem('admin.nav.media', '/admin/media', ImageIcon)] : []),
...(isVisible('blog', 'blogs.view') ? [navItem('admin.nav.blog', '/admin/blog', FileTextIcon)] : []),
      ],
    },
    {
      title: 'admin.nav.group.management',
      items: [
        ...(isVisible(null, 'users.view') ? [navItem('admin.nav.users', '/admin/users', UsersIcon)] : []),
        ...(isVisible(null, 'roles.view') ? [navItem('admin.nav.roles', '/admin/roles', ShieldCheckIcon)] : []),
      ],
    },
    {
      title: 'admin.nav.group.communication',
      items: [
        ...(isVisible('contact', 'contact-messages.view') ? [navItem('admin.nav.contactMessages', '/admin/contact-messages', MailIcon)] : []),
        ...(isVisible('email_templates', 'email-templates.view') ? [navItem('admin.nav.emailTemplates', '/admin/email-templates', FileTextIcon)] : []),
      ],
    },
    {
      title: 'admin.nav.group.system',
      items: [
        ...(isVisible('activity_logs', 'logs.view') ? [navItem('admin.nav.activityLogs', '/admin/activity-logs', ScrollTextIcon)] : []),
        ...(isVisible('translations', 'settings.view') ? [navItem('admin.nav.translations', '/admin/translations', LanguagesIcon)] : []),
        ...(isVisible(null, 'settings.view') ? [navItem('admin.nav.settings', '/admin/settings', SettingsIcon)] : []),
      ],
    },
  ])

  return {
    navData,
    settingsNavItems: ref(settingsNavItems),
    otherPages: ref([]),
  }
}
