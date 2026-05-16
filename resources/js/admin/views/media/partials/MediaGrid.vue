<script lang="ts" setup>
import type { IMedia } from '@/services/api/media.api'
import { CheckIcon, FileIcon } from '@lucide/vue'

defineProps<{
  items: IMedia[]
  selected: Set<number>
  pickerMode?: boolean
}>()

const emit = defineEmits<{
  select: [id: number]
  edit: [item: IMedia]
  toggleSelect: [id: number]
}>()

function formatSize(bytes: number): string {
  if (bytes < 1024) return bytes + ' B'
  if (bytes < 1024 * 1024) return (bytes / 1024).toFixed(0) + ' KB'
  return (bytes / (1024 * 1024)).toFixed(1) + ' MB'
}

function isImage(item: IMedia): boolean {
  return item.mime_type.startsWith('image/')
}
</script>

<template>
  <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-6 gap-4">
    <div
      v-for="item in items"
      :key="item.id"
      class="relative group rounded-lg border overflow-hidden cursor-pointer hover:ring-2 hover:ring-primary transition-all"
      :class="{ 'ring-2 ring-primary': selected.has(item.id) }"
      @click="pickerMode ? emit('select', item.id) : emit('edit', item)"
    >
      <div v-if="!pickerMode" class="absolute top-2 left-2 z-10">
        <input
          type="checkbox"
          :checked="selected.has(item.id)"
          class="h-4 w-4 rounded border-gray-300"
          @click.stop
          @change="emit('toggleSelect', item.id)"
        />
      </div>

      <div class="aspect-square bg-muted flex items-center justify-center overflow-hidden">
        <img
          v-if="isImage(item)"
          :src="item.thumbnail_url || item.url"
          :alt="item.alt_text || item.name"
          class="w-full h-full object-cover"
          loading="lazy"
        />
        <div v-else class="flex flex-col items-center gap-1 text-muted-foreground">
          <FileIcon class="h-8 w-8" />
          <span class="text-xs font-medium uppercase">{{ item.extension }}</span>
        </div>
      </div>

      <div class="p-2 space-y-1">
        <p class="text-xs font-medium truncate">{{ item.name }}</p>
        <p class="text-[10px] text-muted-foreground">{{ formatSize(item.size) }}</p>
      </div>

      <div
        v-if="selected.has(item.id)"
        class="absolute top-2 right-2 bg-primary text-primary-foreground rounded-full p-0.5"
      >
        <CheckIcon class="h-3 w-3" />
      </div>
    </div>
  </div>
</template>
