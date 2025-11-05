<template>
    <div class="space-y-6">
        <div class="bg-white shadow rounded-lg">
            <div class="px-4 py-5 sm:p-6">
                <div class="flex justify-between items-center mb-6">
                    <div>
                        <h1 class="text-2xl font-bold text-gray-900">
                            Incoming Documents
                        </h1>
                        <p class="text-sm text-gray-600 mt-1">
                            New documents submitted to the system awaiting
                            initial review
                        </p>
                    </div>
                    <div class="flex space-x-3">
                        <button
                            @click="refreshDocuments"
                            class="bg-gray-600 text-white px-4 py-2 rounded-md hover:bg-gray-700"
                        >
                            Refresh
                        </button>
                        <button
                            @click="showBulkReviewModal = true"
                            v-if="selectedDocuments.length > 0"
                            class="bg-indigo-600 text-white px-4 py-2 rounded-md hover:bg-indigo-700"
                        >
                            Review Selected ({{ selectedDocuments.length }})
                        </button>
                        <button
                            @click="bulkHold"
                            v-if="selectedDocuments.length > 0"
                            class="bg-yellow-600 text-white px-4 py-2 rounded-md hover:bg-yellow-700"
                        >
                            Hold Selected ({{ selectedDocuments.length }})
                        </button>
                    </div>
                </div>

                <!-- Stats Cards -->
                <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
                    <div class="bg-blue-50 p-4 rounded-lg">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <div
                                    class="w-8 h-8 bg-blue-500 rounded-full flex items-center justify-center"
                                >
                                    <span
                                        class="text-white text-sm font-bold"
                                        >{{ stats.submitted || 0 }}</span
                                    >
                                </div>
                            </div>
                            <div class="ml-3">
                                <p class="text-sm font-medium text-blue-600">
                                    Submitted
                                </p>
                                <p class="text-xs text-blue-500">
                                    Awaiting review
                                </p>
                            </div>
                        </div>
                    </div>

                    <div class="bg-yellow-50 p-4 rounded-lg">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <div
                                    class="w-8 h-8 bg-yellow-500 rounded-full flex items-center justify-center"
                                >
                                    <span
                                        class="text-white text-sm font-bold"
                                        >{{ stats.urgent || 0 }}</span
                                    >
                                </div>
                            </div>
                            <div class="ml-3">
                                <p class="text-sm font-medium text-yellow-600">
                                    Urgent
                                </p>
                                <p class="text-xs text-yellow-500">
                                    High priority
                                </p>
                            </div>
                        </div>
                    </div>

                    <div class="bg-red-50 p-4 rounded-lg">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <div
                                    class="w-8 h-8 bg-red-500 rounded-full flex items-center justify-center"
                                >
                                    <span
                                        class="text-white text-sm font-bold"
                                        >{{ stats.overdue || 0 }}</span
                                    >
                                </div>
                            </div>
                            <div class="ml-3">
                                <p class="text-sm font-medium text-red-600">
                                    Overdue
                                </p>
                                <p class="text-xs text-red-500">
                                    Past deadline
                                </p>
                            </div>
                        </div>
                    </div>

                    <div class="bg-green-50 p-4 rounded-lg">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <div
                                    class="w-8 h-8 bg-green-500 rounded-full flex items-center justify-center"
                                >
                                    <span
                                        class="text-white text-sm font-bold"
                                        >{{ stats.today || 0 }}</span
                                    >
                                </div>
                            </div>
                            <div class="ml-3">
                                <p class="text-sm font-medium text-green-600">
                                    Today
                                </p>
                                <p class="text-xs text-green-500">
                                    Received today
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Filters -->
                <div class="bg-gray-50 p-4 rounded-lg mb-6">
                    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                        <div>
                            <label
                                class="block text-sm font-medium text-gray-700 mb-1"
                                >Priority</label
                            >
                            <select
                                v-model="filters.priority"
                                @change="applyFilters"
                                class="w-full border-gray-300 rounded-md"
                            >
                                <option value="">All Priorities</option>
                                <option value="urgent">Urgent</option>
                                <option value="high">High</option>
                                <option value="medium">Medium</option>
                                <option value="low">Low</option>
                            </select>
                        </div>

                        <div>
                            <label
                                class="block text-sm font-medium text-gray-700 mb-1"
                                >Type</label
                            >
                            <select
                                v-model="filters.type"
                                @change="applyFilters"
                                class="w-full border-gray-300 rounded-md"
                            >
                                <option value="">All Types</option>
                                <option value="bid">Bid</option>
                                <option value="award">Award</option>
                                <option value="contract">Contract</option>
                                <option value="other">Other</option>
                            </select>
                        </div>

                        <div>
                            <label
                                class="block text-sm font-medium text-gray-700 mb-1"
                                >Date Range</label
                            >
                            <select
                                v-model="filters.date_range"
                                @change="applyFilters"
                                class="w-full border-gray-300 rounded-md"
                            >
                                <option value="">All Time</option>
                                <option value="today">Today</option>
                                <option value="week">This Week</option>
                                <option value="month">This Month</option>
                            </select>
                        </div>

                        <div>
                            <label
                                class="block text-sm font-medium text-gray-700 mb-1"
                                >Search</label
                            >
                            <input
                                v-model="filters.search"
                                @input="applyFilters"
                                type="text"
                                placeholder="Search documents..."
                                class="w-full border-gray-300 rounded-md"
                            />
                        </div>
                    </div>
                </div>

                <!-- Bulk Actions -->
                <div
                    v-if="selectedDocuments.length > 0"
                    class="bg-blue-50 p-4 rounded-lg mb-6"
                >
                    <div class="flex justify-between items-center">
                        <span class="text-sm font-medium text-blue-900">
                            {{ selectedDocuments.length }} document(s) selected
                        </span>
                        <div class="flex space-x-2">
                            <button
                                @click="bulkAccept"
                                v-if="canAcceptDocuments()"
                                class="px-3 py-1 text-sm bg-green-600 text-white rounded hover:bg-green-700"
                            >
                                Accept & Review
                            </button>
                            <button
                                @click="bulkAssign"
                                v-if="canAssignDocuments()"
                                class="px-3 py-1 text-sm bg-blue-600 text-white rounded hover:bg-blue-700"
                            >
                                Assign
                            </button>
                            <button
                                @click="bulkReject"
                                v-if="canRejectDocuments()"
                                class="px-3 py-1 text-sm bg-red-600 text-white rounded hover:bg-red-700"
                            >
                                Reject
                            </button>
                            <button
                                @click="clearSelection"
                                class="px-3 py-1 text-sm bg-gray-600 text-white rounded hover:bg-gray-700"
                            >
                                Clear
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Documents Table -->
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
                                >
                                    <input
                                        type="checkbox"
                                        @change="toggleSelectAll"
                                        :checked="isAllSelected"
                                        class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500"
                                    />
                                </th>
                                <th
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
                                >
                                    Document
                                </th>
                                <th
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
                                >
                                    Type
                                </th>
                                <th
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
                                >
                                    Priority
                                </th>
                                <th
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
                                >
                                    Submitted By
                                </th>
                                <th
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
                                >
                                    Submitted
                                </th>
                                <th
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
                                >
                                    Deadline
                                </th>
                                <th
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
                                >
                                    Actions
                                </th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            <tr
                                v-for="document in documents"
                                :key="document.id"
                                class="hover:bg-gray-50"
                                :class="{
                                    'bg-blue-50': selectedDocuments.includes(
                                        document.id
                                    ),
                                    'bg-red-50': isOverdue(document.deadline),
                                    'bg-yellow-50':
                                        document.priority === 'urgent',
                                }"
                            >
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <input
                                        type="checkbox"
                                        :value="document.id"
                                        v-model="selectedDocuments"
                                        class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500"
                                    />
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div>
                                        <div
                                            class="text-sm font-medium text-gray-900"
                                        >
                                            {{ document.title }}
                                        </div>
                                        <div class="text-sm text-gray-500">
                                            {{ document.document_number }}
                                        </div>
                                        <div
                                            v-if="document.description"
                                            class="text-xs text-gray-400 mt-1 truncate max-w-xs"
                                        >
                                            {{ document.description }}
                                        </div>
                                        <!-- Status indicator -->
                                        <div
                                            class="flex items-center mt-2 space-x-2"
                                        >
                                            <span
                                                class="px-2 py-1 text-xs font-medium rounded-full bg-blue-100 text-blue-800"
                                            >
                                                <!-- {{ getStageLabel(document) }} -->
                                            </span>
                                            <span
                                                v-if="
                                                    isOverdue(document.deadline)
                                                "
                                                class="px-2 py-1 text-xs font-medium rounded-full bg-red-100 text-red-800"
                                            >
                                                Overdue
                                            </span>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span
                                        class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800"
                                    >
                                        {{ document.type }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span
                                        :class="
                                            getPriorityClass(document.priority)
                                        "
                                        class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full"
                                    >
                                        {{ document.priority }}
                                    </span>
                                </td>
                                <td
                                    class="px-6 py-4 whitespace-nowrap text-sm text-gray-900"
                                >
                                    <div>
                                        <div class="font-medium">
                                            {{ document.creator?.name }}
                                        </div>
                                        <div class="text-gray-500">
                                            {{ document.department?.name }}
                                        </div>
                                    </div>
                                </td>
                                <td
                                    class="px-6 py-4 whitespace-nowrap text-sm text-gray-900"
                                >
                                    <div>
                                        <div>
                                            {{
                                                formatDate(document.created_at)
                                            }}
                                        </div>
                                        <div class="text-gray-500 text-xs">
                                            {{
                                                formatTime(document.created_at)
                                            }}
                                        </div>
                                    </div>
                                </td>
                                <td
                                    class="px-6 py-4 whitespace-nowrap text-sm text-gray-900"
                                >
                                    <div v-if="document.deadline">
                                        <div
                                            :class="{
                                                'text-red-600 font-medium':
                                                    isOverdue(
                                                        document.deadline
                                                    ),
                                            }"
                                        >
                                            {{ formatDate(document.deadline) }}
                                        </div>
                                        <div class="text-xs text-gray-500">
                                            {{
                                                getDeadlineStatus(
                                                    document.deadline
                                                )
                                            }}
                                        </div>
                                    </div>
                                    <span v-else class="text-gray-400"
                                        >No deadline</span
                                    >
                                </td>
                                <td
                                    class="px-6 py-4 whitespace-nowrap text-sm font-medium"
                                >
                                    <div class="flex space-x-2">
                                        <button
                                            @click="openPreview(document.id)"
                                            class="text-indigo-600 hover:text-indigo-900"
                                        >
                                            View
                                        </button>
                                        <button
                                            v-if="canAcceptSingleDocument(document)"
                                            @click="acceptDocument(document)"
                                            class="text-green-600 hover:text-green-900"
                                        >
                                            Accept
                                        </button>
                                        <button
                                            @click="rejectDocument(document)"
                                            class="text-red-600 hover:text-red-900"
                                        >
                                            Reject
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <!-- Empty State -->
                <div
                    v-if="!loading && documents.length === 0"
                    class="text-center py-12"
                >
                    <div class="text-gray-400 text-lg mb-2">📥</div>
                    <h3 class="text-lg font-medium text-gray-900 mb-2">
                        No incoming documents
                    </h3>
                    <p class="text-gray-500">
                        All submitted documents have been reviewed.
                    </p>
                </div>

                <!-- Loading State -->
                <div v-if="loading" class="text-center py-12">
                    <div class="text-gray-400 text-lg mb-2">⏳</div>
                    <p class="text-gray-500">Loading documents...</p>
                </div>

                <!-- Pagination -->
                <div
                    v-if="pagination && pagination.last_page > 1"
                    class="mt-6 flex items-center justify-between"
                >
                    <div class="text-sm text-gray-700">
                        Showing
                        {{
                            (pagination.current_page - 1) *
                                pagination.per_page +
                            1
                        }}
                        to
                        {{
                            Math.min(
                                pagination.current_page * pagination.per_page,
                                pagination.total
                            )
                        }}
                        of {{ pagination.total }} results
                    </div>
                    <div class="flex space-x-2">
                        <button
                            v-if="pagination.current_page > 1"
                            @click="changePage(pagination.current_page - 1)"
                            class="px-3 py-2 border border-gray-300 rounded-md text-sm font-medium text-gray-700 hover:bg-gray-50"
                        >
                            Previous
                        </button>
                        <button
                            v-if="
                                pagination.current_page < pagination.last_page
                            "
                            @click="changePage(pagination.current_page + 1)"
                            class="px-3 py-2 border border-gray-300 rounded-md text-sm font-medium text-gray-700 hover:bg-gray-50"
                        >
                            Next
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Bulk Review Modal -->
        <div
            v-if="showBulkReviewModal"
            class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50"
        >
            <div
                class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white"
            >
                <div class="mt-3">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">
                        Review {{ selectedDocuments.length }} Documents
                    </h3>
                    <form @submit.prevent="processBulkReview">
                        <div class="mb-4">
                            <label
                                class="block text-sm font-medium text-gray-700 mb-2"
                                >Action</label
                            >
                            <select
                                v-model="bulkReviewForm.action"
                                required
                                class="w-full border-gray-300 rounded-md"
                            >
                                <option value="">Select Action</option>
                                <option value="accept">
                                    Accept & Move to Review
                                </option>
                                <option value="assign">Accept & Assign</option>
                                <option value="reject">Reject</option>
                            </select>
                        </div>

                        <div
                            v-if="bulkReviewForm.action === 'assign'"
                            class="mb-4"
                        >
                            <label
                                class="block text-sm font-medium text-gray-700 mb-2"
                                >Send To Department</label
                            >
                            <select
                                v-model="bulkReviewForm.department_id"
                                required
                                class="w-full border-gray-300 rounded-md"
                            >
                                <option value="">Select Department</option>
                                <option
                                    v-for="department in availableDepartments"
                                    :key="department.id"
                                    :value="department.id"
                                >
                                    {{ department.name }}
                                </option>
                            </select>
                        </div>

                        <div class="mb-4">
                            <label
                                class="block text-sm font-medium text-gray-700 mb-2"
                                >Notes</label
                            >
                            <textarea
                                v-model="bulkReviewForm.notes"
                                rows="3"
                                class="w-full border-gray-300 rounded-md"
                                placeholder="Add notes about this review..."
                            ></textarea>
                        </div>

                        <div class="flex justify-end space-x-3">
                            <button
                                type="button"
                                @click="showBulkReviewModal = false"
                                class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-200 rounded-md hover:bg-gray-300"
                            >
                                Cancel
                            </button>
                            <button
                                type="submit"
                                :disabled="bulkLoading"
                                class="px-4 py-2 text-sm font-medium text-white bg-indigo-600 rounded-md hover:bg-indigo-700 disabled:opacity-50"
                            >
                                {{
                                    bulkLoading
                                        ? "Processing..."
                                        : "Process Documents"
                                }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Document Preview Modal -->
        <div
            v-if="showPreviewModal"
            class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50"
        >
            <div class="relative top-10 mx-auto p-5 border w-full max-w-3xl shadow-lg rounded-md bg-white">
                <div class="flex justify-between items-start mb-4">
                    <h3 class="text-lg font-medium text-gray-900">Document Preview</h3>
                    <button @click="closePreview" class="text-gray-500 hover:text-gray-700">✕</button>
                </div>

                <div v-if="previewLoading" class="text-center py-8 text-gray-600">Loading document...</div>
                <div v-else-if="previewError" class="text-center py-8 text-red-600">{{ previewError }}</div>

                <div v-else-if="previewDocument" class="space-y-4">
                    <!-- Header -->
                    <div class="border-b pb-3">
                        <div class="flex items-center justify-between">
                            <div>
                                <div class="text-xl font-semibold text-gray-900">{{ previewDocument.title || previewDocument.file_name }}</div>
                                <div class="text-sm text-gray-500">Document No: {{ previewDocument.document_number }}</div>
                            </div>
                            <span :class="getStatusClass(previewDocument.status)" class="px-2 py-1 text-xs font-medium rounded-full">
                                {{ getStatusLabel(previewDocument.status) }}
                            </span>
                        </div>
                    </div>

                    <!-- Meta -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="space-y-1 text-sm">
                            <div><span class="font-medium text-gray-700">Type:</span> {{ previewDocument.type || previewDocument.documentType?.name || 'N/A' }}</div>
                            <div>
                                <span class="font-medium text-gray-700">Priority:</span>
                                <span :class="getPriorityClass(previewDocument.priority)" class="px-2 py-0.5 text-xs font-semibold rounded-full">{{ previewDocument.priority || 'normal' }}</span>
                            </div>
                            <div><span class="font-medium text-gray-700">From:</span> {{ previewDocument.department?.name || 'N/A' }}</div>
                            <div><span class="font-medium text-gray-700">To:</span> {{ previewDocument.currentDepartment?.name || 'N/A' }}</div>
                        </div>
                        <div class="space-y-1 text-sm">
                            <div><span class="font-medium text-gray-700">Created:</span> {{ formatDate(previewDocument.created_at) }} {{ formatTime(previewDocument.created_at) }}</div>
                            <div v-if="previewDocument.received_at"><span class="font-medium text-gray-700">Received:</span> {{ formatDate(previewDocument.received_at) }} {{ formatTime(previewDocument.received_at) }}</div>
                            <div v-if="previewDocument.deadline"><span class="font-medium text-gray-700">Deadline:</span> {{ formatDate(previewDocument.deadline) }}</div>
                        </div>
                    </div>

                    <!-- Description -->
                    <div v-if="previewDocument.description" class="bg-gray-50 rounded p-3 text-sm text-gray-700">
                        {{ previewDocument.description }}
                    </div>

                    <!-- Attachments -->
                    <div v-if="previewDocument.attachments && previewDocument.attachments.length" class="border-t pt-3">
                        <div class="text-sm font-medium text-gray-700 mb-2">Attachments</div>
                        <ul class="space-y-2">
                            <li v-for="att in previewDocument.attachments" :key="att.id" class="flex items-center justify-between">
                                <div class="text-sm text-gray-800">{{ att.file_name }}</div>
                                <div class="flex space-x-2">
                                    <button @click="downloadAttachment(att)" class="text-indigo-600 hover:text-indigo-800 text-sm">Download</button>
                                    <a :href="`/storage/${att.file_path}`" target="_blank" class="text-gray-600 hover:text-gray-800 text-sm">Open</a>
                                </div>
                            </li>
                        </ul>
                    </div>

                    <!-- Actions -->
                    <div class="flex justify-between items-center pt-2">
                        <a :href="`/documents/${previewDocument.id}`" class="text-sm text-gray-600 hover:text-gray-800">Open full details →</a>
                        <div class="flex space-x-2">
                            <button
                                v-if="canAcceptPreview(previewDocument)"
                                @click="acceptPreview()"
                                class="px-3 py-1 text-sm bg-green-600 text-white rounded hover:bg-green-700"
                            >Accept</button>
                            <button
                                v-if="canRejectDocuments()"
                                @click="rejectPreview()"
                                class="px-3 py-1 text-sm bg-red-600 text-white rounded hover:bg-red-700"
                            >Reject</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, reactive, onMounted, computed } from "vue";
