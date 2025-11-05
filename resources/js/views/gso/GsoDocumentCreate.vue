<template>
  <div class="min-h-screen bg-gray-50 py-8">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
      <!-- Header -->
      <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900">GSO Document Creation</h1>
        <p class="mt-2 text-gray-600">Create purchase-related documents with automatic QR generation</p>
      </div>

      <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
        <!-- Document Creation Form -->
        <div class="bg-white shadow-lg rounded-lg">
          <div class="px-6 py-4 border-b border-gray-200">
            <h2 class="text-xl font-semibold text-gray-900">Create GSO Document</h2>
          </div>
          <form @submit.prevent="createDocument" class="p-6 space-y-6">
            <!-- Document Type -->
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-2">Document Type *</label>
              <select
                v-model="form.type"
                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                required
              >
                <option value="">Select Document Type</option>
                <option value="PR">Purchase Request (PR)</option>
                <option value="PO">Purchase Order (PO)</option>
                <option value="DV">Disbursement Voucher (DV)</option>
              </select>
            </div>

            <!-- File Name -->
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-2">Document Title/Name *</label>
              <input
                v-model="form.file_name"
                type="text"
                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                placeholder="Enter document title"
                required
              />
            </div>

            <!-- Description -->
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-2">Description</label>
              <textarea
                v-model="form.description"
                rows="4"
                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                placeholder="Enter document description"
              ></textarea>
            </div>

            <!-- Tags -->
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-2">Tags</label>
              <input
                v-model="tagsInput"
                type="text"
                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                placeholder="Enter tags separated by commas (e.g., procurement, supplies, urgent)"
                @input="updateTags"
              />
              <div v-if="form.tags && form.tags.length" class="mt-2 flex flex-wrap gap-2">
                <span
                  v-for="tag in form.tags"
                  :key="tag"
                  class="px-2 py-1 bg-blue-100 text-blue-800 text-sm rounded-full"
                >
                  {{ tag }}
                </span>
              </div>
            </div>

            <!-- Priority -->
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-2">Priority *</label>
              <select
                v-model="form.priority"
                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                required
              >
                <option value="">Select Priority</option>
                <option value="low">Low</option>
                <option value="medium">Medium</option>
                <option value="high">High</option>
                <option value="urgent">Urgent</option>
              </select>
            </div>

            <!-- Security Level -->
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-2">Security Level</label>
              <select
                v-model="form.security_level"
                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
              >
                <option value="public">Public</option>
                <option value="internal">Internal</option>
                <option value="confidential">Confidential</option>
                <option value="secret">Secret</option>
              </select>
            </div>

            <!-- File Upload -->
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-2">Attach File (Optional)</label>
              <div
                @drop="handleDrop"
                @dragover.prevent
                @dragenter.prevent
                class="border-2 border-dashed border-gray-300 rounded-lg p-4 text-center hover:border-blue-400 transition-colors"
                :class="{ 'border-blue-400 bg-blue-50': isDragging }"
              >
                <input
                  ref="fileInput"
                  type="file"
                  @change="handleFileSelect"
                  accept=".xlsx,.xls,.pdf,.doc,.docx"
                  class="hidden"
                />
                <div v-if="!selectedFile">
                  <svg class="mx-auto h-8 w-8 text-gray-400" stroke="currentColor" fill="none" viewBox="0 0 48 48">
                    <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                  </svg>
                  <div class="mt-2">
                    <button
                      type="button"
                      @click="$refs.fileInput.click()"
                      class="text-blue-600 hover:text-blue-500 font-medium text-sm"
                    >
                      Click to upload
                    </button>
                    <span class="text-gray-500 text-sm"> or drag and drop</span>
                  </div>
                  <p class="text-xs text-gray-500 mt-1">Excel, PDF, Word files up to 10MB</p>
                </div>
                <div v-else class="text-green-600">
                  <svg class="mx-auto h-6 w-6" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                  </svg>
                  <p class="mt-1 font-medium text-sm">{{ selectedFile.name }}</p>
                  <p class="text-xs text-gray-500">{{ formatFileSize(selectedFile.size) }}</p>
                  <button
                    type="button"
                    @click="removeFile"
                    class="mt-1 text-red-600 hover:text-red-500 text-xs"
                  >
                    Remove file
                  </button>
                </div>
              </div>
            </div>

            <!-- Submit Button -->
            <div class="flex justify-end">
              <button
                type="submit"
                :disabled="creating"
                class="px-6 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 disabled:opacity-50 disabled:cursor-not-allowed"
              >
                <span v-if="creating" class="flex items-center">
                  <svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                  </svg>
                  Creating...
                </span>
                <span v-else>Generate QR Code</span>
              </button>
            </div>
          </form>
        </div>

        <!-- QR Display & Sharing -->
        <div class="space-y-6">
          <!-- QR Code Display -->
          <div v-if="createdDocument" class="bg-white shadow-lg rounded-lg">
            <div class="px-6 py-4 border-b border-gray-200">
              <h2 class="text-xl font-semibold text-gray-900">QR Code & Sharing</h2>
            </div>
            <div class="p-6 text-center">
              <!-- Document Info -->
              <div class="mb-6 p-4 bg-green-50 rounded-lg">
                <div class="flex items-center justify-center mb-2">
                  <svg class="w-6 h-6 text-green-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                  </svg>
                  <span class="text-green-800 font-medium">Document Created Successfully!</span>
                </div>
                <p class="text-sm text-green-700">
                  Document Number: <strong>{{ createdDocument.document_number }}</strong>
                </p>
                <p class="text-sm text-green-700">
                  Type: <strong>{{ createdDocument.type }}</strong>
                </p>
              </div>

              <!-- QR Code -->
              <div v-if="createdDocument.qr_code_path" class="mb-6">
                <img
                  :src="`/storage/${createdDocument.qr_code_path}`"
                  alt="QR Code"
                  class="mx-auto w-48 h-48 border border-gray-200 rounded-lg"
                />
                <p class="mt-2 text-sm text-gray-600">Scan this QR code to access the document</p>
              </div>

              <!-- Action Buttons -->
              <div class="space-y-3">
                <button
                  @click="printQR"
                  class="w-full px-4 py-2 bg-gray-600 text-white rounded-md hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-gray-500"
                >
                  🖨️ Print QR Code
                </button>
                
                <button
                  @click="copyShareLink"
                  class="w-full px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500"
                >
                  📋 Copy Share Link
                </button>
                
                <!-- <button
                  v-if="createdDocument.qr_code_path"
                  @click="downloadQR"
                  class="w-full px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500"
                >
                  💾 Download QR Image
                </button> -->
                <button
                  v-if="createdDocument.qr_code_path"
                  @click="downloadQRJpeg"
                  class="w-full px-4 py-2 bg-green-700 text-white rounded-md hover:bg-green-800 focus:outline-none focus:ring-2 focus:ring-green-600"
                >
                  💾 Download QR as JPEG
                </button>
                
                <button
                  @click="regenerateQR"
                  class="w-full px-4 py-2 bg-yellow-600 text-white rounded-md hover:bg-yellow-700 focus:outline-none focus:ring-2 focus:ring-yellow-500"
                >
                  🔄 Re-generate QR Code
                </button>
              </div>

              <!-- Share Link Display -->
              <div class="mt-6 p-4 bg-gray-50 rounded-lg">
                <label class="block text-sm font-medium text-gray-700 mb-2">Shareable Link:</label>
                <div class="flex">
                  <input
                    :value="shareUrl"
                    readonly
                    class="flex-1 px-3 py-2 text-sm border border-gray-300 rounded-l-md bg-gray-50 text-gray-600"
                  />
                  <button
                    @click="copyShareLink"
                    class="px-3 py-2 bg-blue-600 text-white text-sm rounded-r-md hover:bg-blue-700"
                  >
                    Copy
                  </button>
                </div>
              </div>
            </div>
          </div>

          <!-- Instructions -->
          <div class="bg-white shadow-lg rounded-lg">
            <div class="px-6 py-4 border-b border-gray-200">
              <h2 class="text-xl font-semibold text-gray-900">Instructions</h2>
            </div>
            <div class="p-6">
              <div class="space-y-4 text-sm text-gray-600">
                <div class="flex items-start">
                  <span class="flex-shrink-0 w-6 h-6 bg-blue-100 text-blue-600 rounded-full flex items-center justify-center text-xs font-bold mr-3 mt-0.5">1</span>
                  <p>Fill out the document creation form with the required information.</p>
                </div>
                <div class="flex items-start">
                  <span class="flex-shrink-0 w-6 h-6 bg-blue-100 text-blue-600 rounded-full flex items-center justify-center text-xs font-bold mr-3 mt-0.5">2</span>
                  <p>Click "Generate QR Code" to create the document and automatically generate its QR code.</p>
                </div>
                <div class="flex items-start">
                  <span class="flex-shrink-0 w-6 h-6 bg-blue-100 text-blue-600 rounded-full flex items-center justify-center text-xs font-bold mr-3 mt-0.5">3</span>
                  <p>Use the QR code to share the document or print it for physical distribution.</p>
                </div>
                <div class="flex items-start">
                  <span class="flex-shrink-0 w-6 h-6 bg-blue-100 text-blue-600 rounded-full flex items-center justify-center text-xs font-bold mr-3 mt-0.5">4</span>
                  <p>The shareable link allows anyone to view the document details in read-only mode.</p>
                </div>
              </div>
            </div>
          </div>

          <!-- Recent Documents -->
          <div class="bg-white shadow-lg rounded-lg">
            <div class="px-6 py-4 border-b border-gray-200">
              <h2 class="text-xl font-semibold text-gray-900">Recent GSO Documents</h2>
            </div>
            <div class="p-6">
              <div v-if="recentDocuments.length" class="space-y-3">
                <div
                  v-for="doc in recentDocuments"
                  :key="doc.id"
                  class="flex items-center justify-between p-3 bg-gray-50 rounded-lg"
                >
                  <div>
                    <p class="font-medium text-sm">{{ doc.document_number }}</p>
                    <p class="text-xs text-gray-600">{{ doc.file_name || doc.title }}</p>
                    <p class="text-xs text-gray-500">{{ formatDate(doc.created_at) }}</p>
                  </div>
                  <div class="flex space-x-2">
                    <button
                      @click="viewDocument(doc)"
                      class="text-blue-600 hover:text-blue-900 text-xs"
                    >
                      View
                    </button>
                    <button
                      v-if="doc.qr_code_path"
                      @click="showQRForDocument(doc)"
                      class="text-green-600 hover:text-green-900 text-xs"
                    >
                      QR
                    </button>
                  </div>
                </div>
              </div>
              <div v-else class="text-center text-gray-500 text-sm">
                No recent documents
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import { useToast } from 'vue-toastification'
import axios from 'axios'

