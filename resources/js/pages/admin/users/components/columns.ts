import type { ColumnDef } from '@tanstack/vue-table'

import { h } from 'vue'

import { DataTableColumnHeader, SelectColumn } from '@/components/data-table'
import { Copy } from '@/components/prop-ui/copy'
import Badge from '@/components/ui/badge/Badge.vue'

import type { User } from '../data/schema'

import DataTableRowActions from './data-table-row-actions.vue'

export const columns: ColumnDef<User>[] = [
  SelectColumn as ColumnDef<User>,
  {
    accessorKey: 'name',
    header: ({ column }) => h(DataTableColumnHeader<User>, { column, title: 'Name' }),
    cell: ({ row }) => h('div', { class: 'font-medium' }, row.getValue('name')),
    enableSorting: true,
    enableHiding: false,
    enableResizing: true,
  },

  {
    accessorKey: 'email',
    header: ({ column }) => h(DataTableColumnHeader<User>, { column, title: 'Email' }),
    cell: ({ row }) => h('div', { class: 'flex items-center' }, [
      h('span', {}, row.getValue('email')),
      h(Copy, { class: 'ml-2', size: 'sm', content: (row.getValue('email') || '') as string }),
    ]),
    enableSorting: true,
    enableResizing: true,
  },

  {
    accessorKey: 'role',
    header: ({ column }) => h(DataTableColumnHeader<User>, { column, title: 'Role' }),
    cell: ({ row }) => {
      const role = row.getValue('role') as string
      return h(Badge, { variant: 'outline' }, () => role)
    },
    enableSorting: true,
    enableResizing: true,
  },

  {
    accessorKey: 'is_active',
    header: ({ column }) => h(DataTableColumnHeader<User>, { column, title: 'Status' }),
    cell: ({ row }) => {
      const isActive = row.getValue('is_active') as boolean
      return h(Badge, {
        class: isActive ? 'bg-green-500/10 text-green-500' : 'bg-red-500/10 text-red-500',
        variant: 'outline',
      }, () => isActive ? 'Active' : 'Inactive')
    },
    enableSorting: true,
    enableResizing: true,
  },

  {
    id: 'actions',
    cell: ({ row }) => h(DataTableRowActions, { row }),
  },
]
