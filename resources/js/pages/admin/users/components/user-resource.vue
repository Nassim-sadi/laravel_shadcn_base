<script lang="ts" setup>
import { useI18n } from 'vue-i18n'

import { ModalDescription, ModalHeader, ModalTitle } from '@/components/prop-ui/modal'

import type { User } from '../data/schema'

import UserForm from './user-form.vue'

const props = defineProps<{
  user?: User
}>()
defineEmits(['close'])

const user = computed(() => props.user)
const { t } = useI18n()
const title = computed(() => user.value?.id ? `${t('admin.btn.edit')} ${t('admin.page.users.title')}` : `${t('admin.btn.create')} ${t('admin.page.users.title')}`)
const description = computed(() => user.value?.id ? `${t('admin.btn.edit')} ${user.value.name}` : `${t('admin.btn.create')} ${t('admin.page.users.title').toLowerCase()}`)
</script>

<template>
  <div>
    <ModalHeader>
      <ModalTitle>
        {{ title }}
      </ModalTitle>
      <ModalDescription>
        {{ description }}
      </ModalDescription>
    </ModalHeader>

    <UserForm :user="user" @close="$emit('close')" />
  </div>
</template>
