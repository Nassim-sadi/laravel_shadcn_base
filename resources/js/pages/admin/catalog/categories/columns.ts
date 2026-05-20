import type { ColumnDef } from '@tanstack/vue-table'

import { h } from 'vue'

import { Button } from '@/components/ui/button'
import { DataTableColumnHeader } from '@/components/data-table'
import { PencilIcon, Trash2Icon } from '@lucide/vue'
import { Switch } from '@/components/ui/switch'

export interface CatalogCategory {
  id: number
  name: string
  name_translations: Record<string, string | null>
  slug: string
  description?: string
  image_id?: number | null
  image_url?: string | null
  parent_id?: number | null
  parent?: { id: number; name: string }
  order: number
  is_active: boolean
  created_at: string
  updated_at: string
}

export function createColumns(
  onEdit: (category: CatalogCategory) => void,
  onDelete: (id: number) => void,
  onToggle: (category: CatalogCategory) => void,
): ColumnDef<CatalogCategory>[] {
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
            h('path', { 'stroke-linecap': 'round', 'stroke-linejoin': 'round', 'stroke-width': 2, d: 'M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z' }),
          ]),
        ])
      },
      enableSorting: false,
      enableHiding: false,
    },
    {
      accessorKey: 'name',
      header: ({ column }) => h(DataTableColumnHeader as any, { column, title: 'Name' }),
      cell: ({ row }) => h('div', { class: 'font-medium' }, row.getValue('name')),
      enableHiding: false,
    },
    {
      accessorKey: 'slug',
      header: ({ column }) => h(DataTableColumnHeader as any, { column, title: 'Slug' }),
      cell: ({ row }) => h('div', { class: 'text-sm text-muted-foreground' }, row.getValue('slug')),
    },
    {
      accessorKey: 'parent.name',
      header: ({ column }) => h(DataTableColumnHeader as any, { column, title: 'Parent' }),
      cell: ({ row }) => h('div', { class: 'text-sm text-muted-foreground' }, row.original.parent?.name || '—'),
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
        const category = row.original
        return h(Switch, { checked: category.is_active, 'onUpdate:checked': () => onToggle(category) })
      },
    },
    {
      id: 'actions',
      header: 'Actions',
      cell: ({ row }) => {
        const category = row.original
        return h('div', { class: 'flex gap-1' }, [
          h(Button, { variant: 'ghost', size: 'icon', class: 'size-7', onClick: () => onEdit(category) }, () => h(PencilIcon, { class: 'size-3' })),
          h(Button, { variant: 'ghost', size: 'icon', class: 'size-7 text-destructive', onClick: () => onDelete(category.id) }, () => h(Trash2Icon, { class: 'size-3' })),
        ])
      },
      enableSorting: false,
      enableHiding: false,
    },
  ]
}
