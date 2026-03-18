<script setup lang="ts">
import { computed } from 'vue';
import { useI18n } from 'vue-i18n';
import { customT } from '@/i18n';
import WebLayout from '@/layouts/WebLayout.vue';
import logoFull from '@images/logo-full.png';

const { locale } = useI18n();
const t = customT;

const props = defineProps<{
    content: Record<string, Record<string, string>>;
}>();

const c = computed(() =>
    props.content[locale.value] || props.content['zh-TW'] || {},
);
</script>

<template>
    <WebLayout>
        <div class="mx-auto max-w-2xl px-5 py-16">
            <img
                :src="logoFull"
                alt="Amy's House"
                class="mx-auto h-44 w-auto object-contain drop-shadow-md"
            />

            <h1 class="font-display mt-8 text-center text-3xl font-bold text-[var(--color-navy)]">
                {{ t('about.title') }}
            </h1>
            <div class="rainbow-line mx-auto mt-4 w-24" />

            <div class="mt-10 space-y-6 text-[0.95rem] leading-relaxed text-[var(--color-navy-light)]">
                <p v-if="c['about.intro']">{{ c['about.intro'] }}</p>
                <p v-if="c['about.story']" class="whitespace-pre-line">{{ c['about.story'] }}</p>
                <p v-if="c['about.philosophy']">{{ c['about.philosophy'] }}</p>
                <p v-if="c['about.closing']" class="text-gold-shimmer font-display text-xl font-bold">
                    {{ c['about.closing'] }}
                </p>
            </div>
        </div>
    </WebLayout>
</template>