const toast = useToast()

// Reactive data
const form = ref({
  type: '',
  file_name: '',
  description: '',
  tags: [],
  priority: '',
  security_level: 'internal'
})

const tagsInput = ref('')
const selectedFile = ref(null)
const creating = ref(false)
const isDragging = ref(false)

// Document data
const createdDocument = ref(null)
const recentDocuments = ref([])
const gsoUser = ref(null)

// Computed
const shareUrl = computed(() => {
  if (createdDocument.value) {
    return `${window.location.origin}/api/documents/share/${createdDocument.value.document_number}`
  }
  return ''
})

// Methods
const updateTags = () => {
  form.value.tags = tagsInput.value
    .split(',')
    .map(tag => tag.trim())
    .filter(tag => tag.length > 0)
}

const handleDrop = (e) => {
  e.preventDefault()
  isDragging.value = false
  const files = e.dataTransfer.files
  if (files.length > 0) {
    selectedFile.value = files[0]
  }
}

const handleFileSelect = (e) => {
  const files = e.target.files
  if (files.length > 0) {
    selectedFile.value = files[0]
  }
}

const removeFile = () => {
  selectedFile.value = null
}

const formatFileSize = (bytes) => {
  if (bytes === 0) return '0 Bytes'
  const k = 1024
  const sizes = ['Bytes', 'KB', 'MB', 'GB']
  const i = Math.floor(Math.log(bytes) / Math.log(k))
  return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i]
}

