<script lang="ts" setup>
import { computed, ref } from 'vue'
import { BasicPage } from '@/components/global-layout'
import { Button } from '@/components/ui/button'
import { Badge } from '@/components/ui/badge'
import { useGetEmailTemplatesQuery, useDeleteEmailTemplateMutation, useCreateEmailTemplateMutation, useUpdateEmailTemplateMutation } from '@/services/api/email-templates.api'
import { Input } from '@/components/ui/input'
import { Textarea } from '@/components/ui/textarea'
import { Dialog, DialogContent, DialogHeader, DialogTitle, DialogFooter } from '@/components/ui/dialog'
import { Label } from '@/components/ui/label'
import { Switch } from '@/components/ui/switch'

const { data: response, isLoading, refetch } = useGetEmailTemplatesQuery()
const items = computed(() => response.value?.data?.data ?? [])
const showDialog = ref(false)
const editingId = ref<number | null>(null)
const form = ref({ key: '', name: '', subject: '', body: '', is_active: true })
const { mutate: deleteItem } = useDeleteEmailTemplateMutation()
const { mutate: createItem } = useCreateEmailTemplateMutation()
const { mutate: updateItem } = useUpdateEmailTemplateMutation(0)

function openCreate() {
  editingId.value = null
  form.value = { key: '', name: '', subject: '', body: '', is_active: true }
  showDialog.value = true
}

function openEdit(item: any) {
  editingId.value = item.id
  form.value = { key: item.key, name: item.name, subject: item.subject, body: item.body, is_active: item.is_active }
  showDialog.value = true
}

function save() {
  if (editingId.value) {
    updateItem({ id: editingId.value, ...form.value } as any)
  } else {
    createItem(form.value)
  }
  showDialog.value = false
}

function confirmDelete(id: number) {
  if (confirm('Are you sure?')) deleteItem(id)
}
</script>

<template>
  <BasicPage title="Email Templates" description="Manage email notification templates" sticky>
    <template #actions>
      <Button @click="refetch" variant="outline">Refresh</Button>
      <Button @click="openCreate">Create Template</Button>
    </template>
    <div class="space-y-4">
      <div v-for="item in items" :key="item.id" class="flex items-start gap-4 rounded-lg border p-4">
        <div class="flex-1 space-y-1">
          <div class="flex items-center gap-2">
            <span class="font-medium">{{ item.name }}</span>
            <Badge :variant="item.is_active ? 'default' : 'secondary'">
              {{ item.is_active ? 'Active' : 'Inactive' }}
            </Badge>
          </div>
          <p class="text-xs text-muted-foreground">Key: {{ item.key }}</p>
          <p class="text-sm">Subject: {{ item.subject }}</p>
          <p class="text-xs text-muted-foreground">Body: {{ item.body?.slice(0, 100) }}...</p>
          <div v-if="item.variables?.length" class="flex gap-1 flex-wrap">
            <Badge v-for="v in item.variables" :key="v" variant="outline">{{ v }}</Badge>
          </div>
        </div>
        <div class="flex gap-2">
          <Button variant="ghost" size="sm" @click="openEdit(item)">Edit</Button>
          <Button variant="destructive" size="sm" @click="confirmDelete(item.id)">Delete</Button>
        </div>
      </div>
      <div v-if="items.length === 0 && !isLoading" class="text-center py-8 text-muted-foreground">No templates found</div>
    </div>
    <Dialog v-model:open="showDialog">
      <DialogContent class="max-w-2xl">
        <DialogHeader>
          <DialogTitle>{{ editingId ? 'Edit Template' : 'Create Template' }}</DialogTitle>
        </DialogHeader>
        <div class="space-y-4">
          <div class="grid grid-cols-2 gap-4">
            <div><Label>Key</Label><Input v-model="form.key" placeholder="welcome.email" :disabled="!!editingId" /></div>
            <div><Label>Name</Label><Input v-model="form.name" placeholder="Welcome Email" /></div>
          </div>
          <div><Label>Subject</Label><Input v-model="form.subject" placeholder="Hello {name}, welcome!" /></div>
          <div><Label>Body</Label><Textarea v-model="form.body" placeholder="Email body with {name}, {email} placeholders..." rows="8" /></div>
          <div class="flex items-center gap-2">
            <Switch v-model:checked="form.is_active" /><Label>Active</Label>
          </div>
        </div>
        <DialogFooter>
          <Button variant="outline" @click="showDialog = false">Cancel</Button>
          <Button @click="save">{{ editingId ? 'Update' : 'Create' }}</Button>
        </DialogFooter>
      </DialogContent>
    </Dialog>
  </BasicPage>
</template>