import { useDocumentStore } from "../stores/documents";
import { useWorkflowStore } from "../stores/workflow";
import { useAuthStore } from "../stores/auth";
import StatusBadge from "../components/StatusBadge.vue";
import axios from "axios";

const documentStore = useDocumentStore();
const workflowStore = useWorkflowStore();
const authStore = useAuthStore();

// State
const documents = computed(() => documentStore.incoming);
const loading = computed(() => documentStore.loading);
const pagination = computed(() => documentStore.pagination);
const selectedDocuments = ref([]);
const availableUsers = ref([]);
const availableDepartments = ref([]);
const stats = ref({});

// Modal states
const showBulkReviewModal = ref(false);
const bulkLoading = ref(false);

// Preview modal state
const showPreviewModal = ref(false);
const previewLoading = ref(false);
const previewError = ref("");
const previewDocument = ref(null);

// Filters
const filters = reactive({
    priority: "",
    type: "",
    date_range: "",
    search: "",
});

// Forms
const bulkReviewForm = reactive({
    action: "",
    department_id: "",
    notes: "",
});

// Computed properties
const isAllSelected = computed(() => {
    return (
        documents.value.length > 0 &&
        selectedDocuments.value.length === documents.value.length
    );
});

// Functions
const fetchDocuments = async (extraParams = {}) => {
    const params = { ...filters, ...extraParams };
    
    // Convert date range to actual dates
    if (filters.date_range) {
        const now = new Date();
        switch (filters.date_range) {
            case "today":
                params.date_from = now.toISOString().split("T")[0];
                params.date_to = now.toISOString().split("T")[0];
                break;
            case "week":
                const weekAgo = new Date(now.getTime() - 7 * 24 * 60 * 60 * 1000);
                params.date_from = weekAgo.toISOString().split("T")[0];
                break;
            case "month":
                const monthAgo = new Date(now.getFullYear(), now.getMonth() - 1, now.getDate());
                params.date_from = monthAgo.toISOString().split("T")[0];
                break;
        }
    }
    
    await documentStore.fetchIncoming(params);
};

