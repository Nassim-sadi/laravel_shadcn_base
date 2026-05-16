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
    { title: 'Profile', url: '/admin/settings', icon: UserIcon },
    { title: 'Account', url: '/admin/settings/account', icon: UserIcon },
  ]

  const navData = computed<NavGroup[]>(() => [
    {
      title: 'Main',
      items: [
        navItem('Dashboard', '/admin', LayoutDashboardIcon),
        navItem('Users', '/admin/users', UsersIcon),
        navItem('Roles', '/admin/roles', ShieldCheckIcon),
        navItem('Permissions', '/admin/permissions', ScrollTextIcon),
      ],
    },
    {
      title: 'Content',
      items: [
        navItem('Services', '/admin/services', BriefcaseIcon),
        navItem('Projects', '/admin/projects', FolderKanbanIcon),
        navItem('Testimonials', '/admin/testimonials', StarIcon),
        navItem('FAQs', '/admin/faqs', HelpCircleIcon),
        ...(isEnabled('catalog') ? [navItem('Catalog', '/admin/catalog', BriefcaseIcon)] : []),
        ...(isEnabled('blog') ? [navItem('Blog', '/admin/blog', FileTextIcon)] : []),
        ...(isEnabled('booking') ? [navItem('Bookings', '/admin/bookings', FileTextIcon)] : []),
        navItem('Media', '/admin/media', ImageIcon),
      ],
    },
    {
      title: 'Communication',
      items: [
        navItem('Contact Messages', '/admin/contact-messages', MailIcon),
        navItem('Email Templates', '/admin/email-templates', FileTextIcon),
      ],
    },
    {
      title: 'System',
      items: [
        navItem('Activity Logs', '/admin/activity-logs', ScrollTextIcon),
        navItem('Translations', '/admin/translations', LanguagesIcon),
        navItem('Settings', '/admin/settings', SettingsIcon),
      ],
    },
  ])

  return {
    navData,
    settingsNavItems: ref(settingsNavItems),
    otherPages: ref([]),
  }
}
