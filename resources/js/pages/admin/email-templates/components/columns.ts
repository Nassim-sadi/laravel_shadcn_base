import type { ColumnDef } from '@tanstack/vue-table'

import { h } from 'vue'

import { Button } from '@/components/ui/button'
import { DataTableColumnHeader } from '@/components/data-table'
import { PencilIcon, Trash2Icon } from '@lucide/vue'
import { statusBadge } from '@/lib/status-badge'

export interface EmailTemplate {
  id: number
  key: string
  name: string
  name_translations: Record<string, string | null>
  subject: string
  subject_translations: Record<string, string | null>
  body: Record<string, string | null>
  variables: string[]
  is_active: boolean
  created_at: string
  updated_at: string
}

export function createColumns(
  onEdit: (item: EmailTemplate) => void,
  onDelete: (id: number) => void,
): ColumnDef<EmailTemplate>[] {
  return [
    {
      accessorKey: 'key',
      header: ({ column }) => h(DataTableColumnHeader as any, { column, title: 'Key' }),
      cell: ({ row }) => h('div', { class: 'font-mono text-sm' }, row.getValue('key')),
      enableHiding: false,
    },
    {
      accessorKey: 'name',
      header: ({ column }) => h(DataTableColumnHeader as any, { column, title: 'Name' }),
      cell: ({ row }) => h('div', { class: 'font-medium' }, row.getValue('name')),
    },
    {
      accessorKey: 'subject',
      header: ({ column }) => h(DataTableColumnHeader as any, { column, title: 'Subject' }),
      cell: ({ row }) => h('div', { class: 'max-w-[300px] truncate text-muted-foreground' }, row.getValue('subject')),
      enableSorting: false,
    },
    {
      accessorKey: 'is_active',
      header: ({ column }) => h(DataTableColumnHeader as any, { column, title: 'Status' }),
      cell: ({ row }) => {
        const isActive = row.getValue('is_active') as boolean
        return statusBadge(isActive, 'Active', 'Inactive')
      },
    },
    {
      id: 'actions',
      header: 'Actions',
      cell: ({ row }) => {
        const item = row.original
        return h('div', { class: 'flex gap-1' }, [
          h(Button, {
            variant: 'ghost',
            size: 'icon',
            class: 'size-8',
            onClick: () => onEdit(item),
          }, () => h(PencilIcon, { class: 'size-4' })),
          h(Button, {
            variant: 'destructive',
            size: 'icon',
            class: 'size-8',
            onClick: () => onDelete(item.id),
          }, () => h(Trash2Icon, { class: 'size-4' })),
        ])
      },
      enableSorting: false,
      enableHiding: false,
    },
  ]
}
