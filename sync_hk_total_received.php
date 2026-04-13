<?php
/**
 * sync_hk_total_received.php
 * ===========================
 * Makes "Total Received" match "Current Stock" for every housekeeping
 * inventory item by replacing all existing 'supply' movements with a
 * single movement equal to current_stock.
 *
 * This is safe: non-supply movements (consumption, adjustment, transfer)
 * are left untouched — only the supply history is rebuilt.
 */

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\HousekeepingInventoryItem;
use App\Models\InventoryStockMovement;
use App\Models\Staff;
use Illuminate\Support\Facades\DB;

// Find a staff member to log the movement under
$systemStaff = Staff::first();
if (!$systemStaff) {
    die("[ERROR] No staff found in database.\n");
}

echo "====================================================\n";
echo " PrimeLand Hotel - Sync Total Received → Current Stock\n";
echo " Date: " . date('Y-m-d H:i:s') . "\n";
echo "====================================================\n\n";

DB::beginTransaction();

try {
    $items = HousekeepingInventoryItem::orderBy('name')->get();

    foreach ($items as $item) {
        $currentStock = (float) $item->current_stock;

        // Step 1: Remove all existing 'supply' movements for this item
        $deletedCount = InventoryStockMovement::where('inventory_item_id', $item->id)
            ->where('movement_type', 'supply')
            ->delete();

        // Step 2: Insert ONE supply movement = current_stock
        // (only insert if current_stock > 0, otherwise leave total_received as 0)
        if ($currentStock > 0) {
            InventoryStockMovement::create([
                'inventory_item_id' => $item->id,
                'movement_type'     => 'supply',
                'quantity'          => $currentStock,
                'performed_by'      => $systemStaff->id,
                'notes'             => 'Stock reconciliation on ' . date('Y-m-d') .
                                       '. Total Received synced to match current stock.',
            ]);
        }

        echo "[SYNCED] {$item->name}: deleted {$deletedCount} old supply record(s), " .
             "set Total Received = {$currentStock} {$item->unit}\n";
    }

    DB::commit();
    echo "\n====================================================\n";
    echo " DONE! All " . $items->count() . " items synced.\n";
    echo " Refresh /housekeeper/inventory to see the changes.\n";
    echo "====================================================\n";

} catch (\Exception $e) {
    DB::rollBack();
    echo "\n[ERROR] Rolling back all changes.\n";
    echo "Message: " . $e->getMessage() . "\n";
    echo "File:    " . $e->getFile() . ":" . $e->getLine() . "\n";
}
