import type { ColumnDef } from '@tanstack/vue-table'

import { h } from 'vue'

import { Badge } from '@/components/ui/badge'
import { Button } from '@/components/ui/button'
import { DataTableColumnHeader } from '@/components/data-table'
import { PencilIcon, Trash2Icon } from '@lucide/vue'
import { statusBadge } from '@/lib/status-badge'

export interface Project {
  id: number
  title: string
  title_translations: Record<string, string | null>
  description?: string
  description_translations?: Record<string, string | null>
  client?: string
  client_translations?: Record<string, string | null>
  technologies?: string[]
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
  onEdit: (project: Project) => void,
  onDelete: (id: number) => void,
): ColumnDef<Project>[] {
  return [
    {
      accessorKey: 'title',
      header: ({ column }) => h(DataTableColumnHeader as any, { column, title: 'Title' }),
      cell: ({ row }) => h('div', { class: 'font-medium' }, row.getValue('title')),
      enableHiding: false,
    },
    {
      accessorKey: 'client',
      header: ({ column }) => h(DataTableColumnHeader as any, { column, title: 'Client' }),
      cell: ({ row }) => h('div', { class: 'text-muted-foreground' }, (row.getValue('client') as string) || '—'),
    },
    {
      accessorKey: 'technologies',
      header: ({ column }) => h(DataTableColumnHeader as any, { column, title: 'Technologies' }),
      cell: ({ row }) => {
        const techs = row.getValue('technologies') as string[]
        return h('div', { class: 'flex flex-wrap gap-1' }, techs?.map(t => h(Badge, { variant: 'outline', class: 'text-xs' }, () => t)) || '—')
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
        const project = row.original
        return h('div', { class: 'flex gap-1' }, [
          h(Button, {
            variant: 'ghost',
            size: 'icon',
            class: 'size-8',
            onClick: () => onEdit(project),
          }, () => h(PencilIcon, { class: 'size-4' })),
          h(Button, {
            variant: 'destructive',
            size: 'icon',
            class: 'size-8',
            onClick: () => onDelete(project.id),
          }, () => h(Trash2Icon, { class: 'size-4' })),
        ])
      },
      enableSorting: false,
      enableHiding: false,
    },
  ]
}
