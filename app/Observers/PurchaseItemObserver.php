<?php

namespace App\Observers;

use App\Models\PurchaseItem;

class PurchaseItemObserver
{
    /**
     * Add stock when a PurchaseItem is created.
     * Using increment for atomic DB operation.
     */
    public function created(PurchaseItem $purchaseItem): void
    {
        $purchaseItem->product()->increment('stock', $purchaseItem->qty);
    }

    /**
     * Deduct stock if a PurchaseItem is deleted (purchase cancellation).
     */
    public function deleted(PurchaseItem $purchaseItem): void
    {
        $purchaseItem->product()->decrement('stock', $purchaseItem->qty);
    }
}