const fetchStats = async () => {
    try {
        const response = await axios.get("/api/documents-stats");
        const data = response.data;

        stats.value = {
            submitted: data.by_status?.submitted || 0,
            urgent: data.by_priority?.urgent || 0,
            overdue: data.overdue || 0,
            today: data.recent || 0,
        };
    } catch (error) {
        console.error("Error fetching stats:", error);
    }
};

const fetchDepartments = async () => {
    try {
        availableDepartments.value = await documentStore.fetchDepartments();
    } catch (error) {
        console.error("Error fetching departments:", error);
    }
};

const applyFilters = () => {
    fetchDocuments();
};

const refreshDocuments = () => {
    fetchDocuments();
    fetchStats();
};

const changePage = (page) => {
    // Add page to params and fetch
    fetchDocuments({ page });
};

// Selection functions
const toggleSelectAll = () => {
    if (isAllSelected.value) {
        selectedDocuments.value = [];
    } else {
        selectedDocuments.value = documents.value.map((doc) => doc.id);
    }
};

const clearSelection = () => {
    selectedDocuments.value = [];
};

// Document actions
const acceptDocument = async (document) => {
    const notes = prompt("Add notes (optional):");
    
    try {
        await workflowStore.receiveDocument(document.id, notes);
        alert("Document received successfully!");
        fetchDocuments();
        fetchStats();
    } catch (error) {
        console.error("Error receiving document:", error);
        alert("Error receiving document. Please try again.");
    }
};

