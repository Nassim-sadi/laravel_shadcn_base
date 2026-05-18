<script lang="ts" setup>
import { Icon } from '@iconify/vue'

function handleGoogleLogin() {
  window.location.href = '/auth/google'
}
</script>

<template>
  <UiButton variant="outline" class="w-full" @click="handleGoogleLogin">
    <Icon icon="simple-icons:google" class="size-5 mr-2" />
    Google
  </UiButton>
</template>
