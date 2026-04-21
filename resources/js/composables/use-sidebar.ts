import { CreditCardIcon, LayoutDashboardIcon, ScrollTextIcon, SettingsIcon, ShieldCheckIcon, UserIcon, UsersIcon } from '@lucide/vue'

import type { NavGroup } from '@/components/app-sidebar/types'

export function useSidebar() {
  const settingsNavItems = [
    { title: 'Profile', url: '/admin/settings', icon: UserIcon },
    { title: 'Account', url: '/admin/settings/account', icon: UserIcon },
    { title: 'Appearance', url: '/admin/settings/appearance', icon: UserIcon },
    { title: 'Notifications', url: '/admin/settings/notifications', icon: UserIcon },
    { title: 'Display', url: '/admin/settings/display', icon: UserIcon },
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
        { title: 'Billing', url: '/admin/billing', icon: CreditCardIcon },
        { title: 'Settings', url: '/admin/settings', icon: SettingsIcon },
      ],
    },
  ]

  const otherPages: NavGroup[] = [
    {
      title: 'Other',
      items: [
        { title: 'Billing', url: '/admin/billing', icon: CreditCardIcon },
      ],
    },
  ]

  return {
    navData: ref(navData),
    otherPages: ref(otherPages),
    settingsNavItems: ref(settingsNavItems),
  }
}