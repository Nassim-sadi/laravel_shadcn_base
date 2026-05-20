<script setup lang="ts">
import { computed, ref } from 'vue'
import { toast } from 'vue-sonner'

import { Button } from '@/components/ui/button'
import { Dialog, DialogContent, DialogDescription, DialogFooter, DialogHeader, DialogTitle } from '@/components/ui/dialog'
import { Progress } from '@/components/ui/progress'
import { ScrollArea } from '@/components/ui/scroll-area'
import { useConfirmAiContentImportMutation, usePreviewAiContentImportMutation, type AiModuleKey } from '@/services/api/ai-content.api'
import { validateImportJson } from './module-config'

const props = defineProps<{
  module: AiModuleKey
  locale: string
}>()

const emit = defineEmits<{
  imported: []
}>()

const open = defineModel<boolean>('open', { default: false })

const selectedFile = ref<File | null>(null)
const progress = ref(0)
const stageLabel = ref('Waiting for a JSON file')
const rowErrors = ref<Array<{ row: number, errors: Record<string, string[]> }>>([])
const previewToken = ref<string | null>(null)
const itemCount = ref(0)

const { mutateAsync: previewImport, isPending: isPreviewPending } = usePreviewAiContentImportMutation()
const { mutateAsync: confirmImport, isPending: isConfirmPending } = useConfirmAiContentImportMutation()

const canConfirm = computed(() => Boolean(previewToken.value) && rowErrors.value.length === 0 && itemCount.value > 0)

function resetState() {
  selectedFile.value = null
  progress.value = 0
  stageLabel.value = 'Waiting for a JSON file'
  rowErrors.value = []
  previewToken.value = null
  itemCount.value = 0
}

async function handleFileSelection(event: Event) {
  const file = (event.target as HTMLInputElement).files?.[0]
  if (!file)
    return

  selectedFile.value = file
  previewToken.value = null
  rowErrors.value = []
  itemCount.value = 0

  try {
    stageLabel.value = 'Parsing JSON locally'
    progress.value = 20

    const text = await file.text()
    const parsed = JSON.parse(text)
    const localValidation = validateImportJson(props.module, parsed)

    progress.value = 45

    if (!localValidation.valid) {
      rowErrors.value = localValidation.errors
      stageLabel.value = 'Fix local validation errors before upload'
      progress.value = 100
      return
    }

    stageLabel.value = 'Uploading JSON for server validation'

    const response = await previewImport({
      module: props.module,
      file,
      onProgress: (value) => {
        progress.value = 45 + Math.round(value * 0.35)
      },
    })

    const data = response.data
    itemCount.value = data?.item_count ?? 0
    previewToken.value = data?.preview_token ?? null
    rowErrors.value = data?.row_errors ?? []
    progress.value = 100
    stageLabel.value = previewToken.value ? 'Preview ready' : 'Validation finished with errors'
  }
  catch (error: any) {
    const responseData = error?.data?.data
    rowErrors.value = responseData?.row_errors ?? [{ row: 0, errors: { file: [error?.data?.message ?? error?.message ?? 'Import preview failed.'] } }]
    stageLabel.value = 'Validation failed'
    progress.value = 100
  }
}

async function confirm() {
  if (!previewToken.value)
    return

  try {
    stageLabel.value = 'Creating records'
    progress.value = 70
    const response = await confirmImport({ preview_token: previewToken.value })
    progress.value = 100
    toast.success(response.message ?? 'Import completed successfully.')
    emit('imported')
    open.value = false
    resetState()
  }
  catch (error: any) {
    toast.error(error?.data?.message ?? error?.message ?? 'Import failed.')
    stageLabel.value = 'Import failed'
    progress.value = 100
  }
}
</script>

<template>
  <Dialog v-model:open="open" @update:open="(value) => { if (!value) resetState() }">
    <DialogContent class="sm:max-w-3xl">
      <DialogHeader>
        <DialogTitle>Upload JSON</DialogTitle>
        <DialogDescription>
          Preview the JSON, validate it in the browser and on the server, then confirm creation.
        </DialogDescription>
      </DialogHeader>

      <div class="space-y-5 py-2">
        <input type="file" accept=".json,application/json,text/plain" @change="handleFileSelection" />

        <div class="space-y-2">
          <div class="flex items-center justify-between text-sm text-muted-foreground">
            <span>{{ stageLabel }}</span>
            <span>{{ progress }}%</span>
          </div>
          <Progress :model-value="progress" />
        </div>

        <div v-if="selectedFile" class="rounded-md border p-3 text-sm">
          <div><span class="font-medium">File:</span> {{ selectedFile.name }}</div>
          <div><span class="font-medium">Locale:</span> {{ locale }}</div>
          <div><span class="font-medium">Preview items:</span> {{ itemCount }}</div>
        </div>

        <div v-if="rowErrors.length > 0" class="space-y-2">
          <h4 class="text-sm font-medium">Validation errors</h4>
          <ScrollArea class="h-56 rounded-md border p-3">
            <div class="space-y-4 text-sm">
              <div v-for="error in rowErrors" :key="`row-${error.row}`" class="space-y-1">
                <div class="font-medium">
                  {{ error.row === 0 ? 'File' : `Row ${error.row}` }}
                </div>
                <ul class="space-y-1 text-muted-foreground">
                  <li v-for="(messages, field) in error.errors" :key="field">
                    <span class="font-medium">{{ field }}:</span> {{ messages.join(' ') }}
                  </li>
                </ul>
              </div>
            </div>
          </ScrollArea>
        </div>
      </div>

      <DialogFooter>
        <Button variant="outline" type="button" @click="open = false">
          Close
        </Button>
        <Button type="button" :disabled="!canConfirm || isPreviewPending || isConfirmPending" @click="confirm">
          {{ isConfirmPending ? 'Importing...' : 'Confirm import' }}
        </Button>
      </DialogFooter>
    </DialogContent>
  </Dialog>
</template>
