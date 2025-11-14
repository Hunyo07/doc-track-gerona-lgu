<template>
    <div class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
        <div class="relative top-20 mx-auto p-5 border w-11/12 md:w-3/4 lg:w-1/2 shadow-lg rounded-md bg-white">
            <div class="mt-3">
                <!-- Header -->
                <div class="flex items-center justify-between mb-6">
                    <h3 class="text-xl font-semibold text-gray-900 flex items-center">
                        <svg class="w-6 h-6 text-indigo-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                        </svg>
                        Edit Document
                    </h3>
                    <button @click="$emit('close')" class="text-gray-400 hover:text-gray-600 transition-colors">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>

                <!-- Error Display -->
                <div v-if="error" class="mb-4 p-4 bg-red-50 border border-red-200 rounded-lg">
                    <div class="flex">
                        <svg class="w-5 h-5 text-red-400 mr-2" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path>
                        </svg>
                        <p class="text-sm text-red-700">{{ error }}</p>
                    </div>
                </div>

                <!-- Form -->
                <form @submit.prevent="updateDocument" class="space-y-6">
                    <!-- Basic Information Section -->
                    <div class="bg-gray-50 rounded-lg p-4">
                        <h4 class="text-lg font-medium text-gray-900 mb-4 flex items-center">
                            <svg class="w-5 h-5 text-indigo-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            Basic Information
                        </h4>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div class="md:col-span-2">
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    Document Name <span class="text-red-500">*</span>
                                </label>
                                <input
                                    v-model="form.title"
                                    type="text"
                                    required
                                    class="w-full p-2 border-gray-300 rounded-lg shadow-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors"
                                    :class="{ 'border-red-300 focus:ring-red-500 focus:border-red-500': errors.title }"
                                    placeholder="Enter document name"
                                />
                                <div v-if="errors.title" class="mt-1 text-sm text-red-600">
                                    {{ errors.title[0] }}
                                </div>
                            </div>

                            <div class="md:col-span-2">
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    Description
                                </label>
                                <textarea
                                    v-model="form.description"
                                    rows="3"
                                    class="w-full p-2 border-gray-300 rounded-lg shadow-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors"
                                    :class="{ 'border-red-300 focus:ring-red-500 focus:border-red-500': errors.description }"
                                    placeholder="Enter document description"
                                ></textarea>
                                <div v-if="errors.description" class="mt-1 text-sm text-red-600">
                                    {{ errors.description[0] }}
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Document Classification Section -->
                    <div class="bg-gray-50 rounded-lg p-4">
                        <h4 class="text-lg font-medium text-gray-900 mb-4 flex items-center">
                            <svg class="w-5 h-5 text-indigo-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                            </svg>
                            Document Classification
                        </h4>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    Document Type <span class="text-red-500">*</span>
                                </label>
                                <select
                                    v-model="form.document_type_id"
                                    required
                                    class="w-full p-2 border-gray-300 rounded-lg shadow-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors"
                                    :class="{ 'border-red-300 focus:ring-red-500 focus:border-red-500': errors.document_type_id }"
                                >
                                    <option value="">Select Document Type</option>
                                    <option v-for="type in documentTypes" :key="type.id" :value="type.id">
                                        {{ type.name }}
                                    </option>
                                </select>
                                <div v-if="errors.document_type_id" class="mt-1 text-sm text-red-600">
                                    {{ errors.document_type_id[0] }}
                                </div>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    Priority <span class="text-red-500">*</span>
                                </label>
                                <select
                                    v-model="form.priority"
                                    required
                                    class="w-full p-2 border-gray-300 rounded-lg shadow-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors"
                                    :class="{ 'border-red-300 focus:ring-red-500 focus:border-red-500': errors.priority }"
                                >
                                    <option value="">Select Priority</option>
                                    <option value="low">🟢 Low</option>
                                    <option value="medium">🟡 Medium</option>
                                    <option value="high">🟠 High</option>
                                    <option value="urgent">🔴 Urgent</option>
                                </select>
                                <div v-if="errors.priority" class="mt-1 text-sm text-red-600">
                                    {{ errors.priority[0] }}
                                </div>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    Department <span class="text-red-500">*</span>
                                </label>
                                <select
                                    v-model="form.department_id"
                                    required
                                    class="w-full p-2 border-gray-300 rounded-lg shadow-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors"
                                    :class="{ 'border-red-300 focus:ring-red-500 focus:border-red-500': errors.department_id }"
                                >
                                    <option value="">Select Department</option>
                                    <option v-for="dept in departments" :key="dept.id" :value="dept.id">
                                        {{ dept.name }}
                                    </option>
                                </select>
                                <div v-if="errors.department_id" class="mt-1 text-sm text-red-600">
                                    {{ errors.department_id[0] }}
                                </div>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    Status
                                </label>
                                <select
                                    v-model="form.status"
                                    class="w-full p-2 border-gray-300 rounded-lg shadow-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors"
                                >
                                    <option value="pending">Pending</option>
                                    <option value="approved">Approved</option>
                                    <option value="under_review">Under Review</option>
                                    <option value="completed">Completed</option>
                                </select>
                            </div>

                            <div class="md:col-span-2">
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    Deadline
                                </label>
                                <input
                                    v-model="form.deadline"
                                    type="datetime-local"
                                    class="w-full p-2 border-gray-300 rounded-lg shadow-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors"
                                />
                            </div>
                        </div>
                    </div>

                    <!-- Action Buttons -->
                    <div class="flex justify-end space-x-3 pt-4 border-t border-gray-200">
                        <button
                            type="button"
                            @click="$emit('close')"
                            class="px-6 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition-colors"
                        >
                            Cancel
                        </button>
                        <button
                            type="submit"
                            :disabled="loading"
                            class="px-6 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 disabled:opacity-50 disabled:cursor-not-allowed transition-colors flex items-center"
                        >
                            <svg
                                v-if="loading"
                                class="animate-spin -ml-1 mr-3 h-4 w-4 text-white"
                                xmlns="http://www.w3.org/2000/svg"
                                fill="none"
                                viewBox="0 0 24 24"
                            >
                                <circle
                                    class="opacity-25"
                                    cx="12"
                                    cy="12"
                                    r="10"
                                    stroke="currentColor"
                                    stroke-width="4"
                                ></circle>
                                <path
                                    class="opacity-75"
                                    fill="currentColor"
                                    d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"
                                ></path>
                            </svg>
                            {{ loading ? 'Updating...' : 'Update Document' }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, reactive, onMounted, watch } from 'vue';
