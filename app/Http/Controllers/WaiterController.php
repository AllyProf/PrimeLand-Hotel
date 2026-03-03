<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Booking;
use App\Models\Service;
use App\Models\ServiceRequest;
use App\Models\Staff;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class WaiterController extends Controller
{
    /**
     * Show Waiter Dashboard / POS Interface
     */
    public function dashboard()
    {
        // 1. Get Active Bookings (Checked In) for Room Selection
    $activeBookings = Booking::with(['room', 'company'])
        ->where('check_in_status', 'checked_in')
        ->orderBy('created_at', 'desc')
        ->get()
        ->map(function($b) {
            return [
                'id' => $b->id,
                'room_number' => $b->room->room_number ?? '?',
                'room_type' => $b->room->room_type ?? 'Room',
                'guest_name' => $b->guest_name,
                'company' => $b->company->name ?? 'Private Guest',
                'payment_responsibility' => $b->payment_responsibility ?? 'self',
                'is_corporate' => (bool)$b->is_corporate_booking,
                'pax' => $b->number_of_guests,
                'arrival' => $b->check_in->format('d M'),
                'stay' => $b->check_in->diffInDays($b->check_out) . ' Nights'
            ];
        });

        // 2. Get available drinks and calculate stock levels (Replicated from restaurantService logic)
        $barCategories = ['drinks', 'alcoholic_beverage', 'non_alcoholic_beverage', 'water', 'juices', 'energy_drinks', 'spirits', 'wines', 'cocktails', 'hot_beverages', 'bar'];
        
        // Fetch all completed transfers to build historical stock
        $allTransfers = \App\Models\StockTransfer::where('status', 'completed')->get();
        // Fetch all completed sales to deduct from stock
        $allSales = \App\Models\ServiceRequest::where('status', 'completed')
            ->where(function($q) use ($barCategories) {
                $q->whereHas('service', function($sq) use ($barCategories) {
                    $sq->whereIn('category', $barCategories);
                })->orWhereIn('service_id', [3, 4]);
            })->get();

        // Build a stock mapping [variant_id => current_stock_in_pics]
        $stockLevels = [];
        foreach ($allTransfers as $t) {
            $vid = $t->product_variant_id;
            if (!isset($stockLevels[$vid])) $stockLevels[$vid] = 0;
            
            $itemsPerPkg = $t->productVariant->items_per_package ?? 1;
            $pics = ($t->quantity_unit === 'packages') ? ((float)$t->quantity_transferred * (float)$itemsPerPkg) : (float)$t->quantity_transferred;
            $stockLevels[$vid] += $pics;
        }

        foreach ($allSales as $s) {
            $meta = $s->service_specific_data;
            if (isset($meta['product_variant_id'])) {
                $vid = (int)$meta['product_variant_id'];
                if (isset($stockLevels[$vid])) {
                    $variant = \App\Models\ProductVariant::find($vid);
                    if ($variant) {
                        $isGlass = isset($meta['selling_method']) && $meta['selling_method'] === 'glass';
                        if ($isGlass) {
                            $stockLevels[$vid] -= ((float)$s->quantity / (float)($variant->servings_per_pic ?: 1));
                        } else {
                            $stockLevels[$vid] -= (float)$s->quantity;
                        }
                    }
                }
            }
        }

        $products = \App\Models\Product::whereIn('category', $barCategories)
            ->with(['variants'])
            ->get();

        $drinks = [];
        foreach ($products as $product) {
            foreach ($product->variants as $variant) {
                $options = [];
                // Build options based on pricing
                if ($variant->can_sell_as_serving && $variant->selling_price_per_serving > 0) {
                    $options[] = [
                        'type' => 'Glass', 
                        'method' => 'glass', 
                        'price' => (float)$variant->selling_price_per_serving,
                        'price_usd' => (float)$variant->selling_price_per_serving_usd
                    ];
                }
                if ($variant->can_sell_as_pic && $variant->selling_price_per_pic > 0) {
                    $options[] = [
                        'type' => 'Bottle', 
                        'method' => 'pic', 
                        'price' => (float)$variant->selling_price_per_pic,
                        'price_usd' => (float)$variant->selling_price_per_pic_usd
                    ];
                }

                if (!empty($options)) {
                    $currentStock = $stockLevels[$variant->id] ?? 0;
                    $drinks[] = (object)[
                        'id' => $product->id,
                        'variant_id' => $variant->id,
                        'name' => ($variant->variant_name ?: $product->name) . ($variant->measurement ? ' (' . $variant->measurement . ')' : ''),
                        'category' => $product->category,
                        'image' => $variant->image ?: $product->image,
                        'options' => $options,
                        'in_stock' => $currentStock > 0.01,
                        'current_stock' => round($currentStock, 2),
                        'unit' => 'Pcs',
                        'servings_per_pic' => $variant->servings_per_pic > 0 ? (float)$variant->servings_per_pic : 1
                    ];
                }
            }
        }

        // 3. Get all available Food Recipes (Sorted for better POS organization)
        $recipes = \App\Models\Recipe::where('is_available', true)
            ->orderBy('category', 'asc')
            ->orderBy('name', 'asc')
            ->get();
        $foodItems = [];
        foreach ($recipes as $recipe) {
            $foodItems[] = [
                'id' => $recipe->id,
                'name' => $recipe->name,
                'description' => $recipe->description ?? 'Chef Special',
                'price' => $recipe->selling_price,
                'price_usd' => $recipe->selling_price_usd,
                'category' => $recipe->category ?? 'Food',
                'image' => $recipe->image,
            ];
        }

        // 4. Get Active Ceremonies (Any date if unpaid, or any for today)
        $activeCeremonies = \App\Models\DayService::where(function($q) {
                $q->whereDate('service_date', now()->toDateString())
                  ->orWhereIn('payment_status', ['pending', 'partial']);
            })
            ->where(function($q) {
                $q->where('service_type', 'LIKE', '%ceremony%')
                  ->orWhere('service_type', 'LIKE', '%ceremory%')
                  ->orWhere('service_type', 'LIKE', '%birthday%')
                  ->orWhere('service_type', 'LIKE', '%package%');
            })
            ->get()
            ->map(function($c) {
                return [
                    'id' => $c->id,
                    'reference' => $c->service_reference,
                    'guest_name' => $c->guest_name,
                    'package_items' => $c->package_items ?? []
                ];
            });

        $drinksCollection = collect($drinks);
        $drinkCategories = $drinksCollection->groupBy('category')->sortKeys();

        return view('dashboard.waiter-dashboard', compact('activeBookings', 'foodItems', 'drinks', 'drinkCategories', 'activeCeremonies'));
    }

    /**
     * Fetch active bookings via AJAX (if needed for refreshing)
     */
    public function getActiveBookings()
    {
        $bookings = Booking::with('room')
            ->where('check_in_status', 'checked_in')
            ->get()
            ->map(function($b) {
                return [
                    'id' => $b->id,
                    'room_number' => $b->room->room_number ?? 'N/A',
                    'guest_name' => $b->guest_name,
                ];
            });
            
        return response()->json($bookings);
    }

    /**
     * Submit an order from Waiter POS
     */
    public function storeOrder(Request $request)
    {
        $request->validate([
            'order_type' => 'required|in:resident,walk_in,ceremony',
            'booking_id' => 'required_if:order_type,resident',
            'day_service_id' => 'required_if:order_type,ceremony',
            'walk_in_name' => 'nullable|string',
            'items' => 'required|array|min:1',
            'payment_intent' => 'nullable|string', // 'now' or 'later'
            'payment_method' => 'nullable|string', // 'cash', 'mobile', 'card', 'room_charge'
            'payment_reference' => 'nullable|string',
        ]);

        try {
            DB::beginTransaction();
            $user = Auth::guard('staff')->user();
            $createdRequests = [];

            // 1. Pre-validate Bar Stock for all items in the basket
            foreach ($request->items as $item) {
                if (isset($item['variantId']) && $item['variantId']) {
                    $vid = $item['variantId'];
                    $variant = \App\Models\ProductVariant::find($vid);
                    if (!$variant) continue;

                    // Calculate current stock level for this variant (Pics)
                    $totalRecv = \App\Models\StockTransfer::where('product_variant_id', $vid)
                        ->where('status', 'completed')
                        ->sum('quantity_transferred');
                    
                    $totalSold = \App\Models\ServiceRequest::where('status', 'completed')
                        ->where('service_specific_data->product_variant_id', (string)$vid)
                        ->get()
                        ->sum(function($s) use ($variant) {
                            $meta = $s->service_specific_data;
                            if (isset($meta['selling_method']) && $meta['selling_method'] === 'glass') {
                                return (float)$s->quantity / (float)($variant->servings_per_pic ?: 1);
                            }
                            return (float)$s->quantity;
                        });

                    $currentStockPics = $totalRecv - $totalSold;

                    // Calculate requested stock for this order
                    $requestedPics = ($item['method'] === 'glass') 
                        ? ($item['qty'] / ($variant->servings_per_pic ?: 1)) 
                        : $item['qty'];

                    if ($requestedPics > ($currentStockPics + 0.001)) { // Buffer for small decimals
                        throw new \Exception("Insufficient stock for " . ($item['name'] ?? 'Item') . ". Available: " . round($currentStockPics, 2) . " Pcs. Please refresh your inventory.");
                    }
                }
            }

            foreach ($request->items as $item) {
                // Determine base note
                $notePrefix = 'POS Order by Waiter: ' . ($user->name ?? 'Staff');
                if ($request->payment_intent === 'now') {
                    $notePrefix .= ' [PAY AT COUNTER]';
                }
                
                // Protection: Ceremonies MUST NOT have payment recorded by waiter.
                // It must go to Reception's reconciliation modal.
                $isCeremony = $request->order_type === 'ceremony';
                $forceLater = $isCeremony;
                $finalIntent = $forceLater ? 'later' : $request->payment_intent;

                $data = [
                    'quantity' => $item['qty'],
                    'unit_price_tsh' => $item['price'],
                    'total_price_tsh' => $item['price'] * $item['qty'],
                    'requested_at' => now(),
                    'reception_notes' => $notePrefix . (isset($item['note']) && $item['note'] ? (' - Msg: ' . $item['note']) : ''),
                    'payment_status' => $finalIntent === 'now' ? 'paid' : 'pending',
                    'payment_method' => ($finalIntent === 'now') ? $request->payment_method : null,
                    'payment_reference' => ($finalIntent === 'now') ? $request->payment_reference : null,
                    'paid_to' => ($finalIntent === 'now') ? $user->id : null,
                    'approved_by' => $user->id, // Track who recorded this usage
                ];

                // Handle Identity (Booking, Ceremony or Walk-in)
                if ($request->order_type === 'resident') {
                    $data['booking_id'] = $request->booking_id;
                    $data['is_walk_in'] = false;
                } elseif ($request->order_type === 'ceremony') {
                    $data['day_service_id'] = $request->day_service_id;
                    $data['is_walk_in'] = false;
                } else {
                    $data['is_walk_in'] = true;
                    $data['walk_in_name'] = $request->walk_in_name;
                }

                $isFood = isset($item['isFood']) && filter_var($item['isFood'], FILTER_VALIDATE_BOOLEAN);

                if ($isFood) {
                    $data['service_id'] = 4; // Generic Food Order ID
                    $data['service_specific_data'] = [
                        'food_id' => $item['id'],
                        'item_name' => $item['name']
                    ];
                } elseif (isset($item['variantId']) && $item['variantId']) {
                    $data['service_id'] = 3; // Generic Bar Order ID
                    $data['service_specific_data'] = [
                        'product_id' => $item['productId'],
                        'product_variant_id' => $item['variantId'],
                        'selling_method' => $item['method'],
                        'item_name' => $item['name']
                    ];
                } elseif (isset($item['is_service_only']) && $item['is_service_only']) {
                    $data['service_id'] = $item['id'];
                } else {
                    $data['service_id'] = $item['id'];
                }

                $requestModel = ServiceRequest::create($data);
                $createdRequests[] = $requestModel->id;
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Order submitted successfully! Sent to Kitchen/Bar.',
                'request_ids' => $createdRequests
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error submitting waiter order: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Order Failed: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Show Waiter Order History
     */
    public function orders(Request $request)
    {
        $user = Auth::guard('staff')->user();
        $search = $request->input('search');
        $tab = $request->input('tab', 'all');
        
        $query = ServiceRequest::with(['service', 'booking.room', 'booking.company', 'dayService', 'approvedBy', 'paidBy', 'cancelledBy'])
            ->where('reception_notes', 'LIKE', '%Waiter: ' . $user->name . '%');

        // Search Logic
        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('walk_in_name', 'LIKE', "%{$search}%")
                  ->orWhereHas('booking', function($bq) use ($search) {
                      $bq->where('guest_name', 'LIKE', "%{$search}%")
                         ->orWhereHas('room', function($rq) use ($search) {
                             $rq->where('room_number', 'LIKE', "%{$search}%");
                         });
                  })
                  ->orWhereHas('dayService', function($dq) use ($search) {
                      $dq->where('guest_name', 'LIKE', "%{$search}%")
                         ->orWhere('service_reference', 'LIKE', "%{$search}%");
                  });
            });
        }

        // Filter Logic
        switch ($tab) {
            case 'pending':
                $query->whereNotIn('status', ['completed', 'cancelled']);
                break;
            case 'completed':
                $query->where('status', 'completed');
                break;
            case 'unpaid':
                $query->whereIn('payment_status', ['pending', 'unpaid'])
                      ->where('status', '!=', 'cancelled');
                break;
            case 'paid':
                $query->whereIn('payment_status', ['paid', 'room_charge']);
                break;
            case 'cancelled':
                $query->where('status', 'cancelled');
                break;
        }
        
        $orders = $query->orderBy('requested_at', 'desc')->paginate(20)->withQueryString();

        if ($request->ajax()) {
            return view('dashboard.waiter-orders-partial', compact('orders', 'tab', 'search'));
        }
            
        return view('dashboard.waiter-orders', compact('orders', 'tab', 'search'));
    }

    /**
     * Print Docket for an Order
     */
    public function printDocket(ServiceRequest $serviceRequest)
    {
        $order = $serviceRequest->load(['booking.room', 'service', 'dayService']);
        
        // Determine Destination
        $destination = 'Internal';
        if ($order->is_walk_in) {
            $destination = 'WALK-IN (' . ($order->walk_in_name ?? 'Guest') . ')';
        } elseif ($order->booking) {
            $destination = 'ROOM ' . ($order->booking->room->room_number ?? 'N/A');
        } elseif ($order->dayService) {
            $destination = 'CEREMONY (' . ($order->dayService->name ?? 'Event') . ')';
        }

        // Determine Guest Name
        $guestName = $order->is_walk_in ? ($order->walk_in_name ?? 'General Guest') : ($order->booking->guest_name ?? 'Hotel Guest');

        // Determine Requested By
        $requestedBy = 'N/A';
        if ($order->reception_notes && str_contains($order->reception_notes, 'Waiter: ')) {
            $parts = explode('Waiter: ', $order->reception_notes);
            $byParts = explode(' - Msg:', $parts[1] ?? '');
            $requestedBy = trim(explode('|', $byParts[0] ?? 'Waiter')[0]);
        }

        // Determine Note
        $note = $order->guest_request;
        if (!$note && $order->reception_notes && str_contains($order->reception_notes, '- Msg: ')) {
            $parts = explode('- Msg: ', $order->reception_notes);
            $note = $parts[1] ?? null;
        }

        // Determine Item Name
        $itemName = $order->service_specific_data['item_name'] ?? ($order->service->name ?? 'Special Item');

        return view('dashboard.print-kitchen-order-docket', compact('order', 'destination', 'guestName', 'itemName', 'requestedBy', 'note'));
    }
    
    /**
     * Print Docket for All Items in a Guest Group
     */
    public function printGroupDocket(Request $request)
    {
        $user = Auth::guard('staff')->user();
        
        // Get group key from request
        $isWalkIn = $request->input('is_walk_in', false);
        $isCeremony = $request->input('is_ceremony', false);
        $identifier = $request->input('identifier'); // walk_in_name or booking_id or day_service_id
        
        // Fetch all orders for this group
        $orders = ServiceRequest::with(['service', 'booking.room', 'dayService'])
            ->where('reception_notes', 'LIKE', '%Waiter: ' . $user->name . '%');
        
        if ($isWalkIn) {
            $orders = $orders->where('is_walk_in', true)
                ->where('walk_in_name', $identifier);
        } elseif ($isCeremony) {
            $orders = $orders->where('day_service_id', $identifier);
        } else {
            $orders = $orders->where('booking_id', $identifier);
        }
        
        $orders = $orders->whereNotIn('status', ['cancelled'])
            ->orderBy('requested_at', 'desc')
            ->get();
        
        if ($orders->isEmpty()) {
            abort(404, 'No active orders found for this group');
        }

        // If walk-in, further filter by date to avoid picking up same name from different days
        if ($isWalkIn) {
            $firstDate = $orders->first()->requested_at->toDateString();
            $orders = $orders->filter(function($o) use ($firstDate) {
                return $o->requested_at->toDateString() === $firstDate;
            });
        }
        
        $first = $orders->first();
        
        // Determine Destination
        $destination = 'Internal';
        if ($first->is_walk_in) {
            $walkInName = $first->walk_in_name ?? 'Guest';
            $destination = str_contains(strtolower($walkInName), 'walk-in') ? $walkInName : 'WALK-IN (' . $walkInName . ')';
        } elseif ($first->dayService) {
            $destination = 'CEREMONY (' . ($first->dayService->service_reference ?? 'N/A') . ')';
        } elseif ($first->booking) {
            $destination = 'ROOM ' . ($first->booking->room->room_number ?? 'N/A');
        }
        
        // Determine Guest Name
        $guestName = $first->is_walk_in ? ($first->walk_in_name ?? 'General Guest') : 
            ($first->dayService ? ($first->dayService->guest_name ?? 'Ceremony Guest') : 
            ($first->booking->guest_name ?? 'Hotel Guest'));
        
        // Determine Requested By
        $requestedBy = 'N/A';
        if ($first->reception_notes && str_contains($first->reception_notes, 'Waiter: ')) {
            $parts = explode('Waiter: ', $first->reception_notes);
            $byParts = explode(' - Msg:', $parts[1] ?? '');
            $requestedBy = trim(explode('|', $byParts[0] ?? 'Waiter')[0]);
        }
        
        // Calculate total
        $totalAmount = $orders->sum('total_price_tsh');
        
        return view('dashboard.print-waiter-group-docket', compact('orders', 'destination', 'guestName', 'requestedBy', 'totalAmount', 'first'));
    }

    /**
     * Cancel an Individual Order
     */
    public function cancelOrder(Request $request, ServiceRequest $serviceRequest)
    {
        $request->validate([
            'reason' => 'required|string|max:500',
        ]);

        // Only allow cancelling if not already finalized (paid or already cancelled)
        if ($serviceRequest->status === 'cancelled') {
            return back()->with('error', 'This order is already cancelled.');
        }

        if ($serviceRequest->payment_status === 'paid' || $serviceRequest->payment_status === 'room_charge') {
            return back()->with('error', 'Cannot cancel an order that has already been paid.');
        }

        $user = Auth::guard('staff')->user();
        $reason = $request->reason;
        
        $serviceRequest->update([
            'status' => 'cancelled',
            'cancelled_by' => $user->id,
            'reception_notes' => ($serviceRequest->reception_notes ? $serviceRequest->reception_notes . " | " : "") . "CANCELLED by Waiter (" . ($user->name ?? 'Staff') . "): " . $reason
        ]);

        return back()->with('success', 'Order cancelled successfully.');
    }

    /**
     * Show Waiter Sales Summary
     */
    public function salesSummary(Request $request)
    {
        $user = Auth::guard('staff')->user();
        $date = $request->get('date', now()->toDateString());
        
        // Base query for orders placed by this waiter on this date
        $query = ServiceRequest::where('reception_notes', 'LIKE', '%Waiter: ' . $user->name . '%')
            ->whereDate('requested_at', $date);

        $totalSales = $query->sum('total_price_tsh');
        $totalOrders = $query->count();
        
        $paidSales = (clone $query)->where('payment_status', 'paid')->sum('total_price_tsh');
        $pendingSales = (clone $query)->where('payment_status', 'pending')->sum('total_price_tsh');
        $roomChargeSales = (clone $query)->where('payment_status', 'room_charge')->sum('total_price_tsh');

        $activeOrders = (clone $query)->whereIn('status', ['pending', 'approved', 'preparing'])->count();
        $completedOrders = (clone $query)->where('status', 'completed')->count();
        $cancelledOrders = (clone $query)->where('status', 'cancelled')->count();

        $itemsBreakdown = (clone $query)->get()
            ->groupBy(function($item) {
                return $item->service_specific_data['item_name'] ?? ($item->service->name ?? 'Unknown');
            })
            ->map(function($group) {
                return [
                    'qty' => $group->sum('quantity'),
                    'revenue' => $group->sum('total_price_tsh')
                ];
            })
            ->sortByDesc('qty')
            ->take(20);

        return view('dashboard.waiter-sales-summary', compact(
            'totalSales', 'totalOrders', 'paidSales', 'pendingSales', 
            'roomChargeSales', 'itemsBreakdown', 'date',
            'activeOrders', 'completedOrders', 'cancelledOrders'
        ));
    }

    /**
     * Register payment for a group of orders
     */
    public function registerPayment(Request $request)
    {
        $request->validate([
            'is_walk_in' => 'required|boolean',
            'is_ceremony' => 'nullable|boolean',
            'identifier' => 'required',
            'payment_method' => 'required|string',
            'payment_reference' => 'nullable|string',
        ]);

        if ($request->is_ceremony) {
            return response()->json([
                'success' => false, 
                'message' => 'Ceremony payments cannot be recorded by waiters. Please guide the guest to Reception for bill reconciliation.'
            ]);
        }

        $user = Auth::guard('staff')->user();
        $isWalkIn = $request->input('is_walk_in');
        $identifier = $request->input('identifier');

        $query = ServiceRequest::whereIn('payment_status', ['pending', 'unpaid'])
            ->where('status', '!=', 'cancelled');

        if ($isWalkIn) {
            $query->where('is_walk_in', true)->where('walk_in_name', $identifier);
        } elseif ($request->is_ceremony) {
            $query->where('day_service_id', $identifier);
        } else {
            // For Room Guests: Settle items for this booking
            $query->where('booking_id', $identifier);

            // Isolation: Only settle items the waiter typically handles (recorded by them OR non-bar categories)
            $barCats = [
                'drinks', 'beverage', 'alcoholic_beverage', 'non_alcoholic_beverage', 'water', 'juices', 
                'energy_drinks', 'bar', 'spirits', 'whiskey', 'wine', 'wines', 'beers', 'liquor', 
                'cocktails', 'soda', 'beverages', 'alcoholic', 'hot_beverages'
            ];

            $query->where(function($q) use ($user, $barCats) {
                // Settle if recorded by this waiter
                $q->where('reception_notes', 'LIKE', '%Waiter: ' . $user->name . '%')
                  // OR if it's NOT a bar item (meaning it's likely a food/kitchen item)
                  ->orWhere(function($sub) use ($barCats) {
                      $sub->whereHas('service', function($sq) use ($barCats) {
                          $sq->whereNotIn('category', $barCats);
                      })->where('service_id', '!=', 3);
                  });
            });
        }


        $orders = $query->with(['service', 'booking'])->get();

        if ($orders->isEmpty()) {
            return response()->json(['success' => false, 'message' => 'No pending payments found for this group.']);
        }

        // Enforcement of Payment Responsibility Rules
        $firstOrder = $orders->first();
        if ($firstOrder->booking) {
            $resp = $firstOrder->booking->payment_responsibility ?? 'self';

            if ($resp === 'company') {
                // RULE 1: MANDATORY ROOM CHARGE
                if ($request->payment_method !== 'room_charge') {
                    return response()->json(['success' => false, 'message' => 'This guest is COMPANY-SPONSORED. All services MUST be charged to the room.']);
                }
            } else {
                // RULE 2: SELF-PAYER (Resident)
                $mobilePlatforms = ['mpesa', 'halopesa', 'tigopesa', 'airtel', 'mixx'];
                $bankPlatforms = ['nmb', 'crdb', 'kcb', 'nbc', 'dtb'];
                $cardPlatforms = ['visa', 'mastercard', 'amex'];
                $allowed = array_merge(['cash', 'room_charge'], $mobilePlatforms, $bankPlatforms, $cardPlatforms);
                
                if (!in_array($request->payment_method, $allowed)) {
                    return response()->json(['success' => false, 'message' => 'Self-Payer: Please use Cash, Mobile, Bank, Card or Room Charge.']);
                }
            }
        } else {
            // RULE 3: WALK-IN / CEREMONY (No Room Charge)
            $mobilePlatforms = ['mpesa', 'halopesa', 'tigopesa', 'airtel', 'mixx'];
            $bankPlatforms = ['nmb', 'crdb', 'kcb', 'nbc', 'dtb'];
            $cardPlatforms = ['visa', 'mastercard', 'amex'];
            $allowed = array_merge(['cash'], $mobilePlatforms, $bankPlatforms, $cardPlatforms);

            if (!in_array($request->payment_method, $allowed)) {
                return response()->json(['success' => false, 'message' => 'Walk-in/Ceremony: Only Cash, Mobile, Bank or Card payments are accepted.']);
            }
        }

        $totalCollected = $orders->sum('total_price_tsh');
        $isRoomCharge = $request->payment_method === 'room_charge';

        foreach ($orders as $order) {
            $currentNotes = $order->reception_notes;
            
            // Determine suffix based on payment method
            $paymentSuffix = $isRoomCharge 
                ? ' | Charged to Room by Waiter: ' . $user->name
                : ' | Payment recorded by Waiter: ' . $user->name;

            // Clean up previous markers
            $cleanNotes = str_replace(['(Pending Payment)', '(Paid)'], '', $currentNotes);
            
            if (!str_contains($cleanNotes, 'Payment recorded') && !str_contains($cleanNotes, 'Charged to Room')) {
                $cleanNotes .= $paymentSuffix;
            }

            $updateData = [
                'payment_status' => $isRoomCharge ? 'room_charge' : 'paid',
                'payment_method' => $request->payment_method,
                'payment_reference' => $request->payment_reference,
                'paid_to' => $user->id,
                'reception_notes' => $cleanNotes
            ];

            // Automatically mark orders as "COMPLETED" on payment
            // This allows the Chef to focus on cooking without needing to touch the system.
            // If it was already completed (e.g. by Bar Keeper), we just keep it completed.
            if ($order->status !== 'completed' && $order->status !== 'cancelled') {
                $updateData['status'] = 'completed'; 
                $updateData['completed_at'] = now();
                $updateData['approved_at'] = $order->approved_at ?? now();
                $updateData['reception_notes'] .= " | Finalized on Payment by Waiter: " . $user->name;
            }

            $order->update($updateData);
        }

        // Log activity
        try {
            $itemNames = $orders->map(fn($o) => $o->service->name ?? ($o->service_specific_data['item_name'] ?? 'Item'))->implode(', ');
            \App\Models\ActivityLog::create([
                'user_id' => $user->id,
                'user_type' => get_class($user),
                'action' => 'payment_received',
                'description' => "Waiter " . $user->name . " recorded payment of " . number_format($totalCollected) . " TSH for: " . $itemNames . " (Guest: " . $identifier . ")",
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
            ]);
        } catch (\Exception $e) {}

        return response()->json([
            'success' => true,
            'message' => 'Payment registered successfully for ' . $orders->count() . ' items.'
        ]);
    }
}
