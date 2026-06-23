<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Purchase extends Model
{
    use HasFactory;

    protected $fillable = [
        'supplier_id',
        'user_id',
        'invoice_no',
        'total',
        'purchase_date',
    ];

    protected function casts(): array
    {
        return [
            'total'         => 'decimal:2',
            'purchase_date' => 'date',
        ];
    }

    // ─── Invoice Number Generator ──────────────────────────────────────────────

    /**
     * Generate a unique PO number with format: PO-YYYYMMDD-XXXX
     */
    public static function generateInvoiceNo(): string
    {
        $date   = now()->format('Ymd');
        $prefix = "PO-{$date}-";
        $latest = static::where('invoice_no', 'like', $prefix . '%')
                        ->latest('id')
                        ->value('invoice_no');

        $sequence = $latest ? (int) substr($latest, -4) + 1 : 1;

        return $prefix . str_pad($sequence, 4, '0', STR_PAD_LEFT);
    }

    // ─── Relationships ─────────────────────────────────────────────────────────

    public function supplier(): BelongsTo
    {
        return $this->belongsTo(Supplier::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function purchaseItems(): HasMany
    {
        return $this->hasMany(PurchaseItem::class);
    }

    public function products(): BelongsToMany
    {
        return $this->belongsToMany(Product::class, 'purchase_items')
                    ->withPivot(['qty', 'buy_price', 'subtotal'])
                    ->withTimestamps();
    }
}
