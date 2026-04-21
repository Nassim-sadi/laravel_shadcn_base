<script setup lang="ts">
import { ref, computed, watch } from 'vue'
import { CheckIcon, ChevronsUpDownIcon, Loader2 } from '@lucide/vue'
import { toast } from 'vue-sonner'

import { Button } from '@/components/ui/button'
import { Command, CommandEmpty, CommandGroup, CommandInput, CommandItem, CommandList } from '@/components/ui/command'
import { Form, FormControl, FormField, FormItem, FormLabel, FormMessage } from '@/components/ui/form'
import { Input } from '@/components/ui/input'
import { Popover, PopoverContent, PopoverTrigger } from '@/components/ui/popover'
import { Progress } from '@/components/ui/progress'
import { Separator } from '@/components/ui/separator'
import { useUserQuery, useUpdateProfileMutation, useChangePasswordMutation, useUploadAvatarMutation } from '@/services/api/auth.api'
import { cn } from '@/lib/utils'
import { compressImage, formatFileSize } from '@/lib/image-utils'

const { data: userResponse } = useUserQuery()
const updateProfileMutation = useUpdateProfileMutation()
const changePasswordMutation = useChangePasswordMutation()
const uploadAvatarMutation = useUploadAvatarMutation()

const user = computed(() => (userResponse.value as any)?.data)

const languageOpen = ref(false)
const passwordFormVisible = ref(false)

const languages = [
  { label: 'English', value: 'en' },
  { label: 'French', value: 'fr' },
  { label: 'Arabic', value: 'ar' },
] as const

const formData = ref({
  name: user.value?.name ?? '',
  language: user.value?.locale ?? 'en',
})

const passwordData = ref({
  current_password: '',
  password: '',
  password_confirmation: '',
})

watch(user, (newUser) => {
  if (newUser) {
    formData.value.name = newUser.name ?? ''
    formData.value.language = newUser.locale ?? 'en'
  }
}, { immediate: true })

async function onSubmit() {
  try {
    await updateProfileMutation.mutateAsync({
      name: formData.value.name,
      locale: formData.value.language,
    })
    toast.success('Profile updated successfully')
  }
  catch (error: any) {
    toast.error(error.message ?? 'Failed to update profile')
  }
}

async function onPasswordSubmit() {
  try {
    await changePasswordMutation.mutateAsync({
      current_password: passwordData.value.current_password,
      password: passwordData.value.password,
      password_confirmation: passwordData.value.password_confirmation,
    })
    toast.success('Password changed successfully')
    passwordData.value = { current_password: '', password: '', password_confirmation: '' }
    passwordFormVisible.value = false
  }
  catch (error: any) {
    toast.error(error.message ?? 'Failed to change password')
  }
}

const compressProgress = ref(0)
const uploadProgress = ref(0)
const isCompressing = ref(false)
const isUploading = ref(false)

async function onAvatarChange(event: Event) {
  const file = (event.target as HTMLInputElement).files?.[0]
  if (!file) return

  compressProgress.value = 0
  uploadProgress.value = 0
  isCompressing.value = true
  isUploading.value = false

  try {
    compressProgress.value = 10
    const compressed = await compressImage(file, { maxWidth: 400, maxHeight: 400, quality: 0.8 })
    compressProgress.value = 100

    isCompressing.value = false
    isUploading.value = true

    const compressedFile = new File([compressed.blob], file.name, { type: 'image/jpeg' })
    console.log(`Compressed from ${formatFileSize(file.size)} to ${formatFileSize(compressedFile.size)}`)

    await uploadAvatarMutation.mutateAsync(compressedFile)
    toast.success('Avatar uploaded successfully')

    uploadProgress.value = 100
  }
  catch (error: any) {
    toast.error(error.message ?? 'Failed to upload avatar')
  }
  finally {
    isCompressing.value = false
    isUploading.value = false
  }
}
</script>

