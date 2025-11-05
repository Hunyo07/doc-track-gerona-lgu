<template>
    <div
        class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50"
    >
        <div
            class="relative top-10 mx-auto p-6 border w-full max-w-4xl shadow-xl rounded-lg bg-white"
        >
            <div class="mt-3">
                <div class="flex items-center justify-between mb-6">
                    <div class="flex items-center space-x-3">
                        <div class="flex-shrink-0">
                            <div
                                class="w-10 h-10 bg-indigo-100 rounded-full flex items-center justify-center"
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
                                        d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"
                                    ></path>
                                </svg>
                            </div>
                        </div>
                        <div>
                            <h3 class="text-xl font-semibold text-gray-900">
                                Create New Document
                            </h3>
                            <p class="text-sm text-gray-500">
                                {{
                                    currentStep === 1
                                        ? "Generate QR code first"
                                        : "Fill in the details to create document"
                                }}
                            </p>
                        </div>
                    </div>
                    <button
                        @click="$emit('close')"
                        class="text-gray-400 hover:text-gray-600 hover:bg-gray-100 rounded-full p-2 transition-colors"
                    >
                        <svg
                            class="w-6 h-6"
                            fill="none"
                            stroke="currentColor"
                            viewBox="0 0 24 24"
                        >
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="2"
                                d="M6 18L18 6M6 6l12 12"
                            ></path>
                        </svg>
                    </button>
                </div>

                <!-- Step Indicator -->
                <div class="flex items-center justify-center mb-8">
                    <div class="flex items-center space-x-4">
                        <div class="flex items-center">
                            <div
                                :class="
                                    currentStep >= 1
                                        ? 'bg-indigo-600 text-white'
                                        : 'bg-gray-200 text-gray-600'
                                "
                                class="w-8 h-8 rounded-full flex items-center justify-center text-sm font-medium"
                            >
                                1
                            </div>
                            <span class="ml-2 text-sm font-medium text-gray-900"
                                >Generate QR</span
                            >
                        </div>
                        <div class="w-16 h-0.5 bg-gray-200"></div>
                        <div class="flex items-center">
                            <div
                                :class="
                                    currentStep >= 2
                                        ? 'bg-indigo-600 text-white'
                                        : 'bg-gray-200 text-gray-600'
                                "
                                class="w-8 h-8 rounded-full flex items-center justify-center text-sm font-medium"
                            >
                                2
                            </div>
                            <span class="ml-2 text-sm font-medium text-gray-900"
                                >Upload Document</span
                            >
                        </div>
                    </div>
                </div>

                <!-- Step 1: QR Generation -->
                <div v-if="currentStep === 1">
                    <QRGenerator @qr-generated="onQRGenerated" />
                </div>

                <!-- Step 2: Document Upload -->
                <form
                    v-if="currentStep === 2"
                    @submit.prevent="createDocument"
                    class="space-y-6"
                >
                    <!-- QR Code Info -->
                    <div
                        class="bg-green-50 border border-green-200 p-4 rounded-lg"
                    >
                        <div class="flex items-center">
                            <svg
                                class="w-5 h-5 text-green-400 mr-2"
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
                            <div>
                                <p class="text-sm font-medium text-green-800">
                                    QR Code Generated
                                </p>
                                <p class="text-xs text-green-600">
                                    Document Number: {{ qrData.documentNumber }}
                                </p>
                            </div>
                        </div>
                    </div>
                    <!-- Basic Information Section -->
                    <div class="bg-gray-50 p-4 rounded-lg">
                        <h4
                            class="text-lg font-medium text-gray-900 mb-4 flex items-center"
                        >
                            <svg
                                class="w-5 h-5 text-indigo-600 mr-2"
                                fill="none"
                                stroke="currentColor"
                                viewBox="0 0 24 24"
                            >
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-width="2"
                                    d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"
                                ></path>
                            </svg>
                            Basic Information
                        </h4>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div class="md:col-span-2">
                                <label
                                    class="block text-sm font-medium text-gray-700 mb-2"
                                >
                                    Document Name
                                    <span class="text-red-500">*</span>
                                </label>
                                <input
                                    v-model="form.title"
                                    type="text"
                                    required
                                    class="w-full p-2 border-gray-300 rounded-lg shadow-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors"
                                    :class="{
                                        'border-red-300 focus:ring-red-500 focus:border-red-500':
                                            errors.title,
                                    }"
                                    placeholder="Enter a descriptive document name"
                                />
                                <div
                                    v-if="errors.title"
                                    class="mt-1 text-sm text-red-600"
                                >
                                    {{ errors.title[0] }}
                                </div>
                            </div>

                            <div class="md:col-span-2">
                                <label
                                    class="block text-sm font-medium text-gray-700 mb-2"
                                    >Description</label
                                >
                                <textarea
                                    v-model="form.description"
                                    rows="3"
                                    class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors"
                                    :class="{
                                        'border-red-300 focus:ring-red-500 focus:border-red-500':
                                            errors.description,
                                    }"
                                    placeholder="Provide a detailed description of the document"
                                ></textarea>
                                <div
                                    v-if="errors.description"
                                    class="mt-1 text-sm text-red-600"
                                >
                                    {{ errors.description[0] }}
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- File Attachment Section -->
                    <div class="bg-gray-50 p-4 rounded-lg">
                        <h4
                            class="text-lg font-medium text-gray-900 mb-4 flex items-center"
                        >
                            <svg
                                class="w-5 h-5 text-indigo-600 mr-2"
                                fill="none"
                                stroke="currentColor"
                                viewBox="0 0 24 24"
                            >
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-width="2"
                                    d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"
                                ></path>
                            </svg>
                            File Attachment
                        </h4>

                        <div class="space-y-4">
                            <div>
                                <div
                                    class="flex items-center justify-center w-full"
                                >
                                    <label
                                        for="file-upload"
                                        class="flex flex-col items-center justify-center w-full h-32 border-2 border-gray-300 border-dashed rounded-lg cursor-pointer bg-gray-50 hover:bg-gray-100 transition-colors"
                                        :class="{
                                            'border-indigo-500 bg-indigo-50':
                                                isDragOver,
                                        }"
                                        @dragover.prevent="isDragOver = true"
                                        @dragleave.prevent="isDragOver = false"
                                        @drop.prevent="handleFileDrop"
                                    >
                                        <div
                                            class="flex flex-col items-center justify-center pt-5 pb-6"
                                        >
                                            <svg
                                                class="w-8 h-8 mb-4 text-gray-500"
                                                fill="none"
                                                stroke="currentColor"
                                                viewBox="0 0 24 24"
                                            >
                                                <path
                                                    stroke-linecap="round"
                                                    stroke-linejoin="round"
                                                    stroke-width="2"
                                                    d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"
                                                ></path>
                                            </svg>
                                            <p
                                                class="mb-2 text-sm text-gray-500"
                                            >
                                                <span class="font-semibold"
                                                    >Click to upload</span
                                                >
                                                or drag and drop
                                            </p>
                                            <p class="text-xs text-gray-500">
                                                Excel, PDF, Word files (max
                                                10MB)
                                            </p>
                                        </div>
                                        <input
                                            id="file-upload"
                                            ref="fileInput"
                                            type="file"
                                            accept=".xls,.xlsx,.pdf,.doc,.docx"
                                            @change="onFile"
                                            class="hidden"
                                        />
                                    </label>
                                </div>

                                <!-- Selected File Display -->
                                <div
                                    v-if="selectedFile"
                                    class="mt-3 p-3 bg-white border border-gray-200 rounded-lg"
                                >
                                    <div
                                        class="flex items-center justify-between"
                                    >
                                        <div
                                            class="flex items-center space-x-3"
                                        >
                                            <div class="flex-shrink-0">
                                                <svg
                                                    class="w-8 h-8 text-gray-400"
                                                    fill="none"
                                                    stroke="currentColor"
                                                    viewBox="0 0 24 24"
                                                >
                                                    <path
                                                        stroke-linecap="round"
                                                        stroke-linejoin="round"
                                                        stroke-width="2"
                                                        d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"
                                                    ></path>
                                                </svg>
                                            </div>
                                            <div>
                                                <p
                                                    class="text-sm font-medium text-gray-900"
                                                >
                                                    {{ selectedFile.name }}
                                                </p>
                                                <p
                                                    class="text-xs text-gray-500"
                                                >
                                                    {{
                                                        formatFileSize(
                                                            selectedFile.size
                                                        )
                                                    }}
                                                </p>
                                            </div>
                                        </div>
                                        <button
                                            type="button"
                                            @click="removeFile"
                                            class="text-red-400 hover:text-red-600 transition-colors"
                                        >
                                            <svg
                                                class="w-5 h-5"
                                                fill="none"
                                                stroke="currentColor"
                                                viewBox="0 0 24 24"
                                            >
                                                <path
                                                    stroke-linecap="round"
                                                    stroke-linejoin="round"
                                                    stroke-width="2"
                                                    d="M6 18L18 6M6 6l12 12"
                                                ></path>
                                            </svg>
                                        </button>
                                    </div>
                                </div>

                                <div
                                    v-if="errors.file"
                                    class="mt-1 text-sm text-red-600"
                                >
                                    {{ errors.file[0] }}
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Document Classification Section -->
                    <div class="bg-gray-50 p-4 rounded-lg">
                        <h4
                            class="text-lg font-medium text-gray-900 mb-4 flex items-center"
                        >
                            <svg
                                class="w-5 h-5 text-indigo-600 mr-2"
                                fill="none"
                                stroke="currentColor"
                                viewBox="0 0 24 24"
                            >
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-width="2"
                                    d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"
                                ></path>
                            </svg>
                            Document Classification
                        </h4>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label
                                    class="block text-sm font-medium text-gray-700 mb-2"
                                >
                                    Document Type
                                    <span class="text-red-500">*</span>
                                </label>
                                <div class="w-full p-2 border-gray-300 rounded-lg bg-gray-50 text-gray-700">
                                    {{ getDocumentTypeName(qrData.documentTypeId) }}
                                </div>
                            </div>

                            <div>
                                <label
                                    class="block text-sm font-medium text-gray-700 mb-2"
                                >
                                    Priority Level
                                    <span class="text-red-500">*</span>
                                </label>
                                <select
                                    v-model="form.priority"
                                    required
                                    class="w-full p-2 border-gray-300 rounded-lg shadow-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors"
                                    :class="{
                                        'border-red-300 focus:ring-red-500 focus:border-red-500':
                                            errors.priority,
                                    }"
                                >
                                    <option value="">Select Priority</option>
                                    <option value="low" class="text-green-600">
                                        🟢 Low
                                    </option>
                                    <option
                                        value="medium"
                                        class="text-yellow-600"
                                    >
                                        🟡 Medium
                                    </option>
                                    <option
                                        value="high"
                                        class="text-orange-600"
                                    >
                                        🟠 High
                                    </option>
                                    <option value="urgent" class="text-red-600">
                                        🔴 Urgent
                                    </option>
                                </select>
                                <div
                                    v-if="errors.priority"
                                    class="mt-1 text-sm text-red-600"
                                >
                                    {{ errors.priority[0] }}
                                </div>
                            </div>

                            <div>
                                <label
                                    class="block text-sm font-medium text-gray-700 mb-2"
                                >
                                    Department
                                    <span class="text-red-500">*</span>
                                </label>
                                <select
                                    v-model="form.department_id"
                                    required
                                    class="w-full p-2 border-gray-300 rounded-lg shadow-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors"
                                    :class="{
                                        'border-red-300 focus:ring-red-500 focus:border-red-500':
                                            errors.department_id,
                                    }"
                                >
                                    <option value="">Select Department</option>
                                    <option
                                        v-for="department in departments"
                                        :key="department.id"
                                        :value="department.id"
                                    >
                                        {{ department.name }}
                                    </option>
                                </select>
                                <div
                                    v-if="errors.department_id"
                                    class="mt-1 text-sm text-red-600"
                                >
                                    {{ errors.department_id[0] }}
                                </div>
                            </div>

                            <div>
                                <label
                                    class="block text-sm font-medium text-gray-700 mb-2"
                                    >Security Level</label
                                >
                                <select
                                    v-model="form.security_level"
                                    class="w-full p-2 border-gray-300 rounded-lg shadow-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors"
                                    :class="{
                                        'border-red-300 focus:ring-red-500 focus:border-red-500':
                                            errors.security_level,
                                    }"
                                >
                                    <option value="public">🌐 Public</option>
                                    <option value="internal">
                                        🏢 Internal
                                    </option>
                                    <option value="confidential">
                                        🔒 Confidential
                                    </option>
                                    <option value="restricted">
                                        🚫 Restricted
                                    </option>
                                </select>
                                <div
                                    v-if="errors.security_level"
                                    class="mt-1 text-sm text-red-600"
                                >
                                    {{ errors.security_level[0] }}
                                </div>
                            </div>

                            <div class="md:col-span-2">
                                <label
                                    class="block text-sm font-medium text-gray-700 mb-2"
                                    >Deadline (Optional)</label
                                >
                                <input
                                    v-model="form.deadline"
                                    type="date"
                                    class="w-full p-2 border-gray-300 rounded-lg shadow-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors"
                                    :class="{
                                        'border-red-300 focus:ring-red-500 focus:border-red-500':
                                            errors.deadline,
                                    }"
                                    :min="
                                        new Date().toISOString().split('T')[0]
                                    "
                                />
                                <div
                                    v-if="errors.deadline"
                                    class="mt-1 text-sm text-red-600"
                                >
                                    {{ errors.deadline[0] }}
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Error Messages -->
                    <div
                        v-if="error"
                        class="bg-red-50 border border-red-200 rounded-lg p-4"
                    >
                        <div class="flex items-center">
                            <svg
                                class="w-5 h-5 text-red-400 mr-2"
                                fill="none"
                                stroke="currentColor"
                                viewBox="0 0 24 24"
                            >
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-width="2"
                                    d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"
                                ></path>
                            </svg>
                            <div class="text-red-800">
                                <div class="font-medium">
                                    Error creating document
                                </div>
                                <div class="text-sm mt-1">{{ error }}</div>
                            </div>
                        </div>
                    </div>

                    <div
                        v-if="
                            validationErrors &&
                            Object.keys(validationErrors).length > 0
                        "
                        class="bg-red-50 border border-red-200 rounded-lg p-4"
                    >
                        <div class="flex items-start">
                            <svg
                                class="w-5 h-5 text-red-400 mr-2 mt-0.5"
                                fill="none"
                                stroke="currentColor"
                                viewBox="0 0 24 24"
                            >
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-width="2"
                                    d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"
                                ></path>
                            </svg>
                            <div class="text-red-800">
                                <div class="font-medium">
                                    Please fix the following errors:
                                </div>
                                <ul class="mt-2 text-sm space-y-1">
                                    <li
                                        v-for="(
                                            fieldErrors, field
                                        ) in validationErrors"
                                        :key="field"
                                        class="flex items-start"
                                    >
                                        <span class="font-medium capitalize"
                                            >{{
                                                field.replace("_", " ")
                                            }}:</span
                                        >
                                        <span class="ml-1">{{
                                            fieldErrors.join(", ")
                                        }}</span>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>

                    <!-- Action Buttons -->
                    <div
                        class="flex justify-end space-x-3 pt-4 border-t border-gray-200"
                    >
                        <button
                            type="button"
                            @click="$emit('close')"
                            class="px-6 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition-colors"
                        >
                            Cancel
                        </button>
                        <button
                            type="submit"
                            :disabled="loading || !isFormValid"
                            class="px-6 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 disabled:opacity-50 disabled:cursor-not-allowed transition-colors flex items-center"
                        >
                            <svg
                                v-if="loading"
                                class="animate-spin -ml-1 mr-2 h-4 w-4 text-white"
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
                            <span v-if="loading">Creating Document...</span>
                            <span v-else>Create Document</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, reactive, onMounted, computed } from "vue";
