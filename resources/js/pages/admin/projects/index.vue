<script lang="ts" setup>
import { computed, ref } from 'vue'
import { BasicPage } from '@/components/global-layout'
import { Button } from '@/components/ui/button'
import { Badge } from '@/components/ui/badge'
import { useGetProjectsQuery, useDeleteProjectMutation, useCreateProjectMutation, useUpdateProjectMutation } from '@/services/api/projects.api'
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
import ConfirmDialog from '@/components/confirm-dialog.vue'
import ImagePickerField from '@/admin/components/ImagePickerField.vue'

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

const { data: response, isLoading, refetch } = useGetProjectsQuery()
const items = computed(() => response.value?.data?.data ?? [])
const showSheet = ref(false)
const editingId = ref<number | null>(null)
const activeFormLocale = ref('fr')
const techInput = ref('')
const deleteTargetId = ref<number | null>(null)
const showDeleteDialog = ref(false)
const { mutate: deleteItem, isPending: isDeleting } = useDeleteProjectMutation()
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
    title: withLanguages(item.title_translations, item.title),
    description: withLanguages(item.description_translations, item.description),
    client: withLanguages(item.client_translations, item.client),
    image_id: item.image_id ?? null,
    image_url: item.image_thumbnail_url ?? item.image_url ?? null,
    url: item.url || '',
    technologies: item.technologies || [],
    order: item.order,
    is_active: item.is_active,
    seo_title: withLanguages(item.seo_title_translations, item.seo_title),
    seo_description: withLanguages(item.seo_description_translations, item.seo_description),
    seo_keywords: withLanguages(item.seo_keywords_translations, item.seo_keywords),
  }
  activeFormLocale.value = 'fr'
  showSheet.value = true
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
  <BasicPage title="Projects" description="Manage your projects" sticky>
    <template #actions>
      <Button @click="refetch" variant="outline">Refresh</Button>
      <Button @click="openCreate">Create Project</Button>
    </template>
    <div class="space-y-4">
      <div v-for="item in items" :key="item.id" class="flex items-start gap-4 rounded-lg border p-4">
        <div class="flex-1 space-y-1">
          <div class="flex items-center gap-2">
            <span class="font-medium">{{ item.title }}</span>
            <Badge :variant="item.is_active ? 'default' : 'secondary'">
              {{ item.is_active ? 'Active' : 'Inactive' }}
            </Badge>
          </div>
          <p class="text-sm text-muted-foreground">{{ item.description?.slice(0, 100) || 'No description' }}</p>
          <p class="text-xs text-muted-foreground">Client: {{ item.client || '-' }} | Order: {{ item.order }}</p>
          <div v-if="item.technologies?.length" class="flex gap-1 flex-wrap">
            <Badge v-for="tech in item.technologies" :key="tech" variant="outline">{{ tech }}</Badge>
          </div>
        </div>
        <div class="flex gap-2">
          <Button variant="ghost" size="sm" @click="openEdit(item)">Edit</Button>
          <Button variant="destructive" size="sm" @click="confirmDelete(item.id)">Delete</Button>
        </div>
      </div>
      <div v-if="items.length === 0 && !isLoading" class="text-center py-8 text-muted-foreground">No projects found</div>
    </div>

    <Sheet :open="showSheet" @update:open="handleSheetClose">
      <SheetContent side="right" class="xl:max-w-2xl w-full">
        <SheetHeader>
          <SheetTitle>{{ editingId ? 'Edit Project' : 'Create Project' }}</SheetTitle>
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
                  <Label>Title</Label>
                  <Input v-model="form.title[language.code]" placeholder="Project title" :class="{ 'border-destructive': v$.title.$error && language.code === activeFormLocale }" />
                  <span v-if="v$.title.$error && language.code === activeFormLocale" class="text-xs text-destructive">{{ v$.title.$errors[0]?.$message }}</span>
                </div>
                <div class="admin-form-field">
                  <Label>Client</Label>
                  <Input v-model="form.client[language.code]" placeholder="Client name" :class="{ 'border-destructive': v$.client.$error && language.code === activeFormLocale }" />
                  <span v-if="v$.client.$error && language.code === activeFormLocale" class="text-xs text-destructive">{{ v$.client.$errors[0]?.$message }}</span>
                </div>
              </div>
              <div class="admin-form-field">
                <Label>Description</Label>
                <Textarea v-model="form.description[language.code]" placeholder="Project description" :class="{ 'border-destructive': v$.description.$error && language.code === activeFormLocale }" />
                <span v-if="v$.description.$error && language.code === activeFormLocale" class="text-xs text-destructive">{{ v$.description.$errors[0]?.$message }}</span>
              </div>
              <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="admin-form-field">
                  <Label>SEO Title</Label>
                  <Input v-model="form.seo_title[language.code]" placeholder="SEO title" />
                </div>
                <div class="admin-form-field">
                  <Label>SEO Keywords</Label>
                  <Input v-model="form.seo_keywords[language.code]" placeholder="keyword, another keyword" />
                </div>
              </div>
              <div class="admin-form-field">
                <Label>SEO Description</Label>
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
              <Label>URL</Label>
              <Input v-model="form.url" placeholder="https://..." />
            </div>
            <div class="admin-form-field">
              <Label>Order</Label>
              <Input v-model.number="form.order" type="number" />
            </div>
          </div>
          <div>
            <Label>Technologies</Label>
            <div class="flex gap-2 mt-1">
              <Input v-model="techInput" placeholder="Add tech" @keydown.enter.prevent="addTech" />
              <Button @click="addTech" size="sm">Add</Button>
            </div>
            <div class="flex gap-1 flex-wrap mt-2">
              <Badge v-for="(tech, i) in form.technologies" :key="i" variant="secondary" class="cursor-pointer" @click="removeTech(i)">{{ tech }} ×</Badge>
            </div>
          </div>
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
      <template #title>Delete Project</template>
      <template #description>Are you sure you want to delete this project? This action cannot be undone.</template>
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
