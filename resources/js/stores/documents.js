import { defineStore } from "pinia";
import axios from "axios";

export const useDocumentStore = defineStore("documents", {
    state: () => ({
        documents: [],
        outgoing: [],
        incoming: [],
        received: [],
        hold: [],
        completed: [],
        currentDocument: null,
        loading: false,
        pagination: null,
        filters: {
            status: "",
            type: "",
            priority: "",
            search: "",
        },
    }),

    getters: {
        filteredDocuments: (state) => {
            return state.documents.filter((doc) => {
                if (state.filters.status && doc.status !== state.filters.status)
                    return false;
                if (state.filters.type && doc.type !== state.filters.type)
                    return false;
                if (
                    state.filters.priority &&
                    doc.priority !== state.filters.priority
                )
                    return false;
                if (state.filters.search) {
                    const search = state.filters.search.toLowerCase();
                    return (
                        doc.title.toLowerCase().includes(search) ||
                        doc.document_number.toLowerCase().includes(search)
                    );
                }
                return true;
            });
        },
    },

    actions: {
        async fetchDocuments(params = {}) {
            this.loading = true;
            try {
                const response = await axios.get("/api/documents", { params });
                const resData = response.data;

                console.log("📥 Raw response from API:", resData);

                if (
                    resData &&
                    typeof resData === "object" &&
                    Array.isArray(resData.data)
                ) {
                    // ApiResponse::paginated or Laravel paginator
                    this.documents = resData.data;

                    // Prefer explicit pagination object from ApiResponse
                    if (resData.pagination) {
                        this.pagination = {
                            current_page: resData.pagination.current_page,
                            last_page: resData.pagination.last_page,
                            per_page: resData.pagination.per_page,
                            total: resData.pagination.total,
                            from: resData.pagination.from,
                            to: resData.pagination.to,
                        };
                    } else {
                        // Fallback if backend returns Laravel paginator directly
                        this.pagination = {
                            current_page: resData.current_page,
                            last_page: resData.last_page,
                            per_page: resData.per_page,
                            total: resData.total,
                            from: resData.from,
                            to: resData.to,
                        };
                    }
                } else if (Array.isArray(resData)) {
                    // Simple array response
                    this.documents = resData;
                    this.pagination = null;
                } else {
                    console.warn("⚠️ Unexpected response format:", resData);
                    this.documents = [];
                    this.pagination = null;
                }

                // ✅ Log plain array (avoid Proxy view)
                console.log(
                    "✅ Documents fetched:",
                    JSON.parse(JSON.stringify(this.documents))
                );
            } catch (error) {
                console.error("❌ Error fetching documents:", error);
                this.documents = [];
                this.pagination = null;
            } finally {
                this.loading = false;
            }
        },

        async fetchDocument(id) {
            this.loading = true;
            try {
                console.log("📄 Fetching document with ID:", id);
                console.log(
                    "🔑 Current auth header:",
                    axios.defaults.headers.common["Authorization"]
                );

                const response = await axios.get(`/api/documents/${id}`);
                const resData = response.data;
                const doc = resData && typeof resData === "object" && "data" in resData
                    ? resData.data
                    : resData;
                this.currentDocument = doc;
                console.log("✅ Document fetched successfully:", doc);
                return doc;
            } catch (error) {
                console.error("❌ Error fetching document:", error);
                console.error("❌ Error response:", error.response?.data);
                console.error("❌ Error status:", error.response?.status);
                throw error;
            } finally {
                this.loading = false;
            }
        },

        async createDocument(documentData) {
            try {
                const response = await axios.post(
                    "/api/documents",
                    documentData
                );
                const newDoc = response.data.document || response.data;
                this.documents.unshift(newDoc);
                return newDoc;
            } catch (error) {
                console.error("❌ Error creating document:", error);
                throw error;
            }
        },

        async updateDocument(id, documentData) {
            try {
                const response = await axios.put(
                    `/api/documents/${id}`,
                    documentData
                );
                const updatedDoc = response.data;
                const index = this.documents.findIndex((doc) => doc.id === id);
                if (index !== -1) this.documents[index] = updatedDoc;
                if (this.currentDocument?.id === id)
                    this.currentDocument = updatedDoc;
                return updatedDoc;
            } catch (error) {
                console.error("❌ Error updating document:", error);
                throw error;
            }
        },

        async deleteDocument(id) {
            try {
                await axios.delete(`/api/documents/${id}`);
                this.documents = this.documents.filter((doc) => doc.id !== id);
                if (this.currentDocument?.id === id)
                    this.currentDocument = null;
            } catch (error) {
                console.error("❌ Error deleting document:", error);
                throw error;
            }
        },

        async uploadAttachment(documentId, file) {
            try {
                const formData = new FormData();
                formData.append("file", file);
                const response = await axios.post(
                    `/api/documents/${documentId}/attachments`,
                    formData,
                    { headers: { "Content-Type": "multipart/form-data" } }
                );
                const resData = response.data;
                const attachment = resData && typeof resData === "object" && "data" in resData
                    ? resData.data
                    : resData;
                if (this.currentDocument?.id === documentId) {
                    this.currentDocument.attachments ||= [];
                    this.currentDocument.attachments.push(attachment);
                }
                return attachment;
            } catch (error) {
                console.error("❌ Error uploading attachment:", error);
                throw error;
            }
        },

        async addComment(documentId, commentData) {
            try {
                const response = await axios.post(
                    `/api/documents/${documentId}/comments`,
                    commentData
                );
                if (this.currentDocument?.id === documentId) {
                    this.currentDocument.comments ||= [];
                    this.currentDocument.comments.push(response.data);
                }
                return response.data;
            } catch (error) {
                console.error("❌ Error adding comment:", error);
                throw error;
            }
        },

        async generateQRCode(documentId) {
            try {
                const response = await axios.post(
                    `/api/documents/${documentId}/qr`
                );
                if (this.currentDocument?.id === documentId)
                    this.currentDocument.qr_code = response.data;
                return response.data;
            } catch (error) {
                console.error("❌ Error generating QR code:", error);
                throw error;
            }
        },

        async bulkAssignDocuments(documentIds, departmentId, notes = null) {
            try {
                const response = await axios.post(
                    "/api/documents/bulk-update-department",
                    {
                        document_ids: documentIds,
                        department_id: departmentId,
                        notes: notes,
                    }
                );

                // Update local documents if they exist in the store
                documentIds.forEach((docId) => {
                    const index = this.documents.findIndex(
                        (doc) => doc.id === docId
                    );
                    if (index !== -1) {
                        this.documents[index].current_department_id =
                            departmentId;
                    }
                });

                return response.data;
            } catch (error) {
                console.error("❌ Error bulk assigning documents:", error);
                throw error;
            }
        },

        async bulkUpdateStatus(documentIds, status, notes = null) {
            try {
                const response = await axios.post(
                    "/api/documents/bulk-update-status",
                    {
                        document_ids: documentIds,
                        status: status,
                        notes: notes,
                    }
                );

                // Update local documents if they exist in the store
                documentIds.forEach((docId) => {
                    const index = this.documents.findIndex(
                        (doc) => doc.id === docId
                    );
                    if (index !== -1) {
                        this.documents[index].status = status;
                    }
                });

                return response.data;
            } catch (error) {
                console.error("❌ Error bulk updating status:", error);
                throw error;
            }
        },

        async bulkForward(
            documentIds,
            toOfficeId,
            remarks = null,
            assignedUserId = null
        ) {
            try {
                const payload = {
                    document_ids: documentIds,
                    to_office_id: toOfficeId,
                    remarks: remarks,
                };
                if (assignedUserId) {
                    payload.assigned_user_id = assignedUserId;
                }

                const response = await axios.post(
                    "/api/documents/bulk-forward",
                    payload
                );

                // Update local documents in the store
                documentIds.forEach((docId) => {
                    const index = this.documents.findIndex(
                        (doc) => doc.id === docId
                    );
                    if (index !== -1) {
                        this.documents[index].current_department_id =
                            toOfficeId;
                        this.documents[index].status = "submitted";
                        if (assignedUserId) {
                            this.documents[index].assigned_to = assignedUserId;
                        }
                    }
                });

                return response.data;
            } catch (error) {
                console.error("❌ Error bulk forwarding documents:", error);
                throw error;
            }
        },

        async fetchUsers() {
            try {
                const response = await axios.get("/api/users");
                return response.data.data || response.data;
            } catch (error) {
                console.error("❌ Error fetching users:", error);
                throw error;
            }
        },

        async fetchDepartments() {
            try {
                const response = await axios.get("/api/departments");
                return response.data.data || response.data;
            } catch (error) {
                console.error("❌ Error fetching departments:", error);
                throw error;
            }
        },

        setFilters(filters) {
            this.filters = { ...this.filters, ...filters };
        },

        clearFilters() {
            this.filters = {
                status: "",
                type: "",
                priority: "",
                search: "",
            };
        },

        // Workflow-specific methods
        async fetchOutgoing(params = {}) {
            this.loading = true;
            try {
                const response = await axios.get("/api/documents/outgoing", {
                    params,
                });
                this.outgoing = response.data.data || response.data;
            } catch (error) {
                console.error("❌ Error fetching outgoing documents:", error);
            } finally {
                this.loading = false;
            }
        },

        async fetchIncoming(params = {}) {
            this.loading = true;
            try {
                // Clean out empty params and remove UI-only keys
                const cleanParams = (obj) =>
                    Object.fromEntries(
                        Object.entries(obj || {}).filter(
                            ([, v]) => v !== "" && v !== null && v !== undefined
                        )
                    );

                const safeParams = cleanParams(params);
                // date_range is a UI helper; backend expects date_from/date_to
                delete safeParams.date_range;

                // Resolve absolute URL to avoid dev-server routing 404s
                const requestUrl = new URL(
                    "/api/documents/incoming",
                    axios.defaults.baseURL || "http://127.0.0.1:8000"
                ).toString();

                // Debug logging for diagnosis
                console.log("🔧 Incoming request URL:", requestUrl);
                console.log("🔧 Axios baseURL:", axios.defaults.baseURL);
                console.log(
                    "🔧 Authorization header:",
                    axios.defaults.headers.common["Authorization"] || "<none>"
                );
                console.log("🔧 Params:", JSON.parse(JSON.stringify(safeParams)));

                const response = await axios.get(requestUrl, {
                    params: safeParams,
                });

                const resData = response.data;

                // Handle ApiResponse::paginated wrapper
                if (
                    resData &&
                    typeof resData === "object" &&
                    Array.isArray(resData.data)
                ) {
                    this.incoming = resData.data;
                    // Prefer explicit pagination object from ApiResponse
                    if (resData.pagination) {
                        this.pagination = {
                            current_page: resData.pagination.current_page,
                            last_page: resData.pagination.last_page,
                            per_page: resData.pagination.per_page,
                            total: resData.pagination.total,
                            from: resData.pagination.from,
                            to: resData.pagination.to,
                        };
                    } else {
                        // Fallback if backend returns Laravel paginator directly
                        this.pagination = {
                            current_page: resData.current_page,
                            last_page: resData.last_page,
                            per_page: resData.per_page,
                            total: resData.total,
                            from: resData.from,
                            to: resData.to,
                        };
                    }
                } else if (Array.isArray(resData)) {
                    // Simple array response (no pagination)
                    this.incoming = resData;
                    this.pagination = null;
                } else {
                    console.warn(
                        "⚠️ Unexpected incoming response format:",
                        resData
                    );
                    this.incoming = [];
                    this.pagination = null;
                }
            } catch (error) {
                console.error("❌ Error fetching incoming documents:", error);
                console.error("❌ Incoming error status:", error.response?.status);
                console.error(
                    "❌ Incoming error URL:",
                    error.response?.config?.url || "<unknown>"
                );
                console.error(
                    "❌ Incoming error data:",
                    error.response?.data || "<no response body>"
                );
                // Ensure predictable state on error
                this.incoming = [];
                this.pagination = null;
            } finally {
                this.loading = false;
            }
        },

        async fetchReceived(params = {}) {
            this.loading = true;
            try {
                const response = await axios.get("/api/documents/received", {
                    params,
                });
                this.received = response.data.data || response.data;
            } catch (error) {
                console.error("❌ Error fetching received documents:", error);
            } finally {
                this.loading = false;
            }
        },

        async fetchHold(params = {}) {
            this.loading = true;
            try {
                const response = await axios.get("/api/documents/hold", {
                    params,
                });
                this.hold = response.data.data || response.data;
            } catch (error) {
                console.error("❌ Error fetching hold documents:", error);
            } finally {
                this.loading = false;
            }
        },

        async fetchCompleted(params = {}) {
            this.loading = true;
            try {
                const response = await axios.get("/api/documents/completed", {
                    params,
                });
                this.completed = response.data.data || response.data;
            } catch (error) {
                console.error("❌ Error fetching completed documents:", error);
            } finally {
                this.loading = false;
            }
        },
    },
});
