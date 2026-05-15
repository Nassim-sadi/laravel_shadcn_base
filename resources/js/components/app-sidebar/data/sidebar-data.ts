import { FolderIcon } from '@lucide/vue'

import { useSidebar } from '@/composables/use-sidebar'
import { useAuthStore } from '@/stores/auth'

import type { SidebarData, Team, User } from '../types'

const authStore = useAuthStore()
const currentUser = authStore.user

const user: User = {
  name: currentUser?.name ?? 'Admin',
  email: currentUser?.email ?? 'admin@test.com',
  avatar: currentUser?.avatar_url ?? '',
}

const teams: Team[] = [
  {
    name: 'Admin',
    logo: FolderIcon,
    plan: 'Admin',
  },
]

const { navData } = useSidebar()

export const sidebarData: SidebarData = {
  user,
  teams,
  navMain: navData.value!,
}
