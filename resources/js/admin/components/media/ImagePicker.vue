<script lang="ts" setup>
import { ref } from 'vue'

import { Button } from '@/components/ui/button'

import MediaPickerModal from './MediaPickerModal.vue'

defineProps<{
  modelValue: number | null
  previewUrl?: string | null
}>()

const emit = defineEmits<{
  'update:modelValue': [id: number | null]
}>()

const showPicker = ref(false)

function selectMedia(media: { id: number, url: string, alt_text: string | null }) {
  emit('update:modelValue', media.id)
}

function clear() {
  emit('update:modelValue', null)
}
</script>

<template>
  <div class="space-y-2">
    <div
      v-if="modelValue && previewUrl"
      class="relative w-32 h-32 rounded-lg border overflow-hidden group"
    >
      <img :src="previewUrl" class="w-full h-full object-cover">
      <div class="absolute inset-0 bg-black/50 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center gap-2">
        <Button size="icon" variant="ghost" class="h-8 w-8 text-white" @click="showPicker = true">
          <PencilIcon class="h-4 w-4" />
        </Button>
        <Button size="icon" variant="ghost" class="h-8 w-8 text-white" @click="clear">
          <XIcon class="h-4 w-4" />
        </Button>
      </div>
    </div>
    <Button v-else variant="outline" class="w-32 h-32" @click="showPicker = true">
      <div class="flex flex-col items-center gap-1 text-muted-foreground">
        <ImageIcon class="h-6 w-6" />
        <span class="text-xs">Select Image</span>
      </div>
    </Button>

    <MediaPickerModal v-if="showPicker" @close="showPicker = false" @select="selectMedia" />
  </div>
</template>
