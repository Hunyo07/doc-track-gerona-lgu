<template>
  <div class="min-h-screen bg-gray-50 py-8">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
      <!-- Loading State -->
      <div v-if="loading" class="text-center py-12">
        <div class="inline-block animate-spin rounded-full h-8 w-8 border-b-2 border-blue-600"></div>
        <p class="mt-2 text-gray-600">Loading document...</p>
      </div>

      <!-- Error State -->
      <div v-else-if="error" class="text-center py-12">
        <div class="bg-red-50 border border-red-200 rounded-lg p-6">
          <svg class="mx-auto h-12 w-12 text-red-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z" />
          </svg>
          <h3 class="mt-2 text-lg font-medium text-red-800">Document Not Found</h3>
          <p class="mt-1 text-red-600">{{ error }}</p>
        </div>
      </div>

      <!-- Document Display -->
      <div v-else-if="document" class="space-y-6">
        <!-- Header -->
        <div class="bg-white shadow rounded-lg">
          <div class="px-6 py-4 border-b border-gray-200">
            <div class="flex items-center justify-between">
              <div>
                <h1 class="text-2xl font-bold text-gray-900">{{ document.title }}</h1>
                <p class="text-sm text-gray-500">Document #{{ document.document_number }}</p>
              </div>
              <div class="flex items-center space-x-2">
                <span :class="getStatusClass(document.status)" class="px-3 py-1 rounded-full text-xs font-medium">
                  {{ getStatusLabel(document.status) }}
                </span>
                <span :class="getPriorityClass(document.priority)" class="px-3 py-1 rounded-full text-xs font-medium">
                  {{ document.priority }}
                </span>
              </div>
            </div>
          </div>
        </div>

        <!-- Document Details -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
          <!-- Main Content -->
          <div class="lg:col-span-2 space-y-6">
            <!-- Document Information -->
            <div class="bg-white shadow rounded-lg">
              <div class="px-6 py-4 border-b border-gray-200">
                <h2 class="text-lg font-medium text-gray-900">Document Information</h2>
              </div>
              <div class="px-6 py-4 space-y-4">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                  <div>
                    <label class="block text-sm font-medium text-gray-700">Type</label>
                    <p class="mt-1 text-sm text-gray-900">{{ document.document_type?.name || 'N/A' }}</p>
                  </div>
                  <div>
                    <label class="block text-sm font-medium text-gray-700">Security Level</label>
                    <p class="mt-1 text-sm text-gray-900">{{ document.security_level || 'N/A' }}</p>
                  </div>
                  <div>
                    <label class="block text-sm font-medium text-gray-700">Created By</label>
                    <p class="mt-1 text-sm text-gray-900">{{ document.creator?.name || 'N/A' }}</p>
                  </div>
                  <div>
                    <label class="block text-sm font-medium text-gray-700">Department</label>
                    <p class="mt-1 text-sm text-gray-900">{{ document.department?.name || 'N/A' }}</p>
                  </div>
                  <div>
                    <label class="block text-sm font-medium text-gray-700">Created</label>
                    <p class="mt-1 text-sm text-gray-900">{{ formatDate(document.created_at) }}</p>
                  </div>
                  <div>
                    <label class="block text-sm font-medium text-gray-700">Last Updated</label>
                    <p class="mt-1 text-sm text-gray-900">{{ formatDate(document.updated_at) }}</p>
                  </div>
                </div>
                
                <div v-if="document.description">
                  <label class="block text-sm font-medium text-gray-700">Description</label>
                  <p class="mt-1 text-sm text-gray-900">{{ document.description }}</p>
                </div>
              </div>
            </div>

            <!-- Document Trail -->
            <div v-if="documentLogs.length" class="bg-white shadow rounded-lg">
              <div class="px-6 py-4 border-b border-gray-200">
                <h2 class="text-lg font-medium text-gray-900">Document Trail</h2>
              </div>
              <div class="px-6 py-4">
                <div class="flow-root">
                  <ul class="-mb-8">
                    <li v-for="(log, index) in documentLogs" :key="log.id" class="relative pb-8">
                      <div v-if="index !== documentLogs.length - 1" class="absolute top-4 left-4 -ml-px h-full w-0.5 bg-gray-200"></div>
                      <div class="relative flex space-x-3">
                        <div>
                          <span :class="getLogIconClass(log.action)" class="h-8 w-8 rounded-full flex items-center justify-center ring-8 ring-white">
                            <svg class="h-4 w-4 text-white" fill="currentColor" viewBox="0 0 20 20">
                              <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                            </svg>
                          </span>
                        </div>
                        <div class="min-w-0 flex-1 pt-1.5 flex justify-between space-x-4">
                          <div>
                            <p class="text-sm text-gray-900">{{ log.description || getActionLabel(log.action) }}</p>
                            <p v-if="log.user" class="text-xs text-gray-500">by {{ log.user.name }}</p>
                          </div>
                          <div class="text-right text-xs whitespace-nowrap text-gray-500">
                            {{ formatDate(log.created_at) }}
                          </div>
                        </div>
                      </div>
                    </li>
                  </ul>
                </div>
              </div>
            </div>
          </div>

          <!-- Sidebar -->
          <div class="space-y-6">
            <!-- QR Code -->
            <div class="bg-white shadow rounded-lg">
              <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-medium text-gray-900">QR Code</h3>
              </div>
              <div class="px-6 py-4 text-center">
                <img
                  v-if="document.qr_code_path"
                  :src="`/storage/${document.qr_code_path}`"
                  alt="QR Code"
                  class="mx-auto w-32 h-32 border border-gray-200 rounded"
                />
                <div v-if="document.qr_code" class="mt-3 text-xs text-gray-600">
                  <p>Scans: {{ document.qr_code.scan_count || 0 }}</p>
                  <p v-if="document.qr_code.last_scanned_at">
                    Last: {{ formatDate(document.qr_code.last_scanned_at) }}
                  </p>
                </div>
              </div>
            </div>

            <!-- Digital Signatures -->
            <div v-if="document.signatures && document.signatures.length" class="bg-white shadow rounded-lg">
              <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-medium text-gray-900">Digital Signatures</h3>
              </div>
              <div class="px-6 py-4 space-y-3">
                <div v-for="signature in document.signatures" :key="signature.id" class="flex items-center space-x-3">
                  <div class="flex-shrink-0">
                    <div class="h-8 w-8 bg-green-100 rounded-full flex items-center justify-center">
                      <svg class="h-4 w-4 text-green-600" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M6.267 3.455a3.066 3.066 0 001.745-.723 3.066 3.066 0 013.976 0 3.066 3.066 0 001.745.723 3.066 3.066 0 012.812 2.812c.051.643.304 1.254.723 1.745a3.066 3.066 0 010 3.976 3.066 3.066 0 00-.723 1.745 3.066 3.066 0 01-2.812 2.812 3.066 3.066 0 00-1.745.723 3.066 3.066 0 01-3.976 0 3.066 3.066 0 00-1.745-.723 3.066 3.066 0 01-2.812-2.812 3.066 3.066 0 00-.723-1.745 3.066 3.066 0 010-3.976 3.066 3.066 0 00.723-1.745 3.066 3.066 0 012.812-2.812zm7.44 5.252a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                      </svg>
                    </div>
                  </div>
                  <div class="flex-1 min-w-0">
                    <p class="text-sm font-medium text-gray-900">{{ signature.signer_name }}</p>
                    <p class="text-xs text-gray-500">{{ formatDate(signature.created_at) }}</p>
                  </div>
                </div>
              </div>
            </div>

            <!-- Actions -->
            <div class="bg-white shadow rounded-lg">
              <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-medium text-gray-900">Actions</h3>
              </div>
              <div class="px-6 py-4 space-y-2">
                <button
                  @click="printDocument"
                  class="w-full px-4 py-2 bg-gray-600 text-white rounded-md hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-gray-500"
                >
                  🖨️ Print Document
                </button>
                <button
                  v-if="document?.qr_code_path"
                  @click="printQR"
                  class="w-full px-4 py-2 bg-gray-700 text-white rounded-md hover:bg-gray-800 focus:outline-none focus:ring-2 focus:ring-gray-600"
                >
                  🖨️ Print QR
                </button>
                <button
                  @click="copyShareLink"
                  class="w-full px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500"
                >
                  📋 Copy Share Link
                </button>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, computed } from 'vue'
