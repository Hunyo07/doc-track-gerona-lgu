<template>
  <div class="min-h-screen bg-gray-50 py-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
      <!-- Header -->
      <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900">Document Repository</h1>
        <p class="mt-2 text-gray-600">Upload and manage digital documents with QR code generation</p>
      </div>

      <!-- Upload Form -->
      <div class="bg-white shadow-lg rounded-lg mb-8">
        <div class="px-6 py-4 border-b border-gray-200">
          <h2 class="text-xl font-semibold text-gray-900">Upload New Document</h2>
        </div>
        <form @submit.prevent="uploadDocument" class="p-6 space-y-6">
          <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- File Name -->
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-2">File Name</label>
              <input
                v-model="form.file_name"
                type="text"
                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                placeholder="Enter file name"
              />
            </div>

            <!-- Document Type -->
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-2">Type</label>
              <select
                v-model="form.type"
                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                required
              >
                <option value="">Select Type</option>
                <option value="PR">Purchase Request (PR)</option>
                <option value="PO">Purchase Order (PO)</option>
                <option value="DV">Disbursement Voucher (DV)</option>
                <option value="other">Other</option>
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

            <!-- Priority -->
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-2">Priority</label>
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

            <!-- Department -->
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-2">Department</label>
              <select
                v-model="form.department_id"
                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                required
              >
                <option value="">Select Department</option>
                <option v-for="dept in departments" :key="dept.id" :value="dept.id">
                  {{ dept.name }}
                </option>
              </select>
            </div>

            <!-- Document Type ID -->
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-2">Document Category</label>
              <select
                v-model="form.document_type_id"
                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                required
              >
                <option value="">Select Category</option>
                <option v-for="type in documentTypes" :key="type.id" :value="type.id">
                  {{ type.name }}
                </option>
              </select>
            </div>
          </div>

          <!-- Description -->
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Description</label>
            <textarea
              v-model="form.description"
              rows="3"
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
              placeholder="Enter tags separated by commas"
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

          <!-- File Upload -->
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">File Upload</label>
            <div
              @drop="handleDrop"
              @dragover.prevent
              @dragenter.prevent
              class="border-2 border-dashed border-gray-300 rounded-lg p-6 text-center hover:border-blue-400 transition-colors"
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
                <svg class="mx-auto h-12 w-12 text-gray-400" stroke="currentColor" fill="none" viewBox="0 0 48 48">
                  <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                </svg>
                <div class="mt-4">
                  <button
                    type="button"
                    @click="$refs.fileInput.click()"
                    class="text-blue-600 hover:text-blue-500 font-medium"
                  >
                    Click to upload
                  </button>
                  <span class="text-gray-500"> or drag and drop</span>
                </div>
                <p class="text-sm text-gray-500 mt-2">Excel, PDF, Word files up to 10MB</p>
              </div>
              <div v-else class="text-green-600">
                <svg class="mx-auto h-8 w-8" fill="currentColor" viewBox="0 0 20 20">
                  <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                </svg>
                <p class="mt-2 font-medium">{{ selectedFile.name }}</p>
                <p class="text-sm text-gray-500">{{ formatFileSize(selectedFile.size) }}</p>
                <button
                  type="button"
                  @click="removeFile"
                  class="mt-2 text-red-600 hover:text-red-500 text-sm"
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
              :disabled="uploading"
              class="px-6 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 disabled:opacity-50 disabled:cursor-not-allowed"
            >
              <span v-if="uploading" class="flex items-center">
                <svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                  <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                  <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                Uploading...
              </span>
              <span v-else>Upload Document</span>
            </button>
          </div>
        </form>
      </div>

      <!-- Search and Filters -->
      <div class="bg-white shadow-lg rounded-lg mb-8">
        <div class="p-6">
          <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div>
              <input
                v-model="searchQuery"
                type="text"
                placeholder="Search documents..."
                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
              />
            </div>
            <div>
              <select
                v-model="filterOffice"
                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
              >
                <option value="">All Departments</option>
                <option v-for="dept in departments" :key="dept.id" :value="dept.id">
                  {{ dept.name }}
                </option>
              </select>
            </div>
            <div>
              <select
                v-model="filterType"
                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
              >
                <option value="">All Types</option>
                <option value="PR">PR</option>
                <option value="PO">PO</option>
                <option value="DV">DV</option>
                <option value="other">Other</option>
              </select>
            </div>
            <div>
              <select
                v-model="filterStatus"
                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
              >
                <option value="">All Status</option>
                <option value="draft">Draft</option>
                <option value="submitted">Submitted</option>
                <option value="received">Received</option>
                <option value="approved">Approved</option>
                <option value="rejected">Rejected</option>
              </select>
            </div>
          </div>
        </div>
      </div>

      <!-- Documents Table -->
      <div class="bg-white shadow-lg rounded-lg">
        <div class="px-6 py-4 border-b border-gray-200">
          <h2 class="text-xl font-semibold text-gray-900">Document Repository</h2>
        </div>
        <div class="overflow-x-auto">
          <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
              <tr>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                  Document Number
                </th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                  File Name
                </th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                  Office
                </th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                  Status
                </th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                  QR Code
                </th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                  Actions
                </th>
              </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
              <tr v-for="document in filteredDocuments" :key="document.id" class="hover:bg-gray-50">
                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                  {{ document.document_number }}
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                  {{ document.file_name || document.title }}
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                  {{ document.department?.name }}
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                  <span
                    class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full"
                    :class="getStatusColor(document.status)"
                  >
                    {{ document.status }}
                  </span>
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                  <button
                    v-if="document.qr_code_path"
                    @click="showQRCode(document)"
                    class="text-blue-600 hover:text-blue-900"
                  >
                    View QR
                  </button>
                  <span v-else class="text-gray-400">No QR</span>
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium space-x-2">
                  <button
                    @click="viewDetails(document)"
                    class="text-blue-600 hover:text-blue-900"
                  >
                    View Details
                  </button>
                  <button
                    v-if="document.file_path"
                    @click="downloadFile(document)"
                    class="text-green-600 hover:text-green-900"
                  >
                    Download
                  </button>
                  <button
                    @click="deleteDocument(document)"
                    class="text-red-600 hover:text-red-900"
                  >
                    Delete
                  </button>
                </td>
              </tr>
            </tbody>
          </table>
          <div v-if="!filteredDocuments.length" class="text-center py-8 text-gray-500">
            No documents found
          </div>
        </div>
      </div>

      <!-- Details Modal -->
      <div
        v-if="showDetailsModal"
        class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50"
        @click="closeDetailsModal"
      >
        <div
          class="relative top-20 mx-auto p-5 border w-11/12 md:w-3/4 lg:w-1/2 shadow-lg rounded-md bg-white"
          @click.stop
        >
          <div class="flex justify-between items-center mb-4">
            <h3 class="text-lg font-bold text-gray-900">Document Details</h3>
            <button
              @click="closeDetailsModal"
              class="text-gray-400 hover:text-gray-600"
            >
              <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
              </svg>
            </button>
          </div>
          
          <div v-if="selectedDocument" class="space-y-4">
            <div class="grid grid-cols-2 gap-4">
              <div>
                <label class="block text-sm font-medium text-gray-700">Document Number</label>
                <p class="mt-1 text-sm text-gray-900">{{ selectedDocument.document_number }}</p>
              </div>
              <div>
                <label class="block text-sm font-medium text-gray-700">File Name</label>
                <p class="mt-1 text-sm text-gray-900">{{ selectedDocument.file_name || selectedDocument.title }}</p>
              </div>
              <div>
                <label class="block text-sm font-medium text-gray-700">Type</label>
                <p class="mt-1 text-sm text-gray-900">{{ selectedDocument.type }}</p>
              </div>
              <div>
                <label class="block text-sm font-medium text-gray-700">Status</label>
                <span
                  class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full"
                  :class="getStatusColor(selectedDocument.status)"
                >
                  {{ selectedDocument.status }}
                </span>
              </div>
              <div>
                <label class="block text-sm font-medium text-gray-700">Sender</label>
                <p class="mt-1 text-sm text-gray-900">{{ selectedDocument.sender?.name || selectedDocument.creator?.name }}</p>
              </div>
              <div>
                <label class="block text-sm font-medium text-gray-700">Created Date</label>
                <p class="mt-1 text-sm text-gray-900">{{ formatDate(selectedDocument.created_at) }}</p>
              </div>
            </div>
            
            <div v-if="selectedDocument.description">
              <label class="block text-sm font-medium text-gray-700">Description</label>
              <p class="mt-1 text-sm text-gray-900">{{ selectedDocument.description }}</p>
            </div>
            
            <div v-if="selectedDocument.qr_code_path" class="text-center">
              <label class="block text-sm font-medium text-gray-700 mb-2">QR Code</label>
              <img
                :src="`/storage/${selectedDocument.qr_code_path}`"
                alt="QR Code"
                class="mx-auto w-32 h-32"
              />
              <div class="mt-2">
                <button
                  @click="copyShareLink(selectedDocument)"
                  class="text-blue-600 hover:text-blue-900 text-sm"
                >
                  Copy Share Link
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
import { ref, computed, onMounted } from 'vue'
import { useToast } from 'vue-toastification'
import axios from 'axios'

