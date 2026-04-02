<?php

namespace App\Http\Controllers;

use App\Models\LostAndFoundItem;
use App\Models\Room;
use App\Models\Booking;
use App\Models\Staff;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;

class LostAndFoundController extends Controller
{
    /**
     * Display a listing of the lost and found items for housekeepers.
     */
    public function housekeeperIndex()
    {
        $items = LostAndFoundItem::with(['room', 'booking'])
            ->where('staff_id', Auth::guard('staff')->id())
            ->orderBy('found_at', 'desc')
            ->paginate(15);

        return view('dashboard.housekeeper-lost-found', compact('items'));
    }

    /**
     * Show the form for creating a new lost and found item.
     */
    public function housekeeperCreate(Request $request)
    {
        $rooms = Room::orderBy('room_number')->get();
        $selectedRoomId = $request->query('room_id');
        $booking = null;

        if ($selectedRoomId) {
            // Try to find the most recent booking for this room
            $booking = Booking::where('room_id', $selectedRoomId)
                ->orderBy('check_out', 'desc')
                ->first();
        }

        return view('dashboard.housekeeper-lost-found-create', compact('rooms', 'selectedRoomId', 'booking'));
    }

    /**
     * Store a newly created lost and found item in storage.
     */
    public function housekeeperStore(Request $request)
    {
        $request->validate([
            'room_id' => 'nullable|exists:rooms,id',
            'item_name' => 'required|string|max:255',
            'description' => 'required|string|max:1000',
            'found_at' => 'required|date',
            'location_found' => 'nullable|string|max:255',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:5120',
            'guest_name' => 'nullable|string|max:255',
            'guest_phone' => 'nullable|string|max:20',
        ]);

        $imagePath = null;
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('lost-found', 'public');
            $imagePath = 'storage/' . $imagePath;
        }

        $housekeeper = Auth::guard('staff')->user();

        // Get booking_id and guest info if room_id is provided
        $bookingId = null;
        $guestName = $request->guest_name;
        $guestPhone = $request->guest_phone;

        if ($request->room_id) {
             $recentBooking = Booking::where('room_id', $request->room_id)
                ->orderBy('check_out', 'desc')
                ->first();
            if ($recentBooking) {
                $bookingId = $recentBooking->id;
                // Auto-fill guest info from booking if not provided in request
                $guestName = $guestName ?: $recentBooking->guest_name;
                $guestPhone = $guestPhone ?: $recentBooking->guest_phone;
            }
        }

        $item = LostAndFoundItem::create([
            'room_id' => $request->room_id,
            'booking_id' => $bookingId,
            'staff_id' => $housekeeper->id,
            'item_name' => $request->item_name,
            'description' => $request->description,
            'location_found' => $request->location_found ?: 'Room ' . (Room::find($request->room_id)->room_number ?? 'N/A'),
            'found_at' => $request->found_at,
            'image_path' => $imagePath,
            'status' => 'found',
            'guest_name' => $guestName,
            'guest_phone' => $guestPhone,
        ]);

        // Notify reception staff
        $receptionists = Staff::where('role', 'reception')->where('is_active', true)->get();
        foreach ($receptionists as $receptionist) {
            \App\Models\Notification::create([
                'user_id' => $receptionist->id,
                'type' => 'lost_found_reported',
                'title' => 'New Lost & Found Item',
                'message' => "Item: {$item->item_name} found in Room " . ($item->room->room_number ?? 'N/A'),
                'link' => route('reception.lost-found.index'),
                'notifiable_id' => $item->id,
                'notifiable_type' => LostAndFoundItem::class,
                'is_read' => false,
                'icon' => 'fa-suitcase',
                'color' => 'success',
            ]);
        }

        return redirect()->route('housekeeper.lost-found.index')->with('success', 'Forgotten item reported successfully and reception has been notified.');
    }

    /**
     * Display a listing of all lost and found items for reception.
     */
    public function receptionIndex(Request $request)
    {
        $query = LostAndFoundItem::with(['room', 'booking', 'finder', 'processor']);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('period')) {
            $now = Carbon::now();
            if ($request->period === 'week') {
                $query->where('found_at', '>=', $now->subDays(7));
            } elseif ($request->period === 'month') {
                $query->where('found_at', '>=', $now->subMonth());
            }
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('item_name', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%")
                  ->orWhere('guest_name', 'like', "%{$search}%")
                  ->orWhereHas('room', function($r) use ($search) {
                      $r->where('room_number', 'like', "%{$search}%");
                  });
            });
        }

        $items = $query->orderBy('found_at', 'desc')->paginate(20);

        return view('dashboard.reception-lost-found', compact('items'));
    }

    /**
     * Update the status of a lost and found item (claimed, disposed, etc.)
     */
    public function receptionUpdate(Request $request, LostAndFoundItem $item)
    {
        $request->validate([
            'status' => 'required|in:found,claimed,disposed,donated',
            'storage_location' => 'nullable|string|max:255',
            'claimed_by_name' => 'nullable|string|max:255',
            'reception_notes' => 'nullable|string|max:1000',
        ]);

        $updateData = [
            'status' => $request->status,
            'storage_location' => $request->storage_location,
            'reception_notes' => $request->reception_notes,
        ];

        if ($request->status === 'claimed' && $item->status !== 'claimed') {
            $updateData['claimed_at'] = now();
            $updateData['claimed_by_name'] = $request->claimed_by_name ?: $item->guest_name;
            $updateData['processed_by_staff_id'] = Auth::guard('staff')->id();
        }

        $item->update($updateData);

        return back()->with('success', 'Item status updated successfully.');
    }
}
