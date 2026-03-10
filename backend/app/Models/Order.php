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
        'applied_promotions',
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
        'courier_name',
        'courier_order_id',
        'courier_status',
        'otp_code',
        'otp_expires_at',
    ];

    protected $casts = [
        'risk_flags' => 'array',
        'requires_manual_review' => 'boolean',
        'subtotal' => 'decimal:2',
        'shipping_cost' => 'decimal:2',
        'discount_amount' => 'decimal:2',
        'total_amount' => 'decimal:2',
        'applied_promotions' => 'array',
        'otp_expires_at' => 'datetime',
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
