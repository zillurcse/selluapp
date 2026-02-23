<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use App\Traits\BelongsToVendor;

class Order extends Model
{
    use \Illuminate\Database\Eloquent\SoftDeletes, BelongsToVendor;

    protected $vendorIdColumn = 'user_id';

    protected $fillable = [
        'user_id',
        'customer_id',
        'invoice_number',
        'status',
        'type',
        'subtotal',
        'shipping_cost',
        'discount_amount',
        'total_amount',
        'payment_method',
        'payment_status',
        'shipping_address',
        'delivery_zone',
        'courier_id',
        'tracking_number',
        'risk_score',
        'risk_flags',
        'requires_manual_review',
        'notes',
    ];

    protected $casts = [
        'risk_flags' => 'array',
        'requires_manual_review' => 'boolean',
        'subtotal' => 'decimal:2',
        'shipping_cost' => 'decimal:2',
        'discount_amount' => 'decimal:2',
        'total_amount' => 'decimal:2',
    ];

    public function vendor()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function fraudCheck()
    {
        return $this->hasOne(FraudCheck::class);
    }

    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }
}
