<script setup lang="ts">
import { computed } from 'vue'
import { BasicPage } from '@/components/global-layout'
import { useGetUsersQuery } from '@/services/api/users.api'

import { columns } from './components/columns'
import DataTable from './components/data-table.vue'
import UserCreate from './components/user-create.vue'
import UserInvite from './components/user-invite.vue'

const { data, isLoading, error, refetch } = useGetUsersQuery()

const users = computed(() => {
  console.log('FULL RESPONSE:', data.value)
  const d = data.value as any
  return d?.data ?? []
})
const loading = isLoading
</script>

<template>
  <BasicPage
    title="Users"
    description="Manage your team members and their account permissions"
    sticky
  >
    <template #actions>
      <UserInvite />
      <UserCreate />
    </template>
    <div class="overflow-x-auto">
      <DataTable :loading="loading" :data="users" :columns="columns" @refresh="refetch" />
    </div>
  </BasicPage>
</template>