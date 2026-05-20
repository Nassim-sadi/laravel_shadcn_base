import { useMutation, useQuery, useQueryClient } from '@tanstack/vue-query'

import { API_BASE_URL } from '@/constants/app-config'
import { useApiFetch } from '@/composables/use-fetch'

import type { IResponse } from '../types/response.type'

export type AiModuleKey = 'services' | 'projects' | 'faqs' | 'testimonials' | 'blog_posts'
export type AiContentField
  = | 'title'
    | 'description'
    | 'seo_title'
    | 'seo_description'
    | 'seo_keywords'
    | 'client'
    | 'question'
    | 'answer'
    | 'name'
    | 'position'
    | 'company'
    | 'content'
    | 'excerpt'
    | 'body'

export interface IGenerateAiContentRequest {
  module: AiModuleKey
  mode: 'draft' | 'improve'
  locale: string
  fields: AiContentField[]
  tone?: string
  context?: string
  source?: Partial<Record<AiContentField, string>>
}

export interface IGenerateAiContentResponse {
  fields: Partial<Record<AiContentField, string>>
  usage?: {
    input_tokens?: number | null
    output_tokens?: number | null
    total_tokens?: number | null
    model?: string | null
  }
}

export interface IImportPreviewRowError {
  row: number
  errors: Record<string, string[]>
}

export interface IImportPreviewResponse {
  valid: boolean
  message: string
  preview_token?: string
  item_count: number
  module?: AiModuleKey
  module_label?: string
  row_errors: IImportPreviewRowError[]
}

export interface IImportConfirmResponse {
  created_count: number
  module: AiModuleKey
  module_label: string
}

export interface IAiSettingsResponse {
  provider: string
  model: string
  base_url: string
  timeout: number
  has_api_key: boolean
  api_key_masked: string | null
}

export interface IUpdateAiSettingsRequest {
  provider: string
  api_key?: string
  model: string
  base_url: string
  timeout: number
}

function getXSRFToken(): string | undefined {
  const match = document.cookie.match(/(?:^|;\s*)XSRF-TOKEN=([^;]*)/)
  if (!match)
    return undefined

  try {
    return decodeURIComponent(match[1])
  }
  catch {
    return match[1]
  }
}

export function useGenerateAiContentMutation() {
  const { apiFetch } = useApiFetch()

  return useMutation<IResponse<IGenerateAiContentResponse>, Error, IGenerateAiContentRequest>({
    mutationKey: ['useGenerateAiContentMutation'],
    mutationFn: body => apiFetch('/ai/generate-content', { method: 'post', body }),
  })
}

export function usePreviewAiContentImportMutation() {
  return useMutation<IResponse<IImportPreviewResponse>, any, { module: AiModuleKey, file: File, onProgress?: (progress: number) => void }>({
    mutationKey: ['usePreviewAiContentImportMutation'],
    mutationFn: async ({ module, file, onProgress }) => {
      const formData = new FormData()
      formData.append('module', module)
      formData.append('file', file)

      return await new Promise<IResponse<IImportPreviewResponse>>((resolve, reject) => {
        const xhr = new XMLHttpRequest()
        xhr.open('POST', `${API_BASE_URL}/ai/import-content/preview`)
        xhr.responseType = 'json'
        xhr.withCredentials = true
        xhr.setRequestHeader('Accept', 'application/json')

        const token = getXSRFToken()
        if (token) {
          xhr.setRequestHeader('X-XSRF-TOKEN', token)
        }

        xhr.upload.onprogress = (event) => {
          if (event.lengthComputable) {
            onProgress?.(Math.round((event.loaded / event.total) * 100))
          }
        }

        xhr.onload = () => {
          if (xhr.status >= 200 && xhr.status < 300) {
            resolve(xhr.response)
            return
          }

          reject(xhr.response ?? new Error('Import preview failed.'))
        }

        xhr.onerror = () => reject(new Error('Import preview failed.'))
        xhr.send(formData)
      })
    },
  })
}

export function useConfirmAiContentImportMutation() {
  const { apiFetch } = useApiFetch()

  return useMutation<IResponse<IImportConfirmResponse>, Error, { preview_token: string }>({
    mutationKey: ['useConfirmAiContentImportMutation'],
    mutationFn: body => apiFetch('/ai/import-content/confirm', { method: 'post', body }),
  })
}

export function useGetAiSettingsQuery() {
  const { apiFetch } = useApiFetch()

  return useQuery<IResponse<IAiSettingsResponse>, Error>({
    queryKey: ['useGetAiSettingsQuery'],
    queryFn: () => apiFetch('/ai/settings', { method: 'get' }),
  })
}

export function useUpdateAiSettingsMutation() {
  const { apiFetch } = useApiFetch()
  const queryClient = useQueryClient()

  return useMutation<IResponse<IAiSettingsResponse>, Error, IUpdateAiSettingsRequest>({
    mutationKey: ['useUpdateAiSettingsMutation'],
    mutationFn: body => apiFetch('/ai/settings', { method: 'put', body }),
    onSuccess: () => queryClient.invalidateQueries({ queryKey: ['useGetAiSettingsQuery'] }),
  })
}
