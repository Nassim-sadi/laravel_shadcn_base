import type { ColumnDef } from '@tanstack/vue-table'

import { h } from 'vue'

import { Button } from '@/components/ui/button'
import { DataTableColumnHeader } from '@/components/data-table'
import { Trash2Icon, ReplyIcon } from '@lucide/vue'

export interface QuoteRequest {
  id: number
  name: string
  email: string
  phone?: string
  message?: string
  product_id?: number | null
  product?: { id: number; name: string }
  is_read: boolean
  reply?: string
  created_at: string
}

export function createColumns(
  onReply: (quote: QuoteRequest) => void,
  onDelete: (id: number) => void,
): ColumnDef<QuoteRequest>[] {
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
      cell: ({ row }) => h('div', { class: 'text-sm text-muted-foreground' }, row.getValue('email')),
    },
    {
      accessorKey: 'phone',
      header: ({ column }) => h(DataTableColumnHeader as any, { column, title: 'Phone' }),
      cell: ({ row }) => h('div', { class: 'text-sm' }, (row.getValue('phone') as string) || '—'),
    },
    {
      accessorKey: 'product.name',
      header: ({ column }) => h(DataTableColumnHeader as any, { column, title: 'Product' }),
      cell: ({ row }) => h('div', { class: 'text-sm' }, row.original.product?.name || '—'),
    },
    {
      accessorKey: 'is_read',
      header: ({ column }) => h(DataTableColumnHeader as any, { column, title: 'Status' }),
      cell: ({ row }) => {
        const isRead = row.getValue('is_read') as boolean
        return h('span', {
          class: `px-2 py-0.5 text-xs rounded-full ${isRead ? 'bg-gray-100 text-gray-600' : 'bg-blue-100 text-blue-700'}`,
        }, isRead ? 'Read' : 'Unread')
      },
    },
    {
      accessorKey: 'created_at',
      header: ({ column }) => h(DataTableColumnHeader as any, { column, title: 'Date' }),
      cell: ({ row }) => h('div', { class: 'text-sm text-muted-foreground' }, new Date(row.getValue('created_at') as string).toLocaleDateString()),
    },
    {
      id: 'actions',
      header: 'Actions',
      cell: ({ row }) => {
        const quote = row.original
        return h('div', { class: 'flex gap-1' }, [
          h(Button, { variant: 'outline', size: 'sm', onClick: () => onReply(quote) }, () => [h(ReplyIcon, { class: 'size-3 mr-1' }), 'Reply']),
          h(Button, { variant: 'ghost', size: 'icon', class: 'size-7 text-destructive', onClick: () => onDelete(quote.id) }, () => h(Trash2Icon, { class: 'size-3' })),
        ])
      },
      enableSorting: false,
      enableHiding: false,
    },
  ]
}
