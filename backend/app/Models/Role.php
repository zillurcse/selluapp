<?php

namespace App\Models;

use Spatie\Permission\Models\Role as SpatieRole;

use App\Traits\BelongsToVendor;

class Role extends SpatieRole
{
    use BelongsToVendor;
    protected $fillable = [
        'name',
        'guard_name',
        'vendor_id',
    ];

    public function vendor()
    {
        return $this->belongsTo(User::class, 'vendor_id');
    }
}
