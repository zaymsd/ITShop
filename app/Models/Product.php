<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'sku',
        'barcode',
        'buy_price',
        'sell_price',
        'stock',
        'min_stock',
        'specs',
        'category_id',
        'brand_id',
        'supplier_id',
    ];

    protected function casts(): array
    {
        return [
            'buy_price'  => 'decimal:2',
            'sell_price' => 'decimal:2',
            'stock'      => 'integer',
            'min_stock'  => 'integer',
        ];
    }

    // ─── Computed Attributes ───────────────────────────────────────────────────

    /**
     * Check if the product stock is at or below minimum threshold.
     */
    public function isLowStock(): bool
    {
        return $this->stock <= $this->min_stock;
    }

    // ─── Relationships ─────────────────────────────────────────────────────────

    public function images(): HasMany
    {
        return $this->hasMany(ProductImage::class);
    }

    public function primaryImage(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(ProductImage::class)->where('is_primary', true);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function brand(): BelongsTo
    {
        return $this->belongsTo(Brand::class);
    }

    public function supplier(): BelongsTo
    {
        return $this->belongsTo(Supplier::class);
    }

    public function saleItems(): HasMany
    {
        return $this->hasMany(SaleItem::class);
    }

    public function purchaseItems(): HasMany
    {
        return $this->hasMany(PurchaseItem::class);
    }

    /** Many-to-many via sale_items (with extra pivot attributes) */
    public function sales(): BelongsToMany
    {
        return $this->belongsToMany(Sale::class, 'sale_items')
                    ->withPivot(['qty', 'price', 'discount', 'subtotal'])
                    ->withTimestamps();
    }

    /** Many-to-many via purchase_items */
    public function purchases(): BelongsToMany
    {
        return $this->belongsToMany(Purchase::class, 'purchase_items')
                    ->withPivot(['qty', 'buy_price', 'subtotal'])
                    ->withTimestamps();
    }
}
