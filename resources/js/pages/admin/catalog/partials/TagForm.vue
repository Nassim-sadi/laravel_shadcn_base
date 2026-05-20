<script setup lang="ts">
import { useVuelidate } from '@vuelidate/core'
import { computed, ref, watch } from 'vue'
import { toast } from 'vue-sonner'

import { Button } from '@/components/ui/button'
import { Input } from '@/components/ui/input'
import { Label } from '@/components/ui/label'
import { Sheet, SheetContent, SheetFooter, SheetHeader, SheetTitle } from '@/components/ui/sheet'
import { Tabs, TabsContent, TabsList, TabsTrigger } from '@/components/ui/tabs'
import ConfirmDialog from '@/components/confirm-dialog.vue'

import type { TranslatedValue } from '@/composables/use-translated-form'
import { emptyTranslations, withLanguages } from '@/composables/use-translated-form'
import { translatedRequired } from '@/composables/use-validation'
import { languageMetadata } from '@/plugins/i18n'
import {
  useCreateCatalogTagMutation,
} from '@/services/api/catalog.api'

interface TagForm {
  name: TranslatedValue
}

const props = defineProps<{
  editingId: number | null
  item: any
  open?: boolean
}>()

const open = defineModel<boolean>('open', { default: false })

const activeFormLocale = ref('fr')
const { mutate: createTag } = useCreateCatalogTagMutation()
const showUnsavedDialog = ref(false)

function createEmptyForm(): TagForm {
  return {
    name: emptyTranslations(),
  }
}

const form = ref<TagForm>(createEmptyForm())

const rules = computed(() => ({
  name: { required: translatedRequired() },
}))

const v$ = useVuelidate(rules, form)

watch(() => props.open, (isOpen) => {
  if (isOpen) {
    if (props.editingId && props.item) {
      form.value = {
        name: withLanguages(props.item.name_translations, ''),
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

  createTag(form.value, {
    onSuccess: () => toast.success('Tag created'),
    onError: (error: any) => toast.error(error?.message ?? 'Failed to create'),
  })
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
        <SheetTitle>{{ editingId ? $t('admin.catalog.editTag') : $t('admin.catalog.createTag') }}</SheetTitle>
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
          </TabsContent>
        </Tabs>
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
