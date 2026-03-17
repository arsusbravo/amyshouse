<script setup lang="ts">
import { ref, reactive } from 'vue';
import { router } from '@inertiajs/vue3';
import { Plus, Pencil, Trash2, Check, X, Tags, Paintbrush, Scissors } from 'lucide-vue-next';
import AppLayout from '@/layouts/AppLayout.vue';
import type { BreadcrumbItem } from '@/types';

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Dashboard', href: '/admin' },
    { title: 'Attributes', href: '/admin/attributes' },
];

interface ProductType {
    id: number;
    slug: string;
    name_zh: string;
    name_en: string | null;
    sort_order: number;
}

interface MaterialItem {
    id: number;
    slug: string;
    name_zh: string;
    name_en: string | null;
    sort_order: number;
}

interface ColorItem {
    id: number;
    slug: string;
    name_zh: string;
    name_en: string | null;
    hex_code: string | null;
    sort_order: number;
}

const props = defineProps<{
    types: ProductType[];
    materials: MaterialItem[];
    colors: ColorItem[];
}>();

// === Shared edit/add state ===
const editingId = ref<number | null>(null);
const editingSection = ref<string | null>(null);
const addingSection = ref<string | null>(null);

// === Product Types ===
const typeForm = reactive({ name_zh: '', name_en: '', sort_order: 0 });

function startAddType() {
    addingSection.value = 'types';
    editingSection.value = null;
    editingId.value = null;
    Object.assign(typeForm, { name_zh: '', name_en: '', sort_order: 0 });
}

function startEditType(t: ProductType) {
    addingSection.value = null;
    editingSection.value = 'types';
    editingId.value = t.id;
    Object.assign(typeForm, { name_zh: t.name_zh, name_en: t.name_en || '', sort_order: t.sort_order });
}

function saveType() {
    if (editingId.value) {
        router.put(`/admin/api/attributes/types/${editingId.value}`, { ...typeForm }, { preserveScroll: true, onSuccess: cancelEdit });
    } else {
        router.post('/admin/api/attributes/types', { ...typeForm }, { preserveScroll: true, onSuccess: cancelEdit });
    }
}

function deleteType(id: number) {
    if (confirm('Delete this product type?')) {
        router.delete(`/admin/api/attributes/types/${id}`, { preserveScroll: true });
    }
}

// === Materials ===
const materialForm = reactive({ name_zh: '', name_en: '', sort_order: 0 });

function startAddMaterial() {
    addingSection.value = 'materials';
    editingSection.value = null;
    editingId.value = null;
    Object.assign(materialForm, { name_zh: '', name_en: '', sort_order: 0 });
}

function startEditMaterial(m: MaterialItem) {
    addingSection.value = null;
    editingSection.value = 'materials';
    editingId.value = m.id;
    Object.assign(materialForm, { name_zh: m.name_zh, name_en: m.name_en || '', sort_order: m.sort_order });
}

function saveMaterial() {
    if (editingId.value) {
        router.put(`/admin/api/attributes/materials/${editingId.value}`, { ...materialForm }, { preserveScroll: true, onSuccess: cancelEdit });
    } else {
        router.post('/admin/api/attributes/materials', { ...materialForm }, { preserveScroll: true, onSuccess: cancelEdit });
    }
}

function deleteMaterial(id: number) {
    if (confirm('Delete this material?')) {
        router.delete(`/admin/api/attributes/materials/${id}`, { preserveScroll: true });
    }
}

// === Colors ===
const colorForm = reactive({ name_zh: '', name_en: '', hex_code: '', sort_order: 0 });

function startAddColor() {
    addingSection.value = 'colors';
    editingSection.value = null;
    editingId.value = null;
    Object.assign(colorForm, { name_zh: '', name_en: '', hex_code: '', sort_order: 0 });
}

function startEditColor(c: ColorItem) {
    addingSection.value = null;
    editingSection.value = 'colors';
    editingId.value = c.id;
    Object.assign(colorForm, { name_zh: c.name_zh, name_en: c.name_en || '', hex_code: c.hex_code || '', sort_order: c.sort_order });
}

function saveColor() {
    if (editingId.value) {
        router.put(`/admin/api/attributes/colors/${editingId.value}`, { ...colorForm }, { preserveScroll: true, onSuccess: cancelEdit });
    } else {
        router.post('/admin/api/attributes/colors', { ...colorForm }, { preserveScroll: true, onSuccess: cancelEdit });
    }
}

