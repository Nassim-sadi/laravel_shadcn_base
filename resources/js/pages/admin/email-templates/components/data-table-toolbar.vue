<script setup lang="ts">
import type { Table } from '@tanstack/vue-table'
import type { useTableFilters } from '@/composables/use-table-filters'

import { XIcon } from '@lucide/vue'
import { computed, ref, nextTick, watch, onBeforeUnmount } from 'vue'

import { DataTableViewOptions } from '@/components/data-table'
import { Button } from '@/components/ui/button'
import { Input } from '@/components/ui/input'

import type { EmailTemplate } from './columns'

interface DataTableToolbarProps {
  table: Table<EmailTemplate>
  filters: ReturnType<typeof useTableFilters>
}

const props = defineProps<DataTableToolbarProps>()

const isFiltered = computed(() => props.filters.hasFilters())

const showStatusDropdown = ref(false)
const buttonRef = ref<HTMLElement | null>(null)
const dropdownPosition = ref({ top: '0px', left: '0px' })

const statusOptions = [
  { label: 'Active', value: 'true' },
  { label: 'Inactive', value: 'false' },
]

function handleStatusChange(value: string) {
  const current = props.filters.is_active.value
  props.filters.is_active.value = current === value ? undefined : value
  props.filters.page.value = 1
  showStatusDropdown.value = false
}

function updateDropdownPosition() {
  if (buttonRef.value) {
    const rect = buttonRef.value.getBoundingClientRect()
    dropdownPosition.value = {
      top: `${rect.bottom + 4}px`,
      left: `${rect.left}px`,
    }
  }
}

function handleScroll() {
  if (showStatusDropdown.value) updateDropdownPosition()
}

watch(showStatusDropdown, (val) => {
  if (val) {
    nextTick(() => updateDropdownPosition())
    window.addEventListener('scroll', handleScroll, true)
    window.addEventListener('resize', handleScroll)
  } else {
    window.removeEventListener('scroll', handleScroll, true)
    window.removeEventListener('resize', handleScroll)
  }
})

onBeforeUnmount(() => {
  window.removeEventListener('scroll', handleScroll, true)
  window.removeEventListener('resize', handleScroll)
})
</script>

<template>
  <div class="flex items-center justify-between">
    <div class="flex items-center flex-1 space-x-2">
      <Input
        placeholder="Filter templates..."
        :model-value="filters.searchInput.value"
        class="h-8 w-[150px] lg:w-[250px]"
        @input="filters.searchInput.value = ($event.target as HTMLInputElement).value"
      />
      <div class="relative">
        <Button
          ref="buttonRef"
          variant="outline"
          size="sm"
          class="h-8 text-xs"
          :class="{ 'border-primary': filters.is_active.value !== undefined }"
          @click="showStatusDropdown = !showStatusDropdown"
        >
          Status
        </Button>
        <div
          v-if="filters.is_active.value !== undefined"
          class="absolute -top-1 -end-1 size-2 rounded-full bg-primary"
        />
        <Teleport to="body">
          <div
            v-if="showStatusDropdown"
            class="absolute z-50 min-w-[160px] rounded-md border bg-popover p-2 shadow-md"
            :style="dropdownPosition"
          >
            <button
              v-for="option in statusOptions"
              :key="option.value"
              class="flex w-full items-center gap-2 rounded-sm px-2 py-1.5 text-sm hover:bg-accent"
              :class="{ 'bg-accent': filters.is_active.value === option.value }"
              @click="handleStatusChange(option.value)"
            >
              <span class="size-4 rounded-sm border flex items-center justify-center">
                <span v-if="filters.is_active.value === option.value" class="size-2 rounded-sm bg-primary" />
              </span>
              {{ option.label }}
            </button>
          </div>
        </Teleport>
      </div>

      <Button
        v-if="isFiltered"
        variant="ghost"
        class="h-8 px-2 lg:px-3"
        @click="filters.reset()"
      >
        Reset
        <XIcon class="size-4 ml-2" />
      </Button>
    </div>
    <DataTableViewOptions :table="table" />
  </div>
</template>
