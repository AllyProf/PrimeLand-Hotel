<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\ServiceRequest;

$ids = [8, 9, 10];
foreach($ids as $id) {
    $r = ServiceRequest::with('service')->find($id);
    if ($r) {
        echo "ID: {$r->id} | Service: {$r->service->name} | Cat: {$r->service->category} | Qty: {$r->quantity} | Meta: " . json_encode($r->service_specific_data) . "\n";
    }
}
