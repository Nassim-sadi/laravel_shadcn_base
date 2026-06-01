<script setup lang="ts">
import { useVuelidate } from '@vuelidate/core'
import { computed, ref, watch } from 'vue'
import { toast } from 'vue-sonner'

import { Button } from '@/components/ui/button'
import { Input } from '@/components/ui/input'
import { Label } from '@/components/ui/label'
import { Sheet, SheetContent, SheetFooter, SheetHeader, SheetTitle } from '@/components/ui/sheet'
import { Switch } from '@/components/ui/switch'
import { Tabs, TabsContent, TabsList, TabsTrigger } from '@/components/ui/tabs'
import { Textarea } from '@/components/ui/textarea'
import ConfirmDialog from '@/components/confirm-dialog.vue'

import type { TranslatedValue } from '@/composables/use-translated-form'
import { emptyTranslations, withLanguages } from '@/composables/use-translated-form'
import { translatedRequired } from '@/composables/use-validation'
import { languageMetadata } from '@/plugins/i18n'
import {
  useCreateBookingServiceMutation,
  useUpdateBookingServiceMutation,
} from '@/services/api/booking.api'

interface BookingServiceForm {
  name: TranslatedValue
  description: TranslatedValue
  duration_minutes: number
  price: string
  is_active: boolean
  order: number
}

const props = defineProps<{
  editingId: number | null
  item: any
}>()

const open = defineModel<boolean>('open', { default: false })

const activeFormLocale = ref('fr')
const { mutate: createService } = useCreateBookingServiceMutation()
const { mutate: updateService } = useUpdateBookingServiceMutation()
const showUnsavedDialog = ref(false)

function createEmptyForm(): BookingServiceForm {
  return {
    name: emptyTranslations(),
    description: emptyTranslations(),
    duration_minutes: 60,
    price: '',
    is_active: true,
    order: 0,
  }
}

const form = ref<BookingServiceForm>(createEmptyForm())

const rules = computed(() => ({
  name: { required: translatedRequired() },
  duration_minutes: { required: (v: number) => v >= 15 },
}))

const v$ = useVuelidate(rules, form)

watch(open, (isOpen) => {
  if (isOpen) {
    if (props.editingId && props.item) {
      form.value = {
        name: withLanguages(props.item.name_translations, ''),
        description: withLanguages(props.item.description_translations, ''),
        duration_minutes: props.item.duration_minutes,
        price: props.item.price ? String(props.item.price) : '',
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

  const payload = {
    ...form.value,
    price: form.value.price ? parseFloat(form.value.price) : undefined,
  }

  if (props.editingId) {
    updateService({ id: props.editingId, ...payload }, {
      onSuccess: () => toast.success('Service updated'),
      onError: (error: any) => toast.error(error?.message ?? 'Failed to update'),
    })
  } else {
    createService(payload, {
      onSuccess: () => toast.success('Service created'),
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
    <SheetContent side="right" class="xl:max-w-2xl w-full" @interact-outside="(e: Event) => e.preventDefault()">
      <SheetHeader>
        <SheetTitle>{{ editingId ? 'Edit Booking Service' : 'Create Booking Service' }}</SheetTitle>
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
              <Label>Service Name</Label>
              <Input v-model="form.name[language.code]" placeholder="e.g. Haircut" :class="{ 'border-destructive': v$.name?.$error && language.code === activeFormLocale }" />
              <span v-if="v$.name?.$error && language.code === activeFormLocale" class="text-xs text-destructive">{{ v$.name?.$errors?.[0]?.$message }}</span>
            </div>
            <div class="admin-form-field">
              <Label>Description</Label>
              <Textarea v-model="form.description[language.code]" rows="3" />
            </div>
          </TabsContent>
        </Tabs>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
          <div class="admin-form-field">
            <Label>Duration (minutes)</Label>
            <Input v-model.number="form.duration_minutes" type="number" :class="{ 'border-destructive': v$.duration_minutes?.$error }" />
          </div>
          <div class="admin-form-field">
            <Label>Price</Label>
            <Input v-model="form.price" type="number" step="0.01" placeholder="0.00" />
          </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
          <div class="admin-form-field">
            <Label>Order</Label>
            <Input v-model.number="form.order" type="number" />
          </div>
          <div class="flex items-center gap-2 pt-2">
            <Switch v-model:checked="form.is_active" />
            <Label>Active</Label>
          </div>
        </div>
      </div>
      <SheetFooter>
        <Button variant="outline" @click="handleSheetClose(false)">Cancel</Button>
        <Button @click="save">{{ editingId ? 'Update' : 'Create' }}</Button>
      </SheetFooter>
    </SheetContent>
  </Sheet>

  <ConfirmDialog v-model:open="showUnsavedDialog" cancel-button-text="Stay" confirm-button-text="Discard" destructive @confirm="forceClose">
    <template #title>Unsaved changes</template>
    <template #description>You have unsaved changes. Are you sure you want to discard them?</template>
  </ConfirmDialog>
</template>
