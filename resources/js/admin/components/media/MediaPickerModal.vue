<script lang="ts" setup>
import { ref, computed } from 'vue'
import { Button } from '@/components/ui/button'
import { Input } from '@/components/ui/input'
import { Dialog, DialogContent, DialogHeader, DialogTitle, DialogDescription } from '@/components/ui/dialog'
import { useGetMediaQuery } from '@/services/api/media.api'
import type { IMedia } from '@/services/api/media.api'
import MediaGrid from '../../views/media/partials/MediaGrid.vue'

const emit = defineEmits<{
  select: [media: { id: number; url: string; alt_text: string | null }]
  close: []
}>()

const search = ref('')

const queryParams = computed(() => {
  const params: Record<string, any> = { per_page: 24 }
  if (search.value) params.search = search.value
  return params
})

const { data: response } = useGetMediaQuery(queryParams)
const mediaItems = computed(() => response.value?.data?.data ?? [])

const selectedSet = ref<Set<number>>(new Set())
const selectedItem = ref<IMedia | null>(null)

function doSelect(id: number) {
  const item = mediaItems.value.find((m: IMedia) => m.id === id)
  if (item) {
    selectedItem.value = item
    selectedSet.value = new Set([id])
  }
}

function confirm() {
  if (!selectedItem.value) return
  emit('select', {
    id: selectedItem.value.id,
    url: selectedItem.value.url,
    alt_text: selectedItem.value.alt_text,
  })
}
</script>

<template>
  <Dialog :open="true" @update:open="$emit('close')">
    <DialogContent class="max-w-3xl max-h-[80vh] overflow-y-auto">
      <DialogHeader>
        <DialogTitle>Select Media</DialogTitle>
        <DialogDescription class="sr-only">Choose a media file from the library.</DialogDescription>
      </DialogHeader>

      <div class="space-y-4">
        <div class="flex gap-2">
          <Input v-model="search" placeholder="Search files..." class="flex-1" />
        </div>

        <MediaGrid
          :items="mediaItems"
          :selected="selectedSet"
          :picker-mode="true"
          @select="doSelect"
        />

        <div v-if="!mediaItems.length" class="text-center py-8 text-muted-foreground">
          No files found.
        </div>

        <div class="flex justify-end gap-2 pt-4 border-t">
          <Button variant="outline" @click="$emit('close')">Cancel</Button>
          <Button :disabled="!selectedItem" @click="confirm">Select</Button>
        </div>
      </div>
    </DialogContent>
  </Dialog>
</template>
