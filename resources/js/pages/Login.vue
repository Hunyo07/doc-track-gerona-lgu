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
                    class="h-10 w-10 rounded-full"
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
            class="flex-1 bg-cover bg-center relative bg-no-repeat"
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
                                            inputmode="email"
                                            autocapitalize="none"
                                            autofocus
                                            required
                                            :aria-invalid="Boolean(fieldErrors.email)"
                                            aria-describedby="email-error"
                                            class="bg-gray-50 border border-gray-300 text-gray-900 rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                                            placeholder="Email address"
                                        />
                                        <p
                                            v-if="fieldErrors.email"
                                            id="email-error"
                                            class="mt-1 text-red-600 text-xs"
                                        >
                                            {{ fieldErrors.email }}
                                        </p>
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
                                            :type="showPassword ? 'text' : 'password'"
                                            autocomplete="current-password"
                                            required
                                            :aria-invalid="Boolean(fieldErrors.password)"
                                            aria-describedby="password-error"
                                            @keydown="onPasswordKeydown"
                                            class="bg-gray-50 border border-gray-300 text-gray-900 rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-2.5 pr-10 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                                            placeholder="Password"
                                        />
                                        <button
                                            type="button"
                                            class="absolute inset-y-0 right-0 flex items-center pr-3 text-gray-500 hover:text-gray-700 focus:outline-none focus:ring-2 focus:ring-sky-600 rounded"
                                            :aria-pressed="showPassword"
                                            :aria-label="showPassword ? 'Hide password' : 'Show password'"
                                            @click="togglePassword"
                                        >
                                            <svg v-if="!showPassword" class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.477 0 8.268 2.943 9.542 7-1.274 4.057-5.065 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                            </svg>
                                            <svg v-else class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.477 0-8.268-2.943-9.542-7a9.957 9.957 0 012.051-3.36M6.7 6.7A9.956 9.956 0 0112 5c4.477 0 8.268 2.943 9.542 7a9.96 9.96 0 01-3.055 4.18M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3l18 18" />
                                            </svg>
                                        </button>
                                        <p
                                            v-if="fieldErrors.password"
                                            id="password-error"
                                            class="mt-1 text-red-600 text-xs"
                                        >
                                            {{ fieldErrors.password }}
                                        </p>
                                        <p v-if="capsLockOn" class="mt-1 text-yellow-700 text-xs">Caps Lock is on</p>
                                    </div>
                                    <div class="flex items-center justify-between">
                                        <label class="inline-flex items-center">
                                            <input
                                                id="remember"
                                                v-model="form.remember"
                                                type="checkbox"
                                                class="rounded text-sky-700 focus:ring-sky-600"
                                            />
                                            <span class="ml-2 text-sm text-gray-700">Remember me</span>
                                        </label>
                                    </div>
                                </div>

                                <div
                                    v-if="error"
                                    role="alert"
                                    aria-live="polite"
                                    class="text-red-600 text-sm text-center"
                                >
                                    {{ error }}
                                </div>

                                <button
                                    type="submit"
                                    :disabled="loading || !canSubmit"
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
                    class="h-8 w-8 rounded-full"
                />
                <div>
                    <p class="text-sm">LGU Gerona Tarlac • Serbisyong Totoo</p>
                </div>
            </div>
        </footer>
    </div>
</template>

<script setup>
import { ref, reactive, computed } from "vue";
import { useRouter } from "vue-router";
import { useAuthStore } from "../stores/auth";

const router = useRouter();
const authStore = useAuthStore();

const form = reactive({
    email: "",
    password: "",
    remember: true,
});

const loading = ref(false);
const error = ref("");
const fieldErrors = reactive({ email: "", password: "" });
const showPassword = ref(false);
const capsLockOn = ref(false);

const logoSrc = "/geronalogo.ico";
// Use Vite asset resolution for images under resources/assets
const backgroundSrc = new URL("../../assets/img/lgugerona.jpg", import.meta.url).href;

const togglePassword = () => {
    showPassword.value = !showPassword.value;
};

const onPasswordKeydown = (e) => {
    try {
        capsLockOn.value = e.getModifierState && e.getModifierState("CapsLock");
    } catch {
        capsLockOn.value = false;
    }
};

const canSubmit = computed(() => {
    return Boolean(form.email?.trim()) && Boolean(form.password?.trim());
});

const handleLogin = async () => {
    loading.value = true;
    error.value = "";

    const result = await authStore.login(form);

    if (result.success) {
        // Prefer redirect query param if present
        const redirectTo = router.currentRoute.value.query?.redirect;
        if (redirectTo) {
            router.push(String(redirectTo));
        } else {
            // Role-based redirection after login
            const rolesArray = authStore.user?.roles || [];
            if (Array.isArray(rolesArray) && rolesArray.includes("admin")) {
                router.push("/users");
            } else {
                router.push("/");
            }
        }
    } else {
        error.value = result.message || "Login failed";
        fieldErrors.email = result.errors?.email?.[0] || "";
        fieldErrors.password = result.errors?.password?.[0] || "";
    }

    loading.value = false;
};
</script>
