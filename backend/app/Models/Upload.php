<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\BelongsToVendor;

class Upload extends Model
{
    use SoftDeletes, BelongsToVendor;

    protected $fillable = [
        'vendor_id',
        'file_name',
        'file_original_name',
        'extension',
        'type',
        'file_size',
        'file_path',
        'file_url',
    ];
}

