<script setup lang="ts">
import type { Row } from '@tanstack/vue-table'
import type { Component } from 'vue'

import { PencilIcon, Trash2Icon } from '@lucide/vue'

import { Tooltip, TooltipContent, TooltipProvider, TooltipTrigger } from '@/components/ui/tooltip'

import { Modal, ModalContent } from '@/components/prop-ui/modal'

import type { User } from '../data/schema'

interface DataTableRowActionsProps {
  row: Row<User>
}
const props = defineProps<DataTableRowActionsProps>()
const user = computed(() => props.row.original)
const isOpen = ref(false)

const showComponent = shallowRef<Component | null>(null)
type TCommand = 'edit' | 'delete'

const componentLoader: Record<TCommand, () => Promise<{ default: Component }>> = {
  edit: () => import('./user-resource.vue'),
  delete: () => import('./user-delete.vue'),
}

async function handleSelect(command: TCommand) {
  try {
    const { default: component } = await componentLoader[command]()
    showComponent.value = component
    isOpen.value = true
  }
  catch (e) {
    console.error(`Failed to load component for "${command}"`, e)
  }
}
</script>

<template>
  <Modal v-model:open="isOpen">
    <TooltipProvider>
      <div class="flex gap-1">
        <Tooltip>
          <TooltipTrigger as-child>
            <UiButton variant="ghost" size="icon" class="size-8" @click.stop="handleSelect('edit')">
              <PencilIcon class="size-4" />
            </UiButton>
          </TooltipTrigger>
          <TooltipContent><p>{{ $t('admin.btn.edit') }}</p></TooltipContent>
        </Tooltip>
        <Tooltip>
          <TooltipTrigger as-child>
            <UiButton variant="destructive" size="icon" class="size-8" @click.stop="handleSelect('delete')">
              <Trash2Icon class="size-4" />
            </UiButton>
          </TooltipTrigger>
          <TooltipContent><p>{{ $t('admin.btn.delete') }}</p></TooltipContent>
        </Tooltip>
      </div>
    </TooltipProvider>

    <ModalContent>
      <component :is="showComponent" :user="user" @close="isOpen = false" />
    </ModalContent>
  </Modal>
</template>
