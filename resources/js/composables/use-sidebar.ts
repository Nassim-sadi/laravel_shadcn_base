import { CreditCardIcon, LayoutDashboardIcon, ScrollTextIcon, SettingsIcon, ShieldCheckIcon, UserIcon, UsersIcon } from '@lucide/vue'

import type { NavGroup } from '@/components/app-sidebar/types'

export function useSidebar() {
  const settingsNavItems = [
    { title: 'Profile', url: '/adminDashboard/settings', icon: UserIcon },
    { title: 'Account', url: '/adminDashboard/settings/account', icon: UserIcon },
    { title: 'Appearance', url: '/adminDashboard/settings/appearance', icon: UserIcon },
    { title: 'Notifications', url: '/adminDashboard/settings/notifications', icon: UserIcon },
    { title: 'Display', url: '/adminDashboard/settings/display', icon: UserIcon },
  ]

  const navData: NavGroup[] = [
    {
      title: 'Main',
      items: [
        { title: 'Dashboard', url: '/adminDashboard', icon: LayoutDashboardIcon },
        { title: 'Users', url: '/adminDashboard/users', icon: UsersIcon },
        { title: 'Roles', url: '/adminDashboard/roles', icon: ShieldCheckIcon },
        { title: 'Permissions', url: '/adminDashboard/permissions', icon: ScrollTextIcon },
      ],
    },
    {
      title: 'Settings',
      items: [
        { title: 'Billing', url: '/adminDashboard/billing', icon: CreditCardIcon },
        { title: 'Settings', url: '/adminDashboard/settings', icon: SettingsIcon },
      ],
    },
  ]

  const otherPages: NavGroup[] = [
    {
      title: 'Other',
      items: [
        { title: 'Billing', url: '/adminDashboard/billing', icon: CreditCardIcon },
      ],
    },
  ]

  return {
    navData: ref(navData),
    otherPages: ref(otherPages),
    settingsNavItems: ref(settingsNavItems),
  }
}