import { useDocumentStore } from "../stores/documents";
import QRGenerator from "./QRGenerator.vue";
import axios from "axios";

const documentStore = useDocumentStore();

const currentStep = ref(1);
const qrData = ref(null);

const form = reactive({
    title: "",
    description: "",
    priority: "",
    document_type_id: "",
    department_id: "",
    security_level: "internal",
    deadline: "",
});

const loading = ref(false);
const error = ref("");
const validationErrors = ref({});
const errors = ref({});
const documentTypes = ref([]);
const departments = ref([]);
const selectedFile = ref(null);
const isDragOver = ref(false);

// Computed properties
const isFormValid = computed(() => {
    return (
        form.title &&
        form.document_type_id &&
        form.department_id &&
        form.priority
    );
});

const getCompletionPercentage = () => {
    const requiredFields = [
        "title",
        "document_type_id",
        "department_id",
        "priority",
    ];
    const optionalFields = ["description", "deadline"];
    const allFields = [...requiredFields, ...optionalFields];

    let filledFields = 0;
    requiredFields.forEach((field) => {
        if (form[field]) filledFields += 2; // Required fields count double
    });
    optionalFields.forEach((field) => {
        if (form[field]) filledFields += 1;
    });
    if (selectedFile.value) filledFields += 1;

    const maxScore = requiredFields.length * 2 + optionalFields.length + 1; // +1 for file
    return Math.round((filledFields / maxScore) * 100);
};

