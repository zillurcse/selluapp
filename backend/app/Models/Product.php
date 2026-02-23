<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

use App\Traits\BelongsToVendor;

class Product extends Model
{
    use HasFactory, SoftDeletes, BelongsToVendor;

    protected $fillable = [
        'vendor_id',
        'brand_id',
        'unit_id',
        'name',
        'slug',
        'sku',
        'product_code',
        'short_description',
        'description',
        'image',
        'gallery',
        'purchase_price',
        'sale_price',
        'discount_price',
        'stock_qty',
        'has_variants',
        'is_featured',
        'is_special',
        'is_trending',
        'is_buy_one_get_one',
        'is_preorder',
        'is_dropshipping',
        'discount_value',
        'discount_type',
        'note',
        'video',
        'thumbnail',
        'video_url',
        'status',
        'seo_title',
        'seo_description',
        'seo_keywords',
        'seo_image',
        'faqs',
        'specifications',
        'is_active',
    ];

    protected $casts = [
        'gallery' => 'array',
        'faqs' => 'array',
        'specifications' => 'array',
        'has_variants' => 'boolean',
        'is_featured' => 'boolean',
        'is_special' => 'boolean',
        'is_trending' => 'boolean',
        'is_buy_one_get_one' => 'boolean',
        'is_preorder' => 'boolean',
        'is_dropshipping' => 'boolean',
        'is_active' => 'boolean',
        'purchase_price' => 'decimal:2',
        'sale_price' => 'decimal:2',
        'discount_price' => 'decimal:2',
        'discount_value' => 'decimal:2',
    ];

    //scope
    public function scopeDraft($query)
    {
        return $query->where('status', 'draft');
    }

    public function scopePublished($query)
    {
        return $query->where('status', 'published');
    }

    public function scopeInactive($query)
    {
        return $query->where('status', 'inactive');
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function vendor()
    {
        return $this->belongsTo(User::class, 'vendor_id');
    }

    public function categories()
    {
        return $this->belongsToMany(Category::class);
    }

    public function brand()
    {
        return $this->belongsTo(Brand::class);
    }

    public function unit()
    {
        return $this->belongsTo(Unit::class);
    }
}