const rejectDocument = async (document) => {
    const reason = prompt("Please provide a reason for rejection:");
    if (!reason) return;

    try {
        await workflowStore.rejectDocument(document.id, reason);
        alert("Document rejected!");
        fetchDocuments();
        fetchStats();
    } catch (error) {
        console.error("Error rejecting document:", error);
        alert("Error rejecting document. Please try again.");
    }
};

// Preview helpers/actions
const openPreview = async (id) => {
    showPreviewModal.value = true;
    previewLoading.value = true;
    previewError.value = "";
    previewDocument.value = null;
    try {
        const doc = await documentStore.fetchDocument(id);
        // API may return wrapped data; normalize common shapes
        previewDocument.value = doc.data || doc.document || doc;
    } catch (error) {
        console.error("Error loading document preview:", error);
        previewError.value = "Failed to load document.";
    } finally {
        previewLoading.value = false;
    }
};

const closePreview = () => {
    showPreviewModal.value = false;
    previewDocument.value = null;
    previewError.value = "";
};

const acceptPreview = async () => {
    if (!previewDocument.value) return;
    const notes = prompt("Add notes (optional):");
    try {
        await workflowStore.receiveDocument(previewDocument.value.id, notes);
        alert("Document received successfully!");
        closePreview();
        fetchDocuments();
        fetchStats();
    } catch (error) {
        console.error("Error receiving document:", error);
        alert("Error receiving document. Please try again.");
    }
};

