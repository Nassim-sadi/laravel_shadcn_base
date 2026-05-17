import { useCookies } from '@vueuse/integrations/useCookies'
import { ofetch } from 'ofetch'
import { useRouter } from 'vue-router'

import { API_BASE_URL, API_TIMEOUT } from '@/constants/app-config'

const AUTH_TOKEN_NAME = 'auth_token'

export function useApiFetch() {
  const cookies = useCookies(['auth_token'])
  const router = useRouter()

  const getStoredToken = () => {
    return localStorage.getItem(AUTH_TOKEN_NAME) || cookies.get(AUTH_TOKEN_NAME)
  }

  const apiFetch = ofetch.create({
    baseURL: API_BASE_URL,
    timeout: API_TIMEOUT ?? 0,
    async onRequest({ request: _request, options }) {
      const currentToken = getStoredToken()
      if (currentToken) {
        options.headers.set('Authorization', `Bearer ${currentToken}`)
      }
    },
    onResponseError({ response }) {
      if (response.status === 401) {
        localStorage.removeItem(AUTH_TOKEN_NAME)
        cookies.remove(AUTH_TOKEN_NAME, { path: '/' })
        router.push('/auth/login')
      }
    },
  })

  const setToken = (newToken: string) => {
    localStorage.setItem(AUTH_TOKEN_NAME, newToken)
    cookies.set(AUTH_TOKEN_NAME, newToken, {
      expires: new Date(Date.now() + 365 * 24 * 60 * 60 * 1000),
      path: '/',
      sameSite: 'lax',
    })
  }

  const clearToken = () => {
    localStorage.removeItem(AUTH_TOKEN_NAME)
    cookies.remove(AUTH_TOKEN_NAME, { path: '/' })
  }

  const getToken = () => {
    return getStoredToken()
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
