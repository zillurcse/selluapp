<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use App\Traits\BelongsToVendor;

class Designation extends Model
{
    use HasFactory, BelongsToVendor;

    protected $table = 'hrm_designations';

    protected $fillable = [
        'vendor_id',
        'department_id',
        'title',
        'description',
        'status',
    ];

    public function vendor()
    {
        return $this->belongsTo(User::class, 'vendor_id');
    }

    public function department()
    {
        return $this->belongsTo(Department::class, 'department_id');
    }

    public function employees()
    {
        return $this->hasMany(Employee::class, 'designation_id');
    }
}
