<script setup lang="ts">
import { Eye, EyeOff } from '@lucide/vue'
import { $fetch } from 'ofetch'
import { useI18n } from 'vue-i18n'
import { toast } from 'vue-sonner'

import AuthTitle from './components/auth-title.vue'

const { t } = useI18n()
const router = useRouter()
const route = useRoute()

const token = computed(() => route.query.token as string || '')
const email = ref('')
const password = ref('')
const password_confirmation = ref('')
const showPassword = ref(false)
const showConfirmPassword = ref(false)
const loading = ref(false)
const error = ref('')
const fieldErrors = ref<Record<string, string[]>>({})

function readErrorMessage(err: any) {
  fieldErrors.value = err?.data?.errors ?? {}
  const firstError = Object.values(fieldErrors.value).flat().find(Boolean)
  return firstError || err?.data?.message || err?.message || 'Password reset failed'
}

async function handleReset() {
  if (!token.value || !email.value || !password.value || !password_confirmation.value) return
  loading.value = true
  error.value = ''
  fieldErrors.value = {}

  try {
    await $fetch('/sanctum/csrf-cookie', { credentials: 'include' })
    await $fetch('/reset-password', {
      method: 'post',
      body: {
        token: token.value,
        email: email.value,
        password: password.value,
        password_confirmation: password_confirmation.value,
      },
      credentials: 'include',
      headers: { Accept: 'application/json' },
    })
    toast.success(t('admin.toast.passwordChanged'))
    router.push('/auth/login')
  }
  catch (err: any) {
    error.value = readErrorMessage(err)
    toast.error(error.value)
  }
  finally {
    loading.value = false
  }
}
</script>

<template>
  <div class="flex items-center justify-center min-h-screen p-4 min-w-screen">
    <main class="flex flex-col gap-4">
      <AuthTitle />
      <UiCard class="w-full max-w-sm">
        <UiCardHeader>
          <UiCardTitle class="text-2xl">{{ $t('resetPassword') }}</UiCardTitle>
          <UiCardDescription>{{ $t('admin.misc.enterNewPassword') }}</UiCardDescription>
        </UiCardHeader>
        <UiCardContent class="grid gap-4">
          <UiAlert v-if="error" variant="destructive">
            <UiAlertTitle>{{ $t('admin.toast.loginFailed') }}</UiAlertTitle>
            <UiAlertDescription>{{ error }}</UiAlertDescription>
          </UiAlert>

          <div class="grid gap-2">
            <UiLabel for="email">{{ $t('email') }}</UiLabel>
            <UiInput
              id="email"
              v-model="email"
              type="email"
              placeholder="m@example.com"
              required
              :aria-invalid="!!fieldErrors.email?.length"
            />
            <p v-if="fieldErrors.email?.length" class="text-sm text-destructive">
              {{ fieldErrors.email[0] }}
            </p>
          </div>

          <div class="grid gap-2">
            <UiLabel for="password">{{ $t('admin.label.newPassword') }}</UiLabel>
            <div class="relative">
              <UiInput
                id="password"
                v-model="password"
                :type="showPassword ? 'text' : 'password'"
                required
                placeholder="******"
                :aria-invalid="!!fieldErrors.password?.length"
                class="pe-9"
              />
              <button type="button" class="absolute end-1 top-1/2 -translate-y-1/2 p-2 text-muted-foreground hover:text-foreground" tabindex="-1" @click="showPassword = !showPassword">
                <Eye v-if="showPassword" class="size-4" />
                <EyeOff v-else class="size-4" />
              </button>
            </div>
            <p v-if="fieldErrors.password?.length" class="text-sm text-destructive">
              {{ fieldErrors.password[0] }}
            </p>
          </div>

          <div class="grid gap-2">
            <UiLabel for="password-confirm">{{ $t('admin.label.confirmPassword') }}</UiLabel>
            <div class="relative">
              <UiInput
                id="password-confirm"
                v-model="password_confirmation"
                :type="showConfirmPassword ? 'text' : 'password'"
                required
                placeholder="******"
                :aria-invalid="!!fieldErrors.password_confirmation?.length"
                class="pe-9"
              />
              <button type="button" class="absolute end-1 top-1/2 -translate-y-1/2 p-2 text-muted-foreground hover:text-foreground" tabindex="-1" @click="showConfirmPassword = !showConfirmPassword">
                <Eye v-if="showConfirmPassword" class="size-4" />
                <EyeOff v-else class="size-4" />
              </button>
            </div>
            <p v-if="fieldErrors.password_confirmation?.length" class="text-sm text-destructive">
              {{ fieldErrors.password_confirmation[0] }}
            </p>
          </div>

          <UiButton class="w-full" :disabled="loading || !token || !email || !password" @click="handleReset">
            <UiSpinner v-if="loading" class="mr-2 size-4" />
            {{ $t('resetPassword') }}
          </UiButton>
        </UiCardContent>
        <UiCardFooter>
          <UiButton variant="link" class="text-muted-foreground" @click="$router.push('/auth/login')">
            {{ $t('admin.btn.backToLogin') }}
          </UiButton>
        </UiCardFooter>
      </UiCard>
    </main>
  </div>
</template>
