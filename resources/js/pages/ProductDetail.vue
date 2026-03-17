<script setup lang="ts">
import { ref, computed } from 'vue';
import { Link, usePage } from '@inertiajs/vue3';
import { useI18n } from 'vue-i18n';
import { ShoppingBag, ChevronLeft, ChevronRight, Minus, Plus, Clock, Ruler, Scissors } from 'lucide-vue-next';
import WebLayout from '@/layouts/WebLayout.vue';
import { useCart } from '@/composables/useCart';

const { t, locale } = useI18n();
const page = usePage();
const { addItem } = useCart();

interface ProductImage {
    id: number;
    url: string;
    alt: string | null;
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
        type_name_zh: string;
        type_name_en: string;
        size_info: Record<string, any> | null;
        stock: number;
        production_days: number | null;
        materials: { name_zh: string; name_en: string }[];
        colors: { name_zh: string; name_en: string; hex_code: string | null }[];
        images: ProductImage[];
        primary_image_url: string | null;
    };
}>();

const user = computed(() => page.props.auth?.user as any | null);
const currentImageIndex = ref(0);
const quantity = ref(1);
const addedFeedback = ref(false);

const name = computed(() =>
    locale.value === 'zh-TW'
        ? (props.product.name_zh || props.product.name_en)
        : (props.product.name_en || props.product.name_zh),
);

const description = computed(() =>
    locale.value === 'zh-TW'
        ? (props.product.description_zh || props.product.description_en)
        : (props.product.description_en || props.product.description_zh),
);

const typeName = computed(() =>
    locale.value === 'zh-TW'
        ? props.product.type_name_zh
        : props.product.type_name_en,
);
const isOutOfStock = computed(() => props.product.stock <= 0);

const priceFormatted = computed(() =>
    '€' + (props.product.price / 100).toFixed(2).replace('.', ','),
);

function prevImage() {
    if (props.product.images.length === 0) return;
    currentImageIndex.value =
        (currentImageIndex.value - 1 + props.product.images.length) % props.product.images.length;
}

function nextImage() {
    if (props.product.images.length === 0) return;
    currentImageIndex.value = (currentImageIndex.value + 1) % props.product.images.length;
}

function handleAddToCart() {
    addItem(
        {
            productId: props.product.id,
            slug: props.product.slug,
            nameZh: props.product.name_zh,
            nameEn: props.product.name_en,
            price: props.product.price,
            image: props.product.primary_image_url,
            stock: props.product.stock,
        },
        quantity.value,
    );
    addedFeedback.value = true;
    setTimeout(() => (addedFeedback.value = false), 1500);
}
</script>

