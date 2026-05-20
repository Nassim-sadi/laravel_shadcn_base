<script setup lang="ts">
import { computed, ref, watch } from 'vue'
import { toast } from 'vue-sonner'

import { Button } from '@/components/ui/button'
import { Input } from '@/components/ui/input'
import { Label } from '@/components/ui/label'
import { Separator } from '@/components/ui/separator'
import { useGetAiSettingsQuery, useUpdateAiSettingsMutation } from '@/services/api/ai-content.api'

const { data: response } = useGetAiSettingsQuery()
const { mutateAsync, isPending } = useUpdateAiSettingsMutation()

const settings = computed(() => response.value?.data)

const form = ref({
  provider: 'openai',
  api_key: '',
  model: 'gpt-4.1-mini',
  base_url: 'https://api.openai.com/v1',
  timeout: 30,
})

watch(settings, (value) => {
  if (!value)
    return

  form.value.provider = value.provider
  form.value.model = value.model
  form.value.base_url = value.base_url
  form.value.timeout = value.timeout
  form.value.api_key = ''
}, { immediate: true })

async function save() {
  try {
    await mutateAsync({
      provider: form.value.provider,
      model: form.value.model,
      base_url: form.value.base_url,
      timeout: form.value.timeout,
      api_key: form.value.api_key.trim() || undefined,
    })

    form.value.api_key = ''
    toast.success('AI settings saved.')
  }
  catch (error: any) {
    toast.error(error?.data?.message ?? error?.message ?? 'Could not save AI settings.')
  }
}
</script>

<template>
  <div class="max-w-2xl">
    <div class="mb-6">
      <h3 class="text-lg font-medium">
        AI Settings
      </h3>
      <p class="text-sm text-muted-foreground">
        Configure the provider, model, endpoint, and API key used for admin-side AI features.
      </p>
    </div>
    <Separator class="mb-6" />

    <div class="space-y-6">
      <div class="grid gap-4 md:grid-cols-2">
        <div class="space-y-2">
          <Label>Provider</Label>
          <Input v-model="form.provider" placeholder="openai" />
        </div>
        <div class="space-y-2">
          <Label>Model</Label>
          <Input v-model="form.model" placeholder="gpt-4.1-mini" />
        </div>
      </div>

      <div class="space-y-2">
        <Label>Base URL</Label>
        <Input v-model="form.base_url" placeholder="https://api.openai.com/v1" />
      </div>

      <div class="grid gap-4 md:grid-cols-2">
        <div class="space-y-2">
          <Label>Timeout (seconds)</Label>
          <Input v-model.number="form.timeout" type="number" min="5" max="300" />
        </div>
        <div class="space-y-2">
          <Label>API key</Label>
          <Input v-model="form.api_key" type="password" :placeholder="settings?.api_key_masked ?? 'Enter a new API key'" />
          <p class="text-xs text-muted-foreground">
            {{ settings?.has_api_key ? 'Leave this blank to keep the stored key.' : 'No API key is stored yet.' }}
          </p>
        </div>
      </div>

      <Button :disabled="isPending" @click="save">
        {{ isPending ? 'Saving...' : 'Save AI settings' }}
      </Button>
    </div>
  </div>
</template>
