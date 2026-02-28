<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use App\Traits\HasStock;

class ProductVariant extends Model
{
    use HasFactory, HasStock;

    protected $fillable = [
        'product_id',
        'sku',
        'price',
        'stock_qty',
        'image',
        'is_active'
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function attributes()
    {
        return $this->belongsToMany(AttributeValue::class, 'product_variant_attributes');
    }

    public function barcode()
    {
        return $this->hasOne(ProductBarcode::class, 'product_variant_id');
    }
}
