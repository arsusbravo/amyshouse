<script setup lang="ts">
import { computed } from 'vue';
import { useI18n } from 'vue-i18n';
import { Search, X } from 'lucide-vue-next';

const { t, locale } = useI18n();

export interface FilterType {
    id: number;
    slug: string;
    name_zh: string;
    name_en: string;
}

export interface FilterColor {
    id: number;
    slug: string;
    name_zh: string;
    name_en: string;
    hex_code: string | null;
}

export interface FilterMaterial {
    id: number;
    slug: string;
    name_zh: string;
    name_en: string;
}

const props = defineProps<{
    types: FilterType[];
    materials: FilterMaterial[];
    colors: FilterColor[];
    search: string;
    selectedType: string;
    selectedMaterials: string[];
    selectedColors: string[];
}>();

const emit = defineEmits<{
    'update:search': [value: string];
    'update:selectedType': [value: string];
    'update:selectedMaterials': [value: string[]];
    'update:selectedColors': [value: string[]];
    'clear-all': [];
}>();

function typeName(type: FilterType): string {
    return locale.value === 'zh-TW' ? type.name_zh : type.name_en;
}

function materialName(m: FilterMaterial): string {
    return locale.value === 'zh-TW' ? m.name_zh : m.name_en;
}

function colorName(c: FilterColor): string {
    return locale.value === 'zh-TW' ? c.name_zh : c.name_en;
}

function toggleMaterial(slug: string) {
    const current = [...props.selectedMaterials];
    const idx = current.indexOf(slug);
    if (idx >= 0) current.splice(idx, 1);
    else current.push(slug);
    emit('update:selectedMaterials', current);
}

function toggleColor(slug: string) {
    const current = [...props.selectedColors];
    const idx = current.indexOf(slug);
    if (idx >= 0) current.splice(idx, 1);
    else current.push(slug);
    emit('update:selectedColors', current);
}

const hasActiveFilters = computed(() =>
    props.search || props.selectedType || props.selectedMaterials.length > 0 || props.selectedColors.length > 0,
);
</script>

<template>
    <div class="space-y-5">
        <!-- Search -->
        <div class="relative">
            <Search class="absolute left-4 top-1/2 h-4 w-4 -translate-y-1/2 text-[var(--color-navy-muted)]" />
            <input
                :value="search"
                type="text"
                :placeholder="t('common.search')"
                class="w-full rounded-[var(--radius-lg)] border-2 border-[var(--color-gold)]/20 bg-white py-2.5 pl-11 pr-4 text-sm text-[var(--color-navy)] shadow-[var(--shadow-soft)] placeholder:text-[var(--color-navy-muted)]/50 transition focus:border-[var(--color-gold)] focus:outline-none focus:ring-3 focus:ring-[var(--color-gold)]/15 focus:shadow-[var(--shadow-glow)]"
                @input="emit('update:search', ($event.target as HTMLInputElement).value)"
            />
        </div>

        <!-- Type pills -->
        <div class="flex flex-wrap items-center gap-2">
            <button
                class="rounded-full px-4 py-1.5 text-[0.78rem] font-bold tracking-wide shadow-sm transition"
                :class="
                    selectedType === ''
                        ? 'bg-[var(--color-navy)] text-white shadow-md filter-pill-active'
                        : 'bg-white text-[var(--color-navy-muted)] hover:bg-[var(--color-gold-pale)] hover:text-[var(--color-navy)]'
                "
                @click="emit('update:selectedType', '')"
            >
                {{ t('common.all') }}
            </button>
            <button
                v-for="type in types"
                :key="type.slug"
                class="rounded-full px-4 py-1.5 text-[0.78rem] font-bold tracking-wide shadow-sm transition"
                :class="
                    selectedType === type.slug
                        ? 'bg-[var(--color-navy)] text-white shadow-md filter-pill-active'
                        : 'bg-white text-[var(--color-navy-muted)] hover:bg-[var(--color-gold-pale)] hover:text-[var(--color-navy)]'
                "
                @click="emit('update:selectedType', type.slug)"
            >
                {{ typeName(type) }}
            </button>
        </div>

        <!-- Material pills -->
        <div v-if="materials.length > 0">
            <span class="mb-2 block text-[10px] font-bold tracking-[0.12em] text-[var(--color-navy-muted)] uppercase">
                {{ t('product.material') }}
            </span>
            <div class="flex flex-wrap items-center gap-2">
                <button
                    v-for="m in materials"
                    :key="m.slug"
                    class="rounded-full border-2 px-3.5 py-1 text-[0.75rem] font-semibold transition"
                    :class="
                        selectedMaterials.includes(m.slug)
                            ? 'border-[var(--color-lavender)] bg-[var(--color-lavender)] text-white shadow-sm filter-pill-active'
                            : 'border-[var(--color-cream-dark)] bg-white text-[var(--color-navy-muted)] hover:border-[var(--color-lavender)] hover:text-[var(--color-lavender)]'
                    "
                    @click="toggleMaterial(m.slug)"
                >
                    {{ materialName(m) }}
                </button>
            </div>
        </div>

        <!-- Color dots -->
        <div v-if="colors.length > 0">
            <span class="mb-2 block text-[10px] font-bold tracking-[0.12em] text-[var(--color-navy-muted)] uppercase">
                {{ locale === 'zh-TW' ? '顏色' : 'Color' }}
            </span>
            <div class="flex flex-wrap items-center gap-2.5">
                <button
                    v-for="c in colors"
                    :key="c.slug"
                    class="group relative h-8 w-8 rounded-full transition-transform hover:scale-110"
                    :class="selectedColors.includes(c.slug) ? 'scale-110 ring-3 ring-[var(--color-navy)] ring-offset-2 ring-offset-[var(--color-cream)]' : 'ring-1 ring-black/5'"
                    :title="colorName(c)"
                    @click="toggleColor(c.slug)"
                >
                    <span
                        class="block h-full w-full rounded-full border-2 border-white shadow-md"
                        :style="{ backgroundColor: c.hex_code || 'var(--color-cream-dark)' }"
                    />
                </button>
            </div>
        </div>

        <!-- Clear -->
        <button
            v-if="hasActiveFilters"
            class="flex items-center gap-1.5 rounded-full bg-[var(--color-rose)] px-4 py-1.5 text-[0.75rem] font-bold text-white shadow-sm transition hover:bg-[var(--color-rose-deep)] hover:shadow-[var(--shadow-rose)]"
            @click="emit('clear-all')"
        >
            <X class="h-3 w-3" />
            {{ locale === 'zh-TW' ? '清除篩選' : 'Clear filters' }}
        </button>
    </div>
</template>