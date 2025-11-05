<template>
    <div class="p-6 bg-gray-50 min-h-screen">
        <div class="max-w-7xl mx-auto">
            <!-- Header Section -->
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
                                    d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"
                                ></path>
                            </svg>
                            Users
                        </h1>
                        <p class="text-gray-600 mt-1">
                            Manage and organize your users efficiently
                        </p>
                    </div>
                    <button
                        @click="openCreateModal"
                        v-if="canCreateUsers()"
                        class="inline-flex items-center px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white font-medium rounded-lg shadow-sm transition-colors duration-150"
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
                                d="M12 4v16m8-8H4"
                            ></path>
                        </svg>
                        New User
                    </button>
                </div>
            </div>

            <!-- Search and Filter Section -->
            <div class="bg-white rounded-lg shadow-sm p-4 mb-6">
                <div class="flex flex-col sm:flex-row gap-4">
                    <div class="flex-1">
                        <div class="relative">
                            <svg
                                class="absolute left-3 top-1/2 transform -translate-y-1/2 w-4 h-4 text-gray-400"
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
                            <input
                                v-model="searchQuery"
                                @input="searchUsers"
                                type="text"
                                placeholder="Search users..."
                                class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                            />
                        </div>
                    </div>
                    <div class="flex gap-2">
                        <select
                            v-model="selectedDepartmentId"
                            @change="searchUsers"
                            class="px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                        >
                            <option value="">All Departments</option>
                            <option
                                v-for="d in departments"
                                :key="d.id"
                                :value="d.id"
                            >
                                {{ d.name }}
                            </option>
                        </select>
                        <button
                            @click="clearFilters"
                            class="px-3 py-2 text-gray-600 hover:text-gray-800 border border-gray-300 rounded-lg hover:bg-gray-50"
                        >
                            Clear
                        </button>
                    </div>
                </div>
            </div>

            <!-- Users Table -->
            <div class="bg-white rounded-lg shadow-sm overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-medium text-gray-900">
                        Users List
                        <span class="text-sm font-normal text-gray-500 ml-2">
                            ({{ filteredUsers.length }}
                            {{ filteredUsers.length === 1 ? "user" : "users" }})
                        </span>
                    </h3>
                </div>

                <div v-if="loading" class="p-8 text-center">
                    <div class="inline-flex items-center gap-2 text-gray-600">
                        <svg
                            class="animate-spin w-5 h-5"
                            fill="none"
                            stroke="currentColor"
                            viewBox="0 0 24 24"
                        >
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="2"
                                d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"
                            ></path>
                        </svg>
                        Loading users...
                    </div>
                </div>

                <div
                    v-else-if="filteredUsers.length === 0"
                    class="p-8 text-center"
                >
                    <svg
                        class="mx-auto w-12 h-12 text-gray-400 mb-4"
                        fill="none"
                        stroke="currentColor"
                        viewBox="0 0 24 24"
                    >
                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="2"
                            d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"
                        ></path>
                    </svg>
                    <h3 class="text-lg font-medium text-gray-900 mb-2">
                        No users found
                    </h3>
                    <p class="text-gray-600 mb-4">
                        {{
                            searchQuery || selectedDepartmentId
                                ? "Try adjusting your search or filter criteria."
                                : "Get started by creating your first user."
                        }}
                    </p>
                    <button
                        v-if="!searchQuery && !selectedDepartmentId"
                        @click="openCreateModal"
                        class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-lg"
                    >
                        Create User
                    </button>
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
                                    Email
                                </th>
                                <th
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
                                >
                                    Department
                                </th>
                                <th
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
                                >
                                    Roles
                                </th>
                                <th
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
                                >
                                    Status
                                </th>
                                <th
                                    class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider"
                                >
                                    Actions
                                </th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            <tr
                                v-for="user in filteredUsers"
                                :key="user.id"
                                class="hover:bg-gray-50 transition-colors duration-200"
                            >
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div
                                        class="text-sm font-medium text-gray-900"
                                    >
                                        {{ user.name }}
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-600">
                                        {{ user.email }}
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-600">
                                        {{ user.department?.name || "-" }}
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex flex-wrap gap-1">
                                        <span
                                            v-for="role in user.roles"
                                            :key="role.id"
                                            class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800"
                                        >
                                            {{ role.name }}
                                        </span>
                                        <span
                                            v-if="!user.roles?.length"
                                            class="text-sm text-gray-400 italic"
                                        >
                                            No roles
                                        </span>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex flex-wrap gap-1">
                                        <span
                                            v-if="user.is_active === true || user.is_active === 1"
                                            class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800"
                                        >
                                            Active
                                        </span>
                                        <span
                                            v-else
                                            class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-800"
                                        >
                                            Inactive
                                        </span>
                                        <span
                                            v-if="user.can_sign_digitally === true || user.can_sign_digitally === 1"
                                            class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-indigo-100 text-indigo-800"
                                        >
                                            Can Sign
                                        </span>
                                        <span
                                            v-else
                                            class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-800"
                                        >
                                            No Sign
                                        </span>
                                    </div>
                                </td>
                                <td
                                    class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium"
                                >
                                    <div
                                        class="flex items-center justify-end space-x-2"
                                    >
                                        <button
                                            @click="editUser(user)"
                                            v-if="canEditUsers()"
                                            class="text-indigo-600 hover:text-indigo-900"
                                            title="Edit User"
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
                                            @click="openPasswordModal(user)"
                                            v-if="canEditUsers()"
                                            class="text-amber-600 hover:text-amber-900"
                                            title="Change Password"
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
                                                    d="M12 11c0-1.656-1.344-3-3-3s-3 1.344-3 3v2h10v-2c0-1.656-1.344-3-3-3s-3 1.344-3 3m-6 2v6h12v-6H6z"
                                                ></path>
                                            </svg>
                                        </button>
                                        <button
                                            @click="manageRoles(user)"
                                            v-if="canManageRoles()"
                                            class="text-blue-600 hover:text-blue-900"
                                            title="Manage Roles"
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
                                                    d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"
                                                ></path>
                                            </svg>
                                        </button>
                                        <button
                                            @click="deleteUser(user)"
                                            v-if="canDeleteUsers()"
                                            class="text-red-600 hover:text-red-900"
                                            title="Delete User"
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
            <!-- Create User Modal -->
            <div
                v-if="showCreateModal"
                class="fixed inset-0 bg-black bg-opacity-50 flex items-start sm:items-center justify-center p-4 z-50 overflow-y-auto"
            >
                <div class="bg-white rounded-lg shadow-lg w-full max-w-md max-h-[85vh] overflow-y-auto"
                    aria-modal="true" role="dialog">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h3 class="text-lg font-medium text-gray-900">
                            Create New User
                        </h3>
                    </div>

                    <form @submit.prevent="createUser" class="p-6 space-y-4">
                        <div>
                            <label
                                class="block text-sm font-medium text-gray-700 mb-1"
                                >Name</label
                            >
                            <input
                                v-model="createForm.name"
                                type="text"
                                required
                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-indigo-500 focus:border-indigo-500"
                                placeholder="Enter full name"
                            />
                        </div>
                        <div>
                            <label
                                class="block text-sm font-medium text-gray-700 mb-1"
                                >Email</label
                            >
                            <input
                                v-model="createForm.email"
                                type="email"
                                required
                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-indigo-500 focus:border-indigo-500"
                                placeholder="Enter email address"
                            />
                        </div>
                        <div>
                            <label
                                class="block text-sm font-medium text-gray-700 mb-1"
                                >Password</label
                            >
                            <input
                                v-model="createForm.password"
                                type="password"
                                required
                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-indigo-500 focus:border-indigo-500"
                                placeholder="Enter password"
                            />
                        </div>
                        <div>
                            <label
                                class="block text-sm font-medium text-gray-700 mb-1"
                                >Confirm Password</label
                            >
                            <input
                                v-model="createForm.password_confirmation"
                                type="password"
                                required
                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-indigo-500 focus:border-indigo-500"
                                placeholder="Confirm password"
                            />
                        </div>
                        <div>
                            <label
                                class="block text-sm font-medium text-gray-700 mb-1"
                                >Role</label
                            >
                            <select
                                v-model="createForm.role"
                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-indigo-500 focus:border-indigo-500"
                            >
                                <option value="">Select Role</option>
                                <option
                                    v-for="role in availableRoles"
                                    :key="role.id"
                                    :value="role.name"
                                >
                                    {{ role.name }}
                                </option>
                            </select>
                        </div>
                        <div>
                            <label
                                class="block text-sm font-medium text-gray-700 mb-1"
                                >Office</label
                            >
                            <select
                                v-model="createForm.department_id"
                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-indigo-500 focus:border-indigo-500"
                            >
                                <option value="">Select Office</option>
                                <option
                                    v-for="d in departments"
                                    :key="d.id"
                                    :value="d.id"
                                >
                                    {{ d.name }}
                                </option>
                            </select>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Position</label>
                            <input
                                v-model="createForm.position"
                                type="text"
                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-indigo-500 focus:border-indigo-500"
                                placeholder="Enter position"
                            />
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">PNPKI Certificate Serial</label>
                            <input
                                v-model="createForm.pnpki_certificate_serial"
                                type="text"
                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-indigo-500 focus:border-indigo-500"
                                placeholder="Enter PNPKI certificate serial"
                            />
                        </div>

                        <div class="flex items-center gap-4">
                            <label class="inline-flex items-center">
                                <input
                                    v-model="createForm.can_sign_digitally"
                                    type="checkbox"
                                    class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500"
                                />
                                <span class="ml-2 text-sm text-gray-700">Can Sign Digitally</span>
                            </label>
                            <label class="inline-flex items-center">
                                <input
                                    v-model="createForm.is_active"
                                    type="checkbox"
                                    class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500"
                                />
                                <span class="ml-2 text-sm text-gray-700">Active</span>
                            </label>
                        </div>

                        <div
                            class="flex justify-end space-x-3 pt-4 border-t border-gray-200 sticky bottom-0 bg-white"
                        >
                            <button
                                type="button"
                                @click="closeCreateModal"
                                class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 rounded-md hover:bg-gray-200"
                            >
                                Cancel
                            </button>
                            <button
                                type="submit"
                                :disabled="createLoading"
                                class="px-4 py-2 text-sm font-medium text-white bg-indigo-600 rounded-md hover:bg-indigo-700 disabled:opacity-50"
                            >
                                <span v-if="createLoading">Creating...</span>
                                <span v-else>Create User</span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Edit User Modal -->
            <div
                v-if="showEditModal"
                class="fixed inset-0 bg-black bg-opacity-50 flex items-start sm:items-center justify-center p-4 z-50 overflow-y-auto"
            >
                <div class="bg-white rounded-lg shadow-lg w-full max-w-md max-h-[85vh] overflow-y-auto" aria-modal="true" role="dialog">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h3 class="text-lg font-medium text-gray-900">
                            Edit User
                        </h3>
                    </div>
                    <form @submit.prevent="updateUser" class="p-6 space-y-4">
                        <div>
                            <label
                                class="block text-sm font-medium text-gray-700 mb-1"
                                >Name</label
                            >
                            <input
                                v-model="editForm.name"
                                type="text"
                                required
                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-indigo-500 focus:border-indigo-500"
                                placeholder="Enter full name"
                            />
                        </div>
                        <div>
                            <label
                                class="block text-sm font-medium text-gray-700 mb-1"
                                >Email</label
                            >
                            <input
                                v-model="editForm.email"
                                type="email"
                                required
                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-indigo-500 focus:border-indigo-500"
                                placeholder="Enter email address"
                            />
                        </div>
                        <div>
                            <label
                                class="block text-sm font-medium text-gray-700 mb-1"
                                >Office</label
                            >
                            <select
                                v-model="editForm.department_id"
                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-indigo-500 focus:border-indigo-500"
                            >
                                <option value="">Select Office</option>
                                <option
                                    v-for="d in departments"
                                    :key="d.id"
                                    :value="d.id"
                                >
                                    {{ d.name }}
                                </option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Position</label>
                            <input
                                v-model="editForm.position"
                                type="text"
                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-indigo-500 focus:border-indigo-500"
                                placeholder="Enter position"
                            />
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">PNPKI Certificate Serial</label>
                            <input
                                v-model="editForm.pnpki_certificate_serial"
                                type="text"
                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-indigo-500 focus:border-indigo-500"
                                placeholder="Enter PNPKI certificate serial"
                            />
                        </div>
                        <div class="flex items-center gap-4">
                            <label class="inline-flex items-center">
                                <input
                                    v-model="editForm.can_sign_digitally"
                                    type="checkbox"
                                    class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500"
                                />
                                <span class="ml-2 text-sm text-gray-700">Can Sign Digitally</span>
                            </label>
                            <label class="inline-flex items-center">
                                <input
                                    v-model="editForm.is_active"
                                    type="checkbox"
                                    class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500"
                                />
                                <span class="ml-2 text-sm text-gray-700">Active</span>
                            </label>
                        </div>

                        <div
                            class="flex justify-end space-x-3 pt-4 border-t border-gray-200 sticky bottom-0 bg-white"
                        >
                            <button
                                type="button"
                                @click="closeEditModal"
                                class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 rounded-md hover:bg-gray-200"
                            >
                                Cancel
                            </button>
                            <button
                                type="submit"
                                :disabled="editLoading"
                                class="px-4 py-2 text-sm font-medium text-white bg-indigo-600 rounded-md hover:bg-indigo-700 disabled:opacity-50"
                            >
                                <span v-if="editLoading">Updating...</span>
                                <span v-else>Update User</span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Role Management Modal -->
            <div
                v-if="showRoleModal"
                class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center p-4 z-50"
            >
                <div class="bg-white rounded-lg shadow-lg w-full max-w-md">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h3 class="text-lg font-medium text-gray-900">
                            Manage User Roles
                        </h3>
                    </div>
                    <form @submit.prevent="addUserRole" class="p-6 space-y-4">
                        <div>
                            <label
                                class="block text-sm font-medium text-gray-700 mb-2"
                                >Current Roles</label
                            >
                            <div
                                class="flex flex-wrap gap-2 min-h-[2rem] p-3 bg-gray-50 rounded-md border border-gray-200"
                            >
                                <span
                                    v-for="role in selectedUser?.roles"
                                    :key="role.id"
                                    class="inline-flex items-center px-2 py-1 text-xs font-medium bg-blue-100 text-blue-800 rounded-md"
                                >
                                    {{ role.name }}
                                    <button
                                        type="button"
                                        @click="removeUserRole(role.name)"
                                        class="ml-1 text-blue-600 hover:text-blue-800"
                                    >
                                        <svg
                                            class="w-3 h-3"
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
                                </span>
                                <span
                                    v-if="!selectedUser?.roles?.length"
                                    class="text-sm text-gray-400 italic"
                                >
                                    No roles assigned
                                </span>
                            </div>
                        </div>

                        <div>
                            <label
                                class="block text-sm font-medium text-gray-700 mb-1"
                                >Add Role</label
                            >
                            <select
                                v-model="selectedRole"
                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-indigo-500 focus:border-indigo-500"
                            >
                                <option value="">Select Role</option>
                                <option
                                    v-for="role in availableRolesToAdd"
                                    :key="role.id"
                                    :value="role.name"
                                >
                                    {{ role.name }}
                                </option>
                            </select>
                        </div>

                        <div
                            class="flex justify-end space-x-3 pt-4 border-t border-gray-200"
                        >
                            <button
                                type="button"
                                @click="closeRoleModal"
                                class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 rounded-md hover:bg-gray-200"
                            >
                                Close
                            </button>
                            <button
                                type="submit"
                                :disabled="!selectedRole || roleLoading"
                                class="px-4 py-2 text-sm font-medium text-white bg-indigo-600 rounded-md hover:bg-indigo-700 disabled:opacity-50"
                            >
                                <span v-if="roleLoading">Adding...</span>
                                <span v-else>Add Role</span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Delete Confirmation Modal -->
            <div
                v-if="showDeleteModal"
                class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center p-4 z-50"
            >
                <div class="bg-white rounded-lg shadow-lg w-full max-w-md">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h3 class="text-lg font-medium text-gray-900">
                            Delete User
                        </h3>
                    </div>
                    <div class="p-6">
                        <p class="text-gray-600 mb-4">
                            Are you sure you want to delete this user? This
                            action cannot be undone.
                        </p>
                        <div class="flex justify-end space-x-3">
                            <button
                                @click="showDeleteModal = false"
                                class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 rounded-md hover:bg-gray-200"
                            >
                                Cancel
                            </button>
                            <button
                                @click="deleteUser"
                                class="px-4 py-2 text-sm font-medium text-white bg-red-600 rounded-md hover:bg-red-700"
                            >
                                Delete
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Change Password Modal -->
            <div
                v-if="showPasswordModal"
                class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center p-4 z-50"
            >
                <div class="bg-white rounded-lg shadow-lg w-full max-w-md">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h3 class="text-lg font-medium text-gray-900">
                            Change User Password
                        </h3>
                        <p class="text-sm text-gray-500">This will set a new password for the selected user.</p>
                    </div>
                    <form @submit.prevent="changeUserPassword" class="p-6 space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">New Password</label>
                            <input
                                v-model="passwordForm.password"
                                type="password"
                                required
                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-indigo-500 focus:border-indigo-500"
                                placeholder="Enter new password"
                            />
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Confirm New Password</label>
                            <input
                                v-model="passwordForm.password_confirmation"
                                type="password"
                                required
                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-indigo-500 focus:border-indigo-500"
                                placeholder="Confirm new password"
                            />
                        </div>

                        <div class="flex justify-end space-x-3 pt-4 border-t border-gray-200">
                            <button
                                type="button"
                                @click="closePasswordModal"
                                class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 rounded-md hover:bg-gray-200"
                            >
                                Cancel
                            </button>
                            <button
                                type="submit"
                                :disabled="passwordLoading"
                                class="px-4 py-2 text-sm font-medium text-white bg-amber-600 rounded-md hover:bg-amber-700 disabled:opacity-50"
                            >
                                <span v-if="passwordLoading">Updating...</span>
                                <span v-else>Update Password</span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, onMounted, computed } from "vue";
