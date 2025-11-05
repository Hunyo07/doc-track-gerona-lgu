<template>
    <div class="space-y-6">
        <div class="bg-white shadow rounded-lg">
            <div class="px-4 py-5 sm:p-6">
                <div class="flex justify-between items-center mb-6">
                    <div>
                        <h1 class="text-2xl font-bold text-gray-900">
                            Received Documents
                        </h1>
                        <p class="text-sm text-gray-600 mt-1">
                            Documents assigned to you or your department
                            requiring action
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
                    <div class="bg-blue-50 p-4 rounded-lg">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <div
                                    class="w-8 h-8 bg-blue-500 rounded-full flex items-center justify-center"
                                >
                                    <span
                                        class="text-white text-sm font-bold"
                                        >{{ stats.assigned_to_me || 0 }}</span
                                    >
                                </div>
                            </div>
                            <div class="ml-3">
                                <p class="text-sm font-medium text-blue-600">
                                    Assigned to Me
                                </p>
                                <p class="text-xs text-blue-500">
                                    Personal assignments
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
                                        >{{ stats.department || 0 }}</span
                                    >
                                </div>
                            </div>
                            <div class="ml-3">
                                <p class="text-sm font-medium text-green-600">
                                    Department
                                </p>
                                <p class="text-xs text-green-500">
                                    Department queue
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
                </div>

                <!-- Filters and Tabs -->
                <div class="bg-gray-50 p-4 rounded-lg mb-6">
                    <!-- Assignment Tabs -->
                    <div class="flex space-x-1 mb-4">
                        <button
                            @click="activeTab = 'all'"
                            :class="
                                activeTab === 'all'
                                    ? 'bg-white text-indigo-600 shadow'
                                    : 'text-gray-500 hover:text-gray-700'
                            "
                            class="px-3 py-2 text-sm font-medium rounded-md"
                        >
                            All Received ({{ stats.total || 0 }})
                        </button>
                        <button
                            @click="activeTab = 'personal'"
                            :class="
                                activeTab === 'personal'
                                    ? 'bg-white text-indigo-600 shadow'
                                    : 'text-gray-500 hover:text-gray-700'
                            "
                            class="px-3 py-2 text-sm font-medium rounded-md"
                        >
                            Personal ({{ stats.assigned_to_me || 0 }})
                        </button>
                        <button
                            @click="activeTab = 'department'"
                            :class="
                                activeTab === 'department'
                                    ? 'bg-white text-indigo-600 shadow'
                                    : 'text-gray-500 hover:text-gray-700'
                            "
                            class="px-3 py-2 text-sm font-medium rounded-md"
                        >
                            Department ({{ stats.department || 0 }})
                        </button>
                    </div>

                    <!-- Filters -->
                    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                        <div>
                            <label
                                class="block text-sm font-medium text-gray-700 mb-1"
                                >Status</label
                            >
                            <select
                                v-model="filters.status"
                                @change="applyFilters"
                                class="w-full border-gray-300 rounded-md"
                            >
                                <option value="">All Status</option>
                                <option value="received">
                                    Received
                                </option>
                                <option value="approved">Approved</option>
                                <option value="rejected">Rejected</option>
                            </select>
                        </div>

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
                                @click="bulkApprove"
                                v-if="canApproveDocuments()"
                                class="px-3 py-1 text-sm bg-green-600 text-white rounded hover:bg-green-700"
                            >
                                Approve
                            </button>
                            <button
                                @click="bulkReject"
                                v-if="canRejectDocuments()"
                                class="px-3 py-1 text-sm bg-red-600 text-white rounded hover:bg-red-700"
                            >
                                Reject
                            </button>
                            <button
                                @click="bulkForward"
                                v-if="canForwardDocuments()"
                                class="px-3 py-1 text-sm bg-blue-600 text-white rounded hover:bg-blue-700"
                            >
                                Forward
                            </button>
                            <button
                                @click="bulkHold"
                                v-if="canHoldDocuments()"
                                class="px-3 py-1 text-sm bg-yellow-600 text-white rounded hover:bg-yellow-700"
                            >
                                Hold
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
                                    Status
                                </th>
                                <th
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
                                >
                                    Priority
                                </th>
                                <th
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
                                >
                                    Assignment
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
                                        :class="getStatusClass(document.status)"
                                        class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full"
                                    >
                                        {{ getStageLabel(document) }}
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
                                        <div
                                            v-if="document.assigned_to_user"
                                            class="font-medium text-blue-600"
                                        >
                                            👤
                                            {{ document.assigned_to_user.name }}
                                        </div>
                                        <div
                                            v-else-if="
                                                document.current_department
                                            "
                                            class="font-medium text-green-600"
                                        >
                                            🏢
                                            {{
                                                document.current_department.name
                                            }}
                                        </div>
                                        <div class="text-xs text-gray-500">
                                            {{
                                                formatDate(document.updated_at)
                                            }}
                                        </div>
                                    </div>
                                </td>
                                <!-- <td
                                    class="px-6 py-4 whitespace-nowrap text-sm text-gray-900"
                                >
                                    <div v-if="document.received_by_user">
                                        <div class="font-medium text-blue-600">
                                            👤 {{ document.received_by_user.name }}
                                        </div>
                                        <div class="text-xs text-gray-500">
                                            {{ formatDate(document.received_at) }}
                                        </div>
                                    </div>
                                    <span v-else class="text-gray-400">
                                        Not received yet
                                    </span>
                                </td> -->
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
                                        <router-link
                                            :to="`/documents/${document.id}`"
                                            class="text-indigo-600 hover:text-indigo-900"
                                        >
                                            View
                                        </router-link>
                                        <button
                                            @click="approveDocument(document)"
                                            v-if="document.status === 'received'"
                                            class="text-green-600 hover:text-green-900"
                                        >
                                            Approve
                                        </button>
                                        <button
                                            @click="signDocument(document)"
                                            v-if="document.status === 'received'"
                                            class="text-purple-600 hover:text-purple-900"
                                        >
                                            Sign (PNPKI)
                                        </button>
                                        <button
                                            @click="rejectDocument(document)"
                                            v-if="document.status === 'received'"
                                            class="text-red-600 hover:text-red-900"
                                        >
                                            Reject
                                        </button>
                                        <button
                                            @click="forwardDocument(document)"
                                            class="text-blue-600 hover:text-blue-900"
                                        >
                                            Forward
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
                    <div class="text-gray-400 text-lg mb-2">📋</div>
                    <h3 class="text-lg font-medium text-gray-900 mb-2">
                        No documents received
                    </h3>
                    <p class="text-gray-500">
                        You don't have any documents assigned to you or your
                        department.
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
                                <option value="approve">Approve</option>
                                <option value="reject">Reject</option>
                                <option value="forward">
                                    Forward to Department
                                </option>
                                <option value="hold">Put on Hold</option>
                            </select>
                        </div>

                        <div
                            v-if="bulkActionForm.action === 'forward'"
                            class="mb-4"
                        >
                            <label
                                class="block text-sm font-medium text-gray-700 mb-2"
                                >Forward To</label
                            >
                            <select
                                v-model="bulkActionForm.forward_to"
                                required
                                class="w-full border-gray-300 rounded-md"
                            >
                                <option value="">Select Department</option>
                                <option
                                    v-for="dept in availableDepartments"
                                    :key="dept.id"
                                    :value="dept.id"
                                >
                                    {{ dept.name }}
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
const availableDepartments = ref([]);
const stats = ref({});
const activeTab = ref("all");

