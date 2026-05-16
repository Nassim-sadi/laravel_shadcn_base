import { BriefcaseIcon, FolderKanbanIcon, HelpCircleIcon, ImageIcon, LanguagesIcon, LayoutDashboardIcon, MailIcon, FileTextIcon, ScrollTextIcon, SettingsIcon, ShieldCheckIcon, StarIcon, UserIcon, UsersIcon } from '@lucide/vue'

import type { NavGroup } from '@/components/app-sidebar/types'

export function useSidebar() {
  const settingsNavItems = [
    { title: 'Profile', url: '/admin/settings', icon: UserIcon },
    { title: 'Account', url: '/admin/settings/account', icon: UserIcon },
  ]

  const navData: NavGroup[] = [
    {
      title: 'Main',
      items: [
        { title: 'Dashboard', url: '/admin', icon: LayoutDashboardIcon },
        { title: 'Users', url: '/admin/users', icon: UsersIcon },
        { title: 'Roles', url: '/admin/roles', icon: ShieldCheckIcon },
        { title: 'Permissions', url: '/admin/permissions', icon: ScrollTextIcon },
      ],
    },
    {
      title: 'Content',
      items: [
        { title: 'Services', url: '/admin/services', icon: BriefcaseIcon },
        { title: 'Projects', url: '/admin/projects', icon: FolderKanbanIcon },
        { title: 'Testimonials', url: '/admin/testimonials', icon: StarIcon },
        { title: 'FAQs', url: '/admin/faqs', icon: HelpCircleIcon },
        { title: 'Media', url: '/admin/media', icon: ImageIcon },
      ],
    },
    {
      title: 'Communication',
      items: [
        { title: 'Contact Messages', url: '/admin/contact-messages', icon: MailIcon },
        { title: 'Email Templates', url: '/admin/email-templates', icon: FileTextIcon },
      ],
    },
    {
      title: 'System',
      items: [
        { title: 'Activity Logs', url: '/admin/activity-logs', icon: ScrollTextIcon },
        { title: 'Translations', url: '/admin/translations', icon: LanguagesIcon },
        { title: 'Settings', url: '/admin/settings', icon: SettingsIcon },
      ],
    },
  ]

  return {
    navData: ref(navData),
    settingsNavItems: ref(settingsNavItems),
    otherPages: ref([]),
  }
}
