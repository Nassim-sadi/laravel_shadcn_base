<script lang="ts" setup>
import { computed, ref } from 'vue'
import { BasicPage } from '@/components/global-layout'
import { Button } from '@/components/ui/button'
import { Badge } from '@/components/ui/badge'
import { useGetEmailTemplatesQuery, useDeleteEmailTemplateMutation, useCreateEmailTemplateMutation, useUpdateEmailTemplateMutation } from '@/services/api/email-templates.api'
import type { TranslatedValue } from '@/services/api/email-templates.api'
import { Input } from '@/components/ui/input'
import { Textarea } from '@/components/ui/textarea'
import { Label } from '@/components/ui/label'
import { Switch } from '@/components/ui/switch'
import { Tabs, TabsContent, TabsList, TabsTrigger } from '@/components/ui/tabs'
import { Sheet, SheetContent, SheetHeader, SheetTitle, SheetFooter } from '@/components/ui/sheet'
import { languageMetadata } from '@/plugins/i18n'
import { emptyTranslations, withLanguages } from '@/composables/use-translated-form'
import { translatedRequired } from '@/composables/use-validation'
import { useVuelidate } from '@vuelidate/core'
import { required, helpers } from '@vuelidate/validators'
import ConfirmDialog from '@/components/confirm-dialog.vue'

interface EmailTemplateForm {
  key: string
  name: TranslatedValue
  subject: TranslatedValue
  body: TranslatedValue
  is_active: boolean
}

const { data: response, isLoading, refetch } = useGetEmailTemplatesQuery()
const items = computed(() => response.value?.data?.data ?? [])
const showSheet = ref(false)
const editingId = ref<number | null>(null)
const activeFormLocale = ref('fr')
const deleteTargetId = ref<number | null>(null)
const showDeleteDialog = ref(false)
const { mutate: deleteItem, isPending: isDeleting } = useDeleteEmailTemplateMutation()
const { mutate: createItem } = useCreateEmailTemplateMutation()
const { mutate: updateItem } = useUpdateEmailTemplateMutation(0)
const showUnsavedDialog = ref(false)

function createEmptyForm(): EmailTemplateForm {
  return {
    key: '',
    name: emptyTranslations(),
    subject: emptyTranslations(),
    body: emptyTranslations(),
    is_active: true,
  }
}

const form = ref<EmailTemplateForm>(createEmptyForm())

const rules = computed(() => ({
  key: { required: helpers.withMessage('Key is required', required) },
  name: { required: translatedRequired() },
  subject: { required: translatedRequired() },
  body: { required: translatedRequired() },
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
    key: item.key,
    name: withLanguages(item.name_translations, item.name),
    subject: withLanguages(item.subject_translations, item.subject),
    body: withLanguages(item.body_translations, item.body),
    is_active: item.is_active,
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

    <Sheet :open="showSheet" @update:open="handleSheetClose">
      <SheetContent side="right" class="xl:max-w-2xl w-full">
        <SheetHeader>
          <SheetTitle>{{ editingId ? 'Edit Template' : 'Create Template' }}</SheetTitle>
        </SheetHeader>
        <div class="flex-1 overflow-y-auto px-6 py-4 space-y-6">
          <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div class="admin-form-field">
              <Label>Key</Label>
              <Input v-model="form.key" placeholder="welcome.email" :disabled="!!editingId" :class="{ 'border-destructive': v$.key.$error }" />
              <span v-if="v$.key.$error" class="text-xs text-destructive">{{ v$.key.$errors[0]?.$message }}</span>
            </div>
          </div>
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
              <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="admin-form-field">
                  <Label>Name</Label>
                  <Input v-model="form.name[language.code]" placeholder="Welcome Email" :class="{ 'border-destructive': v$.name.$error && language.code === activeFormLocale }" />
                  <span v-if="v$.name.$error && language.code === activeFormLocale" class="text-xs text-destructive">{{ v$.name.$errors[0]?.$message }}</span>
                </div>
                <div class="admin-form-field">
                  <Label>Subject</Label>
                  <Input v-model="form.subject[language.code]" placeholder="Hello {name}, welcome!" :class="{ 'border-destructive': v$.subject.$error && language.code === activeFormLocale }" />
                  <span v-if="v$.subject.$error && language.code === activeFormLocale" class="text-xs text-destructive">{{ v$.subject.$errors[0]?.$message }}</span>
                </div>
              </div>
              <div class="admin-form-field">
                <Label>Body</Label>
                <Textarea v-model="form.body[language.code]" placeholder="Email body with {name}, {email} placeholders..." rows="8" :class="{ 'border-destructive': v$.body.$error && language.code === activeFormLocale }" />
                <span v-if="v$.body.$error && language.code === activeFormLocale" class="text-xs text-destructive">{{ v$.body.$errors[0]?.$message }}</span>
              </div>
            </TabsContent>
          </Tabs>

          <div class="flex items-center gap-2">
            <Switch v-model:checked="form.is_active" /><Label>Active</Label>
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
      <template #title>Delete Template</template>
      <template #description>Are you sure you want to delete this email template? This action cannot be undone.</template>
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
