<template>
    <!-- Loading / Error States -->
    <div v-if="loading" class="space-y-6">
        <div class="animate-pulse bg-white shadow rounded-lg">
            <div class="px-4 py-5 sm:p-6">
                <div class="h-6 w-48 bg-gray-200 rounded mb-2"></div>
                <div class="h-4 w-32 bg-gray-200 rounded"></div>
            </div>
        </div>
        <div class="animate-pulse bg-white shadow rounded-lg h-24"></div>
    </div>

    <div v-else-if="error" class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded">
        {{ error }}
    </div>

    <div v-else-if="document" class="space-y-6">
        <!-- Document Header -->
        <div class="bg-white shadow rounded-lg">
            <div class="px-4 py-5 sm:p-6">
                <div class="flex justify-between items-start mb-4">
                    <div>
                        <h1 class="text-2xl font-bold text-gray-900">
                            {{ document.title || document.file_name || 'Untitled Document' }}
                        </h1>
                        <p class="text-sm text-gray-500">
                            {{ document.document_number || '—' }}
                        </p>
                    </div>
                    <div class="flex flex-col items-end space-y-2">
                        <div class="flex space-x-2">
                            <span
                                :class="getStatusClass(document.status)"
                                class="px-3 py-1 rounded-full text-sm font-medium"
                            >
                                {{ document.status }}
                            </span>
                            <span
                                :class="getPriorityClass(document.priority)"
                                class="px-3 py-1 rounded-full text-sm font-medium"
                            >
                                {{ document.priority }}
                            </span>
                        </div>
                        <!-- Workflow progress -->
                        <div v-if="workflow && workflow.progress_percentage !== undefined" class="w-48">
                            <div class="flex justify-between items-center mb-1">
                                <span class="text-xs text-gray-500">Workflow Progress</span>
                                <span class="text-xs font-medium text-gray-700">{{ workflow.progress_percentage }}%</span>
                            </div>
                            <div class="h-2 bg-gray-200 rounded-full overflow-hidden">
                                <div class="h-2 bg-indigo-600" :style="{ width: (workflow.progress_percentage || 0) + '%' }"></div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700"
                            >Type</label
                        >
                        <p class="mt-1 text-sm text-gray-900">
                            {{ document.document_type?.name || document.documentType?.name || '—' }}
                        </p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700"
                            >Department</label
                        >
                        <p class="mt-1 text-sm text-gray-900">
                            {{ document.department?.name || document.department_name || '—' }}
                        </p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700"
                            >Created By</label
                        >
                        <p class="mt-1 text-sm text-gray-900">
                            {{ document.creator?.name || document.user?.name || '—' }}
                        </p>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Created</label>
                        <p class="mt-1 text-sm text-gray-900">{{ formatDate(document.created_at) }}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Updated</label>
                        <p class="mt-1 text-sm text-gray-900">{{ formatDate(document.updated_at) }}</p>
                    </div>
                    <div v-if="workflow?.workflow_info?.current_office">
                        <label class="block text-sm font-medium text-gray-700">Current Office</label>
                        <p class="mt-1 text-sm text-gray-900">{{ workflow.workflow_info.current_office }}</p>
                    </div>
                </div>

                <div v-if="document.description" class="mb-6">
                    <label class="block text-sm font-medium text-gray-700 mb-2"
                        >Description</label
                    >
                    <p class="text-sm text-gray-900">
                        {{ document.description }}
                    </p>
                </div>
                <div v-if="hasActions" class="mt-4 flex gap-2">
                    <input v-model="actionRemarks" placeholder="Remarks (optional)" class="flex-1 border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500" />
                    <button
                        v-if="workflow?.available_actions?.can_receive"
                        @click="receiveDocument"
                        :disabled="actionLoading"
                        class="px-3 py-2 bg-green-600 text-white rounded-md hover:bg-green-700 disabled:opacity-50"
                    >
                        {{ actionLoading ? 'Receiving…' : 'Receive' }}
                    </button>
                    <button
                        v-if="workflow?.available_actions?.can_reject"
                        @click="rejectDocument"
                        :disabled="actionLoading"
                        class="px-3 py-2 bg-red-600 text-white rounded-md hover:bg-red-700 disabled:opacity-50"
                    >
                        {{ actionLoading ? 'Rejecting…' : 'Reject' }}
                    </button>
                </div>
            </div>
        </div>

        <!-- QR Code Section -->
        <div v-if="document.qr_code" class="bg-white shadow rounded-lg">
            <div class="px-4 py-5 sm:p-6">
                <h2 class="text-lg font-medium text-gray-900 mb-4">QR Code</h2>
                <div class="flex items-center space-x-4">
                    <img
                        :src="document.qr_code.qr_image_url || document.qr_code_path"
                        alt="QR Code"
                        class="w-32 h-32"
                    />
                    <div>
                        <p class="text-sm text-gray-600">
                            Scan Count: {{ document.qr_code.scan_count }}
                        </p>
                        <p class="text-sm text-gray-600">
                            Last Scanned:
                            {{ formatDate(document.qr_code.last_scanned_at) }}
                        </p>
                        <div class="mt-2 flex gap-2">
                            <button @click="copyShareLink" class="px-3 py-2 text-sm rounded-md bg-gray-100 hover:bg-gray-200">Copy Share Link</button>
                            <button @click="printQR" class="px-3 py-2 text-sm rounded-md bg-indigo-600 text-white hover:bg-indigo-700">Print QR</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Attachments -->
        <div class="bg-white shadow rounded-lg">
            <div class="px-4 py-5 sm:p-6">
                <!-- Primary File (uploaded at creation) -->
                <div v-if="document.file_path" class="mb-4">
                    <div class="flex items-center justify-between p-3 border border-gray-200 rounded-md bg-gray-50">
                        <div class="flex items-center">
                            <div class="w-8 h-8 bg-gray-100 rounded-full flex items-center justify-center mr-3">
                                <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7v10a2 2 0 002 2h6a2 2 0 002-2V9l-4-4H9a2 2 0 00-2 2z" />
                                </svg>
                            </div>
                            <div>
                                <p class="text-sm font-medium text-gray-900">{{ document.file_name || 'Primary document file' }}</p>
                                <p class="text-xs text-gray-500">Stored at /storage/{{ document.file_path }}</p>
                            </div>
                        </div>
                        <div class="flex items-center gap-3">
                            <button @click="openPrimaryFile()" class="text-gray-700 hover:text-gray-900 text-sm">Open</button>
                            <button @click="downloadPrimaryFile()" class="text-indigo-600 hover:text-indigo-900 text-sm">Download</button>
                        </div>
                    </div>
                </div>
                <div class="flex justify-between items-center mb-4">
                    <h2 class="text-lg font-medium text-gray-900">Attachments</h2>
                    <input
                        ref="fileInput"
                        type="file"
                        multiple
                        accept="image/*,.pdf,.xls,.xlsx"
                        @change="handleFileUpload"
                        aria-label="Upload attachments"
                        class="hidden"
                    />
                    <button
                        @click="$refs.fileInput.click()"
                        class="bg-indigo-600 text-white px-4 py-2 rounded-md hover:bg-indigo-700"
                    >
                        Upload File(s)
                    </button>
                </div>
                <p class="text-xs text-gray-500 mb-3">Allowed: images (JPEG/PNG/GIF), PDF, Excel (.xls, .xlsx), max 10MB per file.</p>

                <div v-if="document.attachments && document.attachments.length > 0" class="grid grid-cols-1 md:grid-cols-2 gap-3">
                    <div
                        v-for="attachment in document.attachments"
                        :key="attachment.id"
                        class="flex items-center justify-between p-3 border border-gray-200 rounded-md"
                    >
                        <div class="flex items-center">
                            <div class="w-8 h-8 bg-gray-100 rounded-full flex items-center justify-center mr-3">
                                <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                </svg>
                            </div>
                            <div>
                                <p class="text-sm font-medium text-gray-900">{{ attachment.original_name }}</p>
                                <p class="text-xs text-gray-500">
                                    {{ formatFileSize(attachment.file_size) }}
                                    <span v-if="attachment.created_at"> • {{ formatDate(attachment.created_at) }}</span>
                                </p>
                            </div>
                        </div>
                        <div class="flex items-center gap-3">
                            <button @click="openAttachment(attachment)" class="text-gray-700 hover:text-gray-900 text-sm">Open</button>
                            <button @click="downloadAttachment(attachment)" class="text-indigo-600 hover:text-indigo-900 text-sm">Download</button>
                        </div>
                    </div>
                </div>
                <div v-else class="p-6 border border-dashed border-gray-300 rounded-md text-center">
                    <div class="flex flex-col items-center">
                        <svg class="w-10 h-10 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                        </svg>
                        <p class="mt-2 text-sm text-gray-600">No attachments yet.</p>
                        <button @click="$refs.fileInput.click()" class="mt-3 px-3 py-2 text-sm rounded-md bg-indigo-600 text-white hover:bg-indigo-700">Upload Files</button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Workflow & Routing History -->
        <div class="bg-white shadow rounded-lg">
            <div class="px-4 py-5 sm:p-6">
                <div class="flex items-center justify-between mb-4">
                    <h2 class="text-lg font-medium text-gray-900">Workflow & Routing History</h2>
                    <span v-if="workflow?.current_status" class="text-sm text-gray-600">
                        Current Status: <span class="font-medium">{{ workflow.current_status }}</span>
                    </span>
                </div>
                <div v-if="routes && routes.length" class="space-y-3 mb-6">
                    <h3 class="text-sm font-semibold text-gray-800">Routes</h3>
                    <ul class="space-y-2">
                        <li v-for="r in routes" :key="r.id" class="flex items-start">
                            <div class="mt-1 h-2 w-2 rounded-full bg-indigo-600 mr-3"></div>
                            <div>
                                <div class="text-sm text-gray-800">
                                    {{ r.from_department?.name || '—' }} → {{ r.to_department?.name || '—' }}
                                </div>
                                <div class="text-xs text-gray-500">{{ formatDate(r.created_at) }}</div>
                            </div>
                        </li>
                    </ul>
                </div>
            </div>
        </div>

        <!-- Timeline -->
        <div class="bg-white shadow rounded-lg">
            <div class="px-4 py-5 sm:p-6">
                <h2 class="text-lg font-medium text-gray-900 mb-4">Timeline</h2>
                <div
                    v-if="document.logs && document.logs.length > 0"
                    class="space-y-3"
                >
                    <div
                        v-for="log in document.logs"
                        :key="log.id"
                        class="flex items-center space-x-3"
                    >
                        <div class="flex-shrink-0">
                            <div
                                class="w-8 h-8 bg-indigo-100 rounded-full flex items-center justify-center"
                            >
                                <svg
                                    class="w-4 h-4 text-indigo-600"
                                    fill="none"
                                    stroke="currentColor"
                                    viewBox="0 0 24 24"
                                >
                                    <path
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-width="2"
                                        d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"
                                    ></path>
                                </svg>
                            </div>
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-sm text-gray-900">
                                <span class="font-medium">{{
                                    log.user?.name
                                }}</span>
                                {{ log.description }}
                            </p>
                            <p class="text-xs text-gray-500">
                                {{ formatDate(log.created_at) }}
                            </p>
                        </div>
                    </div>
                </div>
                <div v-else class="text-gray-500 text-sm">
                    No timeline entries
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, onMounted, computed } from "vue";
import { useRoute } from "vue-router";
import { useDocumentStore } from "../stores/documents";
import axios from "axios";
import { useToast } from "vue-toastification";

