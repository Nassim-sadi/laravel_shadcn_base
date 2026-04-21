import { useCookies } from '@vueuse/integrations/useCookies'
import { useRouter } from 'vue-router'
import { ofetch } from 'ofetch'

import { API_BASE_URL, API_TIMEOUT } from '@/constants/app-config'

const AUTH_TOKEN_NAME = 'auth_token'

export function useApiFetch() {
  const cookies = useCookies([AUTH_TOKEN_NAME])
  const router = useRouter()

  const apiFetch = ofetch.create({
    baseURL: API_BASE_URL,
    timeout: API_TIMEOUT ?? 0,
    async onRequest({ request }) {
      const currentToken = cookies.get(AUTH_TOKEN_NAME)
      if (currentToken && request.headers) {
        request.headers.set('Authorization', `Bearer ${currentToken}`)
      }
    },
    onResponseError({ response }) {
      if (response.status === 401) {
        cookies.remove(AUTH_TOKEN_NAME)
        router.push('/auth/login')
      }
    },
  })

  const setToken = (newToken: string) => {
    cookies.set(AUTH_TOKEN_NAME, newToken, { expires: new Date(Date.now() + 365 * 24 * 60 * 60 * 1000) })
  }

  const clearToken = () => {
    cookies.remove(AUTH_TOKEN_NAME)
  }

  const getToken = () => {
    return cookies.get(AUTH_TOKEN_NAME)
  }

  const isAuthenticated = () => {
    return !!getToken()
  }

  return {
    apiFetch,
    setToken,
    clearToken,
    getToken,
    isAuthenticated,
  }
}
