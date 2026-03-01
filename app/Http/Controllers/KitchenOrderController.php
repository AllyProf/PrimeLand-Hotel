<?php

namespace App\Http\Controllers;

use App\Models\ServiceRequest;
use App\Models\Recipe;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class KitchenOrderController extends Controller
{
    /**
     * Display pending food orders for the kitchen
     */
    public function index()
    {
        $pendingOrders = ServiceRequest::with(['booking.room', 'service'])
            ->where(function($query) {
                // Include Generic Food Order (4), Restaurant Food Order (48), and categories
                $query->whereIn('service_id', [4, 48])
                      ->orWhereHas('service', function($q) {
                          $q->whereIn('category', ['food', 'restaurant']);
                      });
            })
            ->where(function($q) {
                // 1. All active orders (pending, approved, or preparing)
                $q->whereIn('status', ['pending', 'approved', 'preparing'])
                // 2. OR Served orders that are WAITING FOR PAYMENT (exclude room_charge as they're already paid)
                ->orWhere(function($sub) {
                    $sub->where('status', 'completed')
                        ->whereIn('payment_status', ['pending', 'unpaid']);
                });
            })
            ->orderBy('requested_at', 'desc')
            ->get();

        // Statistics
        $stats = [
            'pending_count' => $pendingOrders->count(),
            'completed_today' => ServiceRequest::where(function($query) {
                                    $query->whereIn('service_id', [4, 48])
                                          ->orWhereHas('service', function($q) {
                                              $q->whereIn('category', ['food', 'restaurant']);
                                          });
                                })
                                ->where('status', 'completed')
                                ->whereDate('completed_at', now())
                                ->count(),
        ];

        return view('admin.restaurants.kitchen.orders', compact('pendingOrders', 'stats'));
    }

    public function startPreparation(Request $request, ServiceRequest $serviceRequest)
    {
        $user = Auth::guard('staff')->user();
        \Log::info('Kitchen starting preparation', ['order_id' => $serviceRequest->id, 'user_id' => $user->id ?? 'unknown']);
        
        try {
            $serviceRequest->update([
                'status' => 'preparing',
                'preparation_started_at' => now(),
                'approved_by' => $user->id ?? $serviceRequest->approved_by,
                'approved_at' => $serviceRequest->approved_at ?? now(),
            ]);

            return response()->json(['success' => true, 'message' => 'Preparation started!']);
        } catch (\Exception $e) {
            \Log::error('Kitchen start preparation failed', ['order_id' => $serviceRequest->id, 'error' => $e->getMessage()]);
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * Mark an order as completed and deduct ingredients
     */
    public function complete(Request $request, ServiceRequest $serviceRequest)
    {
        $user = Auth::guard('staff')->user();
        
        DB::beginTransaction();
        try {
            \Log::info('Kitchen completing order', ['order_id' => $serviceRequest->id, 'user_id' => $user->id ?? 'unknown']);
            // 1. Mark as completed
            $updateData = [
                'status' => 'completed',
                'completed_at' => now(),
                'approved_by' => $user->id ?? $serviceRequest->approved_by,
                'approved_at' => $serviceRequest->approved_at ?? now(),
                'preparation_started_at' => $serviceRequest->preparation_started_at ?? now(),
                'reception_notes' => ($serviceRequest->reception_notes ? $serviceRequest->reception_notes . ' | ' : '') . "Completed by Kitchen (" . ($user->name ?? 'Staff') . ")"
            ];

            // Check if this is a company-paid booking
            $isCompanyPaid = !$serviceRequest->is_walk_in && 
                             $serviceRequest->booking && 
                             $serviceRequest->booking->payment_responsibility === 'company';

            $fresh = $serviceRequest->fresh();
            $isPaid = in_array($fresh->payment_status, ['paid', 'room_charge']);

            // Handle Payment if provided (e.g. for Walk-ins paying at Kitchen)
            if ($request->filled('payment_method')) {
                $isRoomCharge = $request->payment_method === 'room_charge';
                $updateData['payment_status'] = $isRoomCharge ? 'room_charge' : 'paid';
                $updateData['payment_method'] = $request->payment_method;
                $updateData['payment_reference'] = $request->payment_reference;
                $updateData['paid_to'] = $user->id;
                
                $methodName = strtoupper(str_replace('_', ' ', $request->payment_method));
                $updateData['reception_notes'] .= " (Paid via $methodName)";
            } elseif ($isCompanyPaid) {
                // Auto-charge to room for company-paid bookings
                $updateData['payment_status'] = 'room_charge';
                $updateData['payment_method'] = 'room_charge';
                $updateData['reception_notes'] .= " (Auto-charged to Company)";
            } elseif ($isPaid) {
                // Already paid (e.g. via waiter POS)
                $updateData['payment_status'] = $fresh->payment_status;
                $updateData['payment_method'] = $fresh->payment_method;
                $updateData['reception_notes'] .= " (Paid)";
            }

            // Final note cleanup to remove any stray (Pending Payment) markers
            $updateData['reception_notes'] = str_replace('(Pending Payment)', '', $updateData['reception_notes']);
            $updateData['reception_notes'] = trim(preg_replace('/\s+/', ' ', $updateData['reception_notes']));

            $serviceRequest->update($updateData);

            // Note: Ingredient deduction is handled manually through the shopping list/inventory system 
            // in this simplified version. we just mark the order as completed.

            DB::commit();
            return response()->json(['success' => true, 'message' => 'Order completed!']);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * Cancel an Individual Order
     */
    public function cancelOrder(Request $request, ServiceRequest $serviceRequest)
    {
        $request->validate([
            'reason' => 'nullable|string|max:500',
        ]);

        // Only allow cancelling if not already finalized (paid or already cancelled)
        if ($serviceRequest->status === 'cancelled') {
            return response()->json(['success' => false, 'message' => 'This order is already cancelled.']);
        }

        if ($serviceRequest->payment_status === 'paid' || $serviceRequest->payment_status === 'room_charge') {
            return response()->json(['success' => false, 'message' => 'Cannot cancel an order that has already been paid.']);
        }

        $user = Auth::guard('staff')->user();
        $reason = $request->reason ?? 'No reason provided';
        
        $roleName = $user ? str_replace('_', ' ', strtoupper($user->role)) : 'Staff';
        $serviceRequest->update([
            'status' => 'cancelled',
            'cancelled_by' => $user->id,
            'reception_notes' => ($serviceRequest->reception_notes ? $serviceRequest->reception_notes . " | " : "") . "CANCELLED by $roleName (" . ($user->name ?? 'Staff') . "): " . $reason
        ]);

        return response()->json(['success' => true, 'message' => 'Order item cancelled successfully.']);
    }

    /**
     * Bulk transition a group of orders (by Guest or Room) to a new status
     * Used primarily by the KDS Monitor
     */
    public function completeGroup(Request $request)
    {
        $isWalkIn = $request->input('is_walk_in', 0);
        $identifier = $request->input('identifier'); 
        $newStatus = $request->input('status', 'completed'); // 'preparing' or 'completed'
        $user = Auth::guard('staff')->user();

        if (!$identifier) {
            return response()->json(['success' => false, 'message' => 'No identifier provided.']);
        }

        $query = ServiceRequest::with('booking')
            ->whereIn('status', ['pending', 'approved', 'preparing']);

        if ($isWalkIn) {
            $query->where('is_walk_in', true)->where('walk_in_name', $identifier);
        } else {
            $query->where('booking_id', $identifier);
        }

        $orders = $query->get();
        if ($orders->isEmpty()) {
            return response()->json(['success' => false, 'message' => 'No active orders found for this group.']);
        }

        DB::beginTransaction();
        try {
            foreach ($orders as $order) {
                $updateData = [];
                
                if ($newStatus === 'preparing') {
                    $updateData = [
                        'status' => 'preparing',
                        'preparation_started_at' => now(),
                        'reception_notes' => ($order->reception_notes ? $order->reception_notes . ' | ' : '') . "Preparation started by " . ($user->name ?? 'Staff')
                    ];
                } elseif ($newStatus === 'completed') {
                    $updateData = [
                        'status' => 'completed',
                        'completed_at' => now(),
                        'preparation_started_at' => $order->preparation_started_at ?? now(),
                        'reception_notes' => ($order->reception_notes ? $order->reception_notes . ' | ' : '') . "Completed by Kitchen (" . ($user->name ?? 'Staff') . ")"
                    ];

                    // Auto-settle if already paid or room charge
                    if (in_array($order->payment_status, ['paid', 'room_charge'])) {
                        // Do nothing extra
                    } else {
                        // Default behavior for KDS is mark as served, pay later
                        $updateData['payment_status'] = 'pending'; 
                    }
                }

                $order->update($updateData);
            }

            DB::commit();
            $msg = $newStatus === 'preparing' ? 'Preparation started!' : 'Orders marked as served!';
            return response()->json(['success' => true, 'message' => $msg]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * Cancel all items in a group (Room or Walk-in)
     */
    public function cancelGroup(Request $request)
    {
        $isWalkIn = $request->input('is_walk_in', 0);
        $identifier = $request->input('identifier'); 
        $reason = $request->input('reason', 'Group Cancelled by Kitchen');

        if (!$identifier) {
            return response()->json(['success' => false, 'message' => 'No identifier provided.']);
        }

        $query = ServiceRequest::whereIn('status', ['pending', 'approved', 'preparing', 'ready', 'completed'])
            ->whereNotIn('payment_status', ['paid', 'room_charge']);

        if ($isWalkIn) {
            $query->where('is_walk_in', true)->where('walk_in_name', $identifier);
        } else {
            $query->where('booking_id', $identifier);
        }

        $orders = $query->get();
        if ($orders->isEmpty()) {
            return response()->json(['success' => false, 'message' => 'No active orders found for this group.']);
        }

        $user = Auth::guard('staff')->user();
        foreach ($orders as $order) {
            $roleName = $user ? str_replace('_', ' ', strtoupper($user->role)) : 'Staff';
            $order->update([
                'status' => 'cancelled',
                'cancelled_by' => $user->id,
                'reception_notes' => ($order->reception_notes ? $order->reception_notes . " | " : "") . "GROUP CANCELLED by $roleName (" . ($user->name ?? 'Staff') . "): " . $reason
            ]);
        }

        return response()->json(['success' => true, 'message' => 'Entire order group cancelled successfully.']);
    }

    /**
     * Show order history for the kitchen
     */
    public function history()
    {
        $completedOrders = ServiceRequest::with(['booking.room', 'service', 'approvedBy', 'cancelledBy'])
            ->where(function($query) {
                // Service ID 48 is "Restaurant Food Order", 4 is "Generic Food Order"
                $query->whereIn('service_id', [4, 48])
                      ->orWhereHas('service', function($q) {
                          $q->whereIn('category', ['food', 'restaurant']);
                      });
            })
            ->whereIn('status', ['completed', 'cancelled'])
            ->orderBy('requested_at', 'desc')
            ->paginate(20);

        return view('admin.restaurants.kitchen.order_history', compact('completedOrders'));
    }

    /**
     * Print kitchen order docket
     */
    public function printDocket(ServiceRequest $serviceRequest)
    {
        $order = $serviceRequest->load(['booking.room', 'service', 'dayService']);
        
        // Determine Destination
        $destination = 'Internal';
        if ($order->is_walk_in) {
            $walkInName = $order->walk_in_name ?? 'Guest';
            $destination = str_contains(strtolower($walkInName), 'walk-in') ? $walkInName : 'WALK-IN (' . $walkInName . ')';
        } elseif ($order->booking) {
            $destination = 'ROOM ' . ($order->booking->room->room_number ?? 'N/A');
        } elseif ($order->dayService) {
            $destination = 'CEREMONY (' . ($order->dayService->name ?? 'Event') . ')';
        }

        // Determine Guest Name
        $guestName = $order->is_walk_in ? ($order->walk_in_name ?? 'General Guest') : ($order->booking->guest_name ?? 'Hotel Guest');

        // Determine Requested By
        $requestedBy = 'N/A';
        if ($order->reception_notes) {
            if (str_contains($order->reception_notes, 'Waiter: ')) {
                $parts = explode('Waiter: ', $order->reception_notes);
                $byParts = explode(' - Msg:', $parts[1] ?? '');
                $requestedBy = $byParts[0] ?? 'Waiter';
            } elseif (str_contains($order->reception_notes, 'Recorded by ')) {
                $parts = explode('Recorded by ', $order->reception_notes);
                $byParts = explode(':', $parts[1] ?? '');
                // The format is "Recorded by Role: Name"
                $requestedBy = trim($byParts[1] ?? 'Staff');
            }
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
        // Get group key from request
        $isWalkIn = $request->input('is_walk_in', false);
        $identifier = $request->input('identifier'); // walk_in_name or booking_id
        
        // Fetch all orders for this group
        $orders = ServiceRequest::with(['service', 'booking.room', 'dayService', 'approvedBy']);
        
        if ($isWalkIn) {
            $orders = $orders->where('is_walk_in', true)
                ->where('walk_in_name', $identifier)
                ->whereDate('requested_at', \Carbon\Carbon::today());
        } else {
            $orders = $orders->where('booking_id', $identifier);
        }
        
        $orders = $orders->orderBy('requested_at', 'desc')->get();
        
        if ($orders->isEmpty()) {
            abort(404, 'No orders found');
        }
        
        $first = $orders->first();
        
        // Determine Destination
        $destination = 'Internal';
        if ($first->is_walk_in) {
            $walkInName = $first->walk_in_name ?? 'Guest';
            $destination = str_contains(strtolower($walkInName), 'walk-in') ? $walkInName : 'WALK-IN (' . $walkInName . ')';
        } elseif ($first->booking) {
            $destination = 'ROOM ' . ($first->booking->room->room_number ?? 'N/A');
        }
        
        // Determine Guest Name
        $guestName = $first->is_walk_in ? ($first->walk_in_name ?? 'General Guest') : ($first->booking->guest_name ?? 'Hotel Guest');
        
        // Determine Requested By
        $requestedBy = 'N/A';
        if ($first->reception_notes) {
            if (str_contains($first->reception_notes, 'Waiter: ')) {
                $parts = explode('Waiter: ', $first->reception_notes);
                $byParts = explode(' - Msg:', $parts[1] ?? '');
                $requestedBy = $byParts[0] ?? 'Waiter';
            } elseif (str_contains($first->reception_notes, 'Recorded by: ')) {
                $parts = explode('Recorded by: ', $first->reception_notes);
                $requestedBy = trim($parts[1] ?? 'Staff');
            } elseif (str_contains($first->reception_notes, 'Recorded by ')) {
                $parts = explode('Recorded by ', $first->reception_notes);
                $byParts = explode(':', $parts[1] ?? '');
                $requestedBy = trim($byParts[1] ?? 'Staff');
            }
        }
        
        // Fallback to approvedBy name if logic above failed or returned generic 'Staff'
        if (($requestedBy === 'N/A' || $requestedBy === 'Staff') && $first->approvedBy) {
            $requestedBy = $first->approvedBy->name;
        }
        
        // Calculate total amount from non-cancelled orders only
        $totalAmount = $orders->filter(fn($o) => strtolower($o->status) !== 'cancelled')->sum('total_price_tsh');
        
        return view('dashboard.print-waiter-group-docket', compact('orders', 'destination', 'guestName', 'requestedBy', 'totalAmount', 'first'));
    }
}
