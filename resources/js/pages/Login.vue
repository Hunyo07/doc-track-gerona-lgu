<template>
    <div class="flex flex-col bg-sky-700 md:h-svh lg:py-0">
        <!-- Header strip with logo/title -->
        <div class="bg-sky-700 text-white">
            <div
                class="container mx-auto px-4 py-3 flex items-center space-x-3"
            >
                <img
                    :src="logoSrc"
                    alt="Logo"
                    class="h-10 w-10 rounded-full bg-white p-1"
                />
                <div>
                    <p class="text-xs uppercase tracking-wider text-sky-200">
                        Republic of the Philippines
                    </p>
                    <h1 class="text-xl font-semibold leading-tight">
                        LGU Gerona Tarlac
                    </h1>
                    <p class="text-xs text-sky-200 -mt-0.5">Serbisyong Totoo</p>
                </div>
            </div>
        </div>

        <!-- Background image area -->
        <div
            class="flex-1 bg-cover bg-center relative bg-[url(/resources/assets/img/lgugerona.jpg)] bg-no-repeat"
            :style="{ backgroundImage: `url(${backgroundSrc})` }"
        >
            <div class="absolute inset-0 bg-sky-900/50"></div>

            <!-- Centered card -->
            <div class="absolute inset-0 flex items-center justify-center">
                <div
                    class="relative z-10 flex flex-col items-center justify-center px-6 py-8 mx-auto"
                >
                    <div class="max-w-sm w-full">
                        <div class="bg-white rounded-lg shadow-xl p-6 sm:p-8">
                            <h2
                                class="text-center text-2xl mb-4 font-extrabold text-gray-900"
                            >
                                Document Tracking System Login
                            </h2>

                            <form
                                class="space-y-4"
                                @submit.prevent="handleLogin"
                            >
                                <div class="space-y-3">
                                    <div class="relative">
                                        <label for="email" class="sr-only"
                                            >Email address</label
                                        >
                                        <input
                                            id="email"
                                            v-model="form.email"
                                            name="email"
                                            type="email"
                                            autocomplete="email"
                                            required
                                            class="bg-gray-50 border border-gray-300 text-gray-900 rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                                            placeholder="Username"
                                        />
                                        <span
                                            class="pointer-events-none absolute inset-y-0 right-0 flex items-center pr-3 text-gray-400"
                                        >
                                            <svg
                                                class="h-5 w-5"
                                                fill="none"
                                                stroke="currentColor"
                                                viewBox="0 0 24 24"
                                            >
                                                <path
                                                    stroke-linecap="round"
                                                    stroke-linejoin="round"
                                                    stroke-width="2"
                                                    d="M5.121 17.804A13.937 13.937 0 0112 15c2.5 0 4.847.655 6.879 1.804M15 11a3 3 0 11-6 0 3 3 0 016 0z"
                                                />
                                            </svg>
                                        </span>
                                    </div>
                                    <div class="relative">
                                        <label for="password" class="sr-only"
                                            >Password</label
                                        >
                                        <input
                                            id="password"
                                            v-model="form.password"
                                            name="password"
                                            type="password"
                                            autocomplete="current-password"
                                            required
                                            class="bg-gray-50 border border-gray-300 text-gray-900 rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                                            placeholder="Password"
                                        />
                                        <span
                                            class="pointer-events-none absolute inset-y-0 right-0 flex items-center pr-3 text-gray-400"
                                        >
                                            <svg
                                                class="h-5 w-5"
                                                fill="none"
                                                stroke="currentColor"
                                                viewBox="0 0 24 24"
                                            >
                                                <path
                                                    stroke-linecap="round"
                                                    stroke-linejoin="round"
                                                    stroke-width="2"
                                                    d="M12 11c-1.657 0-3-1.343-3-3V6a3 3 0 116 0v2c0 1.657-1.343 3-3 3z"
                                                />
                                            </svg>
                                        </span>
                                    </div>
                                </div>

                                <div
                                    v-if="error"
                                    class="text-red-600 text-sm text-center"
                                >
                                    {{ error }}
                                </div>

                                <button
                                    type="submit"
                                    :disabled="loading"
                                    class="w-full py-2 px-4 text-sm font-medium rounded-md text-white bg-sky-700 hover:bg-sky-800 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-sky-600 disabled:opacity-50"
                                >
                                    <span v-if="loading">Signing in...</span>
                                    <span v-else>Login</span>
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <footer class="bg-sky-700 text-white py-6">
            <div class="container mx-auto px-4 flex items-center space-x-3">
                <img
                    :src="logoSrc"
                    alt="Logo"
                    class="h-8 w-8 rounded-full bg-white p-1"
                />
                <div>
                    <p class="text-sm">LGU Gerona Tarlac • Serbisyong Totoo</p>
                </div>
            </div>
        </footer>
    </div>
</template>

<script setup>
import { ref, reactive } from "vue";
import { useRouter } from "vue-router";
import { useAuthStore } from "../stores/auth";

const router = useRouter();
const authStore = useAuthStore();

const form = reactive({
    email: "",
    password: "",
});

const loading = ref(false);
const error = ref("");

const logoSrc = "/geronalogo.ico";
const backgroundSrc = "(/resources/assets/img/lgugerona.jpg)";

const handleLogin = async () => {
    loading.value = true;
    error.value = "";

    const result = await authStore.login(form);

    if (result.success) {
        // Role-based redirection after login
        const userRoles = authStore.user?.roles || [];
        
        console.log("User roles after login:", userRoles);
        console.log("User roles type:", typeof userRoles);
        console.log("Is array:", Array.isArray(userRoles));
        
        // Convert roles to array if it's a Laravel Collection object
        const rolesArray = Array.isArray(userRoles) ? userRoles : Object.values(userRoles);
        
        if (rolesArray.includes('admin')) {
            // Redirect admin users to users management page
            router.push("/users");
        } else {
            // Redirect regular users to dashboard
            router.push("/");
        }
    } else {
        error.value = result.message;
    }

    loading.value = false;
};
</script>
