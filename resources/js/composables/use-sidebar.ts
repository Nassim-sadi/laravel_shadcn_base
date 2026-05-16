import { computed } from 'vue'
import { BriefcaseIcon, FolderKanbanIcon, HelpCircleIcon, ImageIcon, LanguagesIcon, LayoutDashboardIcon, MailIcon, FileTextIcon, ScrollTextIcon, SettingsIcon, ShieldCheckIcon, StarIcon, UserIcon, UsersIcon } from '@lucide/vue'

import type { NavGroup } from '@/components/app-sidebar/types'

const activeModules: string[] = (window as any).activeModules ?? []

function isEnabled(module: string): boolean {
  return activeModules.includes(module)
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
      title: 'admin.nav.group.main',
      items: [
        navItem('admin.nav.dashboard', '/admin', LayoutDashboardIcon),
        navItem('admin.nav.users', '/admin/users', UsersIcon),
        navItem('admin.nav.roles', '/admin/roles', ShieldCheckIcon),
        navItem('admin.nav.permissions', '/admin/permissions', ScrollTextIcon),
      ],
    },
    {
      title: 'admin.nav.group.content',
      items: [
        navItem('admin.nav.services', '/admin/services', BriefcaseIcon),
        navItem('admin.nav.projects', '/admin/projects', FolderKanbanIcon),
        navItem('admin.nav.testimonials', '/admin/testimonials', StarIcon),
        navItem('admin.nav.faqs', '/admin/faqs', HelpCircleIcon),
        ...(isEnabled('catalog') ? [navItem('admin.nav.catalog', '/admin/catalog', BriefcaseIcon)] : []),
        ...(isEnabled('blog') ? [navItem('admin.nav.blog', '/admin/blog', FileTextIcon)] : []),
        ...(isEnabled('booking') ? [navItem('admin.nav.bookings', '/admin/bookings', FileTextIcon)] : []),
        navItem('admin.nav.media', '/admin/media', ImageIcon),
      ],
    },
    {
      title: 'admin.nav.group.communication',
      items: [
        navItem('admin.nav.contactMessages', '/admin/contact-messages', MailIcon),
        navItem('admin.nav.emailTemplates', '/admin/email-templates', FileTextIcon),
      ],
    },
    {
      title: 'admin.nav.group.system',
      items: [
        navItem('admin.nav.activityLogs', '/admin/activity-logs', ScrollTextIcon),
        navItem('admin.nav.translations', '/admin/translations', LanguagesIcon),
        navItem('admin.nav.settings', '/admin/settings', SettingsIcon),
      ],
    },
  ])

  return {
    navData,
    settingsNavItems: ref(settingsNavItems),
    otherPages: ref([]),
  }
}
