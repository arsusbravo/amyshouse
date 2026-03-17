<?php

namespace App\Http\Controllers\Admin\Api;

use App\Http\Controllers\Controller;
use App\Models\Color;
use App\Models\Material;
use App\Models\ProductType;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class AttributeController extends Controller
{
    // === Product Types ===

    public function storeType(Request $request)
    {
        $validated = $request->validate([
            'name_zh' => 'required|string|max:255',
            'name_en' => 'nullable|string|max:255',
            'sort_order' => 'integer|min:0',
        ]);

        ProductType::create([
            ...$validated,
            'slug' => Str::slug($validated['name_en'] ?: $validated['name_zh']),
            'sort_order' => $validated['sort_order'] ?? 0,
        ]);

        return back()->with('success', 'Product type created.');
    }

    public function updateType(Request $request, ProductType $type)
    {
        $validated = $request->validate([
            'name_zh' => 'required|string|max:255',
            'name_en' => 'nullable|string|max:255',
            'sort_order' => 'integer|min:0',
        ]);

        $type->update([
            ...$validated,
            'slug' => Str::slug($validated['name_en'] ?: $validated['name_zh']),
        ]);

        return back()->with('success', 'Product type updated.');
    }

    public function destroyType(ProductType $type)
    {
        if ($type->products()->count() > 0) {
            return back()->withErrors(['type' => 'Cannot delete: this type has products.']);
        }

        $type->delete();
        return back()->with('success', 'Product type deleted.');
    }

    // === Materials ===

    public function storeMaterial(Request $request)
    {
        $validated = $request->validate([
            'name_zh' => 'required|string|max:255',
            'name_en' => 'nullable|string|max:255',
            'sort_order' => 'integer|min:0',
        ]);

        Material::create([
            ...$validated,
            'slug' => Str::slug($validated['name_en'] ?: $validated['name_zh']),
            'sort_order' => $validated['sort_order'] ?? 0,
        ]);

        return back()->with('success', 'Material created.');
    }

    public function updateMaterial(Request $request, Material $material)
    {
        $validated = $request->validate([
            'name_zh' => 'required|string|max:255',
            'name_en' => 'nullable|string|max:255',
            'sort_order' => 'integer|min:0',
        ]);

        $material->update([
            ...$validated,
            'slug' => Str::slug($validated['name_en'] ?: $validated['name_zh']),
        ]);

        return back()->with('success', 'Material updated.');
    }

    public function destroyMaterial(Material $material)
    {
        if ($material->products()->count() > 0) {
            return back()->withErrors(['material' => 'Cannot delete: this material is used by products.']);
        }

        $material->delete();
        return back()->with('success', 'Material deleted.');
    }

    // === Colors ===

    public function storeColor(Request $request)
    {
        $validated = $request->validate([
            'name_zh' => 'required|string|max:255',
            'name_en' => 'nullable|string|max:255',
            'hex_code' => 'nullable|string|max:7',
            'sort_order' => 'integer|min:0',
        ]);

        Color::create([
            ...$validated,
            'slug' => Str::slug($validated['name_en'] ?: $validated['name_zh']),
            'sort_order' => $validated['sort_order'] ?? 0,
        ]);

        return back()->with('success', 'Color created.');
    }

    public function updateColor(Request $request, Color $color)
    {
        $validated = $request->validate([
            'name_zh' => 'required|string|max:255',
            'name_en' => 'nullable|string|max:255',
            'hex_code' => 'nullable|string|max:7',
            'sort_order' => 'integer|min:0',
        ]);

        $color->update([
            ...$validated,
            'slug' => Str::slug($validated['name_en'] ?: $validated['name_zh']),
        ]);

        return back()->with('success', 'Color updated.');
    }

    public function destroyColor(Color $color)
    {
        if ($color->products()->count() > 0) {
            return back()->withErrors(['color' => 'Cannot delete: this color is used by products.']);
        }

        $color->delete();
        return back()->with('success', 'Color deleted.');
    }
}