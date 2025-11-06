<template>
    <div class="min-h-screen bg-gray-50">
        <!-- Header -->
        <div class="bg-white shadow-sm border-b border-gray-200">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between items-center py-6">
                    <div>
                        <h1 class="text-2xl font-bold text-gray-900">
                            Digital Signatures
                        </h1>
                        <p class="mt-1 text-sm text-gray-500">
                            View and filter digital signatures applied to documents
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Main Content -->
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
            <div class="space-y-6">
                <!-- Search and Filters -->
                <div class="space-y-4">
                    <!-- Search Bar -->
                    <div class="flex flex-col sm:flex-row gap-4">
                        <div class="flex-1">
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                    </svg>
                                </div>
                                <input
                                    v-model="searchQuery"
                                    @keyup.enter="applyFilters"
                                    type="text"
                                    class="block w-full pl-10 pr-3 py-2 border border-gray-300 rounded-md leading-5 bg-white placeholder-gray-500 focus:outline-none focus:placeholder-gray-400 focus:ring-1 focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                                    placeholder="Search by signer, certificate, document number, or title"
                                />
                            </div>
                        </div>

                        <!-- Quick Filters -->
                        <div class="flex flex-wrap gap-2">
                            <select
                                v-model="filters.verification_status"
                                class="block px-3 py-2 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-1 focus:ring-indigo-500 focus:border-indigo-500"
                            >
                                <option value="">All Status</option>
                                <option value="verified">Verified</option>
                                <option value="pending">Pending</option>
                                <option value="failed">Failed</option>
                            </select>

                            <select
                                v-model="filters.signature_type"
                                class="block px-3 py-2 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-1 focus:ring-indigo-500 focus:border-indigo-500"
                            >
                                <option value="">All Types</option>
                                <option value="pnpki">PNPKI</option>
                                <option value="digital">Digital</option>
                                <option value="manual">Manual</option>
                            </select>

                            <button
                                @click="toggleAdvancedFilters"
                                class="inline-flex items-center px-3 py-2 border border-gray-300 rounded-md text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
                            >
                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.414A1 1 0 013 6.707V4z" />
                                </svg>
                                Filters
                            </button>
                        </div>
                    </div>

                    <!-- Advanced Filters -->
                    <div v-show="showAdvancedFilters" class="bg-gray-50 p-4 rounded-lg border">
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Date From</label>
                                <input v-model="filters.date_from" type="date" class="block w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-1 focus:ring-indigo-500 focus:border-indigo-500" />
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Date To</label>
                                <input v-model="filters.date_to" type="date" class="block w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-1 focus:ring-indigo-500 focus:border-indigo-500" />
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Items per page</label>
                                <select v-model.number="filters.per_page" class="block w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-1 focus:ring-indigo-500 focus:border-indigo-500">
                                    <option :value="10">10</option>
                                    <option :value="15">15</option>
                                    <option :value="25">25</option>
                                    <option :value="50">50</option>
                                </select>
                            </div>
                        </div>

                        <div class="mt-4 flex justify-end space-x-2">
                            <button @click="clearFilters" class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                Clear Filters
                            </button>
                            <button @click="applyFilters" class="px-4 py-2 text-sm font-medium text-white bg-indigo-600 border border-transparent rounded-md hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                Apply Filters
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Results Table -->
                <div class="bg-white shadow rounded-lg">
                    <div class="px-4 py-5 sm:p-6">
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Document #</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Title</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Signer</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Certificate Serial</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Type</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Signed At</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    <tr v-if="loading">
                                        <td colspan="8" class="px-6 py-4 text-sm text-gray-500">Loading signatures...</td>
                                    </tr>
                                    <tr v-else-if="signatures.length === 0">
                                        <td colspan="8" class="px-6 py-4 text-sm text-gray-500">No signatures found.</td>
                                    </tr>
                                    <tr v-else v-for="sig in signatures" :key="sig.id">
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-indigo-600">
                                            <router-link :to="{ name: 'document-detail', params: { id: sig.document?.id } }" class="hover:underline">
                                                {{ sig.document?.document_number || '—' }}
                                            </router-link>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                            {{ sig.document?.title || '—' }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                            {{ sig.user?.name || '—' }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 font-mono">
                                            {{ sig.certificate_serial || '—' }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm">
                                            <span :class="typeClasses(sig.signature_type)">{{ typeLabel(sig.signature_type) }}</span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm">
                                            <span :class="statusClasses(sig.verification_status)">{{ statusLabel(sig.verification_status) }}</span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                            {{ formatDate(sig.signed_at) }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm">
                                            <button
                                                v-if="sig.verification_status !== 'verified'"
                                                @click="verifySignature(sig)"
                                                class="px-3 py-1 border border-gray-300 rounded-md text-sm font-medium text-gray-700 hover:bg-gray-50"
                                            >
                                                Verify
                                            </button>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>

                        <!-- Pagination -->
                        <div v-if="pagination && pagination.total > 0" class="mt-6 flex items-center justify-between">
                            <div class="text-sm text-gray-700">
                                Showing
                                <span class="font-medium">{{ pagination.from || 0 }}</span>
                                to
                                <span class="font-medium">{{ pagination.to || 0 }}</span>
                                of
                                <span class="font-medium">{{ pagination.total || 0 }}</span>
                                results
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
                                    v-if="pagination.current_page < pagination.last_page"
                                    @click="changePage(pagination.current_page + 1)"
                                    class="px-3 py-2 border border-gray-300 rounded-md text-sm font-medium text-gray-700 hover:bg-gray-50"
                                >
                                    Next
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
</template>

<script setup>
import { ref, reactive, onMounted } from "vue";
import axios from "axios";

const signatures = ref([]);
const loading = ref(false);
const pagination = ref(null);
const showAdvancedFilters = ref(false);
const searchQuery = ref("");

const filters = reactive({
    verification_status: "",
    signature_type: "",
    date_from: "",
    date_to: "",
    per_page: 15,
});

const toggleAdvancedFilters = () => {
    showAdvancedFilters.value = !showAdvancedFilters.value;
};

const formatDate = (dateStr) => {
    if (!dateStr) return "—";
    const d = new Date(dateStr);
    return d.toLocaleString();
};

const statusClasses = (status) => {
    const base = "px-2 py-1 rounded-full text-xs font-medium";
    const map = {
        verified: "bg-green-100 text-green-800",
        pending: "bg-yellow-100 text-yellow-800",
        failed: "bg-red-100 text-red-800",
    };
    return `${base} ${map[status] || "bg-gray-100 text-gray-800"}`;
};

const statusLabel = (status) => {
    if (!status) return "Unknown";
    const labels = {
        verified: "Verified",
        pending: "Pending",
        failed: "Failed",
    };
    return labels[status] || status;
};

const typeClasses = (type) => {
    const base = "px-2 py-1 rounded-full text-xs font-medium";
    const map = {
        pnpki: "bg-indigo-100 text-indigo-800",
        digital: "bg-blue-100 text-blue-800",
        manual: "bg-gray-100 text-gray-800",
    };
    return `${base} ${map[type] || "bg-gray-100 text-gray-800"}`;
};

const typeLabel = (type) => {
    if (!type) return "Unknown";
    const labels = {
        pnpki: "PNPKI",
        digital: "Digital",
        manual: "Manual",
    };
    return labels[type] || type;
};

const fetchSignatures = async (page = 1) => {
    loading.value = true;
    try {
        const response = await axios.get("/api/signatures", {
            params: {
                search: searchQuery.value || undefined,
                verification_status: filters.verification_status || undefined,
                signature_type: filters.signature_type || undefined,
                date_from: filters.date_from || undefined,
                date_to: filters.date_to || undefined,
                per_page: filters.per_page || 15,
                page,
            },
        });

        const resData = response.data;
        if (resData && typeof resData === "object" && Array.isArray(resData.data)) {
            signatures.value = resData.data;
            pagination.value = resData.pagination
                ? {
                      current_page: resData.pagination.current_page,
                      last_page: resData.pagination.last_page,
                      per_page: resData.pagination.per_page,
                      total: resData.pagination.total,
                      from: resData.pagination.from,
                      to: resData.pagination.to,
                  }
                : {
                      current_page: resData.current_page,
                      last_page: resData.last_page,
                      per_page: resData.per_page,
                      total: resData.total,
                      from: resData.from,
                      to: resData.to,
                  };
        } else if (Array.isArray(resData)) {
            signatures.value = resData;
            pagination.value = null;
        } else {
            signatures.value = [];
            pagination.value = null;
            console.warn("Unexpected response format:", resData);
        }
    } catch (error) {
        console.error("Error fetching signatures:", error);
        signatures.value = [];
        pagination.value = null;
    } finally {
        loading.value = false;
    }
};

const applyFilters = async () => {
    await fetchSignatures(1);
};

const clearFilters = async () => {
    searchQuery.value = "";
    filters.verification_status = "";
    filters.signature_type = "";
    filters.date_from = "";
    filters.date_to = "";
    await fetchSignatures(1);
};

const changePage = async (page) => {
    await fetchSignatures(page);
};

onMounted(async () => {
    await fetchSignatures(1);
});

const verifySignature = async (sig) => {
    try {
        await axios.post(`/api/signatures/${sig.id}/verify`);
        const currentPage = pagination.value?.current_page || 1;
        await fetchSignatures(currentPage);
    } catch (error) {
        console.error("Error verifying signature:", error);
        const msg = error?.response?.data?.message || error.message || "Verification failed";
        window.alert(msg);
    }
};
</script>

<style scoped>
/* minimal styles can go here if needed */
</style>
