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
$room = Room::first();
if (!$room) {
    echo "No room found to test.";
    exit;
}

// Find a user to act as auth
$user = User::where('role', 'reception')->first() ?: User::first();
auth()->login($user);

$controller = new BookingController();

// Mock the request data for a Quick Invoice
$requestData = [
    'invoice_type' => 'individual',
    'guest_name' => 'Ally Prof Test',
    'guest_email' => 'allyict24@gmail.com', // Recipient
    'guest_phone' => '255678123456',
    'check_in' => date('Y-m-d', strtotime('+1 day')),
    'check_out' => date('Y-m-d', strtotime('+3 days')),
    'room_type' => $room->room_type,
    'room_id' => $room->id,
    'number_of_rooms' => 1,
    'total_price' => 150.00,
    'notes' => 'Testing invoice email sending logic after fixes.'
];

$request = new Request($requestData);

try {
    echo "Simulating 'Create Quick Invoice' process for allyprof7@gmail.com...\n";
    $response = $controller->storeInvoice($request);
    
    echo "Response: " . json_encode($response->getData(), JSON_PRETTY_PRINT) . "\n";
    
    if ($response->getData()->success) {
        echo "\nSUCCESS: Invoice logic executed. Check your email (allyprof7@gmail.com) for the PDF.\n";
    } else {
        echo "\nFAILED: Controller returned failure.\n";
    }
} catch (\Exception $e) {
    echo "\nFATAL ERROR: " . $e->getMessage() . "\n";
    echo "Trace: " . $e->getTraceAsString() . "\n";
}
