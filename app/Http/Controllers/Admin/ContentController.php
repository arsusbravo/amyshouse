<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SiteContent;
use Inertia\Inertia;
use Inertia\Response;

class ContentController extends Controller
{
    public function index(): Response
    {
        $contents = SiteContent::orderBy('sort_order')
            ->orderBy('key')
            ->get()
            ->groupBy('key')
            ->map(function ($group) {
                $first = $group->first();
                return [
                    'key' => $first->key,
                    'label' => $first->label,
                    'type' => $first->type,
                    'sort_order' => $first->sort_order,
                    'zh_TW' => $group->firstWhere('locale', 'zh-TW')?->content ?? '',
                    'en' => $group->firstWhere('locale', 'en')?->content ?? '',
                    'zh_TW_id' => $group->firstWhere('locale', 'zh-TW')?->id,
                    'en_id' => $group->firstWhere('locale', 'en')?->id,
                ];
            })
            ->values();

        return Inertia::render('Admin/Content', [
            'contents' => $contents,
        ]);
    }
}