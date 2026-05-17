<script lang="ts" setup>
import { PaintbrushIcon } from '@lucide/vue'

import { Button } from '@/components/ui/button'
import {
  Popover,
  PopoverContent,
  PopoverTrigger,
} from '@/components/ui/popover'

import CustomColor from './custom-color.vue'
import CustomHover from './custom-hover.vue'
import CustomRadius from './custom-radius.vue'
import CustomShadow from './custom-shadow.vue'
import CustomThemeTitle from './custom-theme-title.vue'
import MenuStyle from './menu-style.vue'
import ToggleColorMode from './toggle-color-mode.vue'
</script>

<template>
  <Popover>
    <PopoverTrigger>
      <Button variant="outline" size="icon">
        <PaintbrushIcon />
      </Button>
    </PopoverTrigger>
    <PopoverContent align="end">
      <CustomThemeTitle />
      <CustomColor />
      <CustomRadius />
      <ToggleColorMode />
      <CustomShadow />
      <CustomHover />
      <MenuStyle />
    </PopoverContent>
  </Popover>
</template>
