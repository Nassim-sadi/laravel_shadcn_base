<script lang="ts" setup>
import { computed, ref } from 'vue'
import { BasicPage } from '@/components/global-layout'
import { Button } from '@/components/ui/button'
import { Input } from '@/components/ui/input'
import { Tooltip, TooltipContent, TooltipProvider, TooltipTrigger } from '@/components/ui/tooltip'
import { PencilIcon, Trash2Icon } from '@lucide/vue'
import { Label } from '@/components/ui/label'
import { Checkbox } from '@/components/ui/checkbox'
import { Sheet, SheetContent, SheetHeader, SheetTitle, SheetFooter } from '@/components/ui/sheet'
import { useGetRolesQuery, useCreateRoleMutation, useUpdateRoleMutation, useDeleteRoleMutation } from '@/services/api/roles.api'
import { useGetAllPermissionsQuery } from '@/services/api/permissions.api'
import { useVuelidate } from '@vuelidate/core'
import { required, helpers } from '@vuelidate/validators'
import ConfirmDialog from '@/components/confirm-dialog.vue'
import type { IPermission } from '@/services/api/permissions.api'

const { data: rolesResponse, isLoading: _isLoading, refetch } = useGetRolesQuery()
const { data: allPermissionsResponse } = useGetAllPermissionsQuery()

const roles = computed(() => rolesResponse.value?.data ?? [])

const allPermissions = computed(() => {
  const r = allPermissionsResponse.value
  if (!r) return []
  if (Array.isArray(r)) return r
  if (r.data && Array.isArray(r.data)) return r.data
  return []
})

const permissionGroups = computed(() => {
  const groups: Record<string, IPermission[]> = {}
  for (const p of allPermissions.value) {
    const group = p.name.includes('.') ? p.name.split('.')[0] : 'other'
    if (!groups[group]) groups[group] = []
    groups[group].push(p)
  }
  return groups
})

const showSheet = ref(false)
const editingId = ref<number | null>(null)
const deleteTargetId = ref<number | null>(null)
const showDeleteDialog = ref(false)

const { mutate: createRole } = useCreateRoleMutation()
const { mutate: updateRole } = useUpdateRoleMutation()
const { mutate: deleteRole, isPending: isDeleting } = useDeleteRoleMutation()

const form = ref({ name: '', description: '', permissions: [] as string[] })

const rules = computed(() => ({
  name: { required: helpers.withMessage('Role name is required', required) },
}))

const v$ = useVuelidate(rules, form)

function togglePermission(permName: string) {
  const idx = form.value.permissions.indexOf(permName)
  if (idx === -1) {
    form.value.permissions.push(permName)
  } else {
    form.value.permissions.splice(idx, 1)
  }
}

function openCreate() {
  editingId.value = null
  form.value = { name: '', description: '', permissions: [] }
  v$.value.$reset()
  showSheet.value = true
}

function openEdit(role: any) {
  editingId.value = role.id
  form.value = {
    name: role.name,
    description: role.description || '',
    permissions: (role.permissions ?? []).map((p: any) => typeof p === 'string' ? p : p.name),
  }
  v$.value.$reset()
  showSheet.value = true
}

async function save() {
  const isValid = await v$.value.$validate()
  if (!isValid) return

  const payload = { name: form.value.name, description: form.value.description || undefined, permissions: form.value.permissions }

  if (editingId.value) {
    updateRole({ id: editingId.value, ...payload })
  } else {
    createRole(payload)
  }
  showSheet.value = false
}

function confirmDelete(id: number) {
  deleteTargetId.value = id
  showDeleteDialog.value = true
}

function handleDelete() {
  if (deleteTargetId.value !== null) {
    deleteRole(deleteTargetId.value)
  }
  showDeleteDialog.value = false
  deleteTargetId.value = null
}

const systemRoles = ['super_admin', 'admin', 'user']
</script>

