<script setup lang="ts">
import { useVuelidate } from '@vuelidate/core'
import { numeric } from '@vuelidate/validators'
import { computed, ref, watch } from 'vue'
import { SparklesIcon } from '@lucide/vue'

import AiContentGeneratorDialog from '@/admin/components/ai/AiContentGeneratorDialog.vue'
import { Button } from '@/components/ui/button'
import { Input } from '@/components/ui/input'
import { Label } from '@/components/ui/label'
import { Sheet, SheetContent, SheetFooter, SheetHeader, SheetTitle } from '@/components/ui/sheet'
import { Switch } from '@/components/ui/switch'
import { Tabs, TabsContent, TabsList, TabsTrigger } from '@/components/ui/tabs'
import { Textarea } from '@/components/ui/textarea'
import ConfirmDialog from '@/components/confirm-dialog.vue'

import { hasPermission } from '@/composables/use-role'
import type { TranslatedValue } from '@/services/api/faqs.api'
import { emptyTranslations, withLanguages } from '@/composables/use-translated-form'
import { translatedRequired } from '@/composables/use-validation'
import { languageMetadata } from '@/plugins/i18n'
import type { AiContentField } from '@/services/api/ai-content.api'
import { useCreateFaqMutation, useUpdateFaqMutation } from '@/services/api/faqs.api'

interface FaqForm {
  question: TranslatedValue
  answer: TranslatedValue
  category: string
  order: number
  is_active: boolean
  seo_title: TranslatedValue
  seo_description: TranslatedValue
}

const props = defineProps<{
  editingId: number | null
  item: any
  open?: boolean
}>()

const open = defineModel<boolean>('open', { default: false })

const activeFormLocale = ref('fr')
const { mutate: createItem } = useCreateFaqMutation()
const { mutate: updateItem } = useUpdateFaqMutation(0)
const showUnsavedDialog = ref(false)
const aiGeneratorOpen = ref(false)

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

watch(() => props.open, (isOpen) => {
  if (isOpen) {
    if (props.editingId && props.item) {
      form.value = {
        question: withLanguages(props.item.question_translations, props.item.question),
        answer: withLanguages(props.item.answer_translations, props.item.answer),
        category: props.item.category || '',
        order: props.item.order,
        is_active: props.item.is_active,
        seo_title: withLanguages(props.item.seo_title_translations, props.item.seo_title),
        seo_description: withLanguages(props.item.seo_description_translations, props.item.seo_description),
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

function applyAiDraft(payload: Partial<Record<AiContentField, string>>) {
  const locale = activeFormLocale.value

  if (payload.question !== undefined) {
    form.value.question[locale] = payload.question
  }
  if (payload.answer !== undefined) {
    form.value.answer[locale] = payload.answer
  }
  if (payload.seo_title !== undefined) {
    form.value.seo_title[locale] = payload.seo_title
  }
  if (payload.seo_description !== undefined) {
    form.value.seo_description[locale] = payload.seo_description
  }
}

const aiSource = computed<Partial<Record<AiContentField, string>>>(() => {
  const locale = activeFormLocale.value

  return {
    question: form.value.question[locale] || '',
    answer: form.value.answer[locale] || '',
    seo_title: form.value.seo_title[locale] || '',
    seo_description: form.value.seo_description[locale] || '',
  }
})
</script>

<template>
  <Sheet :open="open" @update:open="handleSheetClose">
    <SheetContent side="right" class="xl:max-w-2xl w-full">
      <SheetHeader>
        <div class="flex items-center justify-between gap-3">
          <SheetTitle>{{ editingId ? $t('admin.sheet.editFaq') : $t('admin.sheet.createFaq') }}</SheetTitle>
          <Button v-if="hasPermission('ai.generate')" type="button" variant="outline" size="sm" class="shrink-0" @click="aiGeneratorOpen = true">
            <SparklesIcon class="size-4" />
            <span>Generate</span>
          </Button>
        </div>
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
              <Label>{{ $t('admin.label.question') }}</Label>
              <Input v-model="form.question[language.code]" :placeholder="$t('admin.misc.faqQuestionPlaceholder')" :class="{ 'border-destructive': v$.question.$error && language.code === activeFormLocale }" />
              <span v-if="v$.question.$error && language.code === activeFormLocale" class="text-xs text-destructive">{{ v$.question.$errors[0]?.$message }}</span>
            </div>
            <div class="admin-form-field">
              <Label>{{ $t('admin.label.answer') }}</Label>
              <Textarea v-model="form.answer[language.code]" :placeholder="$t('admin.misc.faqAnswerPlaceholder')" :class="{ 'border-destructive': v$.answer.$error && language.code === activeFormLocale }" />
              <span v-if="v$.answer.$error && language.code === activeFormLocale" class="text-xs text-destructive">{{ v$.answer.$errors[0]?.$message }}</span>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
              <div class="admin-form-field">
                <Label>{{ $t('admin.label.seoTitle') }}</Label>
                <Input v-model="form.seo_title[language.code]" placeholder="SEO title" />
              </div>
              <div class="admin-form-field">
                <Label>{{ $t('admin.label.category') }}</Label>
                <Input v-model="form.category" placeholder="General" />
              </div>
            </div>
            <div class="admin-form-field">
              <Label>{{ $t('admin.label.seoDescription') }}</Label>
              <Textarea v-model="form.seo_description[language.code]" placeholder="SEO description" />
            </div>
          </TabsContent>
        </Tabs>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
          <div class="admin-form-field">
            <Label>{{ $t('admin.label.order') }}</Label>
            <Input v-model.number="form.order" type="number" :class="{ 'border-destructive': v$.order.$error }" />
            <span v-if="v$.order.$error" class="text-xs text-destructive">{{ v$.order.$errors[0]?.$message }}</span>
          </div>
          <div class="flex items-center gap-2 pt-2">
            <Switch v-model:checked="form.is_active" /><Label>{{ $t('admin.label.active') }}</Label>
          </div>
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

  <AiContentGeneratorDialog
    v-model:open="aiGeneratorOpen"
    module="faqs"
    :locale="activeFormLocale"
    :source="aiSource"
    @apply="applyAiDraft"
  />
</template>
