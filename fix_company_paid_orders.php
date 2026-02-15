<?php

/**
 * Fix Existing Company-Paid Orders
 * 
 * This script updates existing service requests that:
 * - Are linked to company-paid bookings (payment_responsibility = 'company')
 * - Have payment_status = 'unpaid' or 'pending'
 * - Should have been auto-charged to room
 * 
 * Run with: php fix_company_paid_orders.php
 */

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\ServiceRequest;
use App\Models\Booking;

echo "=== Fixing Company-Paid Orders ===\n\n";

// Find all service requests that:
// 1. Are linked to a booking
// 2. The booking has payment_responsibility = 'company'
// 3. The order has payment_status = 'unpaid' or 'pending'
// 4. The order is completed (already served)

$ordersToFix = ServiceRequest::with('booking')
    ->whereNotNull('booking_id')
    ->where('status', 'completed')
    ->whereIn('payment_status', ['unpaid', 'pending'])
    ->whereHas('booking', function($q) {
        $q->where('payment_responsibility', 'company');
    })
    ->get();

echo "Found " . $ordersToFix->count() . " orders to fix.\n\n";

if ($ordersToFix->isEmpty()) {
    echo "No orders need fixing. All company-paid orders are correctly charged!\n";
    exit(0);
}

foreach ($ordersToFix as $order) {
    $itemName = $order->service_specific_data['item_name'] ?? $order->service->name ?? 'Unknown';
    $room = $order->booking->room->room_number ?? 'N/A';
    $guest = $order->booking->guest_name ?? 'N/A';
    
    echo "Order ID: {$order->id}\n";
    echo "  Item: {$itemName}\n";
    echo "  Room: {$room}\n";
    echo "  Guest: {$guest}\n";
    echo "  Current Payment Status: {$order->payment_status}\n";
    
    // Update to room_charge
    $order->update([
        'payment_status' => 'room_charge',
        'payment_method' => 'room_charge',
        'reception_notes' => ($order->reception_notes ?? '') . " | Auto-charged to Company (Fixed)"
    ]);
    
    echo "  ✓ Updated to: room_charge\n\n";
}

echo "=== Complete! ===\n";
echo "Fixed {$ordersToFix->count()} orders.\n";
echo "These orders will no longer appear in the pending queue.\n";
