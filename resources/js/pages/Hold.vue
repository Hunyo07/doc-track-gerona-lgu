<template>
    <div class="space-y-6">
        <div class="bg-white shadow rounded-lg">
            <div class="px-4 py-5 sm:p-6">
                <div class="flex justify-between items-center mb-6">
                    <div>
                        <h1 class="text-2xl font-bold text-gray-900">
                            Hold Documents
                        </h1>
                        <p class="text-sm text-gray-600 mt-1">
                            Documents on hold requiring additional information
                            or action
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
                            @click="showBulkActionModal = true"
                            v-if="selectedDocuments.length > 0"
                            class="bg-indigo-600 text-white px-4 py-2 rounded-md hover:bg-indigo-700"
                        >
                            Action ({{ selectedDocuments.length }})
                        </button>
                    </div>
                </div>

                <!-- Stats Cards -->
                <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
                    <div class="bg-yellow-50 p-4 rounded-lg">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <div
                                    class="w-8 h-8 bg-yellow-500 rounded-full flex items-center justify-center"
                                >
                                    <span
                                        class="text-white text-sm font-bold"
                                        >{{ stats.total || 0 }}</span
                                    >
                                </div>
                            </div>
                            <div class="ml-3">
                                <p class="text-sm font-medium text-yellow-600">
                                    Total on Hold
                                </p>
                                <p class="text-xs text-yellow-500">
                                    All held documents
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
                                        >{{ stats.urgent || 0 }}</span
                                    >
                                </div>
                            </div>
                            <div class="ml-3">
                                <p class="text-sm font-medium text-red-600">
                                    Urgent
                                </p>
                                <p class="text-xs text-red-500">
                                    High priority holds
                                </p>
                            </div>
                        </div>
                    </div>

                    <div class="bg-orange-50 p-4 rounded-lg">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <div
                                    class="w-8 h-8 bg-orange-500 rounded-full flex items-center justify-center"
                                >
                                    <span
                                        class="text-white text-sm font-bold"
                                        >{{ stats.overdue || 0 }}</span
                                    >
                                </div>
                            </div>
                            <div class="ml-3">
                                <p class="text-sm font-medium text-orange-600">
                                    Overdue
                                </p>
                                <p class="text-xs text-orange-500">
                                    Past deadline
                                </p>
                            </div>
                        </div>
                    </div>

                    <div class="bg-blue-50 p-4 rounded-lg">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <div
                                    class="w-8 h-8 bg-blue-500 rounded-full flex items-center justify-center"
                                >
                                    <span
                                        class="text-white text-sm font-bold"
                                        >{{ stats.recent || 0 }}</span
                                    >
                                </div>
                            </div>
                            <div class="ml-3">
                                <p class="text-sm font-medium text-blue-600">
                                    Recent
                                </p>
                                <p class="text-xs text-blue-500">Last 7 days</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Filters and Categories -->
                <div class="bg-gray-50 p-4 rounded-lg mb-6">
                    <!-- Hold Reason Tabs -->
                    <div class="flex flex-wrap gap-1 mb-4">
                        <button
                            @click="activeCategory = 'all'"
                            :class="
                                activeCategory === 'all'
                                    ? 'bg-white text-indigo-600 shadow'
                                    : 'text-gray-500 hover:text-gray-700'
                            "
                            class="px-3 py-2 text-sm font-medium rounded-md"
                        >
                            All Hold ({{ stats.total || 0 }})
                        </button>
                        <button
                            @click="activeCategory = 'missing_info'"
                            :class="
                                activeCategory === 'missing_info'
                                    ? 'bg-white text-indigo-600 shadow'
                                    : 'text-gray-500 hover:text-gray-700'
                            "
                            class="px-3 py-2 text-sm font-medium rounded-md"
                        >
                            Missing Info ({{ stats.missing_info || 0 }})
                        </button>
                        <button
                            @click="activeCategory = 'clarification'"
                            :class="
                                activeCategory === 'clarification'
                                    ? 'bg-white text-indigo-600 shadow'
                                    : 'text-gray-500 hover:text-gray-700'
                            "
                            class="px-3 py-2 text-sm font-medium rounded-md"
                        >
                            Clarification ({{ stats.clarification || 0 }})
                        </button>
                        <button
                            @click="activeCategory = 'approval_pending'"
                            :class="
                                activeCategory === 'approval_pending'
                                    ? 'bg-white text-indigo-600 shadow'
                                    : 'text-gray-500 hover:text-gray-700'
                            "
                            class="px-3 py-2 text-sm font-medium rounded-md"
                        >
                            Approval Pending ({{ stats.approval_pending || 0 }})
                        </button>
                        <button
                            @click="activeCategory = 'technical_issue'"
                            :class="
                                activeCategory === 'technical_issue'
                                    ? 'bg-white text-indigo-600 shadow'
                                    : 'text-gray-500 hover:text-gray-700'
                            "
                            class="px-3 py-2 text-sm font-medium rounded-md"
                        >
                            Technical Issue ({{ stats.technical_issue || 0 }})
                        </button>
                    </div>

                    <!-- Filters -->
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
                                >Hold Duration</label
                            >
                            <select
                                v-model="filters.hold_duration"
                                @change="applyFilters"
                                class="w-full border-gray-300 rounded-md"
                            >
                                <option value="">All Durations</option>
                                <option value="1">1 day</option>
                                <option value="3">3 days</option>
                                <option value="7">1 week</option>
                                <option value="30">1 month</option>
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
                                v-if="canReleaseHold()"
                                @click="bulkRelease"
                                class="px-3 py-1 text-sm bg-green-600 text-white rounded hover:bg-green-700"
                            >
                                Release Hold
                            </button>
                            <button
                                v-if="canUpdateHold()"
                                @click="bulkUpdateHold"
                                class="px-3 py-1 text-sm bg-blue-600 text-white rounded hover:bg-blue-700"
                            >
                                Update Hold
                            </button>
                            <button
                                v-if="canEscalateDocuments()"
                                @click="bulkEscalate"
                                class="px-3 py-1 text-sm bg-orange-600 text-white rounded hover:bg-orange-700"
                            >
                                Escalate
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
                                    Hold Reason
                                </th>
                                <th
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
                                >
                                    Hold Duration
                                </th>
                                <th
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
                                >
                                    Assigned To
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
                                    'bg-orange-50':
                                        getHoldDuration(document) > 7,
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
                                        <!-- Status indicators -->
                                        <div class="flex items-center mt-2 space-x-2">
                                            <span
                                                class="px-2 py-1 text-xs font-medium rounded-full bg-yellow-100 text-yellow-800"
                                            >
                                                On Hold
                                            </span>
                                            <span
                                                v-if="getHoldDuration(document) > 7"
                                                class="px-2 py-1 text-xs font-medium rounded-full bg-red-100 text-red-800"
                                            >
                                                Long Hold
                                            </span>
                                            <span
                                                v-else-if="getHoldDuration(document) > 3"
                                                class="px-2 py-1 text-xs font-medium rounded-full bg-orange-100 text-orange-800"
                                            >
                                                Extended Hold
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
                                            {{ getHoldReason(document) }}
                                        </div>
                                        <div
                                            class="text-xs text-gray-500 mt-1 max-w-xs truncate"
                                        >
                                            {{ getHoldNotes(document) }}
                                        </div>
                                    </div>
                                </td>
                                <td
                                    class="px-6 py-4 whitespace-nowrap text-sm text-gray-900"
                                >
                                    <div>
                                        <div
                                            :class="{
                                                'text-red-600 font-medium':
                                                    getHoldDuration(document) >
                                                    7,
                                                'text-orange-600':
                                                    getHoldDuration(document) >
                                                    3,
                                            }"
                                        >
                                            {{ getHoldDuration(document) }} days
                                        </div>
                                        <div class="text-xs text-gray-500">
                                            Since
                                            {{
                                                formatDate(
                                                    getHoldDate(document)
                                                )
                                            }}
                                        </div>
                                    </div>
                                </td>
                                <td
                                    class="px-6 py-4 whitespace-nowrap text-sm text-gray-900"
                                >
                                    <div v-if="document.assigned_to_user">
                                        <div class="font-medium">
                                            {{ document.assigned_to_user.name }}
                                        </div>
                                        <div class="text-xs text-gray-500">
                                            {{
                                                document.assigned_to_user.email
                                            }}
                                        </div>
                                    </div>
                                    <span v-else class="text-gray-400"
                                        >Unassigned</span
                                    >
                                </td>
                                <td
                                    class="px-6 py-4 whitespace-nowrap text-sm font-medium"
                                >
                                    <div class="flex space-x-2">
                                        <router-link
                                            :to="`/documents/${document.id}`"
                                            class="text-indigo-600 hover:text-indigo-900"
                                        >
                                            View
                                        </router-link>
                                        <button
                                            @click="releaseHold(document)"
                                            class="text-green-600 hover:text-green-900"
                                        >
                                            Release
                                        </button>
                                        <button
                                            @click="updateHold(document)"
                                            class="text-blue-600 hover:text-blue-900"
                                        >
                                            Update
                                        </button>
                                        <button
                                            @click="escalateDocument(document)"
                                            class="text-orange-600 hover:text-orange-900"
                                        >
                                            Escalate
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
                    <div class="text-gray-400 text-lg mb-2">⏸️</div>
                    <h3 class="text-lg font-medium text-gray-900 mb-2">
                        No documents on hold
                    </h3>
                    <p class="text-gray-500">
                        All documents are currently in active processing.
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

        <!-- Bulk Action Modal -->
        <div
            v-if="showBulkActionModal"
            class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50"
        >
            <div
                class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white"
            >
                <div class="mt-3">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">
                        Bulk Action for {{ selectedDocuments.length }} Documents
                    </h3>
                    <form @submit.prevent="processBulkAction">
                        <div class="mb-4">
                            <label
                                class="block text-sm font-medium text-gray-700 mb-2"
                                >Action</label
                            >
                            <select
                                v-model="bulkActionForm.action"
                                required
                                class="w-full border-gray-300 rounded-md"
                            >
                                <option value="">Select Action</option>
                                <option value="release">Release Hold</option>
                                <option value="update_hold">Update Hold</option>
                                <option value="escalate">Escalate</option>
                                <option value="reassign">Reassign</option>
                            </select>
                        </div>

                        <div
                            v-if="bulkActionForm.action === 'update_hold'"
                            class="mb-4"
                        >
                            <label
                                class="block text-sm font-medium text-gray-700 mb-2"
                                >Hold Reason</label
                            >
                            <select
                                v-model="bulkActionForm.hold_reason"
                                required
                                class="w-full border-gray-300 rounded-md"
                            >
                                <option value="">Select Reason</option>
                                <option value="missing_info">
                                    Missing Information
                                </option>
                                <option value="clarification">
                                    Clarification Needed
                                </option>
                                <option value="approval_pending">
                                    Approval Pending
                                </option>
                                <option value="technical_issue">
                                    Technical Issue
                                </option>
                            </select>
                        </div>

                        <div
                            v-if="bulkActionForm.action === 'reassign'"
                            class="mb-4"
                        >
                            <label
                                class="block text-sm font-medium text-gray-700 mb-2"
                                >Assign To</label
                            >
                            <select
                                v-model="bulkActionForm.assigned_to"
                                required
                                class="w-full border-gray-300 rounded-md"
                            >
                                <option value="">Select User</option>
                                <option
                                    v-for="user in availableUsers"
                                    :key="user.id"
                                    :value="user.id"
                                >
                                    {{ user.name }}
                                </option>
                            </select>
                        </div>

                        <div class="mb-4">
                            <label
                                class="block text-sm font-medium text-gray-700 mb-2"
                                >Notes</label
                            >
                            <textarea
                                v-model="bulkActionForm.notes"
                                rows="3"
                                class="w-full border-gray-300 rounded-md"
                                placeholder="Add notes about this action..."
                            ></textarea>
                        </div>

                        <div class="flex justify-end space-x-3">
                            <button
                                type="button"
                                @click="showBulkActionModal = false"
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
    </div>
