<template>
    <div class="p-4 max-w-5xl mx-auto">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-xl font-semibold text-gray-900">
                    QR Code Generator
                </h1>
                <p class="text-sm text-gray-500">
                    Generate and print QR codes for document numbers.
                </p>
            </div>
        </div>

        <div class="mt-6 grid grid-cols-1 md:grid-cols-3 gap-4">
            <div class="md:col-span-1">
                <div
                    class="rounded-lg border border-gray-200 bg-white p-4 shadow-sm"
                >
                    <h2 class="text-sm font-medium text-gray-900 mb-3">
                        Generate
                    </h2>
                    <form @submit.prevent="generate">
                        <div class="mb-3">
                            <label class="block text-sm text-gray-700 mb-1"
                                >Document Numbers</label
                            >
                            <textarea
                                v-model="input"
                                rows="6"
                                placeholder="One per line"
                                ref="inputArea"
                                class="w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500"
                            ></textarea>
                        </div>
                        <!-- Validation summary -->
                        <div class="mb-3 text-xs text-gray-600" v-if="stats.total > 0">
                            <span class="mr-3">Valid: {{ stats.valid }}</span>
                            <span class="mr-3">Invalid: {{ stats.invalid }}</span>
                            <span>Duplicates: {{ stats.duplicates }}</span>
                        </div>
                        <!-- Problems panel -->
                        <div v-if="stats.invalid > 0 || stats.duplicates > 0" class="mb-3 border border-yellow-200 bg-yellow-50 rounded-md p-3">
                            <div class="text-xs font-medium text-yellow-800 mb-2">Problems</div>
                            <div v-if="invalidLines.length" class="mb-2">
                                <div class="text-[11px] text-yellow-900 mb-1">Invalid lines ({{ invalidLines.length }})</div>
                                <ul class="space-y-1">
                                    <li v-for="p in invalidLines" :key="'inv-'+p.index" class="flex items-center justify-between">
                                        <span class="text-[11px] text-gray-800 truncate">#{{ p.index + 1 }} — {{ p.value }}</span>
                                        <div class="flex gap-2">
                                            <button type="button" @click="focusLine(p.index)" class="px-2 py-1 text-[11px] bg-gray-100 rounded hover:bg-gray-200">Jump</button>
                                            <button type="button" @click="removeLineAt(p.index)" class="px-2 py-1 text-[11px] bg-red-100 text-red-700 rounded hover:bg-red-200">Delete</button>
                                        </div>
                                    </li>
                                </ul>
                            </div>
                            <div v-if="duplicateLines.length">
                                <div class="text-[11px] text-yellow-900 mb-1">Duplicate lines ({{ duplicateLines.length }})</div>
                                <ul class="space-y-1">
                                    <li v-for="p in duplicateLines" :key="'dup-'+p.index" class="flex items-center justify-between">
                                        <span class="text-[11px] text-gray-800 truncate">#{{ p.index + 1 }} — {{ p.value }}</span>
                                        <div class="flex gap-2">
                                            <button type="button" @click="focusLine(p.index)" class="px-2 py-1 text-[11px] bg-gray-100 rounded hover:bg-gray-200">Jump</button>
                                            <button type="button" @click="removeLineAt(p.index)" class="px-2 py-1 text-[11px] bg-red-100 text-red-700 rounded hover:bg-red-200">Delete</button>
                                        </div>
                                    </li>
                                </ul>
                            </div>
                        </div>
                        <!-- Options -->
                        <div class="mb-3 grid grid-cols-2 gap-3">
                            <div>
                                <label class="block text-xs text-gray-700 mb-1">QR size (px)</label>
                                <input type="range" min="120" max="320" step="20" v-model.number="settings.size" />
                                <div class="text-xs text-gray-500 mt-1">{{ settings.size }} px</div>
                            </div>
                            <div class="flex items-center mt-6">
                                <input id="showLabel" type="checkbox" v-model="settings.showLabel" class="mr-2" />
                                <label for="showLabel" class="text-xs text-gray-700">Show label under QR</label>
                            </div>
                        </div>
                        <!-- Quick batch generator -->
                        <div class="mb-3 grid grid-cols-4 gap-3">
                            <div>
                                <label class="block text-xs text-gray-700 mb-1">Prefix</label>
                                <input v-model="quick.prefix" type="text" class="w-full border-gray-300 rounded-md text-sm px-2 py-1" placeholder="e.g., GSO-2025-" />
                            </div>
                            <div>
                                <label class="block text-xs text-gray-700 mb-1">Start</label>
                                <input v-model.number="quick.start" type="number" min="1" class="w-full border-gray-300 rounded-md text-sm px-2 py-1" />
                            </div>
                            <div>
                                <label class="block text-xs text-gray-700 mb-1">Count</label>
                                <input v-model.number="quick.count" type="number" min="1" class="w-full border-gray-300 rounded-md text-sm px-2 py-1" />
                            </div>
                            <div>
                                <label class="block text-xs text-gray-700 mb-1">Pad</label>
                                <input v-model.number="quick.pad" type="number" min="1" max="6" class="w-full border-gray-300 rounded-md text-sm px-2 py-1" />
                            </div>
                            <div class="col-span-4">
                                <button type="button" @click="addGenerated" class="px-3 py-2 text-xs bg-gray-100 rounded-md hover:bg-gray-200">Add generated to input</button>
                            </div>
                        </div>
                        <!-- Cleanup actions -->
                        <div class="mb-3 flex items-center gap-3" v-if="stats.total > 0">
                            <button type="button" @click="removeInvalids" :disabled="stats.invalid === 0" class="px-3 py-1 text-xs bg-yellow-100 text-yellow-800 rounded disabled:opacity-50">Remove invalids</button>
                            <button type="button" @click="removeDuplicates" :disabled="stats.duplicates === 0" class="px-3 py-1 text-xs bg-orange-100 text-orange-800 rounded disabled:opacity-50">Remove duplicates</button>
                        </div>
                        <div class="flex items-center justify-between mb-3">
                            <input
                                type="file"
                                ref="fileInput"
                                @change="handleImport"
                                accept=".csv,.txt"
                                class="hidden"
                            />
                            <button
                                type="button"
                                @click="$refs.fileInput.click()"
                                class="px-3 py-2 text-sm bg-gray-100 rounded-md hover:bg-gray-200"
                            >
                                Import CSV/TXT
                            </button>
                            <button
                                type="submit"
                                :disabled="loading || stats.valid === 0"
                                class="px-3 py-2 text-sm bg-indigo-600 text-white rounded-md hover:bg-indigo-700 disabled:opacity-50"
                            >
                                {{ loading ? "Generating..." : "Generate" }}
                            </button>
                        </div>
                        <div class="flex gap-2">
                            <button
                                type="button"
                                @click="printAll"
                                :disabled="items.length === 0"
                                class="px-3 py-2 text-sm bg-blue-600 text-white rounded-md disabled:opacity-50"
                            >
                                Print All
                            </button>
                            <button
                                type="button"
                                @click="toggleSelectAll"
                                :disabled="items.length === 0"
                                class="px-3 py-2 text-sm bg-blue-100 text-blue-700 rounded-md disabled:opacity-50"
                            >
                                {{ isAllSelected ? 'Unselect All' : 'Select All' }}
                            </button>
                            <button
                                type="button"
                                @click="printSelected"
                                :disabled="selectedIds.size === 0"
                                class="px-3 py-2 text-sm bg-blue-500 text-white rounded-md disabled:opacity-50"
                            >
                                Print Selected
                            </button>
                            <button
                                type="button"
                                @click="clearAll"
                                class="px-3 py-2 text-sm bg-gray-200 rounded-md"
                            >
                                Clear
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <div class="md:col-span-2">
                <div
                    class="rounded-lg border border-gray-200 bg-white p-4 shadow-sm"
                >
                    <h2 class="text-sm font-medium text-gray-900 mb-3">
                        Generated ({{ items.length }}) <span v-if="selectedIds.size">• Selected: {{ selectedIds.size }}</span>
                    </h2>
                    <div
                        v-if="items.length === 0"
                        class="text-gray-500 text-sm"
                    >
                        No QR codes yet. Enter document numbers (one per line) or import CSV/TXT. Use options to change size and labels.
                    </div>
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                        <div
                            v-for="(it, idx) in items"
                            :key="idx"
                            class="p-3 border rounded-md"
                        >
                            <div class="flex items-center justify-between mb-2">
                                <label class="inline-flex items-center text-xs text-gray-600">
                                    <input type="checkbox" :value="it.qr_image_url" v-model="selected" class="mr-2" /> Select
                                </label>
                                <span class="text-[10px] text-gray-400">#{{ idx + 1 }}</span>
                            </div>
                            <img
                                :src="it.qr_image_url"
                                alt="qr"
                                class="w-full h-auto"
                                :style="{ width: settings.size + 'px' }"
                            />
                            <div v-if="settings.showLabel" class="mt-2 text-xs text-gray-700 truncate">
                                {{ it.value }}
                            </div>
                            <div class="mt-2 flex gap-2">
                                <a
                                    :href="it.qr_image_url"
                                    download
                                    class="text-indigo-600 hover:text-indigo-800 text-xs"
                                    >Download</a
                                >
                                <button
                                    @click="printOne(it)"
                                    class="text-blue-600 hover:text-blue-800 text-xs"
                                >
                                    Print
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
 </template>

