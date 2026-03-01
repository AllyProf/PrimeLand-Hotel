<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\SmsService;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

use App\Models\SmsLog;
use App\Models\Guest;
use App\Models\Staff;
use App\Models\Booking;

class SmsDashboardController extends Controller
{
    protected $smsService;

    public function __construct(SmsService $smsService)
    {
        $this->smsService = $smsService;
    }

    /**
     * Display the SMS Dashboard
     */
    public function index(Request $request)
    {
        $user = auth()->guard('staff')->user();
        $role = strtolower($user->role ?? 'manager');

        // 1. Fetch Balance (Live from API)
        $balanceRes = $this->smsService->getBalance();
        $balance = $balanceRes['success'] ? $balanceRes['balance'] : 'N/A';
        $smsCount = $balanceRes['success'] ? $balanceRes['sms_count'] : 0;

        // 2. Local Stats
        $todayStr = Carbon::today();
        $totalSent = SmsLog::where('status', 'sent')->count();
        $todayCount = SmsLog::whereDate('created_at', $todayStr)->where('status', 'sent')->count();
        $failedCount = SmsLog::where('status', 'failed')->count();

        // 3. Fetch Local Logs with Pagination
        $query = SmsLog::with(['sender'])->orderBy('created_at', 'desc');
        
        if ($request->filled('phone')) {
            $query->where('recipient', 'LIKE', '%' . $request->phone . '%');
        }
        
        $logs = $query->paginate(20)->withQueryString();

        // 4. Guest and Staff Audience Stats
        $totalGuestsCount = Guest::whereNotNull('phone')->count();
        $activeGuestsCount = Booking::where('check_in_status', 'checked_in')
            ->whereNotNull('guest_phone')
            ->distinct('guest_phone')
            ->count('guest_phone');
        
        $staffCount = Staff::whereNotNull('phone')->where('is_active', true)->count();
        
        $allGuests = Guest::whereNotNull('phone')
            ->select('id', 'name', 'phone')
            ->orderBy('name')
            ->get()
            ->map(function($g) { $g->type = 'Guest'; return $g; });

        $allStaff = Staff::whereNotNull('phone')
            ->where('is_active', true)
            ->select('id', 'name', 'phone')
            ->orderBy('name')
            ->get()
            ->map(function($s) {
                $s->type = 'Staff';
                $s->id_str = 'staff_' . $s->id;
                return $s;
            });

        return view('dashboard.sms.index', [
            'role' => $role,
            'balance' => $balance,
            'smsCount' => $smsCount,
            'logs' => $logs,
            'todayCount' => $todayCount,
            'totalSent' => $totalSent,
            'failedCount' => $failedCount,
            'totalGuestsCount' => $totalGuestsCount,
            'activeGuestsCount' => $activeGuestsCount,
            'staffCount' => $staffCount,
            'allGuests' => $allGuests,
            'allStaff' => $allStaff,
            'filters' => $request->all(),
            'activePage' => 'messaging',
        ]);
    }

    /**
     * Send Manual SMS
     */
    public function send(Request $request)
    {
        $request->validate([
            'phone' => 'required|string',
            'message' => 'required|string|max:500',
        ]);

        $res = $this->smsService->sendSingle($request->phone, $request->message);
        
        // Log locally
        SmsLog::create([
            'recipient' => $request->phone,
            'message' => $request->message,
            'status' => $res['success'] ? 'sent' : 'failed',
            'sender_id' => auth()->guard('staff')->id(),
            'api_response' => json_encode($res['data'] ?? []),
            'sms_count' => ceil(strlen($request->message) / 160)
        ]);

        if ($res['success']) {
            return back()->with('success', 'SMS sent successfully to ' . $request->phone);
        }

        return back()->with('error', 'Failed to send SMS: ' . $res['message']);
    }

    /**
     * Bulk Send SMS
     */
    public function bulkSend(Request $request)
    {
        $request->validate([
            'target' => 'required|in:all,active,all_staff,all_everyone,specific',
            'guest_ids' => 'required_if:target,specific|array',
            'staff_ids' => 'required_if:target,specific|array|nullable',
            'message' => 'required|string|max:500',
        ]);

        $recipients = [];
        if ($request->target === 'all') {
            $recipients = Guest::whereNotNull('phone')->pluck('phone')->toArray();
        } elseif ($request->target === 'active') {
            $recipients = Booking::where('check_in_status', 'checked_in')
                ->whereNotNull('guest_phone')
                ->pluck('guest_phone')
                ->toArray();
        } elseif ($request->target === 'all_staff') {
            $recipients = Staff::whereNotNull('phone')->where('is_active', true)->pluck('phone')->toArray();
        } elseif ($request->target === 'all_everyone') {
            $guestPhones = Guest::whereNotNull('phone')->pluck('phone')->toArray();
            $staffPhones = Staff::whereNotNull('phone')->where('is_active', true)->pluck('phone')->toArray();
            $recipients = array_merge($guestPhones, $staffPhones);
        } elseif ($request->target === 'specific') {
            if ($request->filled('guest_ids')) {
                $guestPhones = Guest::whereIn('id', $request->guest_ids)->whereNotNull('phone')->pluck('phone')->toArray();
                $recipients = array_merge($recipients, $guestPhones);
            }
            if ($request->filled('staff_ids')) {
                $staffPhones = Staff::whereIn('id', $request->staff_ids)->whereNotNull('phone')->pluck('phone')->toArray();
                $recipients = array_merge($recipients, $staffPhones);
            }
        }

        $recipients = array_unique(array_filter($recipients));

        if (empty($recipients)) {
            return back()->with('error', 'No valid phone numbers found for the selected target group.');
        }

        $res = $this->smsService->sendMultiple($recipients, $request->message);

        // Bulk Logging
        foreach ($recipients as $number) {
            SmsLog::create([
                'recipient' => $number,
                'message' => $request->message,
                'status' => $res['success'] ? 'sent' : 'failed',
                'sender_id' => auth()->guard('staff')->id(),
                'api_response' => json_encode($res['data'] ?? []),
                'sms_count' => ceil(strlen($request->message) / 160)
            ]);
        }

        if ($res['success']) {
            return back()->with('success', 'Bulk SMS sent to ' . count($recipients) . ' recipients.');
        }

        return back()->with('error', 'Failed to send bulk SMS: ' . $res['message']);
    }
}
