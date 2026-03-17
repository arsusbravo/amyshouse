<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class SiteContent extends Model
{
    protected $fillable = ['key', 'locale', 'content', 'label', 'type', 'sort_order'];

    /**
     * Get content by key and locale, with fallback to zh-TW.
     */
    public static function get(string $key, string $locale = 'zh-TW', string $default = ''): string
    {
        $cacheKey = "site_content.{$key}.{$locale}";

        return Cache::remember($cacheKey, 3600, function () use ($key, $locale, $default) {
            $content = static::where('key', $key)->where('locale', $locale)->first();

            if (! $content && $locale !== 'zh-TW') {
                $content = static::where('key', $key)->where('locale', 'zh-TW')->first();
            }

            return $content?->content ?? $default;
        });
    }

    /**
     * Get all content for a locale, keyed by content key.
     */
    public static function allForLocale(string $locale = 'zh-TW'): array
    {
        $cacheKey = "site_content.all.{$locale}";

        return Cache::remember($cacheKey, 3600, function () use ($locale) {
            $contents = static::where('locale', $locale)->pluck('content', 'key')->toArray();
            $fallbacks = static::where('locale', 'zh-TW')->pluck('content', 'key')->toArray();

            return array_merge($fallbacks, $contents);
        });
    }

    /**
     * Clear cache when content is updated.
     */
    public static function clearCache(): void
    {
        Cache::forget('site_content.all.zh-TW');
        Cache::forget('site_content.all.en');

        static::select('key', 'locale')->each(function ($item) {
            Cache::forget("site_content.{$item->key}.{$item->locale}");
        });
    }
}