<script setup>
import { ref, computed } from "vue";
import axios from "axios";
import { useToast } from "vue-toastification";

const toast = useToast();
const input = ref("");
const inputArea = ref(null);
const items = ref([]);
const loading = ref(false);
const settings = ref({ size: 200, showLabel: true });
const selected = ref([]);
const selectedIds = computed(() => new Set(selected.value));
const isAllSelected = computed(() => items.value.length > 0 && selected.value.length === items.value.length);

// Quick generator
const quick = ref({ prefix: "", start: 1, count: 1, pad: 4 });
const addGenerated = () => {
    const nums = [];
    for (let i = 0; i < quick.value.count; i++) {
        const n = (quick.value.start + i).toString().padStart(quick.value.pad, "0");
        nums.push(`${quick.value.prefix}${n}`);
    }
    const existing = input.value.trim();
    input.value = existing ? existing + "\n" + nums.join("\n") : nums.join("\n");
};

// Parse and validate lines
const parsedLines = computed(() => {
    const rawLines = input.value.split(/\r?\n/);
    const lines = rawLines.map((s) => s.trim()).filter((s) => s.length > 0);
    const seen = new Set();
    const validRegex = /^[A-Za-z0-9._\-\/]+$/; // allow letters, numbers, dash, underscore, slash, dot
    const metas = [];
    let pos = 0;
    for (let i = 0; i < rawLines.length; i++) {
        const raw = rawLines[i];
        const value = raw.trim();
        const hasContent = value.length > 0;
        const isValid = hasContent ? validRegex.test(value) : false;
        const isDuplicate = hasContent ? seen.has(value) : false;
        if (hasContent && !seen.has(value)) seen.add(value);
        const start = pos;
        const end = pos + raw.length;
        metas.push({ index: i, value, raw, hasContent, isValid, isDuplicate, start, end });
        pos = end + 1; // account for newline
    }
    // Only return lines that have content when calculating stats, but keep meta for UI
    return metas.filter((m) => m.hasContent);
});

