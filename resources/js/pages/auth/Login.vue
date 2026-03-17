<script setup lang="ts">
import { useForm } from '@inertiajs/vue3';
import { useI18n } from 'vue-i18n';
import WebLayout from '@/layouts/WebLayout.vue';
import logoSm from '@images/logo-sm.png';

const { t } = useI18n();

const form = useForm({
    email: '',
    password: '',
});

function submit() {
    form.post('/login', {
        onFinish: () => form.reset('password'),
    });
}
</script>

<template>
    <WebLayout>
        <div class="flex min-h-[60vh] items-center justify-center px-5 py-16">
            <div class="w-full max-w-sm">
                <div class="mb-8 text-center">
                    <img
                        :src="logoSm"
                        alt="Amy's House"
                        class="mx-auto h-16 w-16 rounded-full ring-3 ring-[var(--color-gold)]/30 shadow-[var(--shadow-gold)]"
                    />
                    <h1 class="font-display mt-4 text-2xl font-bold text-[var(--color-navy)]">
                        {{ t('auth.loginTitle') }}
                    </h1>
                    <div class="rainbow-line mx-auto mt-3 w-16" />
                </div>

                <div class="rounded-[var(--radius-xl)] border-2 border-[var(--color-gold)]/15 bg-white p-6 shadow-[var(--shadow-card)]">
                    <div class="space-y-4">
                        <div>
                            <label class="mb-1.5 block text-xs font-bold tracking-widest text-[var(--color-navy-muted)] uppercase">
                                {{ t('auth.email') }}
                            </label>
                            <input
                                v-model="form.email"
                                type="email"
                                autocomplete="email"
                                class="w-full rounded-[var(--radius-md)] border-2 border-[var(--color-cream-dark)] bg-[var(--color-cream)] px-4 py-2.5 text-sm text-[var(--color-navy)] transition focus:border-[var(--color-gold)] focus:outline-none focus:ring-3 focus:ring-[var(--color-gold)]/15"
                                @keyup.enter="submit"
                            />
                        </div>

                        <div>
                            <label class="mb-1.5 block text-xs font-bold tracking-widest text-[var(--color-navy-muted)] uppercase">
                                {{ t('auth.password') }}
                            </label>
                            <input
                                v-model="form.password"
                                type="password"
                                autocomplete="current-password"
                                class="w-full rounded-[var(--radius-md)] border-2 border-[var(--color-cream-dark)] bg-[var(--color-cream)] px-4 py-2.5 text-sm text-[var(--color-navy)] transition focus:border-[var(--color-gold)] focus:outline-none focus:ring-3 focus:ring-[var(--color-gold)]/15"
                                @keyup.enter="submit"
                            />
                        </div>

                        <p v-if="form.errors.email" class="rounded-lg bg-[var(--color-rose-light)] px-3 py-2 text-xs font-bold text-[var(--color-rose)]">
                            {{ form.errors.email }}
                        </p>

                        <button
                            class="btn-gold w-full rounded-[var(--radius-md)] py-3 text-sm tracking-wide"
                            :disabled="form.processing"
                            @click="submit"
                        >
                            {{ form.processing ? t('common.loading') : t('auth.loginButton') }}
                        </button>
                    </div>
                </div>

                <p class="mt-5 text-center text-xs text-[var(--color-navy-muted)]">
                    {{ t('auth.noAccount') }}
                </p>
            </div>
        </div>
    </WebLayout>
</template>