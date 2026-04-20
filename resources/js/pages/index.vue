<script lang="ts" setup>
import { useRouter } from 'vue-router'

import { Button } from '@/components/ui/button'

const router = useRouter()
</script>

<template>
  <div class="min-h-screen bg-background">
    <header class="border-b">
      <div class="container mx-auto flex h-16 items-center justify-between px-4">
        <div class="flex items-center gap-2">
          <span class="text-xl font-bold">NsBase</span>
        </div>
        <nav class="flex items-center gap-4">
          <Button variant="ghost" @click="router.push('/auth/sign-in')">
            Sign In
          </Button>
          <Button @click="router.push('/auth/sign-up')">
            Get Started
          </Button>
        </nav>
      </div>
    </header>

    <main>
      <section class="py-20">
        <div class="container mx-auto px-4 text-center">
          <h1 class="text-4xl font-bold tracking-tight sm:text-6xl">
            Build modern web apps<br>with NsBase
          </h1>
          <p class="mx-auto mt-6 max-w-2xl text-lg text-muted-foreground">
            A powerful Vue 3 + TypeScript admin template with shadcn-vue, TanStack Table, Vue Query, and more.
          </p>
          <div class="mt-10 flex items-center justify-center gap-4">
            <Button size="lg" @click="router.push('/auth/sign-up')">
              Start Building
            </Button>
            <Button size="lg" variant="outline" @click="router.push('/admin')">
              View Demo
            </Button>
          </div>
        </div>
      </section>

      <section class="py-20 bg-muted/30">
        <div class="container mx-auto px-4">
          <div class="grid gap-8 sm:grid-cols-3">
            <div class="space-y-2">
              <h3 class="text-xl font-semibold">
                Vue 3 + TypeScript
              </h3>
              <p class="text-muted-foreground">
                Built with Composition API and full TypeScript support for type-safe development.
              </p>
            </div>
            <div class="space-y-2">
              <h3 class="text-xl font-semibold">
                shadcn-vue
              </h3>
              <p class="text-muted-foreground">
                Beautiful and accessible components built with Radix Vue and styled with Tailwind.
              </p>
            </div>
            <div class="space-y-2">
              <h3 class="text-xl font-semibold">
                Full Admin Panel
              </h3>
              <p class="text-muted-foreground">
                Users, Roles, Permissions, and Activity Logs management out of the box.
              </p>
            </div>
          </div>
        </div>
      </section>
    </main>

    <footer class="border-t py-8">
      <div class="container mx-auto px-4 text-center text-sm text-muted-foreground">
        <p>&copy; 2026 NsBase. All rights reserved.</p>
      </div>
    </footer>
  </div>
</template>
