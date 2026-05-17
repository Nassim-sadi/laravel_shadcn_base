<script lang="ts" setup>
import { CheckIcon, ImageIcon, Loader2Icon, Trash2Icon, UploadIcon, XIcon } from '@lucide/vue'
import { computed, ref, watch } from 'vue'

import type { IMedia } from '@/services/api/media.api'

import { useMediaUpload } from '@/admin/composables/useMediaUpload'
import ConfirmDialog from '@/components/confirm-dialog.vue'
import { Button } from '@/components/ui/button'
import { Input } from '@/components/ui/input'
import { Label } from '@/components/ui/label'
import { useDeleteMediaMutation, useGetMediaQuery, useUpdateMediaMutation } from '@/services/api/media.api'

const props = withDefaults(defineProps<{
  open: boolean
  selectMode?: boolean
}>(), {
  selectMode: false,
})

const emit = defineEmits<{
  close: []
  select: [media: { id: number, url: string, name: string, alt_text: string | null }]
}>()

const activeTab = ref<'library' | 'upload'>('library')
const search = ref('')
const page = ref(1)
const editingImage = ref<IMedia | null>(null)
const selectedImage = ref<IMedia | null>(null)
const showDeleteDialog = ref(false)
const deletingImage = ref<IMedia | null>(null)

const queryParams = computed(() => {
  const params: Record<string, any> = { page: page.value, per_page: 24 }
  if (search.value)
    params.search = search.value
  return params
})

const { data: response, isLoading } = useGetMediaQuery(queryParams)
const mediaItems = computed(() => {
  const r = response.value
  if (!r)
    return []
  if (Array.isArray(r))
    return r
  if (r.data && Array.isArray(r.data))
    return r.data
  if (Array.isArray((r as any)?.data?.data))
    return (r as any).data.data
  return []
})
const pagination = computed(() => {
  const r = response.value
  if (!r)
    return null
  const meta = (r as any)?.meta
  if (meta?.current_page)
    return meta
  if ((r as any)?.data?.current_page)
    return (r as any).data
  return null
})

const { mutate: updateMedia } = useUpdateMediaMutation()
const { mutate: deleteMedia, isPending: isDeleting } = useDeleteMediaMutation()

const {
  files,
  isUploading,
  addFiles,
  removeFile,
  uploadAll,
  reset,
} = useMediaUpload()

const fileInputRef = ref<HTMLInputElement | null>(null)
const dragOver = ref(false)

function handleDrop(e: DragEvent) {
  e.preventDefault()
  dragOver.value = false
  if (e.dataTransfer?.files) {
    addFiles(e.dataTransfer.files)
    activeTab.value = 'upload'
  }
}

function handleDragOver(e: DragEvent) {
  e.preventDefault()
  dragOver.value = true
}

function handleDragLeave() {
  dragOver.value = false
}

function handleFilePick(e: Event) {
  const input = e.target as HTMLInputElement
  if (input.files) {
    addFiles(input.files)
    activeTab.value = 'upload'
  }
  input.value = ''
}

function openUploadTab() {
  activeTab.value = 'upload'
}

function handleGridEdit(item: IMedia) {
  if (props.selectMode) {
    selectedImage.value = item
    return
  }
  editingImage.value = item
}

function handleGridSelect(id: number) {
  const item = mediaItems.value.find((m: IMedia) => m.id === id)
  if (item)
    selectedImage.value = item
}

function confirmSelect() {
  if (!selectedImage.value)
    return
  emit('select', {
    id: selectedImage.value.id,
    url: selectedImage.value.url,
    name: selectedImage.value.name,
    alt_text: selectedImage.value.alt_text,
  })
  emit('close')
}

const editForm = ref({ name: '', alt_text: '', caption: '', description: '' })

watch(() => editingImage.value, (img) => {
  if (img) {
    editForm.value = {
      name: img.name || '',
      alt_text: img.alt_text || '',
      caption: img.caption || '',
      description: img.description || '',
    }
  }
}, { immediate: true })

function saveEdit() {
  if (!editingImage.value)
    return
  updateMedia({
    id: editingImage.value.id,
    ...editForm.value,
  })
  editingImage.value = null
}

function getObjectUrl(file: File): string {
  return URL.createObjectURL(file)
}

function confirmDeleteImage(item: IMedia) {
  deletingImage.value = item
  showDeleteDialog.value = true
}

function handleDeleteImage() {
  if (!deletingImage.value)
    return
  deleteMedia(deletingImage.value.id)
  showDeleteDialog.value = false
  deletingImage.value = null
  editingImage.value = null
}

