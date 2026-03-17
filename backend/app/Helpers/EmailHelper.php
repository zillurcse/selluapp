<?php

namespace App\Helpers;

use App\Models\ShopSetting;
use Illuminate\Support\Facades\Config;

class EmailHelper
{
    /**
     * Set dynamic SMTP configuration for a specific vendor.
     *
     * @param int $vendorId
     * @return void
     */
    public static function setMailConfig($vendorId)
    {
        $settings = ShopSetting::where('user_id', $vendorId)
            ->where('group', 'smtp_settings')
            ->get()
            ->pluck('value', 'key');

        if ($settings->isNotEmpty()) {
            Config::set('mail.mailers.smtp.host', $settings->get('host', config('mail.mailers.smtp.host')));
            Config::set('mail.mailers.smtp.port', $settings->get('port', config('mail.mailers.smtp.port')));
            Config::set('mail.mailers.smtp.encryption', $settings->get('encryption', config('mail.mailers.smtp.encryption')));
            Config::set('mail.mailers.smtp.username', $settings->get('username', config('mail.mailers.smtp.username')));
            Config::set('mail.mailers.smtp.password', $settings->get('password', config('mail.mailers.smtp.password')));
            Config::set('mail.from.address', $settings->get('from_address', config('mail.from.address')));
            Config::set('mail.from.name', $settings->get('from_name', config('mail.from.name')));
        }
    }
}
