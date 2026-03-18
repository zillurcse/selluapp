<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Order;
use App\Models\ShopSetting;
use App\Services\Courier\SteadfastService;

$order = Order::latest()->first();
if (!$order) {
    echo "No orders found\n";
    exit;
}

$vendorId = $order->user_id;
echo "Vendor ID: $vendorId\n";

$setting = ShopSetting::where('user_id', $vendorId)
    ->where('group', 'delivery')
    ->where('key', 'steadfast')
    ->first();

if ($setting) {
    echo "Steadfast Setting found: " . $setting->value . "\n";
} else {
    echo "Steadfast Setting NOT found for vendor $vendorId\n";
}

$service = new SteadfastService($vendorId);
// Reflection to access protected/private properties for debugging
$reflection = new ReflectionClass($service);
$configProp = $reflection->getProperty('config');
$configProp->setAccessible(true);
$config = $configProp->getValue($service);

echo "Loaded Config: " . json_encode($config, JSON_PRETTY_PRINT) . "\n";
