<template>
    <div class="min-h-screen bg-gray-50">
        <!-- Header -->
        <div class="bg-white shadow-sm border-b border-gray-200">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between items-center py-6">
                    <div class="flex items-center space-x-4">
                        <!-- Export Dropdown -->
                        <div class="relative" ref="exportDropdownRef">
                            <button
                                @click="
                                    showExportDropdown = !showExportDropdown
                                "
                                class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-colors duration-150"
                            >
                                <svg
                                    class="w-4 h-4 mr-2"
                                    fill="none"
                                    stroke="currentColor"
                                    viewBox="0 0 24 24"
                                >
                                    <path
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-width="2"
                                        d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"
                                    ></path>
                                </svg>
                                Export
                                <svg
                                    class="w-4 h-4 ml-2"
                                    fill="none"
                                    stroke="currentColor"
                                    viewBox="0 0 24 24"
                                >
                                    <path
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-width="2"
                                        d="M19 9l-7 7-7-7"
                                    ></path>
                                </svg>
                            </button>

                            <!-- Export Dropdown Menu -->
                            <div
                                v-show="showExportDropdown"
                                class="absolute right-0 mt-2 w-48 rounded-md shadow-lg bg-white ring-1 ring-black ring-opacity-5 z-50"
                            >
                                <div class="py-1">
                                    <button
                                        v-if="canExportDocuments()"
                                        @click="exportDocuments('csv')"
                                        class="flex items-center w-full px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 hover:text-gray-900 transition-colors duration-150"
                                    >
                                        <svg
                                            class="w-4 h-4 mr-3 text-green-500"
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
                                        Export as CSV
                                    </button>
                                    <button
                                        v-if="canExportDocuments()"
                                        @click="exportDocuments('excel')"
                                        class="flex items-center w-full px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 hover:text-gray-900 transition-colors duration-150"
                                    >
                                        <svg
                                            class="w-4 h-4 mr-3 text-blue-500"
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
                                        Export as Excel
                                    </button>
                                    <hr
                                        v-if="canExportDocuments()"
                                        class="my-1"
                                    />
                                    <button
                                        v-if="canExportDocuments()"
                                        @click="exportSelectedDocuments('csv')"
                                        :disabled="
                                            selectedDocuments.length === 0
                                        "
                                        class="flex items-center w-full px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 hover:text-gray-900 disabled:opacity-50 disabled:cursor-not-allowed transition-colors duration-150"
                                    >
                                        >
                                        <svg
                                            class="w-4 h-4 mr-3 text-purple-500"
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
                                        Export Selected ({{
                                            selectedDocuments.length
                                        }})
                                    </button>
                                </div>
                            </div>
                        </div>

                        <!-- Create Document Button -->
                        <button
                            @click="showCreateModal = true"
                            class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-colors duration-150"
                        >
                            <svg
                                class="w-4 h-4 mr-2"
                                fill="none"
                                stroke="currentColor"
                                viewBox="0 0 24 24"
                            >
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-width="2"
                                    d="M12 4v16m8-8H4"
                                ></path>
                            </svg>
                            Create Document
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Main Content -->
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
            <div class="space-y-6">
                <!-- Page Header -->
                <div
                    class="flex flex-col sm:flex-row sm:items-center sm:justify-between"
                >
                    <div>
                        <h1 class="text-2xl font-bold text-gray-900">
                            Documents
                        </h1>
                        <p class="mt-1 text-sm text-gray-500">
                            Manage and track all documents in the system
                        </p>
                    </div>
                </div>

                <!-- Search and Filters -->
                <div class="space-y-4">
                    <!-- Search Bar -->
                    <div class="flex flex-col sm:flex-row gap-4">
                        <div class="flex-1">
                            <div class="relative">
                                <div
                                    class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none"
                                >
                                    <svg
                                        class="h-5 w-5 text-gray-400"
                                        fill="none"
                                        stroke="currentColor"
                                        viewBox="0 0 24 24"
                                    >
                                        <path
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                            stroke-width="2"
                                            d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"
                                        ></path>
                                    </svg>
                                </div>
                                <input
                                    v-model="searchQuery"
                                    type="text"
                                    class="block w-full pl-10 pr-3 py-2 border border-gray-300 rounded-md leading-5 bg-white placeholder-gray-500 focus:outline-none focus:placeholder-gray-400 focus:ring-1 focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                                    placeholder="Search documents..."
                                />
                            </div>
                        </div>

                        <!-- Quick Filters -->
                        <div class="flex flex-wrap gap-2">
                            <select
                                v-model="filters.status"
                                class="block px-3 py-2 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-1 focus:ring-indigo-500 focus:border-indigo-500"
                            >
                                <option value="">All Status</option>
                                <option value="pending">Pending</option>
                                <option value="approved">Approved</option>
                                <option value="under_review">
                                    Under Review
                                </option>
                                <option value="completed">Completed</option>
                            </select>

                            <select
                                v-model="filters.priority"
                                class="block px-3 py-2 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-1 focus:ring-indigo-500 focus:border-indigo-500"
                            >
                                <option value="">All Priority</option>
                                <option value="low">Low</option>
                                <option value="medium">Medium</option>
                                <option value="high">High</option>
                                <option value="urgent">Urgent</option>
                            </select>

                            <button
                                @click="toggleAdvancedFilters"
                                class="inline-flex items-center px-3 py-2 border border-gray-300 rounded-md text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
                            >
                                <svg
                                    class="w-4 h-4 mr-1"
                                    fill="none"
                                    stroke="currentColor"
                                    viewBox="0 0 24 24"
                                >
                                    <path
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-width="2"
                                        d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.414A1 1 0 013 6.707V4z"
                                    ></path>
                                </svg>
                                Filters
                            </button>
                        </div>
                    </div>

                    <!-- Advanced Filters -->
                    <div
                        v-show="showAdvancedFilters"
                        class="bg-gray-50 p-4 rounded-lg border"
                    >
                        <div
                            class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4"
                        >
                            <div>
                                <label
                                    class="block text-sm font-medium text-gray-700 mb-1"
                                    >Department</label
                                >
                                <select
                                    v-model="filters.department"
                                    class="block w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-1 focus:ring-indigo-500 focus:border-indigo-500"
                                >
                                    <option value="">All Departments</option>
                                    <option value="hr">Human Resources</option>
                                    <option value="finance">Finance</option>
                                    <option value="it">IT</option>
                                    <option value="legal">Legal</option>
                                </select>
                            </div>

                            <div>
                                <label
                                    class="block text-sm font-medium text-gray-700 mb-1"
                                    >Document Type</label
                                >
                                <select
                                    v-model="filters.type"
                                    class="block w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-1 focus:ring-indigo-500 focus:border-indigo-500"
                                >
                                    <option value="">All Types</option>
                                    <option value="memo">Memo</option>
                                    <option value="letter">Letter</option>
                                    <option value="report">Report</option>
                                    <option value="contract">Contract</option>
                                </select>
                            </div>

                            <div>
                                <label
                                    class="block text-sm font-medium text-gray-700 mb-1"
                                    >Date From</label
                                >
                                <input
                                    v-model="filters.dateFrom"
                                    type="date"
                                    class="block w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-1 focus:ring-indigo-500 focus:border-indigo-500"
                                />
                            </div>

                            <div>
                                <label
                                    class="block text-sm font-medium text-gray-700 mb-1"
                                    >Date To</label
                                >
                                <input
                                    v-model="filters.dateTo"
                                    type="date"
                                    class="block w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-1 focus:ring-indigo-500 focus:border-indigo-500"
                                />
                            </div>
                        </div>

                        <div class="mt-4 flex justify-end space-x-2">
                            <button
                                @click="clearFilters"
                                class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
                            >
                                Clear Filters
                            </button>
                            <button
                                @click="applyFilters"
                                class="px-4 py-2 text-sm font-medium text-white bg-indigo-600 border border-transparent rounded-md hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
                            >
                                Apply Filters
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Bulk Actions -->
                <div
                    v-if="selectedDocuments.length > 0"
                    class="bg-indigo-50 border border-indigo-200 rounded-lg p-4"
                >
                    <div class="flex items-center justify-between">
                        <div class="flex items-center">
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
                                    d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"
                                ></path>
                            </svg>
                            <span class="text-sm font-medium text-indigo-900">
                                {{ selectedDocuments.length }} document{{
                                    selectedDocuments.length !== 1 ? "s" : ""
                                }}
                                selected
                            </span>
                        </div>
                        <div class="flex space-x-2">
                            <button
                                v-if="canUpdateDocuments()"
                                @click="showBulkStatusModal = true"
                                class="inline-flex items-center px-3 py-1.5 border border-transparent text-xs font-medium rounded text-indigo-700 bg-indigo-100 hover:bg-indigo-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
                            >
                                Change Status
                            </button>
                            <button
                                @click="showBulkAssignModal = true"
                                class="inline-flex items-center px-3 py-1.5 border border-transparent text-xs font-medium rounded text-indigo-700 bg-indigo-100 hover:bg-indigo-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
                            >
                                Send to Department
                            </button>
                            <button
                                @click="selectedDocuments = []"
                                class="inline-flex items-center px-3 py-1.5 border border-gray-300 text-xs font-medium rounded text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
                            >
                                Clear Selection
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Mobile Card View -->
                <div class="mobile-card-view md:hidden">
                    <!-- Loading Skeleton for Mobile -->
                    <div v-if="loading" class="space-y-4">
                        <div
                            v-for="i in 5"
                            :key="i"
                            class="mobile-card-skeleton"
                        >
                            <div class="flex items-start justify-between mb-3">
                                <div class="flex items-center space-x-3">
                                    <div class="skeleton-checkbox"></div>
                                    <div>
                                        <div
                                            class="skeleton-text skeleton-title"
                                        ></div>
                                        <div
                                            class="skeleton-text skeleton-subtitle"
                                        ></div>
                                    </div>
                                </div>
                                <div class="skeleton-badge"></div>
                            </div>
                            <div class="space-y-2 mb-4">
                                <div
                                    class="skeleton-text skeleton-content"
                                ></div>
                                <div
                                    class="skeleton-text skeleton-content-short"
                                ></div>
                            </div>
                            <div class="flex justify-between items-center">
                                <div class="skeleton-text skeleton-date"></div>
                                <div class="flex space-x-2">
                                    <div class="skeleton-button"></div>
                                    <div class="skeleton-button"></div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Mobile Cards -->
                    <div v-else class="space-y-4">
                        <div
                            v-for="document in documents"
                            :key="document.id"
                            class="document-card"
                        >
                            <div class="flex items-start justify-between mb-3">
                                <div class="flex items-center space-x-3">
                                    <input
                                        type="checkbox"
                                        :value="document.id"
                                        v-model="selectedDocuments"
                                        class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded"
                                    />
                                    <div>
                                        <h3
                                            class="font-medium text-gray-900 text-sm"
                                        >
                                            {{ document.title }}
                                        </h3>
                                        <p class="text-xs text-gray-500">
                                            {{ document.document_number }}
                                        </p>
                                    </div>
                                </div>
                                <span
                                    :class="getStatusClass(document.status)"
                                    class="status-badge"
                                >
                                    {{ document.status }}
                                </span>
                            </div>

                            <div class="space-y-2 mb-4">
                                <p class="text-sm text-gray-600">
                                    {{ document.description }}
                                </p>
                                <div
                                    class="flex items-center text-xs text-gray-500"
                                >
                                    <svg
                                        class="w-4 h-4 mr-1"
                                        fill="none"
                                        stroke="currentColor"
                                        viewBox="0 0 24 24"
                                    >
                                        <path
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                            stroke-width="2"
                                            d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"
                                        ></path>
                                    </svg>
                                    {{ document.created_by?.name || "Unknown" }}
                                </div>
                            </div>

                            <div class="flex justify-between items-center">
                                <span class="text-xs text-gray-500">
                                    {{ formatDate(document.created_at) }}
                                </span>
                                <div class="flex space-x-2">
                                    <button
                                        @click="viewDocument(document)"
                                        class="text-indigo-600 hover:text-indigo-900 text-sm font-medium"
                                    >
                                        View
                                    </button>
                                    <button
                                        @click="editDocument(document)"
                                        class="text-gray-600 hover:text-gray-900 text-sm font-medium"
                                    >
                                        Edit
                                    </button>
                                    <button
                                        v-if="document.qr_code_path"
                                        @click="printQR(document)"
                                        class="text-gray-600 hover:text-gray-900 text-sm font-medium"
                                    >
                                        Print QR
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Documents Table -->
                <div class="table-container documents-table hidden md:block">
                    <!-- Loading Skeleton -->
                    <div v-if="loading" class="loading-skeleton">
                        <div class="skeleton-table">
                            <div class="skeleton-header">
                                <div
                                    v-for="i in 6"
                                    :key="i"
                                    class="skeleton-header-cell"
                                ></div>
                            </div>
                            <div v-for="i in 8" :key="i" class="skeleton-row">
                                <div
                                    v-for="j in 6"
                                    :key="j"
                                    class="skeleton-cell"
                                ></div>
                            </div>
                        </div>
                    </div>

                    <!-- Table -->
                    <div v-else class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
                                    >
                                        <input
                                            type="checkbox"
                                            :checked="
                                                selectedDocuments.length ===
                                                    documents.length &&
                                                documents.length > 0
                                            "
                                            @change="toggleSelectAll"
                                            class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded"
                                        />
                                    </th>
                                    <th
                                        @click="sort('title')"
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider cursor-pointer hover:bg-gray-100"
                                    >
                                        <div
                                            class="flex items-center space-x-2"
                                        >
                                            <svg
                                                class="w-4 h-4 text-gray-400"
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
                                            <span>Document</span>
                                            <span class="text-sm font-bold">{{
                                                getSortIcon("title")
                                            }}</span>
                                        </div>
                                    </th>
                                    <th
                                        @click="sort('status')"
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider cursor-pointer hover:bg-gray-100"
                                    >
                                        <div
                                            class="flex items-center space-x-2"
                                        >
                                            <svg
                                                class="w-4 h-4 text-gray-400"
                                                fill="none"
                                                stroke="currentColor"
                                                viewBox="0 0 24 24"
                                            >
                                                <path
                                                    stroke-linecap="round"
                                                    stroke-linejoin="round"
                                                    stroke-width="2"
                                                    d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"
                                                ></path>
                                            </svg>
                                            <span>Status</span>
                                            <span class="text-sm font-bold">{{
                                                getSortIcon("status")
                                            }}</span>
                                        </div>
                                    </th>
                                    <th
                                        @click="sort('priority')"
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider cursor-pointer hover:bg-gray-100"
                                    >
                                        <div
                                            class="flex items-center space-x-2"
                                        >
                                            <svg
                                                class="w-4 h-4 text-gray-400"
                                                fill="none"
                                                stroke="currentColor"
                                                viewBox="0 0 24 24"
                                            >
                                                <path
                                                    stroke-linecap="round"
                                                    stroke-linejoin="round"
                                                    stroke-width="2"
                                                    d="M7 4V2a1 1 0 011-1h8a1 1 0 011 1v2m-9 0h10m-10 0a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V6a2 2 0 00-2-2z"
                                                ></path>
                                            </svg>
                                            <span>Priority</span>
                                            <span class="text-sm font-bold">{{
                                                getSortIcon("priority")
                                            }}</span>
                                        </div>
                                    </th>
                                    <th
                                        @click="sort('created_at')"
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider cursor-pointer hover:bg-gray-100"
                                    >
                                        <div
                                            class="flex items-center space-x-2"
                                        >
                                            <svg
                                                class="w-4 h-4 text-gray-400"
                                                fill="none"
                                                stroke="currentColor"
                                                viewBox="0 0 24 24"
                                            >
                                                <path
                                                    stroke-linecap="round"
                                                    stroke-linejoin="round"
                                                    stroke-width="2"
                                                    d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"
                                                ></path>
                                            </svg>
                                            <span>Created</span>
                                            <span class="text-sm font-bold">{{
                                                getSortIcon("created_at")
                                            }}</span>
                                        </div>
                                    </th>
                                    <th
                                        @click="sort('deadline')"
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider cursor-pointer hover:bg-gray-100"
                                    >
                                        <div
                                            class="flex items-center space-x-2"
                                        >
                                            <svg
                                                class="w-4 h-4 text-gray-400"
                                                fill="none"
                                                stroke="currentColor"
                                                viewBox="0 0 24 24"
                                            >
                                                <path
                                                    stroke-linecap="round"
                                                    stroke-linejoin="round"
                                                    stroke-width="2"
                                                    d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"
                                                ></path>
                                            </svg>
                                            <span>Deadline</span>
                                            <span class="text-sm font-bold">{{
                                                getSortIcon("deadline")
                                            }}</span>
                                        </div>
                                    </th>
                                    <th
                                        class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
                                    >
                                        <div
                                            class="flex items-center space-x-2"
                                        >
                                            <svg
                                                class="w-4 h-4 text-gray-400"
                                                fill="none"
                                                stroke="currentColor"
                                                viewBox="0 0 24 24"
                                            >
                                                <path
                                                    stroke-linecap="round"
                                                    stroke-linejoin="round"
                                                    stroke-width="2"
                                                    d="M12 5v.01M12 12v.01M12 19v.01M12 6a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2z"
                                                ></path>
                                            </svg>
                                            <span>Actions</span>
                                        </div>
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                <tr
                                    v-for="document in documents"
                                    :key="document.id"
                                    class="hover:bg-gray-50 transition-colors duration-150"
                                    :class="{
                                        'bg-blue-50':
                                            selectedDocuments.includes(
                                                document.id
                                            ),
                                    }"
                                >
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <input
                                            type="checkbox"
                                            :value="document.id"
                                            v-model="selectedDocuments"
                                            class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded"
                                        />
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center">
                                            <div
                                                class="flex-shrink-0 h-10 w-10"
                                            >
                                                <div
                                                    class="h-10 w-10 rounded-lg bg-indigo-100 flex items-center justify-center"
                                                >
                                                    <svg
                                                        class="h-6 w-6 text-indigo-600"
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
                                            <div class="ml-4">
                                                <div
                                                    class="text-sm font-medium text-gray-900"
                                                >
                                                    {{ document.title }}
                                                </div>
                                                <div
                                                    class="text-sm text-gray-500"
                                                >
                                                    {{
                                                        document.document_number
                                                    }}
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span
                                            :class="
                                                getStatusClass(document.status)
                                            "
                                            class="status-badge"
                                        >
                                            {{ document.status }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span
                                            :class="
                                                getPriorityClass(
                                                    document.priority
                                                )
                                            "
                                            class="priority-badge"
                                        >
                                            {{ document.priority }}
                                        </span>
                                    </td>
                                    <td
                                        class="px-6 py-4 whitespace-nowrap text-sm text-gray-500"
                                    >
                                        {{ formatDate(document.created_at) }}
                                    </td>
                                    <td
                                        class="px-6 py-4 whitespace-nowrap text-sm text-gray-500"
                                    >
                                        <span
                                            v-if="document.deadline"
                                            :class="
                                                getDeadlineClass(
                                                    document.deadline
                                                )
                                            "
                                        >
                                            {{ formatDate(document.deadline) }}
                                        </span>
                                        <span v-else class="text-gray-400"
                                            >No deadline</span
                                        >
                                    </td>
                                    <td
                                        class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium"
                                    >
                                        <div
                                            class="flex items-center space-x-2"
                                        >
                                            <button
                                                @click="viewDocument(document)"
                                                class="text-indigo-600 hover:text-indigo-900 transition-colors duration-150"
                                                title="View Document"
                                            >
                                                <svg
                                                    class="w-4 h-4"
                                                    fill="none"
                                                    stroke="currentColor"
                                                    viewBox="0 0 24 24"
                                                >
                                                    <path
                                                        stroke-linecap="round"
                                                        stroke-linejoin="round"
                                                        stroke-width="2"
                                                        d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"
                                                    ></path>
                                                    <path
                                                        stroke-linecap="round"
                                                        stroke-linejoin="round"
                                                        stroke-width="2"
                                                        d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"
                                                    ></path>
                                                </svg>
                                            </button>
                                            <button
                                                @click="editDocument(document)"
                                                class="text-gray-600 hover:text-gray-900 transition-colors duration-150"
                                                title="Edit Document"
                                            >
                                                <svg
                                                    class="w-4 h-4"
                                                    fill="none"
                                                    stroke="currentColor"
                                                    viewBox="0 0 24 24"
                                                >
                                                    <path
                                                        stroke-linecap="round"
                                                        stroke-linejoin="round"
                                                        stroke-width="2"
                                                        d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"
                                                    ></path>
                                                </svg>
                                            </button>
                                            <button
                                                @click="forwardDocument(document)"
                                                v-if="canForwardDocument(document)"
                                                class="text-blue-600 hover:text-blue-900 transition-colors duration-150"
                                                title="Forward Document"
                                            >
                                                <svg
                                                    class="w-4 h-4"
                                                    fill="none"
                                                    stroke="currentColor"
                                                    viewBox="0 0 24 24"
                                                >
                                                    <path
                                                        stroke-linecap="round"
                                                        stroke-linejoin="round"
                                                        stroke-width="2"
                                                        d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"
                                                    ></path>
                                                </svg>
                                            </button>
                                            <button
                                                v-if="document.qr_code_path"
                                                @click="printQR(document)"
                                                class="text-gray-600 hover:text-gray-900 transition-colors duration-150"
                                                title="Print QR"
                                            >
                                                <svg
                                                    class="w-4 h-4"
                                                    fill="none"
                                                    stroke="currentColor"
                                                    viewBox="0 0 24 24"
                                                >
                                                    <path
                                                        stroke-linecap="round"
                                                        stroke-linejoin="round"
                                                        stroke-width="2"
                                                        d="M6 9V2h12v7M6 18h12v4H6v-4zM6 14h12"
                                                    ></path>
                                                </svg>
                                            </button>
                                            <button
                                                v-if="
                                                    canDeleteDocument(document)
                                                "
                                                @click="
                                                    deleteDocument(document)
                                                "
                                                class="text-red-600 hover:text-red-900 transition-colors duration-150"
                                                title="Delete Document"
                                            >
                                                <svg
                                                    class="w-4 h-4"
                                                    fill="none"
                                                    stroke="currentColor"
                                                    viewBox="0 0 24 24"
                                                >
                                                    <path
                                                        stroke-linecap="round"
                                                        stroke-linejoin="round"
                                                        stroke-width="2"
                                                        d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"
                                                    ></path>
                                                </svg>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Pagination -->
                <div
                    v-if="pagination && pagination.total > 0"
                    class="bg-white px-4 py-3 flex items-center justify-between border-t border-gray-200 sm:px-6"
                >
                    <div class="flex-1 flex justify-between sm:hidden">
                        <button
                            @click="previousPage"
                            :disabled="pagination.current_page === 1"
                            class="relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed"
                        >
                            Previous
                        </button>
                        <button
                            @click="nextPage"
                            :disabled="
                                pagination.current_page === pagination.last_page
                            "
                            class="ml-3 relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed"
                        >
                            Next
                        </button>
                    </div>
                    <div
                        class="hidden sm:flex-1 sm:flex sm:items-center sm:justify-between"
                    >
                        <div>
                            <p class="text-sm text-gray-700">
                                Showing
                                <span class="font-medium">{{
                                    pagination.from || 0
                                }}</span>
                                to
                                <span class="font-medium">{{
                                    pagination.to || 0
                                }}</span>
                                of
                                <span class="font-medium">{{
                                    pagination.total || 0
                                }}</span>
                                results
                            </p>
                        </div>
                        <div>
                            <nav
                                class="relative z-0 inline-flex rounded-md shadow-sm -space-x-px"
                                aria-label="Pagination"
                            >
                                <button
                                    @click="previousPage"
                                    :disabled="
                                        !pagination ||
                                        pagination.current_page === 1
                                    "
                                    class="relative inline-flex items-center px-2 py-2 rounded-l-md border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed"
                                >
                                    <span class="sr-only">Previous</span>
                                    <svg
                                        class="h-5 w-5"
                                        fill="currentColor"
                                        viewBox="0 0 20 20"
                                    >
                                        <path
                                            fill-rule="evenodd"
                                            d="M12.707 5.293a1 1 0 010 1.414L9.414 10l3.293 3.293a1 1 0 01-1.414 1.414l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 0z"
                                            clip-rule="evenodd"
                                        />
                                    </svg>
                                </button>

                                <button
                                    v-for="page in visiblePages"
                                    :key="page"
                                    @click="goToPage(page)"
                                    :class="[
                                        page === (pagination?.current_page || 1)
                                            ? 'z-10 bg-indigo-500 border-indigo-500 text-indigo-600'
                                            : 'bg-white border-gray-300 text-gray-500 hover:bg-gray-50',
                                        'relative inline-flex items-center px-4 py-2 border text-sm font-medium',
                                    ]"
                                >
                                    {{ page }}
                                </button>

                                <button
                                    @click="nextPage"
                                    :disabled="
                                        !pagination ||
                                        pagination.current_page ===
                                            pagination.last_page
                                    "
                                    class="relative inline-flex items-center px-2 py-2 rounded-r-md border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed"
                                >
                                    <span class="sr-only">Next</span>
                                    <svg
                                        class="h-5 w-5"
                                        fill="currentColor"
                                        viewBox="0 0 20 20"
                                    >
                                        <path
                                            fill-rule="evenodd"
                                            d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z"
                                            clip-rule="evenodd"
                                        />
                                    </svg>
                                </button>
                            </nav>
                        </div>
                    </div>
                </div>

                <!-- Empty State -->
                <div
                    v-if="!loading && documents.length === 0"
                    class="text-center py-12"
                >
                    <svg
                        class="mx-auto h-12 w-12 text-gray-400"
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
                    <h3 class="mt-2 text-sm font-medium text-gray-900">
                        No documents
                    </h3>
                    <p class="mt-1 text-sm text-gray-500">
                        Get started by creating a new document.
                    </p>
                    <div class="mt-6">
                        <button
                            @click="showCreateModal = true"
                            class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
                        >
                            <svg
                                class="w-4 h-4 mr-2"
                                fill="none"
                                stroke="currentColor"
                                viewBox="0 0 24 24"
                            >
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-width="2"
                                    d="M12 4v16m8-8H4"
                                ></path>
                            </svg>
                            New Document
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Create Document Modal -->
        <CreateDocumentModal
            v-if="showCreateModal"
            @close="showCreateModal = false"
            @created="onDocumentCreated"
        />

        <!-- View Document Modal -->
        <ViewDocumentModal
            v-if="showViewModal && selectedDocument"
            :document="selectedDocument"
            @close="showViewModal = false"
            @edit="editDocument"
            @delete="deleteDocument"
        />

        <!-- Edit Document Modal -->
        <EditDocumentModal
            v-if="showEditModal && editingDocument"
            :document="editingDocument"
            @close="showEditModal = false"
            @updated="onDocumentUpdated"
        />

        <!-- Bulk Status Modal -->
        <div
            v-if="showBulkStatusModal"
            class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50"
        >
            <div
                class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white"
            >
                <div class="mt-3">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-lg font-medium text-gray-900">
                            Change Status
                        </h3>
                        <button
                            @click="showBulkStatusModal = false"
                            class="text-gray-400 hover:text-gray-600"
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

                    <form @submit.prevent="bulkUpdateStatus" class="space-y-4">
                        <div>
                            <label
                                class="block text-sm font-medium text-gray-700 mb-1"
                                >New Status</label
                            >
                            <select
                                v-model="bulkStatus"
                                required
                                class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500"
                            >
                                <option value="">Select Status</option>
                                <option value="pending">Pending</option>
                                <option value="approved">Approved</option>
                                <option value="under_review">
                                    Under Review
                                </option>
                                <option value="completed">Completed</option>
                            </select>
                        </div>

                        <div class="flex justify-end space-x-3 pt-4">
                            <button
                                type="button"
                                @click="showBulkStatusModal = false"
                                class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
                            >
                                Cancel
                            </button>
                            <button
                                type="submit"
                                :disabled="bulkLoading"
                                class="px-4 py-2 text-sm font-medium text-white bg-indigo-600 border border-transparent rounded-md hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 disabled:opacity-50 disabled:cursor-not-allowed"
                            >
                                <span v-if="bulkLoading">Updating...</span>
                                <span v-else>Update Status</span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Bulk Assign Modal -->
        <div
            v-if="showBulkAssignModal"
            class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50"
        >
            <div
                class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white"
            >
                <div class="mt-3">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-lg font-medium text-gray-900">
                            Send to Department
                        </h3>
                        <button
                            @click="showBulkAssignModal = false"
                            class="text-gray-400 hover:text-gray-600"
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

                    <form
                        @submit.prevent="bulkSendDocuments"
                        class="space-y-4"
                    >
                        <div>
                            <label
                                class="block text-sm font-medium text-gray-700 mb-1"
                                >Send To Department</label
                            >
                            <select
                                v-model="bulkAssignee"
                                required
                                class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500"
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
                        </div>

                        <div>
                            <label
                                class="block text-sm font-medium text-gray-700 mb-1"
                                >Assign to Employee (optional)</label
                            >
                            <select
                                v-model="bulkAssignedUser"
                                :disabled="!bulkAssignee"
                                class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 disabled:bg-gray-100 disabled:text-gray-500"
                            >
                                <option value="">Select Employee</option>
                                <option
                                    v-for="user in users.filter(u => u.department_id === Number(bulkAssignee))"
                                    :key="user.id"
                                    :value="user.id"
                                >
                                    {{ user.name || `${user.first_name} ${user.last_name}` }}
                                </option>
                            </select>
                            <p v-if="!bulkAssignee" class="text-xs text-gray-500 mt-1">
                                Select a department first
                            </p>
                        </div>

                        <div>
                            <label
                                class="block text-sm font-medium text-gray-700 mb-1"
                                >Remarks (optional)</label
                            >
                            <textarea
                                v-model="bulkRemarks"
                                rows="3"
                                class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500"
                            ></textarea>
                        </div>

                        <div class="flex justify-end space-x-3 pt-4">
                            <button
                                type="button"
                                @click="showBulkAssignModal = false"
                                class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
                            >
                                Cancel
                            </button>
                            <button
                                type="submit"
                                :disabled="bulkLoading"
                                class="px-4 py-2 text-sm font-medium text-white bg-indigo-600 border border-transparent rounded-md hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 disabled:opacity-50 disabled:cursor-not-allowed"
                            >
                                <span v-if="bulkLoading">Sending...</span>
                                <span v-else>Send to Department</span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, reactive, computed, onMounted, watch, onUnmounted } from "vue";
