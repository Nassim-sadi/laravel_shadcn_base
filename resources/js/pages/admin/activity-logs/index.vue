<script lang="ts" setup>
import { BasicPage } from '@/components/global-layout'
import { Badge } from '@/components/ui/badge'
import { Button } from '@/components/ui/button'
import { useGetActivityLogsQuery } from '@/services/api/activity-log.api'

const { data: logsResponse, isLoading, refetch } = useGetActivityLogsQuery()

const logs = computed(() => logsResponse.value?.data?.data ?? [])

function getEventColor(event: string | null) {
  switch (event) {
    case 'created': return 'default'
    case 'updated': return 'secondary'
    case 'deleted': return 'destructive'
    case 'restored': return 'outline'
    default: return 'outline'
  }
}
</script>

<template>
  <BasicPage :title="$t('admin.page.activityLogs.title')" :description="$t('admin.page.activityLogs.description')" sticky>
    <template #actions>
      <Button variant="outline" @click="refetch">
        {{ $t('admin.btn.refresh') }}
      </Button>
    </template>
    <div class="space-y-4">
      <div v-for="log in logs" :key="log.id" class="flex items-start gap-4 rounded-lg border p-4">
        <div class="flex-1 space-y-1">
          <div class="flex items-center gap-2">
            <span class="font-medium">{{ log.description }}</span>
            <Badge :variant="getEventColor(log.event)">
              {{ log.event ?? 'action' }}
            </Badge>
          </div>
          <p class="text-sm text-muted-foreground">
            {{ log.subject_type ?? 'N/A' }}
            <span v-if="log.user"> • by {{ log.user.name ?? log.user.email }}</span>
            • {{ new Date(log.created_at).toLocaleString() }}
          </p>
        </div>
      </div>
      <div v-if="logs.length === 0 && !isLoading" class="text-center py-8 text-muted-foreground">
        {{ $t('admin.empty.activityLogs') }}
      </div>
    </div>
  </BasicPage>
</template>
