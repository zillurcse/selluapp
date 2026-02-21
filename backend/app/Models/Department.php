<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Department extends Model
{
    use HasFactory;

    protected $table = 'hrm_departments';

    protected $fillable = [
        'vendor_id',
        'name',
        'description',
        'status',
    ];

    public function vendor()
    {
        return $this->belongsTo(User::class, 'vendor_id');
    }

    public function designations()
    {
        return $this->hasMany(Designation::class, 'department_id');
    }

    public function employees()
    {
        return $this->hasMany(Employee::class, 'department_id');
    }

    public function getEmployeeCountAttribute(): int
    {
        return $this->employees()->count();
    }
}
