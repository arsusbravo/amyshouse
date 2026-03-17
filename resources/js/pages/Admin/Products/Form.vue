<script setup lang="ts">
import { ref, computed } from 'vue';
import { router, Link } from '@inertiajs/vue3';
import { ArrowLeft, Upload, X, Star } from 'lucide-vue-next';
import AppLayout from '@/layouts/AppLayout.vue';
import type { BreadcrumbItem } from '@/types';

interface ExistingImage {
    id: number;
    url: string;
    is_primary: boolean;
    sort_order: number;
}

interface ProductData {
    id: number;
    name_zh: string;
    name_en: string | null;
    description_zh: string | null;
    description_en: string | null;
    price: number;
    product_type_id: number;
    stock: number;
    size_info: string;
    production_days: number | null;
    is_active: boolean;
    sort_order: number;
    material_ids: number[];
    color_ids: number[];
    images: ExistingImage[];
}

interface TypeOption { id: number; name_zh: string; name_en: string | null }
interface MaterialOption { id: number; name_zh: string; name_en: string | null }
interface ColorOption { id: number; name_zh: string; name_en: string | null; hex_code: string | null }

const props = defineProps<{
    product: ProductData | null;
    types: TypeOption[];
    materials: MaterialOption[];
    colors: ColorOption[];
}>();

const isEditing = computed(() => !!props.product);

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Dashboard', href: '/admin' },
    { title: 'Products', href: '/admin/products' },
    { title: isEditing.value ? 'Edit Product' : 'New Product', href: '#' },
];

const form = ref({
    name_zh: props.product?.name_zh ?? '',
    name_en: props.product?.name_en ?? '',
    description_zh: props.product?.description_zh ?? '',
    description_en: props.product?.description_en ?? '',
    price: props.product?.price ?? 0,
    product_type_id: props.product?.product_type_id ?? '',
    stock: props.product?.stock ?? 0,
    size_info: props.product?.size_info ?? '',
    production_days: props.product?.production_days ?? null,
    is_active: props.product?.is_active ?? true,
    sort_order: props.product?.sort_order ?? 0,
    material_ids: props.product?.material_ids ?? [],
    color_ids: props.product?.color_ids ?? [],
});

const newImages = ref<File[]>([]);
const previewUrls = ref<string[]>([]);
const errors = ref<Record<string, string>>({});
const isSubmitting = ref(false);

const priceInEuros = computed({
    get: () => (form.value.price / 100).toFixed(2),
    set: (val: string) => { form.value.price = Math.round(parseFloat(val || '0') * 100); },
});

function toggleMaterial(id: number) {
    const idx = form.value.material_ids.indexOf(id);
    if (idx >= 0) form.value.material_ids.splice(idx, 1);
    else form.value.material_ids.push(id);
}

function toggleColor(id: number) {
    const idx = form.value.color_ids.indexOf(id);
    if (idx >= 0) form.value.color_ids.splice(idx, 1);
    else form.value.color_ids.push(id);
}

function handleFileSelect(e: Event) {
    const input = e.target as HTMLInputElement;
    if (!input.files) return;
    for (const file of Array.from(input.files)) {
        newImages.value.push(file);
        previewUrls.value.push(URL.createObjectURL(file));
    }
    input.value = '';
}

function removeNewImage(index: number) {
    URL.revokeObjectURL(previewUrls.value[index]);
    newImages.value.splice(index, 1);
    previewUrls.value.splice(index, 1);
}

function deleteExistingImage(imageId: number) {
    if (confirm('Delete this image?')) {
        router.delete(`/admin/api/product-images/${imageId}`, { preserveScroll: true });
    }
}

function setPrimaryImage(imageId: number) {
    router.post(`/admin/api/product-images/${imageId}/primary`, {}, { preserveScroll: true });
}

