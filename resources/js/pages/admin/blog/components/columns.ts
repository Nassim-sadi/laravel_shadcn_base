import type { ColumnDef } from '@tanstack/vue-table'

import { h } from 'vue'

import { Button } from '@/components/ui/button'
import { DataTableColumnHeader } from '@/components/data-table'
import { PencilIcon, Trash2Icon } from '@lucide/vue'
import { statusBadge, featuredBadge } from '@/lib/status-badge'

export interface BlogPost {
  id: number
  title: string
  title_translations: Record<string, string | null>
  slug: string
  excerpt?: string
  excerpt_translations?: Record<string, string | null>
  is_published: boolean
  featured: boolean
  published_at?: string
  user_id: number
  category_id?: number
  category?: { name: string }
  tags?: { name: string }[]
  created_at: string
  updated_at: string
}

export function createColumns(
  onEdit: (item: BlogPost) => void,
  onDelete: (id: number) => void,
): ColumnDef<BlogPost>[] {
  return [
    {
      accessorKey: 'title',
      header: ({ column }) => h(DataTableColumnHeader as any, { column, title: 'Title' }),
      cell: ({ row }) => h('div', { class: 'font-medium max-w-[300px] truncate' }, row.getValue('title')),
      enableHiding: false,
    },
    {
      accessorKey: 'category',
      header: ({ column }) => h(DataTableColumnHeader as any, { column, title: 'Category' }),
      cell: ({ row }) => {
        const category = row.getValue('category') as { name: string } | null
        return h('div', { class: 'text-muted-foreground' }, category?.name || '—')
      },
    },
    {
      accessorKey: 'is_published',
      header: ({ column }) => h(DataTableColumnHeader as any, { column, title: 'Status' }),
      cell: ({ row }) => {
        const isPublished = row.getValue('is_published') as boolean
        return statusBadge(isPublished, 'Published', 'Draft')
      },
    },
    {
      accessorKey: 'featured',
      header: ({ column }) => h(DataTableColumnHeader as any, { column, title: 'Featured' }),
      cell: ({ row }) => {
        const featured = row.getValue('featured') as boolean
        return featuredBadge(featured)
      },
      filterFn: (row, id, value) => value.includes(row.getValue(id)),
    },
    {
      accessorKey: 'created_at',
      header: ({ column }) => h(DataTableColumnHeader as any, { column, title: 'Date' }),
      cell: ({ row }) => h('div', { class: 'text-muted-foreground' }, new Date(row.getValue('created_at') as string).toLocaleDateString()),
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