const route = useRoute();
const documentStore = useDocumentStore();

const document = ref(null);
const loading = ref(true);
const error = ref(null);
const actionRemarks = ref("");
const actionLoading = ref(false);

// Workflow and routing history
const workflow = ref({
    current_status: null,
    progress_percentage: 0,
    available_actions: {},
    workflow_info: {},
});
const routes = ref([]);

const toast = useToast();

const fetchDocument = async () => {
    try {
        document.value = await documentStore.fetchDocument(route.params.id);
    } catch (err) {
        console.error("Error fetching document:", err);
        error.value = "Failed to load document details.";
    }
};

const fetchWorkflow = async () => {
    try {
        if (!document.value?.id) return;
        const res = await axios.get(`/api/workflow/documents/${document.value.id}/status`);
        const payload = res.data && typeof res.data === "object" && "data" in res.data ? res.data.data : res.data;
        workflow.value = payload || {};
    } catch (err) {
        // Non-blocking
    }
};

const fetchHistory = async () => {
    try {
        if (!document.value?.id) return;
        const res = await axios.get(`/api/documents/${document.value.id}/history`);
        const payload = res.data && typeof res.data === "object" && "data" in res.data ? res.data.data : res.data;
        routes.value = payload?.routes || [];
        // Logs are included in document via show
    } catch (err) {
        // Non-blocking
    }
};

