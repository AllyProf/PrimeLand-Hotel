<?php

namespace App\Http\Controllers;

use App\Models\IssueReport;
use App\Models\Booking;
use App\Services\CurrencyExchangeService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class IssueReportController extends Controller
{
    /**
     * Customer: View all their issue reports
     */
    public function customerIndex(Request $request)
    {
        $user = Auth::guard('guest')->user() ?? Auth::user();
        
        if (!$user) {
            abort(403, 'Unauthorized. Please log in.');
        }
        
        $query = IssueReport::where('user_id', $user->id)
            ->with(['booking.room', 'room'])
            ->orderBy('created_at', 'desc');
        
        // Filter by status
        if ($request->has('status') && $request->status) {
            $query->where('status', $request->status);
        }
        
        $issues = $query->paginate(15);
        
        $stats = [
            'total' => IssueReport::where('user_id', $user->id)->count(),
            'pending' => IssueReport::where('user_id', $user->id)->where('status', 'pending')->count(),
            'in_progress' => IssueReport::where('user_id', $user->id)->where('status', 'in_progress')->count(),
            'resolved' => IssueReport::where('user_id', $user->id)->where('status', 'resolved')->count(),
        ];
        
        // Check if guest has any checked-in bookings
        $hasCheckedIn = Booking::where('guest_email', $user->email)
            ->where('check_in_status', 'checked_in')
            ->where('status', '!=', 'cancelled')
            ->exists();
        
        $currencyService = new CurrencyExchangeService();
        $exchangeRate = $currencyService->getUsdToTshRate();
        
        return view('dashboard.customer-issues', [
            'role' => 'customer',
            'userName' => $user->name ?? 'Guest User',
            'userRole' => 'Customer',
            'user' => $user,
            'issues' => $issues,
            'stats' => $stats,
            'exchangeRate' => $exchangeRate,
            'hasCheckedIn' => $hasCheckedIn,
        ]);
    }
    
    /**
     * Customer: View a single issue report
     */
    public function customerShow(IssueReport $issue)
    {
        $user = Auth::guard('guest')->user() ?? Auth::user();
        
        if (!$user) {
            abort(403, 'Unauthorized. Please log in.');
        }
        
        // Verify the issue belongs to the logged-in user
        if ($issue->user_id !== $user->id) {
            abort(403, 'Unauthorized access.');
        }
        
        $issue->load(['booking.room', 'room', 'user', 'guest']);
        
        $currencyService = new CurrencyExchangeService();
        $exchangeRate = $currencyService->getUsdToTshRate();
        
        return view('dashboard.customer-issue-show', [
            'role' => 'customer',
            'userName' => $user->name ?? 'Guest User',
            'userRole' => 'Customer',
            'issue' => $issue,
            'exchangeRate' => $exchangeRate,
        ]);
    }
    
    /**
     * Reception: View all issue reports
     */
    public function receptionIndex(Request $request)
    {
        $query = IssueReport::with(['user', 'guest', 'booking.room', 'room'])
            ->orderBy('created_at', 'desc');
        
        // Filter by status
        if ($request->has('status') && $request->status) {
            $query->where('status', $request->status);
        }
        
        // Filter by priority
        if ($request->has('priority') && $request->priority) {
            $query->where('priority', $request->priority);
        }
        
        // Search
        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('subject', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%")
                  ->orWhereHas('user', function($q) use ($search) {
                      $q->where('name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%");
                  });
            });
        }
        
        $issues = $query->paginate(20);
        
        $stats = [
            'total' => IssueReport::count(),
            'pending' => IssueReport::where('status', 'pending')->count(),
            'in_progress' => IssueReport::where('status', 'in_progress')->count(),
            'resolved' => IssueReport::where('status', 'resolved')->count(),
            'urgent' => IssueReport::where('priority', 'urgent')->where('status', '!=', 'resolved')->count(),
        ];
        
        $currencyService = new CurrencyExchangeService();
        $exchangeRate = $currencyService->getUsdToTshRate();
        
        return view('dashboard.reception-issues', [
            'role' => 'reception',
            'userName' => Auth::user()->name ?? 'Reception Staff',
            'userRole' => 'Reception',
            'issues' => $issues,
            'stats' => $stats,
            'exchangeRate' => $exchangeRate,
        ]);
    }
    
    /**
     * Reception: View a single issue report
     */
    public function receptionShow(IssueReport $issue)
    {
        $issue->load(['user', 'guest', 'booking.room', 'room']);
        
        $currencyService = new CurrencyExchangeService();
        $exchangeRate = $currencyService->getUsdToTshRate();
        
        return view('dashboard.reception-issue-show', [
            'role' => 'reception',
            'userName' => Auth::user()->name ?? 'Reception Staff',
            'userRole' => 'Reception',
            'issue' => $issue,
            'exchangeRate' => $exchangeRate,
        ]);
    }
    
    /**
     * Reception: Update issue status
     */
    public function receptionUpdate(Request $request, IssueReport $issue)
    {
        $validated = $request->validate([
            'status' => 'required|in:pending,in_progress,resolved',
            'admin_notes' => 'nullable|string',
        ]);
        
        $oldStatus = $issue->status;
        $newStatus = $validated['status'];
        $updateData = [
            'status' => $newStatus,
        ];
        
        if (isset($validated['admin_notes'])) {
            $updateData['admin_notes'] = $validated['admin_notes'];
        }
        
        if ($newStatus === 'resolved' && !$issue->resolved_at) {
            $updateData['resolved_at'] = now();
        }
        
        $issue->update($updateData);

        // Prepare response data first (to return as quickly as possible)
        $responseData = [
            'success' => true,
            'message' => 'Issue updated successfully.',
            'issue' => $issue->fresh(['user', 'guest', 'booking.room', 'room']),
        ];

        // Only proceed with notifications if the status actually changed
        if ($oldStatus !== $newStatus) {
            // 1. Queue email notifications to guest/reporter
            try {
                $reporter = $issue->getReporter();
                if ($reporter && $reporter->email) {
                    $shouldSend = true;
                    if ($reporter instanceof \App\Models\Guest) {
                        $shouldSend = $reporter->isNotificationEnabled('issue_report');
                    }
                    
                    if ($shouldSend) {
                        \Illuminate\Support\Facades\Mail::to($reporter->email)
                            ->queue(new \App\Mail\IssueStatusUpdateMail($issue->fresh(), $newStatus, $validated['admin_notes'] ?? null));
                    }
                }
            } catch (\Exception $e) {
                \Log::error('Failed to queue issue status update email to reporter: ' . $e->getMessage());
            }

            // Note: Staff email notifications removed for faster performance.

            // 3. Create or update in-app notification for guest
            try {
                $notificationType = 'issue_' . $newStatus;
                $notificationTitle = 'Issue ' . ucfirst(str_replace('_', ' ', $newStatus));
                $notificationMessage = 'Your issue "' . $issue->subject . '" status has been updated to ' . ucfirst(str_replace('_', ' ', $newStatus)) . '.';
                
                \App\Models\Notification::updateOrCreate(
                    [
                        'user_id' => $issue->user_id,
                        'notifiable_id' => $issue->id,
                        'notifiable_type' => IssueReport::class,
                        'type' => $notificationType,
                    ],
                    [
                        'title' => $notificationTitle,
                        'message' => $notificationMessage,
                        'icon' => $newStatus === 'resolved' ? 'fa-check-circle' : ($newStatus === 'in_progress' ? 'fa-cog' : 'fa-clock-o'),
                        'color' => $newStatus === 'resolved' ? 'success' : ($newStatus === 'in_progress' ? 'info' : 'warning'),
                        'role' => 'customer',
                        'link' => route('customer.issues.show', $issue),
                        'is_read' => false,
                    ]
                );
                
                // Mark associated staff notifications as read if resolved (moved here for consistency)
                if ($newStatus === 'resolved') {
                    \App\Models\Notification::where('notifiable_type', IssueReport::class)
                        ->where('notifiable_id', $issue->id)
                        ->where('type', 'issue_report')
                        ->whereIn('role', ['reception', 'manager'])
                        ->update(['is_read' => true]);
                }
            } catch (\Exception $e) {
                \Log::error('Failed to create/update issue status notification: ' . $e->getMessage());
            }
        }
        
        return response()->json($responseData);
    }
    
    /**
     * Admin: View all issue reports
     */
    public function adminIndex(Request $request)
    {
        $query = IssueReport::with(['user', 'guest', 'booking.room', 'room'])
            ->orderBy('created_at', 'desc');
        
        // Filter by status
        if ($request->has('status') && $request->status) {
            $query->where('status', $request->status);
        }
        
        // Filter by priority
        if ($request->has('priority') && $request->priority) {
            $query->where('priority', $request->priority);
        }
        
        // Search
        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('subject', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%")
                  ->orWhereHas('user', function($q) use ($search) {
                      $q->where('name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%");
                  });
            });
        }
        
        $issues = $query->paginate(20);
        
        $stats = [
            'total' => IssueReport::count(),
            'pending' => IssueReport::where('status', 'pending')->count(),
            'in_progress' => IssueReport::where('status', 'in_progress')->count(),
            'resolved' => IssueReport::where('status', 'resolved')->count(),
            'urgent' => IssueReport::where('priority', 'urgent')->where('status', '!=', 'resolved')->count(),
        ];
        
        $currencyService = new CurrencyExchangeService();
        $exchangeRate = $currencyService->getUsdToTshRate();
        
        return view('dashboard.admin-issues', [
            'role' => 'manager',
            'userName' => Auth::user()->name ?? 'Manager',
            'userRole' => 'Manager',
            'issues' => $issues,
            'stats' => $stats,
            'exchangeRate' => $exchangeRate,
        ]);
    }
    
    /**
     * Admin: View a single issue report
     */
    public function adminShow(IssueReport $issue)
    {
        $issue->load(['user', 'guest', 'booking.room', 'room']);
        
        $currencyService = new CurrencyExchangeService();
        $exchangeRate = $currencyService->getUsdToTshRate();
        
        return view('dashboard.admin-issue-show', [
            'role' => 'manager',
            'userName' => Auth::user()->name ?? 'Manager',
            'userRole' => 'Manager',
            'issue' => $issue,
            'exchangeRate' => $exchangeRate,
        ]);
    }
    
    /**
     * Admin: Update issue status
     */
    public function adminUpdate(Request $request, IssueReport $issue)
    {
        $validated = $request->validate([
            'status' => 'required|in:pending,in_progress,resolved',
            'admin_notes' => 'nullable|string',
        ]);
        
        $oldStatus = $issue->status;
        $newStatus = $validated['status'];
        
        $updateData = [
            'status' => $newStatus,
        ];
        
        if (isset($validated['admin_notes'])) {
            $updateData['admin_notes'] = $validated['admin_notes'];
        }
        
        if ($newStatus === 'resolved' && !$issue->resolved_at) {
            $updateData['resolved_at'] = now();
        }
        
        $issue->update($updateData);

        // Notify guest and manage notifications if status changed
        if ($oldStatus !== $newStatus) {
            // 1. Queue email to guest/reporter
            try {
                $reporter = $issue->getReporter();
                if ($reporter && $reporter->email) {
                    $shouldSend = true;
                    if ($reporter instanceof \App\Models\Guest) {
                        $shouldSend = $reporter->isNotificationEnabled('issue_report');
                    }
                    
                    if ($shouldSend) {
                        \Illuminate\Support\Facades\Mail::to($reporter->email)
                            ->queue(new \App\Mail\IssueStatusUpdateMail($issue->fresh(), $newStatus, $validated['admin_notes'] ?? null));
                    }
                }
            } catch (\Exception $e) {
                \Log::error('Failed to queue admin-initiated issue update email: ' . $e->getMessage());
            }

            // 2. Manage in-app notification
            try {
                if ($newStatus === 'resolved') {
                    \App\Models\Notification::create([
                        'user_id' => $issue->user_id,
                        'type' => 'issue_resolved',
                        'title' => 'Issue Resolved',
                        'message' => 'Your issue "' . $issue->subject . '" has been resolved.',
                        'icon' => 'fa-check-circle',
                        'color' => 'success',
                        'role' => 'customer',
                        'notifiable_id' => $issue->id,
                        'notifiable_type' => IssueReport::class,
                        'link' => route('customer.issues.show', $issue),
                    ]);
                    
                    // Mark housekeeping/reception notifications as read
                    \App\Models\Notification::where('notifiable_type', IssueReport::class)
                        ->where('notifiable_id', $issue->id)
                        ->where('type', 'issue_report')
                        ->update(['is_read' => true]);
                }
            } catch (\Exception $e) {
                \Log::error('Failed to create admin-initiated resolution notification: ' . $e->getMessage());
            }
        }
        
        return response()->json([
            'success' => true,
            'message' => 'Issue updated successfully.',
            'issue' => $issue->fresh(['user', 'booking.room', 'room']),
        ]);
    }

    /**
     * Store a newly created issue report
     */
    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'booking_id' => 'nullable|exists:bookings,id',
                'room_id' => 'nullable|exists:rooms,id',
                'issue_type' => 'required|in:room_issue,service_issue,technical_issue,other',
                'priority' => 'required|in:low,medium,high,urgent',
                'subject' => 'required|string|max:255',
                'description' => 'required|string',
            ]);

            // Get active booking if booking_id is not provided
            $booking = null;
            $room = null;
            
            if ($validated['booking_id']) {
                $booking = Booking::findOrFail($validated['booking_id']);
                $room = $booking->room;
            } elseif ($validated['room_id']) {
                $room = \App\Models\Room::findOrFail($validated['room_id']);
            } else {
                // Try to get the user's active booking
                $user = Auth::guard('guest')->user() ?? Auth::user();
                if ($user) {
                    $activeBooking = Booking::where('guest_email', $user->email)
                        ->where('status', 'confirmed')
                        ->where('check_in_status', 'checked_in')
                        ->orderBy('check_in', 'desc')
                        ->first();
                    
                    if ($activeBooking) {
                        $booking = $activeBooking;
                        $room = $activeBooking->room;
                    }
                }
            }

            $user = Auth::guard('guest')->user() ?? Auth::user();
            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized. Please log in.',
                ], 401);
            }

            $issueReport = IssueReport::create([
                'user_id' => $user->id,
                'booking_id' => $booking?->id,
                'room_id' => $room?->id ?? $validated['room_id'],
                'issue_type' => $validated['issue_type'],
                'priority' => $validated['priority'],
                'subject' => $validated['subject'],
                'description' => $validated['description'],
                'status' => 'pending',
            ]);

            // Create notification for admin and reception
            try {
                $notificationService = new \App\Services\NotificationService();
                
                // Notify manager
                \App\Models\Notification::create([
                    'type' => 'issue_report',
                    'title' => 'New Issue Reported',
                    'message' => ($user->name ?? 'Guest') . ' reported an issue: ' . $validated['subject'],
                    'icon' => 'fa-exclamation-triangle',
                    'color' => 'warning',
                    'role' => 'manager',
                    'notifiable_id' => $issueReport->id,
                    'notifiable_type' => IssueReport::class,
                    'link' => route('admin.issues.show', $issueReport),
                ]);
                
                // Notify reception
                \App\Models\Notification::create([
                    'type' => 'issue_report',
                    'title' => 'New Issue Reported',
                    'message' => ($user->name ?? 'Guest') . ' reported an issue: ' . $validated['subject'],
                    'icon' => 'fa-exclamation-triangle',
                    'color' => 'warning',
                    'role' => 'reception',
                    'notifiable_id' => $issueReport->id,
                    'notifiable_type' => IssueReport::class,
                    'link' => route('reception.issues.show', $issueReport),
                ]);
            } catch (\Exception $e) {
            } catch (\Exception $e) {
                Log::error('Failed to create issue report notification: ' . $e->getMessage());
            }

            // Notify reception via SMS (Instead of email for faster response)
            try {
                $receptionUsers = \App\Models\Staff::where('role', 'reception')->get();
                $smsService = new \App\Services\SmsService();
                foreach ($receptionUsers as $staff) {
                    if ($staff->phone) {
                        $smsService->sendIssueReportSms(
                            $staff->phone,
                            $user->name ?? 'Guest',
                            $room->room_number ?? 'N/A',
                            $validated['subject'],
                            $validated['priority']
                        );
                    }
                }
            } catch (\Exception $e) {
                Log::error('Failed to send issue report SMS to reception: ' . $e->getMessage());
            }

            return response()->json([
                'success' => true,
                'message' => 'Issue reported successfully! Our team will address it shortly.',
                'issue' => $issueReport,
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Please check the form and correct any errors.',
                'errors' => $e->errors(),
            ], 422);
        } catch (\Exception $e) {
            Log::error('Issue report error: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString(),
                'user' => Auth::guard('guest')->user()?->id ?? Auth::user()?->id,
            ]);
            
            // Provide more helpful error message in development
            $errorMessage = config('app.debug') 
                ? 'Error: ' . $e->getMessage() 
                : 'An error occurred while reporting the issue. Please try again.';
            
            return response()->json([
                'success' => false,
                'message' => $errorMessage,
            ], 500);
        }
    }
}
