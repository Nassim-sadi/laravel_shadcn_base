import type { ColumnDef } from '@tanstack/vue-table'

import { h } from 'vue'

import { Button } from '@/components/ui/button'
import { DataTableColumnHeader } from '@/components/data-table'
import { PencilIcon, Trash2Icon } from '@lucide/vue'
import { Switch } from '@/components/ui/switch'

export interface CatalogMarqueeItem {
  id: number
  image_url?: string
  text?: Record<string, string | null>
  position: number
  order: number
  is_active: boolean
}

export function createColumns(
  onEdit: (item: CatalogMarqueeItem) => void,
  onDelete: (id: number) => void,
): ColumnDef<CatalogMarqueeItem>[] {
  return [
    {
      id: 'image',
      header: '',
      cell: ({ row }) => {
        const url = row.original.image_url
        if (url) {
          return h('div', { class: 'h-10 w-10 overflow-hidden rounded border' }, [
            h('img', { src: url, class: 'h-full w-full object-cover' }),
          ])
        }
        return h('div', { class: 'flex h-10 w-10 items-center justify-center rounded border bg-muted text-muted-foreground' }, [
          h('svg', { class: 'h-4 w-4', fill: 'none', stroke: 'currentColor', viewBox: '0 0 24 24' }, [
            h('path', { 'stroke-linecap': 'round', 'stroke-linejoin': 'round', 'stroke-width': 2, d: 'M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z' }),
          ]),
        ])
      },
      enableSorting: false,
      enableHiding: false,
    },
    {
      accessorKey: 'text',
      header: ({ column }) => h(DataTableColumnHeader as any, { column, title: 'Text' }),
      cell: ({ row }) => {
        const text = row.original.text
        const val = text?.fr || text?.en || '—'
        return h('div', { class: 'text-sm' }, val)
      },
    },
    {
      accessorKey: 'position',
      header: ({ column }) => h(DataTableColumnHeader as any, { column, title: 'Position' }),
      cell: ({ row }) => h('div', { class: 'text-sm text-center' }, `Position ${row.getValue('position')}`),
    },
    {
      accessorKey: 'order',
      header: ({ column }) => h(DataTableColumnHeader as any, { column, title: 'Order' }),
      cell: ({ row }) => h('div', { class: 'text-sm text-center' }, row.getValue('order')),
    },
    {
      accessorKey: 'is_active',
      header: ({ column }) => h(DataTableColumnHeader as any, { column, title: 'Active' }),
      cell: ({ row }) => {
        return h(Switch, { checked: row.original.is_active })
      },
    },
    {
      id: 'actions',
      header: 'Actions',
      cell: ({ row }) => {
        const item = row.original
        return h('div', { class: 'flex gap-1' }, [
          h(Button, { variant: 'ghost', size: 'icon', class: 'size-7', onClick: () => onEdit(item) }, () => h(PencilIcon, { class: 'size-3' })),
          h(Button, { variant: 'ghost', size: 'icon', class: 'size-7 text-destructive', onClick: () => onDelete(item.id) }, () => h(Trash2Icon, { class: 'size-3' })),
        ])
      },
      enableSorting: false,
      enableHiding: false,
    },
  ]
}