import { useToast } from "vue-toastification";
import { useDepartmentsStore } from "../stores/departments";
import { useAuthStore } from "../stores/auth";
import axios from "axios";

const toast = useToast();
const departmentsStore = useDepartmentsStore();
const authStore = useAuthStore();

// State
const users = ref([]);
const searchQuery = ref("");
const selectedDepartment = ref("");
const selectedDepartmentId = ref("");
const availableRoles = ref([]);

// Modal states
const showCreateModal = ref(false);
const showEditModal = ref(false);
const showRoleModal = ref(false);
const showDeleteModal = ref(false);
const showPasswordModal = ref(false);

// Loading states
const loading = ref(false);

// Form data
const createForm = ref({
    name: "",
    email: "",
    password: "",
    password_confirmation: "",
    role: "",
    department_id: "",
    position: "",
    pnpki_certificate_serial: "",
    can_sign_digitally: false,
    is_active: true,
});

const editForm = ref({
    id: null,
    name: "",
    email: "",
    department_id: "",
    position: "",
    pnpki_certificate_serial: "",
    can_sign_digitally: false,
    is_active: true,
    password: "",
    password_confirmation: "",
});

const selectedUser = ref(null);
const selectedRole = ref("");
const userToDelete = ref(null);
const passwordForm = ref({ id: null, password: '', password_confirmation: '' });
const passwordLoading = ref(false);

