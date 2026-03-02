<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel' )->bootstrap();

use App\Models\ShoppingListItem;
use App\Models\ProductVariant;

$items = [
    'BONITE - Coca cola (500 ml)',
    'BONITE - Fanta Orange (500 ml)',
    'TBL - Heineken (500 ml)',
    'TBL - Serengeti Lager (500 ml)'
];

foreach ($items as $name) {
    $item = new ShoppingListItem(['product_name' => $name]);
    $v = $item->productVariant;
    if ($v) {
        $p = $v->product;
        echo "MATCH: '$name' -> " . ($p ? $p->name . " | " : "") . $v->variant_name . " (" . $v->measurement . ") [PIC:" . (int)$v->can_sell_as_pic . " SRV:" . (int)$v->can_sell_as_serving . "]\n";
    } else {
        echo "FAIL: '$name' not matched\n";
    }
}
