<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ShoppingListItem extends Model
{
    protected $fillable = [
        'shopping_list_id',
        'product_id',
        'product_variant_id',
        'product_name',
        'category',
        'quantity',
        'unit',
        'estimated_price',
        'is_purchased',
        'purchased_quantity',
        'purchased_cost',
        'unit_price',
        'is_found',
        'storage_location',
        'purchase_request_id',
        'transferred_to_department',
        'is_received_by_department',
        'received_by_department_at',
        'expiry_date',
    ];

    protected $casts = [
        'quantity' => 'decimal:2',
        'estimated_price' => 'decimal:2',
        'purchased_quantity' => 'decimal:2',
        'purchased_cost' => 'decimal:2',
        'unit_price' => 'decimal:2',
        'is_purchased' => 'boolean',
        'is_found' => 'boolean',
        'is_received_by_department' => 'boolean',
        'received_by_department_at' => 'datetime',
        'expiry_date' => 'date',
    ];
    
    /**
     * Get the purchase request this item is linked to
     */
    public function purchaseRequest()
    {
        return $this->belongsTo(PurchaseRequest::class);
    }

    public function shoppingList()
    {
        return $this->belongsTo(ShoppingList::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function getCategoryNameAttribute()
    {
        return match($this->category) {
            'meat_poultry' => 'Meat & Poultry',
            'seafood' => 'Seafood & Fish',
            'vegetables' => 'Vegetables & Fruits',
            'dairy' => 'Dairy & Eggs',
            'pantry_baking' => 'Pantry & Baking',
            'spices_herbs' => 'Spices & Herbs',
            'grains_pasta' => 'Grains & Pasta',
            'bakery' => 'Bakery & Bread',
            'oils_fats' => 'Oils & Fats',
            'frozen_foods' => 'Frozen Foods',
            'canned_goods' => 'Canned & Packaged Goods',
            'beverages' => 'Beverages',
            'water' => 'Water',
            'kitchen_disposables' => 'Kitchen Disposables',
            'cleaning_supplies' => 'Cleaning Supplies',
            'linens' => 'Linens',
            'other' => 'Other',
            default => ucfirst(str_replace('_', ' ', $this->category ?? '')),
        };
    }

    /**
     * Proper relationship to variant
     */
    public function variant()
    {
        return $this->belongsTo(ProductVariant::class, 'product_variant_id');
    }

    /**
     * Helper to find the best matching variant for this item
     */
    public function guessProductVariant()
    {
        // 1. Precise variant link (Priority)
        if ($this->product_variant_id) {
            $v = ProductVariant::find($this->product_variant_id);
            if ($v) return $v;
        }

        // Parse name and size
        $name = $this->product_name;
        $size = null;
        if (preg_match('/\((.*?)\)/', $name, $matches)) {
            $size = trim($matches[1]);
            $baseName = trim(str_replace($matches[0], '', $name));
        } else {
            $baseName = trim($name);
        }

        // Clean brand prefixes (e.g. "BONITE - Coca Cola" -> "Coca Cola")
        $cleanName = $baseName;
        if (strpos($baseName, ' - ') !== false) {
            $parts = explode(' - ', $baseName);
            $cleanName = trim(end($parts));
        }

        // --- MATCHING STRATEGY ---
        
        // A. Direct Match by Variant Name or Measurement
        $variant = ProductVariant::active()
                 ->where(function($q) use ($baseName, $cleanName) {
                    $q->where('variant_name', 'LIKE', '%' . $cleanName . '%')
                      ->orWhere('variant_name', 'LIKE', '%' . $baseName . '%')
                      ->orWhereRaw('? LIKE CONCAT("%", variant_name, "%")', [$baseName]);
                 })->first();

        // B. If no variant, try product-based drill down
        if (!$variant) {
            $product = Product::where('is_active', true)
                     ->where(function($q) use ($baseName, $cleanName) {
                         $q->where('name', 'LIKE', '%' . $cleanName . '%')
                           ->orWhere('name', 'LIKE', '%' . $baseName . '%')
                           ->orWhereRaw('? LIKE CONCAT("%", name, "%")', [$baseName]);
                     })->first();

            if ($product) {
                // Try to find variant within product
                $variant = $product->variants()
                         ->where(function($q) use ($cleanName, $size) {
                             $q->where('variant_name', 'LIKE', '%' . $cleanName . '%');
                             if ($size) $q->orWhere('measurement', 'LIKE', '%' . $size . '%');
                         })->first() ?? $product->variants()->first();
            }
        }

        return $variant;
    }

    /**
     * Legacy accessor - now uses the guessing helper if not linked
     */
    public function getProductVariantAttribute()
    {
        if ($this->product_variant_id) {
            return $this->variant;
        }
        return $this->guessProductVariant();
    }
}
