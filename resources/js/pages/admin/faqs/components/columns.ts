import type { ColumnDef } from '@tanstack/vue-table'

import { h } from 'vue'

import { Button } from '@/components/ui/button'
import { DataTableColumnHeader } from '@/components/data-table'
import { PencilIcon, Trash2Icon } from '@lucide/vue'
import { statusBadge } from '@/lib/status-badge'

export interface Faq {
  id: number
  question: string
  question_translations: Record<string, string | null>
  answer: string
  answer_translations: Record<string, string | null>
  category?: string
  order: number
  is_active: boolean
  seo_title?: string
  seo_title_translations?: Record<string, string | null>
  seo_description?: string
  seo_description_translations?: Record<string, string | null>
  created_at: string
  updated_at: string
}

export function createColumns(
  onEdit: (item: Faq) => void,
  onDelete: (id: number) => void,
): ColumnDef<Faq>[] {
  return [
    {
      accessorKey: 'question',
      header: ({ column }) => h(DataTableColumnHeader as any, { column, title: 'Question' }),
      cell: ({ row }) => h('div', { class: 'font-medium max-w-[400px] truncate' }, row.getValue('question')),
      enableHiding: false,
    },
    {
      accessorKey: 'answer',
      header: ({ column }) => h(DataTableColumnHeader as any, { column, title: 'Answer' }),
      cell: ({ row }) => {
        const answer = row.getValue('answer') as string
        return h('div', { class: 'max-w-[400px] truncate text-muted-foreground' }, answer?.slice(0, 100) || '—')
      },
      enableSorting: false,
    },
    {
      accessorKey: 'category',
      header: ({ column }) => h(DataTableColumnHeader as any, { column, title: 'Category' }),
      cell: ({ row }) => h('div', { class: 'text-muted-foreground' }, (row.getValue('category') as string) || '—'),
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
