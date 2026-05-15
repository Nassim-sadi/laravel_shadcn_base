<script lang="ts" setup>
import { computed, ref } from 'vue'
import { BasicPage } from '@/components/global-layout'
import { Button } from '@/components/ui/button'
import { Badge } from '@/components/ui/badge'
import { useGetTestimonialsQuery, useDeleteTestimonialMutation, useCreateTestimonialMutation, useUpdateTestimonialMutation } from '@/services/api/testimonials.api'
import { Input } from '@/components/ui/input'
import { Textarea } from '@/components/ui/textarea'
import { Dialog, DialogContent, DialogHeader, DialogTitle, DialogFooter } from '@/components/ui/dialog'
import { Label } from '@/components/ui/label'
import { Switch } from '@/components/ui/switch'

const { data: response, isLoading, refetch } = useGetTestimonialsQuery()
const items = computed(() => response.value?.data?.data ?? [])
const showDialog = ref(false)
const editingId = ref<number | null>(null)
const form = ref({ name: '', position: '', company: '', content: '', rating: 5, order: 0, is_active: true })
const { mutate: deleteItem } = useDeleteTestimonialMutation()
const { mutate: createItem } = useCreateTestimonialMutation()
const { mutate: updateItem } = useUpdateTestimonialMutation(0)

function openCreate() {
  editingId.value = null
  form.value = { name: '', position: '', company: '', content: '', rating: 5, order: 0, is_active: true }
  showDialog.value = true
}

function openEdit(item: any) {
  editingId.value = item.id
  form.value = { name: item.name, position: item.position || '', company: item.company || '', content: item.content, rating: item.rating, order: item.order, is_active: item.is_active }
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
  <BasicPage title="Testimonials" description="Manage client testimonials" sticky>
    <template #actions>
      <Button @click="refetch" variant="outline">Refresh</Button>
      <Button @click="openCreate">Create Testimonial</Button>
    </template>
    <div class="space-y-4">
      <div v-for="item in items" :key="item.id" class="flex items-start gap-4 rounded-lg border p-4">
        <div class="flex-1 space-y-1">
          <div class="flex items-center gap-2">
            <span class="font-medium">{{ item.name }}</span>
            <Badge :variant="item.is_active ? 'default' : 'secondary'">
              {{ item.is_active ? 'Active' : 'Inactive' }}
            </Badge>
            <Badge variant="outline">★ {{ item.rating }}</Badge>
          </div>
          <p class="text-sm text-muted-foreground">{{ item.position }}{{ item.company ? ` at ${item.company}` : '' }}</p>
          <p class="text-sm">{{ item.content?.slice(0, 150) }}{{ item.content?.length > 150 ? '...' : '' }}</p>
        </div>
        <div class="flex gap-2">
          <Button variant="ghost" size="sm" @click="openEdit(item)">Edit</Button>
          <Button variant="destructive" size="sm" @click="confirmDelete(item.id)">Delete</Button>
        </div>
      </div>
      <div v-if="items.length === 0 && !isLoading" class="text-center py-8 text-muted-foreground">No testimonials found</div>
    </div>
    <Dialog v-model:open="showDialog">
      <DialogContent>
        <DialogHeader>
          <DialogTitle>{{ editingId ? 'Edit Testimonial' : 'Create Testimonial' }}</DialogTitle>
        </DialogHeader>
        <div class="space-y-4">
          <div><Label>Name</Label><Input v-model="form.name" placeholder="Client name" /></div>
          <div><Label>Position</Label><Input v-model="form.position" placeholder="CEO" /></div>
          <div><Label>Company</Label><Input v-model="form.company" placeholder="Company name" /></div>
          <div><Label>Content</Label><Textarea v-model="form.content" placeholder="Testimonial text" /></div>
          <div><Label>Rating (1-5)</Label><Input v-model.number="form.rating" type="number" min="1" max="5" /></div>
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
