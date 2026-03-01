<?php

namespace App\Http\Controllers;

use App\Models\Staff;
use App\Models\ActivityLog;
use App\Models\Booking;
use App\Models\ServiceRequest;
use App\Models\RoomCleaningLog;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class StaffPerformanceController extends Controller
{
    /**
     * Display staff performance dashboard
     */
    public function index(Request $request)
    {
        $period = $request->get('period', 'month'); // day, week, month, year, all
        $dateFrom = $this->getDateFrom($period);
        
        $staffPerformance = Staff::where('is_active', true)
            ->get()
            ->map(function ($staff) use ($dateFrom) {
                $stats = $this->getStaffStats($staff, $dateFrom);
                $staff->performance = $stats;
                return $staff;
            });

        // 1. Team Summary Stats
        $summary = [
            'total_actions' => $staffPerformance->sum('performance.total_actions'),
            'total_revenue' => $staffPerformance->sum('performance.revenue_handled'),
            'tasks_completed' => $staffPerformance->sum('performance.tasks_completed'),
            'active_staff' => $staffPerformance->where('performance.total_actions', '>', 0)->count(),
        ];

        // 2. Rankings & Spotlight
        $topPerformers = $staffPerformance->sortByDesc('performance.metric_value')->take(3);
        $revenueLeaders = $staffPerformance->sortByDesc('performance.revenue_handled')->where('performance.revenue_handled', '>', 0)->take(3);
        
        // 3. Weekly Spotlight (Special calculation for "Staff of the Week")
        $weekStart = Carbon::now()->startOfWeek();
        $staffOfTheWeek = Staff::where('is_active', true)->get()->map(function($s) use ($weekStart) {
            $s->week_stats = $this->getStaffStats($s, $weekStart);
            return $s;
        })->sortByDesc('week_stats.metric_value')->first();

        // 4. Chart Data (Activity per day for last 14 days)
        $chartData = $this->getOverallPerformanceChart();

        return view('dashboard.owner.staff-performance', [
            'staffPerformance' => $staffPerformance,
            'summary' => $summary,
            'topPerformers' => $topPerformers,
            'revenueLeaders' => $revenueLeaders,
            'staffOfTheWeek' => $staffOfTheWeek,
            'chartData' => $chartData,
            'period' => $period,
            'periodLabel' => ucfirst($period),
            'activePage' => 'owner/performance'
        ]);
    }

    /**
     * Show detailed performance for a specific staff member
     */
    public function show(Staff $staff, Request $request)
    {
        $period = $request->get('period', 'month');
        $dateFrom = $this->getDateFrom($period);
        
        $stats = $this->getStaffStats($staff, $dateFrom);
        
        // Get recent activities
        $activities = ActivityLog::where('user_id', $staff->id)
            ->where('user_type', 'staff')
            ->where('created_at', '>=', $dateFrom)
            ->orderBy('created_at', 'desc')
            ->limit(50)
            ->get();

        return view('dashboard.owner.staff-performance-detail', [
            'staff' => $staff,
            'stats' => $stats,
            'activities' => $activities,
            'period' => $period,
            'activePage' => 'owner/performance'
        ]);
    }

    /**
     * Helper to get stats for a staff member
     */
    private function getStaffStats($staff, $dateFrom)
    {
        $role = strtolower(trim($staff->role));
        $stats = [
            'total_actions' => ActivityLog::where('user_id', $staff->id)->where('user_type', 'staff')->where('created_at', '>=', $dateFrom)->count(),
            'revenue_handled' => 0,
            'tasks_completed' => 0,
            'metric_label' => 'Actions',
            'metric_value' => 0
        ];

        // Specific metrics based on role
        if (in_array($role, ['waiter', 'bar_keeper', 'bar keeper', 'bartender'])) {
            $stats['tasks_completed'] = ServiceRequest::where('approved_by', $staff->id)
                ->where('created_at', '>=', $dateFrom)
                ->count();
            $stats['revenue_handled'] = ServiceRequest::where('approved_by', $staff->id)
                ->where('created_at', '>=', $dateFrom)
                ->where('payment_status', 'paid')
                ->sum('total_price_tsh');
            $stats['metric_label'] = 'Orders Handled';
            $stats['metric_value'] = $stats['tasks_completed'];
        } 
        elseif ($role === 'housekeeper') {
            $stats['tasks_completed'] = RoomCleaningLog::where('cleaned_by', $staff->id)
                ->where('cleaned_at', '>=', $dateFrom)
                ->count();
            $stats['metric_label'] = 'Rooms Cleaned';
            $stats['metric_value'] = $stats['tasks_completed'];
        }
        elseif ($role === 'reception' || $role === 'manager') {
            // Bookings handled (Check-ins)
            $stats['tasks_completed'] = Booking::where('check_in_status', 'checked_in')
                ->where('checked_in_at', '>=', $dateFrom)
                // We don't have a 'handled_by' field in Booking, 
                // but we can check ActivityLog for 'check-in' actions
                ->count(); // This is a rough estimate for now
            
            // Refine by ActivityLog
            $checkIns = ActivityLog::where('user_id', $staff->id)
                ->where('user_type', 'staff')
                ->where('action', 'like', '%check-in%')
                ->where('created_at', '>=', $dateFrom)
                ->count();
            
            $checkOuts = ActivityLog::where('user_id', $staff->id)
                ->where('user_type', 'staff')
                ->where('action', 'like', '%check-out%')
                ->where('created_at', '>=', $dateFrom)
                ->count();

            $stats['tasks_completed'] = $checkIns + $checkOuts;
            $stats['metric_label'] = 'Front Desk Actions';
            $stats['metric_value'] = $stats['tasks_completed'];
        }

        return $stats;
    }

    /**
     * Helper to get overall performance chart data
     */
    private function getOverallPerformanceChart()
    {
        $days = [];
        $actions = [];
        $revenue = [];
        
        for ($i = 13; $i >= 0; $i--) {
            $date = Carbon::today()->subDays($i);
            $days[] = $date->format('d M');
            
            $actions[] = ActivityLog::where('user_type', 'staff')
                ->whereDate('created_at', $date)
                ->count();
                
            $revenue[] = ServiceRequest::where('status', 'completed')
                ->whereDate('created_at', $date)
                ->sum('total_price_tsh');
        }
        
        return [
            'labels' => $days,
            'actions' => $actions,
            'revenue' => array_map(function($val) { return round($val / 1000, 1); }, $revenue) // in k (Thousands)
        ];
    }

    /**
     * Helper to get date from period
     */
    private function getDateFrom($period)
    {
        switch ($period) {
            case 'day': return Carbon::today();
            case 'week': return Carbon::now()->subWeek();
            case 'year': return Carbon::now()->subYear();
            case 'all': return Carbon::parse('2020-01-01');
            case 'month':
            default: return Carbon::now()->subMonth();
        }
    }
}
