<script setup lang="ts">
import { computed } from 'vue'

import { hasPermission } from '@/composables/use-role'
import { BasicPage } from '@/components/global-layout'
import { useGetUsersQuery } from '@/services/api/users.api'

import { columns } from './components/columns'
import DataTable from './components/data-table.vue'
import UserCreate from './components/user-create.vue'
import UserInvite from './components/user-invite.vue'

const { data, isLoading, error: _error, refetch } = useGetUsersQuery()

const users = computed(() => {
  const d = data.value as any
  return d?.data ?? []
})
const loading = isLoading
</script>

<template>
  <BasicPage
    :title="$t('admin.page.users.title')"
    :description="$t('admin.page.users.description')"
    sticky
  >
    <template #actions>
      <UserInvite v-if="hasPermission('users.create')" />
      <UserCreate v-if="hasPermission('users.create')" />
    </template>
    <div class="overflow-x-auto">
      <DataTable :loading="loading" :data="users" :columns="columns" @refresh="refetch" />
    </div>
  </BasicPage>
</template>