// Computed properties
const departments = computed(() => departmentsStore.items);

const filteredUsers = computed(() => {
    let filtered = users.value;

    if (searchQuery.value) {
        const query = searchQuery.value.toLowerCase();
        filtered = filtered.filter(
            (user) =>
                user.name.toLowerCase().includes(query) ||
                user.email.toLowerCase().includes(query)
        );
    }

    if (selectedDepartment.value) {
        filtered = filtered.filter(
            (user) => user.department_id == selectedDepartment.value
        );
    }

    return filtered;
});

const availableRolesToAdd = computed(() => {
    if (!selectedUser.value || !availableRoles.value) return [];
    const userRoleNames =
        selectedUser.value.roles?.map((role) => role.name) || [];
    return availableRoles.value.filter(
        (role) => !userRoleNames.includes(role.name)
    );
});

// Functions
const fetchUsers = async () => {
    try {
        loading.value = true;
        const response = await axios.get("/api/users");
        users.value = response.data.data || response.data;
    } catch (error) {
        console.error("Error fetching users:", error);
        toast.error("Failed to fetch users");
    } finally {
        loading.value = false;
    }
};

const fetchRoles = async () => {
    try {
        const response = await axios.get("/api/roles");
        availableRoles.value = response.data.data || response.data;
    } catch (error) {
        console.error("Error fetching roles:", error);
        toast.error("Failed to fetch roles");
    }
};

