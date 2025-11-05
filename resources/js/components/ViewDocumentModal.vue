<template>
    <div
        class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50"
        @click="$emit('close')"
    >
        <div
            class="relative top-4 mx-auto p-0 border w-11/12 md:w-4/5 lg:w-3/4 xl:w-2/3 max-w-6xl shadow-2xl rounded-lg bg-white"
            @click.stop
        >
            <!-- Header -->
            <div class="bg-gradient-to-r from-blue-600 to-indigo-600 text-white px-6 py-4 rounded-t-lg">
                <div class="flex justify-between items-center">
                    <div>
                        <h2 class="text-xl font-bold">{{ document?.title || 'Document Details' }}</h2>
                        <p class="text-blue-100 text-sm">{{ document?.document_number }}</p>
                    </div>
                    <div class="flex items-center space-x-3">
                        <div class="flex space-x-2">
                            <span
                                :class="getStatusClass(document?.status)"
                                class="px-3 py-1 rounded-full text-xs font-medium"
                            >
                                {{ document?.status }}
                            </span>
                            <span
                                :class="getPriorityClass(document?.priority)"
                                class="px-3 py-1 rounded-full text-xs font-medium"
                            >
                                {{ document?.priority }}
                            </span>
                        </div>
                        <button
                            @click="$emit('close')"
                            class="text-white hover:text-gray-200 transition-colors"
                        >
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </button>
                    </div>
                </div>
            </div>

            <!-- Content -->
            <div class="max-h-[80vh] overflow-y-auto">
                <div class="p-6">
                    <!-- Document Information Grid -->
                    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">
                        <!-- Main Information -->
                        <div class="lg:col-span-2 space-y-6">
                            <!-- Basic Info Card -->
                            <div class="bg-gray-50 rounded-lg p-4">
                                <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                                    <svg class="w-5 h-5 mr-2 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                    </svg>
                                    Document Information
                                </h3>
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700">Document Type</label>
                                        <p class="mt-1 text-sm text-gray-900">{{ document?.document_type?.name || document?.type || 'N/A' }}</p>
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700">Department</label>
                                        <p class="mt-1 text-sm text-gray-900">{{ document?.department?.name || 'N/A' }}</p>
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700">Security Level</label>
                                        <span :class="getSecurityLevelClass(document?.security_level)" class="inline-flex px-2 py-1 text-xs font-semibold rounded-full">
                                            {{ document?.security_level || 'N/A' }}
                                        </span>
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700">Created By</label>
                                        <p class="mt-1 text-sm text-gray-900">{{ document?.creator?.name || document?.sender?.name || 'N/A' }}</p>
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700">Created Date</label>
                                        <p class="mt-1 text-sm text-gray-900">{{ formatDate(document?.created_at) }}</p>
                                    </div>
                                    <div v-if="document?.deadline">
                                        <label class="block text-sm font-medium text-gray-700">Deadline</label>
                                        <p class="mt-1 text-sm" :class="isOverdue(document.deadline) ? 'text-red-600 font-medium' : 'text-gray-900'">
                                            {{ formatDate(document.deadline) }}
                                            <span v-if="isOverdue(document.deadline)" class="text-xs">(Overdue)</span>
                                        </p>
                                    </div>
                                </div>
                            </div>

                            <!-- Description -->
                            <div v-if="document?.description" class="bg-gray-50 rounded-lg p-4">
                                <h3 class="text-lg font-semibold text-gray-900 mb-3 flex items-center">
                                    <svg class="w-5 h-5 mr-2 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h7"></path>
                                    </svg>
                                    Description
                                </h3>
                                <p class="text-sm text-gray-700 leading-relaxed">{{ document.description }}</p>
                            </div>

                            <!-- Attachments -->
                            <div class="bg-gray-50 rounded-lg p-4">
                                <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                                    <svg class="w-5 h-5 mr-2 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"></path>
                                    </svg>
                                    Attachments
                                </h3>
                                <div v-if="document?.attachments && document.attachments.length > 0" class="space-y-2">
                                    <div
                                        v-for="attachment in document.attachments"
                                        :key="attachment.id"
                                        class="flex items-center justify-between p-3 bg-white border border-gray-200 rounded-md hover:bg-gray-50 transition-colors"
                                    >
                                        <div class="flex items-center">
                                            <div class="flex-shrink-0 mr-3">
                                                <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                                </svg>
                                            </div>
                                            <div>
                                                <p class="text-sm font-medium text-gray-900">{{ attachment.original_name }}</p>
                                                <p class="text-xs text-gray-500">{{ formatFileSize(attachment.file_size) }}</p>
                                            </div>
                                        </div>
                                        <button
                                            @click="downloadAttachment(attachment)"
                                            class="text-blue-600 hover:text-blue-800 text-sm font-medium flex items-center"
                                        >
                                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                            </svg>
                                            Download
                                        </button>
                                    </div>
                                </div>
                                <div v-else-if="document?.file_path" class="space-y-2">
                                    <div class="flex items-center justify-between p-3 bg-white border border-gray-200 rounded-md hover:bg-gray-50 transition-colors">
                                        <div class="flex items-center">
                                            <div class="flex-shrink-0 mr-3">
                                                <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                                </svg>
                                            </div>
                                            <div>
                                                <p class="text-sm font-medium text-gray-900">{{ document.file_name || document.title }}</p>
                                                <p class="text-xs text-gray-500">Document file</p>
                                            </div>
                                        </div>
                                        <button
                                            @click="downloadFile(document)"
                                            class="text-blue-600 hover:text-blue-800 text-sm font-medium flex items-center"
                                        >
                                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                            </svg>
                                            Download
                                        </button>
                                    </div>
                                </div>
                                <div v-else class="text-gray-500 text-sm text-center py-4">
                                    No attachments available
                                </div>
                            </div>
                        </div>

                        <!-- Sidebar -->
                        <div class="space-y-6">
                            <!-- QR Code -->
                            <div v-if="document?.qr_code || document?.qr_code_path" class="bg-gray-50 rounded-lg p-4">
                                <h3 class="text-lg font-semibold text-gray-900 mb-3 flex items-center">
                                    <svg class="w-5 h-5 mr-2 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"></path>
                                    </svg>
                                    QR Code
                                </h3>
                                <div class="text-center">
                                    <img
                                        :src="document.qr_code?.qr_image_url || `/storage/${document.qr_code_path}`"
                                        alt="QR Code"
                                        class="mx-auto w-32 h-32 border border-gray-200 rounded"
                                    />
                                    <div v-if="document.qr_code" class="mt-3 text-xs text-gray-600">
                                        <p>Scans: {{ document.qr_code.scan_count || 0 }}</p>
                                        <p v-if="document.qr_code.last_scanned_at">
                                            Last: {{ formatDate(document.qr_code.last_scanned_at) }}
                                        </p>
                                    </div>
                                    <button
                                        @click="copyShareLink"
                                        class="mt-2 text-blue-600 hover:text-blue-800 text-sm font-medium"
                                    >
                                        Copy Share Link
                                    </button>
                                    <button
                                        v-if="document?.qr_code_path || document?.qr_code?.qr_image_url"
                                        @click="printQR"
                                        class="mt-2 ml-3 text-gray-700 hover:text-gray-900 text-sm font-medium"
                                    >
                                        Print QR
                                    </button>
                                </div>
                            </div>

                            <!-- Quick Actions -->
                            <div class="bg-gray-50 rounded-lg p-4">
                                <h3 class="text-lg font-semibold text-gray-900 mb-3 flex items-center">
                                    <svg class="w-5 h-5 mr-2 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                                    </svg>
                                    Quick Actions
                                </h3>
                                <div class="space-y-2">
                                    <button
                                        @click="$emit('edit', document)"
                                        class="w-full px-3 py-2 bg-blue-600 text-white text-sm rounded-md hover:bg-blue-700 transition-colors flex items-center justify-center"
                                    >
                                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                        </svg>
                                        Edit Document
                                    </button>
                                    <button
                                        v-if="document?.file_path || (document?.attachments && document.attachments.length > 0)"
                                        @click="downloadFile(document)"
                                        class="w-full px-3 py-2 bg-green-600 text-white text-sm rounded-md hover:bg-green-700 transition-colors flex items-center justify-center"
                                    >
                                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                        </svg>
                                        Download
                                    </button>
                                    <button
                                        @click="$emit('delete', document)"
                                        class="w-full px-3 py-2 bg-red-600 text-white text-sm rounded-md hover:bg-red-700 transition-colors flex items-center justify-center"
                                    >
                                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                        </svg>
                                        Delete
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Timeline -->
                    <div v-if="document?.logs && document.logs.length > 0" class="bg-gray-50 rounded-lg p-4">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                            <svg class="w-5 h-5 mr-2 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            Activity Timeline
                        </h3>
                        <div class="flow-root">
                            <ul class="-mb-8">
                                <li v-for="(log, index) in document.logs" :key="log.id">
                                    <div class="relative pb-8">
                                        <span
                                            v-if="index !== document.logs.length - 1"
                                            class="absolute top-4 left-4 -ml-px h-full w-0.5 bg-gray-200"
                                            aria-hidden="true"
                                        ></span>
                                        <div class="relative flex space-x-3">
                                            <div>
                                                <span class="h-8 w-8 rounded-full bg-blue-500 flex items-center justify-center ring-8 ring-white">
                                                    <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                                    </svg>
                                                </span>
                                            </div>
                                            <div class="min-w-0 flex-1 pt-1.5 flex justify-between space-x-4">
                                                <div>
                                                    <p class="text-sm text-gray-900">
                                                        <span class="font-medium">{{ log.user?.name || 'System' }}</span>
                                                        {{ log.description }}
                                                    </p>
                                                </div>
                                                <div class="text-right text-sm whitespace-nowrap text-gray-500">
                                                    {{ formatDate(log.created_at) }}
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Footer -->
            <div class="bg-gray-50 px-6 py-3 rounded-b-lg border-t border-gray-200">
                <div class="flex justify-between items-center">
                    <div class="text-sm text-gray-500">
                        Last updated: {{ formatDate(document?.updated_at) }}
                    </div>
                    <button
                        @click="$emit('close')"
                        class="px-4 py-2 bg-gray-600 text-white rounded-md hover:bg-gray-700 transition-colors"
                    >
                        Close
                    </button>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { computed } from 'vue'

