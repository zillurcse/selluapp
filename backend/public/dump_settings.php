<?php
// backend/public/dump_settings.php
use Illuminate\Support\Facades\DB;

require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';

$app->make(\Illuminate\Contracts\Http\Kernel::class)->handle(
    \Illuminate\Http\Request::capture()
);

echo "--- Business Settings ---\n";
foreach (\App\Models\BusinessSetting::all() as $s) {
    echo "{$s->type}: {$s->value}\n";
}

echo "\n--- Shop Settings (Vendor 3) ---\n";
foreach (\App\Models\ShopSetting::where('user_id', 3)->get() as $s) {
    echo "{$s->key}: {$s->value}\n";
}

echo "\n--- Cities ---\n";
foreach (\App\Models\City::limit(5)->get() as $c) {
    echo "ID: {$c->id}, Name: {$c->name}, Cost: {$c->cost}\n";
}
