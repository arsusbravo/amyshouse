<script setup lang="ts">
import { ref, watch } from 'vue';
import { Link, router } from '@inertiajs/vue3';
import { Plus, Search, Eye, Pencil, Trash2 } from 'lucide-vue-next';
import AppLayout from '@/layouts/AppLayout.vue';
import type { BreadcrumbItem } from '@/types';

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Dashboard', href: '/admin' },
    { title: 'Products', href: '/admin/products' },
];

interface Product {
    id: number;
    slug: string;
    name_zh: string;
    name_en: string;
    price: number;
    type_name: string;
    stock: number;
    is_active: boolean;
    primary_image_url: string | null;
    materials: string[];
    colors: { name: string; hex_code: string | null }[];
}

const props = defineProps<{
    products: { data: Product[]; links: any[]; last_page: number };
    types: { id: number; name_zh: string; name_en: string | null }[];
    materials: { id: number; name_zh: string; name_en: string | null }[];
    colors: { id: number; name_zh: string; name_en: string | null; hex_code: string | null }[];
    filters: { search?: string; type_id?: string; material_id?: string; color_id?: string };
}>();

const search = ref(props.filters.search ?? '');
const typeId = ref(props.filters.type_id ?? '');
const materialId = ref(props.filters.material_id ?? '');
const colorId = ref(props.filters.color_id ?? '');

let timeout: ReturnType<typeof setTimeout>;
watch(search, () => { clearTimeout(timeout); timeout = setTimeout(applyFilters, 400); });
watch([typeId, materialId, colorId], applyFilters);

function applyFilters() {
    router.get('/admin/products', {
        search: search.value || undefined,
        type_id: typeId.value || undefined,
        material_id: materialId.value || undefined,
        color_id: colorId.value || undefined,
    }, { preserveState: true });
}

function formatPrice(cents: number): string {
    return '€' + (cents / 100).toFixed(2).replace('.', ',');
}

function deleteProduct(product: Product) {
    if (confirm(`Delete "${product.name_zh}"? This cannot be undone.`)) {
        router.delete(`/admin/api/products/${product.id}`);
    }
}
</script>

