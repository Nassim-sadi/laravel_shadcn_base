<script lang="ts" setup>
import { computed, ref } from 'vue'
import { BasicPage } from '@/components/global-layout'
import { Button } from '@/components/ui/button'
import { Badge } from '@/components/ui/badge'
import { Tooltip, TooltipContent, TooltipProvider, TooltipTrigger } from '@/components/ui/tooltip'
import { PencilIcon, Trash2Icon } from '@lucide/vue'
import { useGetTestimonialsQuery, useDeleteTestimonialMutation, useCreateTestimonialMutation, useUpdateTestimonialMutation } from '@/services/api/testimonials.api'
import { Input } from '@/components/ui/input'
import { Textarea } from '@/components/ui/textarea'
import { Label } from '@/components/ui/label'
import { Tabs, TabsContent, TabsList, TabsTrigger } from '@/components/ui/tabs'
import { Sheet, SheetContent, SheetHeader, SheetTitle, SheetFooter } from '@/components/ui/sheet'
import { Switch } from '@/components/ui/switch'
import { languageMetadata } from '@/plugins/i18n'
import { emptyTranslations, withLanguages } from '@/composables/use-translated-form'
import type { TranslatedValue } from '@/services/api/testimonials.api'
import { translatedRequired } from '@/composables/use-validation'
import { useVuelidate } from '@vuelidate/core'
import { minValue, maxValue } from '@vuelidate/validators'
import ConfirmDialog from '@/components/confirm-dialog.vue'
import ImagePickerField from '@/admin/components/ImagePickerField.vue'

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

const { data: response, isLoading, refetch } = useGetTestimonialsQuery()
const items = computed(() => response.value?.data?.data ?? [])
const showSheet = ref(false)
const editingId = ref<number | null>(null)
const activeFormLocale = ref('fr')
const deleteTargetId = ref<number | null>(null)
const showDeleteDialog = ref(false)
const { mutate: deleteItem, isPending: isDeleting } = useDeleteTestimonialMutation()
const { mutate: createItem } = useCreateTestimonialMutation()
const { mutate: updateItem } = useUpdateTestimonialMutation(0)
const showUnsavedDialog = ref(false)

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
    name: withLanguages(item.name_translations, item.name),
    position: withLanguages(item.position_translations, item.position),
    company: withLanguages(item.company_translations, item.company),
    content: withLanguages(item.content_translations, item.content),
    image_id: item.image_id ?? null,
    image_url: item.image_thumbnail_url ?? item.image_url ?? null,
    rating: item.rating,
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

  const payload = { ...form.value, image_id: form.value.image_id ?? undefined }
  if (editingId.value) {
    updateItem({ id: editingId.value, ...payload } as any)
  } else {
    createItem(payload)
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
  <BasicPage :title="$t('admin.page.testimonials.title')" :description="$t('admin.page.testimonials.description')" sticky>
    <template #actions>
      <Button @click="refetch" variant="outline">{{ $t('admin.btn.refresh') }}</Button>
      <Button @click="openCreate">{{ $t('admin.sheet.createTestimonial') }}</Button>
    </template>
    <div class="space-y-4">
      <div v-for="item in items" :key="item.id" class="flex items-start gap-4 rounded-lg border p-4">
        <div class="flex-1 space-y-1">
          <div class="flex items-center gap-2">
            <span class="font-medium">{{ item.name }}</span>
            <Badge :variant="item.is_active ? 'default' : 'secondary'">
              {{ item.is_active ? $t('admin.status.active') : $t('admin.status.inactive') }}
            </Badge>
            <Badge variant="outline">★ {{ item.rating }}</Badge>
          </div>
          <p class="text-sm text-muted-foreground">{{ item.position }}{{ item.company ? ` at ${item.company}` : '' }}</p>
          <p class="text-sm">{{ item.content?.slice(0, 150) }}{{ item.content?.length > 150 ? '...' : '' }}</p>
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
      <div v-if="items.length === 0 && !isLoading" class="text-center py-8 text-muted-foreground">{{ $t('admin.empty.testimonials') }}</div>
    </div>

    <Sheet :open="showSheet" @update:open="handleSheetClose">
      <SheetContent side="right" class="xl:max-w-2xl w-full" @interact-outside.prevent>
        <SheetHeader>
          <SheetTitle>{{ editingId ? $t('admin.sheet.editTestimonial') : $t('admin.sheet.createTestimonial') }}</SheetTitle>
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
      <template #title>{{ $t('admin.dialog.deleteTitle', { item: $t('admin.nav.testimonials') }) }}</template>
      <template #description>{{ $t('admin.dialog.deleteDescription', { item: $t('admin.nav.testimonials').toLowerCase() }) }}</template>
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
