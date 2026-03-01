<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\Mail;
use App\Models\Booking;

$booking = Booking::first();
if (!$booking) {
    echo "No booking found to test with.";
    exit;
}

try {
    echo "Attempting to send a test email to reception@primeland.com...\n";
    Mail::raw('This is a test email to verify SMTP configuration.', function ($message) {
        $message->to('reception@primeland.com')
                ->subject('SMTP Configuration Test');
    });
    echo "SUCCESS: Email sent successfully!\n";
} catch (\Exception $e) {
    echo "FAILED: Email could not be sent.\n";
    echo "Error: " . $e->getMessage() . "\n";
}