// File handling methods
const onFile = (event) => {
    const file = event.target.files[0];
    if (file) {
        if (file.size > 10 * 1024 * 1024) {
            // 10MB limit
            errors.value.file = ["File size must be less than 10MB"];
            return;
        }
        selectedFile.value = file;
        errors.value.file = null;
    }
};

const handleFileDrop = (event) => {
    isDragOver.value = false;
    const files = event.dataTransfer.files;
    if (files.length > 0) {
        const file = files[0];
        if (file.size > 10 * 1024 * 1024) {
            errors.value.file = ["File size must be less than 10MB"];
            return;
        }
        selectedFile.value = file;
        errors.value.file = null;
    }
};

const removeFile = () => {
    selectedFile.value = null;
    if (document.getElementById("file-upload")) {
        document.getElementById("file-upload").value = "";
    }
};

const formatFileSize = (bytes) => {
    if (bytes === 0) return "0 Bytes";
    const k = 1024;
    const sizes = ["Bytes", "KB", "MB", "GB"];
    const i = Math.floor(Math.log(bytes) / Math.log(k));
    return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + " " + sizes[i];
};

// API methods
const fetchDocumentTypes = async () => {
    try {
        const response = await axios.get("/api/document-types");
        documentTypes.value = response.data;
    } catch (error) {
        console.error("Error fetching document types:", error);
    }
};