</template>

<script setup>
import { ref, reactive, onMounted, computed, watch } from "vue";
import axios from "axios";
import { useAuthStore } from "../stores/auth";
import { useWorkflowStore } from "../stores/workflow";

const authStore = useAuthStore();
const workflowStore = useWorkflowStore();

// State
const documents = ref([]);
const pagination = ref(null);
const loading = ref(false);
const selectedDocuments = ref([]);
const availableUsers = ref([]);
const stats = ref({});
const activeCategory = ref("all");

// Modal states
const showBulkActionModal = ref(false);
const bulkLoading = ref(false);

// Filters
const filters = reactive({
    priority: "",
    type: "",
    hold_duration: "",
    search: "",
});

// Forms
const bulkActionForm = reactive({
    action: "",
    hold_reason: "",
    assigned_to: "",
    notes: "",
});

// Computed properties
const isAllSelected = computed(() => {
    return (
        documents.value.length > 0 &&
        selectedDocuments.value.length === documents.value.length
    );
});

// Watch for category changes
watch(activeCategory, () => {
    fetchDocuments();
});

// Functions
const fetchDocuments = async (overrides = {}) => {
    loading.value = true;
    try {
        const params = {
            ...filters,
            ...overrides,
        };

        // Use dedicated on-hold endpoint
        const response = await axios.get("/api/documents/hold", { params });
        documents.value = response.data.data;
        pagination.value = response.data;
    } catch (error) {
        console.error("Error fetching documents:", error);
        alert("Error fetching documents. Please try again.");
    } finally {
        loading.value = false;
    }
};

