<script setup lang="ts">
import '../../css/shop.css';
import { ref, computed } from 'vue';
import { Link, usePage } from '@inertiajs/vue3';
import { useI18n } from 'vue-i18n';
import { ShoppingBag, Globe, LogOut, Settings, Menu, X } from 'lucide-vue-next';
import { useCart } from '@/composables/useCart';
import { setStoredLocale } from '@/i18n';
import logoSm from '@images/logo-sm.png';

const { t, locale } = useI18n();
const page = usePage();
const { totalItems } = useCart();

const user = computed(() => page.props.auth?.user as any | null);
const isAdmin = computed(() => user.value?.is_admin ?? false);
const mobileOpen = ref(false);
const siteContent = computed(() => {
    const all = page.props.siteContent as Record<string, Record<string, string>> | undefined;
    return all?.[locale.value] || all?.['zh-TW'] || {};
});

console.log('Locale:', locale.value);
console.log('languages:', t('common.home'));

function toggleLocale() {
    const next = locale.value === 'zh-TW' ? 'en' : 'zh-TW';
    locale.value = next;
    setStoredLocale(next);
}
</script>

<template>
    <div class="shop-layout flex min-h-screen flex-col bg-[var(--color-cream)] bg-texture">
        <!-- Header -->
        <header class="sticky top-0 z-50 border-b border-[var(--color-gold)]/15 bg-[var(--color-navy)] shadow-lg">
            <div class="mx-auto flex h-[68px] max-w-6xl items-center justify-between px-5">
                <!-- Logo -->
                <Link href="/" class="flex items-center gap-3 transition-opacity hover:opacity-85">
                    <img
                        :src="logoSm"
                        alt="Amy's House"
                        class="h-10 w-10 rounded-full ring-2 ring-[var(--color-gold)]/40"
                    />
                    <span class="text-gold-shimmer font-display hidden text-[1.3rem] font-bold tracking-wide sm:block">
                        Amy's House
                    </span>
                </Link>

                <!-- Desktop nav -->
                <nav class="hidden items-center gap-0.5 md:flex">
                    <Link
                        href="/"
                        class="rounded-lg px-4 py-2 text-[0.82rem] font-semibold tracking-wide text-white/75 transition hover:bg-white/10 hover:text-white"
                    >
                        {{ t('common.home') }}
                    </Link>
                    <Link
                        href="/about"
                        class="rounded-lg px-4 py-2 text-[0.82rem] font-semibold tracking-wide text-white/75 transition hover:bg-white/10 hover:text-white"
                    >
                        {{ t('common.about') }}
                    </Link>

                    <div class="mx-2 h-5 w-px bg-white/15" />

                    <button
                        class="flex items-center gap-1.5 rounded-lg px-3 py-2 text-[0.8rem] font-medium text-[var(--color-gold-light)] transition hover:bg-white/10 hover:text-[var(--color-gold-bright)]"
                        @click="toggleLocale"
                    >
                        <Globe class="h-[15px] w-[15px]" />
                        {{ locale === 'zh-TW' ? 'EN' : '中文' }}
                    </button>

                    <!-- Cart -->
                    <Link
                        v-if="user"
                        href="/cart"
                        class="relative ml-1 rounded-lg p-2.5 text-white/75 transition hover:bg-white/10 hover:text-white"
                    >
                        <ShoppingBag class="h-[18px] w-[18px]" />
                        <span
                            v-if="totalItems > 0"
                            class="absolute -right-1 -top-1 flex h-5 min-w-5 items-center justify-center rounded-full bg-[var(--color-rose)] px-1 text-[10px] font-bold text-white shadow-[var(--shadow-rose)]"
                        >
                            {{ totalItems }}
                        </span>
                    </Link>

                    <template v-if="user">
                        <Link
                            href="/orders"
                            class="rounded-lg px-3 py-2 text-[0.82rem] font-semibold tracking-wide text-white/75 transition hover:bg-white/10 hover:text-white"
                        >
                            {{ t('common.myOrders') }}
                        </Link>
                        <Link
                            v-if="isAdmin"
                            href="/admin"
                            class="rounded-lg p-2.5 text-white/50 transition hover:bg-white/10 hover:text-white"
                        >
                            <Settings class="h-[15px] w-[15px]" />
                        </Link>
                        <Link
                            href="/logout"
                            method="post"
                            as="button"
                            class="rounded-lg p-2.5 text-white/50 transition hover:bg-[var(--color-rose)]/20 hover:text-[var(--color-rose)]"
                        >
                            <LogOut class="h-[15px] w-[15px]" />
                        </Link>
                    </template>
                    <Link
                        v-else
                        href="/login"
                        class="btn-gold ml-2 rounded-lg px-5 py-2 text-[0.8rem] tracking-wide"
                    >
                        {{ t('common.login') }}
                    </Link>
                </nav>

                <!-- Mobile -->
                <div class="flex items-center gap-1.5 md:hidden">
                    <button
                        class="rounded-lg p-2 text-[var(--color-gold-light)] transition hover:bg-white/10"
                        @click="toggleLocale"
                    >
                        <Globe class="h-4 w-4" />
                    </button>
                    <Link
                        v-if="user"
                        href="/cart"
                        class="relative rounded-lg p-2 text-white/75 transition hover:bg-white/10"
                    >
                        <ShoppingBag class="h-[18px] w-[18px]" />
                        <span
                            v-if="totalItems > 0"
                            class="absolute -right-1 -top-1 flex h-5 min-w-5 items-center justify-center rounded-full bg-[var(--color-rose)] px-1 text-[10px] font-bold text-white"
                        >
                            {{ totalItems }}
                        </span>
                    </Link>
                    <button
                        class="rounded-lg p-2 text-white transition hover:bg-white/10"
                        @click="mobileOpen = !mobileOpen"
                    >
                        <Menu v-if="!mobileOpen" class="h-5 w-5" />
                        <X v-else class="h-5 w-5" />
                    </button>
                </div>
            </div>

            <!-- Mobile menu -->
            <Transition
                enter-active-class="transition duration-200 ease-out"
                enter-from-class="opacity-0 -translate-y-1"
                enter-to-class="opacity-100 translate-y-0"
                leave-active-class="transition duration-150 ease-in"
                leave-from-class="opacity-100 translate-y-0"
                leave-to-class="opacity-0 -translate-y-1"
            >
                <div
                    v-if="mobileOpen"
                    class="border-t border-white/10 bg-[var(--color-navy-light)] px-5 pb-5 pt-3 md:hidden"
                >
                    <nav class="flex flex-col gap-0.5">
                        <Link href="/" class="rounded-lg px-4 py-3 text-sm font-semibold text-white transition hover:bg-white/10" @click="mobileOpen = false">
                            {{ t('common.home') }}
                        </Link>
                        <Link href="/about" class="rounded-lg px-4 py-3 text-sm font-semibold text-white transition hover:bg-white/10" @click="mobileOpen = false">
                            {{ t('common.about') }}
                        </Link>
                        <template v-if="user">
                            <Link href="/orders" class="rounded-lg px-4 py-3 text-sm font-semibold text-white transition hover:bg-white/10" @click="mobileOpen = false">
                                {{ t('common.myOrders') }}
                            </Link>
                            <Link v-if="isAdmin" href="/admin" class="rounded-lg px-4 py-3 text-sm font-semibold text-white transition hover:bg-white/10" @click="mobileOpen = false">
                                {{ t('common.admin') }}
                            </Link>
                            <div class="rainbow-line my-2" />
                            <Link href="/logout" method="post" as="button" class="rounded-lg px-4 py-3 text-left text-sm text-white/60 transition hover:bg-[var(--color-rose)]/20 hover:text-[var(--color-rose)]" @click="mobileOpen = false">
                                {{ t('common.logout') }}
                            </Link>
                        </template>
                        <template v-else>
                            <div class="rainbow-line my-2" />
                            <Link href="/login" class="btn-gold rounded-lg px-4 py-3 text-center text-sm" @click="mobileOpen = false">
                                {{ t('common.login') }}
                            </Link>
                        </template>
                    </nav>
                </div>
            </Transition>
        </header>

        <!-- Page content -->
        <main class="flex-1">
            <slot />
        </main>

        <!-- Footer -->
        <footer class="bg-[var(--color-navy)] text-white">
            <div class="mx-auto max-w-6xl px-5 py-10">
                <div class="rainbow-line mx-auto mb-8 w-32" />
                <div class="text-center">
                    <p class="text-gold-shimmer font-display text-xl font-bold">
                        {{ t('common.siteName') }}
                    </p>
                    <p class="mt-2 text-[0.82rem] tracking-wide text-white/50">
                        {{ siteContent['footer.tagline'] || t('about.subtitle') }}
                    </p>
                    <p class="mt-5 text-xs text-white/30">
                        © {{ new Date().getFullYear() }} Amy's House. Handcrafted with love.
                    </p>
                </div>
            </div>
        </footer>
    </div>
</template>