<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
import { useI18n } from 'vue-i18n';
import { customT } from '@/i18n';
import { Trash2, Minus, Plus, ShoppingBag } from 'lucide-vue-next';
import WebLayout from '@/layouts/WebLayout.vue';
import { useCart } from '@/composables/useCart';

const { locale } = useI18n();
const t = customT;
const { items, totalPrice, updateQuantity, removeItem } = useCart();

function formatPrice(cents: number): string {
    return '€' + (cents / 100).toFixed(2).replace('.', ',');
}

function itemName(item: { nameZh: string; nameEn: string | null }): string {
    return locale.value === 'zh-TW' ? item.nameZh : (item.nameEn || item.nameZh);
}
</script>

<template>
    <WebLayout>
        <div class="mx-auto max-w-3xl px-4 py-8">
            <h1 class="text-2xl font-bold text-amber-900">{{ t('cart.title') }}</h1>

            <template v-if="items.length > 0">
                <div class="mt-6 space-y-4">
                    <div
                        v-for="item in items"
                        :key="item.productId"
                        class="flex items-center gap-4 rounded-xl border border-amber-100 bg-white p-4"
                    >
                        <!-- Image -->
                        <div class="h-20 w-20 shrink-0 overflow-hidden rounded-lg bg-amber-50">
                            <img
                                v-if="item.image"
                                :src="item.image"
                                :alt="itemName(item)"
                                class="h-full w-full object-cover"
                            />
                            <div v-else class="flex h-full w-full items-center justify-center text-2xl">🧶</div>
                        </div>

                        <!-- Details -->
                        <div class="flex-1">
                            <Link
                                :href="`/product/${item.slug}`"
                                class="font-medium text-amber-900 hover:text-amber-700"
                            >
                                {{ itemName(item) }}
                            </Link>
                            <p class="mt-1 text-sm text-amber-600">{{ formatPrice(item.price) }}</p>
                        </div>

                        <!-- Quantity -->
                        <div class="flex items-center rounded-lg border border-amber-200">
                            <button
                                class="px-2 py-1 text-amber-700 hover:bg-amber-50 disabled:opacity-40"
                                :disabled="item.quantity <= 1"
                                @click="updateQuantity(item.productId, item.quantity - 1)"
                            >
                                <Minus class="h-3 w-3" />
                            </button>
                            <span class="min-w-[1.5rem] text-center text-sm font-medium">{{ item.quantity }}</span>
                            <button
                                class="px-2 py-1 text-amber-700 hover:bg-amber-50 disabled:opacity-40"
                                :disabled="item.quantity >= item.stock"
                                @click="updateQuantity(item.productId, item.quantity + 1)"
                            >
                                <Plus class="h-3 w-3" />
                            </button>
                        </div>

                        <!-- Subtotal -->
                        <span class="w-20 text-right font-semibold text-amber-800">
                            {{ formatPrice(item.price * item.quantity) }}
                        </span>

                        <!-- Remove -->
                        <button
                            class="text-amber-400 transition hover:text-rose-500"
                            @click="removeItem(item.productId)"
                        >
                            <Trash2 class="h-4 w-4" />
                        </button>
                    </div>
                </div>

                <!-- Total & checkout -->
                <div class="mt-8 flex items-center justify-between border-t border-amber-100 pt-6">
                    <span class="text-lg font-medium text-amber-800">{{ t('cart.total') }}</span>
                    <span class="text-2xl font-bold text-amber-900">{{ formatPrice(totalPrice) }}</span>
                </div>

                <div class="mt-6 flex gap-3">
                    <Link
                        href="/"
                        class="rounded-lg border border-amber-200 px-6 py-3 text-sm font-medium text-amber-700 transition hover:bg-amber-50"
                    >
                        {{ t('cart.continueShopping') }}
                    </Link>
                    <Link
                        href="/checkout"
                        class="flex flex-1 items-center justify-center gap-2 rounded-lg bg-amber-800 px-6 py-3 font-medium text-white transition hover:bg-amber-900"
                    >
                        <ShoppingBag class="h-5 w-5" />
                        {{ t('cart.checkout') }}
                    </Link>
                </div>
            </template>

            <!-- Empty cart -->
            <div v-else class="py-20 text-center">
                <p class="text-lg text-amber-500">{{ t('cart.empty') }}</p>
                <Link
                    href="/"
                    class="mt-4 inline-block rounded-lg bg-amber-800 px-6 py-3 font-medium text-white transition hover:bg-amber-900"
                >
                    {{ t('cart.continueShopping') }}
                </Link>
            </div>
        </div>
    </WebLayout>
</template>
