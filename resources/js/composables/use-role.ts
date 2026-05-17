import { useAuthStore } from '@/stores/auth'

export function hasRole(role: string): boolean {
  return useAuthStore().hasRole(role)
}

export function hasPermission(permission: string): boolean {
  return useAuthStore().hasPermission(permission)
}

export function canAnyPermission(permissions: string[]): boolean {
  return permissions.some(p => hasPermission(p))
}

export function canAllPermissions(permissions: string[]): boolean {
  return permissions.every(p => hasPermission(p))
}

export function isSuperAdmin(): boolean {
  return hasRole('super_admin')
}

export function isAdmin(): boolean {
  return hasRole('super_admin') || hasRole('admin')
}
