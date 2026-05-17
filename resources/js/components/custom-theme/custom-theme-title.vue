<script lang="ts" setup>
import { useI18n } from 'vue-i18n'

const { t } = useI18n()
</script>

<template>
  <div class="grid space-y-1">
    <h1 class="font-semibold text-md text-foreground">
      {{ t('admin.theme.title') }}
    </h1>
    <p class="text-xs text-muted-foreground">
      {{ t('admin.theme.description') }}
    </p>
  </div>
</template>
