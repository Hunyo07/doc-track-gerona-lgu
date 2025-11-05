import { defineStore } from "pinia";
import axios from "axios";

export const useNotificationsStore = defineStore("notifications", {
  state: () => ({
    items: [],
    unread: 0,
    loading: false,
    pagination: null,
  }),
  actions: {
    async fetchUnreadCount() {
      try {
        const res = await axios.get('/api/notifications/unread-count');
        this.unread = res.data?.unread ?? 0;
      } catch (e) {
        console.error('Failed to fetch unread count', e);
      }
    },
    async fetch(page = 1, perPage = 20) {
      this.loading = true;
      try {
        const res = await axios.get('/api/notifications', { params: { page, per_page: perPage } });
        // Laravel paginator shape: { data, current_page, last_page, total, per_page }
        const data = res.data;
        this.items = data.data ?? [];
        this.pagination = {
          current_page: data.current_page,
          last_page: data.last_page,
          total: data.total,
          per_page: data.per_page,
        };
      } finally {
        this.loading = false;
      }
    },
    async markAsRead(id) {
      try {
        await axios.post(`/api/notifications/${id}/read`);
        this.items = this.items.map(n => n.id === id ? { ...n, read_at: n.read_at || new Date().toISOString() } : n);
        if (this.unread > 0) this.unread -= 1;
      } catch (e) {
        console.error('Failed to mark notification as read', e);
      }
    },
    async markAllAsRead() {
      try {
        await axios.post('/api/notifications/read-all');
        this.items = this.items.map(n => ({ ...n, read_at: n.read_at || new Date().toISOString() }));
        this.unread = 0;
      } catch (e) {
        console.error('Failed to mark all notifications as read', e);
      }
    },
  },
});