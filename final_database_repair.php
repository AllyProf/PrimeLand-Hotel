<?php
/**
 * PrimeLand Hotel - Comprehensive Database Repair Utility
 * 
 * This script resolves historical data corruption caused by the revenue inflation bug.
 * It deflates pricing in Bookings, Services, and Shift Reconciliations.
 * 
 * INSTRUCTIONS:
 * 1. Upload this file to your server root.
 * 2. Run via CLI: php final_database_repair.php
 * 3. Delete this file after successful execution for security.
 */

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Booking;
use App\Models\Shift;
use App\Models\ServiceRequest;
use App\Models\DayService;
use Illuminate\Support\Facades\DB;

echo "========================================\n";
echo "PRIME LAND HOTEL - DATABASE REPAIR\n";
echo "========================================\n\n";

DB::beginTransaction();

try {
    // 1. REPAIR BOOKINGS
    // Deflate TZS bookings/payments that were multiplied by the exchange rate (~2500-2600x)
    // Target records where EITHER total_price OR amount_paid is suspiciously high for a local guest
    $inflatedBookings = Booking::where(function($q) {
            $q->where('total_price', '>', 1000000)
              ->orWhere('amount_paid', '>', 1000000);
        })
        ->where(function($q) {
            $q->where('guest_type', 'tanzanian')
              ->orWhere('guest_type', 'guest_tanzanian');
        })->get();

    echo "1. Checking Bookings...\n";
    foreach ($inflatedBookings as $b) {
        $oldPrice = $b->total_price;
        $oldPaid  = $b->amount_paid;
        
        // Deflate only if they are significantly higher than reasonable (e.g. > 1M)
        $newPrice = ($oldPrice > 1000000) ? round($oldPrice / 2600) : $oldPrice;
        $newPaid  = ($oldPaid > 1000000) ? round($oldPaid / 2600) : $oldPaid;
        
        $b->update(['total_price' => $newPrice, 'amount_paid' => $newPaid]);
        echo "   [FIXED] Booking #{$b->booking_reference}: Price " . number_format($oldPrice) . " -> " . number_format($newPrice) . " | Paid " . number_format($oldPaid) . " -> " . number_format($newPaid) . " TZS\n";
    }

    // 2. REPAIR SERVICE REQUESTS (F&B / Laundry etc)
    $inflatedServices = ServiceRequest::where('total_price_tsh', '>', 1000000)->get();
    echo "\n2. Checking Service Requests...\n";
    foreach ($inflatedServices as $sr) {
        $oldPrice = $sr->total_price_tsh;
        $newPrice = round($oldPrice / 2600);
        $sr->update(['total_price_tsh' => $newPrice]);
        echo "   [FIXED] Service #{$sr->id}: " . number_format($oldPrice) . " -> " . number_format($newPrice) . " TZS\n";
    }

    // 3. REPAIR DAY SERVICES
    $inflatedDayServices = DayService::where('amount_paid', '>', 1000000)
        ->where(function($q) {
            $q->where('guest_type', 'tanzanian')
              ->orWhere('guest_type', 'guest_tanzanian');
        })->get();
    echo "\n3. Checking Day Services...\n";
    foreach ($inflatedDayServices as $ds) {
        $oldPrice = $ds->amount_paid;
        $newPrice = round($oldPrice / 2600);
        $ds->update(['amount_paid' => $newPrice]);
        echo "   [FIXED] Day Service #{$ds->id}: " . number_format($oldPrice) . " -> " . number_format($newPrice) . " TZS\n";
    }

    // 4. REPAIR SHIFT DATA
    // Deflate expected totals while preserving the opening_cash float
    $inflatedShifts = Shift::where('status', 'closed')
        ->where('closing_cash_expected', '>', 5000000)->get();

    echo "\n4. Checking Shift Reconciliations...\n";
    foreach ($inflatedShifts as $s) {
        $opening = (float)$s->opening_cash;
        $expectedCashWithFloat = (float)$s->closing_cash_expected;
        
        // Subtract opening cash, deflate the revenue portion, then re-add opening cash
        $actualRevenueComponent = ($expectedCashWithFloat - $opening) / 2500;
        $newExpectedCash = round($opening + $actualRevenueComponent);
        
        $s->update([
            'closing_cash_expected' => $newExpectedCash,
            'total_mobile_expected' => round($s->total_mobile_expected / 2500),
            'total_card_expected'   => round($s->total_card_expected / 2500),
            'total_bank_expected'   => round($s->total_bank_expected / 2500),
            'total_online_expected' => round($s->total_online_expected / 2500),
        ]);
        echo "   [FIXED] Shift #{$s->id}: Revenue deflated. New Expected Cash: " . number_format($newExpectedCash) . " TZS\n";
    }

    // 5. CLOSE STALE SESSIONS
    $staleShifts = Shift::where('status', 'open')
        ->where('opened_at', '<', now()->subHours(24))->get();
    
    echo "\n5. Closing Stale Active Sessions...\n";
    foreach ($staleShifts as $ss) {
        $ss->update([
            'closed_at' => now(),
            'closing_cash_actual' => $ss->opening_cash,
            'closing_cash_expected' => $ss->opening_cash,
            'status' => 'closed',
            'notes' => 'Force closed by system cleanup.'
        ]);
        echo "   [CLOSED] Shift #{$ss->id} (Staff: {$ss->staff_id}) - Inactive for 24h+\n";
    }

    DB::commit();
    echo "\n========================================\n";
    echo "SUCCESS: Database repair completed.\n";
    echo "========================================\n";

} catch (\Exception $e) {
    DB::rollBack();
    echo "\n[ERROR] Repair failed: " . $e->getMessage() . "\n";
}