const toast = useToast()

// Reactive data
const form = ref({
  file_name: '',
  description: '',
  tags: [],
  type: '',
  security_level: 'internal',
  priority: '',
  department_id: '',
  document_type_id: ''
})

const tagsInput = ref('')
const selectedFile = ref(null)
const uploading = ref(false)
const isDragging = ref(false)

// Table data
const documents = ref([])
const departments = ref([])
const documentTypes = ref([])
const loading = ref(false)

// Filters
const searchQuery = ref('')
const filterOffice = ref('')
const filterType = ref('')
const filterStatus = ref('')

// Modal
const showDetailsModal = ref(false)
const selectedDocument = ref(null)

// Computed
const filteredDocuments = computed(() => {
  let filtered = documents.value

  if (searchQuery.value) {
    const query = searchQuery.value.toLowerCase()
    filtered = filtered.filter(doc =>
      doc.document_number.toLowerCase().includes(query) ||
      (doc.file_name && doc.file_name.toLowerCase().includes(query)) ||
      (doc.title && doc.title.toLowerCase().includes(query)) ||
      (doc.description && doc.description.toLowerCase().includes(query))
    )
  }

  if (filterOffice.value) {
    filtered = filtered.filter(doc => doc.department_id == filterOffice.value)
  }

  if (filterType.value) {
    filtered = filtered.filter(doc => doc.type === filterType.value)
  }

  if (filterStatus.value) {
    filtered = filtered.filter(doc => doc.status === filterStatus.value)
  }

  return filtered
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
    if (!form.value.file_name) {
      form.value.file_name = files[0].name
    }
  }
}

