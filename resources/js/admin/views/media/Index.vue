<script lang="ts" setup>
import { ref, computed, watch } from 'vue'
import { BasicPage } from '@/components/global-layout'
import { Button } from '@/components/ui/button'
import { Input } from '@/components/ui/input'
import { ImageIcon, Loader2Icon, UploadIcon } from '@lucide/vue'
import { useGetMediaQuery, useBulkDeleteMediaMutation } from '@/services/api/media.api'
import MediaGrid from './partials/MediaGrid.vue'
import Pagination from '@/admin/components/Pagination.vue'
import MediaModal from '@/admin/components/MediaModal.vue'

const search = ref('')
const typeFilter = ref('')
const page = ref(1)
const showModal = ref(false)

const queryParams = computed(() => {
  const params: Record<string, any> = { page: page.value, per_page: 24 }
  if (search.value) params.search = search.value
  if (typeFilter.value) params.type = typeFilter.value
  return params
})

const { data: response, isLoading, refetch } = useGetMediaQuery(queryParams)
const mediaItems = computed(() => response.value?.data?.data ?? [])
const pagination = computed(() => {
  if (!response.value?.data) return null
  const { current_page, last_page, total } = response.value.data
  return { current_page, last_page, total }
})

const selected = ref<Set<number>>(new Set())
const selectMode = ref(false)

const { mutate: bulkDelete } = useBulkDeleteMediaMutation()

function toggleSelect(id: number) {
  const s = new Set(selected.value)
  if (s.has(id)) s.delete(id); else s.add(id)
  selected.value = s
  selectMode.value = s.size > 0
}

function clearSelection() {
  selected.value = new Set()
  selectMode.value = false
}

function deleteSelected() {
  if (selected.value.size === 0) return
  bulkDelete(Array.from(selected.value))
  clearSelection()
}

watch(search, () => { page.value = 1 })
</script>

<template>
  <BasicPage title="Media Library" description="Manage your media files" sticky>
    <template #actions>
      <Input v-model="search" placeholder="Search files..." class="w-64" />

      <select v-model="typeFilter" class="h-9 rounded-md border border-input bg-background px-3 text-sm">
        <option value="">All Types</option>
        <option value="image">Images</option>
      </select>

      <Button v-if="selectMode" variant="destructive" size="sm" @click="deleteSelected">
        Delete Selected ({{ selected.size }})
      </Button>

      <Button v-if="selectMode" variant="ghost" size="sm" @click="clearSelection">
        Cancel
      </Button>

      <Button @click="refetch" variant="outline">Refresh</Button>
      <Button @click="showModal = true">
        <UploadIcon class="h-4 w-4 mr-2" />
        Upload
      </Button>
    </template>

    <div v-if="isLoading" class="flex justify-center py-12">
      <Loader2Icon class="h-8 w-8 animate-spin text-muted-foreground" />
    </div>

    <MediaGrid
      v-else-if="mediaItems.length > 0"
      :items="mediaItems"
      :selected="selected"
      @edit="showModal = true"
      @toggle-select="toggleSelect"
    />

    <div v-else class="text-center py-12 text-muted-foreground">
      <ImageIcon class="h-12 w-12 mx-auto mb-4" />
      <p>No media files yet.</p>
      <Button variant="outline" class="mt-4" @click="showModal = true">Upload your first file</Button>
    </div>

    <Pagination
      v-if="pagination"
      :current-page="pagination.current_page"
      :last-page="pagination.last_page"
      :total="pagination.total"
      @page-change="page = $event"
    />

    <MediaModal :open="showModal" @close="showModal = false" />
  </BasicPage>
</template>