<template>
    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex flex-1 flex-col gap-6 p-6">
            <div class="flex items-center justify-between">
                <h1 class="text-2xl font-bold text-gray-900">Products</h1>
                <Link
                    href="/admin/products/create"
                    class="inline-flex items-center gap-2 rounded-lg bg-gray-900 px-4 py-2 text-sm font-medium text-white shadow-sm hover:bg-gray-800"
                >
                    <Plus class="h-4 w-4" /> Add Product
                </Link>
            </div>

            <!-- Filters -->
            <div class="flex flex-wrap gap-3">
                <div class="relative flex-1 max-w-sm">
                    <Search class="absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-gray-400" />
                    <input
                        v-model="search"
                        type="text"
                        placeholder="Search products..."
                        class="w-full rounded-lg border border-gray-300 bg-white py-2 pl-10 pr-4 text-sm shadow-sm focus:border-gray-400 focus:outline-none focus:ring-1 focus:ring-gray-400"
                    />
                </div>
                <select v-model="typeId" class="rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm shadow-sm">
                    <option value="">All Types</option>
                    <option v-for="t in types" :key="t.id" :value="t.id">{{ t.name_zh }}</option>
                </select>
                <select v-model="materialId" class="rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm shadow-sm">
                    <option value="">All Materials</option>
                    <option v-for="m in materials" :key="m.id" :value="m.id">{{ m.name_zh }}</option>
                </select>
                <select v-model="colorId" class="rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm shadow-sm">
                    <option value="">All Colors</option>
                    <option v-for="c in colors" :key="c.id" :value="c.id">{{ c.name_zh }}</option>
                </select>
            </div>

            <!-- Table -->
            <div class="overflow-x-auto rounded-lg border border-gray-200 bg-white shadow-sm">
                <table class="w-full min-w-175 text-left text-sm">
                    <thead class="border-b border-gray-100 bg-gray-50">
                        <tr>
                            <th class="px-4 py-3 font-medium text-gray-600">Product</th>
                            <th class="px-4 py-3 font-medium text-gray-600">Type</th>
                            <th class="px-4 py-3 font-medium text-gray-600">Materials</th>
                            <th class="px-4 py-3 font-medium text-gray-600">Colors</th>
                            <th class="px-4 py-3 text-right font-medium text-gray-600">Price</th>
                            <th class="px-4 py-3 text-right font-medium text-gray-600">Stock</th>
                            <th class="px-4 py-3 font-medium text-gray-600">Status</th>
                            <th class="px-4 py-3 text-right font-medium text-gray-600">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        <tr v-for="p in products.data" :key="p.id" class="hover:bg-gray-50">
                            <td class="px-4 py-3">
                                <div class="flex items-center gap-3">
                                    <div class="h-10 w-10 shrink-0 overflow-hidden rounded-lg bg-gray-100">
                                        <img v-if="p.primary_image_url" :src="p.primary_image_url" class="h-full w-full object-cover" />
                                        <div v-else class="flex h-full w-full items-center justify-center text-sm">🧶</div>
                                    </div>
                                    <div>
                                        <p class="font-medium text-gray-900">{{ p.name_zh }}</p>
                                        <p v-if="p.name_en" class="text-xs text-gray-500">{{ p.name_en }}</p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-4 py-3 text-gray-600">{{ p.type_name }}</td>
                            <td class="px-4 py-3">
                                <div class="flex flex-wrap gap-1">
                                    <span v-for="m in p.materials" :key="m" class="rounded bg-purple-50 px-1.5 py-0.5 text-[10px] font-medium text-purple-700">
                                        {{ m }}
                                    </span>
                                    <span v-if="p.materials.length === 0" class="text-xs text-gray-400">—</span>
                                </div>
                            </td>
                            <td class="px-4 py-3">
                                <div class="flex items-center gap-1">
                                    <span
                                        v-for="c in p.colors.slice(0, 4)"
                                        :key="c.name"
                                        class="h-4 w-4 rounded-full border border-gray-200"
                                        :style="{ backgroundColor: c.hex_code || '#eee' }"
                                        :title="c.name"
                                    />
                                    <span v-if="p.colors.length > 4" class="text-[10px] text-gray-400">+{{ p.colors.length - 4 }}</span>
                                    <span v-if="p.colors.length === 0" class="text-xs text-gray-400">—</span>
                                </div>
                            </td>
                            <td class="px-4 py-3 text-right font-medium text-gray-900">{{ formatPrice(p.price) }}</td>
                            <td class="px-4 py-3 text-right" :class="p.stock === 0 ? 'text-red-500 font-medium' : 'text-gray-700'">
                                {{ p.stock }}
                            </td>
                            <td class="px-4 py-3">
                                <span
                                    class="inline-flex rounded-full px-2 py-0.5 text-xs font-semibold"
                                    :class="p.is_active ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-500'"
                                >
                                    {{ p.is_active ? 'Active' : 'Hidden' }}
                                </span>
                            </td>
                            <td class="px-4 py-3">
                                <div class="flex items-center justify-end gap-1">
                                    <Link :href="`/admin/products/${p.id}`" class="rounded p-1.5 text-gray-400 hover:bg-gray-100 hover:text-blue-600" title="View">
                                        <Eye class="h-4 w-4" />
                                    </Link>
                                    <Link :href="`/admin/products/${p.id}/edit`" class="rounded p-1.5 text-gray-400 hover:bg-gray-100 hover:text-gray-600" title="Edit">
                                        <Pencil class="h-4 w-4" />
                                    </Link>
                                    <button class="rounded p-1.5 text-gray-400 hover:bg-red-50 hover:text-red-500" @click="deleteProduct(p)">
                                        <Trash2 class="h-4 w-4" />
                                    </button>
                                </div>
                            </td>
                        </tr>
                        <tr v-if="products.data.length === 0">
                            <td colspan="8" class="px-4 py-8 text-center text-gray-400">No products found</td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div v-if="products.last_page > 1" class="flex gap-1">
                <Link
                    v-for="link in products.links"
                    :key="link.label"
                    :href="link.url || '#'"
                    class="rounded-lg border px-3 py-1.5 text-sm"
                    :class="link.active ? 'border-gray-900 bg-gray-900 text-white' : 'border-gray-200 text-gray-600 hover:bg-gray-50'"
                    v-html="link.label"
                />
            </div>
        </div>
    </AppLayout>
</template>