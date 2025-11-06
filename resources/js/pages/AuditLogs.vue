<template>
  <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-6">
      <div>
        <h1 class="text-2xl font-bold text-gray-900">Audit Logs</h1>
        <p class="mt-1 text-sm text-gray-500">System activity and security events</p>
      </div>
    </div>

    <!-- Tabs -->
    <div class="mb-6">
      <nav class="-mb-px flex space-x-6" aria-label="Tabs">
        <button
          :class="tabClass('all')"
          @click="setTab('all')"
        >
          All
        </button>
        <button
          :class="tabClass('security')"
          @click="setTab('security')"
        >
          Security
        </button>
        <button
          :class="tabClass('authentication')"
          @click="setTab('authentication')"
        >
          Authentication
        </button>
      </nav>
    </div>

    <!-- Filters -->
    <div class="bg-white shadow rounded-lg p-4 mb-6">
      <div class="grid grid-cols-1 md:grid-cols-5 gap-4">
        <div class="md:col-span-2">
          <label class="block text-sm font-medium text-gray-700">Search</label>
          <input v-model="filters.search" type="text" placeholder="Description, action, etc." class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500" />
        </div>
        <div>
          <label class="block text-sm font-medium text-gray-700">From</label>
          <input v-model="filters.date_from" type="date" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500" />
        </div>
        <div>
          <label class="block text-sm font-medium text-gray-700">To</label>
          <input v-model="filters.date_to" type="date" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500" />
        </div>
        <div class="flex items-end space-x-2">
          <button @click="applyFilters" class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">Apply</button>
          <button @click="clearFilters" class="px-4 py-2 bg-gray-100 text-gray-700 rounded-md hover:bg-gray-200">Clear</button>
        </div>
      </div>
    </div>

    <!-- Stats -->
    <div class="mb-6">
      <div v-if="statsLoading" class="grid grid-cols-1 md:grid-cols-4 gap-4">
        <div class="bg-white shadow rounded-lg p-4 animate-pulse" v-for="n in 4" :key="n">
          <div class="h-4 bg-gray-200 rounded w-24"></div>
          <div class="mt-3 h-8 bg-gray-200 rounded w-32"></div>
        </div>
      </div>
      <div v-else-if="statsError" class="bg-yellow-50 border border-yellow-200 text-yellow-700 rounded-lg p-4">
        <p class="text-sm">Failed to load stats. {{ statsError }}</p>
      </div>
      <div v-else class="grid grid-cols-1 md:grid-cols-4 gap-4">
        <div class="bg-white shadow rounded-lg p-4">
          <p class="text-sm text-gray-500">Total Logs</p>
          <p class="mt-1 text-2xl font-semibold text-gray-900">{{ stats.total_logs || 0 }}</p>
        </div>
        <div class="bg-white shadow rounded-lg p-4">
          <p class="text-sm text-gray-500">Document Actions</p>
          <p class="mt-1 text-2xl font-semibold text-gray-900">{{ stats.document_actions || 0 }}</p>
        </div>
        <div class="bg-white shadow rounded-lg p-4">
          <p class="text-sm text-gray-500">Auth Events</p>
          <p class="mt-1 text-2xl font-semibold text-gray-900">{{ stats.auth_events || 0 }}</p>
        </div>
        <div class="bg-white shadow rounded-lg p-4">
          <p class="text-sm text-gray-500">Security Events</p>
          <p class="mt-1 text-2xl font-semibold text-gray-900">{{ stats.security_events || 0 }}</p>
        </div>
      </div>
    </div>

    <!-- Table -->
    <div class="bg-white shadow rounded-lg">
      <div class="px-6 py-4 border-b border-gray-200 flex items-center justify-between">
        <h2 class="text-lg font-medium text-gray-900">Logs</h2>
        <div class="text-sm text-gray-500" v-if="pagination">
          Showing {{ showingFrom }} to {{ showingTo }} of {{ pagination.total }} results
        </div>
      </div>

      <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
          <thead class="bg-gray-50">
            <tr>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">User</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Action</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Description</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Document</th>
            </tr>
          </thead>
          <tbody class="bg-white divide-y divide-gray-200" v-if="!loading">
            <tr v-for="log in logs" :key="log.id">
              <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ formatDate(log.created_at) }}</td>
              <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ log.user?.name || '—' }}</td>
              <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ log.action }}</td>
              <td class="px-6 py-4 text-sm text-gray-700">{{ log.description }}</td>
              <td class="px-6 py-4 whitespace-nowrap text-sm">
                <router-link v-if="log.document" :to="{ name: 'document-detail', params: { id: log.document.id } }" class="text-blue-600 hover:text-blue-800">
                  {{ log.document.document_number || `#${log.document.id}` }}
                </router-link>
                <span v-else class="text-gray-400">—</span>
              </td>
            </tr>
            <tr v-if="logs.length === 0">
              <td colspan="5" class="px-6 py-4 text-center text-sm text-gray-500">No logs found for the selected filters</td>
            </tr>
          </tbody>
          <tbody v-else>
            <tr>
              <td colspan="5" class="px-6 py-10 text-center text-sm text-gray-500">Loading logs…</td>
            </tr>
          </tbody>
        </table>
      </div>

      <!-- Pagination -->
      <div v-if="pagination" class="px-6 py-4 border-t border-gray-200 flex items-center justify-between">
        <div class="text-sm text-gray-500">
          Page {{ pagination.current_page }} of {{ pagination.last_page }}
        </div>
        <div class="flex space-x-2">
          <button
            :disabled="pagination.current_page <= 1 || loading"
            @click="changePage(pagination.current_page - 1)"
            class="px-3 py-2 border border-gray-300 rounded-md text-sm font-medium text-gray-700 hover:bg-gray-50 disabled:opacity-50"
          >
            Previous
          </button>
          <button
            :disabled="pagination.current_page >= pagination.last_page || loading"
            @click="changePage(pagination.current_page + 1)"
            class="px-3 py-2 border border-gray-300 rounded-md text-sm font-medium text-gray-700 hover:bg-gray-50 disabled:opacity-50"
          >
            Next
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import axios from 'axios'
import { useToast } from 'vue-toastification'

