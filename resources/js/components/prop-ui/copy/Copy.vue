<script lang="ts" setup>
import type { HTMLAttributes } from 'vue'

import { CopyCheckIcon, CopyIcon } from '@lucide/vue'
import { useClipboard } from '@vueuse/core'

import type { ButtonVariants } from '@/components/ui/button'

import { Button } from '@/components/ui/button'
import {
  Tooltip,
  TooltipContent,
  TooltipProvider,
  TooltipTrigger,
} from '@/components/ui/tooltip'
import { cn } from '@/lib/utils'

import { copyVariants } from '.'

interface Props {
  content: string
  size?: 'sm' | 'default'
  variant?: ButtonVariants['variant']
  class?: HTMLAttributes['class']
  copyTooltipText?: string
  copiedTooltipText?: string
}

const props = withDefaults(defineProps<Props>(), {
  size: 'default',
  variant: 'outline',
  copyTooltipText: 'Copy',
  copiedTooltipText: 'Copied',
})

const iconSize = computed(() => {
  return props.size === 'sm' ? 'sm' : 'default'
})

const size = computed(() => {
  return props.size === 'sm' ? 'sm' : 'icon'
})

const source = computed(() => props.content)

const { copy, copied } = useClipboard({ source })

const fallbackCopied = ref(false)
const isCopied = computed(() => copied.value || fallbackCopied.value)

async function copyContent() {
  try {
    await copy(source.value)
    return
  }
  catch (_e) {
    // fall back to textarea method
  }

  const input = document.createElement('textarea')
  input.value = source.value
  input.setAttribute('readonly', '')
  input.style.position = 'fixed'
  input.style.opacity = '0'
  document.body.appendChild(input)
  input.select()
  document.execCommand('copy')
  document.body.removeChild(input)

  fallbackCopied.value = true
  window.setTimeout(() => {
    fallbackCopied.value = false
  }, 1500)
}
</script>

<template>
  <TooltipProvider>
    <Tooltip>
      <TooltipTrigger as-child>
        <Button
          :variant="props.variant"
          :size="size"
          :class="cn(props.class)"
          @click="copyContent"
        >
          <CopyIcon v-if="!isCopied" :class="cn(copyVariants({ iconSize }))" />
          <CopyCheckIcon v-else :class="cn(copyVariants({ iconSize }))" />
        </Button>
      </TooltipTrigger>
      <TooltipContent>
        <p v-if="!isCopied">
          {{ props.copyTooltipText }}: {{ props.content }}
        </p>
        <p v-else>
          {{ props.copiedTooltipText }}: {{ props.content }}
        </p>
      </TooltipContent>
    </Tooltip>
  </TooltipProvider>
</template>
