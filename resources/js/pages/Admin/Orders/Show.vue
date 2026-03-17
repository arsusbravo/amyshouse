<script setup lang="ts">
import { router, Link } from '@inertiajs/vue3';
import { ArrowLeft, MapPin, MessageSquare, User, Mail, Phone } from 'lucide-vue-next';
import AppLayout from '@/layouts/AppLayout.vue';
import type { BreadcrumbItem } from '@/types';

interface OrderItem {
    id: number;
    product_name_zh: string;
    product_name_en: string;
    quantity: number;
    unit_price: number;
}

interface OrderData {
    id: number;
    order_number: string;
    status: string;
    total_amount: number;
    notes: string | null;
    created_at: string;
    client: {
        id: number;
        name: string;
        email: string;
        phone: string | null;
        group: string | null;
    };
    items: OrderItem[];
}

interface StatusOption {
    value: string;
    label: string;
}

const props = defineProps<{
    order: OrderData;
    statuses: StatusOption[];
}>();

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Dashboard', href: '/admin' },
    { title: 'Orders', href: '/admin/orders' },
    { title: props.order.order_number, href: `/admin/orders/${props.order.id}` },
];

function formatPrice(cents: number): string {
    return '€' + (cents / 100).toFixed(2).replace('.', ',');
}

function formatDate(dateStr: string): string {
    return new Date(dateStr).toLocaleDateString('nl-NL', {
        day: 'numeric',
        month: 'long',
        year: 'numeric',
        hour: '2-digit',
        minute: '2-digit',
    });
}

function updateStatus(status: string) {
    router.patch(`/admin/api/orders/${props.order.id}/status`, { status }, { preserveScroll: true });
}

const statusColors: Record<string, string> = {
    pending: 'bg-yellow-100 text-yellow-800 border-yellow-200',
    confirmed: 'bg-blue-100 text-blue-800 border-blue-200',
    ready: 'bg-green-100 text-green-800 border-green-200',
    completed: 'bg-gray-100 text-gray-600 border-gray-200',
    cancelled: 'bg-red-100 text-red-700 border-red-200',
};

const statusDotColors: Record<string, string> = {
    pending: 'bg-yellow-500',
    confirmed: 'bg-blue-500',
    ready: 'bg-green-500',
    completed: 'bg-gray-400',
    cancelled: 'bg-red-500',
};
</script>

