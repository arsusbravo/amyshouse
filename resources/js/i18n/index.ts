import { createI18n } from 'vue-i18n';
import zhTW from './zh-TW.json';
import en from './en.json';

const STORAGE_KEY = 'amyshouse-locale';

// Keep the raw data in a simple object for our manual resolver
const messages: Record<string, any> = { 
    'zh-TW': zhTW, 
    'en': en 
};

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

/**
 * Fail-safe translation function
 * Bypasses Vue-i18n's runtime resolver which often fails in production bundles
 */
export function customT(path: string): string {
    // Access the current reactive locale
    const currentLocale = i18n.global.locale.value;
    
    // Split the path (e.g., 'common.home' -> ['common', 'home'])
    const keys = path.split('.');
    
    // Start at the root of the current language's messages
    let result = messages[currentLocale] || messages['zh-TW'];

    for (const key of keys) {
        if (result && Object.prototype.hasOwnProperty.call(result, key)) {
            result = result[key];
        } else {
            // Fallback to the default i18n t() if manual lookup fails
            // or just return the key if all else fails
            try {
                const fallback = i18n.global.t(path);
                return fallback !== path ? fallback : path;
            } catch {
                return path;
            }
        }
    }

    return typeof result === 'string' ? result : path;
}

export default i18n;