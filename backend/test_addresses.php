<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$address = \App\Models\CustomerAddress::first();
echo json_encode($address ? $address->toArray() : null, JSON_PRETTY_PRINT);