// Modal states
const showBulkActionModal = ref(false);
const bulkLoading = ref(false);

// Filters
const filters = reactive({
    status: "",
    priority: "",
    type: "",
    search: "",
});

// Forms
const bulkActionForm = reactive({
    action: "",
    forward_to: "",
    notes: "",
});

// Computed properties
const isAllSelected = computed(() => {
    return (
        documents.value.length > 0 &&
        selectedDocuments.value.length === documents.value.length
    );
});

// Watch for tab changes
watch(activeTab, () => {
    fetchDocuments();
});

// Functions
const fetchDocuments = async (extraParams = {}) => {
    loading.value = true;
    try {
        const params = {
            search: filters.search || "",
            status: filters.status || "",
            priority: filters.priority || "",
            type: filters.type || "",
            page: extraParams.page || undefined,
        };

        const response = await axios.get("/api/documents/received", { params });
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
        const response = await axios.get("/api/documents-stats");
        const data = response.data;

        // Creator-only stats
        const baseParams = {
            created_by: authStore.user?.id,
            status_in: "received,approved",
        };
        const deptParams = {
            ...baseParams,
            current_department_id: authStore.user?.department_id,
        };

        const [allResponse, deptResponse] = await Promise.all([
            axios.get("/api/documents", { params: baseParams }),
            axios.get("/api/documents", { params: deptParams }),
        ]);

        stats.value = {
            assigned_to_me: allResponse.data.total || 0,
            department: deptResponse.data.total || 0,
            total: allResponse.data.total || 0,
            urgent: data.by_priority?.urgent || 0,
            overdue: data.overdue || 0,
        };
    } catch (error) {
        console.error("Error fetching stats:", error);
    }
};

