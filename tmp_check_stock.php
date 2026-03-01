<?php
include 'vendor/autoload.php';
$app = include 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->boot();
use App\Models\KitchenInventoryItem;
use App\Models\ProductVariant;

echo "--- Kitchen Inventory ---\n";
foreach(KitchenInventoryItem::where('name', 'LIKE', '%Fanta%')->get() as $i) {
    echo $i->name . ': ' . $i->current_stock . "\n";
}

echo "\n--- Product Variants ---\n";
foreach(ProductVariant::where('variant_name', 'LIKE', '%Fanta%')->orWhereHas('product', function($q){$q->where('name', 'LIKE', '%Fanta%');})->get() as $v) {
    echo $v->product->name . ' (' . $v->variant_name . '): ' . $v->servings_per_pic . " servings/pic\n";
}
