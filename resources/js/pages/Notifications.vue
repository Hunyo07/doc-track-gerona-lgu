<template>
  <div class="p-4">
    <!-- Page Header -->
    <div class="flex items-center justify-between mb-4">
      <h1 class="text-2xl font-semibold">Notifications</h1>
      <div class="flex items-center gap-3">
        <div class="text-sm text-gray-600">Unread: <span class="font-semibold">{{ store.unread }}</span></div>
        <button
          @click="markAll"
          class="px-3 py-2 bg-indigo-600 text-white rounded hover:bg-indigo-700 disabled:opacity-50"
          :disabled="store.unread === 0 || bulkWorking"
        >Mark all as read</button>
      </div>
    </div>

    <!-- Tabs & Search -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-3 mb-4">
      <div class="flex items-center gap-1 bg-white rounded-md p-1 shadow-sm border border-gray-200">
        <button
          class="px-3 py-1.5 rounded-md text-sm"
          :class="activeTab === 'all' ? 'bg-indigo-600 text-white' : 'text-gray-700 hover:bg-gray-100'"
          @click="activeTab = 'all'"
        >All<span class="ml-1 text-xs opacity-80">{{ allCount }}</span></button>
        <button
          class="px-3 py-1.5 rounded-md text-sm"
          :class="activeTab === 'unread' ? 'bg-indigo-600 text-white' : 'text-gray-700 hover:bg-gray-100'"
          @click="activeTab = 'unread'"
        >Unread<span class="ml-1 text-xs opacity-80">{{ unreadCount }}</span></button>
        <button
          class="px-3 py-1.5 rounded-md text-sm"
          :class="activeTab === 'read' ? 'bg-indigo-600 text-white' : 'text-gray-700 hover:bg-gray-100'"
          @click="activeTab = 'read'"
        >Read<span class="ml-1 text-xs opacity-80">{{ readCount }}</span></button>
      </div>
      <div class="relative w-full md:w-96">
        <input
          v-model="searchQuery"
          type="text"
          placeholder="Search title or message..."
          class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500"
          @keydown="handleSlashFocus"
        />
        <span class="absolute right-2 top-1/2 -translate-y-1/2 text-xs text-gray-400">/</span>
      </div>
    </div>

    <!-- Bulk actions toolbar -->
    <div v-if="selectedIds.length > 0" class="flex items-center justify-between bg-indigo-50 border border-indigo-200 rounded-md p-2 mb-4">
      <div class="text-sm text-indigo-700">Selected {{ selectedIds.length }} item(s)</div>
      <div class="flex items-center gap-2">
        <button
          @click="bulkMarkRead"
          class="px-3 py-1.5 bg-indigo-600 text-white rounded hover:bg-indigo-700 disabled:opacity-50"
          :disabled="bulkWorking"
        >Mark selected as read</button>
        <button
          @click="bulkDelete"
          class="px-3 py-1.5 bg-red-600 text-white rounded hover:bg-red-700 disabled:opacity-50"
          :disabled="bulkWorking"
        >Delete selected</button>
        <button
          @click="clearSelection"
          class="px-3 py-1.5 bg-white text-gray-700 border border-gray-300 rounded hover:bg-gray-50"
        >Clear</button>
      </div>
    </div>

    <!-- Loading Skeleton -->
    <div v-if="store.loading" class="space-y-3">
      <div v-for="i in 5" :key="i" class="animate-pulse border rounded p-3 bg-white">
        <div class="flex items-center gap-2 mb-2">
          <div class="h-4 w-16 bg-gray-200 rounded"></div>
          <div class="h-4 w-32 bg-gray-200 rounded"></div>
        </div>
        <div class="h-5 w-1/3 bg-gray-200 rounded mb-2"></div>
        <div class="h-4 w-2/3 bg-gray-200 rounded"></div>
      </div>
    </div>

    <!-- Content -->
    <div v-else>
      <div v-if="filteredItems.length === 0" class="bg-white border rounded p-6 text-center text-gray-600">
        <div class="flex justify-center mb-2">
          <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-gray-400" viewBox="0 0 20 20" fill="currentColor"><path d="M2 10a8 8 0 1116 0 8 8 0 01-16 0zm8-5a1 1 0 00-1 1v3H6a1 1 0 100 2h3v3a1 1 0 102 0v-3h3a1 1 0 100-2h-3V6a1 1 0 00-1-1z"/></svg>
        </div>
        <p class="mb-2">No notifications{{ searchQuery ? ' matching your search' : '' }}.</p>
        <p class="text-sm text-gray-500">Try clearing filters or check back later.</p>
      </div>

      <div class="mb-2 flex items-center gap-2">
        <label class="inline-flex items-center gap-2 text-sm text-gray-700">
          <input type="checkbox" class="rounded border-gray-300" :checked="isAllSelected" @change="toggleSelectAll" />
          Select all on this page
        </label>
      </div>

      <ul class="space-y-2">
        <li
          v-for="n in pagedItems"
          :key="n.id"
          class="border rounded p-3 flex items-start gap-3 bg-white"
          :class="!n.read_at ? 'border-indigo-200 bg-indigo-50' : 'border-gray-200'"
        >
          <input
            type="checkbox"
            class="mt-1 rounded border-gray-300"
            :checked="selectedIds.includes(n.id)"
            @change="toggleSelected(n.id)"
            aria-label="Select notification"
          />
          <div class="flex-1">
            <div class="flex items-center gap-2">
              <span class="text-xs px-2 py-0.5 rounded bg-gray-200 text-gray-700" v-if="n.type">{{ n.type }}</span>
              <span class="text-xs text-gray-500">{{ formatDate(n.created_at) }}</span>
              <span v-if="!n.read_at" class="ml-2 text-xs text-indigo-700 font-medium">Unread</span>
            </div>
            <h3 class="text-sm font-semibold mt-1">{{ (n.data && n.data.title) || 'Notification' }}</h3>
            <p class="text-sm text-gray-700 mt-1">{{ (n.data && n.data.message) || '' }}</p>
            <div v-if="n.data && (n.data.document_number || n.data.document_id)" class="mt-2 text-xs text-gray-600">
              Related: <span class="font-mono">{{ n.data.document_number || ('ID ' + n.data.document_id) }}</span>
            </div>
          </div>
          <div class="flex items-center gap-2">
            <button
              v-if="!n.read_at"
              @click="mark(n.id)"
              class="text-indigo-600 hover:text-indigo-800 text-sm"
            >Mark read</button>
            <button
              @click="remove(n.id)"
              class="text-red-600 hover:text-red-800 text-sm"
            >Delete</button>
          </div>
        </li>
      </ul>

      <!-- Pagination -->
      <div v-if="store.pagination && store.pagination.last_page > 1" class="mt-4 flex items-center justify-between">
        <button
          class="px-3 py-1.5 rounded border bg-white text-gray-700 hover:bg-gray-50 disabled:opacity-50"
          :disabled="page <= 1"
          @click="changePage(page - 1)"
        >Previous</button>
        <div class="text-sm text-gray-600">Page {{ page }} of {{ store.pagination.last_page }}</div>
        <button
          class="px-3 py-1.5 rounded border bg-white text-gray-700 hover:bg-gray-50 disabled:opacity-50"
          :disabled="page >= store.pagination.last_page"
          @click="changePage(page + 1)"
        >Next</button>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue';
