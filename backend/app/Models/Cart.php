<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cart extends Model
{
    use HasFactory;

    protected $fillable = ['user_id', 'temp_user_id', 'product_id', 'quantity', 'shipping_cost'];

    public function scopeActive($query)
    {
        return $query; // For now return all, usually you'd filter by some criteria
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
