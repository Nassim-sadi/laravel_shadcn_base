<script lang="ts" setup>
import { PencilIcon, Trash2Icon } from '@lucide/vue'
import { computed, ref } from 'vue'

import ConfirmDialog from '@/components/confirm-dialog.vue'
import { BasicPage } from '@/components/global-layout'
import { Badge } from '@/components/ui/badge'
import { Button } from '@/components/ui/button'
import { Tooltip, TooltipContent, TooltipProvider, TooltipTrigger } from '@/components/ui/tooltip'
import { useDeleteProjectMutation, useGetProjectsQuery } from '@/services/api/projects.api'
import { hasPermission } from '@/composables/use-role'
import Form from './partials/Form.vue'

const { data: response, isLoading, refetch } = useGetProjectsQuery()
const items = computed(() => response.value?.data?.data ?? [])
const showSheet = ref(false)
const editingId = ref<number | null>(null)
const editingItem = ref<any>(null)
const deleteTargetId = ref<number | null>(null)
const showDeleteDialog = ref(false)
const { mutate: deleteItem, isPending: isDeleting } = useDeleteProjectMutation()

function openCreate() {
  editingId.value = null
  editingItem.value = null
  showSheet.value = true
}

function openEdit(item: any) {
  editingId.value = item.id
  editingItem.value = item
  showSheet.value = true
}

function confirmDelete(id: number) {
  deleteTargetId.value = id
  showDeleteDialog.value = true
}

function handleDelete() {
  if (deleteTargetId.value !== null) {
    deleteItem(deleteTargetId.value)
  }
  showDeleteDialog.value = false
  deleteTargetId.value = null
}
</script>

<template>
  <BasicPage :title="$t('admin.page.projects.title')" :description="$t('admin.page.projects.description')" sticky>
    <template #actions>
      <Button variant="outline" @click="refetch">
        {{ $t('admin.btn.refresh') }}
      </Button>
      <Button v-if="hasPermission('projects.create')" @click="openCreate">
        {{ $t('admin.sheet.createProject') }}
      </Button>
    </template>
    <div class="space-y-4">
      <div v-for="item in items" :key="item.id" class="flex items-start gap-4 rounded-lg border p-4">
        <div class="flex-1 space-y-1">
          <div class="flex items-center gap-2">
            <span class="font-medium">{{ item.title }}</span>
            <Badge :variant="item.is_active ? 'default' : 'secondary'">
              {{ item.is_active ? $t('admin.status.active') : $t('admin.status.inactive') }}
            </Badge>
          </div>
          <p class="text-sm text-muted-foreground">
            {{ item.description?.slice(0, 100) || $t('admin.misc.noDescription') }}
          </p>
          <p class="text-xs text-muted-foreground">
            {{ $t('admin.misc.clientLabel', { value: item.client || '-' }) }} | {{ $t('admin.misc.orderLabel', { value: item.order }) }}
          </p>
          <div v-if="item.technologies?.length" class="flex gap-1 flex-wrap">
            <Badge v-for="tech in item.technologies" :key="tech" variant="outline">
              {{ tech }}
            </Badge>
          </div>
        </div>
        <TooltipProvider>
          <div class="flex gap-1">
            <Tooltip>
              <TooltipTrigger as-child>
                <Button v-if="hasPermission('projects.edit')" variant="ghost" size="icon" class="size-8" @click="openEdit(item)">
                  <PencilIcon class="size-4" />
                </Button>
              </TooltipTrigger>
              <TooltipContent><p>{{ $t('admin.btn.edit') }}</p></TooltipContent>
            </Tooltip>
            <Tooltip>
              <TooltipTrigger as-child>
                <Button v-if="hasPermission('projects.delete')" variant="destructive" size="icon" class="size-8" @click="confirmDelete(item.id)">
                  <Trash2Icon class="size-4" />
                </Button>
              </TooltipTrigger>
              <TooltipContent><p>{{ $t('admin.btn.delete') }}</p></TooltipContent>
            </Tooltip>
          </div>
        </TooltipProvider>
      </div>
      <div v-if="items.length === 0 && !isLoading" class="text-center py-8 text-muted-foreground">
        {{ $t('admin.empty.projects') }}
      </div>
    </div>

    <ConfirmDialog
      v-model:open="showDeleteDialog"
      :is-loading="isDeleting"
      :cancel-button-text="$t('admin.btn.cancel')"
      :confirm-button-text="$t('admin.btn.delete')"
      destructive
      @confirm="handleDelete"
    >
      <template #title>
        {{ $t('admin.dialog.deleteTitle', { item: $t('admin.nav.projects') }) }}
      </template>
      <template #description>
        {{ $t('admin.dialog.deleteDescription', { item: $t('admin.nav.projects').toLowerCase() }) }}
      </template>
    </ConfirmDialog>

    <Form v-model:open="showSheet" :editingId="editingId" :item="editingItem" />
  </BasicPage>
</template>
