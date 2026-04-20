<script lang="ts" setup>
import { faker } from '@faker-js/faker'

import { BasicPage } from '@/components/global-layout'
import { Button } from '@/components/ui/button'

interface Permission {
  id: number
  name: string
  guard_name: string
  description: string
  created_at: string
}

const permissions = ref<Permission[]>(Array.from({ length: 30 }, (_, i) => ({
  id: i + 1,
  name: faker.helpers.arrayElement([
    'view_any_role',
    'view_role',
    'create_role',
    'update_role',
    'delete_role',
    'view_any_user',
    'view_user',
    'create_user',
    'update_user',
    'delete_user',
    'view_dashboard',
    'manage_settings',
  ]),
  guard_name: 'web',
  description: faker.lorem.sentence(),
  created_at: faker.date.past().toISOString(),
})))

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
    accessorKey: 'created_at',
    header: 'Created',
  },
]
</script>

<template>
  <BasicPage title="Permissions" description="Manage system permissions" sticky>
    <template #actions>
      <Button>Sync Permissions</Button>
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
                <span class="text-muted-foreground text-sm">{{ permission.description }}</span>
              </td>
              <td class="px-4 py-3">
                <span class="text-muted-foreground text-sm">{{ new Date(permission.created_at).toLocaleDateString() }}</span>
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
