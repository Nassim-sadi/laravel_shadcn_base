import type { ColumnDef } from '@tanstack/vue-table'

import { h } from 'vue'

import { Button } from '@/components/ui/button'
import { DataTableColumnHeader } from '@/components/data-table'
import { PencilIcon, Trash2Icon } from '@lucide/vue'
import { statusBadge } from '@/lib/status-badge'

export interface Service {
  id: number
  title: string
  title_translations: Record<string, string | null>
  description?: string
  description_translations?: Record<string, string | null>
  icon?: string
  image?: string
  image_id?: number | null
  image_url?: string | null
  image_thumbnail_url?: string | null
  url?: string
  order: number
  is_active: boolean
  seo_title?: string
  seo_title_translations?: Record<string, string | null>
  seo_description?: string
  seo_description_translations?: Record<string, string | null>
  seo_keywords?: string
  seo_keywords_translations?: Record<string, string | null>
  created_at: string
  updated_at: string
}

export function createColumns(
  onEdit: (service: Service) => void,
  onDelete: (id: number) => void,
): ColumnDef<Service>[] {
  return [
    {
      accessorKey: 'title',
      header: ({ column }) => h(DataTableColumnHeader as any, { column, title: 'Title' }),
      cell: ({ row }) => h('div', { class: 'font-medium' }, row.getValue('title')),
      enableHiding: false,
    },
    {
      accessorKey: 'description',
      header: ({ column }) => h(DataTableColumnHeader as any, { column, title: 'Description' }),
      cell: ({ row }) => {
        const desc = row.getValue('description') as string
        return h('div', { class: 'max-w-[400px] truncate text-muted-foreground' }, desc || '—')
      },
      enableSorting: false,
    },
    {
      accessorKey: 'icon',
      header: ({ column }) => h(DataTableColumnHeader as any, { column, title: 'Icon' }),
      cell: ({ row }) => h('div', { class: 'text-muted-foreground' }, (row.getValue('icon') as string) || '—'),
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
        const service = row.original
        return h('div', { class: 'flex gap-1' }, [
          h(Button, {
            variant: 'ghost',
            size: 'icon',
            class: 'size-8',
            onClick: () => onEdit(service),
          }, () => h(PencilIcon, { class: 'size-4' })),
          h(Button, {
            variant: 'destructive',
            size: 'icon',
            class: 'size-8',
            onClick: () => onDelete(service.id),
          }, () => h(Trash2Icon, { class: 'size-4' })),
        ])
      },
      enableSorting: false,
      enableHiding: false,
    },
  ]
}
