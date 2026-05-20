<script setup lang="ts">
import { useVuelidate } from '@vuelidate/core'
import { computed, ref, watch } from 'vue'
import { SparklesIcon } from '@lucide/vue'

import AiContentGeneratorDialog from '@/admin/components/ai/AiContentGeneratorDialog.vue'
import { Button } from '@/components/ui/button'
import ConfirmDialog from '@/components/confirm-dialog.vue'
import { Dialog, DialogContent, DialogDescription, DialogFooter, DialogHeader, DialogTitle } from '@/components/ui/dialog'
import { Input } from '@/components/ui/input'
import { Label } from '@/components/ui/label'
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select'
import { Switch } from '@/components/ui/switch'
import { Tabs, TabsContent, TabsList, TabsTrigger } from '@/components/ui/tabs'
import { Textarea } from '@/components/ui/textarea'
import ImagePickerField from '@/admin/components/ImagePickerField.vue'
import TiptapEditor from '@/admin/components/TiptapEditor.vue'

import { useGetBlogCategoriesQuery } from '@/services/api/blog-categories.api'
import { useCreateBlogPostMutation, useUpdateBlogPostMutation } from '@/services/api/blog-posts.api'
import { useGetBlogTagsQuery, useCreateBlogTagMutation } from '@/services/api/blog-tags.api'
import type { TranslatedValue } from '@/composables/use-translated-form'
import { emptyTranslations, withLanguages } from '@/composables/use-translated-form'
import { translatedRequired } from '@/composables/use-validation'
import { languageMetadata } from '@/plugins/i18n'
import { hasPermission } from '@/composables/use-role'
import type { AiContentField } from '@/services/api/ai-content.api'

interface BlogPostForm {
  title: TranslatedValue
  slug: string
  excerpt: TranslatedValue
  body: TranslatedValue
  is_published: boolean
  featured: boolean
  category_id: number | null
  tag_ids: number[]
  image_id: number | null
  image_url: string | null
}

const props = defineProps<{
  editingId: number | null
  item: any
  open?: boolean
}>()

const open = defineModel<boolean>('open', { default: false })

const activeFormLocale = ref('fr')
const { mutate: createPost } = useCreateBlogPostMutation()
const { mutate: updatePost } = useUpdateBlogPostMutation()
const { data: categoriesResponse } = useGetBlogCategoriesQuery()
const { data: tagsResponse } = useGetBlogTagsQuery()
const categories = computed(() => categoriesResponse.value?.data?.data ?? [])
const tags = computed(() => tagsResponse.value ?? [])
const { mutate: createTag } = useCreateBlogTagMutation()
const newTagName = ref('')
const newTagSlug = ref('')
const showCancelConfirm = ref(false)
const aiGeneratorOpen = ref(false)

function createEmptyForm(): BlogPostForm {
  return {
    title: emptyTranslations(),
    slug: '',
    excerpt: emptyTranslations(),
    body: emptyTranslations(),
    is_published: true,
    featured: false,
    category_id: null,
    tag_ids: [],
    image_id: null,
    image_url: null,
  }
}

const form = ref<BlogPostForm>(createEmptyForm())

const rules = computed(() => ({
  title: { required: translatedRequired() },
}))

const v$ = useVuelidate(rules, form)

watch(() => props.open, (isOpen) => {
  if (isOpen) {
    if (props.editingId && props.item) {
      form.value = {
        title: withLanguages(props.item.title_translations, props.item.title),
        slug: props.item.slug || '',
        excerpt: withLanguages(props.item.excerpt_translations, props.item.excerpt),
        body: withLanguages(props.item.body_translations, props.item.body),
        is_published: props.item.is_published ?? true,
        featured: props.item.featured ?? false,
        category_id: props.item.category_id ?? null,
        tag_ids: props.item.tag_ids ?? [],
        image_id: props.item.image_id ?? null,
        image_url: props.item.image_thumbnail_url ?? props.item.image_url ?? null,
      }
    } else {
      form.value = createEmptyForm()
    }
    activeFormLocale.value = 'fr'
    v$.value.$reset()
  }
})

function requestClose() {
  showCancelConfirm.value = true
}

function confirmClose() {
  showCancelConfirm.value = false
  open.value = false
}

function handleDialogClose(newVal: boolean) {
  if (newVal === false)
    requestClose()
}

async function save() {
  const isValid = await v$.value.$validate()
  if (!isValid)
    return

  const payload = { ...form.value, image_id: form.value.image_id ?? undefined }
  if (props.editingId) {
    updatePost({ id: props.editingId, ...payload })
  } else {
    createPost(payload)
  }
  open.value = false
}

function addNewTag() {
  if (!newTagName.value || !newTagSlug.value)
    return
  createTag({ name: newTagName.value, slug: newTagSlug.value }, {
    onSuccess: (tag: any) => {
      form.value.tag_ids.push(tag.id)
      newTagName.value = ''
      newTagSlug.value = ''
    },
  })
}

function removeTag(tagId: number) {
  form.value.tag_ids = form.value.tag_ids.filter(id => id !== tagId)
}

function applyAiDraft(payload: Partial<Record<AiContentField, string>>) {
  const locale = activeFormLocale.value

  if (payload.title !== undefined) {
    form.value.title[locale] = payload.title
  }
  if (payload.excerpt !== undefined) {
    form.value.excerpt[locale] = payload.excerpt
  }
  if (payload.body !== undefined) {
    form.value.body[locale] = payload.body
  }
}

