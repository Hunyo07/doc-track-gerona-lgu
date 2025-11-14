<template>
    <div class="p-6 bg-gray-50 min-h-screen">
        <div class="max-w-7xl mx-auto">
            <!-- Header -->
            <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
                <div class="flex items-center justify-between">
                    <div>
                        <h1
                            class="text-2xl font-bold text-gray-900 flex items-center gap-2"
                        >
                            <svg
                                class="w-6 h-6 text-indigo-600"
                                fill="none"
                                stroke="currentColor"
                                viewBox="0 0 24 24"
                            >
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-width="2"
                                    d="M9 12h6m-6 4h6M7 4h10a2 2 0 012 2v12a2 2 0 01-2 2H7a2 2 0 01-2-2V6a2 2 0 012-2z"
                                />
                            </svg>
                            Document Types
                        </h1>
                        <p class="text-gray-600 mt-1">
                            Manage document types and their JSON schema.
                        </p>
                    </div>
                </div>
            </div>

            <!-- Create Form (Admin) -->
            <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-4">
                    Create New Document Type
                </h2>
                <form
                    @submit.prevent="createType"
                    class="grid grid-cols-1 md:grid-cols-2 gap-4"
                >
                    <div>
                        <label
                            class="block text-sm font-medium text-gray-700 mb-1"
                            >Name<span class="text-red-500">*</span></label
                        >
                        <input
                            v-model="form.name"
                            type="text"
                            required
                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-indigo-500 focus:border-indigo-500"
                            placeholder="e.g., Purchase Request"
                        />
                        <p v-if="errors.name" class="mt-1 text-sm text-red-600">
                            {{ firstError(errors.name) }}
                        </p>
                    </div>
                    <div>
                        <label
                            class="block text-sm font-medium text-gray-700 mb-1"
                            >Code<span class="text-red-500">*</span></label
                        >
                        <input
                            v-model="form.code"
                            type="text"
                            required
                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-indigo-500 focus:border-indigo-500"
                            placeholder="e.g., PR"
                        />
                        <p v-if="errors.code" class="mt-1 text-sm text-red-600">
                            {{ firstError(errors.code) }}
                        </p>
                    </div>
                    <div>
                        <label
                            class="block text-sm font-medium text-gray-700 mb-1"
                            >Prefix</label
                        >
                        <input
                            v-model="form.prefix"
                            type="text"
                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-indigo-500 focus:border-indigo-500"
                            placeholder="e.g., PR"
                        />
                        <p
                            v-if="errors.prefix"
                            class="mt-1 text-sm text-red-600"
                        >
                            {{ firstError(errors.prefix) }}
                        </p>
                    </div>
                    <div class="flex items-center gap-3">
                        <label class="inline-flex items-center">
                            <input
                                v-model="form.requires_approval"
                                type="checkbox"
                                class="mr-2"
                            />
                            <span class="text-sm text-gray-700"
                                >Requires Approval</span
                            >
                        </label>
                        <label class="inline-flex items-center">
                            <input
                                v-model="form.is_active"
                                type="checkbox"
                                class="mr-2"
                            />
                            <span class="text-sm text-gray-700">Active</span>
                        </label>
                    </div>
                    <div class="md:col-span-2">
                        <label
                            class="block text-sm font-medium text-gray-700 mb-1"
                            >Description</label
                        >
                        <textarea
                            v-model="form.description"
                            rows="2"
                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-indigo-500 focus:border-indigo-500"
                            placeholder="Optional description"
                        ></textarea>
                        <p
                            v-if="errors.description"
                            class="mt-1 text-sm text-red-600"
                        >
                            {{ firstError(errors.description) }}
                        </p>
                    </div>
                    <div class="md:col-span-2">
                        <div class="flex items-center justify-between mb-1">
                            <label
                                class="block text-sm font-medium text-gray-700"
                                >Schema (JSON, optional)</label
                            >
                            <button
                                type="button"
                                @click="insertSchemaTemplate"
                                class="text-xs text-indigo-600 hover:text-indigo-800"
                            >
                                Insert template
                            </button>
                        </div>
                        <textarea
                            v-model="schemaText"
                            rows="6"
                            class="w-full font-mono text-sm px-3 py-2 border border-gray-300 rounded-md focus:ring-indigo-500 focus:border-indigo-500"
                            placeholder='{
  "fields": [
    { "key": "supplier", "label": "Supplier", "type": "string", "required": true }
  ]
}'
                        ></textarea>

                        <p v-if="schemaError" class="mt-1 text-sm text-red-600">
                            {{ schemaError }}
                        </p>
                        <p
                            v-if="errors.schema"
                            class="mt-1 text-sm text-red-600"
                        >
                            {{ firstError(errors.schema) }}
                        </p>
                    </div>
                    <div class="md:col-span-2 flex gap-2">
                        <button
                            type="submit"
                            :disabled="submitting"
                            class="inline-flex items-center px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white font-medium rounded-lg shadow-sm disabled:opacity-50"
                        >
                            <svg
                                v-if="submitting"
                                class="animate-spin w-4 h-4 mr-2"
                                fill="none"
                                stroke="currentColor"
                                viewBox="0 0 24 24"
                            >
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-width="2"
                                    d="M4 12a8 8 0 018-8v0m0 0a8 8 0 018 8v0m0 0a8 8 0 01-8 8v0m0 0a8 8 0 01-8-8v0"
                                />
                            </svg>
                            Create Type
                        </button>
                        <button
                            type="button"
                            @click="resetForm"
                            class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-lg"
                        >
                            Reset
                        </button>
                    </div>
                </form>
            </div>

            <!-- Types List -->
            <div class="bg-white rounded-lg shadow-sm overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-medium text-gray-900">
                        Existing Document Types
                        <span class="text-sm font-normal text-gray-500 ml-2"
                            >({{ documentTypes.length }})</span
                        >
                    </h3>
                </div>
                <div v-if="loading" class="p-8 text-center text-gray-600">
                    Loading document types...
                </div>
                <div v-else class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
                                >
                                    Name
                                </th>
                                <th
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
                                >
                                    Code
                                </th>
                                <th
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
                                >
                                    Prefix
                                </th>
                                <th
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
                                >
                                    Requires Approval
                                </th>
                                <th
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
                                >
                                    Active
                                </th>
                                <th
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
                                >
                                    Actions
                                </th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            <tr v-for="t in documentTypes" :key="t.id">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div
                                        class="text-sm font-medium text-gray-900"
                                    >
                                        {{ t.name }}
                                    </div>
                                    <div class="text-xs text-gray-500">
                                        {{ t.description || "" }}
                                    </div>
                                </td>
                                <td
                                    class="px-6 py-4 whitespace-nowrap text-sm text-gray-700"
                                >
                                    {{ t.code }}
                                </td>
                                <td
                                    class="px-6 py-4 whitespace-nowrap text-sm text-gray-700"
                                >
                                    {{ t.prefix || "-" }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span
                                        :class="
                                            t.requires_approval
                                                ? 'bg-amber-100 text-amber-800'
                                                : 'bg-gray-100 text-gray-600'
                                        "
                                        class="inline-flex px-2 py-1 rounded-full text-xs font-medium"
                                        >{{
                                            t.requires_approval ? "Yes" : "No"
                                        }}</span
                                    >
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span
                                        :class="
                                            t.is_active
                                                ? 'bg-green-100 text-green-800'
                                                : 'bg-red-100 text-red-800'
                                        "
                                        class="inline-flex px-2 py-1 rounded-full text-xs font-medium"
                                        >{{
                                            t.is_active ? "Active" : "Inactive"
                                        }}</span
                                    >
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm">
                                    <div class="flex items-center gap-2">
                                        <button
                                            @click="toggleActive(t)"
                                            :disabled="isBusy(t.id)"
                                            class="px-3 py-1 rounded-md text-white"
                                            :class="t.is_active ? 'bg-yellow-600 hover:bg-yellow-700' : 'bg-green-600 hover:bg-green-700'"
                                        >
                                            {{ t.is_active ? 'Deactivate' : 'Activate' }}
                                        </button>
                                        <button
                                            @click="deleteType(t)"
                                            :disabled="isBusy(t.id)"
                                            class="px-3 py-1 rounded-md text-white bg-red-600 hover:bg-red-700"
                                        >
                                            Delete
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, onMounted } from "vue";
import axios from "axios";
import { useToast } from "vue-toastification";