// Modal functions
const openCreateModal = () => {
    resetCreateForm();
    showCreateModal.value = true;
};

const closeCreateModal = () => {
    showCreateModal.value = false;
    resetCreateForm();
};

const openEditModal = (user) => {
    editForm.value = {
        id: user.id,
        name: user.name,
        email: user.email,
        department_id: user.department_id,
        position: user.position || "",
        pnpki_certificate_serial: user.pnpki_certificate_serial || "",
        can_sign_digitally: !!user.can_sign_digitally,
        is_active: user.is_active !== undefined ? !!user.is_active : true,
    };
    showEditModal.value = true;
};

const editUser = (user) => {
    openEditModal(user);
};

const closeEditModal = () => {
    showEditModal.value = false;
    resetEditForm();
};

const openRoleModal = (user) => {
    selectedUser.value = user;
    selectedRole.value = "";
    showRoleModal.value = true;
};

const closeRoleModal = () => {
    showRoleModal.value = false;
    selectedUser.value = null;
    selectedRole.value = "";
};

const confirmDelete = (user) => {
    userToDelete.value = user;
    showDeleteModal.value = true;
};

// Password modal functions
const openPasswordModal = (user) => {
    passwordForm.value = { id: user.id, password: '', password_confirmation: '' };
    showPasswordModal.value = true;
};

