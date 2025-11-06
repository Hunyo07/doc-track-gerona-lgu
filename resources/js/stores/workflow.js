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

        async signDocument(documentId, payload) {
            this.loading = true;
            try {
                const file = payload?.file ?? payload; // support both object payload and raw File
                const extraRemarks = (arguments && arguments.length >= 3) ? arguments[2] : undefined;
                const remarks = payload?.remarks ?? extraRemarks ?? "";
                const demoMode = payload?.demo_mode ?? false;

                if (!file) {
                    throw new Error("Signed PDF file is required for signing.");
                }

                const formData = new FormData();
                formData.append("file", file);
                if (remarks) formData.append("remarks", remarks);
                // Pass demo mode flag to server to bypass verification
                formData.append("demo_mode", demoMode ? "true" : "false");

                const response = await axios.post(
                    `/api/workflow/documents/${documentId}/sign`,
                    formData,
                    { headers: { "Content-Type": "multipart/form-data" } }
                );
                return response.data;
            } catch (error) {
                console.error("❌ Error signing document:", error);
                // Surface server validation messages when available
                const resp = error?.response?.data || {};
                const validationMsg = resp?.errors?.signature?.[0] 
                    || resp?.errors?.file?.[0]
                    || resp?.message;
                const message = validationMsg || error.message || "Error signing document";
                const err = new Error(message);
                err.response = error?.response;
                throw err;
            } finally {
                this.loading = false;
            }
        },

        async approveDocument(documentId, remarks) {
            this.loading = true;
            try {
                const response = await axios.post(`/api/workflow/documents/${documentId}/approve`, {
                    remarks
                });
                return response.data;
            } catch (error) {
                console.error("❌ Error approving document:", error);
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