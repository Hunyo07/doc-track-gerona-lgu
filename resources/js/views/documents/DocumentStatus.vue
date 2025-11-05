<template>
  <div class="min-h-screen bg-gray-50 py-8">
    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
      <!-- Header -->
      <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900">Document Status & Workflow Tracking</h1>
        <p class="mt-2 text-gray-600">Track document movement and status updates in real-time</p>
      </div>

      <!-- QR Scanner Section -->
      <div class="bg-white shadow-lg rounded-lg mb-8">
        <div class="px-6 py-4 border-b border-gray-200">
          <h2 class="text-xl font-semibold text-gray-900">QR Code Scanner</h2>
        </div>
        <div class="p-6">
          <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
              <button
                @click="toggleScanner"
                class="w-full px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500"
              >
                <span v-if="!showScanner">📱 Scan QR Code</span>
                <span v-else>❌ Close Scanner</span>
              </button>
              
              <!-- QR Scanner Component -->
              <div v-if="showScanner" class="mt-4">
                <div class="border-2 border-gray-300 rounded-lg p-4">
                  <div id="qr-scanner" class="w-full h-64 bg-gray-100 rounded flex items-center justify-center">
                    <p class="text-gray-500">QR Scanner would be initialized here</p>
                    <!-- In a real implementation, you would use vue-qr-reader here -->
                  </div>
                </div>
              </div>
            </div>
            
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-2">Or Enter Document Number</label>
              <div class="flex">
                <input
                  v-model="searchDocumentNumber"
                  type="text"
                  placeholder="Enter document number"
                  class="flex-1 px-3 py-2 border border-gray-300 rounded-l-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                />
                <button
                  @click="searchDocument"
                  class="px-4 py-2 bg-green-600 text-white rounded-r-md hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500"
                >
                  Search
                </button>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Document Information -->
      <div v-if="currentDocument" class="bg-white shadow-lg rounded-lg mb-8">
        <div class="px-6 py-4 border-b border-gray-200">
          <div class="flex justify-between items-center">
            <h2 class="text-xl font-semibold text-gray-900">Document Information</h2>
            <span
              class="px-3 py-1 text-sm font-semibold rounded-full"
              :class="getStatusColor(currentDocument.status)"
            >
              {{ currentDocument.status.toUpperCase() }}
            </span>
          </div>
        </div>
        <div class="p-6">
          <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div>
              <h3 class="text-lg font-medium text-gray-900 mb-4">Basic Information</h3>
              <dl class="space-y-2">
                <div>
                  <dt class="text-sm font-medium text-gray-500">Document Number</dt>
                  <dd class="text-sm text-gray-900">{{ currentDocument.document_number }}</dd>
                </div>
                <div>
                  <dt class="text-sm font-medium text-gray-500">Title</dt>
                  <dd class="text-sm text-gray-900">{{ currentDocument.file_name || currentDocument.title }}</dd>
                </div>
                <div>
                  <dt class="text-sm font-medium text-gray-500">Type</dt>
                  <dd class="text-sm text-gray-900">{{ currentDocument.type }}</dd>
                </div>
                <div>
                  <dt class="text-sm font-medium text-gray-500">Priority</dt>
                  <dd class="text-sm text-gray-900">
                    <span
                      class="px-2 py-1 text-xs font-semibold rounded-full"
                      :class="getPriorityColor(currentDocument.priority)"
                    >
                      {{ currentDocument.priority }}
                    </span>
                  </dd>
                </div>
              </dl>
            </div>
            
            <div>
              <h3 class="text-lg font-medium text-gray-900 mb-4">Tracking Information</h3>
              <dl class="space-y-2">
                <div>
                  <dt class="text-sm font-medium text-gray-500">Current Office</dt>
                  <dd class="text-sm text-gray-900">{{ currentDocument.current_department?.name }}</dd>
                </div>
                <div>
                  <dt class="text-sm font-medium text-gray-500">Sender</dt>
                  <dd class="text-sm text-gray-900">{{ currentDocument.sender?.name || currentDocument.creator?.name }}</dd>
                </div>
                <div>
                  <dt class="text-sm font-medium text-gray-500">Created Date</dt>
                  <dd class="text-sm text-gray-900">{{ formatDate(currentDocument.created_at) }}</dd>
                </div>
                <div v-if="currentDocument.deadline">
                  <dt class="text-sm font-medium text-gray-500">Deadline</dt>
                  <dd class="text-sm text-gray-900">{{ formatDate(currentDocument.deadline) }}</dd>
                </div>
              </dl>
            </div>
            
            <div>
              <h3 class="text-lg font-medium text-gray-900 mb-4">Actions</h3>
              <div class="space-y-2">
                <button
                  @click="showForwardModal = true"
                  class="w-full px-3 py-2 bg-blue-600 text-white text-sm rounded-md hover:bg-blue-700"
                >
                  Forward Document
                </button>
                <button
                  @click="receiveDocument"
                  class="w-full px-3 py-2 bg-green-600 text-white text-sm rounded-md hover:bg-green-700"
                >
                  Receive Document
                </button>
                <button
                  @click="showRejectModal = true"
                  class="w-full px-3 py-2 bg-red-600 text-white text-sm rounded-md hover:bg-red-700"
                >
                  Reject Document
                </button>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Timeline View -->
      <div v-if="currentDocument" class="bg-white shadow-lg rounded-lg">
        <div class="px-6 py-4 border-b border-gray-200">
          <h2 class="text-xl font-semibold text-gray-900">Document Timeline</h2>
        </div>
        <div class="p-6">
          <div class="flow-root">
            <ul class="-mb-8">
              <li v-for="(item, index) in timeline" :key="index">
                <div class="relative pb-8">
                  <span
                    v-if="index !== timeline.length - 1"
                    class="absolute top-4 left-4 -ml-px h-full w-0.5 bg-gray-200"
                    aria-hidden="true"
                  ></span>
                  <div class="relative flex space-x-3">
                    <div>
                      <span
                        class="h-8 w-8 rounded-full flex items-center justify-center ring-8 ring-white"
                        :class="getTimelineIconColor(item.action)"
                      >
                        <component :is="getTimelineIcon(item.action)" class="w-5 h-5 text-white" />
                      </span>
                    </div>
                    <div class="min-w-0 flex-1 pt-1.5 flex justify-between space-x-4">
                      <div>
                        <p class="text-sm text-gray-500">
                          {{ item.description }}
                          <span v-if="item.user" class="font-medium text-gray-900">
                            by {{ item.user.name }}
                          </span>
                        </p>
                        <p v-if="item.remarks" class="mt-1 text-sm text-gray-600 italic">
                          "{{ item.remarks }}"
                        </p>
                        <div v-if="item.from_office || item.to_office" class="mt-1 text-xs text-gray-500">
                          <span v-if="item.from_office">From: {{ item.from_office.name }}</span>
                          <span v-if="item.from_office && item.to_office"> → </span>
                          <span v-if="item.to_office">To: {{ item.to_office.name }}</span>
                        </div>
                      </div>
                      <div class="text-right text-sm whitespace-nowrap text-gray-500">
                        <time :datetime="item.created_at">{{ formatDate(item.created_at) }}</time>
                      </div>
                    </div>
                  </div>
                </div>
              </li>
            </ul>
          </div>
        </div>
      </div>

      <!-- Forward Modal -->
      <div
        v-if="showForwardModal"
        class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50"
        @click="showForwardModal = false"
      >
        <div
          class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white"
          @click.stop
        >
          <h3 class="text-lg font-bold text-gray-900 mb-4">Forward Document</h3>
          <form @submit.prevent="forwardDocument">
            <div class="mb-4">
              <label class="block text-sm font-medium text-gray-700 mb-2">Select Office</label>
              <select
                v-model="forwardForm.to_office_id"
                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                required
              >
                <option value="">Select Department</option>
                <option v-for="dept in departments" :key="dept.id" :value="dept.id">
                  {{ dept.name }}
                </option>
              </select>
            </div>
            <div class="mb-4">
              <label class="block text-sm font-medium text-gray-700 mb-2">Remarks</label>
              <textarea
                v-model="forwardForm.remarks"
                rows="3"
                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                placeholder="Add remarks (optional)"
              ></textarea>
            </div>
            <div class="flex justify-end space-x-2">
              <button
                type="button"
                @click="showForwardModal = false"
                class="px-4 py-2 text-gray-600 border border-gray-300 rounded-md hover:bg-gray-50"
              >
                Cancel
              </button>
              <button
                type="submit"
                class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700"
              >
                Forward
              </button>
            </div>
          </form>
        </div>
      </div>

      <!-- Reject Modal -->
      <div
        v-if="showRejectModal"
        class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50"
        @click="showRejectModal = false"
      >
        <div
          class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white"
          @click.stop
        >
          <h3 class="text-lg font-bold text-gray-900 mb-4">Reject Document</h3>
          <form @submit.prevent="rejectDocument">
            <div class="mb-4">
              <label class="block text-sm font-medium text-gray-700 mb-2">Reason for Rejection *</label>
              <textarea
                v-model="rejectForm.reason"
                rows="4"
                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-red-500"
                placeholder="Please provide a reason for rejection"
                required
              ></textarea>
            </div>
            <div class="flex justify-end space-x-2">
              <button
                type="button"
                @click="showRejectModal = false"
                class="px-4 py-2 text-gray-600 border border-gray-300 rounded-md hover:bg-gray-50"
              >
                Cancel
              </button>
              <button
                type="submit"
                class="px-4 py-2 bg-red-600 text-white rounded-md hover:bg-red-700"
              >
                Reject
              </button>
            </div>
          </form>
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

