<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Booking;
use Illuminate\Support\Facades\DB;

echo "--- MONTH REVENUE DIAGNOSTIC ---\n";
$thisMonth = now()->startOfMonth();

$topBookings = Booking::where('created_at', '>=', $thisMonth)
    ->orderBy('amount_paid', 'desc')
    ->limit(20)
    ->get();

echo "Top 20 Bookings by Amount Paid this Month:\n";
echo str_pad("REF", 15) . " | " . str_pad("GUEST TYPE", 15) . " | " . str_pad("TOTAL PRICE", 15) . " | " . str_pad("PAID", 15) . "\n";
echo str_repeat("-", 65) . "\n";

foreach($topBookings as $b) {
    echo str_pad($b->booking_reference, 15) . " | " 
       . str_pad($b->guest_type ?? 'NULL', 15) . " | " 
       . str_pad(number_format($b->total_price), 15) . " | " 
       . str_pad(number_format($b->amount_paid), 15) . "\n";
}

$total = Booking::where('created_at', '>=', $thisMonth)->sum('amount_paid');
echo "\nTotal sum of amount_paid from Bookings this month: " . number_format($total) . " TZS\n";
