<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\BelongsToVendor;

class Supplier extends Model
{
    use SoftDeletes, BelongsToVendor;

    protected $fillable = [
        'vendor_id',
        'name',
        'email',
        'phone',
        'address',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function vendor()
    {
        return $this->belongsTo(User::class, 'vendor_id');
    }
}
