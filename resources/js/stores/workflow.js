import { defineStore } from "pinia";
import axios from "axios";

export const useWorkflowStore = defineStore("workflow", {
    state: () => ({
        loading: false,
    }),

    actions: {
        async forwardDocument(documentId, toOfficeId, remarks, assignedUserId = null) {
            this.loading = true;
            try {
                const payload = {
                    to_office_id: toOfficeId,
                    remarks,
                };
                if (assignedUserId) {
                    payload.assigned_user_id = assignedUserId;
                }
                const response = await axios.post(`/api/workflow/documents/${documentId}/forward`, payload);
                return response.data;
            } catch (error) {
                console.error("❌ Error forwarding document:", error);
                throw error;
            } finally {
                this.loading = false;
            }
        },

        async receiveDocument(documentId, notes) {
            this.loading = true;
            try {
                const response = await axios.post(`/api/workflow/documents/${documentId}/receive`, {
                    notes
                });
                return response.data;
            } catch (error) {
                console.error("❌ Error receiving document:", error);
                throw error;
            } finally {
                this.loading = false;
            }
        },

        async rejectDocument(documentId, reason) {
            this.loading = true;
            try {
                const response = await axios.post(`/api/workflow/documents/${documentId}/reject`, {
                    reason
                });
                return response.data;
            } catch (error) {
                console.error("❌ Error rejecting document:", error);
                throw error;
            } finally {
                this.loading = false;
            }
        },

        async signDocument(documentId, remarks) {
            this.loading = true;
            try {
                const response = await axios.post(`/api/workflow/documents/${documentId}/sign`, {
                    remarks
                });
                return response.data;
            } catch (error) {
                console.error("❌ Error signing document:", error);
                throw error;
            } finally {
                this.loading = false;
            }
        },

        async holdDocument(documentId, reason, remarks) {
            this.loading = true;
            try {
                const response = await axios.post(`/api/workflow/documents/${documentId}/hold`, {
                    reason,
                    remarks
                });
                return response.data;
            } catch (error) {
                console.error("❌ Error holding document:", error);
                throw error;
            } finally {
                this.loading = false;
            }
        },

        async resumeDocument(documentId, remarks) {
            this.loading = true;
            try {
                const response = await axios.post(`/api/workflow/documents/${documentId}/resume`, {
                    remarks
                });
                return response.data;
            } catch (error) {
                console.error("❌ Error resuming document:", error);
                throw error;
            } finally {
                this.loading = false;
            }
        },

        async completeDocument(documentId, remarks) {
            this.loading = true;
            try {
                const response = await axios.post(`/api/workflow/documents/${documentId}/complete`, {
                    remarks
                });
                return response.data;
            } catch (error) {
                console.error("❌ Error completing document:", error);
                throw error;
            } finally {
                this.loading = false;
            }
        },

        async getWorkflowStatus(documentId) {
            try {
                const response = await axios.get(`/api/workflow/documents/${documentId}/status`);
                return response.data;
            } catch (error) {
                console.error("❌ Error getting workflow status:", error);
                throw error;
            }
        },

        async trackDocument(identifier) {
            try {
                const response = await axios.get(`/api/track/${identifier}`);
                return response.data;
            } catch (error) {
                console.error("❌ Error tracking document:", error);
                throw error;
            }
        },

        async scanQRCode(qrData) {
            try {
                const response = await axios.post("/api/track/scan", {
                    qr_data: qrData
                });
                return response.data;
            } catch (error) {
                console.error("❌ Error scanning QR code:", error);
                throw error;
            }
        }
    }
});