import { useRoute, useRouter } from "vue-router";
import { storeToRefs } from "pinia";
import { useDocumentStore } from "../stores/documents";
import { useAuthStore } from "../stores/auth";
import axios from "axios";
import CreateDocumentModal from "../components/CreateDocumentModal.vue";
import ViewDocumentModal from "../components/ViewDocumentModal.vue";
import EditDocumentModal from "../components/EditDocumentModal.vue";

// Store
const documentStore = useDocumentStore();
const authStore = useAuthStore();
const { documents, pagination, loading } = storeToRefs(documentStore);

// Reactive data
const searchQuery = ref("");
const selectedDocuments = ref([]);
const showAdvancedFilters = ref(false);
const showExportDropdown = ref(false);
const exportDropdownRef = ref(null);
const showCreateModal = ref(false);
const showViewModal = ref(false);
const showEditModal = ref(false);
const selectedDocument = ref(null);
const editingDocument = ref(null);
const showBulkStatusModal = ref(false);
const showBulkAssignModal = ref(false);
const bulkLoading = ref(false);
const bulkStatus = ref("");
const bulkAssignee = ref("");
const bulkAssignedUser = ref("");
const bulkRemarks = ref("");
const users = ref([]);
const departments = ref([]);

// Filters
const filters = reactive({
    status: "",
    priority: "",
    department: "",
    type: "",
    dateFrom: "",
    dateTo: "",
});

