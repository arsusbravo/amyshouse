import { createI18n } from 'vue-i18n';
import zhTW from './zh-TW.json';
import en from './en.json';

const STORAGE_KEY = 'amyshouse-locale';
const messages: Record<string, any> = { 'zh-TW': zhTW, en };

export function getStoredLocale(): string {
    if (typeof window !== 'undefined') {
        return localStorage.getItem(STORAGE_KEY) || 'zh-TW';
    }
    return 'zh-TW';
}

export function setStoredLocale(locale: string): void {
    localStorage.setItem(STORAGE_KEY, locale);
    if (typeof document !== 'undefined') {
        document.documentElement.lang = locale;
    }
}

const i18n = createI18n({
    legacy: false,
    globalInjection: true,
    locale: getStoredLocale(),
    fallbackLocale: 'zh-TW',
    messages: messages,
});

// THE FAIL-SAFE FUNCTION
export function customT(path: string): string {
    const currentLocale = i18n.global.locale.value;
    const keys = path.split('.');
    let result = messages[currentLocale];

    for (const key of keys) {
        if (result && result[key]) {
            result = result[key];
        } else {
            // If manual lookup fails, try fallback to the actual t() function
            return i18n.global.t(path);
        }
    }
    return typeof result === 'string' ? result : path;
}

export default i18n;