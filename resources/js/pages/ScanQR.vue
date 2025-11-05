<template>
  <div class="p-4 max-w-4xl mx-auto">
    <h1 class="text-xl font-semibold text-gray-900">Scan or Enter Barcode</h1>
    <p class="text-sm text-gray-500">Search by barcode, then Receive or Reject with remarks.</p>

    <div class="mt-4 flex gap-2">
      <input v-model="barcode" @keyup.enter="lookup" placeholder="Scan or type barcode" class="flex-1 border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500" />
      <button @click="lookup" class="px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700">Search</button>
    </div>

    <div v-if="error" class="mt-3 text-sm text-red-600">{{ error }}</div>

    <div v-if="doc" class="mt-6 bg-white border rounded-lg shadow-sm p-4">
      <div class="flex items-start justify-between">
        <div>
          <div class="text-lg font-semibold text-gray-900">{{ doc.title }}</div>
          <div class="text-sm text-gray-500">#{{ doc.document_number }} · {{ doc.department?.name }}</div>
        </div>
        <div>
          <span :class="statusClass(doc.status)" class="px-2 py-1 rounded-full text-xs font-medium">{{ doc.status }}</span>
        </div>
      </div>
      <div class="mt-3 text-sm text-gray-700" v-if="doc.description">{{ doc.description }}</div>

      <div class="mt-4 flex gap-2">
        <input v-model="remarks" placeholder="Remarks (optional)" class="flex-1 border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500" />
        <button @click="receive" class="px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700">Receive</button>
        <button @click="reject" class="px-4 py-2 bg-red-600 text-white rounded-md hover:bg-red-700">Reject</button>
      </div>

      <div class="mt-6">
        <h2 class="text-sm font-medium text-gray-900 mb-2">History</h2>
        <div v-if="doc.logs?.length" class="space-y-2">
          <div v-for="log in doc.logs" :key="log.id" class="flex items-center justify-between text-sm border rounded p-2">
            <div>
              <span class="font-medium">{{ log.user?.name || 'System' }}</span>
              <span class="text-gray-600"> - {{ log.action || 'event' }}</span>
              <span class="text-gray-500" v-if="log.description"> · {{ log.description }}</span>
            </div>
            <div class="text-gray-500">{{ formatDate(log.created_at) }}</div>
          </div>
        </div>
        <div v-else class="text-xs text-gray-500">No history yet</div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue';
import axios from 'axios';
import { useToast } from 'vue-toastification';

const toast = useToast();
const barcode = ref('');
const doc = ref(null);
const error = ref('');
const remarks = ref('');

const lookup = async () => {
  error.value = '';
  doc.value = null;
  if (!barcode.value) return;
  try {
    const { data } = await axios.post('/api/documents/find-by-barcode', { barcode: barcode.value });
    doc.value = data;
  } catch (e) {
    error.value = e.response?.data?.message || 'Document not found';
  }
};

const receive = async () => {
  if (!doc.value) return;
  try {
    const { data } = await axios.post(`/api/documents/${doc.value.id}/receive`, { notes: remarks.value });
    doc.value = data;
    toast.success('Document received');
  } catch (e) {
    toast.error(e.response?.data?.message || 'Failed to receive');
  }
};

const reject = async () => {
  if (!doc.value) return;
  if (!remarks.value) { toast.error('Please add reason'); return; }
  try {
    const { data } = await axios.post(`/api/documents/${doc.value.id}/reject`, { reason: remarks.value });
    doc.value = data;
    toast.success('Document rejected');
  } catch (e) {
    toast.error(e.response?.data?.message || 'Failed to reject');
  }
};

const statusClass = (s) => {
  const m = {
    draft: 'bg-gray-100 text-gray-800',
    submitted: 'bg-blue-100 text-blue-800',
    received: 'bg-green-100 text-green-800',
    under_review: 'bg-yellow-100 text-yellow-800',
    for_approval: 'bg-indigo-100 text-indigo-800',
    approved: 'bg-emerald-100 text-emerald-800',
    rejected: 'bg-red-100 text-red-800',
    awaiting_payment: 'bg-orange-100 text-orange-800',
    paid: 'bg-teal-100 text-teal-800',
    completed: 'bg-purple-100 text-purple-800',
    archived: 'bg-slate-100 text-slate-800',
  };
  return m[s] || 'bg-gray-100 text-gray-800';
};

const formatDate = (d) => new Date(d).toLocaleString();
</script>