// Icons (you would import these from @heroicons/vue in a real app)
const DocumentIcon = 'div'
const ArrowRightIcon = 'div'
const CheckIcon = 'div'
const XMarkIcon = 'div'
const ClockIcon = 'div'

// Reactive data
const currentDocument = ref(null)
const timeline = ref([])
const departments = ref([])
const showScanner = ref(false)
const searchDocumentNumber = ref('')

// Modals
const showForwardModal = ref(false)
const showRejectModal = ref(false)

// Forms
const forwardForm = ref({
  to_office_id: '',
  remarks: ''
})

const rejectForm = ref({
  reason: ''
})

// Methods
const toggleScanner = () => {
  showScanner.value = !showScanner.value
  // In a real implementation, you would initialize/destroy the QR scanner here
}

const searchDocument = async () => {
  if (!searchDocumentNumber.value.trim()) {
    toast.error('Please enter a document number')
    return
  }

  try {
    const response = await axios.get(`/api/documents/share/${searchDocumentNumber.value}`)
    currentDocument.value = response.data.document
    await fetchDocumentHistory()
    toast.success('Document found!')
  } catch (error) {
    console.error('Search error:', error)
    toast.error('Document not found')
    currentDocument.value = null
    timeline.value = []
  }
}

const fetchDocumentHistory = async () => {
  if (!currentDocument.value) return

  try {
    const response = await axios.get(`/api/documents/${currentDocument.value.id}/history`)
    
    // Combine routes and logs into a single timeline
    const routes = response.data.routes.map(route => ({
      ...route,
      type: 'route',
      action: route.status,
      description: `Document ${route.status}`,
      user: route.user,
      from_office: route.from_office,
      to_office: route.to_office
    }))
    
    const logs = response.data.logs.map(log => ({
      ...log,
      type: 'log',
      action: log.action,
      description: log.description,
      user: log.user,
      remarks: log.metadata?.remarks || log.metadata?.reason
    }))
    
    // Sort by created_at descending
    timeline.value = [...routes, ...logs].sort((a, b) => 
      new Date(b.created_at) - new Date(a.created_at)
    )
    
  } catch (error) {
    console.error('Fetch history error:', error)
    toast.error('Failed to fetch document history')
  }
}

