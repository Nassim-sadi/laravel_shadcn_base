import { computed, ref, watch } from 'vue'

export interface FilterParams {
  search?: string
  is_active?: string
  is_read?: string
  is_published?: string
  category?: string
  category_id?: string
  brand_id?: string
  rating?: string
  icon?: string
  client?: string
  from_date?: string
  to_date?: string
  date?: string
  status?: string
  page?: number
  per_page?: number
}

export function useTableFilters(defaults: Partial<FilterParams> = {}, debounceMs = 300) {
  const searchInput = ref(defaults.search ?? '')
  const search = ref(defaults.search ?? '')
  const is_active = ref<string | undefined>(defaults.is_active)
  const is_read = ref<string | undefined>(defaults.is_read)
  const is_published = ref<string | undefined>(defaults.is_published)
  const category = ref<string | undefined>(defaults.category)
  const category_id = ref<string | undefined>(defaults.category_id)
  const brand_id = ref<string | undefined>(defaults.brand_id)
  const rating = ref<string | undefined>(defaults.rating)
  const icon = ref<string | undefined>(defaults.icon)
  const client = ref<string | undefined>(defaults.client)
  const from_date = ref<string | undefined>(defaults.from_date)
  const to_date = ref<string | undefined>(defaults.to_date)
  const date = ref<string | undefined>(defaults.date)
  const status = ref<string | undefined>(defaults.status)
  const page = ref(1)
  const pageSize = ref(15)

  let debounceTimer: ReturnType<typeof setTimeout> | null = null

  watch(searchInput, (value) => {
    if (debounceTimer) clearTimeout(debounceTimer)
    debounceTimer = setTimeout(() => {
      search.value = value
      page.value = 1
    }, debounceMs)
  })

  function reset() {
    searchInput.value = ''
    search.value = ''
    is_active.value = undefined
    is_read.value = undefined
    is_published.value = undefined
    category.value = undefined
    category_id.value = undefined
    brand_id.value = undefined
    rating.value = undefined
    icon.value = undefined
    client.value = undefined
    from_date.value = undefined
    to_date.value = undefined
    date.value = undefined
    status.value = undefined
    page.value = 1
  }

  function hasFilters() {
    return !!(search.value || is_active.value || is_read.value || is_published.value || category.value || category_id.value || brand_id.value || rating.value || icon.value || client.value || from_date.value || to_date.value || date.value || status.value)
  }

  const params = computed<FilterParams>(() => {
    const p: FilterParams = {}
    if (search.value) p.search = search.value
    if (is_active.value !== undefined) p.is_active = is_active.value
    if (is_read.value !== undefined) p.is_read = is_read.value
    if (is_published.value !== undefined) p.is_published = is_published.value
    if (category.value) p.category = category.value
    if (category_id.value) p.category_id = category_id.value
    if (brand_id.value) p.brand_id = brand_id.value
    if (rating.value) p.rating = rating.value
    if (icon.value) p.icon = icon.value
    if (client.value) p.client = client.value
    if (from_date.value) p.from_date = from_date.value
    if (to_date.value) p.to_date = to_date.value
    if (date.value) p.date = date.value
    if (status.value) p.status = status.value
    p.page = page.value
    p.per_page = pageSize.value
    return p
  })

  return {
    searchInput,
    search,
    is_active,
    is_read,
    is_published,
    category,
    category_id,
    brand_id,
    rating,
    icon,
    client,
    from_date,
    to_date,
    date,
    status,
    page,
    pageSize,
    params,
    reset,
    hasFilters,
  }
}
