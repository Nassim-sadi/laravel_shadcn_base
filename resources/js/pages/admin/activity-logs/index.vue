<script lang="ts" setup>
import { faker } from '@faker-js/faker'

import { BasicPage } from '@/components/global-layout'
import { Badge } from '@/components/ui/badge'
import { Button } from '@/components/ui/button'

interface ActivityLog {
  id: number
  log_name: string
  description: string
  event: string
  subject_type?: string
  causer_name?: string
  properties?: Record<string, unknown>
  created_at: string
}

const logs = ref<ActivityLog[]>(Array.from({ length: 20 }, (_, i) => ({
  id: i + 1,
  log_name: faker.helpers.arrayElement(['default', 'admin', 'auth']),
  description: faker.helpers.arrayElement([
    'created a new user',
    'updated role permissions',
    'deleted a task',
    'logged in',
    'changed settings',
    'exported data',
  ]),
  event: faker.helpers.arrayElement(['created', 'updated', 'deleted', 'restored']),
  subject_type: faker.helpers.arrayElement(['App\\Models\\User', 'App\\Models\\Role', 'App\\Models\\Task']),
  causer_name: faker.person.fullName(),
  properties: { ip: faker.internet.ipv4() },
  created_at: faker.date.recent().toISOString(),
})))

function getEventColor(event: string) {
  switch (event) {
    case 'created': return 'default'
    case 'updated': return 'secondary'
    case 'deleted': return 'destructive'
    case 'restored': return 'outline'
    default: return 'default'
  }
}
</script>

<template>
  <BasicPage title="Activity Logs" description="View system activity history" sticky>
    <template #actions>
      <Button variant="outline">
        Export
      </Button>
    </template>
    <div class="space-y-4">
      <div v-for="log in logs" :key="log.id" class="flex items-start gap-4 rounded-lg border p-4">
        <div class="flex-1 space-y-1">
          <div class="flex items-center gap-2">
            <span class="font-medium">{{ log.description }}</span>
            <Badge :variant="getEventColor(log.event)">
              {{ log.event }}
            </Badge>
          </div>
          <p class="text-sm text-muted-foreground">
            {{ log.subject_type }} • by {{ log.causer_name }} • {{ new Date(log.created_at).toLocaleString() }}
          </p>
        </div>
      </div>
    </div>
  </BasicPage>
</template>
