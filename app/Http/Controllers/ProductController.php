<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Inertia\Inertia;
use Inertia\Response;

class ProductController extends Controller
{
    public function show(Product $product): Response
    {
        $product->load([
            'type',
            'translations',
            'images' => fn ($q) => $q->orderBy('sort_order'),
            'materials',
            'colors',
        ]);

        return Inertia::render('ProductDetail', [
            'product' => [
                'id' => $product->id,
                'slug' => $product->slug,
                'name_zh' => $product->nameFor('zh-TW'),
                'name_en' => $product->nameFor('en'),
                'description_zh' => $product->descriptionFor('zh-TW'),
                'description_en' => $product->descriptionFor('en'),
                'price' => $product->price,
                'type_name_zh' => $product->type->name_zh,
                'type_name_en' => $product->type->name_en ?? $product->type->name_zh,
                'size_info' => $product->size_info,
                'stock' => $product->stock,
                'production_days' => $product->production_days,
                'materials' => $product->materials->map(fn ($m) => [
                    'name_zh' => $m->name_zh,
                    'name_en' => $m->name_en ?? $m->name_zh,
                ]),
                'colors' => $product->colors->map(fn ($c) => [
                    'name_zh' => $c->name_zh,
                    'name_en' => $c->name_en ?? $c->name_zh,
                    'hex_code' => $c->hex_code,
                ]),
                'images' => $product->images->map(fn ($img) => [
                    'id' => $img->id,
                    'url' => $img->url(),
                    'alt' => $img->alt,
                ]),
                'primary_image_url' => $product->primaryImage()?->url(),
            ],
        ]);
    }
}