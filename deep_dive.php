<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

$vids = [71, 72, 73];

foreach ($vids as $vid) {
    echo "\n--- [ Variant ID: $vid ] ---\n";
    
    // Check all transfers for this variant
    $trans = \App\Models\StockTransfer::where('product_variant_id', $vid)->orderBy('created_at')->get();
    echo "  - Transfers IN (" . $trans->count() . " records)\n";
    foreach($trans as $t) echo "    * ". $t->status . " | Qty: ". $t->quantity_transferred ." | ". $t->quantity_unit . " | Time: ". $t->created_at ."\n";
    
    // Search ALL ServiceRequests regardless of status
    $sales = \App\Models\ServiceRequest::where('service_specific_data', 'LIKE', '%"product_variant_id":' . $vid . '%')->get();
    echo "  - Sales OUT (" . $sales->count() . " records)\n";
    foreach($sales as $s) echo "    * ". $s->status . " | Qty: ". $s->quantity ." | Time: ". $s->created_at ."\n";
}
