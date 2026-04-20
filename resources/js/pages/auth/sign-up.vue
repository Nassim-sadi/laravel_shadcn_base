<script setup lang="ts">
import { toast } from 'vue-sonner'

import { useRegisterMutation } from '@/services/api/auth.api'

import AuthTitle from './components/auth-title.vue'
import GitHubButton from './components/github-button.vue'
import GoogleButton from './components/google-button.vue'
import PrivacyPolicyButton from './components/privacy-policy-button.vue'
import TermsOfServiceButton from './components/terms-of-service-button.vue'

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
    toast.success('Account created successfully!')
    router.push('/')
  }
  catch (error: any) {
    toast.error(error.message || 'Registration failed')
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
            Sign Up
          </UiCardTitle>
          <UiCardDescription>
            Enter your details to create an account.
            Already have an account?
            <UiButton
              variant="link" class="px-0 text-muted-foreground"
              @click="$router.push('/auth/sign-in')"
            >
              Sign In
            </UiButton>
          </UiCardDescription>
        </UiCardHeader>
        <UiCardContent>
          <div class="grid gap-4">
            <div class="grid gap-2">
              <UiLabel for="name">
                Name
              </UiLabel>
              <UiInput id="name" v-model="name" placeholder="John Doe" required />
            </div>
            <div class="grid gap-2">
              <UiLabel for="email">
                Email
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
                Password
              </UiLabel>
              <UiInput id="password" v-model="password" type="password" placeholder="******" />
            </div>
            <div class="grid gap-2">
              <UiLabel for="confirm-password">
                Confirm Password
              </UiLabel>
              <UiInput id="confirm-password" v-model="password_confirmation" type="password" placeholder="******" />
            </div>
            <UiButton type="submit" class="w-full" :disabled="loading" @click="handleRegister">
              Create Account
            </UiButton>

            <UiSeparator label="Or continue with" />

            <div class="flex flex-col items-center justify-between gap-4">
              <GitHubButton />
              <GoogleButton />
            </div>

            <UiCardDescription>
              By creating an account, you agree to our
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
