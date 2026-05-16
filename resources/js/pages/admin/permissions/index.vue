<script lang="ts" setup>
import { computed, ref } from 'vue'
import { BasicPage } from '@/components/global-layout'
import { Button } from '@/components/ui/button'
import { Input } from '@/components/ui/input'
import { Label } from '@/components/ui/label'
import { Sheet, SheetContent, SheetHeader, SheetTitle, SheetFooter } from '@/components/ui/sheet'
import { useGetPermissionsQuery, useCreatePermissionMutation, useUpdatePermissionMutation, useDeletePermissionMutation, type IPermission } from '@/services/api/permissions.api'
import { useVuelidate } from '@vuelidate/core'
import { required, helpers } from '@vuelidate/validators'
import ConfirmDialog from '@/components/confirm-dialog.vue'

const { data: response, isLoading, refetch } = useGetPermissionsQuery()

const permissions = computed<IPermission[]>(() => {
  const r = response.value
  if (!r) return []
  if (Array.isArray(r)) return r
  if (r.data && Array.isArray(r.data)) return r.data
  return []
})

const showSheet = ref(false)
const editingId = ref<number | null>(null)
const deleteTargetId = ref<number | null>(null)
const showDeleteDialog = ref(false)

const { mutate: createPermission } = useCreatePermissionMutation()
const { mutate: updatePermission } = useUpdatePermissionMutation()
const { mutate: deletePermission, isPending: isDeleting } = useDeletePermissionMutation()

const form = ref({ name: '', guard_name: 'web' })

const rules = computed(() => ({
  name: { required: helpers.withMessage('Permission name is required', required) },
}))

const v$ = useVuelidate(rules, form)

const computeGroup = (name: string) => name.includes('.') ? name.split('.')[0] : '-'

function openCreate() {
  editingId.value = null
  form.value = { name: '', guard_name: 'web' }
  v$.value.$reset()
  showSheet.value = true
}

function openEdit(permission: any) {
  editingId.value = permission.id
  form.value = { name: permission.name, guard_name: permission.guard_name || 'web' }
  v$.value.$reset()
  showSheet.value = true
}

async function save() {
  const isValid = await v$.value.$validate()
  if (!isValid) return

  if (editingId.value) {
    updatePermission({ id: editingId.value, ...form.value })
  } else {
    createPermission(form.value)
  }
  showSheet.value = false
}

function confirmDelete(name: string) {
  deleteTargetId.value = permissions.value.find(p => p.name === name)?.id ?? null
  showDeleteDialog.value = true
}

function handleDelete() {
  if (deleteTargetId.value !== null) {
    deletePermission(deleteTargetId.value)
  }
  showDeleteDialog.value = false
  deleteTargetId.value = null
}
</script>

<template>
  <BasicPage :title="$t('admin.page.permissions.title')" :description="$t('admin.page.permissions.description')" sticky>
    <template #actions>
      <Button @click="refetch" variant="outline">{{ $t('admin.btn.refresh') }}</Button>
      <Button @click="openCreate">{{ $t('admin.sheet.createPermission') }}</Button>
    </template>
    <div class="overflow-x-auto">
      <div v-if="isLoading" class="text-center py-8 text-muted-foreground">{{ $t('admin.misc.loadingTranslations') }}</div>
      <div v-else-if="permissions.length === 0" class="text-center py-8 text-muted-foreground">
        {{ $t('admin.empty.permissions') }}
      </div>
      <div v-else class="rounded-md border">
        <table class="w-full">
          <thead>
            <tr class="border-b bg-muted/50">
              <th class="px-4 py-3 text-left text-sm font-medium">{{ $t('admin.label.permissionName') }}</th>
              <th class="px-4 py-3 text-left text-sm font-medium">Guard</th>
              <th class="px-4 py-3 text-left text-sm font-medium">Group</th>
              <th class="px-4 py-3 text-right text-sm font-medium">{{ $t('admin.btn.edit') }}</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="permission in permissions" :key="permission.id" class="border-b">
              <td class="px-4 py-3">
                <code class="text-sm bg-muted px-2 py-1 rounded">{{ permission.name }}</code>
              </td>
              <td class="px-4 py-3">
                <span class="text-muted-foreground text-sm">{{ permission.guard_name }}</span>
              </td>
              <td class="px-4 py-3">
                <span class="text-muted-foreground text-sm">{{ computeGroup(permission.name) }}</span>
              </td>
              <td class="px-4 py-3 text-right space-x-2">
                <Button variant="ghost" size="sm" @click="openEdit(permission)">{{ $t('admin.btn.edit') }}</Button>
                <Button variant="destructive" size="sm" @click="confirmDelete(permission.name)">{{ $t('admin.btn.delete') }}</Button>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>

    <Sheet :open="showSheet" @update:open="showSheet = $event">
      <SheetContent side="right" class="xl:max-w-xl w-full">
        <SheetHeader>
          <SheetTitle>{{ editingId ? $t('admin.sheet.editPermission') : $t('admin.sheet.createPermission') }}</SheetTitle>
        </SheetHeader>
        <div class="flex-1 overflow-y-auto px-6 py-4 space-y-6">
          <div class="admin-form-field">
            <Label>{{ $t('admin.label.permissionName') }}</Label>
            <Input v-model="form.name" :placeholder="$t('admin.misc.permissionNamePlaceholder')" :class="{ 'border-destructive': v$.name.$error }" />
            <span v-if="v$.name.$error" class="text-xs text-destructive">{{ v$.name.$errors[0]?.$message }}</span>
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
      <template #title>{{ $t('admin.dialog.deleteTitle', { item: $t('admin.nav.permissions') }) }}</template>
      <template #description>{{ $t('admin.dialog.deleteDescription', { item: $t('admin.nav.permissions').toLowerCase() }) }}</template>
    </ConfirmDialog>
  </BasicPage>
</template>
