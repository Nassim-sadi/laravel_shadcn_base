<script lang="ts" setup>
import { useVuelidate } from '@vuelidate/core'
import { email, helpers, minLength, required } from '@vuelidate/validators'
import { toast } from 'vue-sonner'

import { Button } from '@/components/ui/button'
import { FormControl, FormField, FormItem, FormLabel, FormMessage } from '@/components/ui/form'
import { Input } from '@/components/ui/input'
import { Select, SelectContent, SelectGroup, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select'
import { useCreateUserMutation, useUpdateUserMutation } from '@/services/api/users.api'

import type { User } from '../data/schema'

const props = defineProps<{
  user?: User
}>()

const emit = defineEmits<{
  (e: 'close'): void
  (e: 'success'): void
}>()

const roles = ['super_admin', 'admin', 'user', 'guest'] as const

const createUserMutation = useCreateUserMutation()
const updateUserMutation = useUpdateUserMutation(props.user?.id ?? 0)

const isEditing = computed(() => !!props.user?.id)

const formData = reactive({
  name: props.user?.name ?? '',
  email: props.user?.email ?? '',
  password: '',
  role: props.user?.role ?? 'user',
  is_active: props.user?.is_active ?? true,
})

const rules = computed(() => ({
  name: { required: helpers.withMessage('Name is required', required) },
  email: { required: helpers.withMessage('Email is required', required), email: helpers.withMessage('Invalid email', email) },
  password: {
    required: !isEditing.value ? helpers.withMessage('Password is required', required) : {},
    minLength: minLength(8),
  },
  role: { required },
}))

const v$ = useVuelidate(rules, formData)

async function onSubmit() {
  const isValid = await v$.value.$validate()
  if (!isValid)
    return

  try {
    if (isEditing.value && props.user?.id) {
      await updateUserMutation.mutateAsync({
        name: formData.name,
        email: formData.email,
        role: formData.role,
        is_active: formData.is_active,
        ...(formData.password ? { password: formData.password } : {}),
      })
      toast.success('User updated successfully')
    }
    else {
      await createUserMutation.mutateAsync({
        name: formData.name,
        email: formData.email,
        password: formData.password,
        role: formData.role,
        is_active: formData.is_active,
      })
      toast.success('User created successfully')
    }
    emit('success')
    emit('close')
  }
  catch (error: any) {
    toast.error(error.message ?? 'Failed to save user')
  }
}
</script>

<template>
  <div class="max-h-[500px] overflow-y-auto">
    <form class="space-y-6" @submit.prevent="onSubmit">
      <FormField v-slot="{ componentField }" name="name" :error="v$.name.$error">
        <FormItem>
          <FormLabel>{{ $t('admin.label.name') }}</FormLabel>
          <FormControl>
            <Input type="text" v-bind="componentField" :model-value="formData.name" @blur="v$.name.$touch" @update:model-value="formData.name = String($event)" />
          </FormControl>
          <FormMessage v-if="v$.name.$error">
            {{ v$.name.$errors[0]?.$message }}
          </FormMessage>
        </FormItem>
      </FormField>

      <FormField v-slot="{ componentField }" name="email" :error="v$.email.$error">
        <FormItem>
          <FormLabel>Email</FormLabel>
          <FormControl>
            <Input type="email" v-bind="componentField" :model-value="formData.email" @blur="v$.email.$touch" @update:model-value="formData.email = String($event)" />
          </FormControl>
          <FormMessage v-if="v$.email.$error">
            {{ v$.email.$errors[0]?.$message }}
          </FormMessage>
        </FormItem>
      </FormField>

      <FormField v-if="!isEditing" v-slot="{ componentField }" name="password" :error="v$.password.$error">
        <FormItem>
          <FormLabel>{{ $t('admin.label.newPassword') }}</FormLabel>
          <FormControl>
            <Input type="password" v-bind="componentField" :model-value="formData.password" @blur="v$.password.$touch" @update:model-value="formData.password = String($event)" />
          </FormControl>
          <FormMessage v-if="v$.password.$error">
            {{ v$.password.$errors[0]?.$message }}
          </FormMessage>
        </FormItem>
      </FormField>

      <FormField v-if="isEditing" v-slot="{ componentField }" name="password">
        <FormItem>
          <FormLabel>{{ $t('admin.label.newPassword') }} (optional)</FormLabel>
          <FormControl>
            <Input type="password" v-bind="componentField" :model-value="formData.password" @update:model-value="formData.password = String($event)" />
          </FormControl>
        </FormItem>
      </FormField>

      <FormField v-slot="{ componentField }" name="role" :error="v$.role.$error">
        <FormItem>
          <FormLabel>{{ $t('admin.label.roleName') }}</FormLabel>
          <FormControl>
            <Select v-bind="componentField" :model-value="formData.role" @update:model-value="formData.role = ($event as 'super_admin' | 'admin' | 'user' | 'guest')">
              <FormControl>
                <SelectTrigger class="w-full">
                  <SelectValue placeholder="Select a role" />
                </SelectTrigger>
              </FormControl>
              <SelectContent>
                <SelectGroup>
                  <SelectItem v-for="role in roles" :key="role" :value="role">
                    {{ role }}
                  </SelectItem>
                </SelectGroup>
              </SelectContent>
            </Select>
          </FormControl>
          <FormMessage v-if="v$.role.$error">
            {{ v$.role.$errors[0]?.$message }}
          </FormMessage>
        </FormItem>
      </FormField>

      <Button type="submit" class="w-full" :disabled="createUserMutation.isPending.value || updateUserMutation.isPending.value">
        {{ createUserMutation.isPending.value || updateUserMutation.isPending.value ? $t('admin.misc.saving') : (isEditing ? $t('admin.btn.update') : $t('admin.btn.create')) }} {{ $t('admin.page.users.title') }}
      </Button>
    </form>
  </div>
</template>
