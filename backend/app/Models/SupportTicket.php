<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\BelongsToVendor;

class SupportTicket extends Model
{
    use SoftDeletes, BelongsToVendor;

    protected $vendorIdColumn = 'user_id';
    protected $fillable = [
        'user_id',
        'customer_id',
        'subject',
        'description',
        'priority',
        'status',
        'source',
        'ai_metadata',
    ];

    protected $casts = [
        'ai_metadata' => 'array',
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