const fetchDepartments = async () => {
    try {
        const response = await axios.get("/api/departments");
        availableDepartments.value = response.data;
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
const approveDocument = async (document) => {
    const remarks = prompt("Add approval remarks (optional):") || "";
    try {
        await workflowStore.approveDocument(document.id, remarks);
        alert("Document approved successfully!");
        fetchDocuments();
        fetchStats();
    } catch (error) {
        console.error("Error approving document:", error);
        alert("Error approving document. Please try again.");
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

const signDocument = async (document) => {
    const remarks = prompt("Add sign remarks (optional):") || "";
    try {
        await workflowStore.signDocument(document.id, remarks);
        alert("Document signed successfully!");
        fetchDocuments();
        fetchStats();
    } catch (error) {
        console.error("Error signing document:", error);
        alert("Error signing document. Please try again.");
    }
};

const forwardDocument = async (document) => {
    // This would open a modal or prompt for forwarding details
    bulkActionForm.action = "forward";
    selectedDocuments.value = [document.id];
    showBulkActionModal.value = true;
};

// Bulk operations
const bulkApprove = async () => {
    if (!confirm(`Approve ${selectedDocuments.value.length} documents?`)) {
        return;
    }

    const notes = prompt("Add approval notes (optional):") || "Bulk approved";

    bulkLoading.value = true;
    try {
        await Promise.all(
            selectedDocuments.value.map((id) => workflowStore.approveDocument(id, notes))
        );

        selectedDocuments.value = [];
        fetchDocuments();
        fetchStats();
        alert("Documents approved successfully!");
    } catch (error) {
        console.error("Error approving documents:", error);
        alert("Error approving documents. Please try again.");
    } finally {
        bulkLoading.value = false;
    }
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
        await Promise.all(
            selectedDocuments.value.map((id) => workflowStore.rejectDocument(id, reason))
        );

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

const bulkForward = () => {
    bulkActionForm.action = "forward";
    showBulkActionModal.value = true;
};

const bulkHold = async () => {
    const reason = prompt(
        "Please provide a reason for putting these documents on hold:"
    );
    if (!reason) return;

    bulkLoading.value = true;
    try {
        await Promise.all(
            selectedDocuments.value.map((id) => workflowStore.holdDocument(id, reason, `Bulk hold: ${reason}`))
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

const processBulkAction = async () => {
    if (!bulkActionForm.action) return;

    bulkLoading.value = true;
    try {
        if (bulkActionForm.action === "approve") {
            await Promise.all(
                selectedDocuments.value.map((id) => workflowStore.approveDocument(id, bulkActionForm.notes || "Bulk approved"))
            );
        } else if (bulkActionForm.action === "reject") {
            await Promise.all(
                selectedDocuments.value.map((id) => workflowStore.rejectDocument(id, bulkActionForm.notes || "Bulk rejected"))
            );
        } else if (bulkActionForm.action === "forward") {
            // Update current department
            await axios.post("/api/documents/bulk-forward", {
                document_ids: selectedDocuments.value,
                to_office_id: bulkActionForm.forward_to,
                remarks: bulkActionForm.notes || "Forwarded to department",
            });
        } else if (bulkActionForm.action === "hold") {
            await Promise.all(
                selectedDocuments.value.map((id) => workflowStore.holdDocument(id, bulkActionForm.notes || "Put on hold", bulkActionForm.notes || "Put on hold"))
            );
        }

        showBulkActionModal.value = false;
        bulkActionForm.action = "";
        bulkActionForm.forward_to = "";
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

// Access control functions
const canApproveDocuments = () => {
    return authStore.can("approve documents") || authStore.hasRole("admin");
};

const canRejectDocuments = () => {
    return authStore.can("reject documents") || authStore.hasRole("admin");
};

const canForwardDocuments = () => {
    return authStore.can("forward documents") || authStore.hasRole("admin");
};

const canHoldDocuments = () => {
    return authStore.can("hold documents") || authStore.hasRole("admin");
};

// Utility functions
const capitalize = (s) =>
    s ? s.charAt(0).toUpperCase() + s.slice(1).replace(/_/g, " ") : "";
const getStageLabel = (document) => {
    const dept = (document.current_department?.name || "").toLowerCase();
    const status = document.status;

    if (status === "completed") return "Completed";
    if (status === "rejected") return "Rejected";
    if (status === "draft") return "Draft";

    if (dept.includes("budget")) {
        if (status === "submitted") return "For Review";
        if (status === "received") return "Under Review";
        if (status === "approved") return "Signed by Budget Officer";
    }

    if (dept.includes("account")) {
        if (status === "submitted") return "For Obligation";
        if (status === "received") return "Under Review";
        if (status === "approved") return "Obligation Created";
    }

    if (dept.includes("treasury")) {
        if (status === "submitted") return "For Approval";
        if (status === "received") return "Under Review";
        if (status === "approved") return "Approved for Payment";
    }

    return capitalize(status);
};
const getStatusClass = (status) => {
    const classes = {
        draft: "bg-gray-100 text-gray-800",
        submitted: "bg-blue-100 text-blue-800",
        received: "bg-yellow-100 text-yellow-800",
        approved: "bg-green-100 text-green-800",
        rejected: "bg-red-100 text-red-800",
        on_hold: "bg-orange-100 text-orange-800",
        completed: "bg-purple-100 text-purple-800",
    };
    return classes[status] || "bg-gray-100 text-gray-800";
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

const formatStatus = (status) => {
    const statusMap = {
        received: "Under Review",
        approved: "Approved",
        rejected: "Rejected",
    };
    return statusMap[status] || status;
};

const formatDate = (date) => {
    return new Date(date).toLocaleDateString();
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
