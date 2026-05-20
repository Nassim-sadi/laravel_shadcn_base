import type { ColumnDef } from '@tanstack/vue-table'
import { h } from 'vue'
import { Button } from '@/components/ui/button'
import { DataTableColumnHeader } from '@/components/data-table'
import { PencilIcon, Trash2Icon } from '@lucide/vue'
import { statusBadge } from '@/lib/status-badge'

export interface CatalogCategory {
  id: number
  name: string
  name_translations: Record<string, string | null>
  slug: string
  description?: string
  description_translations?: Record<string, string | null>
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
): ColumnDef<CatalogCategory>[] {
  return [
    {
      accessorKey: 'name',
      header: ({ column }) => h(DataTableColumnHeader as any, { column, title: 'Name' }),
      cell: ({ row }) => h('div', { class: 'font-medium' }, row.getValue('name')),
      enableHiding: false,
    },
    {
      accessorKey: 'parent',
      header: ({ column }) => h(DataTableColumnHeader as any, { column, title: 'Parent' }),
      cell: ({ row }) => {
        const parent = row.getValue('parent') as { name: string } | undefined
        return h('div', { class: 'text-muted-foreground' }, parent?.name || '—')
      },
      enableSorting: false,
    },
    {
      accessorKey: 'order',
      header: ({ column }) => h(DataTableColumnHeader as any, { column, title: 'Order' }),
      cell: ({ row }) => h('div', { class: 'text-center' }, row.getValue('order')),
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
        const category = row.original
        return h('div', { class: 'flex gap-1' }, [
          h(Button, { variant: 'ghost', size: 'icon', class: 'size-8', onClick: () => onEdit(category) }, () => h(PencilIcon, { class: 'size-4' })),
          h(Button, { variant: 'destructive', size: 'icon', class: 'size-8', onClick: () => onDelete(category.id) }, () => h(Trash2Icon, { class: 'size-4' })),
        ])
      },
      enableSorting: false,
      enableHiding: false,
    },
  ]
}
