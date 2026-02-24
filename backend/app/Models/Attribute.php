<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Attribute extends Model
{
    use HasFactory;

    protected $fillable = ['vendor_id', 'name', 'type', 'description', 'guide_image'];

    public function values()
    {
        return $this->hasMany(AttributeValue::class);
    }
}
