<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Sale extends Model
{
    use HasFactory;

    protected $fillable = [
        'invoice_no',
        'user_id',
        'customer_name',
        'total',
        'discount',
        'tax',
        'grand_total',
        'payment_method',
        'paid_amount',
        'change_amount',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'total'         => 'decimal:2',
            'discount'      => 'decimal:2',
            'tax'           => 'decimal:2',
            'grand_total'   => 'decimal:2',
            'paid_amount'   => 'decimal:2',
            'change_amount' => 'decimal:2',
        ];
    }

    // ─── Invoice Number Generator ──────────────────────────────────────────────

    /**
     * Generate a unique invoice number with format: INV-YYYYMMDD-XXXX
     */
    public static function generateInvoiceNo(): string
    {
        $date   = now()->format('Ymd');
        $prefix = "INV-{$date}-";
        $latest = static::where('invoice_no', 'like', $prefix . '%')
                        ->latest('id')
                        ->value('invoice_no');

        $sequence = $latest ? (int) substr($latest, -4) + 1 : 1;

        return $prefix . str_pad($sequence, 4, '0', STR_PAD_LEFT);
    }

    // ─── Relationships ─────────────────────────────────────────────────────────

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function saleItems(): HasMany
    {
        return $this->hasMany(SaleItem::class);
    }

    public function products(): BelongsToMany
    {
        return $this->belongsToMany(Product::class, 'sale_items')
                    ->withPivot(['qty', 'price', 'discount', 'subtotal'])
                    ->withTimestamps();
    }
}
