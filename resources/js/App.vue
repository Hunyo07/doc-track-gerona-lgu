<template>
    <div id="app" class="min-h-screen bg-gray-100 flex flex-col">
        <Navbar v-if="authStore.isAuthenticated" />
        <div class="flex flex-1 relative">
            <Sidebar v-if="authStore.isAuthenticated" />
            <!-- Overlay for mobile when sidebar is open -->
            <div
                v-if="authStore.isAuthenticated && uiStore.isSidebarOpen"
                class="fixed inset-0 bg-black/50 z-30 md:hidden"
                @click="uiStore.closeSidebar()"
            ></div>
            <main class="flex-1 ml-0 md:ml-0">
                <router-view />
            </main>
        </div>
    </div>
    
</template>

<script setup>
import { useAuthStore } from "./stores/auth";
import { useUiStore } from "./stores/ui";
import Navbar from "./components/Navbar.vue";
import Sidebar from "./components/Sidebar.vue";

const authStore = useAuthStore();
const uiStore = useUiStore();
const logoSrc = "/geronalogo.ico";
</script>
