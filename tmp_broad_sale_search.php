<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\ServiceRequest;

echo "--- Broad Case-Insensitive Search for 'Henes' or 'Heineken' in ALL Service Requests --- \n";
$requests = ServiceRequest::where('reception_notes', 'LIKE', '%Henes%')
    ->orWhere('reception_notes', 'LIKE', '%Heine%')
    ->orWhere('walk_in_name', 'LIKE', '%Henes%')
    ->orWhere('walk_in_name', 'LIKE', '%Heine%')
    ->orWhereHas('service', function($q) {
        $q->where('name', 'LIKE', '%Henes%')->orWhere('name', 'LIKE', '%Heine%');
    })->get();

foreach($requests as $r) {
    echo "ID: {$r->id} | Status: {$r->status} | Qty: {$r->quantity} | Price: {$r->unit_price_tsh} | Date: {$r->created_at} | Meta: " . json_encode($r->service_specific_data) . " | Notes: {$r->reception_notes}\n";
}
