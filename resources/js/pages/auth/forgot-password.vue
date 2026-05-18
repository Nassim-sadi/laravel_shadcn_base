<script setup lang="ts">
import { $fetch } from 'ofetch'
import { useI18n } from 'vue-i18n'
import { toast } from 'vue-sonner'

import AuthTitle from './components/auth-title.vue'

const { t } = useI18n()

const email = ref('')
const loading = ref(false)
const sent = ref(false)
const fieldErrors = ref<Record<string, string[]>>({})
const generalError = ref('')

async function handleSend() {
  if (!email.value) return
  loading.value = true
  generalError.value = ''
  fieldErrors.value = {}

  try {
    await $fetch('/sanctum/csrf-cookie', { credentials: 'include' })
    await $fetch('/forgot-password', {
      method: 'post',
      body: { email: email.value },
      credentials: 'include',
      headers: { Accept: 'application/json' },
    })
    sent.value = true
    toast.success(t('admin.toast.resetLinkSent'))
  }
  catch (err: any) {
    fieldErrors.value = err?.data?.errors ?? {}
    generalError.value = Object.values(fieldErrors.value).flat().find(Boolean)
      || err?.data?.message
      || err?.message
      || t('admin.toast.loginFailed')
    toast.error(generalError.value)
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
          <UiCardTitle class="text-2xl">{{ $t('forgotPassword') }}</UiCardTitle>
          <UiCardDescription>{{ $t('admin.misc.enterEmailReset') }}</UiCardDescription>
        </UiCardHeader>
        <UiCardContent class="grid gap-4">
          <template v-if="sent">
            <UiAlert variant="default">
              <UiAlertTitle>{{ $t('admin.misc.checkEmail') }}</UiAlertTitle>
              <UiAlertDescription>
                {{ $t('admin.misc.resetLinkSentTo') }} <strong>{{ email }}</strong>.
              </UiAlertDescription>
            </UiAlert>
          </template>

          <template v-else>
            <UiAlert v-if="generalError" variant="destructive">
              <UiAlertTitle>{{ $t('admin.toast.loginFailed') }}</UiAlertTitle>
              <UiAlertDescription>{{ generalError }}</UiAlertDescription>
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
          </template>
        </UiCardContent>
        <UiCardFooter class="flex flex-col gap-2">
          <UiButton v-if="!sent" class="w-full" :disabled="loading || !email" @click="handleSend">
            <UiSpinner v-if="loading" class="mr-2 size-4" />
            {{ $t('admin.btn.continue') }}
          </UiButton>

          <UiButton variant="link" class="text-muted-foreground" @click="$router.push('/auth/login')">
            {{ $t('admin.btn.backToLogin') }}
          </UiButton>
        </UiCardFooter>
      </UiCard>
    </main>
  </div>
</template>
