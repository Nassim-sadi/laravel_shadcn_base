import type { Directive } from 'vue'

import { hasPermission as checkPermission } from '@/composables/use-role'

export const vPermission: Directive = {
  mounted(el, binding) {
    const permission = binding.value

    if (!checkPermission(permission)) {
      el.style.display = 'none'
    }
  },

  updated(el, binding) {
    const permission = binding.value

    if (!checkPermission(permission)) {
      el.style.display = 'none'
    }
    else {
      el.style.display = ''
    }
  },
}
