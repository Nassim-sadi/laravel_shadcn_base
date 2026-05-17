<script setup lang="ts">
import { ref, watch } from 'vue'
import { Button } from '@/components/ui/button'
import { Dialog, DialogContent, DialogDescription, DialogFooter, DialogHeader, DialogTitle } from '@/components/ui/dialog'
import { Label } from '@/components/ui/label'
import { Textarea } from '@/components/ui/textarea'
import { useUpdateContactMessageMutation } from '@/services/api/contact-messages.api'

const props = defineProps<{
  replyingId: number | null
  initialReply: string
}>()

const open = defineModel<boolean>('open', { default: false })

const replyText = ref('')
const { mutate: updateItem } = useUpdateContactMessageMutation(0)

watch(() => props.open, (isOpen) => {
  if (isOpen) {
    replyText.value = props.initialReply || ''
  }
})

function sendReply() {
  if (props.replyingId) {
    updateItem({ id: props.replyingId, reply: replyText.value, replied_at: new Date().toISOString() } as any)
    open.value = false
  }
}
</script>

<template>
  <Dialog v-model:open="open">
    <DialogContent>
      <DialogHeader>
        <DialogTitle>{{ $t('admin.sheet.replyMessage') }}</DialogTitle>
        <DialogDescription class="sr-only">
          {{ $t('admin.misc.writeReply') }}
        </DialogDescription>
      </DialogHeader>
      <div class="space-y-4">
        <div><Label>{{ $t('admin.label.reply') }}</Label><Textarea v-model="replyText" :placeholder="$t('admin.misc.replyPlaceholder')" rows="6" /></div>
      </div>
      <DialogFooter>
        <Button variant="outline" @click="open = false">
          {{ $t('admin.btn.cancel') }}
        </Button>
        <Button @click="sendReply">
          {{ $t('admin.btn.sendReply') }}
        </Button>
      </DialogFooter>
    </DialogContent>
  </Dialog>
</template>