function deleteColor(id: number) {
    if (confirm('Delete this color?')) {
        router.delete(`/admin/api/attributes/colors/${id}`, { preserveScroll: true });
    }
}

function cancelEdit() {
    editingId.value = null;
    editingSection.value = null;
    addingSection.value = null;
}
</script>

<template>
    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex flex-1 flex-col gap-8 p-6">
            <h1 class="text-2xl font-bold text-gray-900">Product Attributes</h1>

            <!-- ==================== PRODUCT TYPES ==================== -->
            <section>
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-2">
                        <Tags class="h-5 w-5 text-amber-600" />
                        <h2 class="text-lg font-semibold text-gray-900">Product Types</h2>
                    </div>
                    <button
                        class="inline-flex items-center gap-1.5 rounded-lg bg-gray-900 px-3 py-1.5 text-xs font-medium text-white hover:bg-gray-800"
                        @click="startAddType"
                    >
                        <Plus class="h-3.5 w-3.5" /> Add Type
                    </button>
                </div>

                <div class="mt-3 overflow-x-auto rounded-lg border border-gray-200 bg-white shadow-sm">
                    <table class="w-full min-w-175 text-left text-sm">
                        <thead class="border-b border-gray-100 bg-gray-50">
                            <tr>
                                <th class="px-4 py-2.5 font-medium text-gray-600">Name (中文)</th>
                                <th class="px-4 py-2.5 font-medium text-gray-600">Name (EN)</th>
                                <th class="px-4 py-2.5 font-medium text-gray-600">Slug</th>
                                <th class="w-20 px-4 py-2.5 text-right font-medium text-gray-600">Order</th>
                                <th class="w-24 px-4 py-2.5 text-right font-medium text-gray-600">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-50">
                            <!-- Add row -->
                            <tr v-if="addingSection === 'types'" class="bg-blue-50/50">
                                <td class="px-4 py-2">
                                    <input v-model="typeForm.name_zh" placeholder="名稱" class="w-full rounded border border-gray-300 px-2 py-1 text-sm focus:border-gray-400 focus:outline-none" />
                                </td>
                                <td class="px-4 py-2">
                                    <input v-model="typeForm.name_en" placeholder="Name" class="w-full rounded border border-gray-300 px-2 py-1 text-sm focus:border-gray-400 focus:outline-none" />
                                </td>
                                <td class="px-4 py-2 text-xs text-gray-400">auto</td>
                                <td class="px-4 py-2">
                                    <input v-model.number="typeForm.sort_order" type="number" min="0" class="w-16 rounded border border-gray-300 px-2 py-1 text-right text-sm focus:border-gray-400 focus:outline-none" />
                                </td>
                                <td class="px-4 py-2">
                                    <div class="flex justify-end gap-1">
                                        <button class="rounded p-1 text-green-600 hover:bg-green-50" @click="saveType"><Check class="h-4 w-4" /></button>
                                        <button class="rounded p-1 text-gray-400 hover:bg-gray-100" @click="cancelEdit"><X class="h-4 w-4" /></button>
                                    </div>
                                </td>
                            </tr>

                            <tr v-for="t in types" :key="t.id" class="hover:bg-gray-50">
                                <template v-if="editingSection === 'types' && editingId === t.id">
                                    <td class="px-4 py-2">
                                        <input v-model="typeForm.name_zh" class="w-full rounded border border-gray-300 px-2 py-1 text-sm focus:border-gray-400 focus:outline-none" />
                                    </td>
                                    <td class="px-4 py-2">
                                        <input v-model="typeForm.name_en" class="w-full rounded border border-gray-300 px-2 py-1 text-sm focus:border-gray-400 focus:outline-none" />
                                    </td>
                                    <td class="px-4 py-2 text-xs text-gray-400">{{ t.slug }}</td>
                                    <td class="px-4 py-2">
                                        <input v-model.number="typeForm.sort_order" type="number" min="0" class="w-16 rounded border border-gray-300 px-2 py-1 text-right text-sm focus:border-gray-400 focus:outline-none" />
                                    </td>
                                    <td class="px-4 py-2">
                                        <div class="flex justify-end gap-1">
                                            <button class="rounded p-1 text-green-600 hover:bg-green-50" @click="saveType"><Check class="h-4 w-4" /></button>
                                            <button class="rounded p-1 text-gray-400 hover:bg-gray-100" @click="cancelEdit"><X class="h-4 w-4" /></button>
                                        </div>
                                    </td>
                                </template>
                                <template v-else>
                                    <td class="px-4 py-2.5 font-medium text-gray-900">{{ t.name_zh }}</td>
                                    <td class="px-4 py-2.5 text-gray-600">{{ t.name_en || '—' }}</td>
                                    <td class="px-4 py-2.5 text-xs text-gray-400">{{ t.slug }}</td>
                                    <td class="px-4 py-2.5 text-right text-gray-500">{{ t.sort_order }}</td>
                                    <td class="px-4 py-2.5">
                                        <div class="flex justify-end gap-1">
                                            <button class="rounded p-1 text-gray-400 hover:bg-gray-100 hover:text-gray-600" @click="startEditType(t)"><Pencil class="h-3.5 w-3.5" /></button>
                                            <button class="rounded p-1 text-gray-400 hover:bg-red-50 hover:text-red-500" @click="deleteType(t.id)"><Trash2 class="h-3.5 w-3.5" /></button>
                                        </div>
                                    </td>
                                </template>
                            </tr>

                            <tr v-if="types.length === 0 && addingSection !== 'types'">
                                <td colspan="5" class="px-4 py-6 text-center text-gray-400">No product types yet</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </section>

            <!-- ==================== MATERIALS ==================== -->
            <section>
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-2">
                        <Scissors class="h-5 w-5 text-purple-600" />
                        <h2 class="text-lg font-semibold text-gray-900">Materials</h2>
                    </div>
                    <button
                        class="inline-flex items-center gap-1.5 rounded-lg bg-gray-900 px-3 py-1.5 text-xs font-medium text-white hover:bg-gray-800"
                        @click="startAddMaterial"
                    >
                        <Plus class="h-3.5 w-3.5" /> Add Material
                    </button>
                </div>

                <div class="mt-3 overflow-x-auto rounded-lg border border-gray-200 bg-white shadow-sm">
                    <table class="w-full min-w-175 text-left text-sm">
                        <thead class="border-b border-gray-100 bg-gray-50">
                            <tr>
                                <th class="px-4 py-2.5 font-medium text-gray-600">Name (中文)</th>
                                <th class="px-4 py-2.5 font-medium text-gray-600">Name (EN)</th>
                                <th class="px-4 py-2.5 font-medium text-gray-600">Slug</th>
                                <th class="w-20 px-4 py-2.5 text-right font-medium text-gray-600">Order</th>
                                <th class="w-24 px-4 py-2.5 text-right font-medium text-gray-600">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-50">
                            <tr v-if="addingSection === 'materials'" class="bg-blue-50/50">
                                <td class="px-4 py-2">
                                    <input v-model="materialForm.name_zh" placeholder="名稱" class="w-full rounded border border-gray-300 px-2 py-1 text-sm focus:border-gray-400 focus:outline-none" />
                                </td>
                                <td class="px-4 py-2">
                                    <input v-model="materialForm.name_en" placeholder="Name" class="w-full rounded border border-gray-300 px-2 py-1 text-sm focus:border-gray-400 focus:outline-none" />
                                </td>
                                <td class="px-4 py-2 text-xs text-gray-400">auto</td>
                                <td class="px-4 py-2">
                                    <input v-model.number="materialForm.sort_order" type="number" min="0" class="w-16 rounded border border-gray-300 px-2 py-1 text-right text-sm focus:border-gray-400 focus:outline-none" />
                                </td>
                                <td class="px-4 py-2">
                                    <div class="flex justify-end gap-1">
                                        <button class="rounded p-1 text-green-600 hover:bg-green-50" @click="saveMaterial"><Check class="h-4 w-4" /></button>
                                        <button class="rounded p-1 text-gray-400 hover:bg-gray-100" @click="cancelEdit"><X class="h-4 w-4" /></button>
                                    </div>
                                </td>
                            </tr>

                            <tr v-for="m in materials" :key="m.id" class="hover:bg-gray-50">
                                <template v-if="editingSection === 'materials' && editingId === m.id">
                                    <td class="px-4 py-2">
                                        <input v-model="materialForm.name_zh" class="w-full rounded border border-gray-300 px-2 py-1 text-sm focus:border-gray-400 focus:outline-none" />
                                    </td>
                                    <td class="px-4 py-2">
                                        <input v-model="materialForm.name_en" class="w-full rounded border border-gray-300 px-2 py-1 text-sm focus:border-gray-400 focus:outline-none" />
                                    </td>
                                    <td class="px-4 py-2 text-xs text-gray-400">{{ m.slug }}</td>
                                    <td class="px-4 py-2">
                                        <input v-model.number="materialForm.sort_order" type="number" min="0" class="w-16 rounded border border-gray-300 px-2 py-1 text-right text-sm focus:border-gray-400 focus:outline-none" />
                                    </td>
                                    <td class="px-4 py-2">
                                        <div class="flex justify-end gap-1">
                                            <button class="rounded p-1 text-green-600 hover:bg-green-50" @click="saveMaterial"><Check class="h-4 w-4" /></button>
                                            <button class="rounded p-1 text-gray-400 hover:bg-gray-100" @click="cancelEdit"><X class="h-4 w-4" /></button>
                                        </div>
                                    </td>
                                </template>
                                <template v-else>
                                    <td class="px-4 py-2.5 font-medium text-gray-900">{{ m.name_zh }}</td>
                                    <td class="px-4 py-2.5 text-gray-600">{{ m.name_en || '—' }}</td>
                                    <td class="px-4 py-2.5 text-xs text-gray-400">{{ m.slug }}</td>
                                    <td class="px-4 py-2.5 text-right text-gray-500">{{ m.sort_order }}</td>
                                    <td class="px-4 py-2.5">
                                        <div class="flex justify-end gap-1">
                                            <button class="rounded p-1 text-gray-400 hover:bg-gray-100 hover:text-gray-600" @click="startEditMaterial(m)"><Pencil class="h-3.5 w-3.5" /></button>
                                            <button class="rounded p-1 text-gray-400 hover:bg-red-50 hover:text-red-500" @click="deleteMaterial(m.id)"><Trash2 class="h-3.5 w-3.5" /></button>
                                        </div>
                                    </td>
                                </template>
                            </tr>

                            <tr v-if="materials.length === 0 && addingSection !== 'materials'">
                                <td colspan="5" class="px-4 py-6 text-center text-gray-400">No materials yet</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </section>

            <!-- ==================== COLORS ==================== -->
            <section>
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-2">
                        <Paintbrush class="h-5 w-5 text-pink-600" />
                        <h2 class="text-lg font-semibold text-gray-900">Colors</h2>
                    </div>
                    <button
                        class="inline-flex items-center gap-1.5 rounded-lg bg-gray-900 px-3 py-1.5 text-xs font-medium text-white hover:bg-gray-800"
                        @click="startAddColor"
                    >
                        <Plus class="h-3.5 w-3.5" /> Add Color
                    </button>
                </div>

                <div class="mt-3 overflow-x-auto rounded-lg border border-gray-200 bg-white shadow-sm">
                    <table class="w-full min-w-175 text-left text-sm">
                        <thead class="border-b border-gray-100 bg-gray-50">
                            <tr>
                                <th class="w-12 px-4 py-2.5 font-medium text-gray-600"></th>
                                <th class="px-4 py-2.5 font-medium text-gray-600">Name (中文)</th>
                                <th class="px-4 py-2.5 font-medium text-gray-600">Name (EN)</th>
                                <th class="px-4 py-2.5 font-medium text-gray-600">Hex</th>
                                <th class="w-20 px-4 py-2.5 text-right font-medium text-gray-600">Order</th>
                                <th class="w-24 px-4 py-2.5 text-right font-medium text-gray-600">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-50">
                            <tr v-if="addingSection === 'colors'" class="bg-blue-50/50">
                                <td class="px-4 py-2">
                                    <input v-model="colorForm.hex_code" type="color" class="h-6 w-6 cursor-pointer rounded border-0 p-0" />
                                </td>
                                <td class="px-4 py-2">
                                    <input v-model="colorForm.name_zh" placeholder="名稱" class="w-full rounded border border-gray-300 px-2 py-1 text-sm focus:border-gray-400 focus:outline-none" />
                                </td>
                                <td class="px-4 py-2">
                                    <input v-model="colorForm.name_en" placeholder="Name" class="w-full rounded border border-gray-300 px-2 py-1 text-sm focus:border-gray-400 focus:outline-none" />
                                </td>
                                <td class="px-4 py-2">
                                    <input v-model="colorForm.hex_code" placeholder="#FF0000" class="w-24 rounded border border-gray-300 px-2 py-1 text-sm font-mono focus:border-gray-400 focus:outline-none" />
                                </td>
                                <td class="px-4 py-2">
                                    <input v-model.number="colorForm.sort_order" type="number" min="0" class="w-16 rounded border border-gray-300 px-2 py-1 text-right text-sm focus:border-gray-400 focus:outline-none" />
                                </td>
                                <td class="px-4 py-2">
                                    <div class="flex justify-end gap-1">
                                        <button class="rounded p-1 text-green-600 hover:bg-green-50" @click="saveColor"><Check class="h-4 w-4" /></button>
                                        <button class="rounded p-1 text-gray-400 hover:bg-gray-100" @click="cancelEdit"><X class="h-4 w-4" /></button>
                                    </div>
                                </td>
                            </tr>

                            <tr v-for="c in colors" :key="c.id" class="hover:bg-gray-50">
                                <template v-if="editingSection === 'colors' && editingId === c.id">
                                    <td class="px-4 py-2">
                                        <input v-model="colorForm.hex_code" type="color" class="h-6 w-6 cursor-pointer rounded border-0 p-0" />
                                    </td>
                                    <td class="px-4 py-2">
                                        <input v-model="colorForm.name_zh" class="w-full rounded border border-gray-300 px-2 py-1 text-sm focus:border-gray-400 focus:outline-none" />
                                    </td>
                                    <td class="px-4 py-2">
                                        <input v-model="colorForm.name_en" class="w-full rounded border border-gray-300 px-2 py-1 text-sm focus:border-gray-400 focus:outline-none" />
                                    </td>
                                    <td class="px-4 py-2">
                                        <input v-model="colorForm.hex_code" class="w-24 rounded border border-gray-300 px-2 py-1 text-sm font-mono focus:border-gray-400 focus:outline-none" />
                                    </td>
                                    <td class="px-4 py-2">
                                        <input v-model.number="colorForm.sort_order" type="number" min="0" class="w-16 rounded border border-gray-300 px-2 py-1 text-right text-sm focus:border-gray-400 focus:outline-none" />
                                    </td>
                                    <td class="px-4 py-2">
                                        <div class="flex justify-end gap-1">
                                            <button class="rounded p-1 text-green-600 hover:bg-green-50" @click="saveColor"><Check class="h-4 w-4" /></button>
                                            <button class="rounded p-1 text-gray-400 hover:bg-gray-100" @click="cancelEdit"><X class="h-4 w-4" /></button>
                                        </div>
                                    </td>
                                </template>
                                <template v-else>
                                    <td class="px-4 py-2.5">
                                        <span
                                            class="inline-block h-5 w-5 rounded-full border border-gray-200 shadow-sm"
                                            :style="{ backgroundColor: c.hex_code || '#eee' }"
                                        />
                                    </td>
                                    <td class="px-4 py-2.5 font-medium text-gray-900">{{ c.name_zh }}</td>
                                    <td class="px-4 py-2.5 text-gray-600">{{ c.name_en || '—' }}</td>
                                    <td class="px-4 py-2.5 font-mono text-xs text-gray-500">{{ c.hex_code || '—' }}</td>
                                    <td class="px-4 py-2.5 text-right text-gray-500">{{ c.sort_order }}</td>
                                    <td class="px-4 py-2.5">
                                        <div class="flex justify-end gap-1">
                                            <button class="rounded p-1 text-gray-400 hover:bg-gray-100 hover:text-gray-600" @click="startEditColor(c)"><Pencil class="h-3.5 w-3.5" /></button>
                                            <button class="rounded p-1 text-gray-400 hover:bg-red-50 hover:text-red-500" @click="deleteColor(c.id)"><Trash2 class="h-3.5 w-3.5" /></button>
                                        </div>
                                    </td>
                                </template>
                            </tr>

                            <tr v-if="colors.length === 0 && addingSection !== 'colors'">
                                <td colspan="6" class="px-4 py-6 text-center text-gray-400">No colors yet</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </section>
        </div>
    </AppLayout>
</template>