<script lang="ts" setup>
import { UserRoundPlusIcon } from '@lucide/vue'

import { hasPermission } from '@/composables/use-role'
import { Modal, ModalContent, ModalTrigger } from '@/components/prop-ui/modal'

import UserResource from './user-resource.vue'

const isOpen = ref(false)
</script>

<template>
  <Modal v-if="hasPermission('users.create')" v-model:open="isOpen">
    <ModalTrigger as-child>
      <UiButton>
        <UserRoundPlusIcon />
        {{ $t('admin.btn.create') }} {{ $t('admin.page.users.title') }}
      </UiButton>
    </ModalTrigger>

    <ModalContent>
      <UserResource @close="isOpen = false" />
    </ModalContent>
  </Modal>
</template>
