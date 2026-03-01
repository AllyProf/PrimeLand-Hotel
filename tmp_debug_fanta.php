<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$response = $kernel->handle(
    $request = Illuminate\Http\Request::capture()
);

use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\KitchenInventoryItem;

echo "--- PRODUCTS matching 'Fanta' ---\n";
$products = Product::where('name', 'LIKE', '%Fanta%')->get();
foreach($products as $p) {
    echo "ID: {$p->id} | Name: {$p->name}\n";
    foreach($p->variants as $v) {
        echo "  - Variant ID: {$v->id} | Name: {$v->variant_name} | Servings: {$v->servings_per_pic} | Packaging: {$v->packaging} | Items/Pkg: {$v->items_per_package}\n";
    }
}

echo "\n--- KITCHEN INVENTORY matching 'Fanta' ---\n";
$items = KitchenInventoryItem::where('name', 'LIKE', '%Fanta%')->get();
foreach($items as $i) {
    echo "ID: {$i->id} | Name: {$i->name} | Stock: {$i->current_stock} | Unit: {$i->unit}\n";
}
