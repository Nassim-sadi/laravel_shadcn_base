import { createPinia, setActivePinia } from 'pinia'
import { describe, expect, it, beforeEach, vi } from 'vitest'

vi.mock('@/plugins/i18n', () => ({
  languageMetadata: { value: [{ code: 'fr', name: 'Français', flag: '🇫🇷', direction: 'ltr' }] },
  appLocale: { value: 'fr' },
  SUPPORTED_LOCALES: new Set(['fr', 'en', 'ar']),
  DEFAULT_LOCALE: 'fr',
  FALLBACK_LOCALE: 'fr',
}))

import { canAllPermissions, canAnyPermission, hasPermission, hasRole, isAdmin, isSuperAdmin } from '@/composables/use-role'
import { useAuthStore } from '@/stores/auth'

function setTestUser(roles: string[], permissions: string[]) {
  const store = useAuthStore()
  store.setUser({
    id: 1,
    name: 'Test',
    email: 'test@test.com',
    role: 'user',
    is_active: true,
    locale: 'en',
    avatar: null,
    email_verified_at: null,
    roles,
    permissions,
  })
}

describe('use-role', () => {
  beforeEach(() => {
    setActivePinia(createPinia())
  })

  describe('hasRole', () => {
    it('returns true when user has the role', () => {
      setTestUser(['super_admin'], [])
      expect(hasRole('super_admin')).toBe(true)
    })

    it('returns false when user lacks the role', () => {
      setTestUser(['user'], [])
      expect(hasRole('super_admin')).toBe(false)
    })
  })

  describe('hasPermission', () => {
    it('returns true when user has the permission', () => {
      setTestUser([], ['users.view'])
      expect(hasPermission('users.view')).toBe(true)
    })

    it('returns false when user lacks the permission', () => {
      setTestUser([], ['users.view'])
      expect(hasPermission('settings.edit')).toBe(false)
    })
  })

  describe('canAnyPermission', () => {
    it('returns true if user has any of the permissions', () => {
      setTestUser([], ['users.view'])
      expect(canAnyPermission(['users.view', 'settings.edit'])).toBe(true)
    })

    it('returns false if user has none', () => {
      setTestUser([], [])
      expect(canAnyPermission(['users.view', 'settings.edit'])).toBe(false)
    })
  })

  describe('canAllPermissions', () => {
    it('returns true if user has all permissions', () => {
      setTestUser([], ['users.view', 'settings.edit'])
      expect(canAllPermissions(['users.view', 'settings.edit'])).toBe(true)
    })

    it('returns false if user lacks any', () => {
      setTestUser([], ['users.view'])
      expect(canAllPermissions(['users.view', 'settings.edit'])).toBe(false)
    })
  })

  describe('isSuperAdmin', () => {
    it('returns true for super_admin', () => {
      setTestUser(['super_admin'], [])
      expect(isSuperAdmin()).toBe(true)
    })

    it('returns false for other roles', () => {
      setTestUser(['admin'], [])
      expect(isSuperAdmin()).toBe(false)
    })
  })

  describe('isAdmin', () => {
    it('returns true for super_admin', () => {
      setTestUser(['super_admin'], [])
      expect(isAdmin()).toBe(true)
    })

    it('returns true for admin', () => {
      setTestUser(['admin'], [])
      expect(isAdmin()).toBe(true)
    })

    it('returns false for user', () => {
      setTestUser(['user'], [])
      expect(isAdmin()).toBe(false)
    })
  })
})
