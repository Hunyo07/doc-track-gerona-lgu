<template>
    <nav class="bg-sky-700 shadow-lg text-white">
        <div class="container mx-auto px-4">
            <div class="flex justify-between items-center py-3">
                <div class="flex items-center space-x-3">
                    <img
                        :src="logoSrc"
                        alt="Logo"
                        class="h-[4rem] rounded-ful"
                    />
                    <div>
                        <p
                            class="text-xs uppercase tracking-wider text-gray-300"
                        >
                            Republic of the Philippines
                        </p>
                        <h1 class="text-lg font-semibold leading-tight">
                            LGU Gerona Tarlac
                        </h1>
                        <p class="text-xs text-gray-300 -mt-1">
                            Serbisyong Totoo
                        </p>
                    </div>
                </div>

                <!-- Right-side actions -->
                <div class="flex items-center space-x-2">
                    <!-- Notifications dropdown -->
                    <div class="relative" ref="notifDropdown">
                        <button
                            class="relative inline-flex items-center justify-center h-10 w-10 rounded hover:bg-sky-800"
                            @click="toggleNotifications"
                            aria-label="Notifications"
                        >
                            <BellIcon class="h-6 w-6" />
                            <span
                                v-if="notificationsStore.unread > 0"
                                class="absolute -top-1 -right-1 bg-red-500 text-white text-xs rounded-full px-1"
                                >{{ notificationsStore.unread }}</span
                            >
                        </button>
                        <div
                            v-if="showNotifications"
                            class="absolute right-0 z-50 mt-2 w-80 bg-white text-gray-900 rounded-md shadow-lg border border-gray-200"
                        >
                            <div class="flex items-center justify-between px-3 py-2">
                                <span class="text-sm font-medium">Notifications</span>
                                <button
                                    @click="markAll"
                                    class="text-xs text-indigo-600 hover:text-indigo-500"
                                >
                                    Mark all
                                </button>
                            </div>
                            <ul class="max-h-96 overflow-y-auto divide-y divide-gray-100">
                                <li
                                    v-for="n in (notificationsStore.items || []).slice(0, 10)"
                                    :key="n.id"
                                    class="px-3 py-2 flex items-start justify-between gap-3"
                                    :class="!n.read_at ? 'bg-gray-50' : ''"
                                >
                                    <div class="min-w-0">
                                        <p class="text-sm font-medium truncate">
                                            {{ (n.data && n.data.title) || 'Notification' }}
                                        </p>
                                        <p class="text-xs text-gray-600 truncate">
                                            {{ (n.data && n.data.message) || '' }}
                                        </p>
                                        <p class="text-xs text-gray-400 mt-0.5">{{ formatDate(n.created_at) }}</p>
                                    </div>
                                    <div class="flex flex-col items-end gap-1">
                                        <button
                                            v-if="!n.read_at"
                                            @click.stop="mark(n.id)"
                                            class="text-xs text-indigo-600 hover:text-indigo-500"
                                        >
                                            Read
                                        </button>
                                        <button
                                            @click.stop="remove(n.id)"
                                            class="text-xs text-red-600 hover:text-red-500"
                                        >
                                            Delete
                                        </button>
                                    </div>
                                </li>
                                <li
                                    v-if="(notificationsStore.items || []).length === 0"
                                    class="px-3 py-2 text-sm text-gray-500"
                                >
                                    No notifications
                                </li>
                            </ul>
                        </div>
                    </div>

                    <!-- Mobile sidebar toggle -->
                    <button
                        class="md:hidden inline-flex items-center justify-center h-10 w-10 rounded hover:bg-sky-800"
                        @click="uiStore.toggleSidebarOpen()"
                        aria-label="Toggle sidebar"
                    >
                        <svg
                            class="h-6 w-6"
                            fill="none"
                            stroke="currentColor"
                            viewBox="0 0 24 24"
                        >
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="2"
                                d="M4 6h16M4 12h16M4 18h16"
                            />
                        </svg>
                    </button>
                </div>

                <!-- <div class="flex items-center space-x-2">
                    <router-link
                        to="/"
                        class="text-gray-200 hover:text-white px-3 py-2 rounded-md"
                        :class="{ 'bg-gray-800': $route.name === 'dashboard' }"
                    >
                        Dashboard
                    </router-link>
                    <router-link
                        to="/documents"
                        class="text-gray-200 hover:text-white px-3 py-2 rounded-md"
                        :class="{ 'bg-gray-800': $route.name === 'documents' }"
                    >
                        Documents
                    </router-link>
                    <router-link
                        to="/users"
                        class="text-gray-200 hover:text-white px-3 py-2 rounded-md"
                        :class="{ 'bg-gray-800': $route.name === 'users' }"
                    >
                        Users
                    </router-link>

                    <div class="relative">
                        <button
                            @click="showUserMenu = !showUserMenu"
                            class="flex items-center text-gray-200 hover:text-white"
                        >
                            <span class="mr-2">{{ authStore.user?.name }}</span>
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
                                    d="M19 9l-7 7-7-7"
                                ></path>
                            </svg>
                        </button>

                        <div
                            v-if="showUserMenu"
                            class="absolute right-0 mt-2 w-48 bg-white text-gray-800 rounded-md shadow-lg py-1 z-50"
                        >
                            <router-link
                                to="/profile"
                                class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100"
                            >
                                Profile
                            </router-link>
                            <button
                                @click="handleLogout"
                                class="block w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100"
                            >
                                Logout
                            </button>
                        </div>
                    </div>
                </div> -->
            </div>
        </div>
    </nav>
