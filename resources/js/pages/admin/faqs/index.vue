<script lang="ts" setup>
import { computed, ref } from 'vue'
import { BasicPage } from '@/components/global-layout'
import { Button } from '@/components/ui/button'
import { Badge } from '@/components/ui/badge'
import { useGetFaqsQuery, useDeleteFaqMutation, useCreateFaqMutation, useUpdateFaqMutation } from '@/services/api/faqs.api'
import { Input } from '@/components/ui/input'
import { Textarea } from '@/components/ui/textarea'
import { Label } from '@/components/ui/label'
import { Switch } from '@/components/ui/switch'
import { Tabs, TabsContent, TabsList, TabsTrigger } from '@/components/ui/tabs'
import { Sheet, SheetContent, SheetHeader, SheetTitle, SheetFooter } from '@/components/ui/sheet'
import { languageMetadata } from '@/plugins/i18n'
import { emptyTranslations, withLanguages } from '@/composables/use-translated-form'
import type { TranslatedValue } from '@/services/api/faqs.api'
import { translatedRequired } from '@/composables/use-validation'
import { useVuelidate } from '@vuelidate/core'
import { numeric } from '@vuelidate/validators'
import ConfirmDialog from '@/components/confirm-dialog.vue'

interface FaqForm {
  question: TranslatedValue
  answer: TranslatedValue
  category: string
  order: number
  is_active: boolean
  seo_title: TranslatedValue
  seo_description: TranslatedValue
}

const { data: response, isLoading, refetch } = useGetFaqsQuery()
const items = computed(() => response.value?.data?.data ?? [])
const showSheet = ref(false)
const editingId = ref<number | null>(null)
const activeFormLocale = ref('fr')
const deleteTargetId = ref<number | null>(null)
const showDeleteDialog = ref(false)
const { mutate: deleteItem, isPending: isDeleting } = useDeleteFaqMutation()
const { mutate: createItem } = useCreateFaqMutation()
const { mutate: updateItem } = useUpdateFaqMutation(0)
const showUnsavedDialog = ref(false)

function createEmptyForm(): FaqForm {
  return {
    question: emptyTranslations(),
    answer: emptyTranslations(),
    category: '',
    order: 0,
    is_active: true,
    seo_title: emptyTranslations(),
    seo_description: emptyTranslations(),
  }
}

const form = ref<FaqForm>(createEmptyForm())

const rules = computed(() => ({
  question: { required: translatedRequired() },
  answer: { required: translatedRequired() },
  order: { numeric },
}))

const v$ = useVuelidate(rules, form)

function handleSheetClose(open: boolean) {
  if (!open) {
    showUnsavedDialog.value = true
    return
  }
  showSheet.value = open
}

function openCreate() {
  editingId.value = null
  form.value = createEmptyForm()
  activeFormLocale.value = 'fr'
  showSheet.value = true
}

function openEdit(item: any) {
  editingId.value = item.id
  form.value = {
    question: withLanguages(item.question_translations, item.question),
    answer: withLanguages(item.answer_translations, item.answer),
    category: item.category || '',
    order: item.order,
    is_active: item.is_active,
    seo_title: withLanguages(item.seo_title_translations, item.seo_title),
    seo_description: withLanguages(item.seo_description_translations, item.seo_description),
  }
  activeFormLocale.value = 'fr'
  showSheet.value = true
}

async function save() {
  const isValid = await v$.value.$validate()
  if (!isValid) return

  if (editingId.value) {
    updateItem({ id: editingId.value, ...form.value } as any)
  } else {
    createItem(form.value)
  }
  showSheet.value = false
}

function forceClose() {
  showUnsavedDialog.value = false
  showSheet.value = false
}

function confirmDelete(id: number) {
  deleteTargetId.value = id
  showDeleteDialog.value = true
}

function handleDelete() {
  if (deleteTargetId.value !== null) {
    deleteItem(deleteTargetId.value)
  }
  showDeleteDialog.value = false
  deleteTargetId.value = null
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

    <Sheet :open="showSheet" @update:open="handleSheetClose">
      <SheetContent side="right" class="xl:max-w-2xl w-full">
        <SheetHeader>
          <SheetTitle>{{ editingId ? 'Edit FAQ' : 'Create FAQ' }}</SheetTitle>
        </SheetHeader>
        <div class="flex-1 overflow-y-auto px-6 py-4 space-y-6">
          <Tabs v-model="activeFormLocale">
            <TabsList>
              <TabsTrigger
                v-for="language in languageMetadata"
                :key="language.code"
                :value="language.code"
              >
                <span>{{ language.flag }}</span>
                <span>{{ language.name }}</span>
              </TabsTrigger>
            </TabsList>

            <TabsContent
              v-for="language in languageMetadata"
              :key="language.code"
              :value="language.code"
              class="space-y-4 pt-4"
            >
              <div class="admin-form-field">
                <Label>Question</Label>
                <Input v-model="form.question[language.code]" placeholder="FAQ question" :class="{ 'border-destructive': v$.question.$error && language.code === activeFormLocale }" />
                <span v-if="v$.question.$error && language.code === activeFormLocale" class="text-xs text-destructive">{{ v$.question.$errors[0]?.$message }}</span>
              </div>
              <div class="admin-form-field">
                <Label>Answer</Label>
                <Textarea v-model="form.answer[language.code]" placeholder="FAQ answer" :class="{ 'border-destructive': v$.answer.$error && language.code === activeFormLocale }" />
                <span v-if="v$.answer.$error && language.code === activeFormLocale" class="text-xs text-destructive">{{ v$.answer.$errors[0]?.$message }}</span>
              </div>
              <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="admin-form-field">
                  <Label>SEO Title</Label>
                  <Input v-model="form.seo_title[language.code]" placeholder="SEO title" />
                </div>
                <div class="admin-form-field">
                  <Label>Category</Label>
                  <Input v-model="form.category" placeholder="General" />
                </div>
              </div>
              <div class="admin-form-field">
                <Label>SEO Description</Label>
                <Textarea v-model="form.seo_description[language.code]" placeholder="SEO description" />
              </div>
            </TabsContent>
          </Tabs>

          <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div class="admin-form-field">
              <Label>Order</Label>
              <Input v-model.number="form.order" type="number" :class="{ 'border-destructive': v$.order.$error }" />
              <span v-if="v$.order.$error" class="text-xs text-destructive">{{ v$.order.$errors[0]?.$message }}</span>
            </div>
            <div class="flex items-center gap-2 pt-2">
              <Switch v-model:checked="form.is_active" /><Label>Active</Label>
            </div>
          </div>
        </div>
        <SheetFooter>
          <Button variant="outline" @click="handleSheetClose(false)">Cancel</Button>
          <Button @click="save">{{ editingId ? 'Update' : 'Create' }}</Button>
        </SheetFooter>
      </SheetContent>
    </Sheet>

    <ConfirmDialog
      v-model:open="showDeleteDialog"
      :is-loading="isDeleting"
      cancel-button-text="Cancel"
      confirm-button-text="Delete"
      destructive
      @confirm="handleDelete"
    >
      <template #title>Delete FAQ</template>
      <template #description>Are you sure you want to delete this FAQ? This action cannot be undone.</template>
    </ConfirmDialog>

    <ConfirmDialog
      v-model:open="showUnsavedDialog"
      cancel-button-text="Stay"
      confirm-button-text="Discard"
      destructive
      @confirm="forceClose"
    >
      <template #title>Unsaved Changes</template>
      <template #description>You have unsaved changes. Are you sure you want to discard them?</template>
    </ConfirmDialog>
  </BasicPage>
</template>
