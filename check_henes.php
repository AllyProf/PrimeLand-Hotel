<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

$vid = 71;
$v = \App\Models\ProductVariant::find($vid);
echo "Variant: {$v->variant_name} (ID: {$v->id})\n";
echo "Opening Stock: " . var_export($v->opening_stock, true) . "\n";
echo "Servings per Pic: " . var_export($v->servings_per_pic, true) . "\n";

$trans = \App\Models\StockTransfer::where('product_variant_id', $vid)->where('status', 'completed')->get();
$totalIn = 0;
foreach($trans as $t) {
    $itemsPerPkg = $t->productVariant->items_per_package ?? 1;
    $pics = ($t->quantity_unit === 'packages') ? ($t->quantity_transferred * $itemsPerPkg) : $t->quantity_transferred;
    $totalIn += $pics;
    echo "   Transfer: Qty {$t->quantity_transferred} {$t->quantity_unit} = $pics pics\n";
}

$sales = \App\Models\ServiceRequest::where('status', 'completed')
    ->where(function($q) use ($vid) {
        $q->where('service_specific_data', 'LIKE', '%"product_variant_id":' . $vid . '%')
          ->orWhere('service_specific_data', 'LIKE', '%"product_variant_id":"' . $vid . '"%');
    })->get();

$totalOut = 0;
foreach($sales as $s) {
    $meta = $s->service_specific_data;
    $method = $meta['selling_method'] ?? 'pic';
    $qty = (float)$s->quantity;
    if ($method === 'pic' || $method === 'bottle') {
        $deduction = $qty;
    } else {
        $ratio = (float)($v->servings_per_pic > 0 ? $v->servings_per_pic : 1);
        $deduction = $qty / $ratio;
    }
    $totalOut += $deduction;
    echo "   Sale ID {$s->id}: Method $method | Qty $qty | Deduction $deduction | Ratio $ratio\n";
}

$final = ($v->opening_stock ?? 0) + $totalIn - $totalOut;
echo "Final Calculated Stock: $final\n";
