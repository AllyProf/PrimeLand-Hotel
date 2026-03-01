<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== DUMPING ALL RELEVANT SERVICE REQUESTS (COMPLETED) ===\n";

$barCategories = ['drinks', 'beverage', 'alcoholic_beverage', 'non_alcoholic_beverage', 'water', 'juices', 'energy_drinks', 'spirits', 'whiskey', 'wine', 'beers', 'liquor', 'food', 'restaurant', 'bar'];

$sales = \App\Models\ServiceRequest::where('status', 'completed')
    ->get();

foreach ($sales as $s) {
    if (empty($s->service_specific_data)) continue;
    
    $data = $s->service_specific_data;
    if (isset($data['product_variant_id']) || isset($data['product_id'])) {
        echo "ID: {$s->id} | Qty: {$s->quantity} | ServiceID: {$s->service_id} | Date: {$s->completed_at}\n";
        echo "  - JSON: " . json_encode($data) . "\n";
    }
}

echo "\n=== DUMPING ALL PRODUCTS & VARIANTS ===\n";
foreach (\App\Models\Product::all() as $p) {
    echo "P: {$p->name} (ID: {$p->id}) | Cat: {$p->category}\n";
    foreach ($p->variants as $v) {
        echo "   V: {$v->variant_name} (ID: {$v->id}) | Op: {$v->opening_stock}\n";
    }
}