// Route-based quick filters (from dashboard links)
const route = useRoute();
const router = useRouter();
const routeFilters = reactive({
    mine: false,
    assigned_to_me: false,
    overdue: false,
});

// Sorting
const sortField = ref("created_at");
const sortDirection = ref("desc");

// Computed properties
const visiblePages = computed(() => {
    if (!pagination || !pagination.current_page || !pagination.last_page) {
        return [1];
    }

    const current = pagination.current_page;
    const last = pagination.last_page;
    const pages = [];

    if (last <= 7) {
        for (let i = 1; i <= last; i++) {
            pages.push(i);
        }
    } else {
        if (current <= 4) {
            for (let i = 1; i <= 5; i++) {
                pages.push(i);
            }
            pages.push("...");
            pages.push(last);
        } else if (current >= last - 3) {
            pages.push(1);
            pages.push("...");
            for (let i = last - 4; i <= last; i++) {
                pages.push(i);
            }
        } else {
            pages.push(1);
            pages.push("...");
            for (let i = current - 1; i <= current + 1; i++) {
                pages.push(i);
            }
            pages.push("...");
            pages.push(last);
        }
    }

    return pages;
});

// Methods
const fetchDocuments = async () => {
    const params = {
        search: searchQuery.value,
        ...filters,
        sort_by: sortField.value,
        sort_order: sortDirection.value,
    };

    // Apply route quick filters
    if (routeFilters.mine && authStore.user?.id) {
        params.created_by = authStore.user.id;
    }
    if (routeFilters.assigned_to_me && authStore.user?.id) {
        params.assigned_to_me = 1;
    }
    if (routeFilters.overdue) {
        params.overdue = 1;
    }

    await documentStore.fetchDocuments(params);
};