onMounted(async () => {
    await fetchDocument();
    await Promise.all([fetchWorkflow(), fetchHistory()]);
    loading.value = false;
});

// Show quick actions only when at least one workflow action is available
const hasActions = computed(() => {
    const a = workflow.value?.available_actions || {};
    return !!(
        a.can_receive ||
        a.can_reject ||
        a.can_hold ||
        a.can_resume ||
        a.can_complete ||
        a.can_forward ||
        a.can_sign
    );
});

// Actions
const receiveDocument = async () => {
    try {
        if (!document.value?.id) return;
        actionLoading.value = true;
        await axios.post(`/api/workflow/documents/${document.value.id}/receive`, {
            notes: actionRemarks.value || undefined,
        });
        toast.success("Document received.");
        await fetchDocument();
        await fetchWorkflow();
    } catch (err) {
        toast.error("Failed to receive document.");
    } finally {
        actionLoading.value = false;
    }
};

const rejectDocument = async () => {
    try {
        if (!document.value?.id) return;
        actionLoading.value = true;
        await axios.post(`/api/workflow/documents/${document.value.id}/reject`, {
            remarks: actionRemarks.value || undefined,
        });
        toast.success("Document rejected.");
        await fetchDocument();
        await fetchWorkflow();
    } catch (err) {
        toast.error("Failed to reject document.");
    } finally {
        actionLoading.value = false;
    }
};