const forwardDocument = async () => {
  try {
    const response = await axios.post(`/api/documents/${currentDocument.value.id}/route`, forwardForm.value)
    
    toast.success('Document forwarded successfully!')
    showForwardModal.value = false
    forwardForm.value = { to_office_id: '', remarks: '' }
    
    // Refresh document and timeline
    await searchDocument()
    
  } catch (error) {
    console.error('Forward error:', error)
    toast.error(error.response?.data?.message || 'Failed to forward document')
  }
}

const receiveDocument = async () => {
  try {
    await axios.post(`/api/documents/${currentDocument.value.id}/receive`, {
      notes: 'Document received'
    })
    
    toast.success('Document received successfully!')
    
    // Refresh document and timeline
    await searchDocument()
    
  } catch (error) {
    console.error('Receive error:', error)
    toast.error(error.response?.data?.message || 'Failed to receive document')
  }
}

const rejectDocument = async () => {
  try {
    await axios.post(`/api/documents/${currentDocument.value.id}/reject`, rejectForm.value)
    
    toast.success('Document rejected successfully!')
    showRejectModal.value = false
    rejectForm.value = { reason: '' }
    
    // Refresh document and timeline
    await searchDocument()
    
  } catch (error) {
    console.error('Reject error:', error)
    toast.error(error.response?.data?.message || 'Failed to reject document')
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

// Utility functions
const getStatusColor = (status) => {
  const colors = {
    draft: 'bg-gray-100 text-gray-800',
    submitted: 'bg-blue-100 text-blue-800',
    received: 'bg-yellow-100 text-yellow-800',
    under_review: 'bg-purple-100 text-purple-800',
    approved: 'bg-green-100 text-green-800',
    rejected: 'bg-red-100 text-red-800',
    completed: 'bg-indigo-100 text-indigo-800'
  }
  return colors[status] || 'bg-gray-100 text-gray-800'
}

const getPriorityColor = (priority) => {
  const colors = {
    low: 'bg-green-100 text-green-800',
    medium: 'bg-yellow-100 text-yellow-800',
    high: 'bg-orange-100 text-orange-800',
    urgent: 'bg-red-100 text-red-800'
  }
  return colors[priority] || 'bg-gray-100 text-gray-800'
}

const getTimelineIconColor = (action) => {
  const colors = {
    created: 'bg-blue-500',
    routed: 'bg-purple-500',
    sent: 'bg-purple-500',
    received: 'bg-green-500',
    rejected: 'bg-red-500',
    approved: 'bg-green-600',
    qr_generated: 'bg-indigo-500',
    qr_scanned: 'bg-indigo-400',
    shared_access: 'bg-gray-500'
  }
  return colors[action] || 'bg-gray-400'
}

const getTimelineIcon = (action) => {
  const icons = {
    created: DocumentIcon,
    routed: ArrowRightIcon,
    sent: ArrowRightIcon,
    received: CheckIcon,
    rejected: XMarkIcon,
    approved: CheckIcon,
    qr_generated: DocumentIcon,
    qr_scanned: DocumentIcon,
    shared_access: ClockIcon
  }
  return icons[action] || ClockIcon
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
  await fetchDepartments()
})
</script>