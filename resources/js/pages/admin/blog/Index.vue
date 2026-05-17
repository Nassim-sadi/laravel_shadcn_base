<script lang="ts" setup>
import { PencilIcon, Trash2Icon } from '@lucide/vue'
import { computed, ref } from 'vue'
import { useI18n } from 'vue-i18n'

import ConfirmDialog from '@/components/confirm-dialog.vue'
import { BasicPage } from '@/components/global-layout'
import { Badge } from '@/components/ui/badge'
import { Button } from '@/components/ui/button'
import { Tooltip, TooltipContent, TooltipProvider, TooltipTrigger } from '@/components/ui/tooltip'
import { hasPermission } from '@/composables/use-role'
import { useDeleteBlogPostMutation, useGetBlogPostsQuery } from '@/services/api/blog-posts.api'
import Form from './partials/Form.vue'
import { appLocale } from '@/plugins/i18n'

const { t } = useI18n()
const { data: response, isLoading, refetch } = useGetBlogPostsQuery()
const posts = computed(() => response.value?.data?.data ?? [])

const showSheet = ref(false)
const editingId = ref<number | null>(null)
const editingItem = ref<any>(null)
const deleteTargetId = ref<number | null>(null)
const showDeleteDialog = ref(false)
const { mutate: deletePost, isPending: isDeleting } = useDeleteBlogPostMutation()

function resolveValue(value: any): string {
  if (!value)
    return ''
  if (typeof value === 'string')
    return value
  const locale = appLocale.value
  const fallbackLocale = 'fr'
  return value[locale] || value[fallbackLocale] || Object.values(value).find((v: any) => v) || ''
}

function openCreate() {
  editingId.value = null
  editingItem.value = null
  showSheet.value = true
}

function openEdit(post: any) {
  editingId.value = post.id
  editingItem.value = post
  showSheet.value = true
}

function confirmDelete(id: number) {
  deleteTargetId.value = id
  showDeleteDialog.value = true
}

function handleDelete() {
  if (deleteTargetId.value !== null) {
    deletePost(deleteTargetId.value)
  }
  showDeleteDialog.value = false
  deleteTargetId.value = null
}
</script>

<template>
  <BasicPage :title="$t('admin.page.blog.title')" :description="$t('admin.page.blog.description')" sticky>
    <template #actions>
      <Button variant="outline" @click="refetch">
        {{ $t('admin.btn.refresh') }}
      </Button>
      <Button v-if="hasPermission('blogs.create')" @click="openCreate">
        {{ $t('admin.sheet.createBlogPost') }}
      </Button>
    </template>
    <div class="space-y-4">
      <div v-for="post in posts" :key="post.id" class="flex items-start gap-4 rounded-lg border p-4">
        <div class="flex-1 space-y-1">
          <div class="flex items-center gap-2">
            <span class="font-medium">{{ resolveValue(post.title) }}</span>
            <Badge v-if="post.featured" variant="default">
              {{ $t('admin.blog.featured') }}
            </Badge>
            <Badge :variant="post.is_published ? 'default' : 'secondary'">
              {{ post.is_published ? $t('admin.status.active') : $t('admin.status.inactive') }}
            </Badge>
          </div>
          <p class="text-sm text-muted-foreground">
            {{ post.category ? resolveValue(post.category.name) : '-' }}
            <template v-if="post.tags?.length">
              &middot;
              <span v-for="(tag, i) in post.tags" :key="tag.id">
                {{ tag.name }}{{ i < post.tags.length - 1 ? ', ' : '' }}
              </span>
            </template>
          </p>
        </div>
        <TooltipProvider>
          <div class="flex gap-1">
            <Tooltip>
              <TooltipTrigger as-child>
                <Button v-if="hasPermission('blogs.edit')" variant="ghost" size="icon" class="size-8" @click="openEdit(post)">
                  <PencilIcon class="size-4" />
                </Button>
              </TooltipTrigger>
              <TooltipContent><p>{{ $t('admin.btn.edit') }}</p></TooltipContent>
            </Tooltip>
            <Tooltip>
              <TooltipTrigger as-child>
                <Button v-if="hasPermission('blogs.delete')" variant="destructive" size="icon" class="size-8" @click="confirmDelete(post.id)">
                  <Trash2Icon class="size-4" />
                </Button>
              </TooltipTrigger>
              <TooltipContent><p>{{ $t('admin.btn.delete') }}</p></TooltipContent>
            </Tooltip>
          </div>
        </TooltipProvider>
      </div>
      <div v-if="posts.length === 0 && !isLoading" class="text-center py-8 text-muted-foreground">
        {{ $t('admin.empty.blogPosts') }}
      </div>
    </div>

    <ConfirmDialog
      v-model:open="showDeleteDialog"
      :is-loading="isDeleting"
      :cancel-button-text="$t('admin.btn.cancel')"
      :confirm-button-text="$t('admin.btn.delete')"
      destructive
      @confirm="handleDelete"
    >
      <template #title>
        {{ $t('admin.dialog.deleteTitle', { item: $t('admin.nav.blog') }) }}
      </template>
      <template #description>
        {{ $t('admin.dialog.deleteDescription', { item: $t('admin.nav.blog').toLowerCase() }) }}
      </template>
    </ConfirmDialog>

    <Form v-model:open="showSheet" :editingId="editingId" :item="editingItem" />
  </BasicPage>
</template>
