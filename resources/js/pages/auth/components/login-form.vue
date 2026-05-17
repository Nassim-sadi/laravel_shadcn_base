<script lang="ts" setup>
import { Eye, EyeOff } from '@lucide/vue'
import { useI18n } from 'vue-i18n'
import { toast } from 'vue-sonner'

import { useAuth } from '@/composables/use-auth'

import GoogleButton from './google-button.vue'
import PrivacyPolicyButton from './privacy-policy-button.vue'
import TermsOfServiceButton from './terms-of-service-button.vue'
import ToForgotPasswordLink from './to-forgot-password-link.vue'

const { t } = useI18n()
const { login, loading } = useAuth()

const email = ref('')
const password = ref('')
const showPassword = ref(false)
const generalError = ref('')
const fieldErrors = ref<Record<string, string[]>>({})

function readErrorMessage(error: any) {
  fieldErrors.value = error?.data?.errors ?? {}

  const firstFieldError = Object.values(fieldErrors.value)
    .flat()
    .find(Boolean)

  return firstFieldError || error?.data?.message || error?.message || t('admin.toast.loginFailed')
}

async function handleLogin() {
  generalError.value = ''
  fieldErrors.value = {}

  try {
    await login({ email: email.value, password: password.value })
    toast.success(t('admin.toast.welcomeBack'))
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
        {{ $t('login') }}
      </UiCardTitle>
      <UiCardDescription>
        {{ $t('admin.misc.enterEmailAndPassword') }}
        {{ $t('admin.misc.dontHaveAccount') }}
        <UiButton
          variant="link" class="px-0 text-muted-foreground"
          @click="$router.push('/auth/sign-up')"
        >
          {{ $t('admin.btn.signUp') }}
        </UiButton>
      </UiCardDescription>
    </UiCardHeader>
    <UiCardContent class="grid gap-4">
      <UiAlert v-if="generalError" variant="destructive">
        <UiAlertTitle>{{ $t('admin.toast.loginFailed') }}</UiAlertTitle>
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
        <div class="relative">
          <UiInput id="password" v-model="password" :type="showPassword ? 'text' : 'password'" required placeholder="*********" :aria-invalid="!!fieldErrors.password?.length" class="pe-9" />
          <button type="button" class="absolute end-1 top-1/2 -translate-y-1/2 p-2 text-muted-foreground hover:text-foreground" tabindex="-1" @click="showPassword = !showPassword">
            <Eye v-if="showPassword" class="size-4" />
            <EyeOff v-else class="size-4" />
          </button>
        </div>
        <p v-if="fieldErrors.password?.length" class="text-sm text-destructive">
          {{ fieldErrors.password[0] }}
        </p>
      </div>

      <UiButton class="w-full" :disabled="loading" @click="handleLogin">
        <UiSpinner v-if="loading" class="mr-2" />
        {{ $t('login') }}
      </UiButton>

      <UiSeparator :label="$t('admin.misc.orContinueWith')" />

      <div class="flex flex-col items-center justify-between gap-4">
        <GoogleButton />
      </div>

      <UiCardDescription>
        {{ $t('admin.misc.byClickingLogin') }}
        <TermsOfServiceButton />
        and
        <PrivacyPolicyButton />
      </UiCardDescription>
    </UiCardContent>
  </UiCard>
</template>
