<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Attendance extends Model
{
    use HasFactory;

    protected $table = 'hrm_attendances';

    protected $fillable = [
        'vendor_id',
        'employee_id',
        'date',
        'check_in',
        'check_out',
        'working_hours',
        'status',
        'notes',
    ];

    protected $casts = [
        'date' => 'date',
        'working_hours' => 'float',
    ];

    public function vendor()
    {
        return $this->belongsTo(User::class, 'vendor_id');
    }

    public function employee()
    {
        return $this->belongsTo(Employee::class, 'employee_id');
    }
}
