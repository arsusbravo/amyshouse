<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Color;
use App\Models\Material;
use App\Models\Product;
use App\Models\ProductType;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class ProductController extends Controller
{
    public function index(Request $request): Response
    {
        $query = Product::with([
            'type', 'translations',
            'images' => fn ($q) => $q->orderBy('sort_order'),
            'materials', 'colors',
        ])->orderBy('sort_order')->orderByDesc('created_at');

        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->whereHas('translations', fn ($tq) => $tq->where('name', 'like', "%{$search}%"))
                    ->orWhere('slug', 'like', "%{$search}%");
            });
        }

        if ($typeId = $request->input('type_id')) {
            $query->where('product_type_id', $typeId);
        }

        if ($materialId = $request->input('material_id')) {
            $query->whereHas('materials', fn ($q) => $q->where('materials.id', $materialId));
        }

        if ($colorId = $request->input('color_id')) {
            $query->whereHas('colors', fn ($q) => $q->where('colors.id', $colorId));
        }

        $products = $query->paginate(20)->through(fn (Product $p) => [
            'id' => $p->id,
            'slug' => $p->slug,
            'name_zh' => $p->nameFor('zh-TW'),
            'name_en' => $p->nameFor('en'),
            'price' => $p->price,
            'type_name' => $p->type->name_zh,
            'stock' => $p->stock,
            'is_active' => $p->is_active,
            'primary_image_url' => $p->primaryImage()?->url(),
            'materials' => $p->materials->pluck('name_zh')->toArray(),
            'colors' => $p->colors->map(fn (Color $c) => [
                'name' => $c->name_zh,
                'hex_code' => $c->hex_code,
            ]),
        ]);

        return Inertia::render('Admin/Products/Index', [
            'products' => $products,
            'types' => ProductType::orderBy('sort_order')->get(['id', 'name_zh', 'name_en']),
            'materials' => Material::orderBy('sort_order')->get(['id', 'name_zh', 'name_en']),
            'colors' => Color::orderBy('sort_order')->get(['id', 'name_zh', 'name_en', 'hex_code']),
            'filters' => $request->only(['search', 'type_id', 'material_id', 'color_id']),
        ]);
    }

    public function show(Product $product): Response
    {
        $product->load([
            'type', 'translations',
            'images' => fn ($q) => $q->orderBy('sort_order'),
            'materials', 'colors',
        ]);

        return Inertia::render('Admin/Products/Show', [
            'product' => [
                'id' => $product->id,
                'slug' => $product->slug,
                'name_zh' => $product->nameFor('zh-TW'),
                'name_en' => $product->nameFor('en'),
                'description_zh' => $product->descriptionFor('zh-TW'),
                'description_en' => $product->descriptionFor('en'),
                'price' => $product->price,
                'type' => ['id' => $product->type->id, 'name_zh' => $product->type->name_zh],
                'stock' => $product->stock,
                'size_info' => $product->size_info,
                'production_days' => $product->production_days,
                'is_active' => $product->is_active,
                'sort_order' => $product->sort_order,
                'materials' => $product->materials->map(fn ($m) => ['id' => $m->id, 'name_zh' => $m->name_zh]),
                'colors' => $product->colors->map(fn ($c) => ['id' => $c->id, 'name_zh' => $c->name_zh, 'hex_code' => $c->hex_code]),
                'images' => $product->images->map(fn ($img) => [
                    'id' => $img->id,
                    'url' => $img->url(),
                    'is_primary' => $img->is_primary,
                    'sort_order' => $img->sort_order,
                ]),
                'created_at' => $product->created_at->toISOString(),
            ],
        ]);
    }

    public function create(): Response
    {
        return Inertia::render('Admin/Products/Form', [
            'product' => null,
            'types' => ProductType::orderBy('sort_order')->get(['id', 'name_zh', 'name_en']),
            'materials' => Material::orderBy('sort_order')->get(['id', 'name_zh', 'name_en']),
            'colors' => Color::orderBy('sort_order')->get(['id', 'name_zh', 'name_en', 'hex_code']),
        ]);
    }

    public function edit(Product $product): Response
    {
        $product->load(['type', 'translations', 'images' => fn ($q) => $q->orderBy('sort_order'), 'materials', 'colors']);

        return Inertia::render('Admin/Products/Form', [
            'product' => [
                'id' => $product->id,
                'name_zh' => $product->nameFor('zh-TW'),
                'name_en' => $product->nameFor('en'),
                'description_zh' => $product->descriptionFor('zh-TW'),
                'description_en' => $product->descriptionFor('en'),
                'price' => $product->price,
                'product_type_id' => $product->product_type_id,
                'stock' => $product->stock,
                'size_info' => $product->size_info ? json_encode($product->size_info) : '',
                'production_days' => $product->production_days,
                'is_active' => $product->is_active,
                'sort_order' => $product->sort_order,
                'material_ids' => $product->materials->pluck('id')->toArray(),
                'color_ids' => $product->colors->pluck('id')->toArray(),
                'images' => $product->images->map(fn ($img) => [
                    'id' => $img->id,
                    'url' => $img->url(),
                    'is_primary' => $img->is_primary,
                    'sort_order' => $img->sort_order,
                ]),
            ],
            'types' => ProductType::orderBy('sort_order')->get(['id', 'name_zh', 'name_en']),
            'materials' => Material::orderBy('sort_order')->get(['id', 'name_zh', 'name_en']),
            'colors' => Color::orderBy('sort_order')->get(['id', 'name_zh', 'name_en', 'hex_code']),
        ]);
    }
}