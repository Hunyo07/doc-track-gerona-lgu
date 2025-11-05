<template>
    <div class="bg-white p-6 rounded-lg shadow">
        <h3 class="text-lg font-semibold mb-4">Generate QR Code</h3>

        <div v-if="!qrGenerated" class="space-y-4">
            <div>
                <label class="block text-sm font-medium mb-2"
                    >Document Type</label
                >
                <select
                    v-model="documentTypeId"
                    class="w-full border rounded px-3 py-2"
                >
                    <option value="">Select document type</option>
                    <option
                        v-for="type in documentTypes"
                        :key="type.id"
                        :value="type.id"
                    >
                        {{ type.name }}
                    </option>
                </select>
            </div>

            <button
                @click="generateQR"
                :disabled="!documentTypeId || loading"
                class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700 disabled:opacity-50"
            >
                {{ loading ? "Generating..." : "Generate QR Code" }}
            </button>
        </div>

        <div v-if="qrGenerated" class="text-center space-y-4">
            <div class="bg-gray-50 p-4 rounded">
                <p class="font-semibold mb-2">
                    Document Number: {{ documentNumber }}
                </p>
                <img :src="qrCodeUrl" alt="QR Code" class="mx-auto mb-4" />
                <div class="flex gap-2 justify-center">
                    <!-- <button
                        @click="downloadQR"
                        class="bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700"
                    >
                        Download QR
                    </button> -->
                    <button
                        @click="downloadQRJpeg"
                        class="bg-green-700 text-white px-4 py-2 rounded hover:bg-green-800"
                    >
                        Download QR to Print
                    </button>
                    <!-- <button
                        @click="printQR"
                        class="bg-purple-600 text-white px-4 py-2 rounded hover:bg-purple-700"
                    >
                        Print QR
                    </button> -->
                </div>
            </div>

            <div class="bg-yellow-50 border border-yellow-200 p-4 rounded">
                <p class="text-sm text-yellow-800">
                    <strong>Next Steps:</strong>
                    <br />1. Print the QR code above <br />2. Attach it to your
                    physical document <br />3. Upload the document using the
                    form below
                </p>
            </div>

            <button
                @click="proceedToUpload"
                class="bg-blue-600 text-white px-6 py-2 rounded hover:bg-blue-700"
            >
                Proceed to Document Upload
            </button>
        </div>
    </div>
</template>

<script>
import { ref, onMounted } from "vue";
import { useDocumentStore } from "../stores/documents";

export default {
    name: "QRGenerator",
    emits: ["qr-generated"],
    setup(props, { emit }) {
        const documentStore = useDocumentStore();

        const documentTypes = ref([]);
        const documentTypeId = ref("");
        const loading = ref(false);
        const qrGenerated = ref(false);
        const documentNumber = ref("");
        const qrCodeUrl = ref("");
        const qrCodePath = ref("");

        const generateQR = async () => {
            if (!documentTypeId.value) return;

            loading.value = true;
            try {
                const response = await fetch("/api/documents/generate-qr", {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/json",
                        Authorization: `Bearer ${localStorage.getItem(
                            "token"
                        )}`,
                    },
                    body: JSON.stringify({
                        document_type_id: documentTypeId.value,
                    }),
                });

                const result = await response.json();
                if (result.success) {
                    documentNumber.value = result.data.document_number;
                    qrCodeUrl.value = result.data.qr_code_url;
                    qrCodePath.value = result.data.qr_code_path;
                    qrGenerated.value = true;
                }
            } catch (error) {
                console.error("Error generating QR:", error);
            } finally {
                loading.value = false;
            }
        };

        const downloadQR = () => {
            const link = document.createElement("a");
            link.href = qrCodeUrl.value;
            link.download = `QR_${documentNumber.value}.svg`;
            link.click();
        };

        const downloadQRJpeg = async () => {
            const svgUrl = qrCodeUrl.value;
            try {
                const res = await fetch(svgUrl, { cache: "no-cache" });
                const svgText = await res.text();
                const blob = new Blob([svgText], {
                    type: "image/svg+xml;charset=utf-8",
                });
                const objUrl = URL.createObjectURL(blob);
                const img = new Image();
                img.onload = () => {
                    const size = 1024; // export size in pixels
                    const canvas = document.createElement("canvas");
                    canvas.width = size;
                    canvas.height = size;
                    const ctx = canvas.getContext("2d");
                    // Fill white background for JPEG
                    ctx.fillStyle = "#ffffff";
                    ctx.fillRect(0, 0, size, size);
                    ctx.drawImage(img, 0, 0, size, size);
                    URL.revokeObjectURL(objUrl);
                    const dataUrl = canvas.toDataURL("image/jpeg", 0.92);
                    const a = document.createElement("a");
                    a.href = dataUrl;
                    a.download = `QR_${documentNumber.value}.jpg`;
                    document.body.appendChild(a);
                    a.click();
                    document.body.removeChild(a);
                };
                img.onerror = () => {
                    URL.revokeObjectURL(objUrl);
                    alert("Failed to convert QR to JPEG");
                };
                img.src = objUrl;
            } catch (e) {
                alert("Failed to download as JPEG");
            }
        };

        const printQR = () => {
            const w = window.open("", "_blank");
            if (!w) return;
            const url = qrCodeUrl.value;
            w.document.write(
                `<!DOCTYPE html><html><head><meta charset="utf-8"><title>QR</title><style>@page{margin:0}html,body{margin:0;padding:0}img{display:block;margin:0 auto;max-width:100vw;max-height:100vh}</style></head><body><img src="${url}" alt="QR" onload="window.print();window.onafterprint=window.close();"/></body></html>`
            );
            w.document.close();
        };

        const proceedToUpload = () => {
            emit("qr-generated", {
                documentNumber: documentNumber.value,
                qrCodePath: qrCodePath.value,
                documentTypeId: documentTypeId.value,
            });
        };

        onMounted(async () => {
            try {
                const response = await fetch("/api/document-types");
                const result = await response.json();
                documentTypes.value = result;
            } catch (error) {
                console.error("Error loading document types:", error);
            }
        });

        return {
            documentTypes,
            documentTypeId,
            loading,
            qrGenerated,
            documentNumber,
            qrCodeUrl,
            generateQR,
            downloadQR,
            downloadQRJpeg,
            printQR,
            proceedToUpload,
        };
    },
};
</script>
<!-- </template> -->
<!-- <parameter name="explanation">Creating QRGenerator component for pre-generating QR codes before document upload -->
