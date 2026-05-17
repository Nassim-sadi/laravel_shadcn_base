<script setup lang="ts">
import { useI18n } from 'vue-i18n'
import { toast } from 'vue-sonner'

import { useRegisterMutation } from '@/services/api/auth.api'

import AuthTitle from './components/auth-title.vue'
import GitHubButton from './components/github-button.vue'
import GoogleButton from './components/google-button.vue'
import PrivacyPolicyButton from './components/privacy-policy-button.vue'
import TermsOfServiceButton from './components/terms-of-service-button.vue'

const { t } = useI18n()
const router = useRouter()
const registerMutation = useRegisterMutation()

const name = ref('')
const email = ref('')
const password = ref('')
const password_confirmation = ref('')
const loading = computed(() => registerMutation.isPending.value)

async function handleRegister() {
  try {
    await registerMutation.mutateAsync({
      name: name.value,
      email: email.value,
      password: password.value,
      password_confirmation: password_confirmation.value,
    })
    toast.success(t('admin.toast.accountCreated'))
    router.push('/')
  }
  catch (error: any) {
    toast.error(error.message || t('admin.toast.registrationFailed'))
  }
}
</script>

<template>
  <div class="flex items-center justify-center min-h-screen p-4 min-w-screen">
    <main class="flex flex-col gap-4">
      <AuthTitle />
      <UiCard class="max-w-sm mx-auto">
        <UiCardHeader>
          <UiCardTitle class="text-xl">
            {{ $t('admin.btn.signUp') }}
          </UiCardTitle>
          <UiCardDescription>
            {{ $t('admin.misc.enterDetails') }}
            {{ $t('admin.misc.alreadyHaveAccount') }}
            <UiButton
              variant="link" class="px-0 text-muted-foreground"
              @click="$router.push('/auth/sign-in')"
            >
              {{ $t('admin.btn.signIn') }}
            </UiButton>
          </UiCardDescription>
        </UiCardHeader>
        <UiCardContent>
          <div class="grid gap-4">
            <div class="grid gap-2">
              <UiLabel for="name">
                {{ $t('admin.label.name') }}
              </UiLabel>
              <UiInput id="name" v-model="name" placeholder="John Doe" required />
            </div>
            <div class="grid gap-2">
              <UiLabel for="email">
                {{ $t('email') }}
              </UiLabel>
              <UiInput
                id="email"
                v-model="email"
                type="email"
                placeholder="m@example.com"
                required
              />
            </div>
            <div class="grid gap-2">
              <UiLabel for="password">
                {{ $t('password') }}
              </UiLabel>
              <UiInput id="password" v-model="password" type="password" placeholder="******" />
            </div>
            <div class="grid gap-2">
              <UiLabel for="confirm-password">
                {{ $t('admin.label.confirmPassword') }}
              </UiLabel>
              <UiInput id="confirm-password" v-model="password_confirmation" type="password" placeholder="******" />
            </div>
            <UiButton type="submit" class="w-full" :disabled="loading" @click="handleRegister">
              {{ $t('admin.btn.createAccount') }}
            </UiButton>

            <UiSeparator :label="$t('admin.misc.orContinueWith')" />

            <div class="flex flex-col items-center justify-between gap-4">
              <GitHubButton />
              <GoogleButton />
            </div>

            <UiCardDescription>
              {{ $t('admin.misc.byClickingLogin') }}
              <TermsOfServiceButton />
              and
              <PrivacyPolicyButton />
            </UiCardDescription>
          </div>
        </UiCardContent>
      </UiCard>
    </main>
  </div>
</template>
