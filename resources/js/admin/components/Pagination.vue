<script lang="ts" setup>
import { Button } from '@/components/ui/button'

defineProps<{
  currentPage: number
  lastPage: number
  total?: number
}>()

const emit = defineEmits<{
  (e: 'page-change', page: number): void
}>()
</script>

<template>
  <div v-if="lastPage > 1" class="flex justify-center gap-2 mt-8">
    <Button
      variant="outline"
      size="sm"
      :disabled="currentPage <= 1"
      @click="emit('page-change', currentPage - 1)"
    >
      Previous
    </Button>
    <span class="flex items-center text-sm text-muted-foreground px-2">
      Page {{ currentPage }} of {{ lastPage }}
    </span>
    <Button
      variant="outline"
      size="sm"
      :disabled="currentPage >= lastPage"
      @click="emit('page-change', currentPage + 1)"
    >
      Next
    </Button>
  </div>
</template>
