<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$rows = \DB::table('service_catalog')->get();
echo json_encode($rows, JSON_PRETTY_PRINT);