// Custom debounce function
const debounce = (func, wait) => {
    let timeout;
    return function executedFunction(...args) {
        const later = () => {
            clearTimeout(timeout);
            func(...args);
        };
        clearTimeout(timeout);
        timeout = setTimeout(later, wait);
    };
};

// Debounced search
const debouncedFetchDocuments = debounce(fetchDocuments, 300);

const toggleAdvancedFilters = () => {
    showAdvancedFilters.value = !showAdvancedFilters.value;
};

const clearFilters = () => {
    Object.keys(filters).forEach((key) => {
        filters[key] = "";
    });
    searchQuery.value = "";
    routeFilters.mine = false;
    routeFilters.assigned_to_me = false;
    routeFilters.overdue = false;
    fetchDocuments();
};

const applyFilters = () => {
    fetchDocuments();
};

const sort = (field) => {
    if (sortField.value === field) {
        sortDirection.value = sortDirection.value === "asc" ? "desc" : "asc";
    } else {
        sortField.value = field;
        sortDirection.value = "asc";
    }
    fetchDocuments();
};

const getSortIcon = (field) => {
    if (sortField.value !== field) return "";
    return sortDirection.value === "asc" ? "↑" : "↓";
};

const toggleSelectAll = () => {
    if (selectedDocuments.value.length === documents.length) {
        selectedDocuments.value = [];
    } else {
        selectedDocuments.value = documents.map((doc) => doc.id);
    }
};

