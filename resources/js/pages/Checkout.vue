<script setup lang="ts">
import { ref, computed } from 'vue';
import { router, usePage, Link } from '@inertiajs/vue3';
import { useI18n } from 'vue-i18n';
import { customT } from '@/i18n';
import { MapPin, Check } from 'lucide-vue-next';
import WebLayout from '@/layouts/WebLayout.vue';
import { useCart } from '@/composables/useCart';

const { locale } = useI18n();
const t = customT;
const page = usePage();
const { items, totalPrice, clearCart } = useCart();

const user = computed(() => page.props.auth?.user as any);

const notes = ref('');
const isSubmitting = ref(false);
const orderPlaced = ref(false);
const orderNumber = ref('');

function formatPrice(cents: number): string {
    return '€' + (cents / 100).toFixed(2).replace('.', ',');
}

function itemName(item: { nameZh: string; nameEn: string | null }): string {
    return locale.value === 'zh-TW' ? item.nameZh : (item.nameEn || item.nameZh);
}

async function placeOrder() {
    if (isSubmitting.value || items.value.length === 0) return;
    isSubmitting.value = true;

    router.post(
        '/orders',
        {
            items: items.value.map((item) => ({
                product_id: item.productId,
                quantity: item.quantity,
            })),
            notes: notes.value || null,
        },
        {
            onSuccess: (response: any) => {
                orderPlaced.value = true;
                orderNumber.value = response.props?.flash?.order_number ?? '';
                clearCart();
            },
            onFinish: () => {
                isSubmitting.value = false;
            },
        },
    );
}
</script>

<template>
    <WebLayout>
        <div class="mx-auto max-w-2xl px-4 py-8">
            <!-- Order placed success -->
            <template v-if="orderPlaced">
                <div class="py-16 text-center">
                    <div class="mx-auto flex h-16 w-16 items-center justify-center rounded-full bg-green-100">
                        <Check class="h-8 w-8 text-green-600" />
                    </div>
                    <h1 class="mt-4 text-2xl font-bold text-amber-900">{{ t('checkout.orderPlaced') }}</h1>
                    <p v-if="orderNumber" class="mt-2 text-amber-600">{{ t('order.orderNumber') }}: {{ orderNumber }}</p>
                    <div class="mt-8 flex justify-center gap-3">
                        <Link
                            href="/orders"
                            class="rounded-lg bg-amber-800 px-6 py-3 font-medium text-white transition hover:bg-amber-900"
                        >
                            {{ t('common.myOrders') }}
                        </Link>
                        <Link
                            href="/"
                            class="rounded-lg border border-amber-200 px-6 py-3 font-medium text-amber-700 transition hover:bg-amber-50"
                        >
                            {{ t('cart.continueShopping') }}
                        </Link>
                    </div>
                </div>
            </template>

            <!-- Checkout form -->
            <template v-else>
                <h1 class="text-2xl font-bold text-amber-900">{{ t('checkout.title') }}</h1>

                <!-- Pickup location -->
                <div class="mt-6 flex items-center gap-3 rounded-xl border border-amber-200 bg-amber-50 p-4">
                    <MapPin class="h-5 w-5 shrink-0 text-amber-600" />
                    <div>
                        <p class="text-sm font-medium text-amber-800">{{ t('checkout.pickupLocation') }}</p>
                        <p class="text-lg font-semibold text-amber-900">{{ user?.group || '-' }}</p>
                    </div>
                </div>

                <!-- Order items -->
                <div class="mt-6 space-y-3">
                    <div
                        v-for="item in items"
                        :key="item.productId"
                        class="flex items-center justify-between rounded-lg bg-white p-3"
                    >
                        <div class="flex items-center gap-3">
                            <div class="h-12 w-12 overflow-hidden rounded-lg bg-amber-50">
                                <img
                                    v-if="item.image"
                                    :src="item.image"
                                    class="h-full w-full object-cover"
                                />
                            </div>
                            <div>
                                <p class="text-sm font-medium text-amber-900">{{ itemName(item) }}</p>
                                <p class="text-xs text-amber-500">x{{ item.quantity }}</p>
                            </div>
                        </div>
                        <span class="font-medium text-amber-800">{{ formatPrice(item.price * item.quantity) }}</span>
                    </div>
                </div>

                <!-- Notes -->
                <div class="mt-6">
                    <label class="block text-sm font-medium text-amber-800">{{ t('checkout.notes') }}</label>
                    <textarea
                        v-model="notes"
                        :placeholder="t('checkout.notesPlaceholder')"
                        rows="3"
                        class="mt-1 w-full rounded-lg border border-amber-200 px-4 py-2 text-sm text-amber-900 placeholder:text-amber-300 focus:border-amber-400 focus:outline-none focus:ring-1 focus:ring-amber-400"
                    />
                </div>

                <!-- Total -->
                <div class="mt-6 flex items-center justify-between border-t border-amber-100 pt-4">
                    <span class="text-lg font-medium text-amber-800">{{ t('cart.total') }}</span>
                    <span class="text-2xl font-bold text-amber-900">{{ formatPrice(totalPrice) }}</span>
                </div>

                <!-- Place order button -->
                <button
                    class="mt-6 w-full rounded-lg bg-amber-800 py-3 font-medium text-white transition hover:bg-amber-900 disabled:opacity-50"
                    :disabled="isSubmitting || items.length === 0"
                    @click="placeOrder"
                >
                    {{ isSubmitting ? t('common.loading') : t('checkout.placeOrder') }}
                </button>
            </template>
        </div>
    </WebLayout>
</template>