<template>
    <WebLayout>
        <div class="mx-auto max-w-5xl px-5 py-8">
            <!-- Breadcrumb -->
            <Link
                href="/"
                class="mb-8 inline-flex items-center gap-1.5 text-sm font-bold text-[var(--color-sky)] transition hover:text-[var(--color-navy)]"
            >
                <ChevronLeft class="h-4 w-4" />
                {{ t('common.home') }}
            </Link>

            <div class="grid gap-10 md:grid-cols-2">
                <!-- Image gallery -->
                <div>
                    <div class="relative aspect-square overflow-hidden rounded-[var(--radius-xl)] bg-gradient-to-br from-[var(--color-cream)] to-[var(--color-gold-pale)]/30 shadow-[var(--shadow-card)]">
                        <img
                            v-if="product.images.length > 0"
                            :src="product.images[currentImageIndex].url"
                            :alt="product.images[currentImageIndex].alt || name"
                            class="h-full w-full object-cover"
                        />
                        <div v-else class="flex h-full w-full flex-col items-center justify-center gap-3">
                            <span class="text-6xl opacity-30">🧶</span>
                            <span class="text-xs font-bold tracking-widest text-[var(--color-navy-muted)]/30 uppercase">
                                {{ typeName }}
                            </span>
                        </div>

                        <template v-if="product.images.length > 1">
                            <button
                                class="absolute left-3 top-1/2 -translate-y-1/2 rounded-full bg-white/90 p-2 shadow-lg transition hover:bg-white hover:shadow-xl"
                                @click="prevImage"
                            >
                                <ChevronLeft class="h-5 w-5 text-[var(--color-navy)]" />
                            </button>
                            <button
                                class="absolute right-3 top-1/2 -translate-y-1/2 rounded-full bg-white/90 p-2 shadow-lg transition hover:bg-white hover:shadow-xl"
                                @click="nextImage"
                            >
                                <ChevronRight class="h-5 w-5 text-[var(--color-navy)]" />
                            </button>
                        </template>
                    </div>

                    <div v-if="product.images.length > 1" class="mt-4 flex gap-2.5">
                        <button
                            v-for="(img, idx) in product.images"
                            :key="img.id"
                            class="h-16 w-16 overflow-hidden rounded-[var(--radius-md)] border-2 transition"
                            :class="
                                idx === currentImageIndex
                                    ? 'border-[var(--color-gold)] shadow-[var(--shadow-gold)]'
                                    : 'border-transparent opacity-50 hover:opacity-80'
                            "
                            @click="currentImageIndex = idx"
                        >
                            <img :src="img.url" :alt="img.alt || ''" class="h-full w-full object-cover" />
                        </button>
                    </div>
                </div>

                <!-- Product info -->
                <div class="flex flex-col">
                    <span class="tag-coral inline-flex w-fit rounded-full px-3 py-0.5 text-[11px] font-bold tracking-[0.08em] uppercase shadow-sm">
                        {{ typeName }}
                    </span>

                    <h1 class="font-display mt-3 text-[1.8rem] font-bold leading-tight text-[var(--color-navy)]">
                        {{ name }}
                    </h1>

                    <p class="text-gold-shimmer font-display mt-3 text-3xl font-bold">
                        {{ priceFormatted }}
                    </p>

                    <div class="rainbow-line my-5 w-20" />

                    <div
                        v-if="description"
                        class="whitespace-pre-line text-[0.9rem] leading-relaxed text-[var(--color-navy-light)]"
                    >
                        {{ description }}
                    </div>

                    <!-- Details -->
                    <div class="mt-6 space-y-3">
                        <div v-if="product.materials.length > 0" class="flex items-start gap-3 text-sm">
                            <div class="rounded-lg bg-[var(--color-lavender-light)] p-1.5">
                                <Scissors class="h-4 w-4 text-[var(--color-lavender)]" />
                            </div>
                            <div>
                                <span class="font-bold text-[var(--color-navy)]">{{ t('product.material') }}</span>
                                <span class="ml-2 text-[var(--color-navy-light)]">
                                    {{ product.materials.map(m => locale === 'zh-TW' ? m.name_zh : m.name_en).join(', ') }}
                                </span>
                            </div>
                        </div>

                        <div v-if="product.colors.length > 0" class="flex items-center gap-3 text-sm">
                            <div class="flex h-8 w-8 shrink-0 items-center justify-center gap-0.5 rounded-lg bg-[var(--color-rose-light)] p-1.5">
                                <span
                                    v-for="c in product.colors.slice(0, 2)"
                                    :key="c.name"
                                    class="block h-3 w-3 rounded-full border border-white shadow-sm"
                                    :style="{ backgroundColor: c.hex_code || '#ddd' }"
                                />
                            </div>
                            <div>
                                <span class="font-bold text-[var(--color-navy)]">
                                    {{ locale === 'zh-TW' ? '顏色' : 'Colors' }}
                                </span>
                                <span class="ml-2 text-[var(--color-navy-light)]">
                                    {{ product.colors.map(c => locale === 'zh-TW' ? c.name_zh : c.name_en).join(', ') }}
                                </span>
                            </div>
                        </div>

                        <div v-if="product.size_info?.fits" class="flex items-start gap-3 text-sm">
                            <div class="rounded-lg bg-[var(--color-sage-light)] p-1.5">
                                <Ruler class="h-4 w-4 text-[var(--color-sage)]" />
                            </div>
                            <div>
                                <span class="font-bold text-[var(--color-navy)]">{{ t('product.fits') }}</span>
                                <span class="ml-2 text-[var(--color-navy-light)]">{{ product.size_info.fits }}</span>
                            </div>
                        </div>

                        <div v-if="product.size_info?.sizes" class="flex items-start gap-3 text-sm">
                            <div class="rounded-lg bg-[var(--color-sage-light)] p-1.5">
                                <Ruler class="h-4 w-4 text-[var(--color-sage)]" />
                            </div>
                            <div>
                                <span class="font-bold text-[var(--color-navy)]">{{ t('product.size') }}</span>
                                <span class="ml-2 text-[var(--color-navy-light)]">
                                    {{ product.size_info.sizes.join(' / ') }}
                                </span>
                            </div>
                        </div>

                        <div v-if="product.production_days" class="flex items-start gap-3 text-sm">
                            <div class="rounded-lg bg-[var(--color-sky-light)] p-1.5">
                                <Clock class="h-4 w-4 text-[var(--color-sky)]" />
                            </div>
                            <div>
                                <span class="font-bold text-[var(--color-navy)]">{{ t('product.productionTime') }}</span>
                                <span class="ml-2 text-[var(--color-navy-light)]">
                                    ~{{ product.production_days }} {{ t('product.days') }}
                                </span>
                            </div>
                        </div>
                    </div>

                    <!-- Add to cart -->
                    <div class="mt-8">
                        <template v-if="!isOutOfStock">
                            <div class="flex items-center gap-4">
                                <div class="flex items-center rounded-[var(--radius-md)] border-2 border-[var(--color-navy)]/10">
                                    <button
                                        class="px-3.5 py-2.5 text-[var(--color-navy-muted)] transition hover:text-[var(--color-navy)] disabled:opacity-30"
                                        :disabled="quantity <= 1"
                                        @click="quantity--"
                                    >
                                        <Minus class="h-4 w-4" />
                                    </button>
                                    <span class="min-w-[2.5rem] text-center text-sm font-bold text-[var(--color-navy)]">
                                        {{ quantity }}
                                    </span>
                                    <button
                                        class="px-3.5 py-2.5 text-[var(--color-navy-muted)] transition hover:text-[var(--color-navy)] disabled:opacity-30"
                                        :disabled="quantity >= product.stock"
                                        @click="quantity++"
                                    >
                                        <Plus class="h-4 w-4" />
                                    </button>
                                </div>

                                <button
                                    v-if="user"
                                    class="btn-gold flex flex-1 items-center justify-center gap-2.5 rounded-[var(--radius-md)] px-6 py-3 text-sm"
                                    @click="handleAddToCart"
                                >
                                    <ShoppingBag class="h-5 w-5" />
                                    {{ addedFeedback ? '✓ Added!' : t('product.addToCart') }}
                                </button>
                                <Link
                                    v-else
                                    href="/login"
                                    class="btn-navy flex flex-1 items-center justify-center gap-2.5 rounded-[var(--radius-md)] px-6 py-3 text-sm"
                                >
                                    {{ t('product.loginToBuy') }}
                                </Link>
                            </div>

                            <p
                                v-if="product.stock <= 5"
                                class="mt-3 inline-flex rounded-full bg-[var(--color-rose-light)] px-3 py-1 text-xs font-bold text-[var(--color-rose)]"
                            >
                                {{ t('product.stockLeft', { count: product.stock }) }}
                            </p>
                        </template>

                        <div
                            v-else
                            class="rounded-[var(--radius-md)] bg-[var(--color-cream-dark)] py-4 text-center font-bold text-[var(--color-navy-muted)]"
                        >
                            {{ t('product.soldOut') }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </WebLayout>
</template>