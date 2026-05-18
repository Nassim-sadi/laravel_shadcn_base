<script setup lang="ts">
import {
  Avatar,
  AvatarFallback,
  AvatarImage,
} from '@/components/ui/avatar'
import { Badge } from '@/components/ui/badge'
import { ScrollArea } from '@/components/ui/scroll-area'
import {
  Sheet,
  SheetContent,
  SheetHeader,
  SheetTitle,
} from '@/components/ui/sheet'
import type { IActivityLog } from '@/services/api/activity-log.api'

defineProps<{
  log: IActivityLog | null
}>()

const open = defineModel<boolean>('open', { default: false })

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
  <Sheet v-model:open="open">
    <SheetContent side="right" class="w-full sm:max-w-lg">
      <SheetHeader>
        <SheetTitle>{{ $t('admin.sheet.activityLogDetail') }}</SheetTitle>
      </SheetHeader>
      <ScrollArea class="flex-1 px-6">
        <div v-if="log" class="space-y-6 py-4">
          <div class="flex items-center gap-3">
            <Avatar class="size-10">
              <AvatarImage :src="`https://api.dicebear.com/9.x/initials/svg?seed=${log.user?.name ?? 'System'}`" />
              <AvatarFallback>{{ getInitials(log.user?.name) }}</AvatarFallback>
            </Avatar>
            <div>
              <p class="font-medium text-sm">{{ log.user?.name ?? log.user?.email ?? $t('admin.label.system') }}</p>
              <p v-if="log.user?.name" class="text-xs text-muted-foreground">{{ log.user.email }}</p>
            </div>
          </div>

          <div class="space-y-1">
            <p class="text-xs text-muted-foreground uppercase tracking-wider font-medium">{{ $t('admin.label.event') }}</p>
            <Badge :variant="getEventColor(log.event)">
              {{ log.event ?? 'action' }}
            </Badge>
          </div>

          <div class="space-y-1">
            <p class="text-xs text-muted-foreground uppercase tracking-wider font-medium">{{ $t('admin.label.description') }}</p>
            <p class="text-sm">{{ log.description }}</p>
          </div>

          <div class="space-y-1">
            <p class="text-xs text-muted-foreground uppercase tracking-wider font-medium">{{ $t('admin.label.subject') }}</p>
            <p class="text-sm">{{ shortSubjectType(log.subject_type) }}</p>
            <p v-if="log.subject_id" class="text-xs text-muted-foreground">ID: {{ log.subject_id }}</p>
          </div>

          <div v-if="log.ip_address" class="space-y-1">
            <p class="text-xs text-muted-foreground uppercase tracking-wider font-medium">IP Address</p>
            <p class="text-sm font-mono">{{ log.ip_address }}</p>
          </div>

          <div v-if="log.user_agent" class="space-y-1">
            <p class="text-xs text-muted-foreground uppercase tracking-wider font-medium">User Agent</p>
            <p class="text-xs text-muted-foreground break-words">{{ log.user_agent }}</p>
          </div>

          <div class="space-y-1">
            <p class="text-xs text-muted-foreground uppercase tracking-wider font-medium">{{ $t('admin.label.date') }}</p>
            <p class="text-sm">{{ new Date(log.created_at).toLocaleString() }}</p>
          </div>

          <div v-if="log.properties && Object.keys(log.properties).length > 0" class="space-y-1">
            <p class="text-xs text-muted-foreground uppercase tracking-wider font-medium">Properties</p>
            <pre class="text-xs bg-muted rounded-lg p-3 overflow-x-auto whitespace-pre-wrap">{{ JSON.stringify(log.properties, null, 2) }}</pre>
          </div>
        </div>
      </ScrollArea>
    </SheetContent>
  </Sheet>
</template>
