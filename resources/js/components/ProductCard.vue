<script setup lang="ts">
import { computed } from 'vue';
import { Link, usePage } from '@inertiajs/vue3';
import { useI18n } from 'vue-i18n';
import { ShoppingBag } from 'lucide-vue-next';
import { useCart } from '@/composables/useCart';

const { t, locale } = useI18n();
const page = usePage();
const { addItem } = useCart();

export interface ProductCardData {
    id: number;
    slug: string;
    name_zh: string;
    name_en: string;
    price: number;
    type_name_zh: string;
    type_name_en: string;
    type_slug?: string;
    stock: number;
    primary_image_url: string | null;
    colors: { hex_code: string | null; name_zh: string; name_en: string }[];
}

const props = defineProps<{
    product: ProductCardData;
    index?: number;
}>();

const user = computed(() => page.props.auth?.user as any | null);
const name = computed(() =>
    locale.value === 'zh-TW'
        ? (props.product.name_zh || props.product.name_en)
        : (props.product.name_en || props.product.name_zh),
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

const animationDelay = computed(() =>
    `animation-delay: ${(props.index ?? 0) * 60}ms`,
);

// Bold tag colors that rotate
const tagColors = ['tag-rose', 'tag-coral', 'tag-sage', 'tag-lavender', 'tag-sky', 'tag-fuchsia', 'tag-gold'];
const tagClass = computed(() => tagColors[props.product.id % tagColors.length]);

function handleAddToCart(e: Event) {
    e.preventDefault();
    e.stopPropagation();
    if (!user.value || isOutOfStock.value) return;

    addItem({
        productId: props.product.id,
        slug: props.product.slug,
        nameZh: props.product.name_zh,
        nameEn: props.product.name_en,
        price: props.product.price,
        image: props.product.primary_image_url,
        stock: props.product.stock,
    });
}
</script>

<template>
    <Link
        :href="`/product/${product.slug}`"
        class="card-enter transition-card gold-border-hover group relative flex flex-col overflow-hidden rounded-[var(--radius-xl)] bg-[var(--color-warm-white)] shadow-[var(--shadow-card)] hover:-translate-y-2 hover:shadow-[var(--shadow-card-hover)]"
        :style="animationDelay"
    >
        <!-- Image -->
        <div class="relative aspect-[4/5] overflow-hidden bg-gradient-to-br from-[var(--color-cream)] via-[var(--color-gold-pale)]/20 to-[var(--color-cream)]">
            <img
                v-if="product.primary_image_url"
                :src="product.primary_image_url"
                :alt="name"
                class="h-full w-full object-cover transition-transform duration-500 ease-out group-hover:scale-[1.07]"
            />
            <!-- Placeholder -->
            <div
                v-else
                class="flex h-full w-full flex-col items-center justify-center gap-2"
            >
                <span class="text-5xl opacity-30">🧶</span>
                <span class="text-xs font-medium tracking-widest text-[var(--color-navy-muted)]/30 uppercase">
                    {{ typeName }}
                </span>
            </div>

            <!-- Sold out overlay -->
            <div
                v-if="isOutOfStock"
                class="absolute inset-0 flex items-center justify-center bg-[var(--color-navy)]/30 backdrop-blur-[3px]"
            >
                <span class="rounded-full bg-[var(--color-warm-white)] px-5 py-2 text-xs font-bold tracking-widest text-[var(--color-navy)] uppercase shadow-lg">
                    {{ t('product.soldOut') }}
                </span>
            </div>

            <!-- Low stock badge -->
            <span
                v-else-if="product.stock > 0 && product.stock <= 3"
                class="absolute left-3 top-3 rounded-full bg-[var(--color-rose)] px-3 py-1 text-[10px] font-bold tracking-wide text-white shadow-[var(--shadow-rose)]"
            >
                {{ t('product.stockLeft', { count: product.stock }) }}
            </span>

            <!-- Quick add — gold gradient button -->
            <button
                v-if="user && !isOutOfStock"
                class="btn-gold absolute bottom-3 right-3 translate-y-2 rounded-full p-2.5 opacity-0 transition-all duration-200 group-hover:translate-y-0 group-hover:opacity-100"
                :title="t('product.addToCart')"
                @click="handleAddToCart"
            >
                <ShoppingBag class="h-4 w-4" />
            </button>
        </div>

        <!-- Info -->
        <div class="flex flex-1 flex-col gap-2 px-4 pb-4 pt-3">
            <!-- Type label — bold color pill -->
            <span
                class="inline-flex w-fit rounded-full px-3 py-0.5 text-[10px] font-bold tracking-[0.06em] uppercase shadow-sm"
                :class="tagClass"
            >
                {{ typeName }}
            </span>

            <!-- Product name -->
            <h3 class="font-display text-[1.05rem] font-semibold leading-snug text-[var(--color-navy)] line-clamp-2">
                {{ name }}
            </h3>

            <!-- Color dots -->
            <div v-if="product.colors.length > 0" class="mt-0.5 flex items-center gap-1.5">
                <span
                    v-for="color in product.colors.slice(0, 5)"
                    :key="color.name"
                    class="h-4 w-4 rounded-full border-2 border-white shadow-md"
                    :style="{ backgroundColor: color.hex_code || 'var(--color-cream-dark)' }"
                    :title="locale === 'zh-TW' ? color.name_zh : color.name_en"
                />
                <span
                    v-if="product.colors.length > 5"
                    class="text-[10px] font-bold text-[var(--color-navy-muted)]"
                >
                    +{{ product.colors.length - 5 }}
                </span>
            </div>

            <!-- Price — gold shimmer -->
            <div class="mt-auto flex items-center justify-between pt-2">
                <span class="text-gold-shimmer font-display text-xl font-bold">
                    {{ priceFormatted }}
                </span>
            </div>
        </div>
    </Link>
</template>