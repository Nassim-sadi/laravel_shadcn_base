<script setup lang="ts">
import { useVuelidate } from '@vuelidate/core'
import { helpers, required } from '@vuelidate/validators'
import { computed, ref, watch } from 'vue'

import { Button } from '@/components/ui/button'
import { Input } from '@/components/ui/input'
import { Label } from '@/components/ui/label'
import { Sheet, SheetContent, SheetFooter, SheetHeader, SheetTitle } from '@/components/ui/sheet'
import { Switch } from '@/components/ui/switch'
import { Tabs, TabsContent, TabsList, TabsTrigger } from '@/components/ui/tabs'
import { Textarea } from '@/components/ui/textarea'
import ConfirmDialog from '@/components/confirm-dialog.vue'

import type { TranslatedValue } from '@/services/api/email-templates.api'
import { emptyTranslations, withLanguages } from '@/composables/use-translated-form'
import { translatedRequired } from '@/composables/use-validation'
import { languageMetadata } from '@/plugins/i18n'
import { useCreateEmailTemplateMutation, useUpdateEmailTemplateMutation } from '@/services/api/email-templates.api'

interface EmailTemplateForm {
  key: string
  name: TranslatedValue
  subject: TranslatedValue
  body: TranslatedValue
  is_active: boolean
}

const props = defineProps<{
  editingId: number | null
  item: any
}>()

const open = defineModel<boolean>('open', { default: false })

const activeFormLocale = ref('fr')
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

watch(() => props.open, (isOpen) => {
  if (isOpen) {
    if (props.editingId && props.item) {
      form.value = {
        key: props.item.key,
        name: withLanguages(props.item.name_translations, props.item.name),
        subject: withLanguages(props.item.subject_translations, props.item.subject),
        body: withLanguages(props.item.body_translations, props.item.body),
        is_active: props.item.is_active,
      }
    } else {
      form.value = createEmptyForm()
    }
    activeFormLocale.value = 'fr'
    v$.value.$reset()
  }
})

function handleSheetClose(isOpen: boolean) {
  if (!isOpen) {
    showUnsavedDialog.value = true
    return
  }
  open.value = isOpen
}

async function save() {
  const isValid = await v$.value.$validate()
  if (!isValid)
    return

  if (props.editingId) {
    updateItem({ id: props.editingId, ...form.value } as any)
  }
  else {
    createItem(form.value)
  }
  open.value = false
}

function forceClose() {
  showUnsavedDialog.value = false
  open.value = false
}
</script>

<template>
  <Sheet :open="open" @update:open="handleSheetClose">
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
        <Button variant="outline" @click="handleSheetClose(false)">
          {{ $t('admin.btn.cancel') }}
        </Button>
        <Button @click="save">
          {{ editingId ? $t('admin.btn.update') : $t('admin.btn.create') }}
        </Button>
      </SheetFooter>
    </SheetContent>
  </Sheet>

  <ConfirmDialog
    v-model:open="showUnsavedDialog"
    :cancel-button-text="$t('admin.btn.stay')"
    :confirm-button-text="$t('admin.btn.discard')"
    destructive
    @confirm="forceClose"
  >
    <template #title>
      {{ $t('admin.dialog.unsavedTitle') }}
    </template>
    <template #description>
      {{ $t('admin.dialog.unsavedDescription') }}
    </template>
  </ConfirmDialog>
</template>
