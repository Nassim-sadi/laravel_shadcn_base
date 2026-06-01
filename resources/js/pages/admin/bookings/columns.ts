import type { ColumnDef } from '@tanstack/vue-table'

import { h } from 'vue'

import { Badge } from '@/components/ui/badge'
import { Button } from '@/components/ui/button'
import { DataTableColumnHeader } from '@/components/data-table'
import {
  DropdownMenu,
  DropdownMenuContent,
  DropdownMenuItem,
  DropdownMenuSeparator,
  DropdownMenuTrigger,
} from '@/components/ui/dropdown-menu'
import {
  CheckCircleIcon,
  EllipsisVerticalIcon,
  PencilIcon,
  Trash2Icon,
  XCircleIcon,
} from '@lucide/vue'

export interface Booking {
  id: number
  booking_service_id: number
  service?: { name: string; duration_minutes: number }
  date: string
  start_time: string
  end_time: string
  customer_name: string
  customer_phone: string
  notes?: string
  status: string
  created_at: string
  updated_at: string
}

const statusConfig: Record<string, { label: string; class: string }> = {
  pending: { label: 'Pending', class: 'bg-yellow-500/15 text-yellow-700 border-yellow-200' },
  confirmed: { label: 'Confirmed', class: 'bg-blue-500/15 text-blue-700 border-blue-200' },
  completed: { label: 'Completed', class: 'bg-green-500/15 text-green-700 border-green-200' },
  cancelled: { label: 'Cancelled', class: 'bg-red-500/15 text-red-700 border-red-200' },
  rescheduled: { label: 'Rescheduled', class: 'bg-purple-500/15 text-purple-700 border-purple-200' },
}

export function createColumns(
  onConfirm: (id: number) => void,
  onComplete: (id: number) => void,
  onCancel: (id: number) => void,
  onReschedule: (booking: Booking) => void,
  onDelete: (id: number) => void,
): ColumnDef<Booking>[] {
  return [
    {
      accessorKey: 'customer_name',
      header: ({ column }) => h(DataTableColumnHeader as any, { column, title: 'Customer' }),
      cell: ({ row }) => h('div', { class: 'font-medium' }, row.getValue('customer_name')),
      enableHiding: false,
    },
    {
      accessorKey: 'customer_phone',
      header: ({ column }) => h(DataTableColumnHeader as any, { column, title: 'Phone' }),
      cell: ({ row }) => h('div', { class: 'text-muted-foreground text-sm' }, row.getValue('customer_phone')),
    },
    {
      accessorKey: 'service',
      header: ({ column }) => h(DataTableColumnHeader as any, { column, title: 'Service' }),
      cell: ({ row }) => {
        const service = row.getValue('service') as { name: string } | null
        return h('div', { class: 'text-sm' }, service?.name || '—')
      },
      enableSorting: false,
    },
    {
      accessorKey: 'date',
      header: ({ column }) => h(DataTableColumnHeader as any, { column, title: 'Date' }),
      cell: ({ row }) => {
        const date = row.getValue('date') as string
        return h('div', { class: 'text-sm' }, date ? new Date(date).toLocaleDateString() : '—')
      },
    },
    {
      id: 'time',
      header: ({ column }) => h(DataTableColumnHeader as any, { column, title: 'Time' }),
      cell: ({ row }) => {
        const booking = row.original
        return h('div', { class: 'text-sm' }, `${booking.start_time} → ${booking.end_time}`)
      },
      enableSorting: false,
    },
    {
      accessorKey: 'status',
      header: ({ column }) => h(DataTableColumnHeader as any, { column, title: 'Status' }),
      cell: ({ row }) => {
        const status = row.getValue('status') as string
        const config = statusConfig[status] || { label: status, class: 'bg-gray-500/15 text-gray-700 border-gray-200' }
        return h(Badge, { variant: 'outline', class: config.class }, () => config.label)
      },
    },
    {
      id: 'actions',
      header: 'Actions',
      cell: ({ row }) => {
        const booking = row.original
        const status = booking.status

        return h('div', { class: 'flex gap-1' }, [
          h(DropdownMenu, null, {
            default: () => [
              h(DropdownMenuTrigger as any, { asChild: true }, () =>
                h(Button, { variant: 'ghost', size: 'icon', class: 'size-8' }, () =>
                  h(EllipsisVerticalIcon, { class: 'size-4' }),
                ),
              ),
              h(DropdownMenuContent as any, { align: 'end', class: 'min-w-[160px]' }, () => [
              status === 'pending'
                ? h(DropdownMenuItem as any, { class: 'cursor-pointer', onClick: () => onConfirm(booking.id) }, () => [
                    h(CheckCircleIcon, { class: 'size-4 mr-2 text-green-600' }),
                    'Confirm',
                  ])
                : null,
              status === 'confirmed'
                ? h(DropdownMenuItem as any, { class: 'cursor-pointer', onClick: () => onComplete(booking.id) }, () => [
                    h(CheckCircleIcon, { class: 'size-4 mr-2 text-blue-600' }),
                    'Complete',
                  ])
                : null,
              status !== 'cancelled'
                ? h(DropdownMenuItem as any, { class: 'cursor-pointer', onClick: () => onCancel(booking.id) }, () => [
                    h(XCircleIcon, { class: 'size-4 mr-2 text-red-600' }),
                    'Cancel',
                  ])
                : null,
              h(DropdownMenuItem as any, { class: 'cursor-pointer', onClick: () => onReschedule(booking) }, () => [
                h(PencilIcon, { class: 'size-4 mr-2' }),
                'Reschedule',
              ]),
              h(DropdownMenuSeparator as any),
              h(DropdownMenuItem as any, { class: 'cursor-pointer text-destructive', onClick: () => onDelete(booking.id) }, () => [
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
