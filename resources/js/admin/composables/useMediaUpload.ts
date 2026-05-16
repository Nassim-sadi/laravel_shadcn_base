import { ref, computed } from 'vue'
import { API_BASE_URL } from '@/constants/app-config'
import { useQueryClient } from '@tanstack/vue-query'

export interface UploadFileEntry {
  id: string
  file: File
  name: string
  alt: string
  progress: number
  status: 'pending' | 'uploading' | 'done' | 'error'
  error?: string
  xhr?: XMLHttpRequest
}

export function useMediaUpload() {
  const files = ref<UploadFileEntry[]>([])
  const isUploading = computed(() => files.value.some(f => f.status === 'uploading'))
  const queryClient = useQueryClient()
  let idCounter = 0

  function addFiles(fileList: FileList | File[]) {
    const newFiles = Array.from(fileList).map(file => ({
      id: `upload-${++idCounter}`,
      file,
      name: file.name.replace(/\.[^.]+$/, ''),
      alt: '',
      progress: 0,
      status: 'pending' as const,
    }))
    files.value.push(...newFiles)
  }

  function removeFile(id: string) {
    const entry = files.value.find(f => f.id === id)
    if (entry?.xhr) entry.xhr.abort()
    files.value = files.value.filter(f => f.id !== id)
  }

  function uploadEntry(entry: UploadFileEntry): Promise<void> {
    return new Promise((resolve) => {
      const formData = new FormData()
      formData.append('file', entry.file)
      if (entry.name) formData.append('name', entry.name)
      if (entry.alt) formData.append('alt_text', entry.alt)

      const xhr = new XMLHttpRequest()
      entry.xhr = xhr
      entry.status = 'uploading'

      xhr.upload.onprogress = (e) => {
        if (e.lengthComputable) {
          entry.progress = Math.round((e.loaded / e.total) * 100)
        }
      }

      xhr.onload = () => {
        if (xhr.status >= 200 && xhr.status < 300) {
          entry.status = 'done'
          entry.progress = 100
        } else {
          entry.status = 'error'
          entry.error = `Upload failed (${xhr.status})`
        }
        resolve()
      }

      xhr.onerror = () => {
        entry.status = 'error'
        entry.error = 'Network error'
        resolve()
      }

      xhr.onabort = () => {
        entry.status = 'error'
        entry.error = 'Cancelled'
        resolve()
      }

      const token = localStorage.getItem('auth_token')
      xhr.open('POST', `${API_BASE_URL}/media`)
      if (token) xhr.setRequestHeader('Authorization', `Bearer ${token}`)
      xhr.send(formData)
    })
  }

  async function uploadAll() {
    const pending = files.value.filter(f => f.status === 'pending')
    await Promise.all(pending.map(uploadEntry))
    queryClient.invalidateQueries({ queryKey: ['useGetMediaQuery'] })
  }

  function cancelFile(id: string) {
    const entry = files.value.find(f => f.id === id)
    if (entry?.xhr) {
      entry.xhr.abort()
    }
    removeFile(id)
  }

  function clearDone() {
    files.value = files.value.filter(f => f.status !== 'done')
  }

  function reset() {
    files.value.forEach(f => f.xhr?.abort())
    files.value = []
  }

  return {
    files,
    isUploading,
    addFiles,
    removeFile,
    uploadAll,
    cancelFile,
    clearDone,
    reset,
    totalFiles: computed(() => files.value.length),
    pendingCount: computed(() => files.value.filter(f => f.status === 'pending').length),
    doneCount: computed(() => files.value.filter(f => f.status === 'done').length),
    errorCount: computed(() => files.value.filter(f => f.status === 'error').length),
  }
}
