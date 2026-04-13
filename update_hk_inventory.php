<?php
/**
 * update_hk_inventory.php
 * ========================
 * Updates PrimeLand Hotel Housekeeping Inventory based on physical stock count.
 * Logic: If item exists (by name mapping) -> SET current_stock to new value.
 *        If item does not exist -> CREATE it with the given stock.
 * Also logs a stock movement (type: 'stock_adjustment') for audit trail.
 */

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\HousekeepingInventoryItem;
use App\Models\InventoryStockMovement;
use App\Models\Staff;
use Illuminate\Support\Facades\DB;

// Find a system staff to attribute the adjustment to (use first housekeeper or any staff)
$systemStaff = Staff::first();
if (!$systemStaff) {
    die("[ERROR] No staff found in the database. Cannot log stock movements.\n");
}
$staffId = $systemStaff->id;

// ===========================================================================
// STOCK DATA - from physical count notes (April 13, 2026)
// Key = the EXACT name in the database (or desired new name)
// 'note_name' = what was written in the notebook (for reference)
// ===========================================================================
$stockUpdates = [
    // --- Housekeeping Supplies ---
    [
        'db_name'   => 'Shampoo',
        'note_name' => 'Shampoo',
        'quantity'  => 153,
        'unit'      => 'pcs',
        'category'  => 'Cleaning_supplies',
    ],
    [
        'db_name'   => 'Shawelgel',
        'note_name' => 'Showergel',
        'quantity'  => 120,
        'unit'      => 'pcs',
        'category'  => 'Cleaning_supplies',
    ],
    [
        'db_name'   => 'Slippers',
        'note_name' => 'Slippers',
        'quantity'  => 152,
        'unit'      => 'pcs',
        'category'  => 'Other',
    ],
    [
        'db_name'   => 'Shower Cap',
        'note_name' => 'Cap (Shower Cap)',
        'quantity'  => 209,
        'unit'      => 'pcs',
        'category'  => 'Cleaning_supplies',
    ],
    [
        'db_name'   => 'Bath Soap',
        'note_name' => 'Sabuni (Soap)',
        'quantity'  => 193,
        'unit'      => 'pcs',
        'category'  => 'Other',
    ],
    [
        'db_name'   => 'Mosquito Spray',
        'note_name' => 'Dawa za mbu (Mosquito Repellent)',
        'quantity'  => 4,
        'unit'      => 'pcs',
        'category'  => 'Cleaning_supplies',
    ],
    [
        'db_name'   => 'Air Freshner',
        'note_name' => 'Airfrenar (Air Freshener)',
        'quantity'  => 7,
        'unit'      => 'pcs',
        'category'  => 'Cleaning_supplies',
    ],
    [
        'db_name'   => 'Jamaa Soap',
        'note_name' => 'Jamaa (Bar Soap)',
        'quantity'  => 1.5,
        'unit'      => 'kg',
        'category'  => 'Cleaning_supplies',
    ],
    [
        'db_name'   => 'Glass Cleaner',   // New item - not in DB yet
        'note_name' => 'Glass Cleaner',
        'quantity'  => 2,
        'unit'      => 'pcs',
        'category'  => 'Cleaning_supplies',
    ],
    [
        'db_name'   => 'Aro',             // New item - not in DB yet
        'note_name' => 'Aro',
        'quantity'  => 2,
        'unit'      => 'pcs',
        'category'  => 'Cleaning_supplies',
    ],

    // --- Pantry / Beverage Supplies ---
    [
        'db_name'   => 'Milk',
        'note_name' => 'Maziwa (Milk) = 200+22',
        'quantity'  => 222,
        'unit'      => 'pcs',
        'category'  => 'Cleaning_supplies',
    ],
    [
        'db_name'   => 'Coffee',
        'note_name' => 'Kahawa (Coffee) = 89+160',
        'quantity'  => 249,
        'unit'      => 'pcs',
        'category'  => 'Other',
    ],
    [
        'db_name'   => 'Tea Bags',
        'note_name' => 'Majani (Tea Leaves) = 127+350+150',
        'quantity'  => 627,
        'unit'      => 'pcs',
        'category'  => 'Cleaning_supplies',
    ],
    [
        'db_name'   => 'Sugar',
        'note_name' => 'Sukari (Sugar) = 83+140',
        'quantity'  => 223,
        'unit'      => 'pcs',
        'category'  => 'Cleaning_supplies',
    ],
];

// ===========================================================================
// EXECUTE
// ===========================================================================
echo "============================================\n";
echo " PrimeLand Hotel - HK Stock Update Script\n";
echo " Date: " . date('Y-m-d H:i:s') . "\n";
echo "============================================\n\n";

DB::beginTransaction();

try {
    $updated = 0;
    $created = 0;

    foreach ($stockUpdates as $row) {
        // Find existing item by exact db_name
        $item = HousekeepingInventoryItem::where('name', $row['db_name'])->first();

        if ($item) {
            // --- EXISTING ITEM: Calculate adjustment delta ---
            $oldStock  = (float) $item->current_stock;
            $newStock  = (float) $row['quantity'];
            $delta     = $newStock - $oldStock;

            // Update current_stock
            $item->current_stock = $newStock;
            $item->save();

            // Log a stock_adjustment movement for the audit trail
            if ($delta != 0) {
                InventoryStockMovement::create([
                    'inventory_item_id' => $item->id,
                    'movement_type'     => 'adjustment',
                    'quantity'          => $delta,
                    'performed_by'      => $staffId,
                    'notes'             => "Physical count adjustment on " . date('Y-m-d') .
                                          ". Note: {$row['note_name']}. " .
                                          "Old: {$oldStock} → New: {$newStock}",
                ]);
            }

            $arrow = $delta >= 0 ? "↑ +{$delta}" : "↓ {$delta}";
            echo "[UPDATED] {$row['db_name']}: {$oldStock} → {$newStock} pcs  ({$arrow})\n";
            $updated++;

        } else {
            // --- NEW ITEM: Create it ---
            $item = HousekeepingInventoryItem::create([
                'name'             => $row['db_name'],
                'category'         => $row['category'],
                'unit'             => $row['unit'],
                'current_stock'    => $row['quantity'],
                'minimum_stock'    => 5,    // default minimum
                'reorder_quantity' => 10,   // default reorder qty
                'description'      => "Added via physical count. Note: {$row['note_name']}.",
            ]);

            // Log initial stock movement
            InventoryStockMovement::create([
                'inventory_item_id' => $item->id,
                'movement_type'     => 'supply',
                'quantity'          => $row['quantity'],
                'performed_by'      => $staffId,
                'notes'             => "Initial stock entry on " . date('Y-m-d') .
                                       ". Note: {$row['note_name']}.",
            ]);

            echo "[CREATED]  {$row['db_name']}: {$row['quantity']} {$row['unit']} (new item)\n";
            $created++;
        }
    }

    DB::commit();

    echo "\n============================================\n";
    echo " DONE! Updated: {$updated} | Created: {$created}\n";
    echo "============================================\n";

} catch (\Exception $e) {
    DB::rollBack();
    echo "\n[ERROR] Rolling back all changes.\n";
    echo "Message: " . $e->getMessage() . "\n";
    echo "File: " . $e->getFile() . ":" . $e->getLine() . "\n";
}
