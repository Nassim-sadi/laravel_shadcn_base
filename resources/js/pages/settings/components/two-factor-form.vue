<script setup lang="ts">
import { $fetch } from 'ofetch'
import { toast } from 'vue-sonner'

interface TwoFactorStatus {
  enabled: boolean
  qrCode?: string
  secretKey?: string
  recoveryCodes?: string[]
}

const status = ref<TwoFactorStatus>({ enabled: false })
const loading = ref(false)
const confirming = ref(false)
const confirmationCode = ref('')
const showRecoveryCodes = ref(false)

async function fetchCsrfCookie() {
  await $fetch('/sanctum/csrf-cookie', { credentials: 'include' })
}

function getCsrfToken(): string {
  const match = document.cookie.match(/(?:^|;\s*)XSRF-TOKEN=([^;]*)/)
  return match ? decodeURIComponent(match[1]) : ''
}

async function checkStatus() {
  try {
    await fetchCsrfCookie()
    const token = getCsrfToken()
    const qrResponse = await $fetch<string>('/user/two-factor-qr-code', {
      credentials: 'include',
      headers: { 'X-XSRF-TOKEN': token, Accept: 'application/json' },
    }).catch(() => null)
    const secretResponse = await $fetch<string>('/user/two-factor-secret-key', {
      credentials: 'include',
      headers: { 'X-XSRF-TOKEN': token, Accept: 'application/json' },
    }).catch(() => null)
    const codesResponse = await $fetch<string[]>('/user/two-factor-recovery-codes', {
      credentials: 'include',
      headers: { 'X-XSRF-TOKEN': token, Accept: 'application/json' },
    }).catch(() => null)

    status.value = {
      enabled: qrResponse !== null,
      qrCode: qrResponse ?? undefined,
      secretKey: secretResponse ?? undefined,
      recoveryCodes: codesResponse ?? undefined,
    }
  }
  catch {
    status.value = { enabled: false }
  }
}

async function enable() {
  loading.value = true
  try {
    await fetchCsrfCookie()
    const token = getCsrfToken()

    await $fetch('/user/two-factor-authentication', {
      method: 'post',
      credentials: 'include',
      headers: { 'X-XSRF-TOKEN': token, Accept: 'application/json' },
    })

    const qrResponse = await $fetch<string>('/user/two-factor-qr-code', {
      credentials: 'include',
      headers: { 'X-XSRF-TOKEN': token, Accept: 'application/json' },
    })
    const secretResponse = await $fetch<string>('/user/two-factor-secret-key', {
      credentials: 'include',
      headers: { 'X-XSRF-TOKEN': token, Accept: 'application/json' },
    })

    status.value.enabled = true
    status.value.qrCode = qrResponse
    status.value.secretKey = secretResponse
    confirming.value = true
  }
  catch (error: any) {
    toast.error(error?.data?.message ?? 'Failed to enable 2FA')
  }
  finally {
    loading.value = false
  }
}

async function confirm() {
  if (!confirmationCode.value) return
  loading.value = true
  try {
    await fetchCsrfCookie()
    const token = getCsrfToken()

    await $fetch('/user/confirmed-two-factor-authentication', {
      method: 'post',
      body: { code: confirmationCode.value },
      credentials: 'include',
      headers: { 'X-XSRF-TOKEN': token, Accept: 'application/json' },
    })

    confirming.value = false
    toast.success('Two-factor authentication enabled')

    const codesResponse = await $fetch<string[]>('/user/two-factor-recovery-codes', {
      credentials: 'include',
      headers: { 'X-XSRF-TOKEN': token, Accept: 'application/json' },
    })
    status.value.recoveryCodes = codesResponse
    showRecoveryCodes.value = true
  }
  catch (error: any) {
    toast.error(error?.data?.message ?? 'Invalid code')
  }
  finally {
    loading.value = false
  }
}

async function disable() {
  loading.value = true
  try {
    await fetchCsrfCookie()
    const token = getCsrfToken()

    await $fetch('/user/two-factor-authentication', {
      method: 'delete',
      credentials: 'include',
      headers: { 'X-XSRF-TOKEN': token, Accept: 'application/json' },
    })

    status.value = { enabled: false }
    confirming.value = false
    showRecoveryCodes.value = false
    toast.success('Two-factor authentication disabled')
  }
  catch (error: any) {
    toast.error(error?.data?.message ?? 'Failed to disable 2FA')
  }
  finally {
    loading.value = false
  }
}