const getStatusClass = (status) => {
    const classes = {
        pending: "bg-yellow-100 text-yellow-800",
        approved: "bg-green-100 text-green-800",
        under_review: "bg-blue-100 text-blue-800",
        completed: "bg-gray-100 text-gray-800",
    };
    return classes[status] || "bg-gray-100 text-gray-800";
};

const getPriorityClass = (priority) => {
    const classes = {
        low: "bg-gray-100 text-gray-800",
        medium: "bg-blue-100 text-blue-800",
        high: "bg-orange-100 text-orange-800",
        urgent: "bg-red-100 text-red-800",
    };
    return classes[priority] || "bg-gray-100 text-gray-800";
};

const getDeadlineClass = (deadline) => {
    const now = new Date();
    const deadlineDate = new Date(deadline);
    const diffTime = deadlineDate - now;
    const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24));

    if (diffDays < 0) return "text-red-600 font-medium";
    if (diffDays <= 3) return "text-orange-600 font-medium";
    if (diffDays <= 7) return "text-yellow-600 font-medium";
    return "text-gray-600";
};

const formatDate = (date) => {
    return new Date(date).toLocaleDateString("en-US", {
        year: "numeric",
        month: "short",
        day: "numeric",
    });
};

const viewDocument = (document) => {
    selectedDocument.value = document;
    showViewModal.value = true;
};

