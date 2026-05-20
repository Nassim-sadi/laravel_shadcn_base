<script setup lang="ts">
import { useVuelidate } from '@vuelidate/core'
import { numeric } from '@vuelidate/validators'
import { computed, ref, watch } from 'vue'
import { ArrowDownIcon, ArrowUpIcon, ImageIcon, VideoIcon, XIcon } from '@lucide/vue'
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
import MediaModal from '@/admin/components/MediaModal.vue'

import type { TranslatedValue } from '@/composables/use-translated-form'
import { emptyTranslations, withLanguages } from '@/composables/use-translated-form'
import { translatedRequired } from '@/composables/use-validation'
import { languageMetadata } from '@/plugins/i18n'
import {
  useCreateCatalogProductMutation,
  useGetAllCatalogCategoriesQuery,
  useGetAllCatalogBrandsQuery,
  useGetCatalogTagsQuery,
  useUpdateCatalogProductMutation,
} from '@/services/api/catalog.api'

interface ProductMediaItem {
  id?: number
  media_id?: number | null
  type: 'image' | 'video'
  video_url?: string
  thumbnail_path?: string
  thumbnail_url?: string
  image_url?: string
  order: number
}

interface ProductForm {
  name: TranslatedValue
  description: TranslatedValue
  body: TranslatedValue
  sku: string
  price_display: string
  badges: string[]
  category_id: number | null
  brand_id: number | null
  media: ProductMediaItem[]
  tag_ids: number[]
  is_active: boolean
  order: number
}

const props = defineProps<{
  editingId: number | null
  item: any
  open?: boolean
}>()

const open = defineModel<boolean>('open', { default: false })

const activeFormLocale = ref('fr')
const { data: categoriesData } = useGetAllCatalogCategoriesQuery()
const { data: brandsData } = useGetAllCatalogBrandsQuery()
const { data: tagsData } = useGetCatalogTagsQuery()
const { mutate: createProduct } = useCreateCatalogProductMutation()
const { mutate: updateProduct } = useUpdateCatalogProductMutation()
const showUnsavedDialog = ref(false)
const mediaModalOpen = ref(false)
const mediaModalTargetIndex = ref<number | null>(null)

const badgeOptions = ['new', 'sale', 'featured', 'popular', 'limited']

const categories = computed(() => {
  const d = categoriesData.value as any
  return d?.data ?? []
})

const brands = computed(() => {
  const d = brandsData.value as any
  return d?.data ?? []
})

const tags = computed(() => {
  const d = tagsData.value as any
  return d?.data ?? []
})

function createEmptyForm(): ProductForm {
  return {
    name: emptyTranslations(),
    description: emptyTranslations(),
    body: emptyTranslations(),
    sku: '',
    price_display: '',
    badges: [],
    category_id: null,
    brand_id: null,
    media: [],
    tag_ids: [],
    is_active: true,
    order: 0,
  }
}

const form = ref<ProductForm>(createEmptyForm())

const rules = computed(() => ({
  name: { required: translatedRequired() },
  order: { numeric },
  price_display: { numeric },
}))

const v$ = useVuelidate(rules, form)

