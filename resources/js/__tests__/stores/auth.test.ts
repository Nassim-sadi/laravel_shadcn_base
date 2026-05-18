import { createPinia, setActivePinia } from 'pinia'
import { describe, expect, it, beforeEach, vi } from 'vitest'

vi.mock('@/plugins/i18n', () => ({
  languageMetadata: { value: [{ code: 'fr', name: 'Français', flag: '🇫🇷', direction: 'ltr' }] },
  appLocale: { value: 'fr' },
  SUPPORTED_LOCALES: new Set(['fr', 'en', 'ar']),
  DEFAULT_LOCALE: 'fr',
  FALLBACK_LOCALE: 'fr',
}))

import { useAuthStore } from '@/stores/auth'

const mockUser = {
  id: 1,
  name: 'Admin',
  email: 'admin@test.com',
  role: 'super_admin',
  is_active: true,
  locale: 'en',
  avatar: null,
  email_verified_at: null,
  roles: ['super_admin'],
  permissions: ['users.view', 'users.create', 'blogs.view'],
}

describe('useAuthStore', () => {
  beforeEach(() => {
    setActivePinia(createPinia())
  })

  it('starts with null user', () => {
    const store = useAuthStore()
    expect(store.user).toBeNull()
    expect(store.isAuthenticated).toBe(false)
    expect(store.isLogin).toBe(false)
  })

  it('setUser updates user state', () => {
    const store = useAuthStore()
    store.setUser(mockUser)

    expect(store.user).toEqual(mockUser)
    expect(store.isAuthenticated).toBe(true)
    expect(store.isLogin).toBe(true)
  })

  it('clearUser resets user to null', () => {
    const store = useAuthStore()
    store.setUser(mockUser)
    store.clearUser()

    expect(store.user).toBeNull()
    expect(store.isAuthenticated).toBe(false)
  })

  it('hasRole returns true for assigned roles', () => {
    const store = useAuthStore()
    store.setUser(mockUser)

    expect(store.hasRole('super_admin')).toBe(true)
    expect(store.hasRole('admin')).toBe(false)
  })

  it('hasPermission returns true for assigned permissions', () => {
    const store = useAuthStore()
    store.setUser(mockUser)

    expect(store.hasPermission('users.view')).toBe(true)
    expect(store.hasPermission('blogs.view')).toBe(true)
    expect(store.hasPermission('settings.edit')).toBe(false)
  })

  it('returns empty roles and permissions when no user', () => {
    const store = useAuthStore()

    expect(store.roles).toEqual([])
    expect(store.permissions).toEqual([])
    expect(store.hasRole('super_admin')).toBe(false)
    expect(store.hasPermission('anything')).toBe(false)
  })
})
