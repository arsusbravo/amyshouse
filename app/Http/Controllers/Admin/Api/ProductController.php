<?php

namespace App\Http\Controllers\Admin\Api;

use App\Http\Controllers\Controller;
use App\Models\Color;
use App\Models\Product;
use App\Models\ProductImage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ProductController extends Controller
{
    public function store(Request $request)
    {
        $validated = $this->validateProduct($request);

        $product = DB::transaction(function () use ($validated, $request) {
            $product = Product::create([
                'slug' => Str::slug($validated['name_en'] ?: $validated['name_zh']),
                'product_type_id' => $validated['product_type_id'],
                'price' => $validated['price'],
                'stock' => $validated['stock'],
                'size_info' => $validated['size_info'] ? json_decode($validated['size_info'], true) : null,
                'production_days' => $validated['production_days'],
                'is_active' => $validated['is_active'] ?? true,
                'sort_order' => $validated['sort_order'] ?? 0,
            ]);

            $this->saveTranslations($product, $validated);
            $this->syncAttributes($product, $validated);
            $this->handleImageUploads($request, $product);

            return $product;
        });

        return redirect()->route('admin.products.show', $product)
            ->with('success', 'Product created.');
    }

    public function update(Request $request, Product $product)
    {
        $validated = $this->validateProduct($request);

        DB::transaction(function () use ($validated, $request, $product) {
            $product->update([
                'product_type_id' => $validated['product_type_id'],
                'price' => $validated['price'],
                'stock' => $validated['stock'],
                'size_info' => $validated['size_info'] ? json_decode($validated['size_info'], true) : null,
                'production_days' => $validated['production_days'],
                'is_active' => $validated['is_active'] ?? true,
                'sort_order' => $validated['sort_order'] ?? 0,
            ]);

            $this->saveTranslations($product, $validated);
            $this->syncAttributes($product, $validated);
            $this->handleImageUploads($request, $product);
        });

        return redirect()->route('admin.products.show', $product)
            ->with('success', 'Product updated.');
    }

    public function destroy(Product $product)
    {
        foreach ($product->images as $image) {
            Storage::disk('public')->delete($image->path);
        }

        $product->delete();

        return redirect()->route('admin.products.index')
            ->with('success', 'Product deleted.');
    }

    public function destroyImage(ProductImage $image)
    {
        Storage::disk('public')->delete($image->path);
        $image->delete();

        return back()->with('success', 'Image deleted.');
    }

    public function setPrimaryImage(ProductImage $image)
    {
        ProductImage::where('product_id', $image->product_id)->update(['is_primary' => false]);
        $image->update(['is_primary' => true]);

        return back()->with('success', 'Primary image set.');
    }

    private function validateProduct(Request $request): array
    {
        return $request->validate([
            'name_zh' => 'required|string|max:255',
            'name_en' => 'nullable|string|max:255',
            'description_zh' => 'nullable|string',
            'description_en' => 'nullable|string',
            'price' => 'required|integer|min:0',
            'product_type_id' => 'required|exists:product_types,id',
            'stock' => 'required|integer|min:0',
            'size_info' => 'nullable|string',
            'production_days' => 'nullable|integer|min:0',
            'is_active' => 'boolean',
            'sort_order' => 'integer|min:0',
            'material_ids' => 'nullable|array',
            'material_ids.*' => 'exists:materials,id',
            'color_ids' => 'nullable|array',
            'color_ids.*' => 'exists:colors,id',
        ]);
    }

    private function saveTranslations(Product $product, array $validated): void
    {
        $product->translations()->updateOrCreate(
            ['locale' => 'zh-TW'],
            ['name' => $validated['name_zh'], 'description' => $validated['description_zh'] ?? null],
        );

        if ($validated['name_en'] ?? null) {
            $product->translations()->updateOrCreate(
                ['locale' => 'en'],
                ['name' => $validated['name_en'], 'description' => $validated['description_en'] ?? null],
            );
        } else {
            $product->translations()->where('locale', 'en')->delete();
        }
    }

    private function syncAttributes(Product $product, array $validated): void
    {
        $product->materials()->sync($validated['material_ids'] ?? []);
        $product->colors()->sync($validated['color_ids'] ?? []);
    }

    private function handleImageUploads(Request $request, Product $product): void
    {
        if (! $request->hasFile('images')) {
            return;
        }

        $request->validate([
            'images.*' => 'image|mimes:jpg,jpeg,png,webp|max:5120',
        ]);

        $maxSort = $product->images()->max('sort_order') ?? -1;
        $hasImages = $product->images()->count() > 0;

        foreach ($request->file('images') as $index => $file) {
            $path = $file->store('products/' . $product->id, 'public');

            $product->images()->create([
                'path' => $path,
                'alt' => $product->nameFor('zh-TW'),
                'sort_order' => $maxSort + $index + 1,
                'is_primary' => ! $hasImages && $index === 0,
            ]);
        }
    }
}