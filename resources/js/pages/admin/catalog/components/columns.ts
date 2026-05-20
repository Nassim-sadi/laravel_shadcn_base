import type { ColumnDef } from '@tanstack/vue-table'
import { h } from 'vue'
import { Button } from '@/components/ui/button'
import { DataTableColumnHeader } from '@/components/data-table'
import { PencilIcon, Trash2Icon } from '@lucide/vue'
import { statusBadge } from '@/lib/status-badge'

export interface CatalogProduct {
  id: number
  name: string
  name_translations: Record<string, string | null>
  slug: string
  sku?: string
  price_display?: string
  badges: string[]
  category_id?: number | null
  category?: { id: number; name: string }
  is_active: boolean
  order: number
  created_at: string
  updated_at: string
}

const badgeColorMap: Record<string, string> = {
  new: 'bg-blue-500',
  sale: 'bg-red-500',
  featured: 'bg-amber-500',
  popular: 'bg-green-500',
  limited: 'bg-purple-500',
}

export function createColumns(
  onEdit: (product: CatalogProduct) => void,
  onDelete: (id: number) => void,
): ColumnDef<CatalogProduct>[] {
  return [
    {
      accessorKey: 'name',
      header: ({ column }) => h(DataTableColumnHeader as any, { column, title: 'Name' }),
      cell: ({ row }) => h('div', { class: 'font-medium' }, row.getValue('name')),
      enableHiding: false,
    },
    {
      accessorKey: 'sku',
      header: ({ column }) => h(DataTableColumnHeader as any, { column, title: 'SKU' }),
      cell: ({ row }) => {
        const sku = row.getValue('sku') as string
        return h('div', { class: 'font-mono text-xs' }, sku || '---')
      },
    },
    {
      accessorKey: 'price_display',
      header: ({ column }) => h(DataTableColumnHeader as any, { column, title: 'Price' }),
      cell: ({ row }) => {
        const price = row.getValue('price_display') as string
        return h('div', { class: 'text-muted-foreground' }, price ? `${price}` : '---')
      },
    },
    {
      accessorKey: 'badges',
      header: 'Badges',
      cell: ({ row }) => {
        const badges = row.getValue('badges') as string[]
        if (!badges || badges.length === 0) return h('div', { class: 'text-muted-foreground text-xs' }, '---')
        return h('div', { class: 'flex gap-1 flex-wrap' }, badges.map((b) => h('span', {
          class: `px-2 py-0.5 rounded-full text-xs text-white ${badgeColorMap[b] || 'bg-gray-500'}`,
        }, b)))
      },
      enableSorting: false,
    },
    {
      accessorKey: 'category',
      header: ({ column }) => h(DataTableColumnHeader as any, { column, title: 'Category' }),
      cell: ({ row }) => {
        const cat = row.getValue('category') as { name: string } | undefined
        return h('div', { class: 'text-muted-foreground' }, cat?.name || '---')
      },
      enableSorting: false,
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
        const product = row.original
        return h('div', { class: 'flex gap-1' }, [
          h(Button, { variant: 'ghost', size: 'icon', class: 'size-8', onClick: () => onEdit(product) }, () => h(PencilIcon, { class: 'size-4' })),
          h(Button, { variant: 'destructive', size: 'icon', class: 'size-8', onClick: () => onDelete(product.id) }, () => h(Trash2Icon, { class: 'size-4' })),
        ])
      },
      enableSorting: false,
      enableHiding: false,
    },
  ]
}
