import { useCookies } from '@vueuse/integrations/useCookies'
/**
 * ofetch: https://github.com/unjs/ofetch
 */
import { ofetch } from 'ofetch'

import { API_BASE_URL, API_TIMEOUT } from '@/constants/app-config'

const AUTH_TOKEN_NAME = 'auth_token'

export function useApiFetch() {
  const cookies = useCookies([AUTH_TOKEN_NAME])

  const apiFetch = ofetch.create({
    baseURL: API_BASE_URL,
    timeout: API_TIMEOUT ?? false,
    async onRequest(_request) {
      const currentToken = cookies.get(AUTH_TOKEN_NAME)
      if (currentToken) {
        // Token will be added via cookie header on server side
      }
    },
    onRequestError: (_error) => {},
    onResponse: (_response) => {},
    onResponseError: (_error) => {},
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

  return {
    apiFetch,
    setToken,
    clearToken,
    getToken,
  }
}
