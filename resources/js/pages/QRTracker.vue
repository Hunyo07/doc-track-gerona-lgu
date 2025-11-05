<template>
    <div class="space-y-6">
        <div class="bg-white shadow rounded-lg">
            <div class="px-4 py-5 sm:p-6">
                <div class="flex justify-between items-center mb-6">
                    <div>
                        <h1 class="text-2xl font-bold text-gray-900">
                            QR Document Tracker
                        </h1>
                        <p class="text-sm text-gray-600 mt-1">
                            Track document status and routing by entering a document number
                        </p>
                    </div>
                    <div class="flex space-x-3">
                        <button
                            @click="clearSearch"
                            v-if="document"
                            class="bg-gray-600 text-white px-4 py-2 rounded-md hover:bg-gray-700"
                        >
                            Clear
                        </button>
                    </div>
                </div>

                <!-- Search Section -->
                <div class="bg-gray-50 p-4 rounded-lg mb-6">
                    <div class="grid grid-cols-1 gap-6">
                        <!-- Manual Search -->
                        <div>
                            <h3 class="text-sm font-medium text-gray-700 mb-3">Enter Document Number</h3>
                            <div class="flex gap-2">
                                <input
                                    v-model="searchQuery"
                                    @keydown.enter.prevent="searchDocument"
                                    type="text"
                                    placeholder="Enter document number (hardware scanners will type here) or paste QR link"
                                    ref="searchInput"
                                    class="flex-1 px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500"
                                />
                                <button
                                    @click="searchDocument"
                                    :disabled="!normalizedQuery || loading"
                                    class="bg-indigo-600 text-white px-4 py-2 rounded-md hover:bg-indigo-700 disabled:opacity-50 disabled:cursor-not-allowed"
                                >
                                    <span v-if="loading">Tracking…</span>
                                    <span v-else>Track</span>
                                </button>
                            </div>
                            <!-- Normalized query hint -->
                            <div v-if="normalizedQuery && !document && !error" class="mt-2 flex items-center gap-3 text-xs text-gray-600">
                                <span>Detected:</span>
                                <span class="inline-flex items-center gap-2 px-2 py-1 rounded-full bg-gray-100 text-gray-800 border border-gray-200">
                                    <svg class="w-3.5 h-3.5 text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V4a2 2 0 10-4 0v1.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5"/></svg>
                                    <code class="font-mono">{{ normalizedQuery }}</code>
                                </span>
                                <router-link :to="documentNumberLink" class="text-indigo-600 hover:text-indigo-800">Open by number</router-link>
                            </div>
                            <div class="mt-2 text-[11px] text-gray-500">
                                Examples: <code class="font-mono">DOC-000123</code>, <code class="font-mono">https://example.com/documents/code/DOC-000123</code>
                            </div>
                        </div>
                    </div>
                    
                    <div v-if="error" class="mt-3 p-3 bg-red-50 border border-red-200 rounded-md">
                        <p class="text-sm text-red-600">{{ error }}</p>
                        <div v-if="normalizedQuery" class="mt-2 text-xs">
                            <router-link :to="documentNumberLink" class="text-red-700 hover:text-red-900">Try opening by number</router-link>
                        </div>
                    </div>
                </div>

                <!-- Loading Skeleton -->
                <div v-if="loading" class="space-y-6">
                    <div class="bg-white border border-gray-200 rounded-lg p-6 animate-pulse">
                        <div class="h-4 bg-gray-200 rounded w-1/3 mb-3"></div>
                        <div class="h-3 bg-gray-200 rounded w-2/3 mb-2"></div>
                        <div class="h-3 bg-gray-200 rounded w-1/2"></div>
                    </div>
                    <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 animate-pulse">
                        <div class="h-3 bg-blue-200 rounded w-1/4"></div>
                    </div>
                    <div class="bg-white border border-gray-200 rounded-lg p-6 animate-pulse">
                        <div class="h-4 bg-gray-200 rounded w-1/4 mb-4"></div>
                        <div class="space-y-3">
                            <div class="h-3 bg-gray-200 rounded w-full"></div>
                            <div class="h-3 bg-gray-200 rounded w-5/6"></div>
                            <div class="h-3 bg-gray-200 rounded w-4/6"></div>
                        </div>
                    </div>
                </div>

                <!-- Document Details -->
                <div v-else-if="document" class="space-y-6">
                    <!-- Document Header -->
                    <div class="bg-white border border-gray-200 rounded-lg p-6">
                        <div class="flex justify-between items-start">
                            <div class="flex-1">
                                <h2 class="text-xl font-semibold text-gray-900">{{ document.title }}</h2>
                                <p class="text-sm text-gray-600 mt-1">{{ document.description }}</p>
                                <div class="flex items-center space-x-4 mt-3">
                                    <span class="text-xs px-2 py-1 rounded-full bg-gray-100 text-gray-800 border border-gray-200"># {{ document.document_number }}</span>
                                    <span class="text-xs px-2 py-1 rounded-full bg-blue-100 text-blue-800">{{ document.type }}</span>
                                    <span class="text-xs px-2 py-1 rounded-full bg-yellow-100 text-yellow-800">Priority: {{ document.priority }}</span>
                                </div>
                            </div>
                            <div class="flex flex-col items-end space-y-3">
                                <div class="flex items-center gap-2">
                                    <StatusBadge :status="document.status" />
                                </div>
                                <span class="text-sm text-gray-500">
                                    Created: {{ formatDate(document.created_at) }}
                                </span>
                                <!-- Quick actions toolbar -->
                                <div class="flex items-center gap-2">
                                    <router-link
                                        v-if="documentIdLink"
                                        :to="documentIdLink"
                                        class="text-indigo-600 hover:text-indigo-800 text-sm"
                                    >Open details</router-link>
                                    <router-link
                                        :to="documentNumberLink"
                                        class="text-indigo-600 hover:text-indigo-800 text-sm"
                                    >Open by number</router-link>
                                    <button
                                        @click="copyShareLink"
                                        class="text-gray-700 hover:text-gray-900 text-sm"
                                    >Copy link</button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Current Location -->
                    <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <svg class="w-5 h-5 text-blue-600" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M5.05 4.05a7 7 0 119.9 9.9L10 18.9l-4.95-4.95a7 7 0 010-9.9zM10 11a2 2 0 100-4 2 2 0 000 4z" clip-rule="evenodd" />
                                </svg>
                            </div>
                            <div class="ml-3">
                                <h3 class="text-sm font-medium text-blue-900">Current Location</h3>
                                <p class="text-sm text-blue-700">
                                    {{ document.current_office?.name || 'Not assigned' }}
                                </p>
                            </div>
                        </div>
                    </div>

                    <!-- Document Timeline -->
                    <div class="bg-white border border-gray-200 rounded-lg p-6">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Document Timeline</h3>
                        
                        <div v-if="document.logs && document.logs.length > 0" class="flow-root">
                            <ul class="-mb-8">
                                <li v-for="(log, index) in visibleLogs" :key="log.id" class="relative pb-8">
                                    <div v-if="index !== document.logs.length - 1" class="absolute top-4 left-4 -ml-px h-full w-0.5 bg-gray-200"></div>
                                    <div class="relative flex space-x-3">
                                        <div>
                                            <span :class="getActionIconClass(log.action)" class="h-8 w-8 rounded-full flex items-center justify-center ring-8 ring-white">
                                                <svg class="w-4 h-4 text-white" fill="currentColor" viewBox="0 0 20 20">
                                                    <path v-if="log.action === 'created'" fill-rule="evenodd" d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z" clip-rule="evenodd" />
                                                    <path v-else-if="log.action === 'forwarded'" d="M10.894 2.553a1 1 0 00-1.788 0l-7 14a1 1 0 001.169 1.409l5-1.429A1 1 0 009 15.571V11a1 1 0 112 0v4.571a1 1 0 00.725.962l5 1.428a1 1 0 001.17-1.408l-7-14z" />
                                                    <path v-else-if="log.action === 'received'" fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                                                    <path v-else fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd" />
                                                </svg>
                                            </span>
                                        </div>
                                        <div class="min-w-0 flex-1 pt-1.5 flex justify-between space-x-4">
                                            <div>
                                                <p class="text-sm text-gray-900">
                                                    <strong>{{ log.user?.name || 'System' }}</strong> {{ getActionDescription(log.action) }}
                                                    <span v-if="log.from_office || log.to_office" class="text-gray-600">
                                                        <span v-if="log.from_office">from {{ log.from_office.name }}</span>
                                                        <span v-if="log.from_office && log.to_office"> to </span>
                                                        <span v-if="log.to_office">{{ log.to_office.name }}</span>
                                                    </span>
                                                </p>
                                                <p v-if="log.remarks" class="mt-1 text-sm text-gray-600">
                                                    "{{ log.remarks }}"
                                                </p>
                                            </div>
                                            <div class="text-right text-sm whitespace-nowrap text-gray-500">
                                                {{ formatDate(log.created_at) }}
                                            </div>
                                        </div>
                                    </div>
                                </li>
                            </ul>
                            <div v-if="document.logs.length > visibleLogsLimit" class="mt-2 text-right">
                                <button @click="showAllLogs = !showAllLogs" class="text-sm text-indigo-600 hover:text-indigo-800">
                                    <span v-if="showAllLogs">Collapse</span>
                                    <span v-else>Show all</span>
                                </button>
                            </div>
                        </div>
                        
                        <div v-else class="text-center py-8">
                            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                            <h3 class="mt-2 text-sm font-medium text-gray-900">No timeline available</h3>
                            <p class="mt-1 text-sm text-gray-500">This document has no recorded activities yet.</p>
                        </div>
                    </div>

                    <!-- Digital Signatures -->
                    <div v-if="document.signatures && document.signatures.length > 0" class="bg-white border border-gray-200 rounded-lg p-6">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Digital Signatures</h3>
                        <div class="space-y-3">
                            <div v-for="signature in document.signatures" :key="signature.id" class="flex items-center justify-between p-3 bg-green-50 border border-green-200 rounded-md">
                                <div class="flex items-center">
                                    <svg class="w-5 h-5 text-green-600 mr-3" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M6.267 3.455a3.066 3.066 0 001.745-.723 3.066 3.066 0 013.976 0 3.066 3.066 0 001.745.723 3.066 3.066 0 012.812 2.812c.051.643.304 1.254.723 1.745a3.066 3.066 0 010 3.976 3.066 3.066 0 00-.723 1.745 3.066 3.066 0 01-2.812 2.812 3.066 3.066 0 00-1.745.723 3.066 3.066 0 01-3.976 0 3.066 3.066 0 00-1.745-.723 3.066 3.066 0 01-2.812-2.812 3.066 3.066 0 00-.723-1.745 3.066 3.066 0 010-3.976 3.066 3.066 0 00.723-1.745 3.066 3.066 0 012.812-2.812zm7.44 5.252a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                                    </svg>
                                    <div>
                                        <p class="text-sm font-medium text-green-900">{{ signature.signer_name }}</p>
                                        <p class="text-xs text-green-700">{{ signature.office?.name }}</p>
                                    </div>
                                </div>
                                <div class="text-right">
                                    <p class="text-sm text-green-900">{{ formatDate(signature.signed_at) }}</p>
                                    <p class="text-xs text-green-700">PNPKI Verified</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- QR Code Info -->
                    <div class="bg-white border border-gray-200 rounded-lg p-6">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">QR Code Information</h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <p class="text-sm text-gray-600">Total Scans</p>
                                <p class="text-2xl font-bold text-gray-900">{{ document.qr_code?.scan_count || 0 }}</p>
                            </div>
                            <div>
                                <p class="text-sm text-gray-600">Last Scanned</p>
                                <p class="text-sm text-gray-900">
                                    {{ document.qr_code?.last_scanned_at ? formatDate(document.qr_code.last_scanned_at) : 'Never' }}
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Empty State Guidance -->
                <div v-else-if="!error" class="bg-white border border-gray-200 rounded-lg p-10 text-center">
                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                    <h3 class="mt-2 text-lg font-medium text-gray-900">Track a document</h3>
                    <p class="mt-1 text-sm text-gray-500">Enter a document number (or use a hardware scanner). Try examples like <code class="font-mono">DOC-000123</code>.</p>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, computed, onMounted, onUnmounted } from 'vue';
