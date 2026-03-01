<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

$names = ['Henes', 'Fanta Orange', 'Fanta pinnaple'];

foreach ($names as $name) {
    echo "\n--- [ $name ] ---\n";
    $product = \App\Models\Product::where('name', 'LIKE', "%$name%")->first();
    if (!$product) { echo "Product not found.\n"; continue; }
    
    foreach ($product->variants as $v) {
        echo "Variant ID: {$v->id} | Name: {$v->variant_name}\n";
        
        // Transfers IN
        $transIn = \App\Models\StockTransfer::where('product_variant_id', $v->id)->where('status', 'completed')->sum('quantity_transferred');
        echo "  - Transfers IN (completed): $transIn\n";
        
        // Sales OUT
        $salesOut = \App\Models\ServiceRequest::where('status', 'completed')
            ->where('service_specific_data', 'LIKE', '%"product_variant_id":' . $v->id . '%')
            ->sum('quantity');
        echo "  - Sales OUT (completed): $salesOut\n";
        
        // Pending Sales (Maybe they should be deducted too?)
        $pending = \App\Models\ServiceRequest::whereIn('status', ['confirmed', 'pending'])
            ->where('service_specific_data', 'LIKE', '%"product_variant_id":' . $v->id . '%')
            ->sum('quantity');
        echo "  - Sales (pending/confirmed): $pending\n";
        
        $calc = $transIn - $salesOut;
        echo "  => Calculated Stock: $calc\n";
    }
}
