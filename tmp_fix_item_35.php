<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\ProductVariant;
use App\Models\ShoppingListItem;

$v73 = ProductVariant::find(73);
if ($v73) {
    echo "--- Variant 73 (Pineapple) ---\n";
    echo "ID: {$v73->id}\n";
    echo "Name: {$v73->variant_name}\n";
    echo "Servings/Bottle: {$v73->servings_per_pic}\n";
    echo "Items/Package: {$v73->items_per_package}\n";
    echo "Packaging: {$v73->packaging}\n";
}

$item35 = ShoppingListItem::find(35);
if ($item35) {
    echo "\n--- Item 35 ---\n";
    echo "Current Variant ID: {$item35->product_variant_id}\n";
    echo "Product Name: {$item35->product_name}\n";
    
    // Fix it to 73 as requested
    $item35->product_variant_id = 73;
    $item35->save();
    echo "Updated Item 35 to use Variant 73 (Pineapple)\n";
}
