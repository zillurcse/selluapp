<?php

namespace App\Traits;

use App\Models\StockLog;
use Illuminate\Support\Facades\Auth;

trait HasStock
{
    /**
     * Log a stock change for the model.
     *
     * @param int $quantity The amount changed (positive or negative)
     * @param string $type The type of change (initial, restock, adjustment)
     * @param int|null $supplierId Optional supplier ID
     * @param string|null $note Optional note
     * @return \App\Models\StockLog
     */
    public function logStockChange($quantity, $type, $supplierId = null, $note = null)
    {
        $oldStock = $this->stock_qty - $quantity;
        
        return StockLog::create([
            'vendor_id' => $this->vendor_id ?? Auth::id(),
            'product_id' => $this instanceof \App\Models\ProductVariant ? $this->product_id : $this->id,
            'product_variant_id' => $this instanceof \App\Models\ProductVariant ? $this->id : null,
            'supplier_id' => $supplierId,
            'type' => $type,
            'quantity' => $quantity,
            'old_stock' => $oldStock,
            'new_stock' => $this->stock_qty,
            'note' => $note,
        ]);
    }
}
