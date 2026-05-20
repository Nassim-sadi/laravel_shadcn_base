import { mount } from '@vue/test-utils'
import { beforeEach, describe, expect, it, vi } from 'vitest'

vi.mock('vue-sonner', () => ({
  toast: {
    success: vi.fn(),
    error: vi.fn(),
  },
}))

const mutateAsync = vi.fn()

vi.mock('@/services/api/ai-content.api', () => ({
  useGenerateAiContentMutation: () => ({
    mutateAsync,
    isPending: { value: false },
  }),
}))

import AiGenerator from '@/admin/components/ai/AiContentGeneratorDialog.vue'

describe('AiGenerator', () => {
  beforeEach(() => {
    vi.clearAllMocks()
  })

  it('renders generator controls for the sidecar flow', () => {
    const wrapper = mount(AiGenerator, {
      props: {
        open: true,
        module: 'services',
        locale: 'fr',
        source: {
          title: 'Existing title',
          description: '',
        },
      },
      global: {
        stubs: {
          Dialog: { template: '<div><slot /></div>' },
          DialogContent: { template: '<div><slot /></div>' },
          DialogDescription: { template: '<div><slot /></div>' },
          DialogFooter: { template: '<div><slot /></div>' },
          DialogHeader: { template: '<div><slot /></div>' },
          DialogTitle: { template: '<div><slot /></div>' },
          Button: { template: '<button><slot /></button>' },
          Checkbox: { template: '<input type="checkbox" />' },
          Input: { template: '<input />' },
          Label: { template: '<label><slot /></label>' },
          Textarea: { template: '<textarea />' },
          SparklesIcon: { template: '<span />' },
        },
      },
    })

    expect(wrapper.text()).toContain('Generate content')
    expect(wrapper.text()).toContain('Draft new')
    expect(wrapper.text()).toContain('Improve existing')
    expect(wrapper.text()).toContain('Cancel')
    expect(mutateAsync).not.toHaveBeenCalled()
  })
})
