<template>
    <div class="space-y-6">
        <div class="bg-white shadow rounded-lg">
            <div class="px-4 py-5 sm:p-6">
                <div class="flex justify-between items-center mb-6">
                    <div>
                        <h1 class="text-2xl font-bold text-gray-900">
                            Outgoing Documents
                        </h1>
                        <p class="text-sm text-gray-600 mt-1">
                            Documents being sent to other departments or
                            external entities
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
                                        >{{ stats.pending || 0 }}</span
                                    >
                                </div>
                            </div>
                            <div class="ml-3">
                                <p class="text-sm font-medium text-blue-600">
                                    Pending Send
                                </p>
                                <p class="text-xs text-blue-500">
                                    Ready to send
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
                                        >{{ stats.in_transit || 0 }}</span
                                    >
                                </div>
                            </div>
                            <div class="ml-3">
                                <p class="text-sm font-medium text-yellow-600">
                                    In Transit
                                </p>
                                <p class="text-xs text-yellow-500">
                                    Being delivered
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
                                        >{{ stats.delivered || 0 }}</span
                                    >
                                </div>
                            </div>
                            <div class="ml-3">
                                <p class="text-sm font-medium text-green-600">
                                    Delivered
                                </p>
                                <p class="text-xs text-green-500">
                                    Successfully sent
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
                                    High priority
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Filters and Tabs -->
                <div class="bg-gray-50 p-4 rounded-lg mb-6">
                    <!-- Status Tabs -->
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
                            All Outgoing ({{ stats.total || 0 }})
                        </button>
                        <button
                            @click="activeTab = 'pending'"
                            :class="
                                activeTab === 'pending'
                                    ? 'bg-white text-indigo-600 shadow'
                                    : 'text-gray-500 hover:text-gray-700'
                            "
                            class="px-3 py-2 text-sm font-medium rounded-md"
                        >
                            Pending ({{ stats.pending || 0 }})
                        </button>
                        <button
                            @click="activeTab = 'in_transit'"
                            :class="
                                activeTab === 'in_transit'
                                    ? 'bg-white text-indigo-600 shadow'
                                    : 'text-gray-500 hover:text-gray-700'
                            "
                            class="px-3 py-2 text-sm font-medium rounded-md"
                        >
                            In Transit ({{ stats.in_transit || 0 }})
                        </button>
                        <button
                            @click="activeTab = 'delivered'"
                            :class="
                                activeTab === 'delivered'
                                    ? 'bg-white text-indigo-600 shadow'
                                    : 'text-gray-500 hover:text-gray-700'
                            "
                            class="px-3 py-2 text-sm font-medium rounded-md"
                        >
                            Delivered ({{ stats.delivered || 0 }})
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
                                >Destination</label
                            >
                            <select
                                v-model="filters.destination"
                                @change="applyFilters"
                                class="w-full border-gray-300 rounded-md"
                            >
                                <option value="">All Destinations</option>
                                <option value="internal">
                                    Internal Department
                                </option>
                                <option value="external">
                                    External Entity
                                </option>
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
                                v-if="canSendDocuments()"
                                @click="bulkSend"
                                class="px-3 py-1 text-sm bg-green-600 text-white rounded hover:bg-green-700"
                            >
                                Send
                            </button>
                            <button
                                v-if="canUpdateDocuments()"
                                @click="bulkMarkDelivered"
                                class="px-3 py-1 text-sm bg-blue-600 text-white rounded hover:bg-blue-700"
                            >
                                Mark Delivered
                            </button>
                            <button
                                v-if="canRecallDocuments()"
                                @click="bulkRecall"
                                class="px-3 py-1 text-sm bg-yellow-600 text-white rounded hover:bg-yellow-700"
                            >
                                Recall
                            </button>
                            <button
                                v-if="canAssignDocuments()"
                                @click="bulkAssign"
                                class="px-3 py-1 text-sm bg-indigo-600 text-white rounded hover:bg-indigo-700"
                            >
                                Assign
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
                                    Destination
                                </th>
                                <th
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
                                >
                                    Status
                                </th>
                                <th
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
                                >
                                    Sent Date
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
                                    'bg-yellow-50':
                                        document.priority === 'urgent',
                                    'bg-green-50':
                                        getOutgoingStatus(document) ===
                                        'delivered',
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
                                            {{
                                                getDestinationName(document) ||
                                                "Not specified"
                                            }}
                                        </div>
                                        <div class="text-xs text-gray-500">
                                            {{
                                                getDestinationType(document) ||
                                                "Unknown"
                                            }}
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span
                                        :class="
                                            getOutgoingStatusClass(
                                                getOutgoingStatus(document)
                                            )
                                        "
                                        class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full"
                                    >
                                        {{
                                            formatOutgoingStatus(
                                                getOutgoingStatus(document)
                                            )
                                        }}
                                    </span>
                                </td>
                                <td
                                    class="px-6 py-4 whitespace-nowrap text-sm text-gray-900"
                                >
                                    <div v-if="document.sent_at">
                                        <div>
                                            {{ formatDate(document.sent_at) }}
                                        </div>
                                        <div class="text-xs text-gray-500">
                                            {{ formatTime(document.sent_at) }}
                                        </div>
                                    </div>
                                    <span v-else class="text-gray-400"
                                        >Not sent</span
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
                                            @click="sendDocument(document)"
                                            v-if="
                                                getOutgoingStatus(document) ===
                                                'pending'
                                            "
                                            class="text-green-600 hover:text-green-900"
                                        >
                                            Send
                                        </button>
                                        <button
                                            @click="markDelivered(document)"
                                            v-if="
                                                getOutgoingStatus(document) ===
                                                'in_transit'
                                            "
                                            class="text-blue-600 hover:text-blue-900"
                                        >
                                            Mark Delivered
                                        </button>
                                        <button
                                            @click="recallDocument(document)"
                                            v-if="
                                                getOutgoingStatus(document) ===
                                                'in_transit'
                                            "
                                            class="text-yellow-600 hover:text-yellow-900"
                                        >
                                            Recall
                                        </button>
                                        <button
                                            @click="trackDocument(document)"
                                            class="text-purple-600 hover:text-purple-900"
                                        >
                                            Track
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
                    <div class="text-gray-400 text-lg mb-2">📤</div>
                    <h3 class="text-lg font-medium text-gray-900 mb-2">
                        No outgoing documents
                    </h3>
                    <p class="text-gray-500">
                        No documents are currently being sent out.
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
                                <option value="send">Send Documents</option>
                                <option value="mark_delivered">
                                    Mark as Delivered
                                </option>
                                <option value="recall">Recall Documents</option>
                                <option value="assign">
                                    Send to Department
                                </option>
                            </select>
                        </div>

                        <div
                            v-if="bulkActionForm.action === 'send'"
                            class="mb-4"
                        >
                            <label
                                class="block text-sm font-medium text-gray-700 mb-2"
                                >Destination</label
                            >
                            <select
                                v-model="bulkActionForm.destination_type"
                                required
                                class="w-full border-gray-300 rounded-md mb-2"
                            >
                                <option value="">
                                    Select Destination Type
                                </option>
                                <option value="internal">
                                    Internal Department
                                </option>
                                <option value="external">
                                    External Entity
                                </option>
                            </select>

                            <div
                                v-if="
                                    bulkActionForm.destination_type ===
                                    'internal'
                                "
                            >
                                <select
                                    v-model="bulkActionForm.department_id"
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

                            <div
                                v-if="
                                    bulkActionForm.destination_type ===
                                    'external'
                                "
                            >
                                <input
                                    v-model="bulkActionForm.external_recipient"
                                    type="text"
                                    required
                                    placeholder="External recipient name/organization"
                                    class="w-full border-gray-300 rounded-md"
                                />
                            </div>
                        </div>

                        <div
                            v-if="bulkActionForm.action === 'assign'"
                            class="mb-4"
                        >
                            <label
                                class="block text-sm font-medium text-gray-700 mb-2"
                                >Send To Department</label
                            >
                            <select
                                v-model="bulkActionForm.department_id"
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
import { useDocumentStore } from "../stores/documents";
import { useWorkflowStore } from "../stores/workflow";
import { useAuthStore } from "../stores/auth";
import StatusBadge from "../components/StatusBadge.vue";

