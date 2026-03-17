<script setup lang="ts">
import { ref, reactive } from 'vue';
import { router } from '@inertiajs/vue3';
import { Save, Plus, Trash2, FileText, X, Check } from 'lucide-vue-next';
import AppLayout from '@/layouts/AppLayout.vue';
import type { BreadcrumbItem } from '@/types';

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Dashboard', href: '/admin' },
    { title: 'Site Content', href: '/admin/content' },
];

interface ContentItem {
    key: string;
    label: string | null;
    type: string;
    sort_order: number;
    zh_TW: string;
    en: string;
    zh_TW_id: number | null;
    en_id: number | null;
}

const props = defineProps<{
    contents: ContentItem[];
}>();

const items = ref<ContentItem[]>(JSON.parse(JSON.stringify(props.contents)));
const isSaving = ref(false);
const showAdd = ref(false);
const newItem = reactive({
    key: '',
    label: '',
    type: 'textarea' as 'text' | 'textarea',
    zh_TW: '',
    en: '',
    sort_order: 99,
});
const errors = ref<Record<string, string>>({});

function save() {
    isSaving.value = true;
    router.put('/admin/api/content', {
        items: items.value.map((item) => ({
            key: item.key,
            zh_TW: item.zh_TW,
            en: item.en,
            label: item.label,
            type: item.type,
            sort_order: item.sort_order,
        })),
    }, {
        preserveScroll: true,
        onFinish: () => { isSaving.value = false; },
    });
}

function addItem() {
    if (!newItem.key || !newItem.label) return;

    router.post('/admin/api/content', { ...newItem }, {
        preserveScroll: true,
        onSuccess: () => {
            showAdd.value = false;
            Object.assign(newItem, { key: '', label: '', type: 'textarea', zh_TW: '', en: '', sort_order: 99 });
        },
        onError: (errs) => { errors.value = errs; },
    });
}

function deleteItem(key: string) {
    if (confirm(`Delete content block "${key}"?`)) {
        router.delete(`/admin/api/content/${key}`, { preserveScroll: true });
    }
}
</script>