import { useRouter } from 'vue-router';
import { useWorkflowStore } from '../stores/workflow';
import StatusBadge from '../components/StatusBadge.vue';

const workflowStore = useWorkflowStore();
const searchQuery = ref('');
const document = ref(null);
const loading = ref(false);
const error = ref('');
const searchInput = ref(null);
const requestSeq = ref(0);
const router = useRouter();
const showAllLogs = ref(false);
const visibleLogsLimit = 5;

onMounted(() => {
    // Auto-focus input on load
    if (searchInput.value) {
        try { searchInput.value.focus(); } catch (_) {}
    }
    const keyListener = (e) => {
        if (e.key === '/' && !e.ctrlKey && !e.metaKey) {
            e.preventDefault();
            try { searchInput.value?.focus(); } catch (_) {}
        }
    };
    window.addEventListener('keydown', keyListener);
    // store listener for cleanup
    cleanup.value = () => window.removeEventListener('keydown', keyListener);
});

const cleanup = ref(() => {});
onUnmounted(() => {
    try { cleanup.value(); } catch (_) {}
});

const validTokenRegex = /^[A-Za-z0-9._\-\/]+$/;
const normalizedQuery = computed(() => {
    const raw = (searchQuery.value || '').trim();
    if (!raw) return '';
    // If raw looks like a URL, try to extract a document number
    if (/^https?:\/\//i.test(raw)) {
        try {
            const url = new URL(raw);
            const keys = ['doc','document','document_number','number','id'];
            for (const k of keys) {
                const v = url.searchParams.get(k);
                if (v && v.trim()) return v.trim();
            }
            const segs = url.pathname.split('/').filter(Boolean);
            if (segs.length) {
                const last = segs[segs.length - 1];
                if (validTokenRegex.test(last)) return last;
            }
        } catch (_) {
            // fallthrough to raw normalization
        }
    }
    // Fallback: first valid token from raw string
    const m = raw.match(validTokenRegex);
    return m ? m[0] : raw;
});

