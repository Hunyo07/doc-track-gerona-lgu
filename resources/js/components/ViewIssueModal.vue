<template>
  <div class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
    <div class="relative top-20 mx-auto p-5 border w-11/12 md:w-3/4 lg:w-2/3 shadow-lg rounded-md bg-white">
      <div class="mt-3">
        <!-- Header -->
        <div class="flex items-center justify-between mb-6">
          <div class="flex items-center space-x-3">
            <h3 class="text-lg font-medium text-gray-900">Issue Details</h3>
            <span :class="getStatusBadgeClass(issue.status)" class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium">
              {{ formatStatus(issue.status) }}
            </span>
            <span :class="getPriorityBadgeClass(issue.priority)" class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium">
              {{ formatPriority(issue.priority) }}
            </span>
          </div>
          <button
            @click="$emit('close')"
            class="text-gray-400 hover:text-gray-600"
          >
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
            </svg>
          </button>
        </div>

        <!-- Issue Information -->
        <div class="space-y-6">
          <!-- Title and Type -->
          <div>
            <div class="flex items-center justify-between mb-2">
              <h4 class="text-xl font-semibold text-gray-900">{{ issue.title }}</h4>
              <span :class="getTypeBadgeClass(issue.type)" class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium">
                {{ formatType(issue.type) }}
              </span>
            </div>
          </div>

          <!-- Description -->
          <div>
            <h5 class="text-sm font-medium text-gray-700 mb-2">Description</h5>
            <div class="bg-gray-50 rounded-md p-4">
              <p class="text-gray-900 whitespace-pre-wrap">{{ issue.description }}</p>
            </div>
          </div>

          <!-- Reporter Information -->
          <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
              <h5 class="text-sm font-medium text-gray-700 mb-3">Reporter Information</h5>
              <div class="bg-gray-50 rounded-md p-4 space-y-2">
                <div class="flex items-center">
                  <svg class="w-4 h-4 text-gray-400 mr-2" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd"></path>
                  </svg>
                  <span class="text-sm text-gray-900">{{ issue.reporter_name }}</span>
                </div>
                <div class="flex items-center">
                  <svg class="w-4 h-4 text-gray-400 mr-2" fill="currentColor" viewBox="0 0 20 20">
                    <path d="M2.003 5.884L10 9.882l7.997-3.998A2 2 0 0016 4H4a2 2 0 00-1.997 1.884z"></path>
                    <path d="M18 8.118l-8 4-8-4V14a2 2 0 002 2h12a2 2 0 002-2V8.118z"></path>
                  </svg>
                  <a :href="`mailto:${issue.reporter_email}`" class="text-sm text-blue-600 hover:text-blue-800">
                    {{ issue.reporter_email }}
                  </a>
                </div>
                <div v-if="issue.reporter_phone" class="flex items-center">
                  <svg class="w-4 h-4 text-gray-400 mr-2" fill="currentColor" viewBox="0 0 20 20">
                    <path d="M2 3a1 1 0 011-1h2.153a1 1 0 01.986.836l.74 4.435a1 1 0 01-.54 1.06l-1.548.773a11.037 11.037 0 006.105 6.105l.774-1.548a1 1 0 011.059-.54l4.435.74a1 1 0 01.836.986V17a1 1 0 01-1 1h-2C7.82 18 2 12.18 2 5V3z"></path>
                  </svg>
                  <span class="text-sm text-gray-900">{{ issue.reporter_phone }}</span>
                </div>
              </div>
            </div>

            <div>
              <h5 class="text-sm font-medium text-gray-700 mb-3">Issue Details</h5>
              <div class="bg-gray-50 rounded-md p-4 space-y-2">
                <div class="flex justify-between">
                  <span class="text-sm text-gray-500">Created:</span>
                  <span class="text-sm text-gray-900">{{ formatDate(issue.created_at) }}</span>
                </div>
                <div class="flex justify-between">
                  <span class="text-sm text-gray-500">Last Updated:</span>
                  <span class="text-sm text-gray-900">{{ formatDate(issue.updated_at) }}</span>
                </div>
                <div v-if="issue.resolved_at" class="flex justify-between">
                  <span class="text-sm text-gray-500">Resolved:</span>
                  <span class="text-sm text-gray-900">{{ formatDate(issue.resolved_at) }}</span>
                </div>
                <div v-if="issue.resolver" class="flex justify-between">
                  <span class="text-sm text-gray-500">Resolved By:</span>
                  <span class="text-sm text-gray-900">{{ issue.resolver.name }}</span>
                </div>
              </div>
            </div>
          </div>

          <!-- Attachments -->
          <div v-if="issue.attachments && issue.attachments.length > 0">
            <h5 class="text-sm font-medium text-gray-700 mb-3">Attachments</h5>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3">
              <div
                v-for="(attachment, index) in issue.attachments"
                :key="index"
                class="flex items-center p-3 bg-gray-50 rounded-md hover:bg-gray-100"
              >
                <svg class="w-5 h-5 text-gray-400 mr-3" fill="currentColor" viewBox="0 0 20 20">
                  <path fill-rule="evenodd" d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4zm2 6a1 1 0 011-1h6a1 1 0 110 2H7a1 1 0 01-1-1zm1 3a1 1 0 100 2h6a1 1 0 100-2H7z" clip-rule="evenodd"></path>
                </svg>
                <div class="flex-1 min-w-0">
                  <p class="text-sm text-gray-900 truncate">{{ attachment.original_name || attachment.name }}</p>
                  <p class="text-xs text-gray-500">{{ formatFileSize(attachment.size) }}</p>
                </div>
                <a
                  :href="attachment.url || attachment.path"
                  target="_blank"
                  class="ml-2 text-blue-600 hover:text-blue-800"
                >
                  <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                  </svg>
                </a>
              </div>
            </div>
          </div>

          <!-- Admin Notes -->
          <div v-if="issue.admin_notes">
            <h5 class="text-sm font-medium text-gray-700 mb-2">Admin Notes</h5>
            <div class="bg-blue-50 border border-blue-200 rounded-md p-4">
              <p class="text-blue-900 whitespace-pre-wrap">{{ issue.admin_notes }}</p>
            </div>
          </div>

          <!-- Status Update Form (for admins) -->
          <div v-if="canUpdateStatus" class="border-t border-gray-200 pt-6">
            <h5 class="text-sm font-medium text-gray-700 mb-4">Update Issue Status</h5>
            <form @submit.prevent="updateStatus" class="space-y-4">
              <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                  <label class="block text-sm font-medium text-gray-700 mb-2">Status</label>
                  <select
                    v-model="statusForm.status"
                    class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                  >
                    <option value="open">Open</option>
                    <option value="in_progress">In Progress</option>
                    <option value="resolved">Resolved</option>
                    <option value="closed">Closed</option>
                  </select>
                </div>
                
                <div>
                  <label class="block text-sm font-medium text-gray-700 mb-2">Priority</label>
                  <select
                    v-model="statusForm.priority"
                    class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                  >
                    <option value="low">Low</option>
                    <option value="medium">Medium</option>
                    <option value="high">High</option>
                    <option value="urgent">Urgent</option>
                  </select>
                </div>
              </div>
              
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Admin Notes</label>
                <textarea
                  v-model="statusForm.admin_notes"
                  rows="3"
                  class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                  placeholder="Add notes about the status update..."
                ></textarea>
              </div>
              
              <div class="flex justify-end">
                <button
                  type="submit"
                  :disabled="updating"
                  class="px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 disabled:opacity-50"
                >
                  <span v-if="updating">Updating...</span>
                  <span v-else>Update Status</span>
                </button>
              </div>
            </form>
          </div>

          <!-- Error Message -->
          <div v-if="updateError" class="p-4 bg-red-50 border border-red-200 rounded-md">
            <p class="text-sm text-red-600">{{ updateError }}</p>
          </div>
        </div>

        <!-- Action Buttons -->
        <div class="flex items-center justify-end space-x-3 pt-6 border-t border-gray-200 mt-6">
          <button
            @click="$emit('close')"
            class="px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500"
          >
            Close
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, reactive, computed, onMounted } from 'vue'
import { useIssueStore } from '../stores/issues'
import { useAuthStore } from '../stores/auth'

