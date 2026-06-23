<?php

namespace App\Observers;

use App\Models\SaleItem;

class SaleItemObserver
{
    /**
     * Deduct stock when a SaleItem is created.
     * Using increment/decrement for atomic DB operation (avoids race conditions).
     */
    public function created(SaleItem $saleItem): void
    {
        $saleItem->product()->decrement('stock', $saleItem->qty);
    }

    /**
     * Restore stock if a SaleItem is deleted (e.g., voided sale).
     */
    public function deleted(SaleItem $saleItem): void
    {
        $saleItem->product()->increment('stock', $saleItem->qty);
    }
}