const props = defineProps({
    document: {
        type: Object,
        required: true
    }
})

const emit = defineEmits(['close', 'edit', 'delete'])

const getStatusClass = (status) => {
    const classes = {
        draft: 'bg-gray-100 text-gray-800',
        submitted: 'bg-blue-100 text-blue-800',
        under_review: 'bg-yellow-100 text-yellow-800',
        approved: 'bg-green-100 text-green-800',
        rejected: 'bg-red-100 text-red-800',
        completed: 'bg-purple-100 text-purple-800',
        pending: 'bg-orange-100 text-orange-800'
    }
    return classes[status] || 'bg-gray-100 text-gray-800'
}

const getPriorityClass = (priority) => {
    const classes = {
        low: 'bg-green-100 text-green-800',
        medium: 'bg-yellow-100 text-yellow-800',
        high: 'bg-orange-100 text-orange-800',
        urgent: 'bg-red-100 text-red-800'
    }
    return classes[priority] || 'bg-gray-100 text-gray-800'
}

const getSecurityLevelClass = (level) => {
    const classes = {
        public: 'bg-green-100 text-green-800',
        internal: 'bg-blue-100 text-blue-800',
        confidential: 'bg-yellow-100 text-yellow-800',
        secret: 'bg-red-100 text-red-800'
    }
    return classes[level] || 'bg-gray-100 text-gray-800'
}