const rejectPreview = async () => {
    if (!previewDocument.value) return;
    const reason = prompt("Please provide a reason for rejection:");
    if (!reason) return;
    try {
        await workflowStore.rejectDocument(previewDocument.value.id, reason);
        alert("Document rejected!");
        closePreview();
        fetchDocuments();
        fetchStats();
    } catch (error) {
        console.error("Error rejecting document:", error);
        alert("Error rejecting document. Please try again.");
    }
};

const canAcceptPreview = (doc) => {
    const user = authStore.user;
    if (!user || !doc) return false;
    const inDepartment = doc.current_department_id === user.department_id;
    const isSubmitted = doc.status === "submitted";
    const notOwnDocument = doc.created_by !== user.id;
    const hasPermission = authStore.can("accept documents") || authStore.hasRole("admin");
    return hasPermission && inDepartment && isSubmitted && notOwnDocument;
};

const downloadAttachment = (attachment) => {
    window.open(`/api/attachments/${attachment.id}/download`, "_blank");
};

const getStatusClass = (status) => {
    const classes = {
        draft: "bg-gray-100 text-gray-800",
        submitted: "bg-blue-100 text-blue-800",
        under_review: "bg-yellow-100 text-yellow-800",
        approved: "bg-green-100 text-green-800",
        rejected: "bg-red-100 text-red-800",
        completed: "bg-purple-100 text-purple-800",
        received: "bg-indigo-100 text-indigo-800",
    };
    return classes[status] || "bg-gray-100 text-gray-800";
};