<template>
    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex flex-1 flex-col gap-6 p-6">
            <!-- Header -->
            <div class="flex items-center justify-between">
                <h1 class="text-2xl font-bold text-gray-900">Site Content</h1>
                <div class="flex gap-2">
                    <button
                        class="inline-flex items-center gap-2 rounded-lg border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 shadow-sm hover:bg-gray-50"
                        @click="showAdd = !showAdd"
                    >
                        <Plus class="h-4 w-4" /> Add Block
                    </button>
                    <button
                        class="inline-flex items-center gap-2 rounded-lg bg-gray-900 px-4 py-2 text-sm font-medium text-white shadow-sm hover:bg-gray-800 disabled:opacity-50"
                        :disabled="isSaving"
                        @click="save"
                    >
                        <Save class="h-4 w-4" />
                        {{ isSaving ? 'Saving...' : 'Save All' }}
                    </button>
                </div>
            </div>

            <p class="text-sm text-gray-500">
                Edit the text content shown on the public site. Changes are cached for 1 hour, or cleared immediately on save.
            </p>

            <!-- Add new block -->
            <div v-if="showAdd" class="rounded-lg border border-blue-200 bg-blue-50 p-5">
                <h3 class="text-sm font-semibold text-blue-900">Add Content Block</h3>
                <div class="mt-3 grid gap-3 sm:grid-cols-2">
                    <div>
                        <label class="block text-xs font-medium text-gray-700">Key *</label>
                        <input
                            v-model="newItem.key"
                            placeholder="e.g. home.banner"
                            class="mt-1 w-full rounded border border-gray-300 px-3 py-1.5 text-sm font-mono focus:border-gray-400 focus:outline-none"
                        />
                        <p v-if="errors.key" class="mt-1 text-xs text-red-500">{{ errors.key }}</p>
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-700">Label *</label>
                        <input
                            v-model="newItem.label"
                            placeholder="e.g. Homepage Banner Text"
                            class="mt-1 w-full rounded border border-gray-300 px-3 py-1.5 text-sm focus:border-gray-400 focus:outline-none"
                        />
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-700">Content (中文) *</label>
                        <textarea
                            v-model="newItem.zh_TW"
                            rows="2"
                            class="mt-1 w-full rounded border border-gray-300 px-3 py-1.5 text-sm focus:border-gray-400 focus:outline-none"
                        />
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-700">Content (English)</label>
                        <textarea
                            v-model="newItem.en"
                            rows="2"
                            class="mt-1 w-full rounded border border-gray-300 px-3 py-1.5 text-sm focus:border-gray-400 focus:outline-none"
                        />
                    </div>
                    <div class="flex items-end gap-3">
                        <div>
                            <label class="block text-xs font-medium text-gray-700">Type</label>
                            <select v-model="newItem.type" class="mt-1 rounded border border-gray-300 px-3 py-1.5 text-sm">
                                <option value="text">Single line</option>
                                <option value="textarea">Multi-line</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-700">Order</label>
                            <input v-model.number="newItem.sort_order" type="number" min="0" class="mt-1 w-16 rounded border border-gray-300 px-3 py-1.5 text-sm" />
                        </div>
                    </div>
                    <div class="flex items-end gap-2">
                        <button
                            class="inline-flex items-center gap-1.5 rounded-lg bg-blue-600 px-4 py-1.5 text-sm font-medium text-white hover:bg-blue-700"
                            @click="addItem"
                        >
                            <Check class="h-3.5 w-3.5" /> Add
                        </button>
                        <button
                            class="rounded-lg border border-gray-300 px-4 py-1.5 text-sm text-gray-600 hover:bg-gray-50"
                            @click="showAdd = false"
                        >
                            Cancel
                        </button>
                    </div>
                </div>
            </div>

            <!-- Content blocks -->
            <div class="space-y-4">
                <div
                    v-for="item in items"
                    :key="item.key"
                    class="rounded-lg border border-gray-200 bg-white p-5"
                >
                    <div class="flex items-start justify-between">
                        <div>
                            <h3 class="flex items-center gap-2 text-sm font-semibold text-gray-900">
                                <FileText class="h-4 w-4 text-gray-400" />
                                {{ item.label || item.key }}
                            </h3>
                            <p class="mt-0.5 font-mono text-xs text-gray-400">{{ item.key }}</p>
                        </div>
                        <button
                            class="rounded p-1 text-gray-400 hover:bg-red-50 hover:text-red-500"
                            title="Delete"
                            @click="deleteItem(item.key)"
                        >
                            <Trash2 class="h-4 w-4" />
                        </button>
                    </div>

                    <div class="mt-4 grid gap-4 sm:grid-cols-2">
                        <div>
                            <label class="mb-1 block text-xs font-medium text-gray-500">中文</label>
                            <textarea
                                v-if="item.type === 'textarea'"
                                v-model="item.zh_TW"
                                rows="4"
                                class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-gray-400 focus:outline-none focus:ring-1 focus:ring-gray-400"
                            />
                            <input
                                v-else
                                v-model="item.zh_TW"
                                class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-gray-400 focus:outline-none focus:ring-1 focus:ring-gray-400"
                            />
                        </div>
                        <div>
                            <label class="mb-1 block text-xs font-medium text-gray-500">English</label>
                            <textarea
                                v-if="item.type === 'textarea'"
                                v-model="item.en"
                                rows="4"
                                class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-gray-400 focus:outline-none focus:ring-1 focus:ring-gray-400"
                            />
                            <input
                                v-else
                                v-model="item.en"
                                class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-gray-400 focus:outline-none focus:ring-1 focus:ring-gray-400"
                            />
                        </div>
                    </div>
                </div>

                <div v-if="items.length === 0" class="rounded-lg border-2 border-dashed border-gray-200 py-12 text-center text-gray-400">
                    No content blocks yet. Click "Add Block" to create one.
                </div>
            </div>
        </div>
    </AppLayout>
</template>