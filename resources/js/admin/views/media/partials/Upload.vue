<script lang="ts" setup>
import { ref } from 'vue'
import { Button } from '@/components/ui/button'
import { Label } from '@/components/ui/label'
import { Input } from '@/components/ui/input'
import { useUploadMediaMutation } from '@/services/api/media.api'
import { Loader2Icon, UploadIcon } from '@lucide/vue'

const emit = defineEmits<{
  uploaded: []
  close: []
}>()

const fileInput = ref<HTMLInputElement | null>(null)
const selectedFile = ref<File | null>(null)
const previewUrl = ref<string | null>(null)
const name = ref('')
const altText = ref('')
const folder = ref('')
const isUploading = ref(false)

const { mutate: uploadMedia } = useUploadMediaMutation()

function onFileChange(event: Event) {
  const input = event.target as HTMLInputElement
  if (input.files && input.files[0]) {
    selectedFile.value = input.files[0]
    previewUrl.value = URL.createObjectURL(input.files[0])
    if (!name.value) {
      name.value = input.files[0].name.replace(/\.[^.]+$/, '')
    }
  }
}

function triggerFileInput() {
  fileInput.value?.click()
}

async function upload() {
  if (!selectedFile.value) return
  isUploading.value = true

  const formData = new FormData()
  formData.append('file', selectedFile.value)
  if (name.value) formData.append('name', name.value)
  if (altText.value) formData.append('alt_text', altText.value)
  if (folder.value) formData.append('folder', folder.value)

  try {
    await uploadMedia(formData)
    emit('uploaded')
    emit('close')
  } catch (e) {
    console.error('Upload failed', e)
  } finally {
    isUploading.value = false
  }
}
</script>

<template>
  <div class="space-y-6">
    <div
      class="border-2 border-dashed rounded-lg p-8 text-center hover:bg-muted/50 transition-colors cursor-pointer"
      @click="triggerFileInput"
    >
      <input
        ref="fileInput"
        type="file"
        class="hidden"
        accept="image/*,.pdf,.doc,.docx,.xls,.xlsx,.mp4,.avi,.mov"
        @change="onFileChange"
      />
      <div v-if="!previewUrl" class="space-y-2">
        <UploadIcon class="h-10 w-10 mx-auto text-muted-foreground" />
        <p class="text-sm text-muted-foreground">Click to select a file</p>
        <p class="text-xs text-muted-foreground">JPG, PNG, GIF, WebP, SVG, PDF, DOC, XLS, MP4 (max 10MB)</p>
      </div>
      <img
        v-else
        :src="previewUrl"
        class="max-h-48 mx-auto rounded object-contain"
        alt="Preview"
      />
    </div>

    <div class="space-y-4">
      <div class="space-y-2">
        <Label for="media-name">Name</Label>
        <Input id="media-name" v-model="name" placeholder="File name" />
      </div>
      <div class="space-y-2">
        <Label for="media-alt">Alt Text</Label>
        <Input id="media-alt" v-model="altText" placeholder="Descriptive alt text" />
      </div>
      <div class="space-y-2">
        <Label for="media-folder">Folder</Label>
        <Input id="media-folder" v-model="folder" placeholder="e.g. services, projects" />
      </div>
    </div>

    <Button class="w-full" :disabled="!selectedFile || isUploading" @click="upload">
      <Loader2Icon v-if="isUploading" class="h-4 w-4 mr-2 animate-spin" />
      {{ isUploading ? 'Uploading...' : 'Upload' }}
    </Button>
  </div>
</template>