const closePasswordModal = () => {
    showPasswordModal.value = false;
    passwordForm.value = { id: null, password: '', password_confirmation: '' };
};

// Form functions
const resetCreateForm = () => {
    createForm.value = {
        name: "",
        email: "",
        password: "",
        password_confirmation: "",
        role: "",
        department_id: "",
        position: "",
        pnpki_certificate_serial: "",
        can_sign_digitally: false,
        is_active: true,
    };
};

const resetEditForm = () => {
    editForm.value = {
        id: null,
        name: "",
        email: "",
        department_id: "",
        position: "",
        pnpki_certificate_serial: "",
        can_sign_digitally: false,
        is_active: true,
    };
};

const createUser = async () => {
    try {
        const response = await axios.post("/api/users", createForm.value);
        toast.success("User created successfully");
        closeCreateModal();
        await fetchUsers();
    } catch (error) {
        console.error("Error creating user:", error);
        if (error.response?.data?.errors) {
            const errors = error.response.data.errors;
            Object.keys(errors).forEach((key) => {
                errors[key].forEach((message) => {
                    toast.error(message);
                });
            });
        } else {
            toast.error("Failed to create user");
        }
    }
};

const updateUser = async () => {
    try {
        const payload = {
            name: editForm.value.name,
            email: editForm.value.email,
            department_id: editForm.value.department_id,
            position: editForm.value.position,
            pnpki_certificate_serial: editForm.value.pnpki_certificate_serial,
            can_sign_digitally: editForm.value.can_sign_digitally,
            is_active: editForm.value.is_active,
        };
        await axios.put(`/api/users/${editForm.value.id}`, payload);
        toast.success("User updated successfully");
        closeEditModal();
        await fetchUsers();
    } catch (error) {
        console.error("Error updating user:", error);
        if (error.response?.data?.errors) {
            const errors = error.response.data.errors;
            Object.keys(errors).forEach((key) => {
                errors[key].forEach((message) => {
                    toast.error(message);
                });
            });
        } else {
            toast.error("Failed to update user");
        }
    }
};

