import { defineStore } from "pinia";
import axios from "axios";

export const useAuthStore = defineStore("auth", {
    state: () => ({
        user: null,
        token: localStorage.getItem("token") || sessionStorage.getItem("token"),
        isAuthenticated: false,
    }),

    getters: {
        isLoggedIn: (state) => !!state.token && !!state.user,
        roles: (state) => state.user?.roles || [],
        permissions: (state) => state.user?.permissions || [],
        hasRole: (state) => (role) => (state.user?.roles || []).includes(role),
        hasAnyRole: (state) => (roles) =>
            (state.user?.roles || []).some((r) => roles.includes(r)),
        can: (state) => (permission) =>
            (state.user?.permissions || []).includes(permission),
    },

    actions: {
        async login(credentials) {
            try {
                const { email, password, remember = true } = credentials;

                const response = await axios.post("/api/auth/login", {
                    email,
                    password,
                });
                // ApiResponse wraps data in a 'data' property
                const { user, token } = response.data.data;

                this.user = user;
                this.token = token;
                this.isAuthenticated = true;

                // Persist token according to Remember Me preference
                if (remember) {
                    localStorage.setItem("token", token);
                    sessionStorage.removeItem("token");
                } else {
                    sessionStorage.setItem("token", token);
                    localStorage.removeItem("token");
                }

                axios.defaults.headers.common["Authorization"] = `Bearer ${token}`;

                return { success: true };
            } catch (error) {
                return {
                    success: false,
                    message: error.response?.data?.message || "Login failed",
                    errors: error.response?.data?.errors || null,
                };
            }
        },
        async register(userData) {
            try {
                const response = await axios.post(
                    "/api/auth/register",
                    userData
                );
                const { user, token } = response.data;

                this.user = user;
                this.token = token;
                this.isAuthenticated = true;

                localStorage.setItem("token", token);
                axios.defaults.headers.common[
                    "Authorization"
                ] = `Bearer ${token}`;

                return { success: true };
            } catch (error) {
                return {
                    success: false,
                    message:
                        error.response?.data?.message || "Registration failed",
                };
            }
        },

        async logout() {
            try {
                await axios.post("/api/auth/logout");
            } catch (error) {
                console.error("Logout error:", error);
            } finally {
                this.user = null;
                this.token = null;
                this.isAuthenticated = false;
                localStorage.removeItem("token");
                sessionStorage.removeItem("token");
                delete axios.defaults.headers.common["Authorization"];
            }
        },

        async fetchUser() {
            try {
                const response = await axios.get("/api/auth/me");
                // ApiResponse wraps data in a 'data' property
                this.user = response.data.data;
                this.isAuthenticated = true;
            } catch (error) {
                console.error("Failed to fetch user:", error);
                this.logout();
            }
        },

        async initializeAuth() {
            // Check both localStorage and sessionStorage for token
            const localToken = localStorage.getItem("token");
            const sessionToken = sessionStorage.getItem("token");
            const storedToken = localToken || sessionToken;

            if (storedToken) {
                // Sync Pinia state with stored token
                this.token = storedToken;
                axios.defaults.headers.common["Authorization"] = `Bearer ${storedToken}`;
                await this.fetchUser();
            } else {
                // No token found, ensure clean state
                this.token = null;
                this.user = null;
                this.isAuthenticated = false;
                delete axios.defaults.headers.common["Authorization"];
            }
        },
    },
});