const stats = computed(() => {
    const total = parsedLines.value.length;
    const valid = parsedLines.value.filter((l) => l.isValid && !l.isDuplicate).length;
    const invalid = parsedLines.value.filter((l) => !l.isValid).length;
    const duplicates = parsedLines.value.filter((l) => l.isDuplicate).length;
    return { total, valid, invalid, duplicates };
});

const invalidLines = computed(() => parsedLines.value.filter((l) => !l.isValid));
const duplicateLines = computed(() => parsedLines.value.filter((l) => l.isDuplicate));

const removeInvalids = () => {
    const kept = parsedLines.value.filter((l) => l.isValid).map((l) => l.value);
    input.value = kept.join("\n");
};

const removeDuplicates = () => {
    const seen = new Set();
    const kept = [];
    for (const l of parsedLines.value) {
        if (!seen.has(l.value)) {
            kept.push(l.value);
            seen.add(l.value);
        }
    }
    input.value = kept.join("\n");
};

const removeLineAt = (idx) => {
    const arr = input.value.split(/\r?\n/);
    if (idx < 0 || idx >= arr.length) return;
    arr.splice(idx, 1);
    input.value = arr.join("\n");
};

const focusLine = (idx) => {
    // Compute start/end character offsets for the requested line in the raw input
    const arr = input.value.split(/\r?\n/);
    let start = 0;
    for (let i = 0; i < idx; i++) {
        start += arr[i].length + 1;
    }
    const end = start + (arr[idx]?.length || 0);
    // Focus and select the line
    if (inputArea.value) {
        inputArea.value.focus();
        try {
            inputArea.value.setSelectionRange(start, end);
        } catch (_) {
            // noop: some browsers may not support setSelectionRange on certain inputs
        }
    }
};

