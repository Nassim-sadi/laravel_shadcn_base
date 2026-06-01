import type { ColumnDef } from '@tanstack/vue-table'

import { h } from 'vue'

import { Button } from '@/components/ui/button'
import { DataTableColumnHeader } from '@/components/data-table'
import {
  DropdownMenu,
  DropdownMenuContent,
  DropdownMenuItem,
  DropdownMenuSeparator,
  DropdownMenuTrigger,
} from '@/components/ui/dropdown-menu'
import { EllipsisVerticalIcon, PencilIcon, PowerIcon, Trash2Icon } from '@lucide/vue'
import { statusBadge } from '@/lib/status-badge'

export interface BookingService {
  id: number
  name: string
  name_translations: Record<string, string | null>
  duration_minutes: number
  price?: string
  is_active: boolean
  order: number
  bookings_count?: number
  created_at: string
  updated_at: string
}

export function createColumns(
  onEdit: (service: BookingService) => void,
  onDelete: (id: number) => void,
  onToggle: (service: BookingService) => void,
): ColumnDef<BookingService>[] {
  return [
    {
      accessorKey: 'name',
      header: ({ column }) => h(DataTableColumnHeader as any, { column, title: 'Service' }),
      cell: ({ row }) => h('div', { class: 'font-medium' }, row.getValue('name')),
      enableHiding: false,
    },
    {
      accessorKey: 'duration_minutes',
      header: ({ column }) => h(DataTableColumnHeader as any, { column, title: 'Duration' }),
      cell: ({ row }) => h('div', { class: 'text-sm' }, `${row.getValue('duration_minutes')} min`),
    },
    {
      accessorKey: 'price',
      header: ({ column }) => h(DataTableColumnHeader as any, { column, title: 'Price' }),
      cell: ({ row }) => h('div', { class: 'text-sm' }, (row.getValue('price') as string) || '—'),
    },
    {
      accessorKey: 'bookings_count',
      header: ({ column }) => h(DataTableColumnHeader as any, { column, title: 'Bookings' }),
      cell: ({ row }) => h('div', { class: 'text-sm text-center' }, (row.getValue('bookings_count') as number) ?? 0),
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
          h(DropdownMenu, null, {
            default: () => [
              h(DropdownMenuTrigger as any, { asChild: true }, () => h(Button, { variant: 'ghost', size: 'icon', class: 'size-8' }, () => h(EllipsisVerticalIcon, { class: 'size-4' }))),
              h(DropdownMenuContent as any, { align: 'end', class: 'min-w-[160px]' }, () => [
              h(DropdownMenuItem as any, { class: 'cursor-pointer', onClick: () => onToggle(service) }, () => [
                h(PowerIcon, { class: 'size-4 mr-2' }),
                service.is_active ? 'Deactivate' : 'Activate',
              ]),
              h(DropdownMenuSeparator as any),
              h(DropdownMenuItem as any, { class: 'cursor-pointer', onClick: () => onEdit(service) }, () => [
                h(PencilIcon, { class: 'size-4 mr-2' }),
                'Edit',
              ]),
              h(DropdownMenuSeparator as any),
              h(DropdownMenuItem as any, { class: 'cursor-pointer text-destructive', onClick: () => onDelete(service.id) }, () => [
                h(Trash2Icon, { class: 'size-4 mr-2' }),
                'Delete',
              ]),
              ]),
            ],
          }),
        ])
      },
      enableSorting: false,
      enableHiding: false,
    },
  ]
}
