<script setup lang="ts">
import { ref, watch, computed } from 'vue';
import { router } from '@inertiajs/vue3';
import { useI18n } from 'vue-i18n';
import { customT } from '@/i18n';
import { Package, SlidersHorizontal, ChevronDown } from 'lucide-vue-next';
import WebLayout from '@/layouts/WebLayout.vue';
import ProductCard, { type ProductCardData } from '@/components/ProductCard.vue';
import FilterBar, {
    type FilterType,
    type FilterMaterial,
    type FilterColor,
} from '@/components/FilterBar.vue';
import logoFull from '@images/logo-full.png';

const { locale } = useI18n();
const t = customT;

const props = defineProps<{
    products: ProductCardData[];
    types: FilterType[];
    materials: FilterMaterial[];
    colors: FilterColor[];
    filters: {
        search?: string;
        type?: string;
        materials?: string[];
        colors?: string[];
    };
    content: Record<string, Record<string, string>>;
}>();

const search = ref(props.filters.search ?? '');
const selectedType = ref(props.filters.type ?? '');
const selectedMaterials = ref<string[]>(props.filters.materials ?? []);
const selectedColors = ref<string[]>(props.filters.colors ?? []);
const mobileFilterOpen = ref(false);

const activeFilterCount = computed(() => {
    let count = 0;
    if (search.value) count++;
    if (selectedType.value) count++;
    count += selectedMaterials.value.length;
    count += selectedColors.value.length;
    return count;
});

let searchTimeout: ReturnType<typeof setTimeout>;

function applyFilters() {
    router.get(
        '/',
        {
            search: search.value || undefined,
            type: selectedType.value || undefined,
            materials: selectedMaterials.value.length > 0 ? selectedMaterials.value : undefined,
            colors: selectedColors.value.length > 0 ? selectedColors.value : undefined,
        },
        { preserveState: true, preserveScroll: true },
    );
}

watch(search, () => {
    clearTimeout(searchTimeout);
    searchTimeout = setTimeout(applyFilters, 400);
});

watch([selectedType, selectedMaterials, selectedColors], applyFilters, { deep: true });

function clearAll() {
    search.value = '';
    selectedType.value = '';
    selectedMaterials.value = [];
    selectedColors.value = [];
    applyFilters();
}

const resultCount = computed(() => props.products.length);
const localizedContent = computed(() =>
    props.content[locale.value] || props.content['zh-TW'] || {},
);
</script>

