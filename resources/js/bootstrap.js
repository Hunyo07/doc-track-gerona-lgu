import axios from "axios";
window.axios = axios;

// Set the base URL for API requests
axios.defaults.baseURL = "http://13.222.119.222";
window.axios.defaults.headers.common["X-Requested-With"] = "XMLHttpRequest";
// Ensure cookies are sent for Sanctum auth across Vite dev server origin
axios.defaults.withCredentials = true;
// Prefer JSON responses for API routes
axios.defaults.headers.common["Accept"] = "application/json";
