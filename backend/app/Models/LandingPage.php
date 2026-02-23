<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

use App\Traits\BelongsToVendor;

class LandingPage extends Model
{
    use BelongsToVendor;
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'landing_page_type',
        'vendor_id',
        'product_id',
        'template_name',
        'title',
        'slug',
        'settings',
        'status',
    ];

    protected $casts = [
        'settings' => 'array',
    ];

    public function vendor()
    {
        return $this->belongsTo(User::class, 'vendor_id');
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
