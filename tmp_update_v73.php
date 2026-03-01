<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\ProductVariant;

$v73 = ProductVariant::find(73);
if ($v73) {
    echo "Updating Variant 73 servings from {$v73->servings_per_pic} to 10\n";
    $v73->servings_per_pic = 10;
    $v73->selling_unit = 'glass';
    $v73->can_sell_as_serving = true;
    $v73->save();
}
