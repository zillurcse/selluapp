<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payroll extends Model
{
    use HasFactory;

    protected $table = 'hrm_payrolls';

    protected $fillable = [
        'vendor_id',
        'employee_id',
        'month',
        'basic_salary',
        'allowances',
        'bonuses',
        'deductions',
        'tax',
        'net_salary',
        'working_days',
        'present_days',
        'leave_days',
        'status',
        'notes',
        'payment_date',
    ];

    protected $casts = [
        'basic_salary' => 'float',
        'allowances' => 'float',
        'bonuses' => 'float',
        'deductions' => 'float',
        'tax' => 'float',
        'net_salary' => 'float',
        'payment_date' => 'date',
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