const formatDate = (date) => {
    if (!date) return 'N/A'
    return new Date(date).toLocaleDateString('en-US', {
        year: 'numeric',
        month: 'short',
        day: 'numeric',
        hour: '2-digit',
        minute: '2-digit'
    })
}

const formatFileSize = (bytes) => {
    if (!bytes || bytes === 0) return '0 Bytes'
    const k = 1024
    const sizes = ['Bytes', 'KB', 'MB', 'GB']
    const i = Math.floor(Math.log(bytes) / Math.log(k))
    return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i]
}

const isOverdue = (deadline) => {
    if (!deadline) return false
    return new Date(deadline) < new Date()
}

const downloadAttachment = (attachment) => {
    window.open(`/api/attachments/${attachment.id}/download`, '_blank')
}

const downloadFile = (document) => {
    if (document.attachments && document.attachments.length > 0) {
        downloadAttachment(document.attachments[0])
    } else if (document.file_path) {
        window.open(`/storage/${document.file_path}`, '_blank')
    }
}

const copyShareLink = () => {
    const url = `${window.location.origin}/documents/${props.document.id}`
    navigator.clipboard.writeText(url).then(() => {
        // You might want to show a toast notification here
        alert('Share link copied to clipboard!')
    })
}

const printQR = () => {
    const src = props.document.qr_code?.qr_image_url || (props.document.qr_code_path ? `/storage/${props.document.qr_code_path}` : null)
    if (!src) {
        alert('QR code not available for this document')
        return
    }
    const win = window.open('', '_blank')
    if (!win) return
    win.document.write(`
        <html>
            <head>
                <title>QR Code</title>
                <style>
                    @page { size: 1in 1in; margin: 0; }
                    html, body { height: 100%; margin: 0; }
                    body { display: flex; align-items: center; justify-content: center; }
                    img { width: 1in; height: 1in; display: block; }
                </style>
            </head>
            <body>
                <img src="${src}" alt="QR Code" />
            </body>
        </html>
    `)
    win.document.close()
    win.focus()
    win.print()
}
</script>