const fetchDepartments = async () => {
    try {
        const response = await axios.get("/api/departments");
        departments.value = response.data.data;
    } catch (error) {
        console.error("Error fetching departments:", error);
    }
};

const onQRGenerated = (data) => {
    qrData.value = data;
    form.document_type_id = data.documentTypeId;
    currentStep.value = 2;
};

const getDocumentTypeName = (typeId) => {
    const type = documentTypes.value.find(t => t.id == typeId);
    return type ? type.name : 'Unknown';
};

const createDocument = async () => {
    loading.value = true;
    error.value = "";
    validationErrors.value = {};
    errors.value = {};

    try {
        const formData = new FormData();

        // Find the selected document type to get its code/type
        const selectedDocumentType = documentTypes.value.find(
            (type) => type.id == form.document_type_id
        );

        // Append form fields
        Object.keys(form).forEach((key) => {
            if (form[key] !== null && form[key] !== "") {
                formData.append(key, form[key]);
            }
        });

        // Add the type field based on document type code
        if (selectedDocumentType) {
            formData.append(
                "type",
                selectedDocumentType.code || selectedDocumentType.name
            );
        }

        // Add QR data if available
        if (qrData.value) {
            formData.append("document_number", qrData.value.documentNumber);
            formData.append("qr_code_path", qrData.value.qrCodePath);
        }

        // Append file if selected
        if (selectedFile.value) {
            formData.append("file", selectedFile.value);
        }

        const response = await axios.post("/api/documents", formData, {
            headers: {
                "Content-Type": "multipart/form-data",
            },
        });

        // Reset form
        Object.keys(form).forEach((key) => {
            form[key] = key === "security_level" ? "internal" : "";
        });
        selectedFile.value = null;
        currentStep.value = 1;
        qrData.value = null;
        if (document.getElementById("file-upload")) {
            document.getElementById("file-upload").value = "";
        }

        // Emit success event
        emit("created", response.data);
        emit("close");
    } catch (err) {
        if (err.response?.status === 422) {
            validationErrors.value = err.response.data.errors || {};
            errors.value = err.response.data.errors || {};
        } else {
            error.value =
                err.response?.data?.message ||
                "An error occurred while creating the document.";
        }
    } finally {
        loading.value = false;
    }
};

// Component lifecycle
onMounted(() => {
    fetchDocumentTypes();
    fetchDepartments();
});

// Define emits
const emit = defineEmits(["close", "created"]);

// Define components
const components = {
    QRGenerator,
};
</script>