import { useRoute } from 'vue-router'
import axios from 'axios'
import { useToast } from 'vue-toastification'

const route = useRoute()
const toast = useToast()

const loading = ref(true)
const error = ref(null)
const document = ref(null)
const documentLogs = ref([])

const documentNumber = computed(() => route.params.document_number)

onMounted(async () => {
  await fetchDocument()
})

const fetchDocument = async () => {
  try {
    loading.value = true
    error.value = null

    // First try to get document by document_number
    const response = await axios.get('/api/documents', {
      params: { document_number: documentNumber.value }
    })

    if (response.data.data && response.data.data.length > 0) {
      document.value = response.data.data[0]
      
      // Fetch document logs/history
      try {
        const historyResponse = await axios.get(`/api/documents/${document.value.id}/history`)
        documentLogs.value = historyResponse.data.data || []
      } catch (historyError) {
        console.warn('Could not fetch document history:', historyError)
        documentLogs.value = []
      }
    } else {
      error.value = 'Document not found or you do not have permission to view it.'
    }
  } catch (err) {
    console.error('Error fetching document:', err)
    if (err.response?.status === 404) {
      error.value = 'Document not found.'
    } else if (err.response?.status === 403) {
      error.value = 'You do not have permission to view this document.'
    } else {
      error.value = 'An error occurred while loading the document.'
    }
  } finally {
    loading.value = false
  }
}