const copyShareLink = () => {
    const url = `${window.location.origin}/documents/${document.value?.id}`;
    navigator.clipboard
        .writeText(url)
        .then(() => toast.success("Share link copied"))
        .catch(() => toast.error("Failed to copy link"));
};

const printQR = () => {
    const path = document.value?.qr_code?.qr_image_url || (document.value?.qr_code_path ? `/storage/${document.value.qr_code_path}` : null);
    if (!path) return;
    const w = window.open("", "_blank");
    if (!w) return;
    w.document.write(`<!DOCTYPE html><html><head><meta charset="utf-8"><title>QR</title><style>@page{margin:0}html,body{margin:0;padding:0;height:100%}body{display:flex;align-items:center;justify-content:center}img{width:280px;height:auto;display:block}</style></head><body><img src="${path}" alt="QR" onload="window.print();window.onafterprint=window.close();"/></body></html>`);
    w.document.close();
};

const handleFileUpload = async (event) => {
    const files = Array.from(event.target.files || []);
    if (!files.length) return;
    try {
        for (const f of files) {
            await documentStore.uploadAttachment(document.value.id, f);
        }
        await fetchDocument(); // Refresh document data
    } catch (error) {
        console.error("Error uploading file(s):", error);
        const msg = error?.response?.data?.message || error?.response?.data?.error || "Upload failed. Only Excel files (.xls, .xlsx) are allowed.";
        try {
            // Use toast if available
            if (typeof toast?.error === 'function') {
                toast.error(msg);
            }
        } catch {}
    } finally {
        // reset input so selecting the same file(s) again re-triggers change
        event.target.value = "";
    }
};

const downloadAttachment = (attachment) => {
    window.open(`/api/attachments/${attachment.id}/download`, "_blank");
};

const openAttachment = (attachment) => {
    const url = (attachment.file_path ? `/storage/${attachment.file_path}` : null) || attachment.url || attachment.preview_url || attachment.file_url || attachment.path;
    if (url) {
        window.open(url, "_blank");
    } else {
        // Fallback to download if no direct preview URL is available
        downloadAttachment(attachment);
    }
};

const openPrimaryFile = () => {
    const path = document.value?.file_path;
    if (!path) return;
    window.open(`/storage/${path}`, "_blank");
};

const downloadPrimaryFile = () => {
    const path = document.value?.file_path;
    if (!path) return;
    // Direct download via storage URL
    const a = document.createElement('a');
    a.href = `/storage/${path}`;
    a.download = document.value?.file_name || 'document-file';
    a.click();
};

const getStatusClass = (status) => {
    const classes = {
        draft: "bg-gray-100 text-gray-800",
        submitted: "bg-blue-100 text-blue-800",
        under_review: "bg-yellow-100 text-yellow-800",
        approved: "bg-green-100 text-green-800",
        rejected: "bg-red-100 text-red-800",
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

const formatDate = (date) => {
    if (!date) return "—";
    const d = new Date(date);
    if (isNaN(d.getTime())) return "—";
    return d.toLocaleDateString("en-US", {
        year: "numeric",
        month: "short",
        day: "numeric",
        hour: "2-digit",
        minute: "2-digit",
    });
};

const formatFileSize = (bytes) => {
    if (bytes === null || bytes === undefined || isNaN(Number(bytes))) return "—";
    const num = Number(bytes);
    if (num === 0) return "0 Bytes";
    const k = 1024;
    const sizes = ["Bytes", "KB", "MB", "GB"];
    const i = Math.min(sizes.length - 1, Math.floor(Math.log(num) / Math.log(k)));
    return parseFloat((num / Math.pow(k, i)).toFixed(2)) + " " + sizes[i];
};
</script>
