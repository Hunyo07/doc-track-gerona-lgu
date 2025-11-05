<template>
  <span :class="statusClasses">
    {{ statusLabel }}
  </span>
</template>

<script>
export default {
  name: 'StatusBadge',
  props: {
    status: {
      type: String,
      required: true
    }
  },
  computed: {
    statusClasses() {
      const baseClasses = 'px-2 py-1 rounded-full text-xs font-medium';
      const colorClasses = {
        draft: 'bg-gray-100 text-gray-800',
        submitted: 'bg-blue-100 text-blue-800',
        received: 'bg-indigo-100 text-indigo-800',
        approved: 'bg-green-100 text-green-800',
        rejected: 'bg-red-100 text-red-800',
        on_hold: 'bg-orange-100 text-orange-800',
        completed: 'bg-emerald-100 text-emerald-800'
      };
      return `${baseClasses} ${colorClasses[this.status] || colorClasses.draft}`;
    },
    statusLabel() {
      const labels = {
        draft: 'Draft',
        submitted: 'Submitted',
        received: 'Received',
        approved: 'Approved',
        rejected: 'Rejected',
        on_hold: 'On Hold',
        completed: 'Completed'
      };
      return labels[this.status] || this.status;
    }
  }
}
</script>