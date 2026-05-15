<script lang="ts" setup>
import { computed, ref } from 'vue'
import { BasicPage } from '@/components/global-layout'
import { Button } from '@/components/ui/button'
import { Badge } from '@/components/ui/badge'
import { useGetProjectsQuery, useDeleteProjectMutation, useCreateProjectMutation, useUpdateProjectMutation } from '@/services/api/projects.api'
import { Input } from '@/components/ui/input'
import { Textarea } from '@/components/ui/textarea'
import { Dialog, DialogContent, DialogHeader, DialogTitle, DialogFooter } from '@/components/ui/dialog'
import { Label } from '@/components/ui/label'
import { Switch } from '@/components/ui/switch'

const { data: response, isLoading, refetch } = useGetProjectsQuery()
const items = computed(() => response.value?.data?.data ?? [])
const showDialog = ref(false)
const editingId = ref<number | null>(null)
const form = ref({ title: '', description: '', client: '', url: '', technologies: [] as string[], order: 0, is_active: true })
const techInput = ref('')
const { mutate: deleteItem } = useDeleteProjectMutation()
const { mutate: createItem } = useCreateProjectMutation()
const { mutate: updateItem } = useUpdateProjectMutation(0)

function openCreate() {
  editingId.value = null
  form.value = { title: '', description: '', client: '', url: '', technologies: [], order: 0, is_active: true }
  showDialog.value = true
}

function openEdit(item: any) {
  editingId.value = item.id
  form.value = { title: item.title, description: item.description || '', client: item.client || '', url: item.url || '', technologies: item.technologies || [], order: item.order, is_active: item.is_active }
  showDialog.value = true
}

function addTech() {
  if (techInput.value && !form.value.technologies.includes(techInput.value)) {
    form.value.technologies.push(techInput.value)
    techInput.value = ''
  }
}

function removeTech(index: number) {
  form.value.technologies.splice(index, 1)
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
  <BasicPage title="Projects" description="Manage your projects" sticky>
    <template #actions>
      <Button @click="refetch" variant="outline">Refresh</Button>
      <Button @click="openCreate">Create Project</Button>
    </template>
    <div class="space-y-4">
      <div v-for="item in items" :key="item.id" class="flex items-start gap-4 rounded-lg border p-4">
        <div class="flex-1 space-y-1">
          <div class="flex items-center gap-2">
            <span class="font-medium">{{ item.title }}</span>
            <Badge :variant="item.is_active ? 'default' : 'secondary'">
              {{ item.is_active ? 'Active' : 'Inactive' }}
            </Badge>
          </div>
          <p class="text-sm text-muted-foreground">{{ item.description?.slice(0, 100) || 'No description' }}</p>
          <p class="text-xs text-muted-foreground">Client: {{ item.client || '-' }} | Order: {{ item.order }}</p>
          <div v-if="item.technologies?.length" class="flex gap-1 flex-wrap">
            <Badge v-for="tech in item.technologies" :key="tech" variant="outline">{{ tech }}</Badge>
          </div>
        </div>
        <div class="flex gap-2">
          <Button variant="ghost" size="sm" @click="openEdit(item)">Edit</Button>
          <Button variant="destructive" size="sm" @click="confirmDelete(item.id)">Delete</Button>
        </div>
      </div>
      <div v-if="items.length === 0 && !isLoading" class="text-center py-8 text-muted-foreground">No projects found</div>
    </div>
    <Dialog v-model:open="showDialog">
      <DialogContent>
        <DialogHeader>
          <DialogTitle>{{ editingId ? 'Edit Project' : 'Create Project' }}</DialogTitle>
        </DialogHeader>
        <div class="space-y-4">
          <div><Label>Title</Label><Input v-model="form.title" placeholder="Project title" /></div>
          <div><Label>Description</Label><Textarea v-model="form.description" placeholder="Project description" /></div>
          <div><Label>Client</Label><Input v-model="form.client" placeholder="Client name" /></div>
          <div><Label>URL</Label><Input v-model="form.url" placeholder="https://..." /></div>
          <div>
            <Label>Technologies</Label>
            <div class="flex gap-2">
              <Input v-model="techInput" placeholder="Add tech" @keydown.enter.prevent="addTech" />
              <Button @click="addTech" size="sm">Add</Button>
            </div>
            <div class="flex gap-1 flex-wrap mt-2">
              <Badge v-for="(tech, i) in form.technologies" :key="i" variant="secondary" class="cursor-pointer" @click="removeTech(i)">{{ tech }} ×</Badge>
            </div>
          </div>
          <div><Label>Order</Label><Input v-model.number="form.order" type="number" /></div>
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
