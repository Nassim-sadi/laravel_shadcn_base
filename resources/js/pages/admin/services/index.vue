<script lang="ts" setup>
import { PencilIcon, Trash2Icon } from '@lucide/vue'
import { computed, ref } from 'vue'

import ConfirmDialog from '@/components/confirm-dialog.vue'
import { BasicPage } from '@/components/global-layout'
import { Badge } from '@/components/ui/badge'
import { Button } from '@/components/ui/button'
import { Tooltip, TooltipContent, TooltipProvider, TooltipTrigger } from '@/components/ui/tooltip'
import { useDeleteServiceMutation, useGetServicesQuery } from '@/services/api/services.api'
import { hasPermission } from '@/composables/use-role'
import Form from './partials/Form.vue'

const { data: response, isLoading, refetch } = useGetServicesQuery()
const services = computed(() => response.value?.data?.data ?? [])

const showSheet = ref(false)
const editingId = ref<number | null>(null)
const editingItem = ref<any>(null)
const deleteTargetId = ref<number | null>(null)
const showDeleteDialog = ref(false)
const { mutate: deleteService, isPending: isDeleting } = useDeleteServiceMutation()

function openCreate() {
  editingId.value = null
  editingItem.value = null
  showSheet.value = true
}

function openEdit(service: any) {
  editingId.value = service.id
  editingItem.value = service
  showSheet.value = true
}

function confirmDelete(id: number) {
  deleteTargetId.value = id
  showDeleteDialog.value = true
}

function handleDelete() {
  if (deleteTargetId.value !== null) {
    deleteService(deleteTargetId.value)
  }
  showDeleteDialog.value = false
  deleteTargetId.value = null
}
</script>

<template>
  <BasicPage :title="$t('admin.page.services.title')" :description="$t('admin.page.services.description')" sticky>
    <template #actions>
      <Button variant="outline" @click="refetch">
        {{ $t('admin.btn.refresh') }}
      </Button>
      <Button v-if="hasPermission('services.create')" @click="openCreate">
        {{ $t('admin.sheet.createService') }}
      </Button>
    </template>
    <div class="space-y-4">
      <div v-for="service in services" :key="service.id" class="flex items-start gap-4 rounded-lg border p-4">
        <div class="flex-1 space-y-1">
          <div class="flex items-center gap-2">
            <span class="font-medium">{{ service.title }}</span>
            <Badge :variant="service.is_active ? 'default' : 'secondary'">
              {{ service.is_active ? $t('admin.status.active') : $t('admin.status.inactive') }}
            </Badge>
          </div>
          <p class="text-sm text-muted-foreground">
            {{ service.description?.slice(0, 100) ?? $t('admin.misc.noDescription') }}
          </p>
          <p class="text-xs text-muted-foreground">
            {{ $t('admin.misc.orderLabel', { value: service.order }) }} | {{ $t('admin.misc.iconLabel', { value: service.icon || '-' }) }}
          </p>
        </div>
        <TooltipProvider>
          <div class="flex gap-1">
            <Tooltip>
              <TooltipTrigger as-child>
                <Button v-if="hasPermission('services.edit')" variant="ghost" size="icon" class="size-8" @click="openEdit(service)">
                  <PencilIcon class="size-4" />
                </Button>
              </TooltipTrigger>
              <TooltipContent><p>{{ $t('admin.btn.edit') }}</p></TooltipContent>
            </Tooltip>
            <Tooltip>
              <TooltipTrigger as-child>
                <Button v-if="hasPermission('services.delete')" variant="destructive" size="icon" class="size-8" @click="confirmDelete(service.id)">
                  <Trash2Icon class="size-4" />
                </Button>
              </TooltipTrigger>
              <TooltipContent><p>{{ $t('admin.btn.delete') }}</p></TooltipContent>
            </Tooltip>
          </div>
        </TooltipProvider>
      </div>
      <div v-if="services.length === 0 && !isLoading" class="text-center py-8 text-muted-foreground">
        {{ $t('admin.empty.services') }}
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
        {{ $t('admin.dialog.deleteTitle', { item: $t('admin.nav.services') }) }}
      </template>
      <template #description>
        {{ $t('admin.dialog.deleteDescription', { item: $t('admin.nav.services').toLowerCase() }) }}
      </template>
    </ConfirmDialog>

    <Form v-model:open="showSheet" :editingId="editingId" :item="editingItem" />
  </BasicPage>
</template>
