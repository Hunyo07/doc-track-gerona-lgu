import { createApp } from "vue";
import { createPinia } from "pinia";
import { createRouter, createWebHistory } from "vue-router";
import App from "./App.vue";
import Toast from "vue-toastification";
import "vue-toastification/dist/index.css";
import "./bootstrap";

// Import pages
import Login from "./pages/Login.vue";
import Dashboard from "./pages/Dashboard.vue";
import Documents from "./pages/Documents.vue";
import DocumentDetail from "./pages/DocumentDetail.vue";
import Incoming from "./pages/Incoming.vue";
import Received from "./pages/Received.vue";
import Outgoing from "./pages/Outgoing.vue";
import Hold from "./pages/Hold.vue";
import Completed from "./pages/Completed.vue";
import Users from "./pages/Users.vue";
const Offices = () => import("./pages/Offices.vue");
import Profile from "./pages/Profile.vue";
import DocumentView from "./pages/DocumentView.vue";
import AuditLogs from "./pages/AuditLogs.vue";
import IssueReport from "./pages/IssueReport.vue";
import ScanQR from "./pages/ScanQR.vue";
import Signatures from "./pages/Signatures.vue";
import Notifications from "./pages/Notifications.vue";
import QrGenerator from "./pages/QrGenerator.vue";
import QRTracker from "./pages/QRTracker.vue";

// Import stores
import { useAuthStore } from "./stores/auth";
import { useDocumentStore } from "./stores/documents";

const routes = [
    { path: "/login", name: "login", component: Login },
    {
        path: "/",
        name: "dashboard",
        component: Dashboard,
        meta: { requiresAuth: true },
    },
    {
        path: "/documents",
        name: "documents",
        component: Documents,
        meta: { requiresAuth: true },
    },
    {
        path: "/documents/:id",
        name: "document-detail",
        component: DocumentDetail,
        meta: { requiresAuth: true },
    },
    {
        path: "/incoming",
        name: "incoming",
        component: Incoming,
        meta: { requiresAuth: true, roles: ["admin", "user"] },
    },
    {
        path: "/received",
        name: "received",
        component: Received,
        meta: { requiresAuth: true },
    },
    {
        path: "/outgoing",
        name: "outgoing",
        component: Outgoing,
        meta: { requiresAuth: true },
    },
    {
        path: "/hold",
        name: "hold",
        component: Hold,
        meta: { requiresAuth: true },
    },
    {
        path: "/completed",
        name: "completed",
        component: Completed,
        meta: { requiresAuth: true, roles: ["admin", "user"] },
    },
    {
        path: "/documents/code/:document_number",
        name: "document-view",
        component: DocumentView,
    },
    {
        path: "/users",
        name: "users",
        component: Users,
        meta: { requiresAuth: true, roles: ["admin"] },
    },
    {
        path: "/admin/offices",
        name: "offices",
        component: Offices,
        meta: { requiresAuth: true, roles: ["admin"] },
    },
    {
        path: "/profile",
        name: "profile",
        component: Profile,
        meta: { requiresAuth: true },
    },
    {
        path: "/audit-logs",
        name: "audit-logs",
        component: AuditLogs,
        meta: { requiresAuth: true, roles: ["admin"] },
    },
    {
        path: "/issue-report",
        name: "issue-report",
        component: IssueReport,
        meta: { requiresAuth: true, roles: ["admin", "user"] },
    },
    {
        path: "/qr-tracker",
        name: "qr-tracker",
        component: QRTracker,
        meta: { requiresAuth: true, roles: ["admin", "user"] },
    },
    {
        path: "/scan-qr",
        name: "scan-qr",
        component: ScanQR,
        meta: { requiresAuth: true, roles: ["user", "admin"] },
    },
    {
        path: "/qr/generator",
        name: "qr-generator",
        component: QrGenerator,
        meta: { requiresAuth: true, roles: ["user", "admin"] },
    },
    {
        path: "/signatures",
        name: "signatures",
        component: Signatures,
        meta: { requiresAuth: true, roles: ["admin", "user"] },
    },
    {
        path: "/notifications",
        name: "notifications",
        component: Notifications,
        meta: { requiresAuth: true },
    },
];

const router = createRouter({
    history: createWebHistory(),
    routes,
});

const pinia = createPinia();
const app = createApp(App);

app.use(pinia);
app.use(router);
app.use(Toast, { position: "top-right", timeout: 3000, closeOnClick: true });

// Initialize auth from persisted token
const authStore = useAuthStore();

// Navigation guard
router.beforeEach(async (to, from, next) => {
    const authStore = useAuthStore();

    // Ensure auth is initialized before checking authentication
    if (!authStore.isAuthenticated && authStore.token) {
        await authStore.initializeAuth();
    }

    if (to.meta.requiresAuth && !authStore.isAuthenticated) {
        next("/login");
    } else if (to.path === "/login" && authStore.isAuthenticated) {
        // Role-based redirection when accessing login while authenticated
        const userRoles = authStore.user?.roles || [];

        // Convert roles to array if it's a Laravel Collection object
        const rolesArray = Array.isArray(userRoles)
            ? userRoles
            : Object.values(userRoles);

        if (rolesArray.includes("admin")) {
            next("/users");
        } else {
            next("/");
        }
    } else {
        // Role-based route protection (if route declares meta.roles)
        if (to.meta && to.meta.roles && to.meta.roles.length > 0) {
            const allowed = authStore.roles.some((r) =>
                to.meta.roles.includes(r)
            );
            if (!allowed) return next("/");
        }

        next();
    }
});

// Initialize auth after router setup
(async () => {
    await authStore.initializeAuth();
    app.mount("#app");
})();