<template>
  <BasicPage :title="$t('admin.page.roles.title')" :description="$t('admin.page.roles.description')" sticky>
    <template #actions>
      <Button @click="refetch" variant="outline">{{ $t('admin.btn.refresh') }}</Button>
      <Button @click="openCreate">{{ $t('admin.sheet.createRole') }}</Button>
    </template>
    <div class="overflow-x-auto">
      <div v-if="roles.length === 0" class="text-center py-8 text-muted-foreground">{{ $t('admin.empty.roles') }}</div>
      <div v-else class="rounded-md border">
        <table class="w-full">
          <thead>
            <tr class="border-b bg-muted/50">
              <th class="px-4 py-3 text-left text-sm font-medium">{{ $t('admin.label.name') }}</th>
              <th class="px-4 py-3 text-left text-sm font-medium">Guard</th>
              <th class="px-4 py-3 text-left text-sm font-medium">{{ $t('admin.label.description') }}</th>
              <th class="px-4 py-3 text-left text-sm font-medium">{{ $t('admin.label.permissionName') }}</th>
              <th class="px-4 py-3 text-right text-sm font-medium">{{ $t('admin.btn.edit') }}</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="role in roles" :key="role.id" class="border-b">
              <td class="px-4 py-3">
                <span class="font-medium">{{ role.name }}</span>
                <span v-if="systemRoles.includes(role.name)" class="ml-2 text-xs text-muted-foreground">(system)</span>
              </td>
              <td class="px-4 py-3">
                <span class="text-muted-foreground text-sm">{{ role.guard_name }}</span>
              </td>
              <td class="px-4 py-3">
                <span class="text-muted-foreground text-sm">{{ role.description || '-' }}</span>
              </td>
              <td class="px-4 py-3">
                <span class="text-muted-foreground text-sm">{{ role.permissions?.length ?? 0 }}</span>
              </td>
              <td class="px-4 py-3 text-right">
                <TooltipProvider>
                  <div class="flex gap-1 justify-end">
                    <Tooltip>
                      <TooltipTrigger as-child>
                        <Button variant="ghost" size="icon" class="size-8" @click="openEdit(role)">
                          <PencilIcon class="size-4" />
                        </Button>
                      </TooltipTrigger>
                      <TooltipContent><p>{{ $t('admin.btn.edit') }}</p></TooltipContent>
                    </Tooltip>
                    <Tooltip v-if="!systemRoles.includes(role.name)">
                      <TooltipTrigger as-child>
                        <Button variant="destructive" size="icon" class="size-8" @click="confirmDelete(role.id)">
                          <Trash2Icon class="size-4" />
                        </Button>
                      </TooltipTrigger>
                      <TooltipContent><p>{{ $t('admin.btn.delete') }}</p></TooltipContent>
                    </Tooltip>
                  </div>
                </TooltipProvider>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>

    <Sheet :open="showSheet" @update:open="showSheet = $event">
      <SheetContent side="right" class="xl:max-w-2xl w-full">
        <SheetHeader>
          <SheetTitle>{{ editingId ? $t('admin.sheet.editRole') : $t('admin.sheet.createRole') }}</SheetTitle>
        </SheetHeader>
        <div class="flex-1 overflow-y-auto px-6 py-4 space-y-6">
          <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div class="admin-form-field">
              <Label>{{ $t('admin.label.roleName') }}</Label>
              <Input v-model="form.name" :placeholder="$t('admin.misc.roleNamePlaceholder')" :class="{ 'border-destructive': v$.name.$error }" />
              <span v-if="v$.name.$error" class="text-xs text-destructive">{{ v$.name.$errors[0]?.$message }}</span>
            </div>
            <div class="admin-form-field">
              <Label>{{ $t('admin.label.description') }}</Label>
              <Input v-model="form.description" placeholder="Optional description" />
            </div>
          </div>

          <div>
            <Label class="mb-2 block">{{ $t('admin.label.permissionName') }}</Label>
            <div v-if="Object.keys(permissionGroups).length === 0" class="text-sm text-muted-foreground py-4 text-center">
              {{ $t('admin.empty.permissionsList') }}
            </div>
            <div v-else class="space-y-4">
              <div v-for="(perms, group) in permissionGroups" :key="group" class="rounded-md border p-3">
                <h4 class="text-sm font-medium mb-2 capitalize">{{ group }}</h4>
                <div class="flex flex-wrap gap-3">
                  <label
                    v-for="perm in perms"
                    :key="perm.name"
                    class="flex items-center gap-2 text-sm cursor-pointer hover:bg-muted/50 rounded px-2 py-1"
                  >
                    <Checkbox
                      :checked="form.permissions.includes(perm.name)"
                      @update:checked="togglePermission(perm.name)"
                    />
                    {{ perm.name }}
                  </label>
                </div>
              </div>
            </div>
          </div>
        </div>
        <SheetFooter>
          <Button variant="outline" @click="showSheet = false">{{ $t('admin.btn.cancel') }}</Button>
          <Button @click="save">{{ editingId ? $t('admin.btn.update') : $t('admin.btn.create') }}</Button>
        </SheetFooter>
      </SheetContent>
    </Sheet>

    <ConfirmDialog
      v-model:open="showDeleteDialog"
      :is-loading="isDeleting"
      :cancel-button-text="$t('admin.btn.cancel')"
      :confirm-button-text="$t('admin.btn.delete')"
      destructive
      @confirm="handleDelete"
    >
      <template #title>{{ $t('admin.dialog.deleteTitle', { item: $t('admin.nav.roles') }) }}</template>
      <template #description>{{ $t('admin.dialog.deleteDescription', { item: $t('admin.nav.roles').toLowerCase() }) }}</template>
    </ConfirmDialog>
  </BasicPage>
</template>