<template>
  <div class="max-w-2xl">
    <div class="mb-6">
      <h3 class="text-lg font-medium">Account</h3>
      <p class="text-sm text-muted-foreground">Manage your account settings.</p>
    </div>
    <Separator class="mb-6" />

    <!-- Avatar -->
    <div class="flex items-center gap-4 mb-6">
      <img
        :src="user?.avatar ? `/storage/${user.avatar}` : '/placeholder-avatar.png'"
        alt="Avatar"
        class="w-20 h-20 rounded-full object-cover bg-muted"
      />
      <div class="flex-1">
        <div class="flex items-center gap-2">
          <Input type="file" accept="image/*" class="w-auto" :disabled="isCompressing || isUploading" @change="onAvatarChange" />
          <Loader2 v-if="isCompressing || isUploading" class="size-4 animate-spin" />
        </div>
        <p class="text-sm text-muted-foreground mt-1">JPG, PNG or GIF. Max 2MB.</p>

        <div v-if="isCompressing" class="mt-3 space-y-2">
          <div class="flex items-center justify-between text-sm">
            <span class="text-muted-foreground">Compressing...</span>
            <span class="text-muted-foreground">{{ compressProgress }}%</span>
          </div>
          <Progress :model-value="compressProgress" />
        </div>

        <div v-if="isUploading" class="mt-3 space-y-2">
          <div class="flex items-center justify-between text-sm">
            <span class="text-muted-foreground">Uploading...</span>
            <span class="text-muted-foreground">{{ uploadProgress }}%</span>
          </div>
          <Progress :model-value="uploadProgress" />
        </div>
      </div>
    </div>

    <!-- Profile Form -->
    <Form class="space-y-6" @submit="onSubmit">
      <FormField v-slot="{ componentField }" name="name">
        <FormItem>
          <FormLabel>Name</FormLabel>
          <FormControl>
            <Input type="text" placeholder="Your name" v-bind="componentField" v-model="formData.name" />
          </FormControl>
          <FormMessage />
        </FormItem>
      </FormField>

      <FormField v-slot="{ value }" name="language">
        <FormItem class="flex flex-col">
          <FormLabel>Language</FormLabel>
          <Popover v-model:open="languageOpen">
            <PopoverTrigger as-child>
              <FormControl>
                <Button
                  variant="outline" role="combobox" :aria-expanded="languageOpen" :class="cn('w-[200px] justify-between', !value && 'text-muted-foreground')"
                >
                  {{ value ? languages.find(l => l.value === value)?.label : 'Select language...' }}
                  <ChevronsUpDownIcon class="size-4 ml-2 opacity-50 shrink-0" />
                </Button>
              </FormControl>
            </PopoverTrigger>
            <PopoverContent class="w-[200px] p-0">
              <Command>
                <CommandInput placeholder="Search language..." />
                <CommandEmpty>No language found.</CommandEmpty>
                <CommandList>
                  <CommandGroup>
                    <CommandItem
                      v-for="language in languages" :key="language.value" :value="language.label"
                      @select="() => { formData.language = language.value; languageOpen = false }"
                    >
                      <CheckIcon :class="cn('mr-2 h-4 w-4', value === language.value ? 'opacity-100' : 'opacity-0')" />
                      {{ language.label }}
                    </CommandItem>
                  </CommandGroup>
                </CommandList>
              </Command>
            </PopoverContent>
          </Popover>
          <FormMessage />
        </FormItem>
      </FormField>

      <Button type="submit">
        Save changes
      </Button>
    </Form>

    <Separator class="my-6" />

    <!-- Change Password -->
    <div class="space-y-4">
      <Button variant="outline" @click="passwordFormVisible = !passwordFormVisible">
        {{ passwordFormVisible ? 'Cancel' : 'Change password' }}
      </Button>

      <div v-if="passwordFormVisible" class="space-y-4">
        <h4 class="text-md font-medium">Change Password</h4>
        <Form class="space-y-4" @submit="onPasswordSubmit">
          <FormField v-slot="{ componentField }" name="current_password">
            <FormItem>
              <FormLabel>Current Password</FormLabel>
              <FormControl>
                <Input type="password" v-bind="componentField" v-model="passwordData.current_password" />
              </FormControl>
              <FormMessage />
            </FormItem>
          </FormField>

          <FormField v-slot="{ componentField }" name="password">
            <FormItem>
              <FormLabel>New Password</FormLabel>
              <FormControl>
                <Input type="password" v-bind="componentField" v-model="passwordData.password" />
              </FormControl>
              <FormMessage />
            </FormItem>
          </FormField>

          <FormField v-slot="{ componentField }" name="password_confirmation">
            <FormItem>
              <FormLabel>Confirm New Password</FormLabel>
              <FormControl>
                <Input type="password" v-bind="componentField" v-model="passwordData.password_confirmation" />
              </FormControl>
              <FormMessage />
            </FormItem>
          </FormField>

          <Button type="submit">
            Change password
          </Button>
        </Form>
      </div>
    </div>
  </div>
</template>