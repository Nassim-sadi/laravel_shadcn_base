<script setup lang="ts">
import type { useTableFilters } from '@/composables/use-table-filters'
import type { Table } from '@tanstack/vue-table'

import { computed } from 'vue'
import { XIcon } from '@lucide/vue'

import { Button } from '@/components/ui/button'
import { DropdownMenu, DropdownMenuContent, DropdownMenuItem, DropdownMenuTrigger } from '@/components/ui/dropdown-menu'
import { Input } from '@/components/ui/input'
import { useGetAllCatalogBrandsQuery } from '@/services/api/catalog.api'

import type { CatalogProduct } from './columns'

const props = defineProps<{
  table: Table<CatalogProduct>
  filters: ReturnType<typeof useTableFilters>
}>()

const { data: brandsData } = useGetAllCatalogBrandsQuery()
const brands = computed(() => {
  const d = brandsData.value as any
  return d?.data ?? []
})

const isFiltered = computed(() => props.filters.brand_id.value !== undefined)

function handleBrandChange(value: string) {
  const current = props.filters.brand_id.value
  props.filters.brand_id.value = current === value ? undefined : value
  props.filters.page.value = 1
}
</script>

<template>
  <div class="flex items-center flex-1 space-x-2">
    <Input
      v-model="filters.search.value"
      placeholder="Search products..."
      class="h-8 w-[150px] lg:w-[250px]"
    />
    <div class="relative">
      <DropdownMenu>
        <DropdownMenuTrigger as-child>
          <Button
            variant="outline"
            size="sm"
            class="h-8 text-xs"
            :class="{ 'border-primary': filters.brand_id.value !== undefined }"
          >
            Brand
          </Button>
        </DropdownMenuTrigger>
        <DropdownMenuContent align="start" class="min-w-[160px]">
          <DropdownMenuItem
            v-for="brand in brands"
            :key="brand.id"
            class="flex items-center gap-2"
            @click="handleBrandChange(String(brand.id))"
          >
            <span class="flex size-4 items-center justify-center rounded-sm border">
              <span v-if="filters.brand_id.value === String(brand.id)" class="size-2 rounded-sm bg-primary" />
            </span>
            {{ brand.name }}
          </DropdownMenuItem>
        </DropdownMenuContent>
      </DropdownMenu>
      <div
        v-if="filters.brand_id.value !== undefined"
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
</template>