// Links
const documentIdLink = computed(() => {
    const id = document.value?.id;
    return id ? `/documents/${id}` : '';
});
const documentNumberLink = computed(() => {
    const num = normalizedQuery.value || document.value?.document_number;
    return num ? `/documents/code/${num}` : '';
});

// Timeline visibility
const visibleLogs = computed(() => {
    const logs = document.value?.logs || [];
    if (showAllLogs.value) return logs;
    return logs.slice(0, visibleLogsLimit);
});

const searchDocument = async () => {
    const q = normalizedQuery.value;
    if (!q) {
        error.value = 'Please enter a document number or valid QR link';
        return;
    }

    loading.value = true;
    error.value = '';
    document.value = null;
    const seq = ++requestSeq.value;

    try {
        const response = await workflowStore.trackDocument(q);
        // Guard against stale responses from older requests
        if (seq === requestSeq.value) {
            document.value = response.data;
        }
    } catch (err) {
        error.value = err.response?.data?.message || err.message || 'Document not found or access denied';
    } finally {
        loading.value = false;
    }
};


const clearSearch = () => {
    searchQuery.value = '';
    document.value = null;
    error.value = '';
};

const copyShareLink = () => {
    const id = document.value?.id;
    const num = document.value?.document_number || normalizedQuery.value;
    const base = window.location.origin;
    const url = id ? `${base}/documents/${id}` : (num ? `${base}/documents/code/${num}` : `${base}/qr-tracker`);
    navigator.clipboard
        .writeText(url)
        .catch(() => {})
        .then(() => {})
};



const getActionIconClass = (action) => {
    const actionClasses = {
        'created': 'bg-blue-500',
        'forwarded': 'bg-indigo-500',
        'received': 'bg-green-500',
        'signed': 'bg-purple-500',
        'approved': 'bg-emerald-500',
        'rejected': 'bg-red-500',
        'completed': 'bg-gray-500'
    };
    return actionClasses[action] || 'bg-gray-400';
};

const getActionDescription = (action) => {
    const descriptions = {
        'created': 'created the document',
        'forwarded': 'forwarded the document',
        'received': 'received the document',
        'signed': 'digitally signed the document',
        'approved': 'approved the document',
        'rejected': 'rejected the document',
        'completed': 'marked the document as completed',
        'updated': 'updated the document'
    };
    return descriptions[action] || 'performed an action on the document';
};

const formatDate = (dateString) => {
    return new Date(dateString).toLocaleString('en-US', {
        year: 'numeric',
        month: 'short',
        day: 'numeric',
        hour: '2-digit',
        minute: '2-digit'
    });
};
</script>