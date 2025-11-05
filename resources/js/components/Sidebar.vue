<template>
    <aside
        class="bg-gray-900 text-gray-200 fixed md:static inset-y-0 left-0 z-40 flex flex-col transition-all duration-200"
        :class="[
            uiStore.isSidebarCollapsed ? 'w-16' : 'w-64',
            uiStore.isSidebarOpen
                ? 'translate-x-0'
                : '-translate-x-full md:translate-x-0',
        ]"
    >
        <!-- Header / Collapse Button (desktop) -->
        <div
            class="hidden md:flex items-center justify-between h-14 px-3 border-b border-gray-800"
        >
            <div class="flex items-center justify-between w-full gap-3">
                <div class="flex items-center gap-3">
                    <div
                        class="h-9 w-9 rounded-full bg-gray-700 flex items-center justify-center text-sm font-semibold"
                    >
                        {{ initials }}
                    </div>
                    <div v-if="!uiStore.isSidebarCollapsed" class="min-w-0">
                        <p class="text-sm font-medium text-white truncate">
                            {{ authStore.user?.name || "User" }}
                        </p>
                        <p class="text-xs text-gray-400 truncate">
                            {{ primaryRole }}
                        </p>
                    </div>
                </div>
                <!-- Notifications drawer beside username -->
                <div class="relative">
                    <!-- Notifications button removed; now in Navbar -->
                    <!-- Sidebar notifications drawer removed -->
                </div>
            </div>
        </div>

        <!-- Navigation -->
        <nav class="flex-1 overflow-y-auto px-2 py-3 space-y-4">
            <!-- Dashboard Group -->
            <div>
                <RouterLink
                    :to="{ name: 'dashboard' }"
                    class="mt-1 flex items-center gap-3 px-2 py-2 rounded-md hover:bg-gray-800"
                    :class="
                        isActive('dashboard')
                            ? 'bg-gray-800 text-white'
                            : 'text-gray-300'
                    "
                    @click="uiStore.closeSidebar()"
                >
                    <HomeIcon class="h-5 w-5 shrink-0" />
                    <span v-if="!uiStore.isSidebarCollapsed">Dashboard</span>
                </RouterLink>
            </div>

            <!-- Documents Group -->
            <div>
                <button
                    class="w-full flex items-center justify-between px-2 py-2 text-left rounded-md hover:bg-gray-800"
                    @click="uiStore.toggleGroup('documents')"
                >
                    <div class="flex items-center gap-3">
                        <DocumentTextIcon class="h-5 w-5" />
                        <span
                            v-if="!uiStore.isSidebarCollapsed"
                            class="font-medium"
                            >Documents</span
                        >
                    </div>
                    <ChevronDownIcon
                        v-if="!uiStore.isSidebarCollapsed"
                        class="h-5 w-5"
                        :class="{
                            'rotate-180': uiStore.expandedGroups.documents,
                        }"
                    />
                </button>
                <div
                    v-show="uiStore.expandedGroups.documents"
                    class="mt-1 space-y-1"
                >
                    <SidebarLink
                        :to="{ name: 'documents' }"
                        label="All Documents"
                        icon="Squares2X2Icon"
                    />
                    <SidebarLink
                        :to="{ name: 'incoming' }"
                        label="Incoming"
                        icon="InboxArrowDownIcon"
                        :roles="['admin', 'user']"
                    />
                    <SidebarLink
                        :to="{ name: 'received' }"
                        label="Received"
                        icon="InboxStackIcon"
                    />
                    <SidebarLink
                        :to="{ name: 'outgoing' }"
                        label="Outgoing"
                        icon="PaperAirplaneIcon"
                    />
                    <SidebarLink
                        :to="{ name: 'hold' }"
                        label="On Hold"
                        icon="ArchiveBoxIcon"
                    />
                    <SidebarLink
                        :to="{ name: 'completed' }"
                        label="Completed"
                        icon="Completed"
                    />
                    <SidebarLink
                        :to="{ name: 'qr-tracker' }"
                        label="QR Tracker"
                        icon="QrCodeIcon"
                    />

                    <SidebarLink
                        :to="{ name: 'signatures' }"
                        label="Signatures"
                        icon="PencilSquareIcon"
                    />

                    <SidebarLink
                        :to="{ name: 'profile' }"
                        label="Profile"
                        icon="UserCircleIcon"
                    />
                </div>
            </div>

            <!-- Management Group (Admin only) -->
            <div v-if="authStore.hasRole('admin')">
                <button
                    class="w-full flex items-center justify-between px-2 py-2 text-left rounded-md hover:bg-gray-800"
                    @click="uiStore.toggleGroup('management')"
                >
                    <div class="flex items-center gap-3">
                        <WrenchScrewdriverIcon class="h-5 w-5" />
                        <span
                            v-if="!uiStore.isSidebarCollapsed"
                            class="font-medium"
                            >Management</span
                        >
                    </div>
                    <ChevronDownIcon
                        v-if="!uiStore.isSidebarCollapsed"
                        class="h-5 w-5"
                        :class="{
                            'rotate-180': uiStore.expandedGroups.management,
                        }"
                    />
                </button>
                <div
                    v-show="uiStore.expandedGroups.management"
                    class="mt-1 space-y-1"
                >
                    <SidebarLink
                        :to="{ name: 'users' }"
                        label="Manage Users"
                        icon="UsersIcon"
                        :roles="['admin']"
                    />
                    <SidebarLink
                        :to="{ name: 'offices' }"
                        label="Department"
                        icon="BuildingOfficeIcon"
                        :roles="['admin']"
                    />
                </div>
            </div>

            <!-- Reports Group -->
            <div>
                <button
                    v-if="authStore.hasRole('admin')"
                    class="w-full flex items-center justify-between px-2 py-2 text-left rounded-md hover:bg-gray-800"
                    @click="uiStore.toggleGroup('reports')"
                >
                    <div class="flex items-center gap-3">
                        <ChartBarIcon class="h-5 w-5" />
                        <span
                            v-if="!uiStore.isSidebarCollapsed"
                            class="font-medium"
                            >Reports
                        </span>
                    </div>
                    <ChevronDownIcon
                        v-if="!uiStore.isSidebarCollapsed"
                        class="h-5 w-5"
                        :class="{
                            'rotate-180': uiStore.expandedGroups.reports,
                        }"
                    />
                </button>
                <div
                    v-show="uiStore.expandedGroups.reports"
                    class="mt-1 space-y-1"
                >
                    <SidebarLink
                        :to="{ name: 'audit-logs' }"
                        label="Audit Logs"
                        icon="ClipboardDocumentListIcon"
                        :roles="['admin']"
                    />
                </div>
            </div>
        </nav>

        <!-- Profile / Actions -->
        <div class="border-t border-gray-800 p-3 mt-auto">
            <div
                class="mt-3 flex items-center gap-2"
                :class="uiStore.isSidebarCollapsed ? 'justify-center' : ''"
            >
                <button
                    @click="handleLogout"
                    class="flex items-center gap-2 px-2 py-2 rounded-md hover:bg-gray-800 w-full"
                    :class="
                        uiStore.isSidebarCollapsed
                            ? 'w-9 h-9 justify-center'
                            : ''
                    "
                >
                    <ArrowLeftOnRectangleIcon class="h-5 w-5" />
                    <span v-if="!uiStore.isSidebarCollapsed">Logout</span>
                </button>
                <button
                    @click="uiStore.toggleSidebarCollapsed()"
                    class="hidden md:inline-flex items-center gap-2 px-2 py-2 rounded-md hover:bg-gray-800"
                    :class="
                        uiStore.isSidebarCollapsed
                            ? 'w-9 h-9 justify-center'
                            : ''
                    "
                >
                    <ArrowsRightLeftIcon class="h-5 w-5" />
                    <span v-if="!uiStore.isSidebarCollapsed">Collapse</span>
                </button>
            </div>
        </div>

        <!-- Mobile overlay handled by App.vue -->
    </aside>
