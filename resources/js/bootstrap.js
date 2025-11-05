import axios from "axios";
window.axios = axios;

// Set the base URL for API requests
axios.defaults.baseURL = "http://127.0.0.1:8000";
window.axios.defaults.headers.common["X-Requested-With"] = "XMLHttpRequest";
// Ensure cookies are sent for Sanctum auth across Vite dev server origin
axios.defaults.withCredentials = true;
// Prefer JSON responses for API routes
axios.defaults.headers.common["Accept"] = "application/json";
