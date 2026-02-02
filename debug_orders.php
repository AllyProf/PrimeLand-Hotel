<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$ids = [33, 38, 40, 43, 45, 47, 48, 50, 51];
$consumptions = \DB::table('recipe_consumptions')
    ->whereIn('service_request_id', $ids)
    ->get();

echo "Total consumption records: " . $consumptions->count() . "\n";
foreach ($consumptions->groupBy('product_id') as $prodId => $group) {
    echo "Product ID: $prodId, Total Consumed: " . $group->sum('quantity_consumed') . "\n";
}
