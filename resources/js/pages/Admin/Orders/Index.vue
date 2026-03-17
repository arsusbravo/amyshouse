<script setup lang="ts">
import { ref, watch } from 'vue';
import { Link, router } from '@inertiajs/vue3';
import { Search, Eye } from 'lucide-vue-next';
import AppLayout from '@/layouts/AppLayout.vue';
import type { BreadcrumbItem } from '@/types';

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Dashboard', href: '/admin' },
    { title: 'Orders', href: '/admin/orders' },
];

interface Order {
    id: number;
    order_number: string;
    client_name: string;
    client_email: string;
    client_group: string | null;
    status: string;
    total_amount: number;
    items_count: number;
    notes: string | null;
    created_at: string;
}

interface StatusOption {
    value: string;
    label: string;
}

const props = defineProps<{
    orders: { data: Order[]; links: any[]; last_page: number };
    statuses: StatusOption[];
    filters: { status?: string; search?: string };
}>();

const search = ref(props.filters.search ?? '');
const selectedStatus = ref(props.filters.status ?? '');

let timeout: ReturnType<typeof setTimeout>;
watch(search, () => {
    clearTimeout(timeout);
    timeout = setTimeout(applyFilters, 400);
});
watch(selectedStatus, applyFilters);

function applyFilters() {
    router.get('/admin/orders', {
        search: search.value || undefined,
        status: selectedStatus.value || undefined,
    }, { preserveState: true });
}

function formatPrice(cents: number): string {
    return '€' + (cents / 100).toFixed(2).replace('.', ',');
}

function formatDate(dateStr: string): string {
    return new Date(dateStr).toLocaleDateString('nl-NL', {
        day: 'numeric',
        month: 'short',
        year: 'numeric',
        hour: '2-digit',
        minute: '2-digit',
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
            <h1 class="text-2xl font-bold text-gray-900">Orders</h1>

            <!-- Filters -->
            <div class="flex gap-3">
                <div class="relative max-w-sm flex-1">
                    <Search class="absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-gray-400" />
                    <input
                        v-model="search"
                        type="text"
                        placeholder="Search order #, client name..."
                        class="w-full rounded-lg border border-gray-300 bg-white py-2 pl-10 pr-4 text-sm shadow-sm focus:border-gray-400 focus:outline-none focus:ring-1 focus:ring-gray-400"
                    />
                </div>
                <select
                    v-model="selectedStatus"
                    class="rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm shadow-sm focus:border-gray-400 focus:outline-none focus:ring-1 focus:ring-gray-400"
                >
                    <option value="">All Statuses</option>
                    <option v-for="s in statuses" :key="s.value" :value="s.value">{{ s.label }}</option>
                </select>
            </div>

            <!-- Table -->
            <div class="overflow-x-auto rounded-lg border border-gray-200 bg-white shadow-sm">
                <table class="w-full min-w-175 text-left text-sm">
                    <thead class="border-b border-gray-100 bg-gray-50">
                        <tr>
                            <th class="px-4 py-3 font-medium text-gray-600">Order</th>
                            <th class="px-4 py-3 font-medium text-gray-600">Client</th>
                            <th class="px-4 py-3 font-medium text-gray-600">Group</th>
                            <th class="px-4 py-3 text-right font-medium text-gray-600">Items</th>
                            <th class="px-4 py-3 text-right font-medium text-gray-600">Total</th>
                            <th class="px-4 py-3 font-medium text-gray-600">Status</th>
                            <th class="px-4 py-3 font-medium text-gray-600">Notes</th>
                            <th class="px-4 py-3 font-medium text-gray-600">Date</th>
                            <th class="px-4 py-3 text-right font-medium text-gray-600"></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        <tr v-for="order in orders.data" :key="order.id" class="hover:bg-gray-50">
                            <td class="px-4 py-3">
                                <Link
                                    :href="`/admin/orders/${order.id}`"
                                    class="font-medium text-blue-600 hover:text-blue-800"
                                >
                                    {{ order.order_number }}
                                </Link>
                            </td>
                            <td class="px-4 py-3">
                                <p class="text-gray-900">{{ order.client_name }}</p>
                                <p class="text-xs text-gray-500">{{ order.client_email }}</p>
                            </td>
                            <td class="px-4 py-3 text-gray-600">{{ order.client_group || '—' }}</td>
                            <td class="px-4 py-3 text-right text-gray-700">{{ order.items_count }}</td>
                            <td class="px-4 py-3 text-right font-medium text-gray-900">
                                {{ formatPrice(order.total_amount) }}
                            </td>
                            <td class="px-4 py-3">
                                <span
                                    class="inline-flex rounded-full px-2 py-0.5 text-xs font-semibold"
                                    :class="statusColors[order.status]"
                                >
                                    {{ order.status }}
                                </span>
                            </td>
                            <td class="max-w-[150px] truncate px-4 py-3 text-xs text-gray-500" :title="order.notes || ''">
                                {{ order.notes || '—' }}
                            </td>
                            <td class="whitespace-nowrap px-4 py-3 text-xs text-gray-500">
                                {{ formatDate(order.created_at) }}
                            </td>
                            <td class="px-4 py-3">
                                <Link
                                    :href="`/admin/orders/${order.id}`"
                                    class="rounded p-1.5 text-gray-400 hover:bg-gray-100 hover:text-blue-600"
                                >
                                    <Eye class="h-4 w-4" />
                                </Link>
                            </td>
                        </tr>
                        <tr v-if="orders.data.length === 0">
                            <td colspan="9" class="px-4 py-12 text-center text-gray-400">No orders found</td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div v-if="orders.last_page > 1" class="flex gap-1">
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
    </AppLayout>
</template>