<script lang="ts" setup>
import { ref } from 'vue'
import { Button } from '@/components/ui/button'
import { Label } from '@/components/ui/label'
import { ImageIcon, XIcon } from '@lucide/vue'
import MediaModal from './MediaModal.vue'

defineProps<{
  imageId?: number | null
  imageUrl?: string | null
}>()

const emit = defineEmits<{
  'update:imageId': [value: number | null]
  'update:imageUrl': [value: string | null]
}>()

const showPicker = ref(false)

function handleSelect(data: { id: number; url: string; thumbnail_url?: string }) {
  emit('update:imageId', data.id)
  emit('update:imageUrl', data.thumbnail_url ?? data.url)
  showPicker.value = false
}

function removeImage() {
  emit('update:imageId', null)
  emit('update:imageUrl', null)
}
</script>

<template>
  <div class="admin-form-field">
    <Label>Image</Label>
    <div class="flex items-center gap-3">
      <div
        v-if="imageUrl"
        class="relative h-16 w-16 shrink-0 overflow-hidden rounded-md border"
      >
        <img :src="imageUrl" alt="Preview" class="h-full w-full object-cover" />
      </div>
      <div
        v-else
        class="flex h-16 w-16 shrink-0 items-center justify-center rounded-md border bg-muted"
      >
        <ImageIcon class="h-6 w-6 text-muted-foreground" />
      </div>
      <div class="flex gap-2">
        <Button type="button" variant="outline" size="sm" @click="showPicker = true">
          {{ imageId ? 'Change' : 'Choose Image' }}
        </Button>
        <Button
          v-if="imageId"
          type="button"
          variant="ghost"
          size="sm"
          class="text-destructive"
          @click="removeImage"
        >
          <XIcon class="h-4 w-4" />
        </Button>
      </div>
    </div>

    <MediaModal :open="showPicker" select-mode @close="showPicker = false" @select="handleSelect" />
  </div>
</template>
