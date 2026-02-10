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
        // Only get bookings that are currently checked in
        $activeBookings = Booking::with('room')
            ->where('check_in_status', 'checked_in')
            ->orderBy('created_at', 'desc')
            ->get();

        // 2. Get Menu Items (Food and Bar)
        $categories = [
            'food', 'restaurant', 'drinks', 'spirits', 'wines', 
            'alcoholic_beverage', 'non_alcoholic_beverage', 'water', 
            'juices', 'energy_drinks', 'bar'
        ];
        
        $menuItems = Service::where('is_active', true)
            ->whereIn('category', $categories)
            ->orWhereIn('id', [3, 4]) // Generic Bar and Food Orders
            ->orderBy('name')
            ->get();

        return view('dashboard.waiter-dashboard', compact('activeBookings', 'menuItems'));
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
     * Submit an order (Handles multiple items and splits them)
     */
    public function storeOrder(Request $request)
    {
        $request->validate([
            'order_type' => 'required|in:resident,walk_in',
            'booking_id' => 'required_if:order_type,resident',
            'walk_in_name' => 'required_if:order_type,walk_in',
            'items' => 'required|array|min:1',
            'items.*.id' => 'required|exists:services,id',
            'items.*.quantity' => 'required|integer|min:1',
            'payment_status' => 'required|in:pending,paid',
            'payment_method' => 'required_if:payment_status,paid',
            'payment_reference' => 'nullable|string',
        ]);

        try {
            DB::beginTransaction();
            $user = Auth::guard('staff')->user();
            $createdRequests = [];

            foreach ($request->items as $itemData) {
                $service = Service::findOrFail($itemData['id']);
                $quantity = $itemData['quantity'];
                $totalPrice = $service->price_tsh * $quantity;

                $data = [
                    'service_id' => $service->id,
                    'quantity' => $quantity,
                    'unit_price_tsh' => $service->price_tsh,
                    'total_price_tsh' => $totalPrice,
                    'status' => 'pending',
                    'requested_at' => now(),
                    'reception_notes' => 'Ordered by Waiter: ' . ($user->name ?? 'Staff'),
                    'payment_status' => $request->payment_status,
                ];

                if ($request->order_type === 'resident') {
                    $data['booking_id'] = $request->booking_id;
                    $data['is_walk_in'] = false;
                    // If resident pays now, it's paid. If not, it's pending (to be billed to room)
                } else {
                    $data['is_walk_in'] = true;
                    $data['walk_in_name'] = $request->walk_in_name;
                }

                if ($request->payment_status === 'paid') {
                    $data['payment_method'] = $request->payment_method;
                    $data['payment_reference'] = $request->payment_reference;
                }

                $requestModel = ServiceRequest::create($data);
                $createdRequests[] = $requestModel->id;
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Order submitted successfully! ' . count($createdRequests) . ' item(s) sent to Kitchen/Bar.',
                'request_ids' => $createdRequests
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error submitting waiter order: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to submit order: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Show Waiter Order History
     */
    public function orders()
    {
        $user = Auth::guard('staff')->user();
        
        $orders = ServiceRequest::with(['service', 'booking.room'])
            ->where('reception_notes', 'LIKE', '%Ordered by Waiter: ' . $user->name . '%')
            ->orderBy('requested_at', 'desc')
            ->paginate(15);
            
        return view('dashboard.waiter-orders', compact('orders'));
    }
}
