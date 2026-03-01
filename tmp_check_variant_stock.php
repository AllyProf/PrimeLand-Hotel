<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\ProductVariant;
use App\Models\StockTransfer;
use App\Models\ServiceRequest;

$vId = 73;
$variant = ProductVariant::find($vId);

// Calculate simplified stock (Transfers - Sales)
$received = StockTransfer::where('product_variant_id', $vId)->where('status', 'completed')->sum('quantity_transferred');
// Simple sale count (this is a bit complex in the real app, but let's estimate)
$sold = ServiceRequest::whereHas('service', function($q) use ($vId) {
    // This part is complex, let's just see transfers first
})->count();

echo "Variant: {$variant->variant_name}\n";
echo "Total Received (Transfers): {$received}\n";
