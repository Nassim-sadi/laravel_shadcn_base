<script lang="ts" setup>
import { computed, ref } from 'vue'
import { BasicPage } from '@/components/global-layout'
import { Button } from '@/components/ui/button'
import { Badge } from '@/components/ui/badge'
import { useGetServicesQuery, useDeleteServiceMutation, useCreateServiceMutation, useUpdateServiceMutation } from '@/services/api/services.api'
import { Input } from '@/components/ui/input'
import { Textarea } from '@/components/ui/textarea'
import { Dialog, DialogContent, DialogHeader, DialogTitle, DialogFooter } from '@/components/ui/dialog'
import { Label } from '@/components/ui/label'
import { Switch } from '@/components/ui/switch'

const { data: response, isLoading, refetch } = useGetServicesQuery()
const services = computed(() => response.value?.data?.data ?? [])

const showDialog = ref(false)
const editingId = ref<number | null>(null)
const form = ref({ title: '', description: '', icon: '', url: '', order: 0, is_active: true })
const { mutate: deleteService } = useDeleteServiceMutation()
const { mutate: createService } = useCreateServiceMutation()
const { mutate: updateService } = useUpdateServiceMutation(0)

function openCreate() {
  editingId.value = null
  form.value = { title: '', description: '', icon: '', url: '', order: 0, is_active: true }
  showDialog.value = true
}

function openEdit(service: any) {
  editingId.value = service.id
  form.value = { title: service.title, description: service.description || '', icon: service.icon || '', url: service.url || '', order: service.order, is_active: service.is_active }
  showDialog.value = true
}

function save() {
  if (editingId.value) {
    updateService({ id: editingId.value, ...form.value } as any)
  } else {
    createService(form.value)
  }
  showDialog.value = false
}

function confirmDelete(id: number) {
  if (confirm('Are you sure you want to delete this service?')) {
    deleteService(id)
  }
}
</script>

<template>
  <BasicPage title="Services" description="Manage your services" sticky>
    <template #actions>
      <Button @click="refetch" variant="outline">Refresh</Button>
      <Button @click="openCreate">Create Service</Button>
    </template>
    <div class="space-y-4">
      <div v-for="service in services" :key="service.id" class="flex items-start gap-4 rounded-lg border p-4">
        <div class="flex-1 space-y-1">
          <div class="flex items-center gap-2">
            <span class="font-medium">{{ service.title }}</span>
            <Badge :variant="service.is_active ? 'default' : 'secondary'">
              {{ service.is_active ? 'Active' : 'Inactive' }}
            </Badge>
          </div>
          <p class="text-sm text-muted-foreground">
            {{ service.description?.slice(0, 100) ?? 'No description' }}
          </p>
          <p class="text-xs text-muted-foreground">
            Order: {{ service.order }} | Icon: {{ service.icon || '-' }}
          </p>
        </div>
        <div class="flex gap-2">
          <Button variant="ghost" size="sm" @click="openEdit(service)">Edit</Button>
          <Button variant="destructive" size="sm" @click="confirmDelete(service.id)">Delete</Button>
        </div>
      </div>
      <div v-if="services.length === 0 && !isLoading" class="text-center py-8 text-muted-foreground">
        No services found
      </div>
    </div>
    <Dialog v-model:open="showDialog">
      <DialogContent>
        <DialogHeader>
          <DialogTitle>{{ editingId ? 'Edit Service' : 'Create Service' }}</DialogTitle>
        </DialogHeader>
        <div class="space-y-4">
          <div>
            <Label>Title</Label>
            <Input v-model="form.title" placeholder="Service title" />
          </div>
          <div>
            <Label>Description</Label>
            <Textarea v-model="form.description" placeholder="Service description" />
          </div>
          <div>
            <Label>Icon</Label>
            <Input v-model="form.icon" placeholder="Icon name" />
          </div>
          <div>
            <Label>URL</Label>
            <Input v-model="form.url" placeholder="https://..." />
          </div>
          <div>
            <Label>Order</Label>
            <Input v-model.number="form.order" type="number" />
          </div>
          <div class="flex items-center gap-2">
            <Switch v-model:checked="form.is_active" />
            <Label>Active</Label>
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
