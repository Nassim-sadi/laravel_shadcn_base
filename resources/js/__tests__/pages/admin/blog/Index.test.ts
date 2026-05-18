import { mount } from '@vue/test-utils'
import { createPinia, setActivePinia } from 'pinia'
import { describe, expect, it, beforeEach, vi } from 'vitest'

vi.mock('vue-i18n', () => ({
  useI18n: () => ({
    t: (key: string) => key,
    locale: { value: 'fr' },
    fallbackLocale: 'fr',
    messages: { value: {} },
  }),
}))

vi.mock('@/plugins/i18n', () => ({
  languageMetadata: { value: [
    { code: 'fr', name: 'Français', flag: '🇫🇷', direction: 'ltr' },
    { code: 'en', name: 'English', flag: '🇬🇧', direction: 'ltr' },
    { code: 'ar', name: 'العربية', flag: '🇩🇿', direction: 'rtl' },
  ]},
  appLocale: { value: 'fr' },
  SUPPORTED_LOCALES: new Set(['fr', 'en', 'ar']),
  DEFAULT_LOCALE: 'fr',
  FALLBACK_LOCALE: 'fr',
  default: {},
}))

vi.mock('@/services/api/blog-posts.api', () => ({
  useGetBlogPostsQuery: vi.fn(),
  useDeleteBlogPostMutation: vi.fn(() => ({
    mutate: vi.fn(),
    isPending: false,
  })),
}))

import { useAuthStore } from '@/stores/auth'
import { useGetBlogPostsQuery } from '@/services/api/blog-posts.api'
import BlogPostIndex from '@/pages/admin/blog/Index.vue'

function createWrapper(options: { posts?: any[] } = {}) {
  const posts = options.posts ?? []

  vi.mocked(useGetBlogPostsQuery).mockReturnValue({
    data: { value: { data: { data: posts } } },
    isLoading: false,
    refetch: vi.fn(),
  } as any)

  setActivePinia(createPinia())

  const authStore = useAuthStore()
  authStore.setUser({
    id: 1,
    name: 'Admin',
    email: 'admin@test.com',
    role: 'super_admin',
    is_active: true,
    locale: 'en',
    avatar: null,
    email_verified_at: null,
    roles: ['super_admin'],
    permissions: ['blogs.view', 'blogs.create', 'blogs.edit', 'blogs.delete'],
  })

  return mount(BlogPostIndex, {
    global: {
      stubs: {
        BasicPage: { template: '<div><slot name="actions" /><slot /></div>' },
        Badge: { template: '<span><slot /></span>' },
        Button: { template: '<button><slot /></button>' },
        Tooltip: { template: '<div><slot /></div>' },
        TooltipContent: { template: '<span><slot /></span>' },
        TooltipProvider: { template: '<div><slot /></div>' },
        TooltipTrigger: { template: '<span><slot /></span>' },
        ConfirmDialog: { template: '<div />' },
        Form: { template: '<div />' },
        PencilIcon: { template: '<span>edit</span>' },
        Trash2Icon: { template: '<span>delete</span>' },
      },
      mocks: {
        $t: (key: string) => key,
      },
    },
  })
}

describe('BlogPostIndex', () => {
  beforeEach(() => {
    vi.clearAllMocks()
  })

  it('renders the create button when user has permission', () => {
    const wrapper = createWrapper()
    expect(wrapper.text()).toContain('admin.sheet.createBlogPost')
  })

  it('renders list of blog posts', () => {
    const wrapper = createWrapper({
      posts: [
        { id: 1, title: { fr: 'Premier article' }, slug: 'premier-article', is_published: true, featured: false, category: null, tags: [] },
        { id: 2, title: { fr: 'Deuxième article' }, slug: 'deuxieme-article', is_published: false, featured: true, category: null, tags: [] },
      ],
    })

    expect(wrapper.text()).toContain('Premier article')
    expect(wrapper.text()).toContain('Deuxième article')
  })

  it('shows published badge for published posts', () => {
    const wrapper = createWrapper({
      posts: [
        { id: 1, title: { fr: 'Publié' }, slug: 'publie', is_published: true, featured: false, category: null, tags: [] },
      ],
    })

    expect(wrapper.text()).toContain('admin.status.active')
  })

  it('shows empty state when no posts', () => {
    const wrapper = createWrapper({ posts: [] })
    expect(wrapper.text()).toContain('admin.empty.blogPosts')
  })
})
