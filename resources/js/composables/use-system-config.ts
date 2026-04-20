import type { ZodType } from 'zod'

import { toTypedSchema } from '@vee-validate/zod'
import { useStorage } from '@vueuse/core'
import { useForm } from 'vee-validate'
import { toast } from 'vue-sonner'

import { useCreateSystemMutation, useGetSystemConfigByKeyQuery, useUpdateSystemConfigByKeyMutation } from '@/services/api/example-system-config.api'

export function useSystemConfig<S extends ZodType<any>>({
  key,
  defaultValue,
  description,
  schema,
}: {
  key: string
  defaultValue: Readonly<any>
  description: string
  schema: S
}) {
  const initialConfig = { ...defaultValue } as any
  const formSchema = toTypedSchema(schema)

  const { handleSubmit, resetForm } = useForm({
    validationSchema: formSchema,
    initialValues: initialConfig,
  })

  const localCacheConfig = useStorage<any>(key, initialConfig)

  const { data: systemConfigData, isPending: isGetSystemConfigByKeyQueryPending } = useGetSystemConfigByKeyQuery(key)
  const { mutate: createSystemConfigMutate, isPending: isCreateSystemConfigPending } = useCreateSystemMutation()
  const { mutate: updateSystemConfigMutate, isPending: isUpdateSystemConfigPending } = useUpdateSystemConfigByKeyMutation(key)
  const isPending = computed(() => isCreateSystemConfigPending.value || isUpdateSystemConfigPending.value)

  watch(systemConfigData, () => {
    if (!isGetSystemConfigByKeyQueryPending.value && !systemConfigData.value) {
      localCacheConfig.value = systemConfigData.value
      createSystemConfigMutate({
        key,
        description,
        value: JSON.stringify(defaultValue),
      }, {
        onSuccess: () => {
          localCacheConfig.value = initialConfig
          toast('System config created with default value.', {
            description: h('pre', { class: 'mt-2 w-[340px] rounded-md bg-slate-950 p-4' }, h('code', { class: 'text-white' }, JSON.stringify({ key, description, value: defaultValue }, null, 2))),
          })
        },
      })
      return
    }

    const configValue: any = systemConfigData.value?.data?.value
      ? JSON.parse(systemConfigData.value.data.value)
      : initialConfig
    localCacheConfig.value = configValue
    resetForm({
      values: { ...configValue },
    })
  }, { immediate: true, deep: true })

  const onSubmit = handleSubmit((values) => {
    const config = {
      key,
      value: values,
      description,
    }
    // 1. local cache
    localCacheConfig.value = values as any

    // 2. sync to server
    updateSystemConfigMutate({
      ...config,
      value: JSON.stringify(values),
    }, {
      onSuccess: () => {
        toast('You submitted the following values:', {
          description: h('pre', { class: 'mt-2 w-[340px] rounded-md bg-slate-950 p-4' }, h('code', { class: 'text-white' }, JSON.stringify(config, null, 2))),
        })
      },
    })
  })

  return {
    isPending,
    isGetting: isGetSystemConfigByKeyQueryPending,
    onSubmit,
  }
}