async function regenerateCodes() {
  loading.value = true
  try {
    await fetchCsrfCookie()
    const token = getCsrfToken()

    const codes = await $fetch<string[]>('/user/two-factor-recovery-codes', {
      method: 'post',
      credentials: 'include',
      headers: { 'X-XSRF-TOKEN': token, Accept: 'application/json' },
    })

    status.value.recoveryCodes = codes
    toast.success('Recovery codes regenerated')
  }
  catch (error: any) {
    toast.error(error?.data?.message ?? 'Failed to regenerate codes')
  }
  finally {
    loading.value = false
  }
}

onMounted(() => {
  checkStatus()
})
</script>

<template>
  <div class="max-w-2xl">
    <div class="mb-6">
      <h3 class="text-lg font-medium">Two-Factor Authentication</h3>
      <p class="text-sm text-muted-foreground">
        Add additional security to your account using two-factor authentication.
      </p>
    </div>

    <Separator class="mb-6" />

    <div v-if="!status.enabled && !confirming" class="space-y-4">
      <p class="text-sm text-muted-foreground">
        When two-factor authentication is enabled, you will be prompted for a secure,
        random token during authentication. You may retrieve this token from your
        phone's authenticator application.
      </p>
      <UiButton :disabled="loading" @click="enable">
        <UiSpinner v-if="loading" class="mr-2 size-4" />
        Enable Two-Factor Authentication
      </UiButton>
    </div>

    <div v-if="confirming && status.qrCode" class="space-y-4">
      <p class="text-sm text-muted-foreground">
        Scan the QR code with your authenticator app, then enter the verification code below.
      </p>
      <div class="flex justify-center">
        <div v-html="status.qrCode" class="bg-white p-4 rounded-lg" />
      </div>
      <div v-if="status.secretKey" class="text-center">
        <p class="text-xs text-muted-foreground mb-1">Or enter this key manually:</p>
        <code class="rounded bg-muted px-3 py-1 text-sm">{{ status.secretKey }}</code>
      </div>
      <div class="flex gap-2 items-end">
        <div class="grid gap-1.5">
          <UiLabel for="code">Verification Code</UiLabel>
          <UiInput id="code" v-model="confirmationCode" placeholder="000000" inputmode="numeric" />
        </div>
        <UiButton :disabled="loading || !confirmationCode" @click="confirm">
          <UiSpinner v-if="loading" class="mr-2 size-4" />
          Confirm
        </UiButton>
      </div>
    </div>

    <div v-if="status.enabled && !confirming" class="space-y-4">
      <div class="flex items-center gap-2">
        <div class="size-2 rounded-full bg-emerald-500" />
        <span class="text-sm font-medium">Two-factor authentication is enabled.</span>
      </div>

      <UiButton variant="outline" :disabled="loading" @click="disable">
        <UiSpinner v-if="loading" class="mr-2 size-4" />
        Disable Two-Factor Authentication
      </UiButton>

      <Separator />

      <div>
        <h4 class="text-sm font-medium mb-2">Recovery Codes</h4>
        <p class="text-xs text-muted-foreground mb-3">
          Store these recovery codes in a secure location. They can be used to access
          your account if you lose access to your authenticator device.
        </p>
        <UiButton variant="outline" size="sm" :disabled="loading" @click="showRecoveryCodes = !showRecoveryCodes">
          {{ showRecoveryCodes ? 'Hide' : 'Show' }} Recovery Codes
        </UiButton>
        <UiButton variant="outline" size="sm" class="ml-2" :disabled="loading" @click="regenerateCodes">
          Regenerate Codes
        </UiButton>

        <div v-if="showRecoveryCodes && status.recoveryCodes" class="mt-3">
          <div class="rounded-lg border bg-muted/50 p-3 font-mono text-sm space-y-1">
            <div v-for="(code, i) in status.recoveryCodes" :key="i">
              {{ code }}
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>