const createDocument = async () => {
  try {
    creating.value = true
    
    // Get user info to auto-assign GSO department
    const userResponse = await axios.get('/api/user')
    const user = userResponse.data
    
    const formData = new FormData()
    
    // Add form data
    Object.keys(form.value).forEach(key => {
      if (form.value[key] !== null && form.value[key] !== '') {
        if (key === 'tags') {
          formData.append(key, JSON.stringify(form.value[key]))
        } else {
          formData.append(key, form.value[key])
        }
      }
    })
    
    // Auto-assign to user's department (GSO)
    formData.append('department_id', user.department_id)
    formData.append('document_type_id', 1) // Default document type, adjust as needed
    
    if (selectedFile.value) {
      formData.append('file', selectedFile.value)
    }

    const response = await axios.post('/api/gso/documents', formData, {
      headers: {
        'Content-Type': 'multipart/form-data'
      }
    })

    createdDocument.value = response.data.document
    toast.success('✅ Document created successfully with QR code!')
    
    // Reset form
    form.value = {
      type: '',
      file_name: '',
      description: '',
      tags: [],
      priority: '',
      security_level: 'internal'
    }
    tagsInput.value = ''
    selectedFile.value = null
    
    // Refresh recent documents
    await fetchRecentDocuments()
    
  } catch (error) {
    console.error('Create document error:', error)
    toast.error(error.response?.data?.message || 'Failed to create document')
  } finally {
    creating.value = false
  }
}

