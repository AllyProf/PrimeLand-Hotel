<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Http\Controllers\BookingController;
use Illuminate\Http\Request;
use App\Models\Room;
use App\Models\User;

// Find a real room
$room = Room::where('status', 'available')->first() ?: Room::first();
if (!$room) {
    echo "No room found to test.";
    exit;
}

// Find a user to act as auth
$user = User::where('role', 'reception')->first() ?: User::first();
auth()->login($user);

$controller = new BookingController();

// Mock the request data for a Manual Booking (storeManual)
$requestData = [
    'full_name' => 'Ally Prof Booking Test',
    'guest_email' => 'allyict24@gmail.com', // Recipient
    'guest_phone' => '+255678123456',
    'country_code' => '+255',
    'guest_type' => 'tanzanian',
    'room_id' => $room->id,
    'check_in' => date('Y-m-d', strtotime('+10 days')),
    'check_in_time' => '14:00',
    'check_out' => date('Y-m-d', strtotime('+12 days')),
    'check_out_time' => '10:00',
    'number_of_guests' => 1,
    'total_price' => 100.00,
    'amount_paid' => 50.00,
    'payment_method' => 'cash',
    'payment_reference' => 'TEST-REF'
];

$request = new Request($requestData);

try {
    echo "Simulating 'Individual Booking' process (storeManual) for allyict24@gmail.com...\n";
    $response = $controller->storeManual($request);
    
    // storeManual usually returns redirect or json depending on request header
    echo "Response: " . json_encode($response, JSON_PRETTY_PRINT) . "\n";
    echo "\nSUCCESS: Booking logic executed. Check your email.\n";
} catch (\Exception $e) {
    echo "\nFATAL ERROR: " . $e->getMessage() . "\n";
    echo "Trace: " . $e->getTraceAsString() . "\n";
}
