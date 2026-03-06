<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\BelongsToVendor;

class ProductReview extends Model
{
    use SoftDeletes, BelongsToVendor;

    protected $vendorIdColumn = 'user_id';
    protected $fillable = [
        'user_id',
        'customer_id',
        'product_id',
        'order_id',
        'rating',
        'comment',
        'status',
        'is_verified',
        'sentiment_score',
        'sentiment_label',
    ];

    protected $casts = [
        'is_verified' => 'boolean',
        'sentiment_score' => 'decimal:2',
    ];

    public function vendor()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function order()
    {
        return $this->belongsTo(Order::class);
    }
}
