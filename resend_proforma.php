<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Booking;
use App\Mail\BookingConfirmationMail;
use Illuminate\Support\Facades\Mail;
use Barryvdh\DomPDF\Facade\Pdf;

try {
    $booking = Booking::where('booking_reference', 'INVEPMERDJV')->first();
    if (!$booking) {
        die("Booking not found\n");
    }
    
    $password = strtoupper($booking->first_name);
    
    // Force status to pending for proforma header
    $booking->status = 'pending';

    $pdf = Pdf::loadView('emails.pdf-individual-invoice', [
        'booking' => $booking->load('room'),
        'password' => $password,
        'paymentPercentage' => 0,
        'remainingAmount' => $booking->total_price
    ]);
    $pdfData = $pdf->output();

    // Send Mail
    Mail::to($booking->guest_email)->send(new BookingConfirmationMail(
        $booking,
        $password,
        0,
        $booking->total_price,
        "Proforma Invoice: This is a quotation based on your inquiry. Please confirm within 48 hours.",
        $pdfData
    ));
    
    // Send SMS
    $smsService = new \App\Services\SmsService();
    $smsMessage = "Dear {$booking->first_name}, we've sent your PrimeLand Hotel Inquiry invoice to {$booking->guest_email}. Ref: {$booking->booking_reference}. Valid for 48h.";
    $res = $smsService->sendSingle($booking->guest_phone, $smsMessage);
    
    echo "SUCCESS\n";
    print_r($res);
} catch (\Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
}
