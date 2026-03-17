<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Product extends Model
{
    protected $fillable = [
        'slug', 'product_type_id', 'price', 'stock',
        'size_info', 'production_days', 'is_active', 'sort_order',
    ];

    protected function casts(): array
    {
        return [
            'price' => 'integer',
            'stock' => 'integer',
            'size_info' => 'array',
            'production_days' => 'integer',
            'is_active' => 'boolean',
            'sort_order' => 'integer',
        ];
    }

    // === Relationships ===

    public function type(): BelongsTo
    {
        return $this->belongsTo(ProductType::class, 'product_type_id');
    }

    public function translations(): HasMany
    {
        return $this->hasMany(ProductTranslation::class);
    }

    public function images(): HasMany
    {
        return $this->hasMany(ProductImage::class)->orderBy('sort_order');
    }

    public function materials(): BelongsToMany
    {
        return $this->belongsToMany(Material::class, 'product_material');
    }

    public function colors(): BelongsToMany
    {
        return $this->belongsToMany(Color::class, 'product_color');
    }

    // === Translation helpers ===

    public function translation(string $locale): ?ProductTranslation
    {
        return $this->translations->firstWhere('locale', $locale);
    }

    public function nameFor(string $locale): string
    {
        return $this->translation($locale)?->name
            ?? $this->translation('zh-TW')?->name
            ?? '';
    }

    public function descriptionFor(string $locale): ?string
    {
        return $this->translation($locale)?->description
            ?? $this->translation('zh-TW')?->description;
    }

    // === Image helpers ===

    public function primaryImage(): ?ProductImage
    {
        return $this->images->firstWhere('is_primary', true)
            ?? $this->images->first();
    }

    // === Scopes ===

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    // === Helpers ===

    public function inStock(): bool
    {
        return $this->stock > 0;
    }

    public function priceFormatted(): string
    {
        return '€' . number_format($this->price / 100, 2, ',', '.');
    }
}