const props = defineProps({
  issue: {
    type: Object,
    required: true
  }
})

const emit = defineEmits(['close', 'updated'])

const issueStore = useIssueStore()
const authStore = useAuthStore()

// Status update form
const statusForm = reactive({
  status: props.issue.status,
  priority: props.issue.priority,
  admin_notes: props.issue.admin_notes || ''
})

// Form state
const updating = ref(false)
const updateError = ref('')

// Computed
const canUpdateStatus = computed(() => {
  // Allow admins or users with appropriate permissions to update status
  return authStore.user?.role === 'admin' || authStore.user?.permissions?.includes('manage_issues')
})

// Methods
const updateStatus = async () => {
  updating.value = true
  updateError.value = ''
  
  try {
    const updatedIssue = await issueStore.updateIssue(props.issue.id, statusForm)
    emit('updated', updatedIssue)
  } catch (error) {
    updateError.value = error.message || 'Failed to update issue status'
  } finally {
    updating.value = false
  }
}

const getStatusBadgeClass = (status) => {
  const classes = {
    open: 'bg-red-100 text-red-800',
    in_progress: 'bg-yellow-100 text-yellow-800',
    resolved: 'bg-green-100 text-green-800',
    closed: 'bg-gray-100 text-gray-800'
  }
  return classes[status] || 'bg-gray-100 text-gray-800'
}

const getPriorityBadgeClass = (priority) => {
  const classes = {
    low: 'bg-gray-100 text-gray-800',
    medium: 'bg-blue-100 text-blue-800',
    high: 'bg-orange-100 text-orange-800',
    urgent: 'bg-red-100 text-red-800'
  }
  return classes[priority] || 'bg-gray-100 text-gray-800'
}

const getTypeBadgeClass = (type) => {
  const classes = {
    bug: 'bg-red-100 text-red-800',
    feature_request: 'bg-blue-100 text-blue-800',
    improvement: 'bg-green-100 text-green-800',
    question: 'bg-purple-100 text-purple-800',
    other: 'bg-gray-100 text-gray-800'
  }
  return classes[type] || 'bg-gray-100 text-gray-800'
}

const formatStatus = (status) => {
  return status.replace('_', ' ').replace(/\b\w/g, l => l.toUpperCase())
}

const formatPriority = (priority) => {
  return priority.charAt(0).toUpperCase() + priority.slice(1)
}

const formatType = (type) => {
  return type.replace('_', ' ').replace(/\b\w/g, l => l.toUpperCase())
}

const formatDate = (date) => {
  return new Date(date).toLocaleDateString('en-US', {
    year: 'numeric',
    month: 'long',
    day: 'numeric',
    hour: '2-digit',
    minute: '2-digit'
  })
}

const formatFileSize = (bytes) => {
  if (!bytes) return '0 Bytes'
  const k = 1024
  const sizes = ['Bytes', 'KB', 'MB', 'GB']
  const i = Math.floor(Math.log(bytes) / Math.log(k))
  return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i]
}
</script>