import type { ColumnDef } from '@tanstack/vue-table'

import { h } from 'vue'

import { Button } from '@/components/ui/button'
import { DataTableColumnHeader } from '@/components/data-table'
import { PencilIcon, Trash2Icon } from '@lucide/vue'
import { statusBadge } from '@/lib/status-badge'

export interface Testimonial {
  id: number
  name: string
  name_translations: Record<string, string | null>
  position?: string
  position_translations?: Record<string, string | null>
  company?: string
  company_translations?: Record<string, string | null>
  content: string
  content_translations: Record<string, string | null>
  rating: number
  image?: string
  image_id?: number | null
  image_url?: string | null
  is_active: boolean
  order: number
  created_at: string
  updated_at: string
}

export function createColumns(
  onEdit: (item: Testimonial) => void,
  onDelete: (id: number) => void,
): ColumnDef<Testimonial>[] {
  return [
    {
      accessorKey: 'name',
      header: ({ column }) => h(DataTableColumnHeader as any, { column, title: 'Name' }),
      cell: ({ row }) => h('div', { class: 'font-medium' }, row.getValue('name')),
      enableHiding: false,
    },
    {
      accessorKey: 'company',
      header: ({ column }) => h(DataTableColumnHeader as any, { column, title: 'Company' }),
      cell: ({ row }) => h('div', { class: 'text-muted-foreground' }, (row.getValue('company') as string) || '—'),
    },
    {
      accessorKey: 'rating',
      header: ({ column }) => h(DataTableColumnHeader as any, { column, title: 'Rating' }),
      cell: ({ row }) => h('div', { class: 'text-center' }, '★'.repeat(row.getValue('rating'))),
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
