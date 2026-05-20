<script setup lang="ts">
import { ref } from 'vue'
import { toast } from 'vue-sonner'

import { Button } from '@/components/ui/button'
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select'
import { languageMetadata } from '@/plugins/i18n'
import type { AiModuleKey } from '@/services/api/ai-content.api'
import AiJsonImportDialog from './AiJsonImportDialog.vue'
import { buildImportPrompt, buildSampleJson } from './module-config'

const props = defineProps<{
  module: AiModuleKey
  disabled?: boolean
}>()

const emit = defineEmits<{
  imported: []
}>()

const activeLocale = ref('fr')
const dialogOpen = ref(false)

async function copyPrompt() {
  try {
    await navigator.clipboard.writeText(buildImportPrompt(props.module, activeLocale.value))
    toast.success('Prompt copied.')
  }
  catch {
    toast.error('Could not copy the prompt.')
  }
}

function downloadSample() {
  const json = buildSampleJson(props.module, activeLocale.value)
  const blob = new Blob([json], { type: 'application/json' })
  const url = URL.createObjectURL(blob)
  const link = document.createElement('a')
  link.href = url
  link.download = `${props.module}-${activeLocale.value}-sample.json`
  link.click()
  URL.revokeObjectURL(url)
}
</script>

<template>
  <div class="flex flex-wrap items-center gap-2">
    <Select v-model="activeLocale" :disabled="disabled">
      <SelectTrigger class="w-[140px]">
        <SelectValue placeholder="Locale" />
      </SelectTrigger>
      <SelectContent>
        <SelectItem v-for="language in languageMetadata" :key="language.code" :value="language.code">
          {{ language.flag }} {{ language.name }}
        </SelectItem>
      </SelectContent>
    </Select>

    <Button variant="outline" :disabled="disabled" @click="copyPrompt">
      Copy prompt
    </Button>
    <Button variant="outline" :disabled="disabled" @click="downloadSample">
      Download sample JSON
    </Button>
    <Button :disabled="disabled" @click="dialogOpen = true">
      Upload JSON
    </Button>

    <AiJsonImportDialog
      v-model:open="dialogOpen"
      :module="module"
      :locale="activeLocale"
      @imported="emit('imported')"
    />
  </div>
</template>
