<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Promotion extends Model
{
    protected $fillable = [
        'vendor_id',
        'title',
        'type',
        'start_date',
        'end_date',
        'target',
        'target_ids',
        'discount_value',
        'discount_unit',
        'banner',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'discount_value' => 'decimal:2',
        'start_date' => 'date',
        'end_date' => 'date',
        'target_ids' => 'array',
    ];

    public function vendor()
    {
        return $this->belongsTo(User::class, 'vendor_id');
    }
}
