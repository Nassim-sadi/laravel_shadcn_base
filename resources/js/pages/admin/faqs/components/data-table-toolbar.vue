<script setup lang="ts">
import type { Table } from '@tanstack/vue-table'
import type { useTableFilters } from '@/composables/use-table-filters'

import { XIcon } from '@lucide/vue'
import { computed } from 'vue'

import { DataTableViewOptions } from '@/components/data-table'
import { Button } from '@/components/ui/button'
import { DropdownMenu, DropdownMenuContent, DropdownMenuItem, DropdownMenuTrigger } from '@/components/ui/dropdown-menu'
import { Input } from '@/components/ui/input'

import type { Faq } from './columns'

interface DataTableToolbarProps {
  table: Table<Faq>
  filters: ReturnType<typeof useTableFilters>
}

const props = defineProps<DataTableToolbarProps>()

const isFiltered = computed(() => props.filters.hasFilters())

const statusOptions = [
  { label: 'Active', value: 'true' },
  { label: 'Inactive', value: 'false' },
]

function handleStatusChange(value: string) {
  const current = props.filters.is_active.value
  props.filters.is_active.value = current === value ? undefined : value
  props.filters.page.value = 1
}
</script>

<template>
  <div class="flex items-center justify-between">
    <div class="flex items-center flex-1 space-x-2">
      <Input
        placeholder="Filter FAQs..."
        :model-value="filters.searchInput.value"
        class="h-8 w-[150px] lg:w-[250px]"
        @input="filters.searchInput.value = ($event.target as HTMLInputElement).value"
      />
      <div class="relative">
        <DropdownMenu>
          <DropdownMenuTrigger as-child>
            <Button
              variant="outline"
              size="sm"
              class="h-8 text-xs"
              :class="{ 'border-primary': filters.is_active.value !== undefined }"
            >
              Status
            </Button>
          </DropdownMenuTrigger>
          <DropdownMenuContent align="start" class="min-w-[160px]">
            <DropdownMenuItem
              v-for="option in statusOptions"
              :key="option.value"
              class="flex items-center gap-2"
              @click="handleStatusChange(option.value)"
            >
              <span class="flex size-4 items-center justify-center rounded-sm border">
                <span v-if="filters.is_active.value === option.value" class="size-2 rounded-sm bg-primary" />
              </span>
              {{ option.label }}
            </DropdownMenuItem>
          </DropdownMenuContent>
        </DropdownMenu>
        <div
          v-if="filters.is_active.value !== undefined"
          class="absolute -top-1 -end-1 size-2 rounded-full bg-primary"
        />
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