<template>
    <WebLayout>
        <!-- Hero opening -->
        <section class="relative overflow-hidden px-5 pb-4 pt-10">
            <!-- Subtle gradient background -->
            <div class="absolute inset-0 bg-gradient-to-b from-[var(--color-gold-pale)]/40 via-[var(--color-cream)] to-[var(--color-cream)]" />

            <div class="relative mx-auto max-w-6xl text-center">
                <img
                    :src="logoFull"
                    alt="Amy's House"
                    class="mx-auto h-44 w-auto object-contain drop-shadow-md sm:h-52"
                />

                <div class="mt-6 space-y-1.5">
                    <p class="text-[0.95rem] leading-relaxed text-(--color-navy-light)">
                        {{ localizedContent['home.greeting'] || '' }}
                    </p>
                </div>

                <div class="rainbow-line mx-auto mt-8 w-48" />
            </div>
        </section>

        <!-- Filters + Products -->
        <section class="px-5 py-8">
            <div class="mx-auto max-w-6xl">
                <div class="grid gap-8 lg:grid-cols-[260px_1fr]">
                    <!-- Sidebar filters (desktop) -->
                    <aside class="hidden lg:block">
                        <div class="sticky top-[92px]">
                            <h2 class="font-display text-lg font-bold text-[var(--color-navy)]">
                                {{ t('common.filter') }}
                            </h2>
                            <div class="gold-line mb-4 mt-2 w-10" />
                            <FilterBar
                                :types="types"
                                :materials="materials"
                                :colors="colors"
                                :search="search"
                                :selected-type="selectedType"
                                :selected-materials="selectedMaterials"
                                :selected-colors="selectedColors"
                                @update:search="search = $event"
                                @update:selected-type="selectedType = $event"
                                @update:selected-materials="selectedMaterials = $event"
                                @update:selected-colors="selectedColors = $event"
                                @clear-all="clearAll"
                            />
                        </div>
                    </aside>

                    <!-- Mobile filters -->
                    <div class="lg:hidden">
                        <button
                            class="flex w-full items-center justify-between rounded-[var(--radius-lg)] border-2 border-[var(--color-gold)]/20 bg-white px-4 py-3 text-sm font-semibold text-[var(--color-navy)] shadow-[var(--shadow-soft)]"
                            @click="mobileFilterOpen = !mobileFilterOpen"
                        >
                            <span class="flex items-center gap-2">
                                <SlidersHorizontal class="h-4 w-4 text-[var(--color-gold)]" />
                                {{ t('common.filter') }}
                                <span
                                    v-if="activeFilterCount > 0"
                                    class="flex h-5 min-w-5 items-center justify-center rounded-full bg-[var(--color-rose)] px-1 text-[10px] font-bold text-white"
                                >
                                    {{ activeFilterCount }}
                                </span>
                            </span>
                            <ChevronDown
                                class="h-4 w-4 text-[var(--color-navy-muted)] transition-transform duration-200"
                                :class="mobileFilterOpen ? 'rotate-180' : ''"
                            />
                        </button>

                        <Transition
                            enter-active-class="transition duration-200 ease-out"
                            enter-from-class="opacity-0 -translate-y-2"
                            enter-to-class="opacity-100 translate-y-0"
                            leave-active-class="transition duration-150 ease-in"
                            leave-from-class="opacity-100 translate-y-0"
                            leave-to-class="opacity-0 -translate-y-2"
                        >
                            <div v-if="mobileFilterOpen" class="mt-3">
                                <FilterBar
                                    :types="types"
                                    :materials="materials"
                                    :colors="colors"
                                    :search="search"
                                    :selected-type="selectedType"
                                    :selected-materials="selectedMaterials"
                                    :selected-colors="selectedColors"
                                    @update:search="search = $event"
                                    @update:selected-type="selectedType = $event"
                                    @update:selected-materials="selectedMaterials = $event"
                                    @update:selected-colors="selectedColors = $event"
                                    @clear-all="clearAll"
                                />
                            </div>
                        </Transition>
                    </div>

                    <!-- Product grid -->
                    <div>
                        <p class="mb-5 text-xs font-bold tracking-widest text-[var(--color-navy-muted)] uppercase">
                            {{ resultCount }} {{ locale === 'zh-TW' ? '件商品' : 'items' }}
                        </p>

                        <div
                            v-if="products.length > 0"
                            class="grid grid-cols-2 gap-5 sm:gap-6 md:grid-cols-3"
                        >
                            <ProductCard
                                v-for="(product, idx) in products"
                                :key="product.id"
                                :product="product"
                                :index="idx"
                            />
                        </div>

                        <div
                            v-else
                            class="flex flex-col items-center justify-center rounded-[var(--radius-xl)] border-2 border-dashed border-[var(--color-gold)]/30 bg-white py-20"
                        >
                            <Package class="h-12 w-12 text-[var(--color-gold)]" />
                            <p class="font-display mt-4 text-xl font-semibold text-[var(--color-navy-muted)]">
                                {{ t('common.noResults') }}
                            </p>
                            <button
                                class="btn-gold mt-4 rounded-full px-6 py-2 text-sm"
                                @click="clearAll"
                            >
                                {{ locale === 'zh-TW' ? '清除篩選條件' : 'Clear all filters' }}
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </WebLayout>
</template>