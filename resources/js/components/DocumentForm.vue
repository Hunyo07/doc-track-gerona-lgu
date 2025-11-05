<template>
    <div class="max-w-2xl mx-auto">
        <form @submit.prevent="submitForm" class="space-y-6">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1"
                    >Title</label
                >
                <input
                    v-model="form.title"
                    type="text"
                    required
                    class="w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500"
                />
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1"
                    >Description</label
                >
                <textarea
                    v-model="form.description"
                    rows="3"
                    class="w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500"
                ></textarea>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1"
                        >Document Type</label
                    >
                    <select
                        v-model="form.document_type_id"
                        required
                        class="w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500"
                    >
                        <option value="">Select Type</option>
                        <option
                            v-for="type in documentTypes"
                            :key="type.id"
                            :value="type.id"
                        >
                            {{ type.name }}
                        </option>
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1"
                        >Priority</label
                    >
                    <select
                        v-model="form.priority"
                        required
                        class="w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500"
                    >
                        <option value="low">Low</option>
                        <option value="medium">Medium</option>
                        <option value="high">High</option>
                        <option value="urgent">Urgent</option>
                    </select>
                </div>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1"
                    >Deadline</label
                >
                <input
                    v-model="form.deadline"
                    type="date"
                    class="w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500"
                />
            </div>

            <div v-if="error" class="text-red-600 text-sm">
                {{ error }}
            </div>

            <div class="flex justify-end space-x-3">
                <button
                    type="button"
                    @click="$emit('cancel')"
                    class="px-4 py-2 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50"
                >
                    Cancel
                </button>
                <button
                    type="submit"
                    :disabled="loading"
                    class="px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700 disabled:opacity-50"
                >
                    <span v-if="loading">Creating...</span>
                    <span v-else>Create Document</span>
                </button>
            </div>
        </form>
    </div>
</template>

<script setup>
import { ref, reactive, onMounted } from "vue";
import { useDocumentStore } from "../stores/documents";
import axios from "axios";

const documentStore = useDocumentStore();

const form = reactive({
    title: "",
    description: "",
    document_type_id: "",
    priority: "medium",
    deadline: "",
});

const documentTypes = ref([]);
const loading = ref(false);
const error = ref("");

const fetchDocumentTypes = async () => {
    try {
        const response = await axios.get("/api/document-types");
        documentTypes.value = response.data;
    } catch (err) {
        console.error("Error fetching document types:", err);
    }
};

const submitForm = async () => {
    loading.value = true;
    error.value = "";

    try {
        const document = await documentStore.createDocument(form);
        emit("created", document);
    } catch (err) {
        error.value = err.response?.data?.message || "Error creating document";
    } finally {
        loading.value = false;
    }
};

const emit = defineEmits(["created", "cancel"]);

onMounted(() => {
    fetchDocumentTypes();
});
</script>
