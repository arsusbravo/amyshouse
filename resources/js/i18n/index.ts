import { createI18n } from 'vue-i18n';
import zhTW from './zh-TW.json';
import en from './en.json';

const STORAGE_KEY = 'amyshouse-locale';

function getStoredLocale(): string {
    if (typeof window !== 'undefined') {
        return localStorage.getItem(STORAGE_KEY) || 'zh-TW';
    }
    return 'zh-TW';
}

export function setStoredLocale(locale: string): void {
    localStorage.setItem(STORAGE_KEY, locale);
    document.documentElement.lang = locale;
}

const i18n = createI18n({
    legacy: false,
    globalInjection: true,
    locale: getStoredLocale(),
    fallbackLocale: 'zh-TW',
    messages: {
        'zh-TW': (zhTW as any).default || zhTW,
        'en': (en as any).default || en,
    },
});

// Use "as any" to stop the "excessively deep" type calculation
export const t = (i18n.global.t as any);

// For the locale, we cast it to a basic Ref so it stays reactive
import { type Ref } from 'vue';
export const locale = (i18n.global.locale as unknown as Ref<string>);

export default i18n;