const changeUserPassword = async () => {
    if (passwordForm.value.password !== passwordForm.value.password_confirmation) {
        toast.error('Passwords do not match');
        return;
    }

    try {
        passwordLoading.value = true;
        await axios.put(`/api/users/${passwordForm.value.id}/password`, {
            password: passwordForm.value.password,
            password_confirmation: passwordForm.value.password_confirmation,
        });
        toast.success('Password updated successfully');
        closePasswordModal();
        await fetchUsers();
    } catch (error) {
        console.error('Error updating password:', error);
        const message = error.response?.data?.message || 'Failed to update password';
        if (error.response?.data?.errors) {
            const errors = error.response.data.errors;
            Object.values(errors).flat().forEach((m) => toast.error(m));
        } else {
            toast.error(message);
        }
    } finally {
        passwordLoading.value = false;
    }
};

const deleteUser = async () => {
    if (!userToDelete.value) return;

    try {
        await axios.delete(`/api/users/${userToDelete.value.id}`);
        users.value = users.value.filter((u) => u.id !== userToDelete.value.id);
        toast.success("User deleted successfully");
        showDeleteModal.value = false;
        userToDelete.value = null;
    } catch (error) {
        console.error("Error deleting user:", error);
        toast.error("Failed to delete user");
    }
};

