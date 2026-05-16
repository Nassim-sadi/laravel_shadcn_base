<script lang="ts" setup>
import { toast } from 'vue-sonner'

import { useAuth } from '@/composables/use-auth'

import GitHubButton from './github-button.vue'
import GoogleButton from './google-button.vue'
import PrivacyPolicyButton from './privacy-policy-button.vue'
import TermsOfServiceButton from './terms-of-service-button.vue'
import ToForgotPasswordLink from './to-forgot-password-link.vue'

const { login, loading } = useAuth()

const email = ref('')
const password = ref('')
const generalError = ref('')
const fieldErrors = ref<Record<string, string[]>>({})

function readErrorMessage(error: any) {
  fieldErrors.value = error?.data?.errors ?? {}

  const firstFieldError = Object.values(fieldErrors.value)
    .flat()
    .find(Boolean)

  return firstFieldError || error?.data?.message || error?.message || 'Login failed'
}

async function handleLogin() {
  generalError.value = ''
  fieldErrors.value = {}

  try {
    await login({ email: email.value, password: password.value })
    toast.success('Welcome back!')
  }
  catch (error: any) {
    generalError.value = readErrorMessage(error)
    toast.error(generalError.value)
  }
}
</script>

<template>
  <UiCard class="w-full max-w-sm">
    <UiCardHeader>
      <UiCardTitle class="text-2xl">
        Login
      </UiCardTitle>
      <UiCardDescription>
        Enter your email and password below to log into your account.
        Not have an account?
        <UiButton
          variant="link" class="px-0 text-muted-foreground"
          @click="$router.push('/auth/sign-up')"
        >
          Sign Up
        </UiButton>
      </UiCardDescription>
    </UiCardHeader>
    <UiCardContent class="grid gap-4">
      <UiAlert v-if="generalError" variant="destructive">
        <UiAlertTitle>Login failed</UiAlertTitle>
        <UiAlertDescription>{{ generalError }}</UiAlertDescription>
      </UiAlert>

      <div class="grid gap-2">
        <UiLabel for="email">
          {{ $t('email') }}
        </UiLabel>
        <UiInput id="email" v-model="email" type="email" placeholder="m@example.com" required :aria-invalid="!!fieldErrors.email?.length" />
        <p v-if="fieldErrors.email?.length" class="text-sm text-destructive">
          {{ fieldErrors.email[0] }}
        </p>
      </div>
      <div class="grid gap-2">
        <div class="flex items-center justify-between">
          <UiLabel for="password">
            {{ $t('password') }}
          </UiLabel>
          <ToForgotPasswordLink />
        </div>
        <UiInput id="password" v-model="password" type="password" required placeholder="*********" :aria-invalid="!!fieldErrors.password?.length" />
        <p v-if="fieldErrors.password?.length" class="text-sm text-destructive">
          {{ fieldErrors.password[0] }}
        </p>
      </div>

      <UiButton class="w-full" :disabled="loading" @click="handleLogin">
        <UiSpinner v-if="loading" class="mr-2" />
        {{ $t('login') }}
      </UiButton>

      <UiSeparator label="Or continue with" />

      <div class="flex flex-col items-center justify-between gap-4">
        <GitHubButton />
        <GoogleButton />
      </div>

      <UiCardDescription>
        By clicking login, you agree to our
        <TermsOfServiceButton />
        and
        <PrivacyPolicyButton />
      </UiCardDescription>
    </UiCardContent>
  </UiCard>
</template>
