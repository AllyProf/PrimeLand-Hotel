<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Booking;
use App\Models\Feedback;

class FeedbackController extends Controller
{
    /**
     * Show feedback and reviews page
     */
    public function index()
    {
        $user = Auth::guard('guest')->user() ?? Auth::user();
        
        // If not logged in, we'll still show the page but with a message or guest identification form
        // To find their specific bookings, they'll need to be logged in or identify themselves
        
        if (!$user) {
            // Public view - No user context
            $allRooms = \App\Models\Room::select('id', 'room_number', 'room_type')
                ->where('status', 'available')
                ->orWhere('status', 'occupied')
                ->orderBy('room_number')
                ->get();

            return view('dashboard.customer-feedback', [
                'role' => 'public',
                'userName' => 'Valued Guest',
                'userRole' => 'Guest',
                'user' => null,
                'bookings' => collect(),
                'allRooms' => $allRooms,
                'submittedFeedback' => collect(),
                'hideSidebar' => true, // Flag to hide sidebar for public guests
                'message' => 'Please share your experience with us. Selecting a room is optional.'
            ]);
        }
        
        // Get completed bookings for feedback
        // Include bookings that are:
        // 1. Explicitly checked out (check_in_status = 'checked_out') - PRIMARY
        // 2. Status is 'completed'
        // 3. Check-out date has passed (for bookings that may not have been explicitly checked out)
        // Exclude bookings that already have feedback and cancelled bookings
        $bookingsWithFeedback = Feedback::pluck('booking_id')->toArray();
        
        // Get bookings - check both exact email match and case-insensitive match
        $bookings = Booking::where(function($query) use ($user) {
                // Match email exactly or case-insensitively
                $query->where('guest_email', $user->email)
                      ->orWhereRaw('LOWER(guest_email) = ?', [strtolower($user->email)]);
            })
            ->where('status', '!=', 'cancelled') // Exclude cancelled bookings
            ->where(function($query) {
                // Primary: Explicitly checked out (most important)
                $query->where('check_in_status', 'checked_out')
                      // OR status is completed
                      ->orWhere('status', 'completed')
                      // OR check-out date has passed (for bookings that may not have been explicitly checked out)
                      ->orWhere(function($q) {
                          $q->where('check_out', '<', now())
                            ->where('status', '!=', 'pending'); // Exclude pending bookings
                      });
            })
            ->whereNotIn('id', $bookingsWithFeedback) // Exclude bookings with existing feedback
            ->with('room')
            ->orderBy('check_out', 'desc')
            ->get(); // Use get() for dropdown
        
        // Log for debugging
        \Log::info('Feedback page - Found bookings for user', [
            'user_id' => $user->id,
            'user_email' => $user->email,
            'user_name' => $user->name,
            'bookings_count' => $bookings->count(),
            'bookings_with_feedback_count' => count($bookingsWithFeedback),
            'bookings' => $bookings->map(function($b) {
                return [
                    'id' => $b->id,
                    'reference' => $b->booking_reference,
                    'guest_email' => $b->guest_email,
                    'status' => $b->status,
                    'check_in_status' => $b->check_in_status,
                    'check_out' => $b->check_out ? $b->check_out->format('Y-m-d') : null,
                    'check_out_passed' => $b->check_out ? $b->check_out->lt(now()) : false,
                    'checked_out_at' => $b->checked_out_at ? $b->checked_out_at->format('Y-m-d H:i:s') : null,
                ];
            })->toArray(),
        ]);
        
        // Get user's submitted feedback
        $submittedFeedback = Feedback::where('guest_email', $user->email)
            ->with(['booking.room'])
            ->orderBy('created_at', 'desc')
            ->get();
        
        return view('dashboard.customer-feedback', [
            'role' => 'customer',
            'userName' => $user->name ?? 'Guest User',
            'userRole' => 'Customer',
            'user' => $user,
            'bookings' => $bookings,
            'submittedFeedback' => $submittedFeedback,
        ]);
    }

    /**
     * Submit feedback
     */
    public function submit(Request $request)
    {
        $request->validate([
            'booking_id' => 'nullable|exists:bookings,id',
            'room_id' => 'nullable|exists:rooms,id',
            'guest_name' => 'nullable|string|max:255',
            'guest_email' => 'nullable|email|max:255',
            'guest_phone' => 'nullable|string|max:50',
            'country' => 'nullable|string|max:100',
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'nullable|string|max:1000',
            'categories' => 'nullable|array',
        ]);

        $user = Auth::guard('guest')->user() ?? Auth::user();
        
        // Data for storage
        $data = [
            'rating' => $request->rating,
            'comment' => $request->comment,
            'categories' => $request->categories ?? [],
            'country' => $request->country,
        ];

        if ($user) {
            // Case 1: Logged in user with a specific booking
            if ($request->booking_id) {
                $booking = Booking::where('id', $request->booking_id)
                    ->where('guest_email', $user->email)
                    ->firstOrFail();

                // Check if feedback already exists for this booking
                if (Feedback::where('booking_id', $request->booking_id)->exists()) {
                    return response()->json([
                        'success' => false,
                        'message' => 'You have already submitted feedback for this booking.',
                    ], 422);
                }

                $data['booking_id'] = $booking->id;
                $data['guest_name'] = $booking->guest_name;
                $data['guest_email'] = $booking->guest_email;
                $data['guest_phone'] = $booking->guest_phone;
            } else {
                // Logged in user submitting general feedback
                $data['guest_name'] = $user->name;
                $data['guest_email'] = $user->email;
                // Try to get phone from recent booking if not provided
                $recentBooking = Booking::where('guest_email', $user->email)->orderBy('created_at', 'desc')->first();
                $data['guest_phone'] = $recentBooking->guest_phone ?? null;
            }
        } else {
            // Case 2: Public Guest submission (Not logged in)
            $data['guest_name'] = $request->guest_name ?? 'Anonymous Guest';
            $data['guest_email'] = $request->guest_email ?? 'guest@example.com';
            $data['guest_phone'] = $request->guest_phone;
            
            // If they provided a room but no booking
            if ($request->room_id && !$request->booking_id) {
                // Let's check if there's a recent booking for this room to associate with
                $recentBooking = Booking::where('room_id', $request->room_id)
                    ->where('status', '!=', 'cancelled')
                    ->orderBy('created_at', 'desc')
                    ->first();
                
                if ($recentBooking) {
                    $data['booking_id'] = $recentBooking->id;
                }
            }
        }

        // Store feedback
        $feedback = Feedback::create($data);

        // Send Notifications
        try {
            // 1. Send SMS
            if ($feedback->guest_phone) {
                $smsService = app(\App\Services\SmsService::class);
                $message = "Hello {$feedback->guest_name}, thank you for your feedback at PrimeLand Hotel. Your {$feedback->rating}-star review helps us improve. We hope to see you again soon!";
                $smsService->sendSingle($feedback->guest_phone, $message);
            }

            // 2. Send Email
            if ($feedback->guest_email && $feedback->guest_email !== 'guest@example.com') {
                \Illuminate\Support\Facades\Mail::to($feedback->guest_email)->send(new \App\Mail\FeedbackReceivedMail($feedback));
            }
        } catch (\Throwable $e) {
            \Log::error('Failed to send feedback notifications: ' . $e->getMessage());
            // We don't fail the request if notifications fail
        }
        
        return response()->json([
            'success' => true,
            'message' => 'Thank you for your feedback! We appreciate your input.',
        ]);
    }
}




