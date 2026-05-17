<script lang="ts" setup>
import { Trash2Icon } from '@lucide/vue'
import { ref, watch } from 'vue'

import type { IMedia } from '@/services/api/media.api'

import ConfirmDialog from '@/components/confirm-dialog.vue'
import { Button } from '@/components/ui/button'
import { Input } from '@/components/ui/input'
import { Label } from '@/components/ui/label'
import { Textarea } from '@/components/ui/textarea'
import { useDeleteMediaMutation } from '@/services/api/media.api'

const props = defineProps<{
  item: IMedia | null
}>()

const emit = defineEmits<{
  close: []
  deleted: []
}>()

// We handle metadata in a local form; actual save will happen via an external call
const { mutate: deleteMedia, isPending: isDeleting } = useDeleteMediaMutation()
const showDeleteDialog = ref(false)

const form = ref({
  name: '',
  alt_text: '',
  caption: '',
  description: '',
  folder: '',
})

watch(() => props.item, (item) => {
  if (item) {
    form.value = {
      name: item.name || '',
      alt_text: item.alt_text || '',
      caption: item.caption || '',
      description: item.description || '',
      folder: item.folder || '',
    }
  }
}, { immediate: true })

function confirmDelete() {
  showDeleteDialog.value = true
}

function handleDelete() {
  if (!props.item)
    return
  deleteMedia(props.item.id)
  showDeleteDialog.value = false
  emit('deleted')
}
</script>

<template>
  <div v-if="item" class="space-y-6">
    <div class="aspect-video bg-muted rounded-lg overflow-hidden flex items-center justify-center">
      <img
        v-if="item.mime_type.startsWith('image/')"
        :src="item.url"
        :alt="item.alt_text || item.name"
        class="w-full h-full object-contain"
      >
      <div v-else class="text-muted-foreground text-sm">
        {{ item.original_name }}
      </div>
    </div>

    <div class="grid grid-cols-2 gap-2 text-xs text-muted-foreground">
      <div>
        <span class="font-medium">Size:</span> {{ (item.size / 1024).toFixed(0) }} KB
      </div>
      <div>
        <span class="font-medium">Type:</span> {{ item.mime_type }}
      </div>
      <div v-if="item.width">
        <span class="font-medium">Dimensions:</span> {{ item.width }}x{{ item.height }}
      </div>
      <div>
        <span class="font-medium">Created:</span> {{ new Date(item.created_at).toLocaleDateString() }}
      </div>
    </div>

    <div class="space-y-4">
      <div class="space-y-2">
        <Label for="edit-name">Name</Label>
        <Input id="edit-name" v-model="form.name" />
      </div>
      <div class="space-y-2">
        <Label for="edit-alt">Alt Text</Label>
        <Input id="edit-alt" v-model="form.alt_text" />
      </div>
      <div class="space-y-2">
        <Label for="edit-caption">Caption</Label>
        <Input id="edit-caption" v-model="form.caption" />
      </div>
      <div class="space-y-2">
        <Label for="edit-desc">Description</Label>
        <Textarea id="edit-desc" v-model="form.description" />
      </div>
      <div class="space-y-2">
        <Label for="edit-folder">Folder</Label>
        <Input id="edit-folder" v-model="form.folder" />
      </div>
    </div>

    <div class="flex gap-2">
      <Button class="flex-1" @click="emit('close')">
        Done
      </Button>
      <Button variant="destructive" size="icon" @click="confirmDelete">
        <Trash2Icon class="h-4 w-4" />
      </Button>
    </div>

    <ConfirmDialog
      :open="showDeleteDialog"
      title="Delete Media"
      description="Are you sure you want to delete this file? This action cannot be undone."
      cancel-button-text="Cancel"
      confirm-button-text="Delete"
      :destructive="true"
      :is-loading="isDeleting"
      @cancel="showDeleteDialog = false"
      @confirm="handleDelete"
    />
  </div>
</template>