const getStatusLabel = (status) => {
    const labels = {
        draft: "Draft",
        submitted: "Submitted",
        under_review: "Under Review",
        approved: "Approved",
        rejected: "Rejected",
        completed: "Completed",
        received: "Received",
    };
    return labels[status] || status;
};

// Bulk operations
const bulkAccept = async () => {
    if (
        !confirm(
            `Accept ${selectedDocuments.value.length} documents and move to review?`
        )
    ) {
        return;
    }

    bulkLoading.value = true;
    try {
        const notes = "Bulk accepted from incoming queue";
        await Promise.all(
            selectedDocuments.value.map((id) =>
                workflowStore.receiveDocument(id, notes)
            )
        );

        selectedDocuments.value = [];
        fetchDocuments();
        fetchStats();
        alert("Documents accepted successfully!");
    } catch (error) {
        console.error("Error accepting documents:", error);
        alert("Error accepting documents. Please try again.");
    } finally {
        bulkLoading.value = false;
    }
};

const bulkAssign = () => {
    bulkReviewForm.action = "assign";
    showBulkReviewModal.value = true;
};

const bulkReject = async () => {
    const reason = prompt(
        "Please provide a reason for rejecting these documents:"
    );
    if (!reason) return;

    if (!confirm(`Reject ${selectedDocuments.value.length} documents?`)) {
        return;
    }

    bulkLoading.value = true;
    try {
        await axios.post("/api/documents/bulk-update-status", {
            document_ids: selectedDocuments.value,
            status: "rejected",
            notes: reason,
        });

        selectedDocuments.value = [];
        fetchDocuments();
        fetchStats();
        alert("Documents rejected successfully!");
    } catch (error) {
        console.error("Error rejecting documents:", error);
        alert("Error rejecting documents. Please try again.");
    } finally {
        bulkLoading.value = false;
    }
};

