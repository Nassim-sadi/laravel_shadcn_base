<script lang="ts" setup>
import { faker } from '@faker-js/faker'

import { BasicPage } from '@/components/global-layout'
import { Button } from '@/components/ui/button'

interface Role {
  id: number
  name: string
  guard_name: string
  description: string
  permissions_count: number
  created_at: string
}

const roles = ref<Role[]>(Array.from({ length: 10 }, (_, i) => ({
  id: i + 1,
  name: faker.helpers.arrayElement(['super_admin', 'admin', 'manager', 'editor', 'viewer']),
  guard_name: 'web',
  description: faker.lorem.sentence(),
  permissions_count: faker.number.int({ min: 1, max: 20 }),
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
    accessorKey: 'permissions_count',
    header: 'Permissions',
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
      <Button>Create Role</Button>
    </template>
    <div class="overflow-x-auto">
      <!-- <DataTable :columns :data :loading :table /> -->
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
                <span class="text-muted-foreground text-sm">{{ role.permissions_count }}</span>
              </td>
              <td class="px-4 py-3">
                <span class="text-muted-foreground text-sm">{{ new Date(role.created_at).toLocaleDateString() }}</span>
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