</template>

<script setup>
import { computed, onMounted, onUnmounted, ref } from "vue";
import { useRouter, useRoute, RouterLink } from "vue-router";
import { useAuthStore } from "../stores/auth";
import { useUiStore } from "../stores/ui";

// Icons - outline set
import {
    HomeIcon,
    DocumentTextIcon,
    Squares2X2Icon,
    InboxArrowDownIcon,
    InboxStackIcon,
    PaperAirplaneIcon,
    ArchiveBoxIcon,
    ExclamationTriangleIcon,
    QrCodeIcon,
    PencilSquareIcon,
    BellIcon,
    UserCircleIcon,
    WrenchScrewdriverIcon,
    UsersIcon,
    BuildingOfficeIcon,
    ChartBarIcon,
    ClipboardDocumentListIcon,
    ChevronDownIcon,
    ChevronDoubleLeftIcon,
    ChevronDoubleRightIcon,
    ArrowLeftOnRectangleIcon,
    ArrowsRightLeftIcon,
} from "@heroicons/vue/24/outline";

const router = useRouter();
const route = useRoute();
const authStore = useAuthStore();
const uiStore = useUiStore();

const handleLogout = async () => {
    await authStore.logout();
    router.push("/login");
};


const initials = computed(() => {
    const name = authStore.user?.name || "U";
    return name
        .split(" ")
        .map((n) => n[0])
        .join("")
        .slice(0, 2)
        .toUpperCase();
});

