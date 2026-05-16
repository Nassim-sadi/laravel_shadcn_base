<script lang="ts" setup>
import { computed, ref } from 'vue'
import { BasicPage } from '@/components/global-layout'
import { Button } from '@/components/ui/button'
import { Badge } from '@/components/ui/badge'
import { Tooltip, TooltipContent, TooltipProvider, TooltipTrigger } from '@/components/ui/tooltip'
import { PencilIcon, Trash2Icon } from '@lucide/vue'
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
  <BasicPage :title="$t('admin.page.emailTemplates.title')" :description="$t('admin.page.emailTemplates.description')" sticky>
    <template #actions>
      <Button @click="refetch" variant="outline">{{ $t('admin.btn.refresh') }}</Button>
      <Button @click="openCreate">{{ $t('admin.sheet.createTemplate') }}</Button>
    </template>
    <div class="space-y-4">
      <div v-for="item in items" :key="item.id" class="flex items-start gap-4 rounded-lg border p-4">
        <div class="flex-1 space-y-1">
          <div class="flex items-center gap-2">
            <span class="font-medium">{{ item.name }}</span>
            <Badge :variant="item.is_active ? 'default' : 'secondary'">
              {{ item.is_active ? $t('admin.status.active') : $t('admin.status.inactive') }}
            </Badge>
          </div>
          <p class="text-xs text-muted-foreground">{{ $t('admin.misc.keyLabel', { value: item.key }) }}</p>
          <p class="text-sm">{{ $t('admin.misc.subjectLabel', { value: item.subject }) }}</p>
          <p class="text-xs text-muted-foreground">{{ $t('admin.label.body') }}: {{ item.body?.slice(0, 100) }}...</p>
          <div v-if="item.variables?.length" class="flex gap-1 flex-wrap">
            <Badge v-for="v in item.variables" :key="v" variant="outline">{{ v }}</Badge>
          </div>
        </div>
        <TooltipProvider>
          <div class="flex gap-1">
            <Tooltip>
              <TooltipTrigger as-child>
                <Button variant="ghost" size="icon" class="size-8" @click="openEdit(item)">
                  <PencilIcon class="size-4" />
                </Button>
              </TooltipTrigger>
              <TooltipContent><p>{{ $t('admin.btn.edit') }}</p></TooltipContent>
            </Tooltip>
            <Tooltip>
              <TooltipTrigger as-child>
                <Button variant="destructive" size="icon" class="size-8" @click="confirmDelete(item.id)">
                  <Trash2Icon class="size-4" />
                </Button>
              </TooltipTrigger>
              <TooltipContent><p>{{ $t('admin.btn.delete') }}</p></TooltipContent>
            </Tooltip>
          </div>
        </TooltipProvider>
      </div>
      <div v-if="items.length === 0 && !isLoading" class="text-center py-8 text-muted-foreground">{{ $t('admin.empty.templates') }}</div>
    </div>

    <Sheet :open="showSheet" @update:open="handleSheetClose">
      <SheetContent side="right" class="xl:max-w-2xl w-full">
        <SheetHeader>
          <SheetTitle>{{ editingId ? $t('admin.sheet.editTemplate') : $t('admin.sheet.createTemplate') }}</SheetTitle>
        </SheetHeader>
        <div class="flex-1 overflow-y-auto px-6 py-4 space-y-6">
          <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div class="admin-form-field">
              <Label>{{ $t('admin.label.key') }}</Label>
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
                  <Label>{{ $t('admin.label.name') }}</Label>
                  <Input v-model="form.name[language.code]" placeholder="Welcome Email" :class="{ 'border-destructive': v$.name.$error && language.code === activeFormLocale }" />
                  <span v-if="v$.name.$error && language.code === activeFormLocale" class="text-xs text-destructive">{{ v$.name.$errors[0]?.$message }}</span>
                </div>
                <div class="admin-form-field">
                  <Label>{{ $t('admin.label.subject') }}</Label>
                  <Input v-model="form.subject[language.code]" placeholder="Hello {name}, welcome!" :class="{ 'border-destructive': v$.subject.$error && language.code === activeFormLocale }" />
                  <span v-if="v$.subject.$error && language.code === activeFormLocale" class="text-xs text-destructive">{{ v$.subject.$errors[0]?.$message }}</span>
                </div>
              </div>
              <div class="admin-form-field">
                <Label>{{ $t('admin.label.body') }}</Label>
                <Textarea v-model="form.body[language.code]" placeholder="Email body with {name}, {email} placeholders..." rows="8" :class="{ 'border-destructive': v$.body.$error && language.code === activeFormLocale }" />
                <span v-if="v$.body.$error && language.code === activeFormLocale" class="text-xs text-destructive">{{ v$.body.$errors[0]?.$message }}</span>
              </div>
            </TabsContent>
          </Tabs>

          <div class="flex items-center gap-2">
            <Switch v-model:checked="form.is_active" /><Label>{{ $t('admin.label.active') }}</Label>
          </div>
        </div>
        <SheetFooter>
          <Button variant="outline" @click="handleSheetClose(false)">{{ $t('admin.btn.cancel') }}</Button>
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
      <template #title>{{ $t('admin.dialog.deleteTitle', { item: 'email template' }) }}</template>
      <template #description>{{ $t('admin.dialog.deleteDescription', { item: 'email template' }) }}</template>
    </ConfirmDialog>

    <ConfirmDialog
      v-model:open="showUnsavedDialog"
      :cancel-button-text="$t('admin.btn.stay')"
      :confirm-button-text="$t('admin.btn.discard')"
      destructive
      @confirm="forceClose"
    >
      <template #title>{{ $t('admin.dialog.unsavedTitle') }}</template>
      <template #description>{{ $t('admin.dialog.unsavedDescription') }}</template>
    </ConfirmDialog>
  </BasicPage>
</template>
