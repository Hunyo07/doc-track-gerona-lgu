import { defineStore } from "pinia";

export const useUiStore = defineStore("ui", {
    state: () => ({
        isSidebarOpen: false,
        isSidebarCollapsed:
            typeof window !== "undefined" &&
            localStorage.getItem("sidebar:collapsed") === "1",
        expandedGroups: (() => {
            try {
                const raw =
                    typeof window !== "undefined" &&
                    localStorage.getItem("sidebar:groups");
                return raw
                    ? JSON.parse(raw)
                    : { documents: true, management: true, reports: true };
            } catch {
                return { documents: true, management: true, reports: true };
            }
        })(),
    }),

    actions: {
        toggleSidebarOpen() {
            this.isSidebarOpen = !this.isSidebarOpen;
        },
        closeSidebar() {
            this.isSidebarOpen = false;
        },
        openSidebar() {
            this.isSidebarOpen = true;
        },
        toggleSidebarCollapsed() {
            this.isSidebarCollapsed = !this.isSidebarCollapsed;
            if (typeof window !== "undefined") {
                localStorage.setItem(
                    "sidebar:collapsed",
                    this.isSidebarCollapsed ? "1" : "0"
                );
            }
        },
        toggleGroup(key) {
            this.expandedGroups[key] = !this.expandedGroups[key];
            if (typeof window !== "undefined") {
                localStorage.setItem(
                    "sidebar:groups",
                    JSON.stringify(this.expandedGroups)
                );
            }
        },
        setGroup(key, value) {
            this.expandedGroups[key] = !!value;
            if (typeof window !== "undefined") {
                localStorage.setItem(
                    "sidebar:groups",
                    JSON.stringify(this.expandedGroups)
                );
            }
        },
    },
});
