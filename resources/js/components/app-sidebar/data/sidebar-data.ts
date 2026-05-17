import { FolderIcon } from '@lucide/vue'
import { shallowRef, watch } from 'vue'

import { useSidebar } from '@/composables/use-sidebar'
import { useAuthStore } from '@/stores/auth'

import type { NavGroup, SidebarData, Team, User } from '../types'

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

const cachedNav = shallowRef<NavGroup[]>(navData.value as NavGroup[])

watch(() => authStore.user, () => {
  cachedNav.value = navData.value as NavGroup[]
}, { immediate: true })

export const sidebarData: SidebarData = {
  user,
  teams,
  get navMain() {
    return cachedNav.value
  },
}
