<?php

use App\Models\User;
use App\Models\Order;
use App\Models\Coupon;
use Illuminate\Http\Request;
use App\Http\Controllers\Vendor\ReportController;

// Find a vendor to test with
$vendor = User::whereHas('roles', function($q){ $q->where('name', 'vendor'); })->first();

if (!$vendor) {
    echo "No vendor found to test with.\n";
    exit;
}

echo "Testing with Vendor ID: {$vendor->id} ({$vendor->email})\n";

// Mock the request
$request = new Request();
$request->setUserResolver(function () use ($vendor) {
    return $vendor;
});

$controller = new ReportController();
$response = $controller->coupons($request);
$data = $response->getData(true);

echo "Response Structure:\n";
print_r(array_keys($data));

if (isset($data['kpi'])) {
    echo "\nKPI Data:\n";
    print_r($data['kpi']);
} else {
    echo "\nERROR: 'kpi' missing in response.\n";
}

if (isset($data['top_coupons'])) {
    echo "\nTop Coupons Count: " . count($data['top_coupons']) . "\n";
    if (count($data['top_coupons']) > 0) {
        echo "Example Coupon Stat:\n";
        print_r($data['top_coupons'][0]);
    }
} else {
    echo "\nERROR: 'top_coupons' missing in response.\n";
}
