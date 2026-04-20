import { h } from 'vue'
import { toast } from 'vue-sonner'

import { EnvSchema } from '@/validators/env.validator'

const { data: env, error } = EnvSchema.safeParse(import.meta.env)

if (error) {
  console.error('❌ Invalid env')
  const flattenError = error.flatten()
  console.error(flattenError)

  setTimeout(() => {
    toast.error(`Env error: you should check your .env file`, {
      description: h(
        'pre',
        { class: 'mt-2 rounded-md bg-slate-950 p-4 text-wrap' },
        h('code', { class: 'text-white' }, JSON.stringify(flattenError, null, 2)),
      ),
      duration: 10000,
    })
  }, 1000)
}

export default env!
