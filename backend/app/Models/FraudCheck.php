<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use App\Traits\BelongsToVendor;

class FraudCheck extends Model
{
    use BelongsToVendor;

    protected $vendorIdColumn = 'user_id';
    protected $fillable = [
        'user_id',
        'order_id',
        'risk_score',
        'flags',
        'status',
        'reviewer_notes',
        'reviewed_by',
        'reviewed_at',
    ];

    protected $casts = [
        'flags' => 'array',
        'reviewed_at' => 'datetime',
    ];

    public function vendor()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function reviewer()
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }
}
