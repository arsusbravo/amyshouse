<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
import { Package, Users, ShoppingCart, TrendingUp } from 'lucide-vue-next';
import AppLayout from '@/layouts/AppLayout.vue';
import type { BreadcrumbItem } from '@/types';

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Dashboard', href: '/admin' },
];

interface Stats {
    total_products: number;
    active_products: number;
    total_clients: number;
    active_clients: number;
    pending_orders: number;
    total_orders: number;
}

interface RecentOrder {
    id: number;
    order_number: string;
    client_name: string;
    client_group: string | null;
    status: string;
    total_amount: number;
    items_count: number;
    created_at: string;
}

const props = defineProps<{
    stats: Stats;
    recent_orders: RecentOrder[];
}>();

function formatPrice(cents: number): string {
    return '€' + (cents / 100).toFixed(2).replace('.', ',');
}

function formatDate(dateStr: string): string {
    return new Date(dateStr).toLocaleDateString('nl-NL', {
        day: 'numeric', month: 'short', hour: '2-digit', minute: '2-digit',
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
            <h1 class="text-2xl font-bold text-gray-900">Dashboard</h1>

            <!-- Stats grid -->
            <div class="grid grid-cols-2 gap-4 lg:grid-cols-4">
                <div class="rounded-xl border border-gray-200 bg-white p-5">
                    <div class="flex items-center gap-3">
                        <div class="rounded-lg bg-amber-50 p-2">
                            <Package class="h-5 w-5 text-amber-600" />
                        </div>
                        <div>
                            <p class="text-2xl font-bold text-gray-900">{{ stats.active_products }}</p>
                            <p class="text-xs text-gray-500">Active Products</p>
                        </div>
                    </div>
                </div>
                <div class="rounded-xl border border-gray-200 bg-white p-5">
                    <div class="flex items-center gap-3">
                        <div class="rounded-lg bg-blue-50 p-2">
                            <Users class="h-5 w-5 text-blue-600" />
                        </div>
                        <div>
                            <p class="text-2xl font-bold text-gray-900">{{ stats.active_clients }}</p>
                            <p class="text-xs text-gray-500">Active Clients</p>
                        </div>
                    </div>
                </div>
                <div class="rounded-xl border border-gray-200 bg-white p-5">
                    <div class="flex items-center gap-3">
                        <div class="rounded-lg bg-orange-50 p-2">
                            <ShoppingCart class="h-5 w-5 text-orange-600" />
                        </div>
                        <div>
                            <p class="text-2xl font-bold text-gray-900">{{ stats.pending_orders }}</p>
                            <p class="text-xs text-gray-500">Pending Orders</p>
                        </div>
                    </div>
                </div>
                <div class="rounded-xl border border-gray-200 bg-white p-5">
                    <div class="flex items-center gap-3">
                        <div class="rounded-lg bg-green-50 p-2">
                            <TrendingUp class="h-5 w-5 text-green-600" />
                        </div>
                        <div>
                            <p class="text-2xl font-bold text-gray-900">{{ stats.total_orders }}</p>
                            <p class="text-xs text-gray-500">Total Orders</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Recent orders -->
            <div>
                <div class="flex items-center justify-between">
                    <h2 class="text-lg font-semibold text-gray-900">Recent Orders</h2>
                    <Link href="/admin/orders" class="text-sm text-blue-600 hover:text-blue-800">View all →</Link>
                </div>

                <div class="mt-4 overflow-x-auto rounded-xl border border-gray-200 bg-white">
                    <table class="w-full min-w-175 text-left text-sm">
                        <thead class="border-b border-gray-100 bg-gray-50">
                            <tr>
                                <th class="px-4 py-3 font-medium text-gray-600">Order</th>
                                <th class="px-4 py-3 font-medium text-gray-600">Client</th>
                                <th class="px-4 py-3 font-medium text-gray-600">Group</th>
                                <th class="px-4 py-3 font-medium text-gray-600">Status</th>
                                <th class="px-4 py-3 text-right font-medium text-gray-600">Total</th>
                                <th class="px-4 py-3 font-medium text-gray-600">Date</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-50">
                            <tr v-for="order in recent_orders" :key="order.id" class="hover:bg-gray-50">
                                <td class="px-4 py-3">
                                    <Link :href="`/admin/orders/${order.id}`" class="font-medium text-blue-600 hover:text-blue-800">
                                        {{ order.order_number }}
                                    </Link>
                                </td>
                                <td class="px-4 py-3 text-gray-700">{{ order.client_name }}</td>
                                <td class="px-4 py-3 text-gray-500">{{ order.client_group }}</td>
                                <td class="px-4 py-3">
                                    <span class="rounded-full px-2 py-0.5 text-xs font-medium" :class="statusColors[order.status]">
                                        {{ order.status }}
                                    </span>
                                </td>
                                <td class="px-4 py-3 text-right font-medium text-gray-900">{{ formatPrice(order.total_amount) }}</td>
                                <td class="px-4 py-3 text-gray-500">{{ formatDate(order.created_at) }}</td>
                            </tr>
                            <tr v-if="recent_orders.length === 0">
                                <td colspan="6" class="px-4 py-8 text-center text-gray-400">No orders yet</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </AppLayout>
</template>