const aiSource = computed<Partial<Record<AiContentField, string>>>(() => {
  const locale = activeFormLocale.value

  return {
    title: form.value.title[locale] || '',
    excerpt: form.value.excerpt[locale] || '',
    body: form.value.body[locale] || '',
  }
})
</script>

<template>
  <Dialog :open="open" @update:open="handleDialogClose">
    <DialogContent class="!max-w-7xl max-h-[85vh] overflow-y-auto" @interact-outside.prevent>
      <DialogHeader>
        <div class="flex items-center justify-between gap-3">
          <DialogTitle>{{ editingId ? $t('admin.sheet.editBlogPost') : $t('admin.sheet.createBlogPost') }}</DialogTitle>
          <Button v-if="hasPermission('ai.generate')" type="button" variant="outline" size="sm" class="shrink-0" @click="aiGeneratorOpen = true">
            <SparklesIcon class="size-4" />
            <span>Generate</span>
          </Button>
        </div>
        <DialogDescription class="sr-only">{{ editingId ? $t('admin.sheet.editBlogPost') : $t('admin.sheet.createBlogPost') }}</DialogDescription>
      </DialogHeader>

      <div class="space-y-6 py-4">
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
              <Label>{{ $t('admin.label.title') }}</Label>
              <Input v-model="form.title[language.code]" :class="{ 'border-destructive': v$.title.$error && language.code === activeFormLocale }" />
              <span v-if="v$.title.$error && language.code === activeFormLocale" class="text-xs text-destructive">{{ v$.title.$errors[0]?.$message }}</span>
            </div>
            <div class="admin-form-field">
              <Label>{{ $t('admin.label.excerpt') }}</Label>
              <Textarea v-model="form.excerpt[language.code]" rows="3" />
            </div>
            <div class="admin-form-field">
              <Label>{{ $t('admin.label.body') }}</Label>
              <TiptapEditor v-model="form.body[language.code]" :placeholder="$t('admin.misc.bodyPlaceholder')" />
            </div>
          </TabsContent>
        </Tabs>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
          <div class="admin-form-field">
            <Label>{{ $t('admin.label.slug') }}</Label>
            <Input v-model="form.slug" :placeholder="$t('admin.misc.slugPlaceholder')" />
          </div>

          <div class="admin-form-field">
            <Label>{{ $t('admin.label.category') }}</Label>
            <Select v-model="form.category_id">
              <SelectTrigger>
                <SelectValue :placeholder="$t('admin.misc.selectCategory')" />
              </SelectTrigger>
              <SelectContent>
                <SelectItem :value="null as any">
                  {{ $t('admin.misc.none') }}
                </SelectItem>
                <SelectItem
                  v-for="cat in categories"
                  :key="cat.id"
                  :value="cat.id"
                >
                  {{ typeof cat.name === 'string' ? cat.name : cat.name?.fr || cat.name?.en || '' }}
                </SelectItem>
              </SelectContent>
            </Select>
          </div>
        </div>

        <div class="admin-form-field">
          <Label>{{ $t('admin.label.tags') }}</Label>
          <div class="flex flex-wrap gap-2 mb-2">
            <span
              v-for="tag in tags"
              :key="tag.id"
              :class="['inline-flex items-center gap-1 px-2 py-1 rounded-full text-xs cursor-pointer transition-colors', form.tag_ids.includes(tag.id) ? 'bg-primary text-primary-foreground' : 'bg-secondary text-secondary-foreground']"
              @click="form.tag_ids.includes(tag.id) ? removeTag(tag.id) : form.tag_ids.push(tag.id)"
            >
              {{ tag.name }}
            </span>
          </div>
          <div v-if="hasPermission('blogs.create')" class="flex gap-2">
            <Input v-model="newTagName" :placeholder="$t('admin.misc.newTagName')" class="flex-1" />
            <Input v-model="newTagSlug" :placeholder="$t('admin.misc.newTagSlug')" class="flex-1" />
            <Button variant="outline" size="sm" @click="addNewTag">
              {{ $t('admin.btn.add') }}
            </Button>
          </div>
        </div>

        <ImagePickerField
          v-model:image-id="form.image_id"
          v-model:image-url="form.image_url"
        />

        <div class="flex items-center gap-6">
          <div class="flex items-center gap-2">
            <Switch v-model:checked="form.is_published" />
            <Label>{{ $t('admin.label.published') }}</Label>
          </div>
          <div class="flex items-center gap-2">
            <Switch v-model:checked="form.featured" />
            <Label>{{ $t('admin.label.featured') }}</Label>
          </div>
        </div>
      </div>

      <DialogFooter>
        <Button variant="outline" @click="requestClose">
          {{ $t('admin.btn.cancel') }}
        </Button>
        <Button @click="save">
          {{ editingId ? $t('admin.btn.update') : $t('admin.btn.create') }}
        </Button>
      </DialogFooter>
    </DialogContent>
  </Dialog>

  <ConfirmDialog
    v-model:open="showCancelConfirm"
    cancel-button-text="Cancel"
    confirm-button-text="Discard"
    :destructive="true"
    @confirm="confirmClose"
  >
    <template #title>
      Discard changes?
    </template>
    <template #description>
      This action cannot be undone. All unsaved changes will be lost.
    </template>
  </ConfirmDialog>

  <AiContentGeneratorDialog
    v-model:open="aiGeneratorOpen"
    module="blog_posts"
    :locale="activeFormLocale"
    :source="aiSource"
    @apply="applyAiDraft"
  />
</template>
