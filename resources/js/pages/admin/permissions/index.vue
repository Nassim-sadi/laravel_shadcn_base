<script lang="ts" setup>
import { BasicPage } from '@/components/global-layout'
import { Button } from '@/components/ui/button'
import { useGetPermissionsQuery } from '@/services/api/permissions.api'

const { data: permissionsResponse, isLoading: _isLoading, refetch } = useGetPermissionsQuery()

const permissions = computed(() => permissionsResponse.value?.data?.data ?? [])

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
    accessorKey: 'group',
    header: 'Group',
  },
  {
    accessorKey: 'created_at',
    header: 'Created',
  },
]
</script>

<template>
  <BasicPage title="Permissions" description="Manage system permissions" sticky>
    <template #actions>
      <Button @click="refetch">Refresh</Button>
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
            <tr v-for="permission in permissions" :key="permission.id" class="border-b">
              <td class="px-4 py-3">
                <code class="text-sm bg-muted px-2 py-1 rounded">{{ permission.name }}</code>
              </td>
              <td class="px-4 py-3">
                <span class="text-muted-foreground text-sm">{{ permission.guard_name }}</span>
              </td>
              <td class="px-4 py-3">
                <span class="text-muted-foreground text-sm">{{ permission.group ?? '-' }}</span>
              </td>
              <td class="px-4 py-3">
                <span class="text-muted-foreground text-sm">{{ permission.created_at ? new Date(permission.created_at).toLocaleDateString() : '-' }}</span>
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