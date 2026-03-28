<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NewsletterSubscription extends Model
{
    use HasFactory;

    protected $fillable = [
        'vendor_id',
        'email',
        'source',
        'is_active',
        'tags',
        'last_email_sent_at',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'tags' => 'array',
        'last_email_sent_at' => 'datetime',
    ];

    public function vendor()
    {
        return $this->belongsTo(User::class, 'vendor_id');
    }
}