const toast = useToast();

// List state
const documentTypes = ref([]);
const loading = ref(false);

// Form state
const form = ref({
    name: "",
    code: "",
    prefix: "",
    description: "",
    requires_approval: false,
    is_active: true,
});
const schemaText = ref("");
const errors = ref({});
const schemaError = ref("");
const submitting = ref(false);
const rowBusy = ref(new Set());
const isBusy = (id) => !!rowBusy.value && rowBusy.value.has(id);

const firstError = (v) => (Array.isArray(v) ? v[0] : v);

const fetchDocumentTypes = async () => {
    try {
        loading.value = true;
        const res = await axios.get("/api/document-types");
        documentTypes.value = Array.isArray(res.data)
            ? res.data
            : res.data.data || [];
    } catch (e) {
        console.error(e);
        toast.error("Failed to fetch document types");
    } finally {
        loading.value = false;
    }
};

const resetForm = () => {
    form.value = {
        name: "",
        code: "",
        prefix: "",
        description: "",
        requires_approval: false,
        is_active: true,
    };
    schemaText.value = "";
    errors.value = {};
    schemaError.value = "";
};

const insertSchemaTemplate = () => {
    schemaText.value = JSON.stringify(
        {
            fields: [
                {
                    key: "title",
                    label: "Title",
                    type: "string",
                    required: true,
                },
                { key: "amount", label: "Amount", type: "number" },
                { key: "notes", label: "Notes", type: "string" },
            ],
        },
        null,
        2
    );
};

