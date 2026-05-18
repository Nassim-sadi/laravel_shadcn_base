import type { ColumnDef } from '@tanstack/vue-table'

import { h } from 'vue'

import { DataTableColumnHeader } from '@/components/data-table'
import { readBadge } from '@/lib/status-badge'

export interface ContactMessage {
  id: number
  name: string
  email: string
  subject?: string
  message: string
  is_read: boolean
  created_at: string
  updated_at: string
}

export function createColumns(): ColumnDef<ContactMessage>[] {
  return [
    {
      accessorKey: 'name',
      header: ({ column }) => h(DataTableColumnHeader as any, { column, title: 'Name' }),
      cell: ({ row }) => h('div', { class: 'font-medium' }, row.getValue('name')),
      enableHiding: false,
    },
    {
      accessorKey: 'email',
      header: ({ column }) => h(DataTableColumnHeader as any, { column, title: 'Email' }),
      cell: ({ row }) => h('div', { class: 'text-muted-foreground' }, row.getValue('email')),
    },
    {
      accessorKey: 'subject',
      header: ({ column }) => h(DataTableColumnHeader as any, { column, title: 'Subject' }),
      cell: ({ row }) => h('div', { class: 'max-w-[300px] truncate' }, (row.getValue('subject') as string) || '—'),
    },
    {
      accessorKey: 'message',
      header: ({ column }) => h(DataTableColumnHeader as any, { column, title: 'Message' }),
      cell: ({ row }) => {
        const message = row.getValue('message') as string
        return h('div', { class: 'max-w-[400px] truncate text-muted-foreground' }, message?.slice(0, 100))
      },
      enableSorting: false,
    },
    {
      accessorKey: 'is_read',
      header: ({ column }) => h(DataTableColumnHeader as any, { column, title: 'Status' }),
      cell: ({ row }) => {
        const isRead = row.getValue('is_read') as boolean
        return readBadge(isRead)
      },
    },
    {
      accessorKey: 'created_at',
      header: ({ column }) => h(DataTableColumnHeader as any, { column, title: 'Date' }),
      cell: ({ row }) => h('div', { class: 'text-muted-foreground' }, new Date(row.getValue('created_at') as string).toLocaleDateString()),
    },
  ]
}