const handleFileSelect = (e) => {
  const files = e.target.files
  if (files.length > 0) {
    selectedFile.value = files[0]
    if (!form.value.file_name) {
      form.value.file_name = files[0].name
    }
  }
}

const removeFile = () => {
  selectedFile.value = null
  if (form.value.file_name === selectedFile.value?.name) {
    form.value.file_name = ''
  }
}

const formatFileSize = (bytes) => {
  if (bytes === 0) return '0 Bytes'
  const k = 1024
  const sizes = ['Bytes', 'KB', 'MB', 'GB']
  const i = Math.floor(Math.log(bytes) / Math.log(k))
  return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i]
}

const uploadDocument = async () => {
  try {
    uploading.value = true
    
    const formData = new FormData()
    Object.keys(form.value).forEach(key => {
      if (form.value[key] !== null && form.value[key] !== '') {
        if (key === 'tags') {
          formData.append(key, JSON.stringify(form.value[key]))
        } else {
          formData.append(key, form.value[key])
        }
      }
    })
    
    if (selectedFile.value) {
      formData.append('file', selectedFile.value)
    }

    const response = await axios.post('/api/documents', formData, {
      headers: {
        'Content-Type': 'multipart/form-data'
      }
    })

    toast.success(response.data.message || '✅ Document registered successfully with QR code.')
    
    // Reset form
    form.value = {
      file_name: '',
      description: '',
      tags: [],
      type: '',
      security_level: 'internal',
      priority: '',
      department_id: '',
      document_type_id: ''
    }
    tagsInput.value = ''
    selectedFile.value = null
    
    // Refresh documents list
    await fetchDocuments()
    
  } catch (error) {
    console.error('Upload error:', error)
    toast.error(error.response?.data?.message || 'Failed to upload document')
  } finally {
    uploading.value = false
  }
}

const fetchDocuments = async () => {
  try {
    loading.value = true
    const response = await axios.get('/api/documents')
    documents.value = response.data.data || response.data
  } catch (error) {
    console.error('Fetch documents error:', error)
    toast.error('Failed to fetch documents')
  } finally {
    loading.value = false
  }
}

const fetchDepartments = async () => {
  try {
    const response = await axios.get('/api/departments')
    departments.value = response.data.data || response.data
  } catch (error) {
    console.error('Fetch departments error:', error)
  }
}

const fetchDocumentTypes = async () => {
  try {
    const response = await axios.get('/api/document-types')
    const data = response.data.data || response.data
    documentTypes.value = Array.isArray(data) ? data.filter(t => t?.is_active) : []
  } catch (error) {
    console.error('Fetch document types error:', error)
  }
}

const viewDetails = (document) => {
  selectedDocument.value = document
  showDetailsModal.value = true
}

const closeDetailsModal = () => {
  showDetailsModal.value = false
  selectedDocument.value = null
}

const showQRCode = (document) => {
  viewDetails(document)
}

const downloadFile = (document) => {
  if (document.file_path) {
    window.open(`/storage/${document.file_path}`, '_blank')
  }
}

const deleteDocument = async (document) => {
  if (confirm('Are you sure you want to delete this document?')) {
    try {
      await axios.delete(`/api/documents/${document.id}`)
      toast.success('Document deleted successfully')
      await fetchDocuments()
    } catch (error) {
      console.error('Delete error:', error)
      toast.error('Failed to delete document')
    }
  }
}

const copyShareLink = async (document) => {
  const shareUrl = `${window.location.origin}/api/documents/share/${document.document_number}`
  try {
    await navigator.clipboard.writeText(shareUrl)
    toast.success('Share link copied to clipboard!')
  } catch (error) {
    console.error('Copy error:', error)
    toast.error('Failed to copy link')
  }
}

const getStatusColor = (status) => {
  const colors = {
    draft: 'bg-gray-100 text-gray-800',
    submitted: 'bg-blue-100 text-blue-800',
    received: 'bg-yellow-100 text-yellow-800',
    approved: 'bg-green-100 text-green-800',
    rejected: 'bg-red-100 text-red-800',
    completed: 'bg-purple-100 text-purple-800'
  }
  return colors[status] || 'bg-gray-100 text-gray-800'
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
  await Promise.all([
    fetchDocuments(),
    fetchDepartments(),
    fetchDocumentTypes()
  ])
})
</script>