const toast = useToast()

const activeTab = ref('all') // 'all' | 'security' | 'authentication'
const logs = ref([])
const pagination = ref(null)
const loading = ref(false)
const stats = ref({})
const statsLoading = ref(true)
const statsError = ref(null)
const filters = ref({ search: '', date_from: '', date_to: '' })

const tabClass = (tab) => {
  const isActive = activeTab.value === tab
  return (
    'px-3 py-2 border-b-2 text-sm font-medium ' +
    (isActive
      ? 'border-blue-600 text-blue-600'
      : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300')
  )
}

const setTab = (tab) => {
  if (activeTab.value !== tab) {
    activeTab.value = tab
    fetchLogs(1)
    // Refresh stats as well, so cards stay in sync with filters/tab
    fetchStats()
  }
}

const showingFrom = computed(() => {
  if (!pagination.value) return 0
  return ((pagination.value.current_page - 1) * pagination.value.per_page) + 1
})

const showingTo = computed(() => {
  if (!pagination.value) return 0
  const max = pagination.value.current_page * pagination.value.per_page
  return Math.min(max, pagination.value.total)
})

const formatDate = (dt) => {
  try {
    const d = new Date(dt)
    return d.toLocaleString()
  } catch (_) {
    return dt
  }
}

const buildEndpoint = () => {
  switch (activeTab.value) {
    case 'security':
      return '/api/audit-logs/security'
    case 'authentication':
      return '/api/audit-logs/authentication'
    default:
      return '/api/audit-logs'
  }
}

const fetchLogs = async (page = 1) => {
  loading.value = true
  try {
    const endpoint = buildEndpoint()
    const params = { page }
    if (filters.value.search) params.search = filters.value.search
    if (filters.value.date_from) params.date_from = filters.value.date_from
    if (filters.value.date_to) params.date_to = filters.value.date_to

    const response = await axios.get(endpoint, { params })
    const resData = response.data

    // ApiResponse::paginated shape
    logs.value = Array.isArray(resData?.data) ? resData.data : []
    if (resData && resData.pagination) {
      pagination.value = {
        current_page: resData.pagination.current_page,
        last_page: resData.pagination.last_page,
        per_page: resData.pagination.per_page,
        total: resData.pagination.total,
        from: resData.pagination.from,
        to: resData.pagination.to
      }
    } else {
      pagination.value = null
    }
  } catch (err) {
    console.error('Failed to fetch logs:', err)
    toast.error(err.response?.data?.message || 'Failed to load audit logs')
  } finally {
    loading.value = false
  }
}

const fetchStats = async () => {
  statsLoading.value = true
  statsError.value = null
  try {
    const params = {}
    if (filters.value.date_from) params.date_from = filters.value.date_from
    if (filters.value.date_to) params.date_to = filters.value.date_to
    const response = await axios.get('/api/audit-logs/stats', { params })
    const resData = response.data
    // ApiResponse::success shape wraps payload in `data`
    stats.value = resData?.data ?? {}
  } catch (err) {
    console.warn('Failed to fetch stats:', err)
    statsError.value = err.response?.data?.message || 'Failed to load statistics'
    toast.warning(statsError.value)
    // Ensure template has an object to read from
    stats.value = stats.value || {}
  } finally {
    statsLoading.value = false
  }
}

const applyFilters = async () => {
  await Promise.all([fetchLogs(1), fetchStats()])
}

const clearFilters = async () => {
  filters.value = { search: '', date_from: '', date_to: '' }
  await Promise.all([fetchLogs(1), fetchStats()])
}

const changePage = async (page) => {
  await fetchLogs(page)
}

onMounted(async () => {
  await Promise.all([fetchLogs(1), fetchStats()])
})
</script>
