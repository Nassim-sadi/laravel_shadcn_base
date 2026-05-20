<script setup lang="ts">
import { computed, ref, watch } from 'vue'
import { SparklesIcon } from '@lucide/vue'
import { toast } from 'vue-sonner'

import { Button } from '@/components/ui/button'
import { Checkbox } from '@/components/ui/checkbox'
import { Dialog, DialogContent, DialogDescription, DialogFooter, DialogHeader, DialogTitle } from '@/components/ui/dialog'
import { Input } from '@/components/ui/input'
import { Label } from '@/components/ui/label'
import { Textarea } from '@/components/ui/textarea'
import { useGenerateAiContentMutation, type AiContentField, type AiModuleKey } from '@/services/api/ai-content.api'
import { aiModuleConfig } from './module-config'

const props = defineProps<{
  module: AiModuleKey
  locale: string
  source: Partial<Record<AiContentField, string>>
}>()

const emit = defineEmits<{
  apply: [payload: Partial<Record<AiContentField, string>>]
}>()

const open = defineModel<boolean>('open', { default: false })

const availableFields = computed(() => aiModuleConfig[props.module].generatorFields)
const selectedFields = ref<AiContentField[]>([])
const mode = ref<'draft' | 'improve'>('draft')
const tone = ref('Professional and clear')
const context = ref('')

const { mutateAsync, isPending } = useGenerateAiContentMutation()

const hasSourceContent = computed(() =>
  Object.values(props.source).some(value => typeof value === 'string' && value.trim().length > 0),
)

watch([open, availableFields], ([isOpen, fields]) => {
  if (!isOpen)
    return

  selectedFields.value = fields.slice(0, Math.min(fields.length, 4)).map(field => field.key)
  mode.value = hasSourceContent.value ? 'improve' : 'draft'
}, { immediate: true })

function toggleField(field: AiContentField, checked: boolean) {
  if (checked) {
    if (!selectedFields.value.includes(field)) {
      selectedFields.value = [...selectedFields.value, field]
    }
    return
  }

  selectedFields.value = selectedFields.value.filter(item => item !== field)
}

async function generate() {
  if (selectedFields.value.length === 0) {
    toast.error('Select at least one field to generate.')
    return
  }

  try {
    const response = await mutateAsync({
      module: props.module,
      mode: mode.value,
      locale: props.locale,
      fields: selectedFields.value,
      tone: tone.value.trim() || undefined,
      context: context.value.trim() || undefined,
      source: props.source,
    })

    emit('apply', response.data?.fields ?? {})
    toast.success('AI draft applied to the form.')
    open.value = false
  }
  catch (error: any) {
    toast.error(error?.data?.message ?? error?.message ?? 'Failed to generate content.')
  }
}
</script>

<template>
  <Dialog v-model:open="open">
    <DialogContent class="sm:max-w-2xl">
      <DialogHeader>
        <DialogTitle class="flex items-center gap-2">
          <SparklesIcon class="size-4" />
          <span>Generate content</span>
        </DialogTitle>
        <DialogDescription>
          Create a draft for the current language without saving anything yet.
        </DialogDescription>
      </DialogHeader>

      <div class="space-y-5 py-2">
        <div class="space-y-2">
          <Label>Mode</Label>
          <div class="flex flex-wrap gap-2">
            <Button :variant="mode === 'draft' ? 'default' : 'outline'" size="sm" type="button" @click="mode = 'draft'">
              Draft new
            </Button>
            <Button :variant="mode === 'improve' ? 'default' : 'outline'" size="sm" type="button" @click="mode = 'improve'">
              Improve existing
            </Button>
          </div>
        </div>

        <div class="space-y-2">
          <Label>Fields</Label>
          <div class="grid grid-cols-1 gap-2 sm:grid-cols-2">
            <label
              v-for="field in availableFields"
              :key="field.key"
              class="flex items-center gap-2 rounded-md border px-3 py-2 text-sm"
            >
              <Checkbox
                :model-value="selectedFields.includes(field.key)"
                @update:model-value="toggleField(field.key, Boolean($event))"
              />
              <span>{{ field.label }}</span>
            </label>
          </div>
        </div>

        <div class="space-y-2">
          <Label>Tone</Label>
          <Input v-model="tone" placeholder="Professional and clear" />
        </div>

        <div class="space-y-2">
          <Label>Context</Label>
          <Textarea v-model="context" rows="5" placeholder="Add audience notes, constraints, differentiators, or things the AI should avoid." />
        </div>
      </div>

      <DialogFooter>
        <Button variant="outline" type="button" @click="open = false">
          Cancel
        </Button>
        <Button type="button" :disabled="isPending" @click="generate">
          {{ isPending ? 'Generating...' : 'Generate draft' }}
        </Button>
      </DialogFooter>
    </DialogContent>
  </Dialog>
</template>