const printQR = () => {
  if (createdDocument.value?.qr_code_path) {
    const w = window.open('', '_blank')
    if (!w) return
    w.document.write(`
      <html>
        <head>
          <title>QR Code</title>
          <style>
            @page { margin: 0; }
            html, body { height: 100%; margin: 0; }
            body { display: flex; align-items: center; justify-content: center; }
            img { width: 1in; height: 1in; display: block; }
          </style>
        </head>
        <body>
          <img src="/storage/${createdDocument.value.qr_code_path}" alt="QR Code" onload="window.print();window.onafterprint=window.close();" />
        </body>
      </html>
    `)
    w.document.close()
  }
}

const copyShareLink = async () => {
  try {
    await navigator.clipboard.writeText(shareUrl.value)
    toast.success('📋 Share link copied to clipboard!')
  } catch (error) {
    console.error('Copy error:', error)
    toast.error('Failed to copy link')
  }
}

const downloadQR = () => {
  if (createdDocument.value?.qr_code_path) {
    const link = document.createElement('a')
    link.href = `/storage/${createdDocument.value.qr_code_path}`
    link.download = `QR_${createdDocument.value.document_number}.svg`
    document.body.appendChild(link)
    link.click()
    document.body.removeChild(link)
    toast.success('💾 QR code downloaded!')
  }
}

const downloadQRJpeg = async () => {
  if (!createdDocument.value?.qr_code_path) return
  try {
    const svgUrl = `/storage/${createdDocument.value.qr_code_path}`
    const res = await fetch(svgUrl, { cache: 'no-cache' })
    const svgText = await res.text()
    const blob = new Blob([svgText], { type: 'image/svg+xml;charset=utf-8' })
    const objUrl = URL.createObjectURL(blob)
    const img = new Image()
    img.onload = () => {
      const size = 1024
      const canvas = document.createElement('canvas')
      canvas.width = size
      canvas.height = size
      const ctx = canvas.getContext('2d')
      ctx.fillStyle = '#ffffff'
      ctx.fillRect(0, 0, size, size)
      ctx.drawImage(img, 0, 0, size, size)
      URL.revokeObjectURL(objUrl)
      const dataUrl = canvas.toDataURL('image/jpeg', 0.92)
      const a = document.createElement('a')
      a.href = dataUrl
      a.download = `QR_${createdDocument.value.document_number}.jpg`
      document.body.appendChild(a)
      a.click()
      document.body.removeChild(a)
      toast.success('💾 QR JPEG downloaded!')
    }
    img.onerror = () => {
      URL.revokeObjectURL(objUrl)
      toast.error('Failed to convert QR to JPEG')
    }
    img.src = objUrl
  } catch (e) {
    toast.error('Failed to download as JPEG')
  }
}

const regenerateQR = async () => {
  if (!createdDocument.value) return
  
  try {
    const response = await axios.post(`/api/documents/${createdDocument.value.id}/qr`)
    
    // Update the document with new QR path
    createdDocument.value.qr_code_path = response.data.qr_image_url.replace('/storage/', '')
    
    toast.success('🔄 QR code regenerated successfully!')
  } catch (error) {
    console.error('Regenerate QR error:', error)
    toast.error('Failed to regenerate QR code')
  }
}

const fetchRecentDocuments = async () => {
  try {
    const response = await axios.get('/api/gso/documents/recent')
    recentDocuments.value = response.data
  } catch (error) {
    console.error('Fetch recent documents error:', error)
  }
}

const viewDocument = (document) => {
  // Navigate to document details or open in modal
  window.open(`/api/documents/share/${document.document_number}`, '_blank')
}

const showQRForDocument = (document) => {
  createdDocument.value = document
}

const formatDate = (dateString) => {
  return new Date(dateString).toLocaleDateString('en-US', {
    year: 'numeric',
    month: 'short',
    day: 'numeric',
    hour: '2-digit',
    minute: '2-digit'
  })
}

// Lifecycle
onMounted(async () => {
  await fetchRecentDocuments()
})
</script>