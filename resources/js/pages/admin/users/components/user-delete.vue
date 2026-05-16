<script lang="ts" setup>
import { toast } from 'vue-sonner'
import { useI18n } from 'vue-i18n'

import { ModalClose, ModalDescription, ModalFooter, ModalHeader, ModalTitle } from '@/components/prop-ui/modal'
import { Button } from '@/components/ui/button'

import type { User } from '../data/schema'
import { useDeleteUserMutation } from '@/services/api/users.api'

const { t } = useI18n()

const props = defineProps<{
  user: User
}>()

const emit = defineEmits<{
  (e: 'remove'): void
  (e: 'close'): void
}>()

const deleteUserMutation = useDeleteUserMutation()

async function handleRemove() {
  try {
    await deleteUserMutation.mutateAsync(props.user.id)
    toast.success(t('admin.toast.userDeleted'))
    emit('remove')
    emit('close')
  }
  catch (error: any) {
    toast.error(error.message ?? t('admin.toast.userDeleteFailed'))
  }
}
</script>

<template>
  <div>
    <ModalHeader>
      <ModalTitle>
        Delete this user: {{ user.name }} ?
      </ModalTitle>

      <ModalDescription>
        You are about to delete a user with the ID {{ user.id }}. This action cannot be undone.
      </ModalDescription>
    </ModalHeader>

    <ModalFooter>
      <ModalClose as-child>
        <Button variant="outline">
          {{ $t('admin.btn.cancel') }}
        </Button>
      </ModalClose>

      <Button variant="destructive" @click="handleRemove" :disabled="deleteUserMutation.isPending.value">
        {{ deleteUserMutation.isPending.value ? $t('admin.misc.deleting') : $t('admin.btn.delete') }}
      </Button>
    </ModalFooter>
  </div>
</template>