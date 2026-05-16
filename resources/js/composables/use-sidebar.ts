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
      title: 'admin.nav.group.content',
      items: [
        navItem('admin.nav.dashboard', '/admin', LayoutDashboardIcon),
        ...(isEnabled('services') ? [navItem('admin.nav.services', '/admin/services', BriefcaseIcon)] : []),
        ...(isEnabled('projects') ? [navItem('admin.nav.projects', '/admin/projects', FolderKanbanIcon)] : []),
        ...(isEnabled('testimonials') ? [navItem('admin.nav.testimonials', '/admin/testimonials', StarIcon)] : []),
        ...(isEnabled('faqs') ? [navItem('admin.nav.faqs', '/admin/faqs', HelpCircleIcon)] : []),
        ...(isEnabled('media') ? [navItem('admin.nav.media', '/admin/media', ImageIcon)] : []),
      ],
    },
    {
      title: 'admin.nav.group.management',
      items: [
        navItem('admin.nav.users', '/admin/users', UsersIcon),
        navItem('admin.nav.roles', '/admin/roles', ShieldCheckIcon),
      ],
    },
    {
      title: 'admin.nav.group.communication',
      items: [
        ...(isEnabled('contact') ? [navItem('admin.nav.contactMessages', '/admin/contact-messages', MailIcon)] : []),
        ...(isEnabled('email_templates') ? [navItem('admin.nav.emailTemplates', '/admin/email-templates', FileTextIcon)] : []),
      ],
    },
    {
      title: 'admin.nav.group.system',
      items: [
        ...(isEnabled('activity_logs') ? [navItem('admin.nav.activityLogs', '/admin/activity-logs', ScrollTextIcon)] : []),
        ...(isEnabled('translations') ? [navItem('admin.nav.translations', '/admin/translations', LanguagesIcon)] : []),
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
