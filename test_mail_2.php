<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\Mail;
use App\Models\Booking;

$to = 'allyprof7@gmail.com';
try {
    echo "Attempting to send a test email to {$to}...\n";
    echo "Using SMTP: " . config('mail.mailers.smtp.host') . ":" . config('mail.mailers.smtp.port') . " (" . config('mail.mailers.smtp.encryption') . ")\n";
    Mail::raw('This is a test email to verify SMTP configuration at PrimeLand.', function ($message) use ($to) {
        $message->to($to)
                ->subject('PrimeLand SMTP Configuration TEST');
    });
    echo "SUCCESS: Email sent successfully!\n";
} catch (\Exception $e) {
    echo "FAILED: Email could not be sent.\n";
    echo "Error: " . $e->getMessage() . "\n";
    
    // Help identify what's wrong
    if (strpos($e->getMessage(), 'Connection could not be established') !== false) {
        echo "TIPS:\n";
        echo "1. Double-check if the PORT is correct (587/TLS or 465/SSL).\n";
        echo "2. Check if your internet provider OR firewall (Windows/XAMPP) is blocking outbound SMTP ports.\n";
        echo "3. Try using host 'mail.primelandhotel.com' instead of 'primelandhotel.com'.\n";
    }
}
