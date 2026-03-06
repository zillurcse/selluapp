<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\BelongsToVendor;

class CustomerMessage extends Model
{
    use SoftDeletes, BelongsToVendor;

    protected $vendorIdColumn = 'user_id';
    protected $fillable = [
        'user_id',
        'customer_id',
        'sender_name',
        'sender_email',
        'subject',
        'message',
        'sentiment_score',
        'sentiment_label',
    ];

    protected $casts = [
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
}
