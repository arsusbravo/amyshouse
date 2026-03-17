<script setup lang="ts">
import { Link, router } from '@inertiajs/vue3';
import { ArrowLeft, Pencil, Trash2, Star, Image as ImageIcon } from 'lucide-vue-next';
import AppLayout from '@/layouts/AppLayout.vue';
import type { BreadcrumbItem } from '@/types';

interface ProductImage {
    id: number;
    url: string;
    is_primary: boolean;
    sort_order: number;
}

const props = defineProps<{
    product: {
        id: number;
        slug: string;
        name_zh: string;
        name_en: string;
        description_zh: string | null;
        description_en: string | null;
        price: number;
        type: { id: number; name_zh: string };
        stock: number;
        size_info: Record<string, any> | null;
        production_days: number | null;
        is_active: boolean;
        sort_order: number;
        materials: { id: number; name_zh: string }[];
        colors: { id: number; name_zh: string; hex_code: string | null }[];
        images: ProductImage[];
        created_at: string;
    };
}>();

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Dashboard', href: '/admin' },
    { title: 'Products', href: '/admin/products' },
    { title: props.product.name_zh, href: `/admin/products/${props.product.id}` },
];

function formatPrice(cents: number): string {
    return '€' + (cents / 100).toFixed(2).replace('.', ',');
}

function deleteProduct() {
    if (confirm(`Delete "${props.product.name_zh}"? This cannot be undone.`)) {
        router.delete(`/admin/api/products/${props.product.id}`);
    }
}

function setPrimary(imageId: number) {
    router.post(`/admin/api/product-images/${imageId}/primary`, {}, { preserveScroll: true });
}

function deleteImage(imageId: number) {
    if (confirm('Delete this image?')) {
        router.delete(`/admin/api/product-images/${imageId}`, { preserveScroll: true });
    }
}
</script>