const editDocument = (document) => {
    editingDocument.value = document;
    showEditModal.value = true;
};

const deleteDocument = async (document) => {
    if (confirm("Are you sure you want to delete this document?")) {
        try {
            await documentStore.deleteDocument(document.id);
            fetchDocuments();
            alert("Document deleted successfully!");
        } catch (error) {
            console.error("Error deleting document:", error);
            alert("Error deleting document. Please try again.");
        }
    }
};

const forwardDocument = async (document) => {
    const departmentId = prompt("Enter department ID to forward document to:");
    if (!departmentId) return;
    
    const remarks = prompt("Add forwarding remarks (optional):") || "";
    
    try {
        await axios.post(`/api/documents/${document.id}/route`, {
            to_office_id: parseInt(departmentId),
            remarks: remarks
        });
        
        fetchDocuments();
        alert("Document forwarded successfully!");
    } catch (error) {
        console.error("Error forwarding document:", error);
        alert("Error forwarding document. Please try again.");
    }
};

// Print QR for a specific document
const printQR = (doc) => {
    if (!doc?.qr_code_path) {
        alert("QR code not available for this document.");
        return;
    }
    const url = `/storage/${doc.qr_code_path}`;
    const w = window.open("", "_blank");
    if (!w) return;
    w.document.write(`<!DOCTYPE html><html><head><meta charset="utf-8"><title>QR</title><style>@page{margin:0}html,body{margin:0;padding:0}img{display:block;margin:0 auto;max-width:100vw;max-height:100vh}</style></head><body><img src="${url}" alt="QR" onload="window.print();window.onafterprint=window.close();"/></body></html>`);
    w.document.close();
};

