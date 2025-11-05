import { defineStore } from "pinia";
import axios from "axios";

export const useDepartmentsStore = defineStore("departments", {
    state: () => ({
        items: [],
        loading: false,
        loadedAt: 0,
    }),
    getters: {
        isStale: (state) => Date.now() - state.loadedAt > 60_000,
        options: (state) =>
            state.items.map((d) => ({ value: d.id, label: d.name })),
    },
    actions: {
        async fetch(force = false) {
            if (!force && this.items.length && !this.isStale) return;
            this.loading = true;
            try {
                const res = await axios.get("/api/departments");
                this.items = res.data.data || res.data;
                this.loadedAt = Date.now();
            } finally {
                this.loading = false;
            }
        },
        async create(payload) {
            const res = await axios.post("/api/departments", payload);
            const newDepartment = res.data.data || res.data;
            this.items.unshift(newDepartment);
            return newDepartment;
        },
        async update(id, payload) {
            const res = await axios.put(`/api/departments/${id}`, payload);
            const updatedDepartment = res.data.data || res.data;
            const idx = this.items.findIndex((d) => d.id === id);
            if (idx !== -1) this.items[idx] = updatedDepartment;
            return updatedDepartment;
        },
        async remove(id) {
            await axios.delete(`/api/departments/${id}`);
            this.items = this.items.filter((d) => d.id !== id);
        },
    },
});
