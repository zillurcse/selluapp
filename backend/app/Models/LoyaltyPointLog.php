<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LoyaltyPointLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'customer_id',
        'vendor_id',
        'order_id',
        'pos_sale_id',
        'points',
        'description',
    ];

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function vendor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'vendor_id');
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function posSale(): BelongsTo
    {
        return $this->belongsTo(PosSale::class);
    }
}
