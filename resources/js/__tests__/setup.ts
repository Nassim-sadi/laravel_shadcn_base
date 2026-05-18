import { computed, ref, watch } from 'vue'

;(globalThis as Record<string, unknown>).ref = ref
;(globalThis as Record<string, unknown>).computed = computed
;(globalThis as Record<string, unknown>).watch = watch
