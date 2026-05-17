<script setup lang="ts">
import { computed, ref } from 'vue'
import { EyeIcon } from '@lucide/vue'

import { BasicPage } from '@/components/global-layout'
import {
  Avatar,
  AvatarFallback,
  AvatarImage,
} from '@/components/ui/avatar'
import { Badge } from '@/components/ui/badge'
import { Button } from '@/components/ui/button'
import {
  Table,
  TableBody,
  TableCell,
  TableHead,
  TableHeader,
  TableRow,
} from '@/components/ui/table'
import { Tooltip, TooltipContent, TooltipProvider, TooltipTrigger } from '@/components/ui/tooltip'
import type { IActivityLog } from '@/services/api/activity-log.api'
import { useGetActivityLogsQuery } from '@/services/api/activity-log.api'
import Detail from './partials/Detail.vue'

const { data: logsResponse, isLoading, refetch } = useGetActivityLogsQuery()
const logs = computed(() => logsResponse.value?.data ?? [])

const selectedLog = ref<IActivityLog | null>(null)
const detailOpen = ref(false)

function viewDetails(log: IActivityLog) {
  selectedLog.value = log
  detailOpen.value = true
}

function getInitials(name: string | undefined) {
  if (!name) return 'S'
  return name
    .split(' ')
    .map(part => part.charAt(0))
    .join('')
    .toUpperCase()
    .slice(0, 2)
}

function getEventColor(event: string | null) {
  switch (event) {
    case 'created': return 'default'
    case 'updated': return 'secondary'
    case 'deleted': return 'destructive'
    case 'restored': return 'outline'
    default: return 'outline'
  }
}

function shortSubjectType(type: string | undefined) {
  if (!type) return '—'
  const parts = type.split('\\')
  return parts[parts.length - 1] ?? type
}
</script>

<template>
  <BasicPage :title="$t('admin.page.activityLogs.title')" :description="$t('admin.page.activityLogs.description')" sticky>
    <template #actions>
      <Button variant="outline" @click="refetch">
        {{ $t('admin.btn.refresh') }}
      </Button>
    </template>

    <div class="overflow-x-auto rounded-lg border">
      <Table>
        <TableHeader>
          <TableRow>
            <TableHead class="w-[220px]">{{ $t('admin.label.user') }}</TableHead>
            <TableHead class="w-[100px]">{{ $t('admin.label.event') }}</TableHead>
            <TableHead>{{ $t('admin.label.description') }}</TableHead>
            <TableHead class="w-[140px]">{{ $t('admin.label.subject') }}</TableHead>
            <TableHead class="w-[160px]">{{ $t('admin.label.date') }}</TableHead>
            <TableHead class="w-[50px]" />
          </TableRow>
        </TableHeader>
        <TableBody>
          <TableRow v-for="log in logs" :key="log.id">
            <TableCell>
              <div class="flex items-center gap-2">
                <Avatar class="size-8">
                  <AvatarImage :src="`https://api.dicebear.com/9.x/initials/svg?seed=${log.user?.name ?? 'System'}`" />
                  <AvatarFallback>{{ getInitials(log.user?.name) }}</AvatarFallback>
                </Avatar>
                <div class="flex flex-col">
                  <span class="font-medium text-sm leading-tight">{{ log.user?.name ?? log.user?.email ?? $t('admin.label.system') }}</span>
                  <span v-if="log.user?.name" class="text-xs text-muted-foreground leading-tight">{{ log.user.email }}</span>
                </div>
              </div>
            </TableCell>
            <TableCell>
              <Badge :variant="getEventColor(log.event)">
                {{ log.event ?? 'action' }}
              </Badge>
            </TableCell>
            <TableCell class="max-w-xs truncate">{{ log.description }}</TableCell>
            <TableCell class="text-sm text-muted-foreground">{{ shortSubjectType(log.subject_type) }}</TableCell>
            <TableCell class="text-sm whitespace-nowrap text-muted-foreground">
              {{ new Date(log.created_at).toLocaleString() }}
            </TableCell>
            <TableCell>
              <TooltipProvider>
                <Tooltip>
                  <TooltipTrigger as-child>
                    <Button variant="ghost" size="icon" class="size-8" @click="viewDetails(log)">
                      <EyeIcon class="size-4" />
                    </Button>
                  </TooltipTrigger>
                  <TooltipContent>
                    <p>{{ $t('admin.btn.viewDetails') }}</p>
                  </TooltipContent>
                </Tooltip>
              </TooltipProvider>
            </TableCell>
          </TableRow>
          <TableRow v-if="logs.length === 0 && !isLoading">
            <TableCell colspan="6" class="h-32 text-center text-muted-foreground">
              {{ $t('admin.empty.activityLogs') }}
            </TableCell>
          </TableRow>
        </TableBody>
      </Table>
    </div>

    <Detail v-model:open="detailOpen" :log="selectedLog" />
  </BasicPage>
</template>