// Role management functions
const addUserRole = async () => {
    if (!selectedRole.value || !selectedUser.value) return;

    try {
        await axios.post(`/api/users/${selectedUser.value.id}/roles`, {
            role: selectedRole.value,
        });

        const response = await axios.get(`/api/users/${selectedUser.value.id}`);
        const updatedUser = response.data.data || response.data;

        const index = users.value.findIndex(
            (u) => u.id === selectedUser.value.id
        );
        if (index !== -1) {
            users.value[index] = updatedUser;
        }

        selectedUser.value = updatedUser;
        selectedRole.value = "";

        toast.success("Role added successfully");
    } catch (error) {
        console.error("Error adding role:", error);
        toast.error("Failed to add role");
    }
};

const removeUserRole = async (roleName) => {
    if (!selectedUser.value) return;

    try {
        await axios.delete(
            `/api/users/${selectedUser.value.id}/roles/${roleName}`
        );

        const response = await axios.get(`/api/users/${selectedUser.value.id}`);
        const updatedUser = response.data.data || response.data;

        const index = users.value.findIndex(
            (u) => u.id === selectedUser.value.id
        );
        if (index !== -1) {
            users.value[index] = updatedUser;
        }

        selectedUser.value = updatedUser;

        toast.success("Role removed successfully");
    } catch (error) {
        console.error("Error removing role:", error);
        toast.error("Failed to remove role");
    }
};

// Access control functions
const canCreateUsers = () => {
    return authStore.can('create users') || authStore.hasRole('admin');
};

const canEditUsers = () => {
    return authStore.can('edit users') || authStore.hasRole('admin');
};

const canDeleteUsers = () => {
    return authStore.can('delete users') || authStore.hasRole('admin');
};

const canManageRoles = () => {
    return authStore.can('manage roles') || authStore.hasRole('admin');
};

const formatDate = (date) => {
    return new Date(date).toLocaleDateString();
};

// Initialize
onMounted(async () => {
    await Promise.all([departmentsStore.fetch(), fetchUsers(), fetchRoles()]);
});
</script>