const fetchStats = async () => {
    try {
        const response = await axios.get("/api/documents/hold");
        const allDocs = response.data.data || [];

        const categorize = (text = "") => {
            const t = text.toLowerCase();
            if (t.includes("missing") || t.includes("incomplete") || t.includes("supporting")) return "missing_info";
            if (t.includes("clarif") || t.includes("explain")) return "clarification";
            if (t.includes("approval") || t.includes("sign") || t.includes("authorize") || t.includes("pending")) return "approval_pending";
            if (t.includes("technical") || t.includes("issue") || t.includes("error") || t.includes("bug") || t.includes("system")) return "technical_issue";
            return "general";
        };

        const categoryCounts = {
            missing_info: 0,
            clarification: 0,
            approval_pending: 0,
            technical_issue: 0,
        };
        allDocs.forEach((doc) => {
            const key = categorize(doc.hold_reason);
            if (categoryCounts[key] !== undefined) categoryCounts[key]++;
        });

        stats.value = {
            total: response.data.total || (response.data.data ? response.data.data.length : 0),
            missing_info: categoryCounts.missing_info,
            clarification: categoryCounts.clarification,
            approval_pending: categoryCounts.approval_pending,
            technical_issue: categoryCounts.technical_issue,
            urgent: allDocs.filter((doc) => doc.priority === "urgent").length || 0,
            overdue: allDocs.filter((doc) => isOverdue(doc.deadline)).length || 0,
            recent:
                allDocs.filter((doc) => {
                    const holdDate = doc.hold_at || getHoldDate(doc);
                    return (
                        holdDate &&
                        new Date() - new Date(holdDate) <= 7 * 24 * 60 * 60 * 1000
                    );
                }).length || 0,
        };
    } catch (error) {
        console.error("Error fetching stats:", error);
    }
};