const bulkHold = async () => {
    const reason = prompt(
        "Please provide a reason for putting these documents on hold:"
    );
    if (!reason) return;

    if (!confirm(`Put ${selectedDocuments.value.length} documents on hold?`)) {
        return;
    }

    bulkLoading.value = true;
    try {
        await Promise.all(
            selectedDocuments.value.map((id) =>
                workflowStore.holdDocument(id, reason, `ON HOLD: ${reason}`)
            )
        );

        selectedDocuments.value = [];
        fetchDocuments();
        fetchStats();
        alert("Documents put on hold successfully!");
    } catch (error) {
        console.error("Error putting documents on hold:", error);
        alert("Error putting documents on hold. Please try again.");
    } finally {
        bulkLoading.value = false;
    }
};

const processBulkReview = async () => {
    if (!bulkReviewForm.action) return;

    bulkLoading.value = true;
    try {
        if (bulkReviewForm.action === "accept") {
            const notes = bulkReviewForm.notes || "Bulk accepted from incoming queue";
            await Promise.all(
                selectedDocuments.value.map((id) =>
                    workflowStore.receiveDocument(id, notes)
                )
            );
        } else if (bulkReviewForm.action === "assign") {
            // First accept documents
            const acceptNotes = bulkReviewForm.notes || "Accepted and assigned";
            await Promise.all(
                selectedDocuments.value.map((id) =>
                    workflowStore.receiveDocument(id, acceptNotes)
                )
            );

            // Then assign them
            await axios.post("/api/documents/bulk-update-department", {
                document_ids: selectedDocuments.value,
                department_id: bulkReviewForm.department_id,
                notes: bulkReviewForm.notes || "Assigned from incoming queue",
            });
        } else if (bulkReviewForm.action === "reject") {
            await axios.post("/api/documents/bulk-update-status", {
                document_ids: selectedDocuments.value,
                status: "rejected",
                notes: bulkReviewForm.notes || "Rejected from incoming queue",
            });
        }

        showBulkReviewModal.value = false;
        bulkReviewForm.action = "";
        bulkReviewForm.department_id = "";
        bulkReviewForm.notes = "";
        selectedDocuments.value = [];

        fetchDocuments();
        fetchStats();
        alert("Documents processed successfully!");
    } catch (error) {
        console.error("Error processing documents:", error);
        alert("Error processing documents. Please try again.");
    } finally {
        bulkLoading.value = false;
    }
};

