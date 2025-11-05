import { defineStore } from 'pinia'
import { ref, computed } from 'vue'
import axios from 'axios'

export const useIssueStore = defineStore('issues', () => {
  // State
  const issues = ref([])
  const loading = ref(false)
  const error = ref(null)
  const statistics = ref({
    total: 0,
    open: 0,
    in_progress: 0,
    resolved: 0,
    closed: 0
  })
  const pagination = ref({
    current_page: 1,
    last_page: 1,
    per_page: 15,
    total: 0,
    from: 0,
    to: 0
  })

  // Getters
  const openIssues = computed(() => 
    issues.value.filter(issue => issue.status === 'open')
  )
  
  const inProgressIssues = computed(() => 
    issues.value.filter(issue => issue.status === 'in_progress')
  )
  
  const resolvedIssues = computed(() => 
    issues.value.filter(issue => issue.status === 'resolved')
  )

  const urgentIssues = computed(() => 
    issues.value.filter(issue => issue.priority === 'urgent')
  )

  // Actions
  const fetchIssues = async (filters = {}) => {
    loading.value = true
    error.value = null
    
    try {
      const params = new URLSearchParams()
      
      // Add filters to params
      if (filters.search) params.append('search', filters.search)
      if (filters.status) params.append('status', filters.status)
      if (filters.type) params.append('type', filters.type)
      if (filters.priority) params.append('priority', filters.priority)
      if (filters.page) params.append('page', filters.page)
      if (filters.per_page) params.append('per_page', filters.per_page)
      if (filters.sort_by) params.append('sort_by', filters.sort_by)
      if (filters.sort_order) params.append('sort_order', filters.sort_order)

      const response = await axios.get(`/api/issues?${params.toString()}`)
      
      if (response.data.success) {
        issues.value = response.data.data.data || response.data.data
        
        // Update pagination if available
        if (response.data.data.current_page !== undefined) {
          pagination.value = {
            current_page: response.data.data.current_page,
            last_page: response.data.data.last_page,
            per_page: response.data.data.per_page,
            total: response.data.data.total,
            from: response.data.data.from,
            to: response.data.data.to
          }
        }
      } else {
        throw new Error(response.data.message || 'Failed to fetch issues')
      }
    } catch (err) {
      error.value = err.response?.data?.message || err.message || 'Failed to fetch issues'
      console.error('Error fetching issues:', err)
    } finally {
      loading.value = false
    }
  }

  const fetchIssue = async (id) => {
    loading.value = true
    error.value = null
    
    try {
      const response = await axios.get(`/api/issues/${id}`)
      
      if (response.data.success) {
        return response.data.data
      } else {
        throw new Error(response.data.message || 'Failed to fetch issue')
      }
    } catch (err) {
      error.value = err.response?.data?.message || err.message || 'Failed to fetch issue'
      console.error('Error fetching issue:', err)
      throw err
    } finally {
      loading.value = false
    }
  }

  const createIssue = async (issueData) => {
    loading.value = true
    error.value = null
    
    try {
      const formData = new FormData()
      
      // Add text fields
      Object.keys(issueData).forEach(key => {
        if (key !== 'attachments' && issueData[key] !== null && issueData[key] !== undefined) {
          formData.append(key, issueData[key])
        }
      })
      
      // Add file attachments
      if (issueData.attachments && issueData.attachments.length > 0) {
        issueData.attachments.forEach((file, index) => {
          formData.append(`attachments[${index}]`, file)
        })
      }

      const response = await axios.post('/api/issues', formData, {
        headers: {
          'Content-Type': 'multipart/form-data'
        }
      })
      
      if (response.data.success) {
        const newIssue = response.data.data
        issues.value.unshift(newIssue)
        
        // Update statistics
        statistics.value.total++
        if (statistics.value[newIssue.status] !== undefined) {
          statistics.value[newIssue.status]++
        }
        
        return newIssue
      } else {
        throw new Error(response.data.message || 'Failed to create issue')
      }
    } catch (err) {
      error.value = err.response?.data?.message || err.message || 'Failed to create issue'
      console.error('Error creating issue:', err)
      throw err
    } finally {
      loading.value = false
    }
  }

  const updateIssue = async (id, issueData) => {
    loading.value = true
    error.value = null
    
    try {
      const formData = new FormData()
      formData.append('_method', 'PUT')
      
      // Add text fields
      Object.keys(issueData).forEach(key => {
        if (key !== 'attachments' && issueData[key] !== null && issueData[key] !== undefined) {
          formData.append(key, issueData[key])
        }
      })
      
      // Add file attachments
      if (issueData.attachments && issueData.attachments.length > 0) {
        issueData.attachments.forEach((file, index) => {
          formData.append(`attachments[${index}]`, file)
        })
      }

      const response = await axios.post(`/api/issues/${id}`, formData, {
        headers: {
          'Content-Type': 'multipart/form-data'
        }
      })
      
      if (response.data.success) {
        const updatedIssue = response.data.data
        const index = issues.value.findIndex(issue => issue.id === id)
        
        if (index !== -1) {
          issues.value[index] = updatedIssue
        }
        
        return updatedIssue
      } else {
        throw new Error(response.data.message || 'Failed to update issue')
      }
    } catch (err) {
      error.value = err.response?.data?.message || err.message || 'Failed to update issue'
      console.error('Error updating issue:', err)
      throw err
    } finally {
      loading.value = false
    }
  }

  const deleteIssue = async (id) => {
    loading.value = true
    error.value = null
    
    try {
      const response = await axios.delete(`/api/issues/${id}`)
      
      if (response.data.success) {
        const index = issues.value.findIndex(issue => issue.id === id)
        
        if (index !== -1) {
          const deletedIssue = issues.value[index]
          issues.value.splice(index, 1)
          
          // Update statistics
          statistics.value.total--
          if (statistics.value[deletedIssue.status] !== undefined) {
            statistics.value[deletedIssue.status]--
          }
        }
        
        return true
      } else {
        throw new Error(response.data.message || 'Failed to delete issue')
      }
    } catch (err) {
      error.value = err.response?.data?.message || err.message || 'Failed to delete issue'
      console.error('Error deleting issue:', err)
      throw err
    } finally {
      loading.value = false
    }
  }

  const fetchStatistics = async () => {
    try {
      const response = await axios.get('/api/issues/statistics')
      
      if (response.data.success) {
        statistics.value = response.data.data
      } else {
        throw new Error(response.data.message || 'Failed to fetch statistics')
      }
    } catch (err) {
      console.error('Error fetching statistics:', err)
      // Don't set error state for statistics as it's not critical
    }
  }

  const clearError = () => {
    error.value = null
  }

  const resetStore = () => {
    issues.value = []
    loading.value = false
    error.value = null
    statistics.value = {
      total: 0,
      open: 0,
      in_progress: 0,
      resolved: 0,
      closed: 0
    }
    pagination.value = {
      current_page: 1,
      last_page: 1,
      per_page: 15,
      total: 0,
      from: 0,
      to: 0
    }
  }

  // Utility functions
  const getIssueById = (id) => {
    return issues.value.find(issue => issue.id === id)
  }

  const getIssuesByStatus = (status) => {
    return issues.value.filter(issue => issue.status === status)
  }

  const getIssuesByType = (type) => {
    return issues.value.filter(issue => issue.type === type)
  }

  const getIssuesByPriority = (priority) => {
    return issues.value.filter(issue => issue.priority === priority)
  }

  return {
    // State
    issues,
    loading,
    error,
    statistics,
    pagination,
    
    // Getters
    openIssues,
    inProgressIssues,
    resolvedIssues,
    urgentIssues,
    
    // Actions
    fetchIssues,
    fetchIssue,
    createIssue,
    updateIssue,
    deleteIssue,
    fetchStatistics,
    clearError,
    resetStore,
    
    // Utilities
    getIssueById,
    getIssuesByStatus,
    getIssuesByType,
    getIssuesByPriority
  }
})