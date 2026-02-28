<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\BelongsToVendor;

class StockLog extends Model
{
    use BelongsToVendor;

    protected $fillable = [
        'vendor_id',
        'product_id',
        'product_variant_id',
        'supplier_id',
        'type',
        'quantity',
        'old_stock',
        'new_stock',
        'note',
    ];

    protected $casts = [
        'quantity' => 'decimal:2',
        'old_stock' => 'decimal:2',
        'new_stock' => 'decimal:2',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function variant()
    {
        return $this->belongsTo(ProductVariant::class, 'product_variant_id');
    }

    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }
}
