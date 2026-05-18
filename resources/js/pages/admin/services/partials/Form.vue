<script setup lang="ts">
import { useVuelidate } from '@vuelidate/core'
import { helpers, numeric, required } from '@vuelidate/validators'
import { computed, ref, watch } from 'vue'

import { Button } from '@/components/ui/button'
import { Input } from '@/components/ui/input'
import { Label } from '@/components/ui/label'
import { Sheet, SheetContent, SheetFooter, SheetHeader, SheetTitle } from '@/components/ui/sheet'
import { Switch } from '@/components/ui/switch'
import { Tabs, TabsContent, TabsList, TabsTrigger } from '@/components/ui/tabs'
import { Textarea } from '@/components/ui/textarea'
import ConfirmDialog from '@/components/confirm-dialog.vue'
import ImagePickerField from '@/admin/components/ImagePickerField.vue'

import type { TranslatedValue } from '@/composables/use-translated-form'
import { emptyTranslations, withLanguages } from '@/composables/use-translated-form'
import { translatedRequired } from '@/composables/use-validation'
import { languageMetadata } from '@/plugins/i18n'
import { useCreateServiceMutation, useUpdateServiceMutation } from '@/services/api/services.api'

interface ServiceForm {
  title: TranslatedValue
  description: TranslatedValue
  icon: string
  image_id: number | null
  image_url: string | null
  url: string
  order: number
  is_active: boolean
  seo_title: TranslatedValue
  seo_description: TranslatedValue
  seo_keywords: TranslatedValue
}

const props = defineProps<{
  editingId: number | null
  item: any
  open?: boolean
}>()

const open = defineModel<boolean>('open', { default: false })

const activeFormLocale = ref('fr')
const { mutate: createService } = useCreateServiceMutation()
const { mutate: updateService } = useUpdateServiceMutation()
const showUnsavedDialog = ref(false)

function createEmptyForm(): ServiceForm {
  return {
    title: emptyTranslations(),
    description: emptyTranslations(),
    icon: '',
    image_id: null,
    image_url: null,
    url: '',
    order: 0,
    is_active: true,
    seo_title: emptyTranslations(),
    seo_description: emptyTranslations(),
    seo_keywords: emptyTranslations(),
  }
}

const form = ref<ServiceForm>(createEmptyForm())

const rules = computed(() => ({
  title: { required: translatedRequired() },
  description: { required: translatedRequired() },
  icon: { required: helpers.withMessage('Icon is required', required) },
  order: { numeric },
}))

const v$ = useVuelidate(rules, form)

watch(() => props.open, (isOpen) => {
  if (isOpen) {
    if (props.editingId && props.item) {
      form.value = {
        title: withLanguages(props.item.title_translations, props.item.title),
        description: withLanguages(props.item.description_translations, props.item.description),
        icon: props.item.icon || '',
        image_id: props.item.image_id ?? null,
        image_url: props.item.image_thumbnail_url ?? props.item.image_url ?? null,
        url: props.item.url || '',
        order: props.item.order,
        is_active: props.item.is_active,
        seo_title: withLanguages(props.item.seo_title_translations, props.item.seo_title),
        seo_description: withLanguages(props.item.seo_description_translations, props.item.seo_description),
        seo_keywords: withLanguages(props.item.seo_keywords_translations, props.item.seo_keywords),
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
    updateService({ id: props.editingId, ...payload })
  }
  else {
    createService(payload)
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
    <SheetContent side="right" class="xl:max-w-2xl w-full" @interact-outside.prevent>
      <SheetHeader>
        <SheetTitle>{{ editingId ? $t('admin.sheet.editService') : $t('admin.sheet.createService') }}</SheetTitle>
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
                <Label>{{ $t('admin.label.title') }}</Label>
                <Input v-model="form.title[language.code]" :placeholder="$t('admin.misc.serviceTitlePlaceholder')" :class="{ 'border-destructive': v$.title.$error && language.code === activeFormLocale }" />
                <span v-if="v$.title.$error && language.code === activeFormLocale" class="text-xs text-destructive">{{ v$.title.$errors[0]?.$message }}</span>
              </div>
              <div class="admin-form-field">
                <Label>{{ $t('admin.label.seoTitle') }}</Label>
                <Input v-model="form.seo_title[language.code]" placeholder="SEO title" />
              </div>
            </div>
            <div class="admin-form-field">
              <Label>{{ $t('admin.label.description') }}</Label>
              <Textarea v-model="form.description[language.code]" placeholder="Service description" :class="{ 'border-destructive': v$.description.$error && language.code === activeFormLocale }" />
              <span v-if="v$.description.$error && language.code === activeFormLocale" class="text-xs text-destructive">{{ v$.description.$errors[0]?.$message }}</span>
            </div>
            <div class="admin-form-field">
              <Label>{{ $t('admin.label.seoDescription') }}</Label>
              <Textarea v-model="form.seo_description[language.code]" placeholder="SEO description" />
            </div>
            <div class="admin-form-field">
              <Label>{{ $t('admin.label.seoKeywords') }}</Label>
              <Input v-model="form.seo_keywords[language.code]" placeholder="keyword, another keyword" />
            </div>
          </TabsContent>
        </Tabs>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
          <div class="admin-form-field">
            <Label>{{ $t('admin.label.icon') }}</Label>
            <Input v-model="form.icon" :placeholder="$t('admin.misc.iconPlaceholder')" :class="{ 'border-destructive': v$.icon.$error }" />
            <span v-if="v$.icon.$error" class="text-xs text-destructive">{{ v$.icon.$errors[0]?.$message }}</span>
          </div>
          <div class="admin-form-field">
            <Label>{{ $t('admin.label.url') }}</Label>
            <Input v-model="form.url" :placeholder="$t('admin.misc.urlPlaceholder')" />
          </div>
        </div>
        <ImagePickerField
          v-model:image-id="form.image_id"
          v-model:image-url="form.image_url"
        />

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
          <div class="admin-form-field">
            <Label>{{ $t('admin.label.order') }}</Label>
            <Input v-model.number="form.order" type="number" :class="{ 'border-destructive': v$.order.$error }" />
            <span v-if="v$.order.$error" class="text-xs text-destructive">{{ v$.order.$errors[0]?.$message }}</span>
          </div>
          <div class="flex items-center gap-2 pt-2">
            <Switch v-model:checked="form.is_active" />
            <Label>{{ $t('admin.label.active') }}</Label>
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
</template>
