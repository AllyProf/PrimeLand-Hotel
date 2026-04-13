<?php
/**
 * fix_hk_item_names.php
 * =====================
 * Renames housekeeping items to match the real names from the notebook.
 */

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\HousekeepingInventoryItem;

$renames = [
    'Shawelgel'  => 'Showergel',
    'Bath Soap'  => 'Sabuni (Soap)',
    'Jamaa Soap' => 'Jamaa',
];

echo "Renaming items...\n";
foreach ($renames as $oldName => $newName) {
    $item = HousekeepingInventoryItem::where('name', $oldName)->first();
    if ($item) {
        $item->name = $newName;
        $item->save();
        echo "[RENAMED] '{$oldName}' → '{$newName}'\n";
    } else {
        echo "[NOT FOUND] '{$oldName}' — skipped\n";
    }
}

// Also verify Jamaa current stock is 1.5
$jamaa = HousekeepingInventoryItem::where('name', 'Jamaa')->first();
if ($jamaa) {
    echo "\n[CHECK] Jamaa current_stock = {$jamaa->current_stock}\n";
    if ((float)$jamaa->current_stock != 1.5) {
        $jamaa->current_stock = 1.5;
        $jamaa->save();
        echo "[FIXED] Jamaa current_stock set to 1.5\n";
    } else {
        echo "[OK] Jamaa stock is already 1.5\n";
    }
}

// Also verify Shower Cap is present and has correct stock
$cap = HousekeepingInventoryItem::where('name', 'Shower Cap')->first();
echo "\n[CHECK] Shower Cap current_stock = " . ($cap ? $cap->current_stock : 'NOT FOUND') . "\n";

echo "\nDone!\n";
