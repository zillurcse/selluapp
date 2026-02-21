<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Package extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'description',
        'price',
        'duration',
        'product_limit',
        'order_limit',
        'employee_limit',
        'pos_access',
        'hrm_access',
        'is_active',
        'features',
    ];

    protected $casts = [
        'features' => 'array',
        'pos_access' => 'boolean',
        'hrm_access' => 'boolean',
        'is_active' => 'boolean',
    ];

    public function vendorProfiles()
    {
        return $this->hasMany(VendorProfile::class);
    }
}