const primaryRole = computed(() => (authStore.user?.roles || [])[0] || "");

const isActive = (name) => route.name === name;

// SidebarLink as local component to reduce duplication
const SidebarLink = {
    props: {
        to: { type: [String, Object], required: true },
        label: { type: String, required: true },
        icon: { type: String, required: true },
        roles: { type: Array, default: () => [] },
    },
    setup(props) {
        const authStore = useAuthStore();
        const uiStore = useUiStore();
        const route = useRoute();
        const allowed = computed(() => {
            if (!props.roles || props.roles.length === 0) return true;
            return (authStore.user?.roles || []).some((r) =>
                props.roles.includes(r)
            );
        });
        const active = computed(() => {
            if (typeof props.to === "string") return route.path === props.to;
            return props.to?.name ? route.name === props.to.name : false;
        });
        const iconMap = {
            HomeIcon,
            DocumentTextIcon,
            Squares2X2Icon,
            InboxArrowDownIcon,
            InboxStackIcon,
            PaperAirplaneIcon,
            ArchiveBoxIcon,
            ExclamationTriangleIcon,
            QrCodeIcon,
            PencilSquareIcon,
            BellIcon,
            UserCircleIcon,
            WrenchScrewdriverIcon,
            UsersIcon,
            BuildingOfficeIcon,
            ChartBarIcon,
            ClipboardDocumentListIcon,
        };
        const IconComp = computed(
            () => iconMap[props.icon] || DocumentTextIcon
        );
        return {
            allowed,
            active,
            IconComp,
            uiStore,
        };
    },
    template: `
    <RouterLink v-if="allowed" :to="to" class="flex items-center gap-3 px-2 py-2 rounded-md hover:bg-gray-800"
        :class="active ? 'bg-gray-800 text-white' : 'text-gray-300'" @click="uiStore.closeSidebar()">
        <component :is="IconComp" class="h-5 w-5 shrink-0" />
        <span v-if="!uiStore.isSidebarCollapsed">{{ label }}</span>
    </RouterLink>
    `,
    components: { RouterLink },
};
</script>

<style scoped>
.rotate-180 {
    transform: rotate(180deg);
}
</style>
