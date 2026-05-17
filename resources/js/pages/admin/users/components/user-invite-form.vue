<script setup lang="ts">
import { Send as SendIcon } from '@lucide/vue'
import { useVuelidate } from '@vuelidate/core'
import { email, helpers, required } from '@vuelidate/validators'
import { toast } from 'vue-sonner'

import Button from '@/components/ui/button/Button.vue'
import { FormControl, FormField, FormItem, FormLabel, FormMessage } from '@/components/ui/form'
import { Input } from '@/components/ui/input'
import { Select, SelectContent, SelectGroup, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select'
import { useInviteUserMutation } from '@/services/api/users.api'

import type { User } from '../data/schema'

const props = defineProps<{
  user?: User
}>()

const emit = defineEmits<{
  (e: 'success'): void
  (e: 'close'): void
}>()

const roles = ['super_admin', 'admin', 'user', 'guest'] as const

const formData = reactive({
  email: props.user?.email ?? '',
  name: props.user?.name ?? '',
  role: props.user?.role ?? 'user',
})

const rules = {
  email: { required: helpers.withMessage('Email is required', required), email: helpers.withMessage('Invalid email', email) },
  name: { required: helpers.withMessage('Name is required', required) },
  role: { required },
}

const v$ = useVuelidate(rules, formData)
const inviteUserMutation = useInviteUserMutation()

async function onSubmit() {
  const isValid = await v$.value.$validate()
  if (!isValid)
    return

  try {
    const result = await inviteUserMutation.mutateAsync({
      email: formData.email,
      name: formData.name,
      role: formData.role,
    })
    toast.success(`User invited! Temporary password: ${result.data?.temporary_password}`)
    emit('success')
    emit('close')
  }
  catch (error: any) {
    toast.error(error.message ?? 'Failed to invite user')
  }
}
</script>

<template>
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
        <FormLabel>Email address</FormLabel>
        <FormControl>
          <Input type="email" v-bind="componentField" :model-value="formData.email" @blur="v$.email.$touch" @update:model-value="formData.email = String($event)" />
        </FormControl>
        <FormMessage v-if="v$.email.$error">
          {{ v$.email.$errors[0]?.$message }}
        </FormMessage>
      </FormItem>
    </FormField>

    <FormField v-slot="{ componentField }" name="role">
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
      </FormItem>
    </FormField>

    <Button type="submit" class="w-full" :disabled="inviteUserMutation.isPending.value">
      {{ inviteUserMutation.isPending.value ? 'Inviting...' : 'Invite' }}
      <SendIcon class="ml-2 size-4" />
    </Button>
  </form>
</template>
