<script setup lang="ts">
import { useVuelidate } from '@vuelidate/core'
import { numeric } from '@vuelidate/validators'
import { computed, ref, watch } from 'vue'
import { toast } from 'vue-sonner'

import { Button } from '@/components/ui/button'
import { Input } from '@/components/ui/input'
import { Label } from '@/components/ui/label'
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select'
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
import {
  useCreateCatalogCategoryMutation,
  useGetAllCatalogCategoriesQuery,
  useUpdateCatalogCategoryMutation,
} from '@/services/api/catalog.api'

interface CategoryForm {
  name: TranslatedValue
  description: TranslatedValue
  image_id: number | null
  image_url: string | null
  parent_id: number | null
  order: number
  is_active: boolean
}

const props = defineProps<{
  editingId: number | null
  item: any
  open?: boolean
}>()

const open = defineModel<boolean>('open', { default: false })

const activeFormLocale = ref('fr')
const { data: categoriesData } = useGetAllCatalogCategoriesQuery()
const { mutate: createCategory } = useCreateCatalogCategoryMutation()
const { mutate: updateCategory } = useUpdateCatalogCategoryMutation()
const showUnsavedDialog = ref(false)

const categories = computed(() => {
  const d = categoriesData.value as any
  return (d?.data ?? []).filter((c: any) => c.id !== props.editingId)
})

function createEmptyForm(): CategoryForm {
  return {
    name: emptyTranslations(),
    description: emptyTranslations(),
    image_id: null,
    image_url: null,
    parent_id: null,
    order: 0,
    is_active: true,
  }
}

const form = ref<CategoryForm>(createEmptyForm())

const rules = computed(() => ({
  name: { required: translatedRequired() },
  order: { numeric },
}))

const v$ = useVuelidate(rules, form)

watch(() => props.open, (isOpen) => {
  if (isOpen) {
    if (props.editingId && props.item) {
      form.value = {
        name: withLanguages(props.item.name_translations, props.item.name),
        description: withLanguages(props.item.description_translations, props.item.description),
        image_id: props.item.image_id ?? null,
        image_url: props.item.image_thumbnail_url ?? props.item.image_url ?? null,
        parent_id: props.item.parent_id ?? null,
        order: props.item.order,
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
  if (!isValid) return

  const payload = {
    ...form.value,
    image_id: form.value.image_id ?? undefined,
    parent_id: form.value.parent_id ?? undefined,
  }

  if (props.editingId) {
    updateCategory({ id: props.editingId, ...payload }, {
      onSuccess: () => toast.success('Category updated successfully'),
      onError: (error: any) => toast.error(error?.message ?? 'Failed to update category'),
    })
  } else {
    createCategory(payload, {
      onSuccess: () => toast.success('Category created successfully'),
      onError: (error: any) => toast.error(error?.message ?? 'Failed to create category'),
    })
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
        <SheetTitle>{{ editingId ? $t('admin.catalog.editCategory') : $t('admin.catalog.createCategory') }}</SheetTitle>
      </SheetHeader>
      <div class="flex-1 overflow-y-auto px-6 py-4 space-y-6">
        <Tabs v-model="activeFormLocale">
          <TabsList>
            <TabsTrigger v-for="language in languageMetadata" :key="language.code" :value="language.code">
              <span>{{ language.flag }}</span>
              <span>{{ language.name }}</span>
            </TabsTrigger>
          </TabsList>

          <TabsContent v-for="language in languageMetadata" :key="language.code" :value="language.code" class="space-y-4 pt-4">
            <div class="admin-form-field">
              <Label>{{ $t('admin.catalog.name') }}</Label>
              <Input v-model="form.name[language.code]" :placeholder="$t('admin.catalog.namePlaceholder')" :class="{ 'border-destructive': v$.name?.$error && language.code === activeFormLocale }" />
              <span v-if="v$.name?.$error && language.code === activeFormLocale" class="text-xs text-destructive">{{ v$.name?.$errors?.[0]?.$message }}</span>
            </div>
            <div class="admin-form-field">
              <Label>{{ $t('admin.catalog.description') }}</Label>
              <Textarea v-model="form.description[language.code]" :placeholder="$t('admin.catalog.descriptionPlaceholder')" />
            </div>
          </TabsContent>
        </Tabs>

        <div class="admin-form-field">
          <Label>{{ $t('admin.catalog.parentCategory') }}</Label>
          <Select v-model="form.parent_id">
            <SelectTrigger>
              <SelectValue :placeholder="$t('admin.catalog.noParent')" />
            </SelectTrigger>
            <SelectContent>
              <SelectItem :value="null">{{ $t('admin.catalog.noParent') }}</SelectItem>
              <SelectItem v-for="cat in categories" :key="cat.id" :value="cat.id">{{ cat.name }}</SelectItem>
            </SelectContent>
          </Select>
        </div>

        <ImagePickerField v-model:image-id="form.image_id" v-model:image-url="form.image_url" />

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
          <div class="admin-form-field">
            <Label>{{ $t('admin.label.order') }}</Label>
            <Input v-model.number="form.order" type="number" :class="{ 'border-destructive': v$.order?.$error }" />
          </div>
          <div class="flex items-center gap-2 pt-2">
            <Switch v-model:checked="form.is_active" />
            <Label>{{ $t('admin.label.active') }}</Label>
          </div>
        </div>
      </div>
      <SheetFooter>
        <Button variant="outline" @click="handleSheetClose(false)">{{ $t('admin.btn.cancel') }}</Button>
        <Button @click="save">{{ editingId ? $t('admin.btn.update') : $t('admin.btn.create') }}</Button>
      </SheetFooter>
    </SheetContent>
  </Sheet>

  <ConfirmDialog v-model:open="showUnsavedDialog" :cancel-button-text="$t('admin.btn.stay')" :confirm-button-text="$t('admin.btn.discard')" destructive @confirm="forceClose">
    <template #title>{{ $t('admin.dialog.unsavedTitle') }}</template>
    <template #description>{{ $t('admin.dialog.unsavedDescription') }}</template>
  </ConfirmDialog>
</template>