import { useNotificationsStore } from '../stores/notifications';
import axios from 'axios';

const store = useNotificationsStore();
const activeTab = ref('all');
const searchQuery = ref('');
const selectedIds = ref([]);
const bulkWorking = ref(false);
const page = ref(1);

const allCount = computed(() => store.pagination?.total ?? store.items.length);
const unreadCount = computed(() => store.items.filter(n => !n.read_at).length);
const readCount = computed(() => store.items.filter(n => !!n.read_at).length);

const filteredItems = computed(() => {
  const q = searchQuery.value.trim().toLowerCase();
  let list = store.items;
  if (activeTab.value === 'unread') list = list.filter(n => !n.read_at);
  if (activeTab.value === 'read') list = list.filter(n => !!n.read_at);
  if (q) {
    list = list.filter(n => {
      const title = (n.data && n.data.title) ? String(n.data.title).toLowerCase() : '';
      const message = (n.data && n.data.message) ? String(n.data.message).toLowerCase() : '';
      const type = n.type ? String(n.type).toLowerCase() : '';
      return title.includes(q) || message.includes(q) || type.includes(q);
    });
  }
  return list;
});

// Local pagination for filtered list (uses server pagination for fetch)
const perPage = computed(() => store.pagination?.per_page ?? 20);
const pagedItems = computed(() => {
  const start = (page.value - 1) * perPage.value;
  return filteredItems.value.slice(start, start + perPage.value);
});
const isAllSelected = computed(() => {
  const idsOnPage = pagedItems.value.map(n => n.id);
  return idsOnPage.length > 0 && idsOnPage.every(id => selectedIds.value.includes(id));
});