watch(() => props.open, (isOpen) => {
  if (isOpen) {
    activeTab.value = 'library'
    search.value = ''
    page.value = 1
    selectedImage.value = null
    editingImage.value = null
  }
})
</script>

<template>
  <Teleport defer to="body">
    <div v-if="open" class="fixed inset-0 z-50" @keydown.escape="emit('close')">
      <div class="fixed inset-0 bg-black/50" @click="emit('close')" />

      <div
        class="fixed inset-0 flex items-center justify-center pointer-events-none"
      >
        <div
          class="pointer-events-auto bg-background rounded-lg shadow-xl w-[95vw] max-w-5xl max-h-[90vh] flex flex-col"
          @drop="handleDrop"
          @dragover="handleDragOver"
          @dragleave="handleDragLeave"
        >
          <!-- Header -->
          <div class="flex items-center justify-between px-6 py-4 border-b shrink-0">
            <h2 class="text-lg font-semibold">
              {{ selectMode ? 'Select Media' : 'Media Library' }}
            </h2>
            <button class="text-muted-foreground hover:text-foreground transition-colors" @click="emit('close')">
              <XIcon class="h-5 w-5" />
            </button>
          </div>

          <!-- Tabs -->
          <div class="flex border-b px-6 shrink-0">
            <button
              class="px-4 py-2.5 text-sm font-medium border-b-2 transition-colors"
              :class="activeTab === 'library' ? 'border-primary text-foreground' : 'border-transparent text-muted-foreground hover:text-foreground'"
              @click="activeTab = 'library'"
            >
              Media Library
            </button>
            <button
              class="px-4 py-2.5 text-sm font-medium border-b-2 transition-colors"
              :class="activeTab === 'upload' ? 'border-primary text-foreground' : 'border-transparent text-muted-foreground hover:text-foreground'"
              @click="activeTab = 'upload'"
            >
              Upload Files
            </button>
          </div>

          <!-- Body -->
          <div class="flex-1 overflow-y-auto p-6">
            <!-- === LIBRARY TAB === -->
            <div v-if="activeTab === 'library'">
              <Transition name="slide" mode="out-in">
                <!-- Grid view -->
                <div v-if="!editingImage" key="grid">
                  <!-- Search -->
                  <div class="flex gap-2 mb-4">
                    <Input v-model="search" placeholder="Search files..." class="flex-1" />
                  </div>

                  <!-- Grid -->
                  <div v-if="isLoading" class="flex justify-center py-12">
                    <Loader2Icon class="h-8 w-8 animate-spin text-muted-foreground" />
                  </div>

                  <div v-else-if="mediaItems.length > 0" class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-4">
                    <div
                      v-for="item in mediaItems"
                      :key="item.id"
                      class="relative group rounded-lg border overflow-hidden cursor-pointer hover:ring-2 hover:ring-primary transition-all"
                      :class="{
                        'ring-2 ring-primary': selectMode && selectedImage?.id === item.id,
                      }"
                      @click="selectMode ? handleGridSelect(item.id) : handleGridEdit(item)"
                    >
                      <div class="aspect-square bg-muted flex items-center justify-center overflow-hidden">
                        <img
                          v-if="item.mime_type.startsWith('image/')"
                          :src="item.thumbnail_url || item.url"
                          :alt="item.alt_text || item.name"
                          class="w-full h-full object-cover"
                          loading="lazy"
                        >
                        <div v-else class="flex flex-col items-center gap-1 text-muted-foreground">
                          <span class="text-xs font-medium uppercase">{{ item.extension }}</span>
                        </div>
                      </div>
                      <div class="p-2">
                        <p class="text-xs font-medium truncate">
                          {{ item.name }}
                        </p>
                        <p class="text-[10px] text-muted-foreground">
                          {{ (item.size / 1024).toFixed(0) }} KB
                        </p>
                      </div>
                      <div
                        v-if="selectMode && selectedImage?.id === item.id"
                        class="absolute top-2 right-2 bg-primary text-primary-foreground rounded-full p-0.5"
                      >
                        <CheckIcon class="h-3 w-3" />
                      </div>
                    </div>
                  </div>

                  <div v-else class="text-center py-12 text-muted-foreground space-y-4">
                    <ImageIcon class="h-12 w-12 mx-auto" />
                    <p>No media files yet.</p>
                    <Button variant="outline" @click="openUploadTab">
                      Upload your first file
                    </Button>
                  </div>

                  <!-- Pagination -->
                  <div v-if="pagination && pagination.last_page > 1" class="flex items-center justify-center gap-2 mt-6">
                    <Button
                      variant="outline"
                      size="sm"
                      :disabled="page <= 1"
                      @click="page = page - 1"
                    >
                      Previous
                    </Button>
                    <span class="text-sm text-muted-foreground">
                      Page {{ pagination.current_page }} of {{ pagination.last_page }}
                    </span>
                    <Button
                      variant="outline"
                      size="sm"
                      :disabled="page >= pagination.last_page"
                      @click="page = page + 1"
                    >
                      Next
                    </Button>
                  </div>
                </div>

                <!-- Inline edit -->
                <div v-else key="edit">
                  <button
                    class="text-sm text-muted-foreground hover:text-foreground mb-4 flex items-center gap-1"
                    @click="editingImage = null"
                  >
                    ← Back to library
                  </button>

                  <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="aspect-video bg-muted rounded-lg overflow-hidden flex items-center justify-center">
                      <img
                        v-if="editingImage.mime_type.startsWith('image/')"
                        :src="editingImage.url"
                        :alt="editingImage.alt_text || editingImage.name"
                        class="w-full h-full object-contain"
                      >
                      <div v-else class="text-muted-foreground text-sm">
                        {{ editingImage.original_name }}
                      </div>
                    </div>

                    <div class="space-y-4">
                      <div class="grid grid-cols-2 gap-2 text-xs text-muted-foreground">
                        <div><span class="font-medium">Size:</span> {{ (editingImage.size / 1024).toFixed(0) }} KB</div>
                        <div><span class="font-medium">Type:</span> {{ editingImage.mime_type }}</div>
                        <div v-if="editingImage.width">
                          <span class="font-medium">Dimensions:</span> {{ editingImage.width }}x{{ editingImage.height }}
                        </div>
                        <div><span class="font-medium">Created:</span> {{ new Date(editingImage.created_at).toLocaleDateString() }}</div>
                      </div>

                      <div class="admin-form-field">
                        <Label>Name</Label>
                        <Input v-model="editForm.name" />
                      </div>
                      <div class="admin-form-field">
                        <Label>Alt Text</Label>
                        <Input v-model="editForm.alt_text" />
                      </div>
                      <div class="admin-form-field">
                        <Label>Caption</Label>
                        <Input v-model="editForm.caption" />
                      </div>
                      <div class="admin-form-field">
                        <Label>Description</Label>
                        <textarea
                          v-model="editForm.description"
                          class="border-input h-20 w-full rounded-md border bg-transparent px-3 py-2 text-sm"
                        />
                      </div>

                      <div class="flex gap-2 pt-2">
                        <Button class="flex-1" @click="saveEdit">
                          Save Changes
                        </Button>
                        <Button variant="destructive" size="icon" @click="confirmDeleteImage(editingImage)">
                          <Trash2Icon class="h-4 w-4" />
                        </Button>
                      </div>
                    </div>
                  </div>
                </div>
              </Transition>
            </div>

            <!-- === UPLOAD TAB === -->
            <div v-if="activeTab === 'upload'" class="space-y-6">
              <!-- Drop zone -->
              <div
                class="border-2 border-dashed rounded-lg p-8 text-center transition-colors"
                :class="dragOver ? 'border-primary bg-primary/5' : 'border-muted-foreground/25 hover:border-muted-foreground/50'"
                @click="fileInputRef?.click()"
              >
                <input
                  ref="fileInputRef"
                  type="file"
                  multiple
                  accept="image/*,.pdf,.doc,.docx,.xls,.xlsx,.mp4,.avi,.mov"
                  class="hidden"
                  @change="handleFilePick"
                >
                <UploadIcon class="h-10 w-10 mx-auto text-muted-foreground mb-2" />
                <p class="text-sm text-muted-foreground">
                  Drag & drop files here or click to browse
                </p>
                <p class="text-xs text-muted-foreground mt-1">
                  JPG, PNG, GIF, WebP, SVG, PDF, DOC, XLS, MP4
                </p>
              </div>

              <!-- File queue -->
              <div v-if="files.length > 0" class="space-y-3">
                <div class="flex items-center justify-between">
                  <span class="text-sm font-medium">{{ files.length }} file(s)</span>
                  <div class="flex gap-2">
                    <Button
                      size="sm"
                      variant="outline"
                      :disabled="isUploading"
                      @click="reset"
                    >
                      Clear All
                    </Button>
                    <Button
                      size="sm"
                      :disabled="isUploading || files.every(f => f.status === 'done')"
                      @click="uploadAll"
                    >
                      <Loader2Icon v-if="isUploading" class="h-4 w-4 mr-1 animate-spin" />
                      {{ isUploading ? 'Uploading...' : 'Upload All' }}
                    </Button>
                  </div>
                </div>

                <div
                  v-for="entry in files"
                  :key="entry.id"
                  class="flex items-center gap-4 border rounded-lg p-3"
                >
                  <div class="w-10 h-10 bg-muted rounded flex items-center justify-center text-xs text-muted-foreground shrink-0 overflow-hidden">
                    <img
                      v-if="entry.file.type.startsWith('image/')"
                      :src="getObjectUrl(entry.file)"
                      class="w-full h-full object-cover"
                    >
                    <span v-else class="uppercase">{{ entry.file.name.split('.').pop() }}</span>
                  </div>

                  <div class="flex-1 min-w-0">
                    <div class="flex items-center gap-2">
                      <Input
                        v-model="entry.name"
                        class="h-7 text-xs flex-1"
                        :disabled="entry.status === 'uploading' || entry.status === 'done'"
                      />
                      <Input
                        v-model="entry.alt"
                        class="h-7 text-xs w-28"
                        placeholder="Alt"
                        :disabled="entry.status === 'uploading' || entry.status === 'done'"
                      />
                    </div>

                    <!-- Progress bar -->
                    <div v-if="entry.status === 'uploading' || entry.status === 'done'" class="mt-1.5">
                      <div class="h-1.5 bg-muted rounded-full overflow-hidden">
                        <div
                          class="h-full rounded-full transition-all duration-300"
                          :class="entry.status === 'done' ? 'bg-primary' : 'bg-primary/70'"
                          :style="{ width: `${entry.progress}%` }"
                        />
                      </div>
                      <p class="text-[10px] text-muted-foreground mt-0.5">
                        {{ entry.progress }}%
                      </p>
                    </div>

                    <p v-if="entry.status === 'error'" class="text-[10px] text-destructive mt-0.5">
                      {{ entry.error }}
                    </p>
                    <p v-else-if="entry.status === 'done'" class="text-[10px] text-green-600 mt-0.5">
                      Uploaded
                    </p>
                    <p v-else class="text-[10px] text-muted-foreground mt-0.5">
                      {{ (entry.file.size / 1024).toFixed(0) }} KB
                    </p>
                  </div>

                  <button
                    class="text-muted-foreground hover:text-destructive shrink-0"
                    :disabled="entry.status === 'uploading'"
                    @click="removeFile(entry.id)"
                  >
                    <XIcon class="h-4 w-4" />
                  </button>
                </div>

                <div v-if="files.some(f => f.status === 'done')" class="flex justify-end">
                  <Button variant="outline" size="sm" @click="activeTab = 'library'">
                    Go to Media Library
                  </Button>
                </div>
              </div>
            </div>
          </div>

          <!-- Footer (select mode) -->
          <div v-if="selectMode && activeTab === 'library' && !editingImage" class="border-t px-6 py-4 flex items-center justify-between shrink-0">
            <span v-if="selectedImage" class="text-sm text-muted-foreground">
              Selected: <strong>{{ selectedImage.name }}</strong>
            </span>
            <span v-else class="text-sm text-muted-foreground">Click an image to select it</span>
            <div class="flex gap-2">
              <Button variant="outline" @click="emit('close')">
                Cancel
              </Button>
              <Button :disabled="!selectedImage" @click="confirmSelect">
                Choose Selected
              </Button>
            </div>
          </div>
        </div>
      </div>
    </div>
  </Teleport>

  <ConfirmDialog
    v-model:open="showDeleteDialog"
    cancel-button-text="Cancel"
    confirm-button-text="Delete"
    :destructive="true"
    :is-loading="isDeleting"
    @confirm="handleDeleteImage"
  >
    <template #title>
      Delete Media
    </template>
    <template #description>
      Are you sure you want to delete this file? This action cannot be undone.
    </template>
  </ConfirmDialog>
</template>

<style scoped>
.slide-enter-active,
.slide-leave-active {
  transition: all 0.25s ease;
}
.slide-enter-from {
  transform: translateX(40px);
  opacity: 0;
}
.slide-leave-to {
  transform: translateX(-40px);
  opacity: 0;
}
</style>