</template>

<script setup>
import { ref, onMounted, onUnmounted } from "vue";
import { useRouter } from "vue-router";
import { useAuthStore } from "../stores/auth";
import { useUiStore } from "../stores/ui";
import { useNotificationsStore } from "../stores/notifications";
import { BellIcon } from "@heroicons/vue/24/outline";
import axios from "axios";

const router = useRouter();
const authStore = useAuthStore();
const uiStore = useUiStore();
const notificationsStore = useNotificationsStore();
const showUserMenu = ref(false);
const logoSrc = "/geronalogo.ico";
const showNotifications = ref(false);
const notifDropdown = ref(null);

const handleLogout = async () => {
    await authStore.logout();
    router.push("/login");
};

const toggleNotifications = async () => {
    showNotifications.value = !showNotifications.value;
    if (showNotifications.value) {
        try {
            await notificationsStore.fetch(1, 10);
        } catch (e) {
            console.error("Failed to fetch notifications", e);
        }
    }
};

const mark = async (id) => {
    await notificationsStore.markAsRead(id);
};

const markAll = async () => {
    await notificationsStore.markAllAsRead();
};

const remove = async (id) => {
    try {
        await axios.delete(`/api/notifications/${id}`);
        notificationsStore.items = notificationsStore.items.filter((n) => n.id !== id);
        await notificationsStore.fetchUnreadCount();
    } catch (e) {
        console.error("Failed to delete notification", e);
    }
};

const formatDate = (str) => {
    try {
        const d = new Date(str);
        return d.toLocaleString();
    } catch {
        return str;
    }
};

const handleDocClick = (e) => {
    if (!showNotifications.value) return;
    const el = notifDropdown.value;
    if (el && !el.contains(e.target)) showNotifications.value = false;
};

const handleKeydown = (e) => {
    if (e.key === "Escape") showNotifications.value = false;
};

onMounted(() => {
    notificationsStore.fetchUnreadCount();
    document.addEventListener("click", handleDocClick);
    window.addEventListener("keydown", handleKeydown);
});

onUnmounted(() => {
    document.removeEventListener("click", handleDocClick);
    window.removeEventListener("keydown", handleKeydown);
});
</script>
