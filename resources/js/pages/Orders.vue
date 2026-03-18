<script setup lang="ts">
import { customT } from '@/i18n';
import { Package } from 'lucide-vue-next';
import WebLayout from '@/layouts/WebLayout.vue';

const t = customT;

interface Order {
    id: number;
    order_number: string;
    status: string;
    total_amount: number;
    created_at: string;
    items_count: number;
}

const props = defineProps<{
    orders: Order[];
}>();

function formatPrice(cents: number): string {
    return '€' + (cents / 100).toFixed(2).replace('.', ',');
}

function formatDate(dateStr: string): string {
    return new Date(dateStr).toLocaleDateString('nl-NL', {
        day: 'numeric',
        month: 'short',
        year: 'numeric',
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
    <WebLayout>
        <div class="mx-auto max-w-3xl px-4 py-8">
            <h1 class="text-2xl font-bold text-amber-900">{{ t('order.title') }}</h1>

            <template v-if="orders.length > 0">
                <div class="mt-6 space-y-4">
                    <div
                        v-for="order in orders"
                        :key="order.id"
                        class="rounded-xl border border-amber-100 bg-white p-5"
                    >
                        <div class="flex items-start justify-between">
                            <div>
                                <p class="font-medium text-amber-900">{{ order.order_number }}</p>
                                <p class="mt-0.5 text-xs text-amber-500">{{ formatDate(order.created_at) }}</p>
                            </div>
                            <span
                                class="rounded-full px-3 py-1 text-xs font-medium"
                                :class="statusColors[order.status] || 'bg-gray-100 text-gray-600'"
                            >
                                {{ t(`order.${order.status}`) }}
                            </span>
                        </div>
                        <div class="mt-3 flex items-center justify-between border-t border-amber-50 pt-3">
                            <span class="text-sm text-amber-600">
                                {{ order.items_count }} {{ order.items_count === 1 ? 'item' : 'items' }}
                            </span>
                            <span class="font-semibold text-amber-800">{{ formatPrice(order.total_amount) }}</span>
                        </div>
                    </div>
                </div>
            </template>

            <div v-else class="py-20 text-center">
                <Package class="mx-auto h-12 w-12 text-amber-200" />
                <p class="mt-4 text-amber-500">{{ t('order.noOrders') }}</p>
            </div>
        </div>
    </WebLayout>
</template>
