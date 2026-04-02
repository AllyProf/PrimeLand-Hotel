<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$b = App\Models\Booking::where('booking_reference', 'like', 'BK5U7%')->first();
echo "guest_type: {$b->guest_type}\n";
echo "total_price: {$b->total_price}\n";