const documentStore = useDocumentStore();
const workflowStore = useWorkflowStore();
const authStore = useAuthStore();

// State
const documents = computed(() => documentStore.outgoing);
const loading = computed(() => documentStore.loading);
const selectedDocuments = ref([]);
const availableDepartments = ref([]);
const availableUsers = ref([]);
const stats = ref({});
const activeTab = ref("all");

// Modal states
const showBulkActionModal = ref(false);
const bulkLoading = ref(false);

// Filters
const filters = reactive({
    priority: "",
    type: "",
    destination: "",
    search: "",
});

// Forms
const bulkActionForm = reactive({
    action: "",
    destination_type: "",
    department_id: "",
    external_recipient: "",
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
const fetchDocuments = async () => {
    const params = { ...filters };
    
    // Filter based on active tab
    if (activeTab.value === "pending") {
        // Only drafts should appear as pending; submitted are already sent
        params.status_in = "draft";
    } else if (activeTab.value === "in_transit") {
        // Documents sent but not yet acknowledged by recipient
        params.status_in = "submitted,approved";
    } else if (activeTab.value === "delivered") {
        // Consider documents delivered once recipient clicks Receive, or when completed
        params.status_in = "received,completed";
    }
    
    await documentStore.fetchOutgoing(params);
};

const fetchDepartments = async () => {
    try {
        availableDepartments.value = await documentStore.fetchDepartments();
    } catch (error) {
        console.error("Error fetching departments:", error);
    }
};

const fetchStats = async () => {
    try {
        // Get stats for outgoing documents
        const params = {
            status_in: "draft,submitted,approved,received,completed,under_review",
            created_by: authStore.user?.id,
        };

        const [
            allResponse,
            pendingResponse,
            transitResponse,
            deliveredResponse,
        ] = await Promise.all([
            axios.get("/api/documents", { params }),
            axios.get("/api/documents", {
                params: { ...params, status_in: "draft" },
            }),
            axios.get("/api/documents", {
                params: { ...params, status_in: "submitted,approved" },
            }),
            axios.get("/api/documents", {
                params: { ...params, status_in: "received,completed" },
            }),
        ]);

        stats.value = {
            total: allResponse.data.total || 0,
            pending: pendingResponse.data.total || 0,
            in_transit: transitResponse.data.total || 0,
            delivered: deliveredResponse.data.total || 0,
            urgent:
                allResponse.data.data?.filter(
                    (doc) => doc.priority === "urgent"
                ).length || 0,
        };
    } catch (error) {
        console.error("Error fetching stats:", error);
    }
};

// const fetchDepartments = async () => {
//     try {
//         const response = await axios.get("/api/departments");
//         availableDepartments.value = response.data;
//     } catch (error) {
//         console.error("Error fetching departments:", error);
//     }
// };

const applyFilters = () => {
    fetchDocuments();
};

const refreshDocuments = () => {
    fetchDocuments();
    fetchStats();
};

const changePage = (page) => {
    fetchDocuments({ ...filters, page });
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
const sendDocument = async (document) => {
    const toOfficeId = prompt("Enter destination office ID:");
    const remarks = prompt("Add remarks (optional):");
    const assignedUserIdInput = prompt("Assign to user ID (optional):");
    
    if (!toOfficeId) return;
    
    try {
        const assignedUserId = assignedUserIdInput ? parseInt(assignedUserIdInput) : null;
        await workflowStore.forwardDocument(document.id, parseInt(toOfficeId), remarks, assignedUserId);
        alert("Document forwarded successfully!");
        fetchDocuments();
        fetchStats();
    } catch (error) {
        console.error("Error forwarding document:", error);
        alert("Error forwarding document. Please try again.");
    }
};

const markDelivered = async (document) => {
    const notes = prompt("Add delivery confirmation notes (optional):");

    try {
        await axios.post("/api/documents/bulk-update-status", {
            document_ids: [document.id],
            status: "completed",
            notes: `DELIVERED: ${notes || "Document delivered successfully"}`,
        });

        alert("Document marked as delivered!");
        fetchDocuments();
        fetchStats();
    } catch (error) {
        console.error("Error marking document as delivered:", error);
        alert("Error marking document as delivered. Please try again.");
    }
};

const recallDocument = async (document) => {
    const reason = prompt(
        "Please provide a reason for recalling this document:"
    );
    if (!reason) return;

    try {
        await axios.post("/api/documents/bulk-update-status", {
            document_ids: [document.id],
            status: "under_review", // Move back to review
            notes: `RECALLED: ${reason}`,
        });

        alert("Document recalled successfully!");
        fetchDocuments();
        fetchStats();
    } catch (error) {
        console.error("Error recalling document:", error);
        alert("Error recalling document. Please try again.");
    }
};

const trackDocument = (document) => {
    // Navigate to tracking page or show tracking modal
    alert(
        `Tracking information for ${document.document_number} would be shown here.`
    );
};

// Bulk operations
const bulkSend = () => {
    bulkActionForm.action = "send";
    showBulkActionModal.value = true;
};

const bulkMarkDelivered = async () => {
    if (
        !confirm(
            `Mark ${selectedDocuments.value.length} documents as delivered?`
        )
    ) {
        return;
    }

    const notes = prompt("Add delivery confirmation notes (optional):");

    bulkLoading.value = true;
    try {
        await axios.post("/api/documents/bulk-update-status", {
            document_ids: selectedDocuments.value,
            status: "completed",
            notes: `DELIVERED: ${notes || "Documents delivered successfully"}`,
        });

        selectedDocuments.value = [];
        fetchDocuments();
        fetchStats();
        alert("Documents marked as delivered successfully!");
    } catch (error) {
        console.error("Error marking documents as delivered:", error);
        alert("Error marking documents as delivered. Please try again.");
    } finally {
        bulkLoading.value = false;
    }
};

const bulkRecall = async () => {
    const reason = prompt(
        "Please provide a reason for recalling these documents:"
    );
    if (!reason) return;

    if (!confirm(`Recall ${selectedDocuments.value.length} documents?`)) {
        return;
    }

    bulkLoading.value = true;
    try {
        await axios.post("/api/documents/bulk-update-status", {
            document_ids: selectedDocuments.value,
            status: "under_review",
            notes: `RECALLED: ${reason}`,
        });

        selectedDocuments.value = [];
        fetchDocuments();
        fetchStats();
        alert("Documents recalled successfully!");
    } catch (error) {
        console.error("Error recalling documents:", error);
        alert("Error recalling documents. Please try again.");
    } finally {
        bulkLoading.value = false;
    }
};

const bulkForward = async () => {
    const toOfficeId = prompt("Enter destination office ID:");
    const remarks = prompt("Add remarks (optional):");
    const assignedUserIdInput = prompt("Assign to user ID for all (optional):");
    
    if (!toOfficeId) return;
    
    bulkLoading.value = true;
    try {
        const assignedUserId = assignedUserIdInput ? parseInt(assignedUserIdInput) : null;
        await documentStore.bulkForward(selectedDocuments.value, parseInt(toOfficeId), remarks, assignedUserId);
        selectedDocuments.value = [];
        fetchDocuments();
        fetchStats();
        alert("Documents forwarded successfully!");
    } catch (error) {
        console.error("Error forwarding documents:", error);
        alert("Error forwarding documents. Please try again.");
    } finally {
        bulkLoading.value = false;
    }
};

const processBulkAction = async () => {
    if (!bulkActionForm.action) {
        alert("Please select an action.");
        return;
    }

    // Validate send action requirements
    if (bulkActionForm.action === "send") {
        if (!bulkActionForm.destination_type) {
            alert("Please select a destination type.");
            return;
        }
        if (
            bulkActionForm.destination_type === "internal" &&
            !bulkActionForm.department_id
        ) {
            alert("Please select a department.");
            return;
        }
        if (
            bulkActionForm.destination_type === "external" &&
            !bulkActionForm.external_recipient
        ) {
            alert("Please enter the external recipient.");
            return;
        }
    }

    // Validate assign action requirements
    if (bulkActionForm.action === "assign" && !bulkActionForm.department_id) {
        alert("Please select a department.");
        return;
    }

    bulkLoading.value = true;
    try {
        if (bulkActionForm.action === "send") {
            let destination = "";
            if (bulkActionForm.destination_type === "internal") {
                const dept = availableDepartments.value.find(
                    (d) => d.id == bulkActionForm.department_id
                );
                destination = `Internal: ${dept?.name}`;
            } else {
                destination = `External: ${bulkActionForm.external_recipient}`;
            }

            await axios.post("/api/documents/bulk-update-status", {
                document_ids: selectedDocuments.value,
                status: "approved", // Keep approved but add sent metadata
                notes: `SENT TO: ${destination}. ${bulkActionForm.notes || ""}`,
            });

            // Update metadata to track outgoing status
            for (const docId of selectedDocuments.value) {
                try {
                    // Get current document data first
                    const docResponse = await axios.get(
                        `/api/documents/${docId}`
                    );
                    const currentDoc = docResponse.data.data;

                    // Merge existing metadata with new outgoing metadata
                    const updatedMetadata = {
                        ...(currentDoc.metadata || {}),
                        outgoing_status: "in_transit",
                        sent_to: destination,
                        sent_at: new Date().toISOString(),
                    };

                    await axios.put(`/api/documents/${docId}`, {
                        metadata: updatedMetadata,
                    });
                } catch (error) {
                    console.error(
                        `Error updating metadata for document ${docId}:`,
                        error
                    );
                    // Continue with other documents even if one fails
                }
            }
        } else if (bulkActionForm.action === "mark_delivered") {
            await axios.post("/api/documents/bulk-update-status", {
                document_ids: selectedDocuments.value,
                status: "completed",
                notes: `DELIVERED: ${
                    bulkActionForm.notes || "Documents delivered successfully"
                }`,
            });
        } else if (bulkActionForm.action === "recall") {
            await axios.post("/api/documents/bulk-update-status", {
                document_ids: selectedDocuments.value,
                status: "under_review",
                notes: `RECALLED: ${
                    bulkActionForm.notes || "Documents recalled"
                }`,
            });
        } else if (bulkActionForm.action === "assign") {
            await axios.post("/api/documents/bulk-update-department", {
                document_ids: selectedDocuments.value,
                department_id: bulkActionForm.department_id,
                notes:
                    bulkActionForm.notes ||
                    "Documents sent to department via bulk action",
            });
        }

        showBulkActionModal.value = false;
        bulkActionForm.action = "";
        bulkActionForm.destination_type = "";
        bulkActionForm.department_id = "";
        bulkActionForm.external_recipient = "";
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
const getOutgoingStatus = (document) => {
    // Determine outgoing status based on document metadata or status
    if (document.metadata?.outgoing_status) {
        return document.metadata.outgoing_status;
    }

    // Fallback logic based on document status and notes
    if (document.status === "completed") {
        return "delivered";
    }
    // Consider received as delivered from the sender's perspective
    if (document.status === "received") {
        return "delivered";
    }
    // Sent but not yet acknowledged
    if (document.status === "submitted" || document.status === "approved") {
        return "in_transit";
    }
    // Only drafts should show pending
    if (document.status === "draft") {
        return "pending";
    }
    // Recall or review states
    if (document.status === "under_review") {
        return "recalled";
    }

    return "pending";
};

const getOutgoingStatusClass = (status) => {
    const classes = {
        pending: "bg-blue-100 text-blue-800",
        in_transit: "bg-yellow-100 text-yellow-800",
        delivered: "bg-green-100 text-green-800",
        recalled: "bg-red-100 text-red-800",
    };
    return classes[status] || "bg-gray-100 text-gray-800";
};

const formatOutgoingStatus = (status) => {
    const statusMap = {
        pending: "Pending Send",
        in_transit: "In Transit",
        delivered: "Delivered",
        recalled: "Recalled",
    };
    return statusMap[status] || status;
};

const getDestinationName = (document) => {
    if (document.metadata?.sent_to) {
        return document.metadata.sent_to;
    }
    return document.current_department?.name || "Not specified";
};

const getDestinationType = (document) => {
    if (document.metadata?.sent_to) {
        return document.metadata.sent_to.startsWith("Internal:")
            ? "Internal Department"
            : "External Entity";
    }
    return "Internal Department";
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

const formatTime = (date) => {
    return new Date(date).toLocaleTimeString([], {
        hour: "2-digit",
        minute: "2-digit",
    });
};

// Access control functions
const canSendDocuments = () => {
    return authStore.can("send documents") || authStore.hasRole("admin");
};

const canUpdateDocuments = () => {
    return authStore.can("update documents") || authStore.hasRole("admin");
};

const canRecallDocuments = () => {
    return authStore.can("recall documents") || authStore.hasRole("admin");
};

const canAssignDocuments = () => {
    return authStore.can("assign documents") || authStore.hasRole("admin");
};

const bulkAssign = () => {
    bulkActionForm.action = "assign";
    showBulkActionModal.value = true;
};

// Initialize
onMounted(() => {
    fetchDocuments();
    fetchStats();
    fetchDepartments();
});
</script>
