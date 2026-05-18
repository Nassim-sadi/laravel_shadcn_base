<script setup lang="ts">
import { $fetch } from 'ofetch'
import { useI18n } from 'vue-i18n'
import { toast } from 'vue-sonner'

import { useAuthStore } from '@/stores/auth'

import AuthTitle from './components/auth-title.vue'

const { t } = useI18n()
const router = useRouter()
const authStore = useAuthStore()

const sending = ref(false)
const sent = ref(false)

async function resend() {
  sending.value = true
  try {
    await $fetch('/sanctum/csrf-cookie', { credentials: 'include' })
    await $fetch('/email/verification-notification', {
      method: 'post',
      credentials: 'include',
      headers: { Accept: 'application/json' },
    })
    sent.value = true
    toast.success(t('admin.toast.verificationLinkSent'))
  }
  catch (err: any) {
    toast.error(err?.data?.message || t('admin.toast.verificationFailed'))
  }
  finally {
    sending.value = false
  }
}

async function logout() {
  try {
    await $fetch('/sanctum/csrf-cookie', { credentials: 'include' })
    await $fetch('/logout', {
      method: 'post',
      credentials: 'include',
      headers: { Accept: 'application/json' },
    })
    authStore.clearUser()
    router.push('/auth/login')
  }
  catch {
    router.push('/auth/login')
  }
}
</script>

<template>
  <div class="flex items-center justify-center min-h-screen p-4 min-w-screen">
    <main class="flex flex-col gap-4">
      <AuthTitle />
      <UiCard class="w-full max-w-sm">
        <UiCardHeader>
          <UiCardTitle class="text-2xl">{{ $t('admin.misc.verifyEmail') }}</UiCardTitle>
          <UiCardDescription>{{ $t('admin.misc.verifyEmailDesc') }}</UiCardDescription>
        </UiCardHeader>
        <UiCardContent class="grid gap-4">
          <UiAlert v-if="sent" variant="default">
            <UiAlertTitle>{{ $t('admin.misc.checkEmail') }}</UiAlertTitle>
            <UiAlertDescription>{{ $t('admin.misc.verificationLinkSent') }}</UiAlertDescription>
          </UiAlert>

          <p class="text-sm text-muted-foreground">
            {{ $t('admin.misc.verifyEmailHelp') }}
          </p>
        </UiCardContent>
        <UiCardFooter class="flex flex-col gap-2">
          <UiButton class="w-full" :disabled="sending" @click="resend">
            <UiSpinner v-if="sending" class="mr-2 size-4" />
            {{ $t('admin.btn.resendVerification') }}
          </UiButton>
          <UiButton variant="link" class="text-muted-foreground" @click="logout">
            {{ $t('admin.nav.logout') }}
          </UiButton>
        </UiCardFooter>
      </UiCard>
    </main>
  </div>
</template>
