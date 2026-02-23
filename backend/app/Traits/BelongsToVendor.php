<?php

namespace App\Traits;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

trait BelongsToVendor
{
    public static function bootBelongsToVendor()
    {
        static::creating(function (Model $model) {
            if (Auth::check()) {
                $user = Auth::user();
                $column = $model->getVendorIdColumn();
                // Automatically set vendor_id if not set and user is vendor/staff
                if (!$user->hasRole('super-admin') && ($user->hasRole('vendor') || $user->vendor_id)) {
                    if (!isset($model->{$column})) {
                        $model->{$column} = $user->vendor_id ?? $user->id;
                    }
                }
            }
        });

        static::addGlobalScope('vendor_isolation', function (Builder $builder) {
            if (Auth::check()) {
                $user = Auth::user();
                // If the user is a vendor or staff (not a super-admin), scope the queries
                if (!$user->hasRole('super-admin') && ($user->hasRole('vendor') || $user->vendor_id)) {
                    $instance = new static;
                    $column = $instance->getVendorIdColumn();
                    $vendorId = $user->vendor_id ?? $user->id;
                    $builder->where($builder->getQuery()->from . '.' . $column, $vendorId);
                }
            }
        });
    }

    public function getVendorIdColumn()
    {
        return property_exists($this, 'vendorIdColumn') ? $this->vendorIdColumn : 'vendor_id';
    }
}
