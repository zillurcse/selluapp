<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    use HasFactory;

    protected $fillable = [
        'vendor_id',
        'parent_id',
        'name',
        'slug',
        'show_home',
        'image',
        'icon',
        'description',
        'is_active',
        'sort_order'
    ];

    public function products()
    {
        return $this->belongsToMany(Product::class);
    }
}