const handleImport = async (e) => {
    const file = e.target.files[0];
    if (!file) return;
    try {
        const formData = new FormData();
        formData.append("file", file);
        const { data } = await axios.post("/api/qr/import", formData, {
            headers: { "Content-Type": "multipart/form-data" },
        });
        items.value = data.items;
        toast.success(`Imported ${items.value.length} QR(s)`);
    } catch (err) {
        toast.error(err.response?.data?.message || "Import failed");
    } finally {
        e.target.value = "";
    }
};

const generate = async () => {
    const lines = parsedLines.value
        .filter((l) => l.isValid && !l.isDuplicate)
        .map((l) => l.value);
    if (lines.length === 0) return;
    loading.value = true;
    try {
        const { data } = await axios.post("/api/qr/generate", {
            numbers: lines,
        });
        items.value = data.items;
        toast.success(`Generated ${items.value.length} QR(s)`);
    } catch (err) {
        toast.error(err.response?.data?.message || "Generate failed");
    } finally {
        loading.value = false;
    }
};

const printOne = (it) => {
    const win = window.open("", "_blank");
    if (!win) return;
    win.document.write(
        `<!DOCTYPE html><html><head><meta charset="utf-8"><title>QR</title><style>@page{margin:0}html,body{margin:0;padding:0}img{display:block;margin:0 auto;max-width:100vw;max-height:100vh}</style></head><body><img src="${it.qr_image_url}" style="width:${settings.value.size}px;height:auto" onload="window.print();window.onafterprint=window.close();"/></body></html>`
    );
    win.document.close();
};

const printAll = () => {
    const win = window.open("", "_blank");
    if (!win) return;
    const total = items.value.length;
    const content = items.value
        .map(
            (it) =>
                `<div style="display:inline-block;margin:10px"><img src="${it.qr_image_url}" style="width:${settings.value.size}px;height:auto" onload="(window.__loaded=(window.__loaded||0)+1)>=${total}&&(window.print(),window.onafterprint=window.close())"/></div>`
        )
        .join("");
    win.document.write(
        `<!DOCTYPE html><html><head><meta charset="utf-8"><title>QRs</title><style>@page{margin:0}html,body{margin:0;padding:0}.grid{display:flex;flex-wrap:wrap;gap:10px;justify-content:center}</style></head><body><div class="grid">${content}</div></body></html>`
    );
    win.document.close();
};

const printSelected = () => {
    const selectedItems = items.value.filter((it) => selectedIds.value.has(it.qr_image_url));
    if (selectedItems.length === 0) return;
    const win = window.open("", "_blank");
    if (!win) return;
    const total = selectedItems.length;
    const content = selectedItems
        .map(
            (it) =>
                `<div style="display:inline-block;margin:10px"><img src="${it.qr_image_url}" style="width:${settings.value.size}px;height:auto" onload="(window.__loaded=(window.__loaded||0)+1)>=${total}&&(window.print(),window.onafterprint=window.close())"/></div>`
        )
        .join("");
    win.document.write(
        `<!DOCTYPE html><html><head><meta charset="utf-8"><title>QRs</title><style>@page{margin:0}html,body{margin:0;padding:0}.grid{display:flex;flex-wrap:wrap;gap:10px;justify-content:center}</style></head><body><div class="grid">${content}</div></body></html>`
    );
    win.document.close();
};

const toggleSelectAll = () => {
    if (isAllSelected.value) {
        selected.value = [];
    } else {
        selected.value = items.value.map((it) => it.qr_image_url);
    }
};

const clearAll = () => {
    items.value = [];
    input.value = "";
    selected.value = [];
};

</script>