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
        // This "cleans" the JSON objects for the production bundler
        'zh-TW': JSON.parse(JSON.stringify(zhTW)),
        'en': JSON.parse(JSON.stringify(en)),
    },
});

export default i18n;
