<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

$all = \App\Models\Product::all();
foreach ($all as $p) {
    echo "ID: " . $p->id . " | Product: " . $p->name . "\n";
    foreach ($p->variants as $v) {
        echo "   - Variant ID: " . $v->id . " | Name: " . $v->variant_name . "\n";
    }
}