onMounted(async () => {
  await store.fetchUnreadCount();
  await store.fetch();
});

const mark = async (id) => {
  await store.markAsRead(id);
};

const markAll = async () => {
  await store.markAllAsRead();
};

const remove = async (id) => {
  try {
    await axios.delete(`/api/notifications/${id}`);
    store.items = store.items.filter(n => n.id !== id);
    selectedIds.value = selectedIds.value.filter(x => x !== id);
  } catch (e) {
    console.error('Failed to delete notification', e);
  }
};

const formatDate = (str) => {
  try {
    const d = new Date(str);
    return d.toLocaleString();
  } catch {
    return str;
  }
};

const toggleSelected = (id) => {
  const idx = selectedIds.value.indexOf(id);
  if (idx >= 0) selectedIds.value.splice(idx, 1);
  else selectedIds.value.push(id);
};

const toggleSelectAll = () => {
  const idsOnPage = pagedItems.value.map(n => n.id);
  if (isAllSelected.value) {
    selectedIds.value = selectedIds.value.filter(id => !idsOnPage.includes(id));
  } else {
    const set = new Set(selectedIds.value.concat(idsOnPage));
    selectedIds.value = Array.from(set);
  }
};

const clearSelection = () => {
  selectedIds.value = [];
};

const bulkMarkRead = async () => {
  if (selectedIds.value.length === 0) return;
  bulkWorking.value = true;
  try {
    const idsToMark = store.items.filter(n => selectedIds.value.includes(n.id) && !n.read_at).map(n => n.id);
    for (const id of idsToMark) {
      await store.markAsRead(id);
    }
  } finally {
    bulkWorking.value = false;
    clearSelection();
  }
};

const bulkDelete = async () => {
  if (selectedIds.value.length === 0) return;
  bulkWorking.value = true;
  try {
    await Promise.all(selectedIds.value.map(id => axios.delete(`/api/notifications/${id}`)));
    store.items = store.items.filter(n => !selectedIds.value.includes(n.id));
  } catch (e) {
    console.error('Failed to bulk delete notifications', e);
  } finally {
    bulkWorking.value = false;
    clearSelection();
  }
};

const changePage = async (newPage) => {
  if (newPage < 1) return;
  page.value = newPage;
  window.scrollTo({ top: 0, behavior: 'smooth' });
  // Also fetch the corresponding server page to keep items fresh
  await store.fetch(newPage, perPage.value);
};

const handleSlashFocus = (e) => {
  // If user presses '/', focus the input (handled globally by showing hint)
  if (e.key === '/') {
    e.preventDefault();
    const el = e.target;
    if (el && typeof el.focus === 'function') el.focus();
  }
};
</script>
