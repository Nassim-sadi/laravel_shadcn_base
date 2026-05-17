<script setup lang="ts">
import { useVuelidate } from '@vuelidate/core'
import { computed, ref, watch } from 'vue'

import { Button } from '@/components/ui/button'
import { Input } from '@/components/ui/input'
import { Label } from '@/components/ui/label'
import { Sheet, SheetContent, SheetFooter, SheetHeader, SheetTitle } from '@/components/ui/sheet'
import { Switch } from '@/components/ui/switch'
import { Tabs, TabsContent, TabsList, TabsTrigger } from '@/components/ui/tabs'
import { Textarea } from '@/components/ui/textarea'
import { Badge } from '@/components/ui/badge'
import ConfirmDialog from '@/components/confirm-dialog.vue'
import ImagePickerField from '@/admin/components/ImagePickerField.vue'

import type { TranslatedValue } from '@/composables/use-translated-form'
import { emptyTranslations, withLanguages } from '@/composables/use-translated-form'
import { translatedRequired } from '@/composables/use-validation'
import { languageMetadata } from '@/plugins/i18n'
import { useCreateProjectMutation, useUpdateProjectMutation } from '@/services/api/projects.api'

interface ProjectForm {
  title: TranslatedValue
  description: TranslatedValue
  client: TranslatedValue
  image_id: number | null
  image_url: string | null
  url: string
  technologies: string[]
  order: number
  is_active: boolean
  seo_title: TranslatedValue
  seo_description: TranslatedValue
  seo_keywords: TranslatedValue
}

const props = defineProps<{
  editingId: number | null
  item: any
}>()

const open = defineModel<boolean>('open', { default: false })

const activeFormLocale = ref('fr')
const techInput = ref('')
const { mutate: createItem } = useCreateProjectMutation()
const { mutate: updateItem } = useUpdateProjectMutation()
const showUnsavedDialog = ref(false)

function createEmptyForm(): ProjectForm {
  return {
    title: emptyTranslations(),
    description: emptyTranslations(),
    client: emptyTranslations(),
    image_id: null,
    image_url: null,
    url: '',
    technologies: [],
    order: 0,
    is_active: true,
    seo_title: emptyTranslations(),
    seo_description: emptyTranslations(),
    seo_keywords: emptyTranslations(),
  }
}

const form = ref<ProjectForm>(createEmptyForm())

const rules = computed(() => ({
  title: { required: translatedRequired() },
  description: { required: translatedRequired() },
  client: { required: translatedRequired() },
}))

const v$ = useVuelidate(rules, form)

watch(() => props.open, (isOpen) => {
  if (isOpen) {
    if (props.editingId && props.item) {
      form.value = {
        title: withLanguages(props.item.title_translations, props.item.title),
        description: withLanguages(props.item.description_translations, props.item.description),
        client: withLanguages(props.item.client_translations, props.item.client),
        image_id: props.item.image_id ?? null,
        image_url: props.item.image_thumbnail_url ?? props.item.image_url ?? null,
        url: props.item.url || '',
        technologies: props.item.technologies ? [...props.item.technologies] : [],
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
    techInput.value = ''
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

function addTech() {
  if (techInput.value && !form.value.technologies.includes(techInput.value)) {
    form.value.technologies.push(techInput.value)
    techInput.value = ''
  }
}

function removeTech(index: number) {
  form.value.technologies.splice(index, 1)
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
</script>

<template>
  <Sheet :open="open" @update:open="handleSheetClose">
    <SheetContent side="right" class="xl:max-w-2xl w-full" @interact-outside.prevent>
      <SheetHeader>
        <SheetTitle>{{ editingId ? $t('admin.sheet.editProject') : $t('admin.sheet.createProject') }}</SheetTitle>
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
                <Input v-model="form.title[language.code]" :placeholder="$t('admin.misc.projectTitlePlaceholder')" :class="{ 'border-destructive': v$.title.$error && language.code === activeFormLocale }" />
                <span v-if="v$.title.$error && language.code === activeFormLocale" class="text-xs text-destructive">{{ v$.title.$errors[0]?.$message }}</span>
              </div>
              <div class="admin-form-field">
                <Label>{{ $t('admin.label.client') }}</Label>
                <Input v-model="form.client[language.code]" :placeholder="$t('admin.misc.clientNamePlaceholder')" :class="{ 'border-destructive': v$.client.$error && language.code === activeFormLocale }" />
                <span v-if="v$.client.$error && language.code === activeFormLocale" class="text-xs text-destructive">{{ v$.client.$errors[0]?.$message }}</span>
              </div>
            </div>
            <div class="admin-form-field">
              <Label>{{ $t('admin.label.description') }}</Label>
              <Textarea v-model="form.description[language.code]" placeholder="Project description" :class="{ 'border-destructive': v$.description.$error && language.code === activeFormLocale }" />
              <span v-if="v$.description.$error && language.code === activeFormLocale" class="text-xs text-destructive">{{ v$.description.$errors[0]?.$message }}</span>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
              <div class="admin-form-field">
                <Label>{{ $t('admin.label.seoTitle') }}</Label>
                <Input v-model="form.seo_title[language.code]" placeholder="SEO title" />
              </div>
              <div class="admin-form-field">
                <Label>{{ $t('admin.label.seoKeywords') }}</Label>
                <Input v-model="form.seo_keywords[language.code]" placeholder="keyword, another keyword" />
              </div>
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
            <Label>{{ $t('admin.label.url') }}</Label>
            <Input v-model="form.url" :placeholder="$t('admin.misc.urlPlaceholder')" />
          </div>
          <div class="admin-form-field">
            <Label>{{ $t('admin.label.order') }}</Label>
            <Input v-model.number="form.order" type="number" />
          </div>
        </div>
        <div>
          <Label>{{ $t('admin.label.technologies') }}</Label>
          <div class="flex gap-2 mt-1">
            <Input v-model="techInput" placeholder="Add tech" @keydown.enter.prevent="addTech" />
            <Button size="sm" @click="addTech">
              {{ $t('admin.btn.add') }}
            </Button>
          </div>
          <div class="flex gap-1 flex-wrap mt-2">
            <Badge v-for="(tech, i) in form.technologies" :key="i" variant="secondary" class="cursor-pointer" @click="removeTech(i)">
              {{ tech }} ×
            </Badge>
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
</template>
