<script lang="ts" setup>
import { computed, ref } from 'vue'
import { BasicPage } from '@/components/global-layout'
import { Button } from '@/components/ui/button'
import { Badge } from '@/components/ui/badge'
import { useGetFaqsQuery, useDeleteFaqMutation, useCreateFaqMutation, useUpdateFaqMutation } from '@/services/api/faqs.api'
import { Input } from '@/components/ui/input'
import { Textarea } from '@/components/ui/textarea'
import { Dialog, DialogContent, DialogHeader, DialogTitle, DialogFooter } from '@/components/ui/dialog'
import { Label } from '@/components/ui/label'
import { Switch } from '@/components/ui/switch'

const { data: response, isLoading, refetch } = useGetFaqsQuery()
const items = computed(() => response.value?.data?.data ?? [])
const showDialog = ref(false)
const editingId = ref<number | null>(null)
const form = ref({ question: '', answer: '', category: '', order: 0, is_active: true })
const { mutate: deleteItem } = useDeleteFaqMutation()
const { mutate: createItem } = useCreateFaqMutation()
const { mutate: updateItem } = useUpdateFaqMutation(0)

function openCreate() {
  editingId.value = null
  form.value = { question: '', answer: '', category: '', order: 0, is_active: true }
  showDialog.value = true
}

function openEdit(item: any) {
  editingId.value = item.id
  form.value = { question: item.question, answer: item.answer, category: item.category || '', order: item.order, is_active: item.is_active }
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
  <BasicPage title="FAQs" description="Manage frequently asked questions" sticky>
    <template #actions>
      <Button @click="refetch" variant="outline">Refresh</Button>
      <Button @click="openCreate">Create FAQ</Button>
    </template>
    <div class="space-y-4">
      <div v-for="item in items" :key="item.id" class="flex items-start gap-4 rounded-lg border p-4">
        <div class="flex-1 space-y-1">
          <div class="flex items-center gap-2">
            <span class="font-medium">{{ item.question }}</span>
            <Badge :variant="item.is_active ? 'default' : 'secondary'">
              {{ item.is_active ? 'Active' : 'Inactive' }}
            </Badge>
            <Badge v-if="item.category" variant="outline">{{ item.category }}</Badge>
          </div>
          <p class="text-sm text-muted-foreground">{{ item.answer?.slice(0, 150) }}{{ item.answer?.length > 150 ? '...' : '' }}</p>
        </div>
        <div class="flex gap-2">
          <Button variant="ghost" size="sm" @click="openEdit(item)">Edit</Button>
          <Button variant="destructive" size="sm" @click="confirmDelete(item.id)">Delete</Button>
        </div>
      </div>
      <div v-if="items.length === 0 && !isLoading" class="text-center py-8 text-muted-foreground">No FAQs found</div>
    </div>
    <Dialog v-model:open="showDialog">
      <DialogContent>
        <DialogHeader>
          <DialogTitle>{{ editingId ? 'Edit FAQ' : 'Create FAQ' }}</DialogTitle>
        </DialogHeader>
        <div class="space-y-4">
          <div><Label>Question</Label><Input v-model="form.question" placeholder="FAQ question" /></div>
          <div><Label>Answer</Label><Textarea v-model="form.answer" placeholder="FAQ answer" /></div>
          <div><Label>Category</Label><Input v-model="form.category" placeholder="General" /></div>
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
