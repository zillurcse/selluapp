<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

use App\Traits\BelongsToVendor;

class Customer extends Model
{
    use HasFactory, BelongsToVendor;

    protected $fillable = [
        'vendor_id',
        'first_name',
        'last_name',
        'email',
        'phone',
        'emergency_phone',
        'password',
        'is_active',
        'tax_number',
        'credit_limit',
        'opening_balance',
        'loyalty_points',
        'date_of_birth',
        'gender',
    ];

    protected $hidden = [
        'password',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'password' => 'hashed',
    ];

    public function vendor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'vendor_id');
    }
}
