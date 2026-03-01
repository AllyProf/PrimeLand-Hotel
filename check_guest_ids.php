<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Booking;

$bookings = Booking::whereNotNull('guest_id')->get();
foreach ($bookings as $b) {
    echo "ID: {$b->id}, GuestID: {$b->guest_id}\n";
}