<template>
    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex flex-1 flex-col gap-6 p-6">
            <!-- Header -->
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-4">
                    <Link href="/admin/products" class="rounded-lg p-1.5 text-gray-400 hover:bg-gray-100 hover:text-gray-600">
                        <ArrowLeft class="h-5 w-5" />
                    </Link>
                    <div>
                        <h1 class="text-2xl font-bold text-gray-900">{{ product.name_zh }}</h1>
                        <p v-if="product.name_en" class="text-sm text-gray-500">{{ product.name_en }}</p>
                    </div>
                </div>
                <div class="flex gap-2">
                    <Link
                        :href="`/admin/products/${product.id}/edit`"
                        class="inline-flex items-center gap-2 rounded-lg bg-gray-900 px-4 py-2 text-sm font-medium text-white hover:bg-gray-800"
                    >
                        <Pencil class="h-4 w-4" /> Edit
                    </Link>
                    <button
                        class="inline-flex items-center gap-2 rounded-lg border border-red-300 px-4 py-2 text-sm font-medium text-red-600 hover:bg-red-50"
                        @click="deleteProduct"
                    >
                        <Trash2 class="h-4 w-4" /> Delete
                    </button>
                </div>
            </div>

            <div class="grid gap-6 lg:grid-cols-3">
                <!-- Left: Images -->
                <div class="lg:col-span-1">
                    <h2 class="text-sm font-semibold uppercase tracking-wider text-gray-500">Images</h2>
                    <div class="mt-3 space-y-3">
                        <div
                            v-for="img in product.images"
                            :key="img.id"
                            class="group relative overflow-hidden rounded-lg border border-gray-200"
                        >
                            <img :src="img.url" class="aspect-square w-full object-cover" />
                            <div class="absolute inset-x-0 bottom-0 flex items-center justify-between bg-gradient-to-t from-black/50 to-transparent p-2 opacity-0 transition group-hover:opacity-100">
                                <button
                                    class="rounded bg-white/90 px-2 py-1 text-[10px] font-semibold hover:bg-white"
                                    :class="img.is_primary ? 'text-amber-600' : 'text-gray-600'"
                                    @click="setPrimary(img.id)"
                                >
                                    <Star class="mr-0.5 inline h-3 w-3" :class="img.is_primary ? 'fill-amber-500' : ''" />
                                    {{ img.is_primary ? 'Primary' : 'Set Primary' }}
                                </button>
                                <button
                                    class="rounded bg-white/90 p-1 text-red-500 hover:bg-red-50"
                                    @click="deleteImage(img.id)"
                                >
                                    <Trash2 class="h-3.5 w-3.5" />
                                </button>
                            </div>
                            <span v-if="img.is_primary" class="absolute left-2 top-2 rounded bg-amber-500 px-1.5 py-0.5 text-[10px] font-bold text-white">
                                Primary
                            </span>
                        </div>
                        <div v-if="product.images.length === 0" class="flex aspect-square items-center justify-center rounded-lg border-2 border-dashed border-gray-200 bg-gray-50">
                            <div class="text-center">
                                <ImageIcon class="mx-auto h-8 w-8 text-gray-300" />
                                <p class="mt-1 text-xs text-gray-400">No images</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Right: Details -->
                <div class="space-y-6 lg:col-span-2">
                    <!-- Status + Price -->
                    <div class="flex items-start justify-between rounded-lg border border-gray-200 bg-white p-5">
                        <div>
                            <p class="text-3xl font-bold text-gray-900">{{ formatPrice(product.price) }}</p>
                            <div class="mt-2 flex items-center gap-3">
                                <span
                                    class="rounded-full px-2.5 py-0.5 text-xs font-semibold"
                                    :class="product.is_active ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-500'"
                                >
                                    {{ product.is_active ? 'Active' : 'Hidden' }}
                                </span>
                                <span class="text-sm text-gray-500">
                                    Stock: <strong :class="product.stock === 0 ? 'text-red-500' : 'text-gray-900'">{{ product.stock }}</strong>
                                </span>
                            </div>
                        </div>
                        <span class="rounded-lg bg-amber-50 px-3 py-1.5 text-sm font-medium text-amber-700">
                            {{ product.type.name_zh }}
                        </span>
                    </div>

                    <!-- Attributes -->
                    <div class="grid gap-4 sm:grid-cols-2">
                        <div class="rounded-lg border border-gray-200 bg-white p-4">
                            <h3 class="text-xs font-semibold uppercase tracking-wider text-gray-500">Materials</h3>
                            <div class="mt-2 flex flex-wrap gap-1.5">
                                <span v-for="m in product.materials" :key="m.id" class="rounded-full bg-purple-50 px-2.5 py-0.5 text-xs font-medium text-purple-700">
                                    {{ m.name_zh }}
                                </span>
                                <span v-if="product.materials.length === 0" class="text-xs text-gray-400">None</span>
                            </div>
                        </div>
                        <div class="rounded-lg border border-gray-200 bg-white p-4">
                            <h3 class="text-xs font-semibold uppercase tracking-wider text-gray-500">Colors</h3>
                            <div class="mt-2 flex flex-wrap gap-2">
                                <div v-for="c in product.colors" :key="c.id" class="flex items-center gap-1.5">
                                    <span class="h-4 w-4 rounded-full border border-gray-200" :style="{ backgroundColor: c.hex_code || '#eee' }" />
                                    <span class="text-xs text-gray-700">{{ c.name_zh }}</span>
                                </div>
                                <span v-if="product.colors.length === 0" class="text-xs text-gray-400">None</span>
                            </div>
                        </div>
                    </div>

                    <!-- Size + Production -->
                    <div class="grid gap-4 sm:grid-cols-2">
                        <div v-if="product.size_info" class="rounded-lg border border-gray-200 bg-white p-4">
                            <h3 class="text-xs font-semibold uppercase tracking-wider text-gray-500">Size Info</h3>
                            <pre class="mt-2 text-xs text-gray-700">{{ JSON.stringify(product.size_info, null, 2) }}</pre>
                        </div>
                        <div v-if="product.production_days" class="rounded-lg border border-gray-200 bg-white p-4">
                            <h3 class="text-xs font-semibold uppercase tracking-wider text-gray-500">Production Time</h3>
                            <p class="mt-2 text-sm text-gray-700">~{{ product.production_days }} days</p>
                        </div>
                    </div>

                    <!-- Descriptions -->
                    <div class="space-y-4">
                        <div v-if="product.description_zh" class="rounded-lg border border-gray-200 bg-white p-4">
                            <h3 class="text-xs font-semibold uppercase tracking-wider text-gray-500">Description (中文)</h3>
                            <p class="mt-2 whitespace-pre-line text-sm text-gray-700">{{ product.description_zh }}</p>
                        </div>
                        <div v-if="product.description_en" class="rounded-lg border border-gray-200 bg-white p-4">
                            <h3 class="text-xs font-semibold uppercase tracking-wider text-gray-500">Description (EN)</h3>
                            <p class="mt-2 whitespace-pre-line text-sm text-gray-700">{{ product.description_en }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>