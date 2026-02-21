<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Coupon extends Model
{
    protected $fillable = [
        'vendor_id',
        'title',
        'code',
        'discount_type',
        'discount_amount',
        'start_date',
        'end_date',
        'min_purchase',
        'max_discount',
        'usage_limit',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'discount_amount' => 'decimal:2',
        'min_purchase' => 'decimal:2',
        'max_discount' => 'decimal:2',
        'start_date' => 'date',
        'end_date' => 'date',
    ];

    public function vendor()
    {
        return $this->belongsTo(User::class, 'vendor_id');
    }
}
