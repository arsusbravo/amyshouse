<?php

namespace App\Http\Controllers;

use App\Models\Color;
use App\Models\Material;
use App\Models\Product;
use App\Models\ProductType;
use App\Models\SiteContent;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class HomeController extends Controller
{
    public function __invoke(Request $request): Response
    {
        $query = Product::active()
            ->with([
                'type',
                'translations',
                'images' => fn ($q) => $q->orderBy('sort_order'),
                'colors',
                'materials',
            ])
            ->orderBy('sort_order')
            ->orderByDesc('created_at');

        if ($search = $request->input('search')) {
            $query->whereHas('translations', function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%");
            });
        }

        if ($type = $request->input('type')) {
            $query->whereHas('type', fn ($q) => $q->where('slug', $type));
        }

        if ($materials = $request->input('materials')) {
            $slugs = is_array($materials) ? $materials : explode(',', $materials);
            $query->whereHas('materials', fn ($q) => $q->whereIn('slug', $slugs));
        }

        if ($colors = $request->input('colors')) {
            $slugs = is_array($colors) ? $colors : explode(',', $colors);
            $query->whereHas('colors', fn ($q) => $q->whereIn('slug', $slugs));
        }

        $products = $query->get()->map(fn (Product $p) => [
            'id' => $p->id,
            'slug' => $p->slug,
            'name_zh' => $p->nameFor('zh-TW'),
            'name_en' => $p->nameFor('en'),
            'price' => $p->price,
            'type_name_zh' => $p->type->name_zh,
            'type_name_en' => $p->type->name_en ?? $p->type->name_zh,
            'type_slug' => $p->type->slug,
            'stock' => $p->stock,
            'primary_image_url' => $p->primaryImage()?->url(),
            'colors' => $p->colors->map(fn (Color $c) => [
                'hex_code' => $c->hex_code,
                'name_zh' => $c->name_zh,
                'name_en' => $c->name_en ?? $c->name_zh,
            ]),
        ]);

        return Inertia::render('Home', [
            'products' => $products,
            'types' => ProductType::orderBy('sort_order')->get()->map(fn (ProductType $t) => [
                'id' => $t->id,
                'slug' => $t->slug,
                'name_zh' => $t->name_zh,
                'name_en' => $t->name_en ?? $t->name_zh,
            ]),
            'materials' => Material::orderBy('sort_order')->get()->map(fn (Material $m) => [
                'id' => $m->id,
                'slug' => $m->slug,
                'name_zh' => $m->name_zh,
                'name_en' => $m->name_en ?? $m->name_zh,
            ]),
            'colors' => Color::orderBy('sort_order')->get()->map(fn (Color $c) => [
                'id' => $c->id,
                'slug' => $c->slug,
                'name_zh' => $c->name_zh,
                'name_en' => $c->name_en ?? $c->name_zh,
                'hex_code' => $c->hex_code,
            ]),
            'filters' => $request->only(['search', 'type', 'materials', 'colors']),
            'content' => [
                'zh-TW' => SiteContent::allForLocale('zh-TW'),
                'en' => SiteContent::allForLocale('en'),
            ],
        ]);
    }
}