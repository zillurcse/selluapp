<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use App\Traits\BelongsToVendor;

class Promotion extends Model
{
    use BelongsToVendor;
    protected $fillable = [
        'vendor_id',
        'title',
        'slug',
        'type',
        'start_date',
        'end_date',
        'target',
        'target_ids',
        'discount_value',
        'discount_unit',
        'banner',
        'is_active',
        'is_stackable',
        'priority',
        'rules',
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

    protected static function boot()
    {
        parent::boot();

        static::saving(function ($promotion) {
            if (empty($promotion->slug) || $promotion->isDirty('title')) {
                $promotion->slug = \Illuminate\Support\Str::slug($promotion->title);
                
                // Ensure uniqueness per vendor
                $count = static::where('vendor_id', $promotion->vendor_id)
                    ->where('slug', 'like', $promotion->slug . '%')
                    ->where('id', '!=', $promotion->id)
                    ->count();
                
                if ($count > 0) {
                    $promotion->slug .= '-' . ($count + 1);
                }
            }
        });

        static::saved(function ($promotion) {
            \Illuminate\Support\Facades\Cache::forget('storefront_index_' . ($promotion->vendor_id ?? 'global'));
        });

        static::deleted(function ($promotion) {
            \Illuminate\Support\Facades\Cache::forget('storefront_index_' . ($promotion->vendor_id ?? 'global'));
        });
    }
}
