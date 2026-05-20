import type { ColumnDef } from '@tanstack/vue-table'

import { h } from 'vue'

import { Button } from '@/components/ui/button'
import { DataTableColumnHeader } from '@/components/data-table'
import { PencilIcon, Trash2Icon } from '@lucide/vue'
import { Switch } from '@/components/ui/switch'
import { Badge } from '@/components/ui/badge'

export interface CatalogProduct {
  id: number
  name: string
  name_translations: Record<string, string | null>
  slug: string
  description?: string
  sku?: string
  price_display?: string
  badges: string[]
  category_id?: number | null
  category?: { id: number; name: string }
  brand?: { id: number; name: string }
  media?: Array<{ id: number; image_url?: string }>
  tags?: Array<{ id: number; name: string }>
  is_active: boolean
  order: number
  created_at: string
  updated_at: string
}

const badgeColorMap: Record<string, string> = {
  new: 'bg-blue-500 text-white',
  sale: 'bg-red-500 text-white',
  featured: 'bg-amber-500 text-white',
  popular: 'bg-green-500 text-white',
  limited: 'bg-purple-500 text-white',
}

export function createColumns(
  onEdit: (product: CatalogProduct) => void,
  onDelete: (id: number) => void,
  onToggle: (product: CatalogProduct) => void,
): ColumnDef<CatalogProduct>[] {
  return [
    {
      id: 'image',
      header: '',
      cell: ({ row }) => {
        const media = row.original.media
        const url = media?.[0]?.image_url
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
      accessorKey: 'name',
      header: ({ column }) => h(DataTableColumnHeader as any, { column, title: 'Name' }),
      cell: ({ row }) => h('div', { class: 'font-medium' }, row.getValue('name')),
      enableHiding: false,
    },
    {
      accessorKey: 'category.name',
      header: ({ column }) => h(DataTableColumnHeader as any, { column, title: 'Category' }),
      cell: ({ row }) => h('div', { class: 'text-sm text-muted-foreground' }, row.original.category?.name || '—'),
    },
    {
      accessorKey: 'brand.name',
      header: ({ column }) => h(DataTableColumnHeader as any, { column, title: 'Brand' }),
      cell: ({ row }) => h('div', { class: 'text-sm text-muted-foreground' }, row.original.brand?.name || '—'),
    },
    {
      accessorKey: 'sku',
      header: ({ column }) => h(DataTableColumnHeader as any, { column, title: 'SKU' }),
      cell: ({ row }) => h('div', { class: 'text-sm font-mono' }, (row.getValue('sku') as string) || '—'),
    },
    {
      accessorKey: 'price_display',
      header: ({ column }) => h(DataTableColumnHeader as any, { column, title: 'Price' }),
      cell: ({ row }) => h('div', { class: 'text-sm' }, (row.getValue('price_display') as string) || '—'),
    },
    {
      accessorKey: 'badges',
      header: ({ column }) => h(DataTableColumnHeader as any, { column, title: 'Badges' }),
      cell: ({ row }) => {
        const badges = row.getValue('badges') as string[]
        if (!badges?.length) return h('div', { class: 'text-sm text-muted-foreground' }, '—')
        return h('div', { class: 'flex gap-1 flex-wrap' }, badges.map((badge) => h(Badge, { class: badgeColorMap[badge] || 'bg-gray-500 text-white' }, () => badge)))
      },
    },
    {
      accessorKey: 'is_active',
      header: ({ column }) => h(DataTableColumnHeader as any, { column, title: 'Active' }),
      cell: ({ row }) => {
        const product = row.original
        return h(Switch, { checked: product.is_active, 'onUpdate:checked': () => onToggle(product) })
      },
    },
    {
      id: 'actions',
      header: 'Actions',
      cell: ({ row }) => {
        const product = row.original
        return h('div', { class: 'flex gap-1' }, [
          h(Button, { variant: 'ghost', size: 'icon', class: 'size-7', onClick: () => onEdit(product) }, () => h(PencilIcon, { class: 'size-3' })),
          h(Button, { variant: 'ghost', size: 'icon', class: 'size-7 text-destructive', onClick: () => onDelete(product.id) }, () => h(Trash2Icon, { class: 'size-3' })),
        ])
      },
      enableSorting: false,
      enableHiding: false,
    },
  ]
}