// Event handlers for CreateDocumentModal
const onDocumentCreated = () => {
    showCreateModal.value = false;
    fetchDocuments();
    alert("Document created successfully!");
};

// Event handlers for EditDocumentModal
const onDocumentUpdated = () => {
    showEditModal.value = false;
    editingDocument.value = null;
    fetchDocuments();
    alert("Document updated successfully!");
};

const bulkUpdateStatus = async () => {
    if (!bulkStatus.value) return;

    bulkLoading.value = true;
    try {
        await documentStore.bulkUpdateStatus(
            selectedDocuments.value,
            bulkStatus.value
        );
        showBulkStatusModal.value = false;
        bulkStatus.value = "";
        selectedDocuments.value = [];
        fetchDocuments();
        alert("Documents status updated successfully!");
    } catch (error) {
        console.error("Error updating documents:", error);
        alert("Error updating documents. Please try again.");
    } finally {
        bulkLoading.value = false;
    }
};

const bulkAssignDocuments = async () => {
    if (!bulkAssignee.value) return;

    bulkLoading.value = true;
    try {
        await documentStore.bulkAssignDocuments(
            selectedDocuments.value,
            bulkAssignee.value
        );
        showBulkAssignModal.value = false;
        bulkAssignee.value = "";
        selectedDocuments.value = [];
        fetchDocuments();
        alert("Documents assigned successfully!");
    } catch (error) {
        console.error("Error assigning documents:", error);
        alert("Error assigning documents. Please try again.");
    } finally {
        bulkLoading.value = false;
    }
};

const bulkSendDocuments = async () => {
    if (!bulkAssignee.value) return;

    bulkLoading.value = true;
    try {
        const toOfficeId = parseInt(bulkAssignee.value);
        const assignedUserId = bulkAssignedUser.value
            ? parseInt(bulkAssignedUser.value)
            : null;
        const remarks = bulkRemarks.value || null;

        await documentStore.bulkForward(
            selectedDocuments.value,
            toOfficeId,
            remarks,
            assignedUserId
        );
        showBulkAssignModal.value = false;
        bulkAssignee.value = "";
        bulkAssignedUser.value = "";
        bulkRemarks.value = "";
        selectedDocuments.value = [];
        fetchDocuments();
        alert("Documents sent successfully!");
    } catch (error) {
        console.error("Error sending documents:", error);
        alert("Error sending documents. Please try again.");
    } finally {
        bulkLoading.value = false;
    }
};

const exportDocuments = async (format) => {
    try {
        showExportDropdown.value = false;
        await documentStore.exportDocuments(format, { ...filters, created_by: authStore.user?.id });
        alert("Export completed successfully!");
    } catch (error) {
        console.error("Export failed:", error);
        alert("Export failed. Please try again.");
    }
};

const exportSelectedDocuments = async (format) => {
    if (selectedDocuments.value.length === 0) {
        alert("Please select documents to export.");
        return;
    }

    try {
        showExportDropdown.value = false;
        await documentStore.exportSelectedDocuments(
            selectedDocuments.value,
            format
        );
        alert("Export completed successfully!");
    } catch (error) {
        console.error("Export failed:", error);
        alert("Export failed. Please try again.");
    }
};

