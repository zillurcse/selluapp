<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';

$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\ShopSetting;
use App\Models\SmsTemplate;

$vendorId = 5; // Assuming from logs

echo "Checking settings for Vendor ID: $vendorId\n";

$smsSetting = ShopSetting::where('user_id', $vendorId)
    ->where('group', 'third_party_apis')
    ->where('key', 'sms')
    ->first();

if ($smsSetting) {
    echo "SMS Setting Found: " . $smsSetting->value . "\n";
    $config = json_decode($smsSetting->value, true);
    echo "Provider: " . ($config['provider'] ?? 'NONE') . "\n";
} else {
    echo "SMS Setting NOT Found\n";
}

$otpTemplate = SmsTemplate::where('user_id', $vendorId)
    ->where('type', 'otp')
    ->where('is_active', true)
    ->first();

if ($otpTemplate) {
    echo "OTP Template Found: " . $otpTemplate->content . "\n";
} else {
    echo "OTP Template NOT Found or Inactive\n";
}