<template>
    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex flex-1 flex-col gap-6 p-6">
            <!-- Header -->
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-4">
                    <Link href="/admin/orders" class="rounded-lg p-1.5 text-gray-400 hover:bg-gray-100 hover:text-gray-600">
                        <ArrowLeft class="h-5 w-5" />
                    </Link>
                    <div>
                        <h1 class="text-2xl font-bold text-gray-900">{{ order.order_number }}</h1>
                        <p class="mt-0.5 text-sm text-gray-500">{{ formatDate(order.created_at) }}</p>
                    </div>
                </div>
                <span
                    class="rounded-full border px-3 py-1 text-sm font-semibold"
                    :class="statusColors[order.status]"
                >
                    {{ order.status }}
                </span>
            </div>

            <!-- Status update -->
            <div class="rounded-lg border border-gray-200 bg-white p-5">
                <h2 class="text-sm font-semibold uppercase tracking-wider text-gray-500">Update Status</h2>
                <div class="mt-3 flex flex-wrap gap-2">
                    <button
                        v-for="s in statuses"
                        :key="s.value"
                        class="inline-flex items-center gap-2 rounded-lg border px-4 py-2 text-sm font-medium transition"
                        :class="
                            order.status === s.value
                                ? 'border-gray-900 bg-gray-900 text-white'
                                : 'border-gray-200 text-gray-600 hover:bg-gray-50'
                        "
                        :disabled="order.status === s.value"
                        @click="updateStatus(s.value)"
                    >
                        <span
                            class="h-2 w-2 rounded-full"
                            :class="order.status === s.value ? 'bg-white' : statusDotColors[s.value]"
                        />
                        {{ s.label }}
                    </button>
                </div>
            </div>

            <div class="grid gap-6 md:grid-cols-2">
                <!-- Client info -->
                <div class="rounded-lg border border-gray-200 bg-white p-5">
                    <h2 class="text-sm font-semibold uppercase tracking-wider text-gray-500">Client</h2>
                    <div class="mt-4 space-y-3">
                        <div class="flex items-center gap-3 text-sm">
                            <User class="h-4 w-4 text-gray-400" />
                            <Link
                                :href="`/admin/clients/${order.client.id}`"
                                class="font-medium text-blue-600 hover:text-blue-800"
                            >
                                {{ order.client.name }}
                            </Link>
                        </div>
                        <div class="flex items-center gap-3 text-sm">
                            <Mail class="h-4 w-4 text-gray-400" />
                            <a :href="`mailto:${order.client.email}`" class="text-gray-600 hover:text-blue-600">
                                {{ order.client.email }}
                            </a>
                        </div>
                        <div v-if="order.client.phone" class="flex items-center gap-3 text-sm">
                            <Phone class="h-4 w-4 text-gray-400" />
                            <span class="text-gray-600">{{ order.client.phone }}</span>
                        </div>
                        <div class="flex items-center gap-3 text-sm">
                            <MapPin class="h-4 w-4 text-gray-400" />
                            <span class="text-gray-600">{{ order.client.group || 'No group' }}</span>
                        </div>
                    </div>
                </div>

                <!-- Notes -->
                <div class="rounded-lg border border-gray-200 bg-white p-5">
                    <h2 class="text-sm font-semibold uppercase tracking-wider text-gray-500">Notes</h2>
                    <div class="mt-4">
                        <div v-if="order.notes" class="flex gap-3 text-sm text-gray-700">
                            <MessageSquare class="mt-0.5 h-4 w-4 shrink-0 text-gray-400" />
                            <p class="whitespace-pre-line">{{ order.notes }}</p>
                        </div>
                        <p v-else class="text-sm text-gray-400">No notes</p>
                    </div>
                </div>
            </div>

            <!-- Order items -->
            <div class="overflow-x-auto rounded-lg border border-gray-200 bg-white shadow-sm">
                <table class="w-full min-w-175 text-left text-sm">
                    <thead class="border-b border-gray-100 bg-gray-50">
                        <tr>
                            <th class="px-4 py-3 font-medium text-gray-600">Product</th>
                            <th class="px-4 py-3 text-right font-medium text-gray-600">Price</th>
                            <th class="px-4 py-3 text-right font-medium text-gray-600">Qty</th>
                            <th class="px-4 py-3 text-right font-medium text-gray-600">Subtotal</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        <tr v-for="item in order.items" :key="item.id">
                            <td class="px-4 py-3">
                                <p class="font-medium text-gray-900">{{ item.product_name_zh }}</p>
                                <p v-if="item.product_name_en" class="text-xs text-gray-500">{{ item.product_name_en }}</p>
                            </td>
                            <td class="px-4 py-3 text-right text-gray-600">
                                {{ formatPrice(item.unit_price) }}
                            </td>
                            <td class="px-4 py-3 text-right text-gray-700">
                                {{ item.quantity }}
                            </td>
                            <td class="px-4 py-3 text-right font-medium text-gray-900">
                                {{ formatPrice(item.unit_price * item.quantity) }}
                            </td>
                        </tr>
                    </tbody>
                    <tfoot class="border-t border-gray-200 bg-gray-50">
                        <tr>
                            <td colspan="3" class="px-4 py-4 text-right font-semibold text-gray-700">
                                Total
                            </td>
                            <td class="px-4 py-4 text-right text-lg font-bold text-gray-900">
                                {{ formatPrice(order.total_amount) }}
                            </td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </AppLayout>
</template>