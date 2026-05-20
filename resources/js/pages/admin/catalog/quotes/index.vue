<script lang="ts" setup>
import { computed, ref } from 'vue'
import { BasicPage } from '@/components/global-layout'
import { Button } from '@/components/ui/button'
import { Textarea } from '@/components/ui/textarea'
import ConfirmDialog from '@/components/confirm-dialog.vue'
import { useTableFilters } from '@/composables/use-table-filters'
import { useDeleteQuoteRequestMutation, useGetQuoteRequestsQuery, useReplyQuoteRequestMutation } from '@/services/api/catalog.api'
import { createColumns, type QuoteRequest } from './columns'
import DataTable from './data-table.vue'

const filters = useTableFilters()
const { data: response, isLoading, refetch } = useGetQuoteRequestsQuery(filters.params)
const { mutate: deleteQuote, isPending: isDeleting } = useDeleteQuoteRequestMutation()
const { mutate: replyQuote } = useReplyQuoteRequestMutation()

const quotes = computed(() => {
  const d = response.value as any
  return d?.data ?? []
})

const pagination = computed(() => ({
  page: filters.page.value,
  pageSize: filters.pageSize.value,
  total: (response.value as any)?.total ?? 0,
  onPageChange: (page: number) => { filters.page.value = page },
  onPageSizeChange: (pageSize: number) => { filters.pageSize.value = pageSize; filters.page.value = 1 },
}))

const replyOpen = ref(false)
const replyId = ref<number | null>(null)
const replyText = ref('')
const deleteTargetId = ref<number | null>(null)
const showDeleteDialog = ref(false)

function openReply(quote: QuoteRequest) {
  replyId.value = quote.id
  replyText.value = quote.reply || ''
  replyOpen.value = true
}

function handleReply() {
  if (replyId.value !== null && replyText.value.trim()) {
    replyQuote({ id: replyId.value, reply: replyText.value }, {
      onSuccess: () => {
        replyOpen.value = false
        replyId.value = null
        replyText.value = ''
      },
    })
  }
}

function confirmDelete(id: number) {
  deleteTargetId.value = id
  showDeleteDialog.value = true
}

function handleDelete() {
  if (deleteTargetId.value !== null) {
    deleteQuote(deleteTargetId.value)
  }
  showDeleteDialog.value = false
  deleteTargetId.value = null
}

const columns = createColumns(openReply, confirmDelete)
</script>

<template>
  <BasicPage :title="$t('admin.catalog.quotes')" :description="''" sticky>
    <template #actions>
      <Button variant="outline" @click="refetch">{{ $t('admin.btn.refresh') }}</Button>
    </template>

    <DataTable :columns :data="quotes" :loading="isLoading" :filters :server-pagination="pagination" />

    <ConfirmDialog v-model:open="showDeleteDialog" :is-loading="isDeleting" :cancel-button-text="$t('admin.btn.cancel')" :confirm-button-text="$t('admin.btn.delete')" destructive @confirm="handleDelete">
      <template #title>{{ $t('admin.dialog.deleteTitle', { item: $t('admin.catalog.quotes') }) }}</template>
      <template #description>{{ $t('admin.dialog.deleteDescription', { item: $t('admin.catalog.quotes').toLowerCase() }) }}</template>
    </ConfirmDialog>

    <ConfirmDialog v-model:open="replyOpen" :cancel-button-text="$t('admin.btn.cancel')" :confirm-button-text="$t('admin.catalog.sendReply')" @confirm="handleReply">
      <template #title>{{ $t('admin.catalog.quoteReply') }}</template>
      <template #description>
        <Textarea v-model="replyText" :placeholder="$t('admin.catalog.quoteReplyPlaceholder')" class="mt-2" rows="4" />
      </template>
    </ConfirmDialog>
  </BasicPage>
</template>
