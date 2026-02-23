<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\GlobalSetting;

class GlobalSettingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $settings = [
            // General settings
            [
                'group' => 'general',
                'key' => 'site_name',
                'value' => 'Sellue Ecom',
                'type' => 'text'
            ],
            [
                'group' => 'general',
                'key' => 'site_tagline',
                'value' => 'Premium Multi-vendor E-commerce platform',
                'type' => 'text'
            ],
            [
                'group' => 'general',
                'key' => 'site_logo',
                'value' => null,
                'type' => 'image'
            ],
            [
                'group' => 'general',
                'key' => 'site_favicon',
                'value' => null,
                'type' => 'image'
            ],

            // Contact Settings
            [
                'group' => 'contact',
                'key' => 'contact_email',
                'value' => 'admin@sellue.com',
                'type' => 'text'
            ],
            [
                'group' => 'contact',
                'key' => 'contact_phone',
                'value' => '+1234567890',
                'type' => 'text'
            ],
            [
                'group' => 'contact',
                'key' => 'contact_address',
                'value' => 'Silicon Valley, CA, USA',
                'type' => 'textarea'
            ],

            // Social Settings
            [
                'group' => 'social',
                'key' => 'facebook_url',
                'value' => 'https://facebook.com/sellue',
                'type' => 'text'
            ],
            [
                'group' => 'social',
                'key' => 'twitter_url',
                'value' => 'https://twitter.com/sellue',
                'type' => 'text'
            ],
            [
                'group' => 'social',
                'key' => 'instagram_url',
                'value' => 'https://instagram.com/sellue',
                'type' => 'text'
            ],

            // Script settings (Header/Footer)
            [
                'group' => 'scripts',
                'key' => 'header_scripts',
                'value' => null,
                'type' => 'textarea'
            ],
            [
                'group' => 'scripts',
                'key' => 'footer_scripts',
                'value' => null,
                'type' => 'textarea'
            ],

            // Appearance settings
            [
                'group' => 'appearance',
                'key' => 'primary_color',
                'value' => '#6366f1',
                'type' => 'color'
            ],
            [
                'group' => 'appearance',
                'key' => 'dark_mode_enabled',
                'value' => 'true',
                'type' => 'boolean'
            ],

            // Mail Settings
            [
                'group' => 'mail',
                'key' => 'mail_mailer',
                'value' => 'smtp',
                'type' => 'text'
            ],
            [
                'group' => 'mail',
                'key' => 'mail_host',
                'value' => 'smtp.mailtrap.io',
                'type' => 'text'
            ],
            [
                'group' => 'mail',
                'key' => 'mail_port',
                'value' => '2525',
                'type' => 'text'
            ],
            [
                'group' => 'mail',
                'key' => 'mail_username',
                'value' => null,
                'type' => 'text'
            ],
            [
                'group' => 'mail',
                'key' => 'mail_password',
                'value' => null,
                'type' => 'text'
            ],
            [
                'group' => 'mail',
                'key' => 'mail_encryption',
                'value' => 'tls',
                'type' => 'text'
            ],
            [
                'group' => 'mail',
                'key' => 'mail_from_address',
                'value' => 'hello@sellue.com',
                'type' => 'text'
            ],

            // Payment Settings
            [
                'group' => 'payments',
                'key' => 'stripe_enabled',
                'value' => 'false',
                'type' => 'boolean'
            ],
            [
                'group' => 'payments',
                'key' => 'stripe_key',
                'value' => null,
                'type' => 'text'
            ],
            [
                'group' => 'payments',
                'key' => 'stripe_secret',
                'value' => null,
                'type' => 'text'
            ],
            [
                'group' => 'payments',
                'key' => 'paypal_enabled',
                'value' => 'false',
                'type' => 'boolean'
            ],
        ];

        foreach ($settings as $setting) {
            GlobalSetting::updateOrCreate(
                ['key' => $setting['key']],
                $setting
            );
        }
    }
}
