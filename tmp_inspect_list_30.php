<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\ShoppingList;
use App\Models\ShoppingListItem;
use App\Models\ProductVariant;

$list = ShoppingList::find(30);
if (!$list) {
    echo "List 30 not found\n";
    exit;
}

echo "--- Shopping List 30: {$list->name} ---\n";
foreach($list->items as $item) {
    echo "ID: {$item->id} | Name: {$item->product_name} | Variant ID: {$item->product_variant_id} | Qty: {$item->purchased_quantity}\n";
    $v = $item->productVariant;
    if ($v) {
        echo "  - Matched Variant: ID {$v->id} | Name: {$v->variant_name} | Servings/Bottle: {$v->servings_per_pic}\n";
    } else {
        echo "  - No Variant Matched\n";
    }
}

echo "\n--- Fanta Variants in DB ---\n";
$variants = ProductVariant::whereHas('product', function($q) {
    $q->where('name', 'LIKE', '%Fanta%');
})->get();
foreach($variants as $v) {
    echo "ID: {$v->id} | Product: {$v->product->name} | Variant: {$v->variant_name} | Servings/Bottle: {$v->servings_per_pic}\n";
}
