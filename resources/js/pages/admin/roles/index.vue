<script lang="ts" setup>
import { BasicPage } from '@/components/global-layout'
import { Button } from '@/components/ui/button'
import { useGetRolesQuery } from '@/services/api/roles.api'

const { data: rolesResponse, isLoading: _isLoading, refetch } = useGetRolesQuery()

const roles = computed(() => rolesResponse.value?.data ?? [])

const columns = [
  {
    accessorKey: 'name',
    header: 'Name',
  },
  {
    accessorKey: 'guard_name',
    header: 'Guard',
  },
  {
    accessorKey: 'description',
    header: 'Description',
  },
  {
    accessorKey: 'permissions',
    header: 'Permissions',
    cell: (row: any) => row.permissions?.length ?? 0,
  },
  {
    accessorKey: 'created_at',
    header: 'Created',
  },
]
</script>

<template>
  <BasicPage title="Roles" description="Manage user roles and permissions" sticky>
    <template #actions>
      <Button @click="refetch">Refresh</Button>
      <Button>Create Role</Button>
    </template>
    <div class="overflow-x-auto">
      <div class="rounded-md border">
        <table class="w-full">
          <thead>
            <tr class="border-b bg-muted/50">
              <th v-for="col in columns" :key="col.accessorKey" class="px-4 py-3 text-left text-sm font-medium">
                {{ col.header }}
              </th>
              <th class="px-4 py-3 text-right text-sm font-medium">
                Actions
              </th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="role in roles" :key="role.id" class="border-b">
              <td class="px-4 py-3">
                <span class="font-medium">{{ role.name }}</span>
              </td>
              <td class="px-4 py-3">
                <span class="text-muted-foreground text-sm">{{ role.guard_name }}</span>
              </td>
              <td class="px-4 py-3">
                <span class="text-muted-foreground text-sm">{{ role.description }}</span>
              </td>
              <td class="px-4 py-3">
                <span class="text-muted-foreground text-sm">{{ role.permissions?.length ?? 0 }}</span>
              </td>
              <td class="px-4 py-3">
                <span class="text-muted-foreground text-sm">{{ role.created_at ? new Date(role.created_at).toLocaleDateString() : '-' }}</span>
              </td>
              <td class="px-4 py-3 text-right">
                <Button variant="ghost" size="sm">
                  Edit
                </Button>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
  </BasicPage>
</template>