<script setup lang="ts">
import { useVuelidate } from '@vuelidate/core'
import { maxValue, minValue } from '@vuelidate/validators'
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
import ImagePickerField from '@/admin/components/ImagePickerField.vue'

import { hasPermission } from '@/composables/use-role'
import type { TranslatedValue } from '@/services/api/testimonials.api'
import { emptyTranslations, withLanguages } from '@/composables/use-translated-form'
import { translatedRequired } from '@/composables/use-validation'
import { languageMetadata } from '@/plugins/i18n'
import type { AiContentField } from '@/services/api/ai-content.api'
import { useCreateTestimonialMutation, useUpdateTestimonialMutation } from '@/services/api/testimonials.api'

interface TestimonialForm {
  name: TranslatedValue
  position: TranslatedValue
  company: TranslatedValue
  content: TranslatedValue
  image_id: number | null
  image_url: string | null
  rating: number
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
const { mutate: createItem } = useCreateTestimonialMutation()
const { mutate: updateItem } = useUpdateTestimonialMutation(0)
const showUnsavedDialog = ref(false)
const aiGeneratorOpen = ref(false)

function createEmptyForm(): TestimonialForm {
  return {
    name: emptyTranslations(),
    position: emptyTranslations(),
    company: emptyTranslations(),
    content: emptyTranslations(),
    image_id: null,
    image_url: null,
    rating: 5,
    order: 0,
    is_active: true,
    seo_title: emptyTranslations(),
    seo_description: emptyTranslations(),
  }
}

const form = ref<TestimonialForm>(createEmptyForm())

const rules = computed(() => ({
  name: { required: translatedRequired() },
  content: { required: translatedRequired() },
  rating: { minValue: minValue(1), maxValue: maxValue(5) },
}))

const v$ = useVuelidate(rules, form)

watch(() => props.open, (isOpen) => {
  if (isOpen) {
    if (props.editingId && props.item) {
      form.value = {
        name: withLanguages(props.item.name_translations, props.item.name),
        position: withLanguages(props.item.position_translations, props.item.position),
        company: withLanguages(props.item.company_translations, props.item.company),
        content: withLanguages(props.item.content_translations, props.item.content),
        image_id: props.item.image_id ?? null,
        image_url: props.item.image_thumbnail_url ?? props.item.image_url ?? null,
        rating: props.item.rating,
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

  const payload = { ...form.value, image_id: form.value.image_id ?? undefined }
  if (props.editingId) {
    updateItem({ id: props.editingId, ...payload } as any)
  }
  else {
    createItem(payload)
  }
  open.value = false
}

function forceClose() {
  showUnsavedDialog.value = false
  open.value = false
}

function applyAiDraft(payload: Partial<Record<AiContentField, string>>) {
  const locale = activeFormLocale.value

  if (payload.name !== undefined) {
    form.value.name[locale] = payload.name
  }
  if (payload.position !== undefined) {
    form.value.position[locale] = payload.position
  }
  if (payload.company !== undefined) {
    form.value.company[locale] = payload.company
  }
  if (payload.content !== undefined) {
    form.value.content[locale] = payload.content
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
    name: form.value.name[locale] || '',
    position: form.value.position[locale] || '',
    company: form.value.company[locale] || '',
    content: form.value.content[locale] || '',
    seo_title: form.value.seo_title[locale] || '',
    seo_description: form.value.seo_description[locale] || '',
  }
})
</script>

<template>
  <Sheet :open="open" @update:open="handleSheetClose">
    <SheetContent side="right" class="xl:max-w-2xl w-full" @interact-outside.prevent>
      <SheetHeader>
        <div class="flex items-center justify-between gap-3">
          <SheetTitle>{{ editingId ? $t('admin.sheet.editTestimonial') : $t('admin.sheet.createTestimonial') }}</SheetTitle>
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
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
              <div class="admin-form-field">
                <Label>{{ $t('admin.label.name') }}</Label>
                <Input v-model="form.name[language.code]" :placeholder="$t('admin.misc.clientNamePlaceholder')" :class="{ 'border-destructive': v$.name.$error && language.code === activeFormLocale }" />
                <span v-if="v$.name.$error && language.code === activeFormLocale" class="text-xs text-destructive">{{ v$.name.$errors[0]?.$message }}</span>
              </div>
              <div class="admin-form-field">
                <Label>{{ $t('admin.label.company') }}</Label>
                <Input v-model="form.company[language.code]" :placeholder="$t('admin.misc.companyNamePlaceholder')" />
              </div>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
              <div class="admin-form-field">
                <Label>{{ $t('admin.label.position') }}</Label>
                <Input v-model="form.position[language.code]" :placeholder="$t('admin.misc.positionPlaceholder')" />
              </div>
              <div class="admin-form-field">
                <Label>{{ $t('admin.label.seoTitle') }}</Label>
                <Input v-model="form.seo_title[language.code]" placeholder="SEO title" />
              </div>
            </div>
            <div class="admin-form-field">
              <Label>{{ $t('admin.label.content') }}</Label>
              <Textarea v-model="form.content[language.code]" placeholder="Testimonial text" :class="{ 'border-destructive': v$.content.$error && language.code === activeFormLocale }" />
              <span v-if="v$.content.$error && language.code === activeFormLocale" class="text-xs text-destructive">{{ v$.content.$errors[0]?.$message }}</span>
            </div>
            <div class="admin-form-field">
              <Label>{{ $t('admin.label.seoDescription') }}</Label>
              <Textarea v-model="form.seo_description[language.code]" placeholder="SEO description" />
            </div>
          </TabsContent>
        </Tabs>

        <ImagePickerField
          v-model:image-id="form.image_id"
          v-model:image-url="form.image_url"
        />

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
          <div class="admin-form-field">
            <Label>{{ $t('admin.label.rating') }}</Label>
            <Input v-model.number="form.rating" type="number" min="1" max="5" :class="{ 'border-destructive': v$.rating.$error }" />
            <span v-if="v$.rating.$error" class="text-xs text-destructive">{{ v$.rating.$errors[0]?.$message }}</span>
          </div>
          <div class="admin-form-field">
            <Label>{{ $t('admin.label.order') }}</Label>
            <Input v-model.number="form.order" type="number" />
          </div>
        </div>
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

  <AiContentGeneratorDialog
    v-model:open="aiGeneratorOpen"
    module="testimonials"
    :locale="activeFormLocale"
    :source="aiSource"
    @apply="applyAiDraft"
  />
</template>