const getStatusClass = (status) => {
  const classes = {
    'draft': 'bg-gray-100 text-gray-800',
    'under_review': 'bg-yellow-100 text-yellow-800',
    'approved': 'bg-green-100 text-green-800',
    'rejected': 'bg-red-100 text-red-800',
    'completed': 'bg-blue-100 text-blue-800',
    'on_hold': 'bg-orange-100 text-orange-800'
  }
  return classes[status] || 'bg-gray-100 text-gray-800'
}

const getStatusLabel = (status) => {
  const labels = {
    'draft': 'Draft',
    'under_review': 'Under Review',
    'approved': 'Approved',
    'rejected': 'Rejected',
    'completed': 'Completed',
    'on_hold': 'On Hold'
  }
  return labels[status] || status
}

const getPriorityClass = (priority) => {
  const classes = {
    'low': 'bg-green-100 text-green-800',
    'medium': 'bg-yellow-100 text-yellow-800',
    'high': 'bg-red-100 text-red-800',
    'urgent': 'bg-purple-100 text-purple-800'
  }
  return classes[priority] || 'bg-gray-100 text-gray-800'
}

const getLogIconClass = (action) => {
  const classes = {
    'created': 'bg-blue-500',
    'forwarded': 'bg-yellow-500',
    'received': 'bg-green-500',
    'approved': 'bg-green-600',
    'rejected': 'bg-red-500',
    'signed': 'bg-purple-500',
    'qr_scanned': 'bg-indigo-500'
  }
  return classes[action] || 'bg-gray-500'
}

const getActionLabel = (action) => {
  const labels = {
    'created': 'Document created',
    'forwarded': 'Document forwarded',
    'received': 'Document received',
    'approved': 'Document approved',
    'rejected': 'Document rejected',
    'signed': 'Document signed',
    'qr_scanned': 'QR code scanned'
  }
  return labels[action] || action
}

const formatDate = (dateString) => {
  if (!dateString) return 'N/A'
  return new Date(dateString).toLocaleString()
}

const printDocument = () => {
  window.print()
}

const printQR = () => {
  const path = document.value?.qr_code?.qr_image_url || (document.value?.qr_code_path ? `/storage/${document.value.qr_code_path}` : null)
  if (!path) {
    toast.error('QR code not available for this document')
    return
  }
  const w = window.open('', '_blank')
  if (!w) return
  w.document.write(`<!DOCTYPE html><html><head><meta charset="utf-8"><title>QR</title><style>@page{margin:0}html,body{margin:0;padding:0}img{display:block;margin:0 auto;max-width:100vw;max-height:100vh}</style></head><body><img src="${path}" alt="QR" onload="window.print();window.onafterprint=window.close();"/></body></html>`)
  w.document.close()
}

const copyShareLink = async () => {
  try {
    const shareUrl = `${window.location.origin}/documents/code/${document.value.document_number}`
    await navigator.clipboard.writeText(shareUrl)
    toast.success('📋 Share link copied to clipboard!')
  } catch (error) {
    console.error('Copy error:', error)
    toast.error('Failed to copy link')
  }
}
</script>