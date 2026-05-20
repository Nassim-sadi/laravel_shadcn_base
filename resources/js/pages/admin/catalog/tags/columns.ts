import type { ColumnDef } from '@tanstack/vue-table'

import { h } from 'vue'

import { Button } from '@/components/ui/button'
import { DataTableColumnHeader } from '@/components/data-table'
import { Trash2Icon } from '@lucide/vue'

export interface CatalogTag {
  id: number
  name: string
  name_translations: Record<string, string | null>
  slug: string
  products_count?: number
}

export function createColumns(
  onDelete: (id: number) => void,
): ColumnDef<CatalogTag>[] {
  return [
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
      accessorKey: 'products_count',
      header: ({ column }) => h(DataTableColumnHeader as any, { column, title: 'Products' }),
      cell: ({ row }) => h('div', { class: 'text-sm text-center' }, (row.getValue('products_count') as number) ?? 0),
    },
    {
      id: 'actions',
      header: 'Actions',
      cell: ({ row }) => {
        return h('div', { class: 'flex gap-1' }, [
          h(Button, { variant: 'ghost', size: 'icon', class: 'size-7 text-destructive', onClick: () => onDelete(row.original.id) }, () => h(Trash2Icon, { class: 'size-3' })),
        ])
      },
      enableSorting: false,
      enableHiding: false,
    },
  ]
}
