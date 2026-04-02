<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$b = App\Models\Booking::where('booking_reference', 'like', 'BK5U7%')->first();
var_dump($b->total_price);
var_dump($b->getAttributes()['total_price']);
