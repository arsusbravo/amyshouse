<?php

namespace App\Http\Controllers\Admin\Api;

use App\Http\Controllers\Controller;
use App\Models\SiteContent;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ContentController extends Controller
{
    public function update(Request $request)
    {
        $validated = $request->validate([
            'items' => 'required|array',
            'items.*.key' => 'required|string|max:255',
            'items.*.zh_TW' => 'required|string',
            'items.*.en' => 'nullable|string',
            'items.*.label' => 'nullable|string|max:255',
            'items.*.type' => 'in:text,textarea',
            'items.*.sort_order' => 'integer|min:0',
        ]);

        foreach ($validated['items'] as $item) {
            SiteContent::updateOrCreate(
                ['key' => $item['key'], 'locale' => 'zh-TW'],
                [
                    'content' => $item['zh_TW'],
                    'label' => $item['label'] ?? Str::title(str_replace('.', ' - ', $item['key'])),
                    'type' => $item['type'] ?? 'text',
                    'sort_order' => $item['sort_order'] ?? 0,
                ],
            );

            SiteContent::updateOrCreate(
                ['key' => $item['key'], 'locale' => 'en'],
                [
                    'content' => $item['en'] ?? '',
                    'label' => $item['label'] ?? Str::title(str_replace('.', ' - ', $item['key'])),
                    'type' => $item['type'] ?? 'text',
                    'sort_order' => $item['sort_order'] ?? 0,
                ],
            );
        }

        SiteContent::clearCache();

        return back()->with('success', 'Content updated.');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'key' => 'required|string|max:255|unique:site_contents,key',
            'label' => 'required|string|max:255',
            'type' => 'in:text,textarea',
            'zh_TW' => 'required|string',
            'en' => 'nullable|string',
            'sort_order' => 'integer|min:0',
        ]);

        SiteContent::create([
            'key' => $validated['key'],
            'locale' => 'zh-TW',
            'content' => $validated['zh_TW'],
            'label' => $validated['label'],
            'type' => $validated['type'] ?? 'text',
            'sort_order' => $validated['sort_order'] ?? 0,
        ]);

        SiteContent::create([
            'key' => $validated['key'],
            'locale' => 'en',
            'content' => $validated['en'] ?? '',
            'label' => $validated['label'],
            'type' => $validated['type'] ?? 'text',
            'sort_order' => $validated['sort_order'] ?? 0,
        ]);

        SiteContent::clearCache();

        return back()->with('success', 'Content block added.');
    }

    public function destroy(string $key)
    {
        SiteContent::where('key', $key)->delete();
        SiteContent::clearCache();

        return back()->with('success', 'Content block deleted.');
    }
}