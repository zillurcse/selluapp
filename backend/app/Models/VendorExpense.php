<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class VendorExpense extends Model
{
    protected $fillable = [
        'user_id',
        'title',
        'description',
        'amount',
        'expense_date',
        'status',
    ];

    protected $casts = [
        'expense_date' => 'date',
        'amount' => 'decimal:2',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
