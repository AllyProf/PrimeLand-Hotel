<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

$name = 'Henes';
echo "Searching for '$name' in ServiceRequest...\n";

// Search in notes or data
$requests = \App\Models\ServiceRequest::where(function($q) use ($name) {
    $q->where('reception_notes', 'LIKE', "%$name%")
      ->orWhere('guest_request', 'LIKE', "%$name%")
      ->orWhere('service_specific_data', 'LIKE', "%$name%");
})->get();

if ($requests->isEmpty()) {
    echo "No direct matches found in ServiceRequest.\n";
} else {
    foreach ($requests as $r) {
        echo "ID: {$r->id} | Status: {$r->status} | Qty: {$r->quantity} | Date: {$r->created_at}\n";
        echo "  - Notes: {$r->reception_notes}\n";
        echo "  - Data: " . json_encode($r->service_specific_data) . "\n";
    }
}

// Check if there is a Service with that name
echo "\nChecking Services table for '$name'...\n";
$services = \App\Models\Service::where('name', 'LIKE', "%$name%")->get();
foreach ($services as $s) {
    echo "Service ID: {$s->id} | Name: {$s->name} | Price: {$s->price_tsh}\n";
    // Check requests for this service ID
    $totalSales = \App\Models\ServiceRequest::where('service_id', $s->id)->where('status', 'completed')->sum('quantity');
    echo "  - Completed Sales via this Service ID: $totalSales\n";
}
