<script lang="ts" setup>
import { computed, ref } from 'vue'
import { BasicPage } from '@/components/global-layout'
import { Button } from '@/components/ui/button'
import { Badge } from '@/components/ui/badge'
import { Tooltip, TooltipContent, TooltipProvider, TooltipTrigger } from '@/components/ui/tooltip'
import { PencilIcon, Trash2Icon } from '@lucide/vue'
import { useGetServicesQuery, useDeleteServiceMutation, useCreateServiceMutation, useUpdateServiceMutation } from '@/services/api/services.api'
import { Input } from '@/components/ui/input'
import { Textarea } from '@/components/ui/textarea'
import { Label } from '@/components/ui/label'
import { Switch } from '@/components/ui/switch'
import { Tabs, TabsContent, TabsList, TabsTrigger } from '@/components/ui/tabs'
import { Sheet, SheetContent, SheetHeader, SheetTitle, SheetFooter } from '@/components/ui/sheet'
import { languageMetadata } from '@/plugins/i18n'
import { emptyTranslations, withLanguages } from '@/composables/use-translated-form'
import type { TranslatedValue } from '@/composables/use-translated-form'
import { translatedRequired } from '@/composables/use-validation'
import { useVuelidate } from '@vuelidate/core'
import { required, numeric, helpers } from '@vuelidate/validators'
import ConfirmDialog from '@/components/confirm-dialog.vue'
import ImagePickerField from '@/admin/components/ImagePickerField.vue'

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

const { data: response, isLoading, refetch } = useGetServicesQuery()
const services = computed(() => response.value?.data?.data ?? [])

const showSheet = ref(false)
const editingId = ref<number | null>(null)
const activeFormLocale = ref('fr')
const deleteTargetId = ref<number | null>(null)
const showDeleteDialog = ref(false)
const showUnsavedDialog = ref(false)
const { mutate: deleteService, isPending: isDeleting } = useDeleteServiceMutation()
const { mutate: createService } = useCreateServiceMutation()
const { mutate: updateService } = useUpdateServiceMutation()

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

function openEdit(service: any) {
  editingId.value = service.id
  form.value = {
    title: withLanguages(service.title_translations, service.title),
    description: withLanguages(service.description_translations, service.description),
    icon: service.icon || '',
    image_id: service.image_id ?? null,
    image_url: service.image_thumbnail_url ?? service.image_url ?? null,
    url: service.url || '',
    order: service.order,
    is_active: service.is_active,
    seo_title: withLanguages(service.seo_title_translations, service.seo_title),
    seo_description: withLanguages(service.seo_description_translations, service.seo_description),
    seo_keywords: withLanguages(service.seo_keywords_translations, service.seo_keywords),
  }
  activeFormLocale.value = 'fr'
  showSheet.value = true
}

async function save() {
  const isValid = await v$.value.$validate()
  if (!isValid) return

  const payload = { ...form.value, image_id: form.value.image_id ?? undefined }
  if (editingId.value) {
    updateService({ id: editingId.value, ...payload })
  } else {
    createService(payload)
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
    deleteService(deleteTargetId.value)
  }
  showDeleteDialog.value = false
  deleteTargetId.value = null
}
</script>

<template>
  <BasicPage :title="$t('admin.page.services.title')" :description="$t('admin.page.services.description')" sticky>
    <template #actions>
      <Button @click="refetch" variant="outline">{{ $t('admin.btn.refresh') }}</Button>
      <Button @click="openCreate">{{ $t('admin.sheet.createService') }}</Button>
    </template>
    <div class="space-y-4">
      <div v-for="service in services" :key="service.id" class="flex items-start gap-4 rounded-lg border p-4">
        <div class="flex-1 space-y-1">
          <div class="flex items-center gap-2">
            <span class="font-medium">{{ service.title }}</span>
            <Badge :variant="service.is_active ? 'default' : 'secondary'">
              {{ service.is_active ? $t('admin.status.active') : $t('admin.status.inactive') }}
            </Badge>
          </div>
          <p class="text-sm text-muted-foreground">
            {{ service.description?.slice(0, 100) ?? $t('admin.misc.noDescription') }}
          </p>
          <p class="text-xs text-muted-foreground">
            {{ $t('admin.misc.orderLabel', { value: service.order }) }} | {{ $t('admin.misc.iconLabel', { value: service.icon || '-' }) }}
          </p>
        </div>
        <TooltipProvider>
          <div class="flex gap-1">
            <Tooltip>
              <TooltipTrigger as-child>
                <Button variant="ghost" size="icon" class="size-8" @click="openEdit(service)">
                  <PencilIcon class="size-4" />
                </Button>
              </TooltipTrigger>
              <TooltipContent><p>{{ $t('admin.btn.edit') }}</p></TooltipContent>
            </Tooltip>
            <Tooltip>
              <TooltipTrigger as-child>
                <Button variant="destructive" size="icon" class="size-8" @click="confirmDelete(service.id)">
                  <Trash2Icon class="size-4" />
                </Button>
              </TooltipTrigger>
              <TooltipContent><p>{{ $t('admin.btn.delete') }}</p></TooltipContent>
            </Tooltip>
          </div>
        </TooltipProvider>
      </div>
      <div v-if="services.length === 0 && !isLoading" class="text-center py-8 text-muted-foreground">
        {{ $t('admin.empty.services') }}
      </div>
    </div>

    <Sheet :open="showSheet" @update:open="handleSheetClose">
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
      <template #title>{{ $t('admin.dialog.deleteTitle', { item: $t('admin.nav.services') }) }}</template>
      <template #description>{{ $t('admin.dialog.deleteDescription', { item: $t('admin.nav.services').toLowerCase() }) }}</template>
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
