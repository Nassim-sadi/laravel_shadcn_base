import type { ColumnDef } from '@tanstack/vue-table'

import { h } from 'vue'

import { Button } from '@/components/ui/button'
import { DataTableColumnHeader } from '@/components/data-table'
import { PencilIcon, Trash2Icon } from '@lucide/vue'
import { Switch } from '@/components/ui/switch'

export interface CatalogBrand {
  id: number
  name: string
  name_translations: Record<string, string | null>
  slug: string
  logo_id?: number | null
  logo_url?: string | null
  description?: string
  website?: string
  is_active: boolean
  order: number
  products_count?: number
  created_at: string
  updated_at: string
}

export function createColumns(
  onEdit: (brand: CatalogBrand) => void,
  onDelete: (id: number) => void,
  onToggle: (brand: CatalogBrand) => void,
): ColumnDef<CatalogBrand>[] {
  return [
    {
      id: 'logo',
      header: '',
      cell: ({ row }) => {
        const url = row.original.logo_url
        if (url) {
          return h('div', { class: 'h-10 w-10 overflow-hidden rounded border' }, [
            h('img', { src: url, class: 'h-full w-full object-contain p-1' }),
          ])
        }
        return h('div', { class: 'flex h-10 w-10 items-center justify-center rounded border bg-muted text-muted-foreground' }, [
          h('svg', { class: 'h-4 w-4', fill: 'none', stroke: 'currentColor', viewBox: '0 0 24 24' }, [
            h('path', { 'stroke-linecap': 'round', 'stroke-linejoin': 'round', 'stroke-width': 2, d: 'M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4' }),
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
      accessorKey: 'website',
      header: ({ column }) => h(DataTableColumnHeader as any, { column, title: 'Website' }),
      cell: ({ row }) => h('div', { class: 'text-sm text-muted-foreground truncate max-w-[150px]' }, (row.getValue('website') as string) || '—'),
    },
    {
      accessorKey: 'products_count',
      header: ({ column }) => h(DataTableColumnHeader as any, { column, title: 'Products' }),
      cell: ({ row }) => h('div', { class: 'text-sm text-center' }, (row.getValue('products_count') as number) ?? 0),
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
        const brand = row.original
        return h(Switch, { checked: brand.is_active, 'onUpdate:checked': () => onToggle(brand) })
      },
    },
    {
      id: 'actions',
      header: 'Actions',
      cell: ({ row }) => {
        const brand = row.original
        return h('div', { class: 'flex gap-1' }, [
          h(Button, { variant: 'ghost', size: 'icon', class: 'size-7', onClick: () => onEdit(brand) }, () => h(PencilIcon, { class: 'size-3' })),
          h(Button, { variant: 'ghost', size: 'icon', class: 'size-7 text-destructive', onClick: () => onDelete(brand.id) }, () => h(Trash2Icon, { class: 'size-3' })),
        ])
      },
      enableSorting: false,
      enableHiding: false,
    },
  ]
}
