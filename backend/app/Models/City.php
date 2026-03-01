<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class City extends Model
{
    protected $fillable = [
        'name',
        'state_id',
        'cost',
        'status',
        'pathao_city_id',
        'pathao_zone_id',
        'pathao_area_id'
    ];

    public function state()
    {
        return $this->belongsTo(State::class);
    }
}
