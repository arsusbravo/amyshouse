import { createI18n } from 'vue-i18n';
import zhTW from './zh-TW.json';
import en from './en.json';

console.log('Traditional Chinese keys:', Object.keys(zhTW));
console.log('English full content:', en);

const STORAGE_KEY = 'amyshouse-locale';

function getStoredLocale(): string {
    console.log('Get locale from localStorage:', typeof window !== 'undefined' ? localStorage.getItem(STORAGE_KEY) : 'window not defined');
    if (typeof window !== 'undefined') {
        return localStorage.getItem(STORAGE_KEY) || 'zh-TW';
    }
    return 'zh-TW';
}

export function setStoredLocale(locale: string): void {
    localStorage.setItem(STORAGE_KEY, locale);
    document.documentElement.lang = locale;
    console.log(`Locale set to ${locale} and stored in localStorage.`);
}

const i18n = createI18n({
    legacy: false,
    locale: getStoredLocale(),
    fallbackLocale: 'zh-TW',
    messages: {
        'zh-TW': zhTW,
        en,
    },
});

export default i18n;
