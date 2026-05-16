<script lang="ts" setup>
import { computed, ref } from 'vue'
import { BasicPage } from '@/components/global-layout'
import { Button } from '@/components/ui/button'
import { Badge } from '@/components/ui/badge'
import { useGetContactMessagesQuery, useDeleteContactMessageMutation, useUpdateContactMessageMutation } from '@/services/api/contact-messages.api'
import { Textarea } from '@/components/ui/textarea'
import { Dialog, DialogContent, DialogHeader, DialogTitle, DialogDescription, DialogFooter } from '@/components/ui/dialog'
import { Label } from '@/components/ui/label'

const { data: response, isLoading, refetch } = useGetContactMessagesQuery()
const items = computed(() => response.value?.data?.data ?? [])
const showReply = ref(false)
const replyingId = ref<number | null>(null)
const replyText = ref('')
const { mutate: deleteItem } = useDeleteContactMessageMutation()
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
  if (confirm('Are you sure?')) deleteItem(id)
}
</script>

<template>
  <BasicPage title="Contact Messages" description="Manage contact form submissions" sticky>
    <template #actions>
      <Button @click="refetch" variant="outline">Refresh</Button>
    </template>
    <div class="space-y-4">
      <div v-for="item in items" :key="item.id" class="flex items-start gap-4 rounded-lg border p-4" :class="{ 'border-l-2 border-l-primary': !item.is_read }" @click="markAsRead(item)">
        <div class="flex-1 space-y-1">
          <div class="flex items-center gap-2">
            <span class="font-medium">{{ item.name }}</span>
            <Badge v-if="!item.is_read" variant="default">New</Badge>
            <Badge v-if="item.replied_at" variant="outline">Replied</Badge>
          </div>
          <p class="text-sm text-muted-foreground">{{ item.email }}{{ item.phone ? ` | ${item.phone}` : '' }}</p>
          <p class="text-sm"><strong>{{ item.subject }}</strong></p>
          <p class="text-sm">{{ item.message?.slice(0, 200) }}{{ item.message?.length > 200 ? '...' : '' }}</p>
          <p class="text-xs text-muted-foreground">{{ new Date(item.created_at).toLocaleString() }}</p>
        </div>
        <div class="flex gap-2">
          <Button variant="ghost" size="sm" @click="openReply(item)">{{ item.reply ? 'Edit Reply' : 'Reply' }}</Button>
          <Button variant="destructive" size="sm" @click="confirmDelete(item.id)">Delete</Button>
        </div>
      </div>
      <div v-if="items.length === 0 && !isLoading" class="text-center py-8 text-muted-foreground">No messages found</div>
    </div>
    <Dialog v-model:open="showReply">
      <DialogContent>
        <DialogHeader>
          <DialogTitle>Reply to Message</DialogTitle>
          <DialogDescription class="sr-only">Write a reply to this contact message.</DialogDescription>
        </DialogHeader>
        <div class="space-y-4">
          <div><Label>Reply</Label><Textarea v-model="replyText" placeholder="Type your reply..." rows="6" /></div>
        </div>
        <DialogFooter>
          <Button variant="outline" @click="showReply = false">Cancel</Button>
          <Button @click="sendReply">Send Reply</Button>
        </DialogFooter>
      </DialogContent>
    </Dialog>
  </BasicPage>
</template>