const fetchUsers = async () => {
    try {
        const response = await axios.get("/api/users");
        availableUsers.value = response.data.data;
    } catch (error) {
        console.error("Error fetching users:", error);
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
const releaseHold = async (document) => {
    const notes = prompt("Add notes about releasing this hold (optional):");

    try {
        await workflowStore.resumeDocument(document.id, notes || "Hold released");
        alert("Hold released successfully!");
        fetchDocuments();
        fetchStats();
    } catch (error) {
        console.error("Error releasing hold:", error);
        alert("Error releasing hold. Please try again.");
    }
};

const updateHold = async (document) => {
    // Open modal for single document update
    selectedDocuments.value = [document.id];
    bulkActionForm.action = "update_hold";
    showBulkActionModal.value = true;
};

const escalateDocument = async (document) => {
    const notes = prompt("Add escalation notes:");
    if (!notes) return;

    try {
        await axios.post("/api/documents/bulk-update-status", {
            document_ids: [document.id],
            status: "under_review",
            notes: `ESCALATED: ${notes}`,
        });

        // Could also assign to a supervisor or change priority
        await axios.put(`/api/documents/${document.id}`, {
            priority: "urgent",
        });

        alert("Document escalated successfully!");
        fetchDocuments();
        fetchStats();
    } catch (error) {
        console.error("Error escalating document:", error);
        alert("Error escalating document. Please try again.");
    }
};

// Bulk operations
const bulkRelease = async () => {
    if (
        !confirm(`Release hold on ${selectedDocuments.value.length} documents?`)
    ) {
        return;
    }

    const notes = prompt("Add notes about releasing these holds (optional):");

    bulkLoading.value = true;
    try {
        await Promise.all(
            selectedDocuments.value.map((id) =>
                workflowStore.resumeDocument(id, notes || "Bulk hold release")
            )
        );

        selectedDocuments.value = [];
        fetchDocuments();
        fetchStats();
        alert("Holds released successfully!");
    } catch (error) {
        console.error("Error releasing holds:", error);
        alert("Error releasing holds. Please try again.");
    } finally {
        bulkLoading.value = false;
    }
};

const bulkUpdateHold = () => {
    bulkActionForm.action = "update_hold";
    showBulkActionModal.value = true;
};

const bulkEscalate = async () => {
    const notes = prompt("Add escalation notes:");
    if (!notes) return;

    if (!confirm(`Escalate ${selectedDocuments.value.length} documents?`)) {
        return;
    }

    bulkLoading.value = true;
    try {
        await axios.post("/api/documents/bulk-update-status", {
            document_ids: selectedDocuments.value,
            status: "under_review",
            notes: `ESCALATED: ${notes}`,
        });

        // Update priority to urgent for escalated documents
        for (const docId of selectedDocuments.value) {
            await axios.put(`/api/documents/${docId}`, {
                priority: "urgent",
            });
        }

        selectedDocuments.value = [];
        fetchDocuments();
        fetchStats();
        alert("Documents escalated successfully!");
    } catch (error) {
        console.error("Error escalating documents:", error);
        alert("Error escalating documents. Please try again.");
    } finally {
        bulkLoading.value = false;
    }
};

const processBulkAction = async () => {
    if (!bulkActionForm.action) return;

    bulkLoading.value = true;
    try {
        if (bulkActionForm.action === "release") {
            await Promise.all(
                selectedDocuments.value.map((id) =>
                    workflowStore.resumeDocument(
                        id,
                        bulkActionForm.notes || "Bulk hold release"
                    )
                )
            );
        } else if (bulkActionForm.action === "update_hold") {
            const reason = bulkActionForm.hold_reason;
            const notes = bulkActionForm.notes || "Hold updated";
            await Promise.all(
                selectedDocuments.value.map((id) =>
                    workflowStore.holdDocument(id, reason, notes)
                )
            );
        } else if (bulkActionForm.action === "escalate") {
            await axios.post("/api/documents/bulk-update-status", {
                document_ids: selectedDocuments.value,
                status: "under_review",
                notes: `ESCALATED: ${
                    bulkActionForm.notes || "Documents escalated"
                }`,
            });

            // Update priority to urgent
            for (const docId of selectedDocuments.value) {
                await axios.put(`/api/documents/${docId}`, {
                    priority: "urgent",
                });
            }
        } else if (bulkActionForm.action === "reassign") {
            await axios.post("/api/documents/bulk-assign", {
                document_ids: selectedDocuments.value,
                assigned_to: bulkActionForm.assigned_to,
                notes: bulkActionForm.notes || "Reassigned from hold queue",
            });
        }

        showBulkActionModal.value = false;
        bulkActionForm.action = "";
        bulkActionForm.hold_reason = "";
        bulkActionForm.assigned_to = "";
        bulkActionForm.notes = "";
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
const getHoldReason = (document) => {
    const text = (document.hold_reason || "").toLowerCase();
    if (text.includes("missing") || text.includes("incomplete") || text.includes("supporting")) return "Missing Information";
    if (text.includes("clarif") || text.includes("explain")) return "Clarification Needed";
    if (text.includes("approval") || text.includes("sign") || text.includes("authorize") || text.includes("pending")) return "Approval Pending";
    if (text.includes("technical") || text.includes("issue") || text.includes("error") || text.includes("bug") || text.includes("system")) return "Technical Issue";
    return "General Hold";
};

const getHoldNotes = (document) => {
    const notes = document.hold_reason || "";
    if (!notes) return "On hold";
    const trimmed = notes.substring(0, 50);
    return trimmed + (notes.length > 50 ? "..." : "");
};

const getLatestHoldNote = (document) => {
    // Use model fields for on-hold details
    return document.hold_reason || "";
};

const getHoldDate = (document) => {
    // Use hold_at when available
    return document.hold_at || document.updated_at;
};

const getHoldDuration = (document) => {
    const holdDate = getHoldDate(document);
    if (!holdDate) return 0;

    const now = new Date();
    const hold = new Date(holdDate);
    return Math.floor((now - hold) / (1000 * 60 * 60 * 24));
};

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

const isOverdue = (deadline) => {
    if (!deadline) return false;
    return new Date(deadline) < new Date();
};

// Access control functions
const canReleaseHold = () => {
    return authStore.can('release hold') || authStore.hasRole('admin');
};

const canUpdateHold = () => {
    return authStore.can('update hold') || authStore.hasRole('admin');
};

const canEscalateDocuments = () => {
    return authStore.can('escalate documents') || authStore.hasRole('admin');
};

// Initialize
onMounted(() => {
    fetchDocuments();
    fetchStats();
    fetchUsers();
});
</script>
