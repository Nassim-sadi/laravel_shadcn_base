import { ofetch } from 'ofetch'
import { useRouter } from 'vue-router'

import { API_BASE_URL, API_TIMEOUT } from '@/constants/app-config'

function getXSRFToken(): string | undefined {
  const match = document.cookie.match(/(?:^|;\s*)XSRF-TOKEN=([^;]*)/)
  if (!match) return undefined
  try {
    return decodeURIComponent(match[1])
  }
  catch {
    return match[1]
  }
}

export function useApiFetch() {
  const router = useRouter()

  const apiFetch = ofetch.create({
    baseURL: API_BASE_URL,
    timeout: API_TIMEOUT ?? 0,
    credentials: 'include',
    headers: {
      Accept: 'application/json',
    },
    onRequest({ options }) {
      const token = getXSRFToken()
      if (token) {
        if (options.headers instanceof Headers) {
          options.headers.set('X-XSRF-TOKEN', token)
        }
        else if (options.headers) {
          (options.headers as Record<string, string>)['X-XSRF-TOKEN'] = token
        }
      }
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