// Utility functions
const getPriorityClass = (priority) => {
    const classes = {
        low: "bg-green-100 text-green-800",
        medium: "bg-yellow-100 text-yellow-800",
        high: "bg-orange-100 text-orange-800",
        urgent: "bg-red-100 text-red-800",
    };
    return classes[priority] || "bg-gray-100 text-gray-800";
};

const formatDate = (date) => {
    return new Date(date).toLocaleDateString();
};

// Access control functions
const canAcceptDocuments = () => {
    return authStore.can("accept documents") || authStore.hasRole("admin");
};

const canAcceptSingleDocument = (document) => {
    const user = authStore.user;
    if (!user) return false;
    const inDepartment = document.current_department_id === user.department_id;
    const isSubmitted = document.status === "submitted";
    const notOwnDocument = document.created_by !== user.id;
    const hasPermission = authStore.can("accept documents") || authStore.hasRole("admin");
    return hasPermission && inDepartment && isSubmitted && notOwnDocument;
};

const canAssignDocuments = () => {
    return authStore.can("assign documents") || authStore.hasRole("admin");
};

const canRejectDocuments = () => {
    return authStore.can("reject documents") || authStore.hasRole("admin");
};

const formatTime = (date) => {
    return new Date(date).toLocaleTimeString([], {
        hour: "2-digit",
        minute: "2-digit",
    });
};

const isOverdue = (deadline) => {
    if (!deadline) return false;
    return new Date(deadline) < new Date();
};

const getDeadlineStatus = (deadline) => {
    if (!deadline) return "";

    const now = new Date();
    const deadlineDate = new Date(deadline);
    const diffTime = deadlineDate - now;
    const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24));

    if (diffDays < 0) {
        return `${Math.abs(diffDays)} days overdue`;
    } else if (diffDays === 0) {
        return "Due today";
    } else if (diffDays === 1) {
        return "Due tomorrow";
    } else {
        return `${diffDays} days remaining`;
    }
};

// Initialize
onMounted(() => {
    fetchDocuments();
    fetchStats();
    fetchDepartments();
});
</script>
// Helpers const capitalize = (s) => (s ? s.charAt(0).toUpperCase() +
s.slice(1).replace(/_/g, " ") : ""); const getStageLabel = (document) => { const
dept = (document.current_department?.name || "").toLowerCase(); const status =
document.status; if (status === "completed") return "Completed"; if (status ===
"rejected") return "Rejected"; if (status === "draft") return "Draft"; if
(dept.includes("budget")) { if (status === "submitted") return "For Review"; if
(status === "received" || status === "under_review") return "Under Review"; if
(status === "approved") return "Signed by Budget Officer"; } if
(dept.includes("account")) { if (status === "submitted") return "For
Obligation"; if (status === "received" || status === "under_review") return
"Under Review"; if (status === "approved") return "Obligation Created"; } if
(dept.includes("treasury")) { if (status === "submitted") return "For Approval";
if (status === "received" || status === "under_review") return "Under Review";
if (status === "approved") return "Approved for Payment"; } return
capitalize(status); };