const createType = async () => {
    errors.value = {};
    schemaError.value = "";
    submitting.value = true;
    try {
        let schemaObj = null;
        if (schemaText.value && schemaText.value.trim().length > 0) {
            try {
                schemaObj = JSON.parse(schemaText.value);
            } catch (e) {
                schemaError.value = "Invalid JSON schema";
                submitting.value = false;
                return;
            }
        }

        const payload = { ...form.value, schema: schemaObj };
        const res = await axios.post("/api/document-types", payload);
        toast.success("Document type created");
        resetForm();
        await fetchDocumentTypes();
    } catch (err) {
        if (err.response?.status === 422) {
            errors.value = err.response.data.errors || {};
            toast.error("Please fix validation errors");
        } else if (err.response?.status === 403) {
            toast.error("Forbidden: Admin access required");
        } else {
            toast.error(err.response?.data?.message || "Failed to create type");
        }
    } finally {
        submitting.value = false;
    }
};

const toggleActive = async (t) => {
    if (rowBusy.value.has(t.id)) return;
    rowBusy.value.add(t.id);
    try {
        const res = await axios.put(`/api/document-types/${t.id}`, {
            is_active: !t.is_active,
        });
        const updated = res.data?.data || res.data;
        const idx = documentTypes.value.findIndex((x) => x.id === t.id);
        if (idx !== -1) documentTypes.value[idx] = updated;
        toast.success(updated.is_active ? 'Activated' : 'Deactivated');
    } catch (err) {
        if (err.response?.status === 403) {
            toast.error('Forbidden: Admin access required');
        } else {
            toast.error(err.response?.data?.message || 'Failed to update');
        }
    } finally {
        rowBusy.value.delete(t.id);
    }
};

const deleteType = async (t) => {
    if (rowBusy.value.has(t.id)) return;
    const ok = window.confirm('Delete this document type?');
    if (!ok) return;
    rowBusy.value.add(t.id);
    try {
        await axios.delete(`/api/document-types/${t.id}`);
        documentTypes.value = documentTypes.value.filter((x) => x.id !== t.id);
        toast.success('Document type deleted');
    } catch (err) {
        if (err.response?.status === 403) {
            toast.error('Forbidden: Admin access required');
        } else {
            toast.error(err.response?.data?.message || 'Failed to delete');
        }
    } finally {
        rowBusy.value.delete(t.id);
    }
};

onMounted(() => {
    fetchDocumentTypes();
});
</script>

<style scoped></style>
