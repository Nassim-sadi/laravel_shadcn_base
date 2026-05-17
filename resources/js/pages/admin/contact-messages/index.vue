<script lang="ts" setup>
import { ReplyIcon, Trash2Icon } from '@lucide/vue'
import { computed, ref } from 'vue'
import { useI18n } from 'vue-i18n'

import { BasicPage } from '@/components/global-layout'
import ConfirmDialog from '@/components/confirm-dialog.vue'
import { Badge } from '@/components/ui/badge'
import { Button } from '@/components/ui/button'
import { Tooltip, TooltipContent, TooltipProvider, TooltipTrigger } from '@/components/ui/tooltip'
import { useDeleteContactMessageMutation, useGetContactMessagesQuery, useUpdateContactMessageMutation } from '@/services/api/contact-messages.api'
import { hasPermission } from '@/composables/use-role'
import Reply from './partials/Reply.vue'

const { t } = useI18n()

const { data: response, isLoading, refetch } = useGetContactMessagesQuery()
const items = computed(() => response.value?.data?.data ?? [])
const showReply = ref(false)
const replyingId = ref<number | null>(null)
const replyText = ref('')
const deleteTargetId = ref<number | null>(null)
const showDeleteDialog = ref(false)
const { mutate: deleteItem, isPending: isDeleting } = useDeleteContactMessageMutation()
const { mutate: updateItem } = useUpdateContactMessageMutation(0)

function markAsRead(item: any) {
  if (!item.is_read) {
    updateItem({ id: item.id, is_read: true } as any)
  }
}

function openReply(item: any) {
  replyingId.value = item.id
  replyText.value = item.reply || ''
  showReply.value = true
  markAsRead(item)
}

function sendReply() {
  if (replyingId.value) {
    updateItem({ id: replyingId.value, reply: replyText.value, replied_at: new Date().toISOString() } as any)
    showReply.value = false
  }
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
  <BasicPage :title="$t('admin.page.contactMessages.title')" :description="$t('admin.page.contactMessages.description')" sticky>
    <template #actions>
      <Button variant="outline" @click="refetch">
        {{ $t('admin.btn.refresh') }}
      </Button>
    </template>
    <div class="space-y-4">
      <div v-for="item in items" :key="item.id" class="flex items-start gap-4 rounded-lg border p-4" :class="{ 'border-l-2 border-l-primary': !item.is_read }" @click="markAsRead(item)">
        <div class="flex-1 space-y-1">
          <div class="flex items-center gap-2">
            <span class="font-medium">{{ item.name }}</span>
            <Badge v-if="!item.is_read" variant="default">
              {{ $t('admin.status.new') }}
            </Badge>
            <Badge v-if="item.replied_at" variant="outline">
              {{ $t('admin.status.replied') }}
            </Badge>
          </div>
          <p class="text-sm text-muted-foreground">
            {{ item.email }}{{ item.phone ? ` | ${item.phone}` : '' }}
          </p>
          <p class="text-sm">
            <strong>{{ item.subject }}</strong>
          </p>
          <p class="text-sm">
            {{ item.message?.slice(0, 200) }}{{ item.message?.length > 200 ? '...' : '' }}
          </p>
          <p class="text-xs text-muted-foreground">
            {{ new Date(item.created_at).toLocaleString() }}
          </p>
        </div>
        <TooltipProvider>
          <div class="flex gap-1">
            <Tooltip>
              <TooltipTrigger as-child>
                <Button v-if="hasPermission('contact-messages.edit')" variant="ghost" size="icon" class="size-8" @click="openReply(item)">
                  <ReplyIcon class="size-4" />
                </Button>
              </TooltipTrigger>
              <TooltipContent><p>{{ item.reply ? $t('admin.btn.editReply') : $t('admin.btn.reply') }}</p></TooltipContent>
            </Tooltip>
            <Tooltip>
              <TooltipTrigger as-child>
                <Button v-if="hasPermission('contact-messages.delete')" variant="destructive" size="icon" class="size-8" @click="confirmDelete(item.id)">
                  <Trash2Icon class="size-4" />
                </Button>
              </TooltipTrigger>
              <TooltipContent><p>{{ $t('admin.btn.delete') }}</p></TooltipContent>
            </Tooltip>
          </div>
        </TooltipProvider>
      </div>
      <div v-if="items.length === 0 && !isLoading" class="text-center py-8 text-muted-foreground">
        {{ $t('admin.empty.messages') }}
      </div>
    </div>
    <Reply v-model:open="showReply" :replyingId="replyingId" :initialReply="replyText" />

    <ConfirmDialog
      v-model:open="showDeleteDialog"
      :is-loading="isDeleting"
      :cancel-button-text="$t('admin.btn.cancel')"
      :confirm-button-text="$t('admin.btn.delete')"
      destructive
      @confirm="handleDelete"
    >
      <template #title>
        {{ $t('admin.dialog.deleteTitle', { item: $t('admin.nav.contactMessages') }) }}
      </template>
      <template #description>
        {{ $t('admin.dialog.deleteDescription', { item: $t('admin.nav.contactMessages').toLowerCase() }) }}
      </template>
    </ConfirmDialog>
  </BasicPage>
</template>