// Access control functions
const canCreateDocument = () => {
    return (
        authStore.can("create documents") || authStore.hasRole("admin", "user")
    );
};

const canDeleteDocument = (document) => {
    // Admin can delete any document
    if (authStore.hasRole("admin")) return true;

    // Users can only delete documents they created
    return document.created_by === authStore.user?.id;
};

const canUpdateDocuments = () => {
    return authStore.can("update documents") || authStore.hasRole("admin");
};

const canExportDocuments = () => {
    return authStore.can("export documents") || authStore.hasRole("admin");
};

const canForwardDocument = (document) => {
    // Only allow forwarding of non-draft documents that user created or has access to
    if (document.status === 'draft') return false;
    
    if (authStore.hasRole('admin')) return true;
    
    // Users can forward documents they created or are in their department
    return document.created_by === authStore.user?.id || 
           document.current_department_id === authStore.user?.department_id;
};

const previousPage = () => {
    if (pagination && pagination.current_page > 1) {
        goToPage(pagination.current_page - 1);
    }
};

const nextPage = () => {
    if (pagination && pagination.current_page < pagination.last_page) {
        goToPage(pagination.current_page + 1);
    }
};

const goToPage = (page) => {
    if (pagination && page !== "..." && page !== pagination.current_page) {
        fetchDocuments({ ...filters, page });
    }
};

// Watchers
watch(searchQuery, debouncedFetchDocuments);

watch(
    [
        () => filters.status,
        () => filters.priority,
        () => filters.department,
        () => filters.type,
        () => filters.dateFrom,
        () => filters.dateTo,
    ],
    debouncedFetchDocuments,
    { deep: true }
);

// Initialize
const initFromRoute = () => {
    const q = route.query || {};
    // status quick filter
    if (typeof q.status === "string" && q.status.length) {
        filters.status = q.status;
    }
    // priority quick filter
    if (typeof q.priority === "string" && q.priority.length) {
        filters.priority = q.priority;
    }
    // booleans/toggles
    const truthy = (v) => v === 1 || v === "1" || v === true || v === "true";
    routeFilters.mine = truthy(q.mine);
    routeFilters.assigned_to_me = truthy(q.assigned_to_me);
    routeFilters.overdue = truthy(q.overdue);
};

onMounted(async () => {
    initFromRoute();
    fetchDocuments();

    // Load departments for assignment functionality
    try {
        departments.value = await documentStore.fetchDepartments();
        users.value = await documentStore.fetchUsers();
    } catch (error) {
        console.error("Error loading departments:", error);
    }

    // Add click-outside event listener
    document.addEventListener("click", handleClickOutside);
});

// React to route query changes (e.g., navigating via stat card links)
watch(
    () => route.query,
    () => {
        initFromRoute();
        fetchDocuments();
    }
);

// Cleanup
onUnmounted(() => {
    document.removeEventListener("click", handleClickOutside);
});

// Click outside handler
const handleClickOutside = (event) => {
    if (
        exportDropdownRef.value &&
        !exportDropdownRef.value.contains(event.target)
    ) {
        showExportDropdown.value = false;
    }
};
</script>

<style scoped lang="postcss">
.fade-enter-active,
.fade-leave-active {
    transition: opacity 0.3s ease;
}

.fade-enter-from,
.fade-leave-to {
    opacity: 0;
}

.slide-enter-active,
.slide-leave-active {
    transition: all 0.3s ease;
}

.slide-enter-from {
    transform: translateX(-100%);
}

.slide-leave-to {
    transform: translateX(100%);
}

.modal-enter-active,
.modal-leave-active {
    transition: opacity 0.3s ease;
}

.modal-enter-from,
.modal-leave-to {
    opacity: 0;
}

.modal-enter-active .modal-content,
.modal-leave-active .modal-content {
    transition: transform 0.3s ease;
}

.modal-enter-from .modal-content,
.modal-leave-to .modal-content {
    transform: scale(0.9);
}

.table-hover tbody tr:hover {
    background-color: #f8fafc;
}

.status-badge {
    @apply inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium;
}

.status-pending {
    @apply bg-yellow-100 text-yellow-800;
}

.status-in-progress {
    @apply bg-blue-100 text-blue-800;
}

.status-completed {
    @apply bg-green-100 text-green-800;
}

.status-cancelled {
    @apply bg-red-100 text-red-800;
}

.priority-badge {
    @apply inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium;
}

.priority-low {
    @apply bg-gray-100 text-gray-800;
}

.priority-medium {
    @apply bg-yellow-100 text-yellow-800;
}

.priority-high {
    @apply bg-red-100 text-red-800;
}

.priority-urgent {
    @apply bg-purple-100 text-purple-800;
}

.loading-spinner {
    animation: spin 1s linear infinite;
}

@keyframes spin {
    from {
        transform: rotate(0deg);
    }
    to {
        transform: rotate(360deg);
    }
}

.dropdown-enter-active,
.dropdown-leave-active {
    transition: all 0.2s ease;
}

.dropdown-enter-from,
.dropdown-leave-to {
    opacity: 0;
    transform: translateY(-10px);
}

.checkbox-indeterminate {
    background-image: url("data:image/svg+xml,%3csvg viewBox='0 0 16 16' fill='white' xmlns='http://www.w3.org/2000/svg'%3e%3cpath d='M4 8h8'/%3e%3c/svg%3e");
}

.pagination-ellipsis {
    @apply px-4 py-2 text-gray-500;
}

.export-button {
    @apply inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm bg-white text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500;
}

.bulk-actions {
    @apply flex items-center space-x-2;
}

.search-input {
    @apply block w-full pl-10 pr-3 py-2 border border-gray-300 rounded-md leading-5 bg-white placeholder-gray-500 focus:outline-none focus:placeholder-gray-400 focus:ring-1 focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm;
}

.filter-select {
    @apply block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm rounded-md;
}

.action-button {
    @apply inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500;
}

.secondary-button {
    @apply inline-flex items-center px-3 py-2 border border-gray-300 text-sm leading-4 font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500;
}

.danger-button {
    @apply inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500;
}
</style>