watch(() => props.open, (isOpen) => {
  if (isOpen) {
    if (props.editingId && props.item) {
      form.value = {
        name: withLanguages(props.item.name_translations, props.item.name),
        description: withLanguages(props.item.description_translations, props.item.description),
        body: withLanguages(props.item.body_translations, props.item.body),
        sku: props.item.sku || '',
        price_display: props.item.price_display ? String(props.item.price_display) : '',
        badges: props.item.badges || [],
        category_id: props.item.category_id ?? null,
        brand_id: props.item.brand_id ?? null,
        media: (props.item.media || []).map((m: any) => ({
          id: m.id,
          media_id: m.media_id ?? null,
          type: m.type || 'image',
          video_url: m.video_url || '',
          thumbnail_url: m.thumbnail_url || null,
          image_url: m.image_url || m.image_thumbnail_url || null,
          order: m.order,
        })),
        tag_ids: (props.item.tags || []).map((t: any) => t.id),
        is_active: props.item.is_active,
        order: props.item.order,
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

  const mediaPayload = form.value.media.map((m, i) => ({
    media_id: m.type === 'image' ? m.media_id : undefined,
    type: m.type,
    video_url: m.type === 'video' ? m.video_url : undefined,
    thumbnail_path: m.type === 'video' ? m.thumbnail_path : undefined,
    order: i,
  }))

  const payload = {
    ...form.value,
    price_display: form.value.price_display ? parseFloat(form.value.price_display) : undefined,
    category_id: form.value.category_id ?? undefined,
    media: mediaPayload,
    tag_ids: form.value.tag_ids,
  }

  if (props.editingId) {
    updateProduct({ id: props.editingId, ...payload }, {
      onSuccess: () => toast.success('Product updated successfully'),
      onError: (error: any) => toast.error(error?.message ?? 'Failed to update product'),
    })
  } else {
    createProduct(payload, {
      onSuccess: () => toast.success('Product created successfully'),
      onError: (error: any) => toast.error(error?.message ?? 'Failed to create product'),
    })
  }
  open.value = false
}

function forceClose() {
  showUnsavedDialog.value = false
  open.value = false
}

function addImageSlide() {
  form.value.media.push({ type: 'image', media_id: undefined, image_url: undefined, order: form.value.media.length })
}

function addVideoSlide() {
  form.value.media.push({ type: 'video', video_url: '', thumbnail_url: undefined, order: form.value.media.length })
}

function removeSlide(index: number) {
  form.value.media.splice(index, 1)
}

function moveSlide(index: number, direction: 'up' | 'down') {
  const newIndex = direction === 'up' ? index - 1 : index + 1
  if (newIndex < 0 || newIndex >= form.value.media.length) return
  const temp = form.value.media[index]
  form.value.media[index] = form.value.media[newIndex]
  form.value.media[newIndex] = temp
}

function toggleBadge(badge: string) {
  const idx = form.value.badges.indexOf(badge)
  if (idx === -1) {
    form.value.badges.push(badge)
  } else {
    form.value.badges.splice(idx, 1)
  }
}

function toggleTag(tagId: number) {
  const idx = form.value.tag_ids.indexOf(tagId)
  if (idx === -1) {
    form.value.tag_ids.push(tagId)
  } else {
    form.value.tag_ids.splice(idx, 1)
  }
}

function openMediaPicker(index: number) {
  mediaModalTargetIndex.value = index
  mediaModalOpen.value = true
}

function handleMediaSelect(data: { id: number, url: string, thumbnail_url?: string }) {
  if (mediaModalTargetIndex.value !== null) {
    form.value.media[mediaModalTargetIndex.value].media_id = data.id
    form.value.media[mediaModalTargetIndex.value].image_url = data.thumbnail_url ?? data.url
  }
  mediaModalOpen.value = false
  mediaModalTargetIndex.value = null
}

const badgeColorMap: Record<string, string> = {
  new: 'bg-blue-500',
  sale: 'bg-red-500',
  featured: 'bg-amber-500',
  popular: 'bg-green-500',
  limited: 'bg-purple-500',
}
</script>

<template>
  <Sheet :open="open" @update:open="handleSheetClose">
    <SheetContent side="right" class="xl:max-w-2xl w-full" @interact-outside.prevent>
      <SheetHeader>
        <SheetTitle>{{ editingId ? $t('admin.catalog.editProduct') : $t('admin.catalog.createProduct') }}</SheetTitle>
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
              <Textarea v-model="form.description[language.code]" :placeholder="$t('admin.catalog.descriptionPlaceholder')" :class="{ 'border-destructive': v$.description?.$error && language.code === activeFormLocale }" />
            </div>
            <div class="admin-form-field">
              <Label>{{ $t('admin.catalog.body') }}</Label>
              <Textarea v-model="form.body[language.code]" :placeholder="$t('admin.catalog.bodyPlaceholder')" rows="5" />
            </div>
          </TabsContent>
        </Tabs>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
          <div class="admin-form-field">
            <Label>{{ $t('admin.catalog.sku') }}</Label>
            <Input v-model="form.sku" :placeholder="$t('admin.catalog.skuPlaceholder')" />
          </div>
          <div class="admin-form-field">
            <Label>{{ $t('admin.catalog.price') }}</Label>
            <Input v-model="form.price_display" type="number" step="0.01" :placeholder="$t('admin.catalog.pricePlaceholder')" :class="{ 'border-destructive': v$.price_display?.$error }" />
          </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
          <div class="admin-form-field">
            <Label>{{ $t('admin.catalog.category') }}</Label>
            <Select v-model="form.category_id">
              <SelectTrigger>
                <SelectValue :placeholder="$t('admin.catalog.selectCategory')" />
              </SelectTrigger>
              <SelectContent>
                <SelectItem :value="null">{{ $t('admin.catalog.noCategory') }}</SelectItem>
                <SelectItem v-for="cat in categories" :key="cat.id" :value="cat.id">{{ cat.name }}</SelectItem>
              </SelectContent>
            </Select>
          </div>
          <div class="admin-form-field">
            <Label>{{ $t('admin.catalog.brand') }}</Label>
            <Select v-model="form.brand_id">
              <SelectTrigger>
                <SelectValue :placeholder="$t('admin.catalog.selectBrand')" />
              </SelectTrigger>
              <SelectContent>
                <SelectItem :value="null">{{ $t('admin.catalog.noBrand') }}</SelectItem>
                <SelectItem v-for="brand in brands" :key="brand.id" :value="brand.id">{{ brand.name }}</SelectItem>
              </SelectContent>
            </Select>
          </div>
        </div>

        <div class="admin-form-field">
          <Label>{{ $t('admin.catalog.badges') }}</Label>
          <div class="flex flex-wrap gap-2 mt-1">
            <button v-for="badge in badgeOptions" :key="badge" type="button" class="px-3 py-1 rounded-full text-xs font-medium border transition-colors" :class="form.badges.includes(badge) ? `${badgeColorMap[badge]} text-white border-transparent` : 'border-border text-muted-foreground'" @click="toggleBadge(badge)">
              {{ $t(`admin.catalog.badge.${badge}`) }}
            </button>
          </div>
        </div>

        <div class="admin-form-field">
          <Label>{{ $t('admin.catalog.tags') }}</Label>
          <div class="flex flex-wrap gap-2 mt-1">
            <button v-for="tag in tags" :key="tag.id" type="button" class="px-3 py-1 rounded-full text-xs font-medium border transition-colors" :class="form.tag_ids.includes(tag.id) ? 'bg-primary text-primary-foreground border-transparent' : 'border-border text-muted-foreground'" @click="toggleTag(tag.id)">
              {{ tag.name }}
            </button>
          </div>
        </div>

        <div class="admin-form-field">
          <Label>{{ $t('admin.catalog.carousel') }}</Label>
          <div class="space-y-2 mt-1">
            <div v-for="(slide, index) in form.media" :key="index" class="flex items-center gap-2 p-2 border rounded-lg">
              <div class="flex flex-col gap-0.5">
                <Button type="button" variant="ghost" size="icon" class="size-5 p-0" :disabled="index === 0" @click="moveSlide(index, 'up')">
                  <ArrowUpIcon class="size-3" />
                </Button>
                <Button type="button" variant="ghost" size="icon" class="size-5 p-0" :disabled="index === form.media.length - 1" @click="moveSlide(index, 'down')">
                  <ArrowDownIcon class="size-3" />
                </Button>
              </div>
              <ImageIcon v-if="slide.type === 'image'" class="size-4 text-muted-foreground" />
              <VideoIcon v-else class="size-4 text-muted-foreground" />
              <div class="flex-1 min-w-0">
                <template v-if="slide.type === 'image'">
                  <div class="flex items-center gap-2">
                    <div v-if="slide.image_url" class="relative h-10 w-10 shrink-0 overflow-hidden rounded border">
                      <img :src="slide.image_url" alt="" class="h-full w-full object-cover">
                    </div>
                    <div v-else class="flex h-10 w-10 shrink-0 items-center justify-center rounded border bg-muted">
                      <ImageIcon class="size-4 text-muted-foreground" />
                    </div>
                    <Button type="button" variant="outline" size="sm" class="h-7 text-xs flex-1" @click="openMediaPicker(index)">
                      {{ slide.media_id ? 'Change' : 'Choose' }}
                    </Button>
                    <Button v-if="slide.media_id" type="button" variant="ghost" size="icon" class="size-7 text-destructive" @click="slide.media_id = undefined; slide.image_url = undefined">
                      <XIcon class="size-3" />
                    </Button>
                  </div>
                </template>
                <template v-else>
                  <Input v-model="slide.video_url" :placeholder="$t('admin.catalog.videoUrlPlaceholder')" class="h-7 text-xs" />
                  <div class="mt-1 flex items-center gap-2">
                    <div v-if="slide.thumbnail_url" class="relative h-8 w-8 shrink-0 overflow-hidden rounded border">
                      <img :src="slide.thumbnail_url" alt="" class="h-full w-full object-cover">
                    </div>
                    <div v-else class="flex h-8 w-8 shrink-0 items-center justify-center rounded border bg-muted">
                      <ImageIcon class="size-3 text-muted-foreground" />
                    </div>
                    <Button type="button" variant="outline" size="sm" class="h-6 text-xs flex-1" @click="openMediaPicker(index)">
                      {{ slide.thumbnail_url ? 'Change' : 'Thumbnail' }}
                    </Button>
                  </div>
                </template>
              </div>
              <Button type="button" variant="ghost" size="icon" class="size-7" @click="removeSlide(index)">
                <XIcon class="size-3" />
              </Button>
            </div>
            <div class="flex gap-2">
              <Button type="button" variant="outline" size="sm" class="flex-1" @click="addImageSlide">
                <ImageIcon class="size-3 mr-1" /> {{ $t('admin.catalog.addImage') }}
              </Button>
              <Button type="button" variant="outline" size="sm" class="flex-1" @click="addVideoSlide">
                <VideoIcon class="size-3 mr-1" /> {{ $t('admin.catalog.addVideo') }}
              </Button>
            </div>
          </div>
        </div>

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

  <MediaModal :open="mediaModalOpen" select-mode @close="mediaModalOpen = false" @select="handleMediaSelect" />
</template>
