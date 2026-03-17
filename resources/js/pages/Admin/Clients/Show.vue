<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
import { ArrowLeft, Mail, Phone, MapPin, ShoppingBag } from 'lucide-vue-next';
import AppLayout from '@/layouts/AppLayout.vue';
import type { BreadcrumbItem } from '@/types';

interface ClientData {
    id: number;
    name: string;
    email: string;
    phone: string | null;
    group_name: string | null;
    group_id: number | null;
    is_active: boolean;
}

interface Order {
    id: number;
    order_number: string;
    status: string;
    status_label: string;
    total_amount: number;
    items_count: number;
    group_name: string | null;
    created_at: string;
}

const props = defineProps<{
    client: ClientData;
    orders: {
        data: Order[];
        links: any[];
        current_page: number;
        last_page: number;
    };
}>();

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Dashboard', href: '/admin' },
    { title: 'Clients', href: '/admin/clients' },
    { title: props.client.name, href: `/admin/clients/${props.client.id}` },
];

function formatPrice(cents: number): string {
    return '€' + (cents / 100).toFixed(2).replace('.', ',');
}

function formatDate(dateStr: string): string {
    return new Date(dateStr).toLocaleDateString('nl-NL', {
        day: 'numeric', month: 'short', year: 'numeric',
    });
}

const statusColors: Record<string, string> = {
    pending: 'bg-yellow-100 text-yellow-800',
    confirmed: 'bg-blue-100 text-blue-800',
    ready: 'bg-green-100 text-green-800',
    completed: 'bg-gray-100 text-gray-600',
    cancelled: 'bg-red-100 text-red-700',
};
</script>

<template>
    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex flex-1 flex-col gap-6 p-6">
            <!-- Header -->
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-4">
                    <Link href="/admin/clients" class="rounded-lg p-1.5 text-gray-400 hover:bg-gray-100 hover:text-gray-600">
                        <ArrowLeft class="h-5 w-5" />
                    </Link>
                    <div>
                        <h1 class="text-2xl font-bold text-gray-900">{{ client.name }}</h1>
                        <span
                            class="mt-1 inline-flex rounded-full px-2 py-0.5 text-xs font-semibold"
                            :class="client.is_active ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-600'"
                        >
                            {{ client.is_active ? 'Active' : 'Inactive' }}
                        </span>
                    </div>
                </div>
                <Link
                    :href="`/admin/clients/${client.id}/edit`"
                    class="rounded-lg bg-gray-900 px-4 py-2 text-sm font-medium text-white hover:bg-gray-800"
                >
                    Edit Client
                </Link>
            </div>

            <!-- Client info -->
            <div class="grid gap-6 md:grid-cols-2">
                <div class="rounded-lg border border-gray-200 bg-white p-5">
                    <h2 class="text-sm font-semibold text-gray-500 uppercase tracking-wider">Contact</h2>
                    <div class="mt-4 space-y-3">
                        <div class="flex items-center gap-3 text-sm">
                            <Mail class="h-4 w-4 text-gray-400" />
                            <a :href="`mailto:${client.email}`" class="text-blue-600 hover:text-blue-800">
                                {{ client.email }}
                            </a>
                        </div>
                        <div class="flex items-center gap-3 text-sm">
                            <Phone class="h-4 w-4 text-gray-400" />
                            <span class="text-gray-700">{{ client.phone || '—' }}</span>
                        </div>
                        <div class="flex items-center gap-3 text-sm">
                            <MapPin class="h-4 w-4 text-gray-400" />
                            <span class="text-gray-700">{{ client.group_name || 'No group assigned' }}</span>
                        </div>
                    </div>
                </div>

                <div class="rounded-lg border border-gray-200 bg-white p-5">
                    <h2 class="text-sm font-semibold text-gray-500 uppercase tracking-wider">Summary</h2>
                    <div class="mt-4 grid grid-cols-2 gap-4">
                        <div>
                            <p class="text-2xl font-bold text-gray-900">{{ orders.data.length > 0 ? orders.data.length : 0 }}</p>
                            <p class="text-xs text-gray-500">Total Orders</p>
                        </div>
                        <div>
                            <p class="text-2xl font-bold text-gray-900">
                                {{ formatPrice(orders.data.reduce((sum, o) => sum + o.total_amount, 0)) }}
                            </p>
                            <p class="text-xs text-gray-500">Total Spent</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Orders -->
            <div>
                <h2 class="text-lg font-semibold text-gray-900">Orders</h2>

                <div class="mt-4 overflow-x-auto rounded-lg border border-gray-200 bg-white shadow-sm">
                    <table class="w-full min-w-175 text-left text-sm">
                        <thead class="border-b border-gray-100 bg-gray-50">
                            <tr>
                                <th class="px-4 py-3 font-medium text-gray-600">Order</th>
                                <th class="px-4 py-3 font-medium text-gray-600">Date</th>
                                <th class="px-4 py-3 font-medium text-gray-600">Group</th>
                                <th class="px-4 py-3 text-right font-medium text-gray-600">Items</th>
                                <th class="px-4 py-3 text-right font-medium text-gray-600">Total</th>
                                <th class="px-4 py-3 font-medium text-gray-600">Status</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-50">
                            <tr v-for="order in orders.data" :key="order.id" class="hover:bg-gray-50">
                                <td class="px-4 py-3">
                                    <Link :href="`/admin/orders/${order.id}`" class="font-medium text-blue-600 hover:text-blue-800">
                                        {{ order.order_number }}
                                    </Link>
                                </td>
                                <td class="px-4 py-3 text-gray-500">{{ formatDate(order.created_at) }}</td>
                                <td class="px-4 py-3 text-gray-600">{{ order.group_name || '—' }}</td>
                                <td class="px-4 py-3 text-right text-gray-700">{{ order.items_count }}</td>
                                <td class="px-4 py-3 text-right font-medium text-gray-900">{{ formatPrice(order.total_amount) }}</td>
                                <td class="px-4 py-3">
                                    <span class="rounded-full px-2 py-0.5 text-xs font-medium" :class="statusColors[order.status]">
                                        {{ order.status_label }}
                                    </span>
                                </td>
                            </tr>
                            <tr v-if="orders.data.length === 0">
                                <td colspan="6" class="px-4 py-12 text-center">
                                    <ShoppingBag class="mx-auto h-8 w-8 text-gray-300" />
                                    <p class="mt-2 text-gray-400">No orders yet</p>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div v-if="orders.last_page > 1" class="mt-4 flex gap-1">
                    <Link
                        v-for="link in orders.links"
                        :key="link.label"
                        :href="link.url || '#'"
                        class="rounded-lg border px-3 py-1.5 text-sm"
                        :class="link.active ? 'border-gray-900 bg-gray-900 text-white' : 'border-gray-200 text-gray-600 hover:bg-gray-50'"
                        v-html="link.label"
                    />
                </div>
            </div>
        </div>
    </AppLayout>
</template>