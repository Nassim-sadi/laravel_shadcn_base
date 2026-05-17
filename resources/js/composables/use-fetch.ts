import { ofetch } from 'ofetch'
import { useRouter } from 'vue-router'

import { API_BASE_URL, API_TIMEOUT } from '@/constants/app-config'

export function useApiFetch() {
  const router = useRouter()

  const apiFetch = ofetch.create({
    baseURL: API_BASE_URL,
    timeout: API_TIMEOUT ?? 0,
    credentials: 'include',
    headers: {
      Accept: 'application/json',
    },
    onResponseError({ response }) {
      if (response.status === 401) {
        router.push('/auth/login')
      }
    },
  })

  return {
    apiFetch,
  }
}
