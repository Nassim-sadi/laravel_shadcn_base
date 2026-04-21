<script lang="ts" setup>
import { toast } from 'vue-sonner'

import { ModalClose, ModalDescription, ModalFooter, ModalHeader, ModalTitle } from '@/components/prop-ui/modal'
import { Button } from '@/components/ui/button'

import type { User } from '../data/schema'
import { useDeleteUserMutation } from '@/services/api/users.api'

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
    toast.success('User deleted successfully')
    emit('remove')
    emit('close')
  }
  catch (error: any) {
    toast.error(error.message ?? 'Failed to delete user')
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
          Cancel
        </Button>
      </ModalClose>

      <Button variant="destructive" @click="handleRemove" :disabled="deleteUserMutation.isPending.value">
        {{ deleteUserMutation.isPending.value ? 'Deleting...' : 'Delete' }}
      </Button>
    </ModalFooter>
  </div>
</template>