function submit() {
    isSubmitting.value = true;
    const formData = new FormData();

    Object.entries(form.value).forEach(([key, val]) => {
        if (key === 'material_ids' || key === 'color_ids') {
            (val as number[]).forEach((id: number) => formData.append(`${key}[]`, String(id)));
        } else if (key === 'is_active') {
            formData.append(key, val ? '1' : '0');
        } else if (val !== null && val !== undefined && val !== '') {
            formData.append(key, String(val));
        }
    });

    newImages.value.forEach((file) => formData.append('images[]', file));

    const url = isEditing.value ? `/admin/api/products/${props.product!.id}` : '/admin/api/products';

    if (isEditing.value) formData.append('_method', 'PUT');

    router.post(url, formData, {
        forceFormData: true,
        onError: (errs) => { errors.value = errs; },
        onFinish: () => { isSubmitting.value = false; },
    });
}
</script>

<template>
    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex flex-1 flex-col gap-6 p-6">
            <div class="flex items-center gap-4">
                <Link href="/admin/products" class="rounded-lg p-1.5 text-gray-400 hover:bg-gray-100 hover:text-gray-600">
                    <ArrowLeft class="h-5 w-5" />
                </Link>
                <h1 class="text-2xl font-bold text-gray-900">
                    {{ isEditing ? 'Edit Product' : 'New Product' }}
                </h1>
            </div>

            <div class="grid gap-8 lg:grid-cols-3">
                <!-- Left column: Main info -->
                <div class="space-y-6 lg:col-span-2">
                    <!-- Names -->
                    <div class="rounded-lg border border-gray-200 bg-white p-5">
                        <h2 class="text-sm font-semibold uppercase tracking-wider text-gray-500">Product Names</h2>
                        <div class="mt-4 grid gap-4 sm:grid-cols-2">
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Name (中文) *</label>
                                <input v-model="form.name_zh" class="mt-1 w-full rounded-lg border border-gray-300 px-3 py-2 text-sm shadow-sm focus:border-gray-400 focus:outline-none focus:ring-1 focus:ring-gray-400" />
                                <p v-if="errors.name_zh" class="mt-1 text-xs text-red-500">{{ errors.name_zh }}</p>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Name (English)</label>
                                <input v-model="form.name_en" class="mt-1 w-full rounded-lg border border-gray-300 px-3 py-2 text-sm shadow-sm focus:border-gray-400 focus:outline-none focus:ring-1 focus:ring-gray-400" />
                            </div>
                        </div>
                    </div>

                    <!-- Descriptions -->
                    <div class="rounded-lg border border-gray-200 bg-white p-5">
                        <h2 class="text-sm font-semibold uppercase tracking-wider text-gray-500">Descriptions</h2>
                        <div class="mt-4 grid gap-4 sm:grid-cols-2">
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Description (中文)</label>
                                <textarea v-model="form.description_zh" rows="5" class="mt-1 w-full rounded-lg border border-gray-300 px-3 py-2 text-sm shadow-sm focus:border-gray-400 focus:outline-none focus:ring-1 focus:ring-gray-400" />
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Description (English)</label>
                                <textarea v-model="form.description_en" rows="5" class="mt-1 w-full rounded-lg border border-gray-300 px-3 py-2 text-sm shadow-sm focus:border-gray-400 focus:outline-none focus:ring-1 focus:ring-gray-400" />
                            </div>
                        </div>
                    </div>

                    <!-- Images -->
                    <div class="rounded-lg border border-gray-200 bg-white p-5">
                        <h2 class="text-sm font-semibold uppercase tracking-wider text-gray-500">Images</h2>

                        <!-- Existing images -->
                        <div v-if="isEditing && product!.images.length > 0" class="mt-4 flex flex-wrap gap-3">
                            <div
                                v-for="img in product!.images"
                                :key="img.id"
                                class="group relative h-24 w-24 overflow-hidden rounded-lg border-2"
                                :class="img.is_primary ? 'border-amber-500' : 'border-gray-200'"
                            >
                                <img :src="img.url" class="h-full w-full object-cover" />
                                <div class="absolute inset-0 flex items-center justify-center gap-1 bg-black/40 opacity-0 transition group-hover:opacity-100">
                                    <button class="rounded bg-white/90 p-1 hover:bg-white" @click="setPrimaryImage(img.id)">
                                        <Star class="h-3.5 w-3.5" :class="img.is_primary ? 'fill-amber-500 text-amber-500' : 'text-gray-600'" />
                                    </button>
                                    <button class="rounded bg-white/90 p-1 hover:bg-red-50" @click="deleteExistingImage(img.id)">
                                        <X class="h-3.5 w-3.5 text-red-500" />
                                    </button>
                                </div>
                                <span v-if="img.is_primary" class="absolute bottom-0 left-0 right-0 bg-amber-500 py-0.5 text-center text-[9px] font-bold text-white">
                                    Primary
                                </span>
                            </div>
                        </div>

                        <!-- Upload -->
                        <div class="mt-4">
                            <label class="flex cursor-pointer flex-col items-center gap-2 rounded-lg border-2 border-dashed border-gray-300 py-6 transition hover:border-gray-400 hover:bg-gray-50">
                                <Upload class="h-6 w-6 text-gray-400" />
                                <span class="text-sm text-gray-500">Click to upload (JPG, PNG, WebP, max 5MB)</span>
                                <input type="file" accept="image/jpeg,image/png,image/webp" multiple class="hidden" @change="handleFileSelect" />
                            </label>
                        </div>

                        <!-- New image previews -->
                        <div v-if="previewUrls.length > 0" class="mt-3 flex flex-wrap gap-3">
                            <div v-for="(url, idx) in previewUrls" :key="idx" class="group relative h-24 w-24 overflow-hidden rounded-lg border border-gray-200">
                                <img :src="url" class="h-full w-full object-cover" />
                                <button
                                    class="absolute right-1 top-1 rounded-full bg-white/90 p-0.5 opacity-0 transition group-hover:opacity-100"
                                    @click="removeNewImage(idx)"
                                >
                                    <X class="h-3.5 w-3.5 text-red-500" />
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Right column: Settings -->
                <div class="space-y-6">
                    <!-- Pricing & Stock -->
                    <div class="rounded-lg border border-gray-200 bg-white p-5">
                        <h2 class="text-sm font-semibold uppercase tracking-wider text-gray-500">Pricing & Stock</h2>
                        <div class="mt-4 space-y-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Price (€) *</label>
                                <input v-model="priceInEuros" type="number" step="0.01" min="0" class="mt-1 w-full rounded-lg border border-gray-300 px-3 py-2 text-sm shadow-sm focus:border-gray-400 focus:outline-none focus:ring-1 focus:ring-gray-400" />
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Stock *</label>
                                <input v-model.number="form.stock" type="number" min="0" class="mt-1 w-full rounded-lg border border-gray-300 px-3 py-2 text-sm shadow-sm focus:border-gray-400 focus:outline-none focus:ring-1 focus:ring-gray-400" />
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Production Days</label>
                                <input v-model.number="form.production_days" type="number" min="0" class="mt-1 w-full rounded-lg border border-gray-300 px-3 py-2 text-sm shadow-sm focus:border-gray-400 focus:outline-none focus:ring-1 focus:ring-gray-400" />
                            </div>
                        </div>
                    </div>

                    <!-- Type -->
                    <div class="rounded-lg border border-gray-200 bg-white p-5">
                        <h2 class="text-sm font-semibold uppercase tracking-wider text-gray-500">Type *</h2>
                        <select
                            v-model="form.product_type_id"
                            class="mt-3 w-full rounded-lg border border-gray-300 px-3 py-2 text-sm shadow-sm focus:border-gray-400 focus:outline-none focus:ring-1 focus:ring-gray-400"
                        >
                            <option value="" disabled>Select type...</option>
                            <option v-for="t in types" :key="t.id" :value="t.id">{{ t.name_zh }} {{ t.name_en ? `/ ${t.name_en}` : '' }}</option>
                        </select>
                        <p v-if="errors.product_type_id" class="mt-1 text-xs text-red-500">{{ errors.product_type_id }}</p>
                    </div>

                    <!-- Materials -->
                    <div class="rounded-lg border border-gray-200 bg-white p-5">
                        <h2 class="text-sm font-semibold uppercase tracking-wider text-gray-500">Materials</h2>
                        <div class="mt-3 flex flex-wrap gap-2">
                            <button
                                v-for="m in materials"
                                :key="m.id"
                                class="rounded-full border px-3 py-1 text-xs font-medium transition"
                                :class="form.material_ids.includes(m.id)
                                    ? 'border-purple-400 bg-purple-100 text-purple-800'
                                    : 'border-gray-200 text-gray-500 hover:border-purple-300 hover:text-purple-700'"
                                @click="toggleMaterial(m.id)"
                            >
                                {{ m.name_zh }}
                            </button>
                        </div>
                    </div>

                    <!-- Colors -->
                    <div class="rounded-lg border border-gray-200 bg-white p-5">
                        <h2 class="text-sm font-semibold uppercase tracking-wider text-gray-500">Colors</h2>
                        <div class="mt-3 flex flex-wrap gap-2">
                            <button
                                v-for="c in colors"
                                :key="c.id"
                                class="flex items-center gap-1.5 rounded-full border px-3 py-1 text-xs font-medium transition"
                                :class="form.color_ids.includes(c.id)
                                    ? 'border-gray-900 bg-gray-100 text-gray-900'
                                    : 'border-gray-200 text-gray-500 hover:border-gray-400 hover:text-gray-700'"
                                @click="toggleColor(c.id)"
                            >
                                <span
                                    class="h-3 w-3 rounded-full border border-gray-300"
                                    :style="{ backgroundColor: c.hex_code || '#eee' }"
                                />
                                {{ c.name_zh }}
                            </button>
                        </div>
                    </div>

                    <!-- Size Info & Sort -->
                    <div class="rounded-lg border border-gray-200 bg-white p-5">
                        <h2 class="text-sm font-semibold uppercase tracking-wider text-gray-500">Extra</h2>
                        <div class="mt-4 space-y-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Size Info (JSON)</label>
                                <input v-model="form.size_info" placeholder='{"fits":"Labubu 17cm"}' class="mt-1 w-full rounded-lg border border-gray-300 px-3 py-2 text-sm font-mono shadow-sm focus:border-gray-400 focus:outline-none focus:ring-1 focus:ring-gray-400" />
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Sort Order</label>
                                <input v-model.number="form.sort_order" type="number" min="0" class="mt-1 w-full rounded-lg border border-gray-300 px-3 py-2 text-sm shadow-sm focus:border-gray-400 focus:outline-none focus:ring-1 focus:ring-gray-400" />
                            </div>
                            <label class="flex items-center gap-2">
                                <input v-model="form.is_active" type="checkbox" class="rounded border-gray-300 text-gray-900 focus:ring-gray-400" />
                                <span class="text-sm font-medium text-gray-700">Active (visible in shop)</span>
                            </label>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Submit -->
            <div class="flex gap-3 border-t border-gray-100 pt-6">
                <button
                    class="rounded-lg bg-gray-900 px-6 py-2.5 text-sm font-medium text-white hover:bg-gray-800 disabled:opacity-50"
                    :disabled="isSubmitting"
                    @click="submit"
                >
                    {{ isSubmitting ? 'Saving...' : (isEditing ? 'Update Product' : 'Create Product') }}
                </button>
                <Link href="/admin/products" class="rounded-lg border border-gray-300 px-6 py-2.5 text-sm font-medium text-gray-600 hover:bg-gray-50">
                    Cancel
                </Link>
            </div>
        </div>
    </AppLayout>
</template>