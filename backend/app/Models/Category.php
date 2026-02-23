<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use App\Traits\BelongsToVendor;

class Category extends Model
{
    use HasFactory, BelongsToVendor;

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

    public function parent()
    {
        return $this->belongsTo(Category::class, 'parent_id');
    }

    public function children()
    {
        return $this->hasMany(Category::class, 'parent_id');
    }

    public static function getDescendantsSlugs($slugs)
    {
        if (is_string($slugs)) {
            $slugs = [$slugs];
        }

        $allSlugs = $slugs;
        $categories = self::whereIn('slug', $slugs)->get();
        
        foreach ($categories as $category) {
            $childSlugs = $category->children()->pluck('slug')->toArray();
            if (!empty($childSlugs)) {
                $allSlugs = array_merge($allSlugs, self::getDescendantsSlugs($childSlugs));
            }
        }

        return array_unique($allSlugs);
    }
}