import axios from 'axios';

// Props
const props = defineProps({
    document: {
        type: Object,
        required: true
    }
});

// Emits
const emit = defineEmits(['close', 'updated']);

// Reactive data
const loading = ref(false);
const error = ref('');
const errors = ref({});
const documentTypes = ref([]);
const departments = ref([]);

// Form data
const form = reactive({
    title: '',
    description: '',
    document_type_id: '',
    priority: '',
    department_id: '',
    status: '',
    deadline: ''
});

// Initialize form with document data
const initializeForm = () => {
    if (props.document) {
        form.title = props.document.title || '';
        form.description = props.document.description || '';
        form.document_type_id = props.document.document_type_id || '';
        form.priority = props.document.priority || '';
        form.department_id = props.document.department_id || '';
        form.status = props.document.status || 'pending';
        
        // Format deadline for datetime-local input
        if (props.document.deadline) {
            const deadline = new Date(props.document.deadline);
            form.deadline = deadline.toISOString().slice(0, 16);
        }
    }
};

// Fetch document types
const fetchDocumentTypes = async () => {
    try {
        const response = await axios.get('/api/document-types');
        const data = response.data.data || response.data;
        documentTypes.value = Array.isArray(data) ? data.filter(t => t?.is_active) : [];
    } catch (err) {
        console.error('Error fetching document types:', err);
    }
};

// Fetch departments
const fetchDepartments = async () => {
    try {
        const response = await axios.get('/api/departments');
        departments.value = response.data.data || response.data;
    } catch (err) {
        console.error('Error fetching departments:', err);
    }
};

// Update document
const updateDocument = async () => {
    loading.value = true;
    error.value = '';
    errors.value = {};

    try {
        const formData = { ...form };
        
        // Convert deadline back to proper format if provided
        if (formData.deadline) {
            formData.deadline = new Date(formData.deadline).toISOString();
        }

        const response = await axios.put(`/api/documents/${props.document.id}`, formData);
        
        // Emit success event
        emit('updated', response.data);
        emit('close');
    } catch (err) {
        if (err.response?.status === 422) {
            errors.value = err.response.data.errors || {};
        } else {
            error.value = err.response?.data?.message || 'An error occurred while updating the document.';
        }
    } finally {
        loading.value = false;
    }
};

// Watch for document changes
watch(() => props.document, initializeForm, { immediate: true });

// Component lifecycle
onMounted(() => {
    fetchDocumentTypes();
    fetchDepartments();
    initializeForm();
});
</script>