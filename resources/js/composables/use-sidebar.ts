import { LayoutDashboardIcon, ScrollTextIcon, SettingsIcon, ShieldCheckIcon, UserIcon, UsersIcon } from '@lucide/vue'

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
      title: 'Settings',
      items: [
        { title: 'Settings', url: '/admin/settings', icon: SettingsIcon },
      ],
    },
  ]

  return {
    navData: ref(navData),
    settingsNavItems: ref(settingsNavItems),
  }
}