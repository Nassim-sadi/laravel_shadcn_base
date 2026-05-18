import { describe, expect, it } from 'vitest'

import { cn, valueUpdater } from '@/lib/utils'

describe('cn', () => {
  it('merges class names', () => {
    expect(cn('px-4', 'py-2')).toBe('px-4 py-2')
  })

  it('handles conditional classes', () => {
    expect(cn('base', false && 'hidden', 'visible')).toBe('base visible')
  })

  it('handles tailwind conflict resolution', () => {
    expect(cn('px-4', 'px-6')).toBe('px-6')
  })

  it('handles empty input', () => {
    expect(cn()).toBe('')
  })
})

describe('valueUpdater', () => {
  it('updates ref with direct value', () => {
    const ref = { value: 1 }
    valueUpdater(5, ref as any)
    expect(ref.value).toBe(5)
  })

  it('updates ref with updater function', () => {
    const ref = { value: 1 }
    valueUpdater((old: number) => old + 1, ref as any)
    expect(ref.value).toBe(2)
  })
})
