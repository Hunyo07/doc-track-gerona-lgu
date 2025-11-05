<template>
    <div class="space-y-6">
        <div class="bg-white shadow rounded-lg">
            <div class="px-4 py-5 sm:p-6">
                <div class="flex justify-between items-center mb-6">
                    <div>
                        <h1 class="text-2xl font-bold text-gray-900">
                            Completed Documents
                        </h1>
                        <p class="text-sm text-gray-600 mt-1">
                            Finalized and archived documents
                        </p>
                    </div>
                </div>

                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
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
                                    Office Trail
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
                            >
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
                                        class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800"
                                        >Completed</span
                                    >
                                </td>
                                <td
                                    class="px-6 py-4 whitespace-nowrap text-sm text-gray-900"
                                >
                                    <div class="text-xs text-gray-600">
                                        <span
                                            v-for="(r, i) in document.routes ||
                                            []"
                                            :key="i"
                                            class="mr-2"
                                        >
                                            {{ r.from_office?.name }} →
                                            {{ r.to_office?.name }}
                                        </span>
                                    </div>
                                </td>
                                <td
                                    class="px-6 py-4 whitespace-nowrap text-sm font-medium"
                                >
                                    <div class="flex space-x-2">
                                        <router-link
                                            :to="`/documents/${document.id}`"
                                            class="text-indigo-600 hover:text-indigo-900"
                                            >View</router-link
                                        >
                                        <button
                                            v-if="document.file_path"
                                            @click="downloadFile(document)"
                                            class="text-green-600 hover:text-green-900"
                                        >
                                            Download
                                        </button>
                                        <button
                                            v-if="document.qr_code_path"
                                            @click="printQR(document)"
                                            class="text-gray-600 hover:text-gray-900"
                                        >
                                            Print QR
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <div
                    v-if="!loading && documents.length === 0"
                    class="text-center py-12"
                >
                    <div class="text-gray-400 text-lg mb-2">✅</div>
                    <h3 class="text-lg font-medium text-gray-900 mb-2">
                        No completed documents
                    </h3>
                    <p class="text-gray-500">
                        Completed documents will appear here.
                    </p>
                </div>

                <div v-if="loading" class="text-center py-12">
                    <div class="text-gray-400 text-lg mb-2">⏳</div>
                    <p class="text-gray-500">Loading documents...</p>
                </div>

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
    </div>
</template>

<script setup>
import { ref, onMounted } from "vue";
import axios from "axios";

const documents = ref([]);
const pagination = ref(null);
const loading = ref(false);

const fetchDocuments = async (page = 1) => {
    loading.value = true;
    try {
        const params = { status: "completed", page };
        const response = await axios.get("/api/documents", { params });
        const resData = response.data;

        // Set documents from ApiResponse or raw array
        documents.value = resData.data || resData;

        // Prefer nested pagination from ApiResponse::paginated
        if (resData && typeof resData === "object" && resData.pagination) {
            pagination.value = {
                current_page: resData.pagination.current_page,
                last_page: resData.pagination.last_page,
                per_page: resData.pagination.per_page,
                total: resData.pagination.total,
                from: resData.pagination.from,
                to: resData.pagination.to,
            };
        } else if (resData && resData.current_page !== undefined) {
            // Fallback for Laravel paginator
            pagination.value = {
                current_page: resData.current_page,
                last_page: resData.last_page,
                per_page: resData.per_page,
                total: resData.total,
                from: resData.from,
                to: resData.to,
            };
        } else {
            pagination.value = null;
        }
    } catch (e) {
        console.error("Error fetching completed documents", e);
        alert("Error fetching completed documents");
    } finally {
        loading.value = false;
    }
};

const changePage = (page) => fetchDocuments(page);
const downloadFile = async (doc) => {
    try {
        const url = `/storage/${doc.file_path}`;
        window.open(url, "_blank");
    } catch (e) {
        alert("Failed to download file");
    }
};
const printQR = (doc) => {
    const url = `/storage/${doc.qr_code_path}`;
    const w = window.open("", "_blank");
    if (!w) return;
    w.document.write(`
      <html>
        <head>
          <title>QR Code</title>
          <style>
            @page { margin: 0; }
            html, body { height: 100%; margin: 0; }
            body { display: flex; align-items: center; justify-content: center; }
            img { width: 1in; height: 1in; display: block; }
          </style>
        </head>
        <body>
          <img src="${url}" alt="QR Code" onload="window.print();window.onafterprint=window.close();" />
        </body>
      </html>
    `);
    w.document.close();
};

onMounted(() => fetchDocuments());
</script>

<style scoped></style>
