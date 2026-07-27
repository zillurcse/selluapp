<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ShopSetting extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'group',
        'key',
        'value'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Load all key/value settings for a group, with optional defaults merged in.
     */
    public static function getGroupSettings(int $userId, string $group, array $defaults = []): array
    {
        $settings = static::where('user_id', $userId)
            ->where('group', $group)
            ->pluck('value', 'key')
            ->map(function ($val) {
                $decoded = json_decode($val, true);

                return (json_last_error() === JSON_ERROR_NONE) ? $decoded : $val;
            })
            ->toArray();

        return array_merge($defaults, $settings);
    }

    public static function toBool(mixed $value): bool
    {
        return $value === true || $value === 'true' || $value === '1' || $value === 1;
    }
}
