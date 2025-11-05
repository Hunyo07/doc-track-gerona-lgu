<template>
  <div class="space-y-6">
    <div class="bg-white overflow-hidden shadow rounded-lg">
      <div class="px-4 py-5 sm:p-6">
        <div class="flex items-center justify-between mb-6">
          <h1 class="text-2xl font-bold text-gray-900">Dashboard</h1>
          <div class="flex items-center gap-3">
            <button @click="fetchStats" class="px-3 py-2 rounded-md bg-indigo-600 text-white text-sm hover:bg-indigo-700">Refresh</button>
            <span v-if="lastUpdated" class="text-xs text-gray-500">Updated {{ timeAgo(lastUpdated) }}</span>
          </div>
        </div>

        <div v-if="error" class="mb-4 rounded-md bg-red-50 p-4 text-sm text-red-700">{{ error }}</div>

        <div v-if="loading" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-6 mb-8">
          <div v-for="n in 5" :key="n" class="p-6 rounded-lg animate-pulse">
            <div class="h-8 w-8 rounded-full bg-gray-200"></div>
            <div class="mt-4 h-4 w-24 bg-gray-200 rounded"></div>
            <div class="mt-2 h-6 w-16 bg-gray-300 rounded"></div>
          </div>
        </div>

        <div v-else class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-6 mb-8">
          <router-link :to="{ name: 'documents', query: {} }" class="bg-blue-50 p-6 rounded-lg hover:bg-blue-100 transition-colors">
            <div class="flex items-center">
              <div class="flex-shrink-0">
                <svg class="h-8 w-8 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                </svg>
              </div>
              <div class="ml-4">
                <p class="text-sm font-medium text-blue-600">Total Documents</p>
                <p class="text-2xl font-semibold text-blue-900">{{ stats.total_documents || 0 }}</p>
              </div>
            </div>
          </router-link>

          <router-link :to="{ name: 'documents', query: { mine: 1 } }" class="bg-green-50 p-6 rounded-lg hover:bg-green-100 transition-colors">
            <div class="flex items-center">
              <div class="flex-shrink-0">
                <svg class="h-8 w-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                </svg>
              </div>
              <div class="ml-4">
                <p class="text-sm font-medium text-green-600">My Documents</p>
                <p class="text-2xl font-semibold text-green-900">{{ stats.my_documents || 0 }}</p>
              </div>
            </div>
          </router-link>

          <router-link :to="{ name: 'documents', query: { status: 'under_review' } }" class="bg-yellow-50 p-6 rounded-lg hover:bg-yellow-100 transition-colors">
            <div class="flex items-center">
              <div class="flex-shrink-0">
                <svg class="h-8 w-8 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
              </div>
              <div class="ml-4">
                <p class="text-sm font-medium text-yellow-600">Pending Review</p>
                <p class="text-2xl font-semibold text-yellow-900">{{ stats.pending_review || 0 }}</p>
              </div>
            </div>
          </router-link>

          <router-link :to="{ name: 'documents', query: { overdue: 1 } }" class="bg-red-50 p-6 rounded-lg hover:bg-red-100 transition-colors">
            <div class="flex items-center">
              <div class="flex-shrink-0">
                <svg class="h-8 w-8 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                </svg>
              </div>
              <div class="ml-4">
                <p class="text-sm font-medium text-red-600">Overdue</p>
                <p class="text-2xl font-semibold text-red-900">{{ stats.overdue || 0 }}</p>
              </div>
            </div>
          </router-link>

          <router-link :to="{ name: 'documents', query: { assigned_to_me: 1 } }" class="bg-indigo-50 p-6 rounded-lg hover:bg-indigo-100 transition-colors">
            <div class="flex items-center">
              <div class="flex-shrink-0">
                <svg class="h-8 w-8 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                </svg>
              </div>
              <div class="ml-4">
                <p class="text-sm font-medium text-indigo-600">Assigned to Me</p>
                <p class="text-2xl font-semibold text-indigo-900">{{ stats.assigned_to_me || 0 }}</p>
              </div>
            </div>
          </router-link>
        </div>
        
        <div v-if="!loading && stats.recent_activity && stats.recent_activity.length > 0">
          <h2 class="text-lg font-medium text-gray-900 mb-4">Recent Activity</h2>
          <div class="bg-gray-50 rounded-lg p-4">
            <div class="space-y-3">
              <div 
                v-for="activity in stats.recent_activity" 
                :key="activity.id"
                class="flex items-center space-x-3"
              >
                <div class="flex-shrink-0">
                  <div class="w-8 h-8 bg-indigo-100 rounded-full flex items-center justify-center">
                    <svg class="w-4 h-4 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                  </div>
                </div>
                <div class="flex-1 min-w-0">
                  <p class="text-sm text-gray-900">
                    <span class="font-medium">{{ activity.user.name }}</span>
                    {{ getActionText(activity.action) }}
                    <router-link :to="{ name: 'document-detail', params: { id: activity.document.id } }" class="font-medium text-indigo-700 hover:text-indigo-900 hover:underline">
                      {{ activity.document.title }}
                    </router-link>
                  </p>
                  <p class="text-xs text-gray-500">{{ timeAgo(activity.action_date) }}</p>
                </div>
              </div>
            </div>
          </div>
        </div>

        <div v-if="!loading && (!stats.recent_activity || stats.recent_activity.length === 0)" class="rounded-lg border border-gray-200 p-6 text-sm text-gray-600">No recent activity yet.</div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, onUnmounted } from 'vue'
import axios from 'axios'
import { useToast } from 'vue-toastification'

const toast = useToast()
const stats = ref({})
const loading = ref(false)
const error = ref('')
const lastUpdated = ref(null)
let intervalId = null

const fetchStats = async () => {
  loading.value = true
  error.value = ''
  try {
    const response = await axios.get('/api/dashboard/stats')
    stats.value = response.data
    lastUpdated.value = new Date().toISOString()
  } catch (e) {
    error.value = 'Failed to load dashboard stats.'
    toast.error('Error fetching dashboard stats')
  } finally {
    loading.value = false
  }
}

const getActionText = (action) => {
  const actionTexts = {
    created: 'created',
    updated: 'updated',
    submitted: 'submitted',
    reviewed: 'reviewed',
    approved: 'approved',
    rejected: 'rejected',
    assigned: 'assigned',
    completed: 'completed'
  }
  return actionTexts[action] || action
}

const timeAgo = (date) => {
  const now = new Date()
  const then = new Date(date)
  const diffMs = now - then
  const seconds = Math.floor(diffMs / 1000)
  const minutes = Math.floor(seconds / 60)
  const hours = Math.floor(minutes / 60)
  const days = Math.floor(hours / 24)
  if (days > 0) return `${days}d ago`
  if (hours > 0) return `${hours}h ago`
  if (minutes > 0) return `${minutes}m ago`
  return 'just now'
}

onMounted(() => {
  fetchStats()
  intervalId = setInterval(fetchStats, 60000)
})

onUnmounted(() => {
  if (intervalId) clearInterval(intervalId)
})
</script>
