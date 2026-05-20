<script setup lang="ts">
import { useVuelidate } from '@vuelidate/core'
import { computed, ref, watch } from 'vue'
import { toast } from 'vue-sonner'

import { Button } from '@/components/ui/button'
import { Input } from '@/components/ui/input'
import { Label } from '@/components/ui/label'
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select'
import { Sheet, SheetContent, SheetFooter, SheetHeader, SheetTitle } from '@/components/ui/sheet'
import { Switch } from '@/components/ui/switch'
import { Tabs, TabsContent, TabsList, TabsTrigger } from '@/components/ui/tabs'
import ConfirmDialog from '@/components/confirm-dialog.vue'
import ImagePickerField from '@/admin/components/ImagePickerField.vue'

import type { TranslatedValue } from '@/composables/use-translated-form'
import { emptyTranslations, withLanguages } from '@/composables/use-translated-form'
import { translatedRequired } from '@/composables/use-validation'
import { languageMetadata } from '@/plugins/i18n'
import {
  useCreateCatalogMarqueeItemMutation,
  useUpdateCatalogMarqueeItemMutation,
} from '@/services/api/catalog.api'

interface MarqueeForm {
  text: TranslatedValue
  image_id: number | null
  image_url: string | null
  position: number
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
const { mutate: createItem } = useCreateCatalogMarqueeItemMutation()
const { mutate: updateItem } = useUpdateCatalogMarqueeItemMutation()
const showUnsavedDialog = ref(false)

function createEmptyForm(): MarqueeForm {
  return {
    text: emptyTranslations(),
    image_id: null,
    image_url: null,
    position: props.item?.position ?? 1,
    order: 0,
    is_active: true,
  }
}

const form = ref<MarqueeForm>(createEmptyForm())

const rules = computed(() => ({
  text: { required: translatedRequired() },
}))

const v$ = useVuelidate(rules, form)

watch(() => props.open, (isOpen) => {
  if (isOpen) {
    if (props.editingId && props.item) {
      form.value = {
        text: withLanguages(props.item.text, ''),
        image_id: props.item.image_id ?? null,
        image_url: props.item.image_url ?? null,
        position: props.item.position,
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
  }

  if (props.editingId) {
    updateItem({ id: props.editingId, ...payload }, {
      onSuccess: () => toast.success('Marquee item updated'),
      onError: (error: any) => toast.error(error?.message ?? 'Failed to update'),
    })
  } else {
    createItem(payload, {
      onSuccess: () => toast.success('Marquee item created'),
      onError: (error: any) => toast.error(error?.message ?? 'Failed to create'),
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
        <SheetTitle>{{ editingId ? $t('admin.catalog.editMarqueeItem') : $t('admin.catalog.createMarqueeItem') }}</SheetTitle>
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
              <Label>{{ $t('admin.catalog.marqueeText') }}</Label>
              <Input v-model="form.text[language.code]" :placeholder="$t('admin.catalog.marqueeTextPlaceholder')" :class="{ 'border-destructive': v$.text?.$error && language.code === activeFormLocale }" />
              <span v-if="v$.text?.$error && language.code === activeFormLocale" class="text-xs text-destructive">{{ v$.text?.$errors?.[0]?.$message }}</span>
            </div>
          </TabsContent>
        </Tabs>

        <ImagePickerField v-model:image-id="form.image_id" v-model:image-url="form.image_url" />

        <div class="admin-form-field">
          <Label>{{ $t('admin.catalog.position') }}</Label>
          <Select v-model="form.position">
            <SelectTrigger>
              <SelectValue />
            </SelectTrigger>
            <SelectContent>
              <SelectItem v-for="p in 5" :key="p" :value="p">{{ $t(`admin.catalog.position${p}`) }}</SelectItem>
            </SelectContent>
          </Select>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
          <div class="admin-form-field">
            <Label>{{ $t('admin.label.order') }}</Label>
            <Input v-model.number="form.order" type="number" />
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
