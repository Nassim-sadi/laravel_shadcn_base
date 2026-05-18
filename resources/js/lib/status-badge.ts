import { h } from 'vue'
import { Badge } from '@/components/ui/badge'

export function statusBadge(isActive: boolean, activeLabel: string, inactiveLabel: string) {
  if (isActive) {
    return h(Badge, { variant: 'default', class: 'bg-green-500/15 text-green-700 hover:bg-green-500/25 border border-green-200' }, () => activeLabel)
  }
  return h(Badge, { variant: 'secondary', class: 'bg-red-500/15 text-red-700 hover:bg-red-500/25 border border-red-200' }, () => inactiveLabel)
}

export function featuredBadge(isFeatured: boolean) {
  if (isFeatured) {
    return h(Badge, { variant: 'default', class: 'bg-amber-500/15 text-amber-700 hover:bg-amber-500/25 border border-amber-200' }, () => 'Yes')
  }
  return h(Badge, { variant: 'outline', class: 'text-gray-500' }, () => 'No')
}

export function readBadge(isRead: boolean) {
  if (isRead) {
    return h(Badge, { variant: 'secondary', class: 'bg-gray-500/15 text-gray-600 hover:bg-gray-500/25 border border-gray-200' }, () => 'Read')
  }
  return h(Badge, { variant: 'default', class: 'bg-blue-500/15 text-blue-700 hover:bg-blue-500/25 border border-blue-200' }, () => 'New')
}
