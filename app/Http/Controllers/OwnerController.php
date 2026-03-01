<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Booking;
use App\Models\ServiceRequest;
use App\Models\ShoppingList;
use App\Models\Staff;
use App\Models\Room;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class OwnerController extends Controller
{
    public function dashboard()
    {
        $today = Carbon::today();
        $monthStart = Carbon::now()->startOfMonth();
        
        // Revenue Stats (Rooms + Services)
        $todayRoomRevenue = Booking::whereIn('status', ['checked_in', 'checked_out'])
            ->whereDate('check_in', '<=', $today)
            ->whereDate('check_out', '>=', $today)
            ->sum('total_bill_tsh');
            
        $todayServiceRevenue = ServiceRequest::where('status', 'completed')
            ->whereDate('created_at', $today)
            ->sum('total_price_tsh');
            
        $todayRevenue = $todayRoomRevenue + $todayServiceRevenue;
            
        $monthlyRoomRevenue = Booking::where('created_at', '>=', $monthStart)
            ->sum('total_bill_tsh');
            
        $monthlyServiceRevenue = ServiceRequest::where('status', 'completed')
            ->where('created_at', '>=', $monthStart)
            ->sum('total_price_tsh');
            
        $monthlyRevenue = $monthlyRoomRevenue + $monthlyServiceRevenue;


        // Shopping Stats
        $monthlyShopping = ShoppingList::where('status', 'completed')
            ->where('created_at', '>=', $monthStart)
            ->sum('total_actual_cost');

        // Occupation
        $totalRooms = Room::count();
        $occupiedRooms = Room::where('status', 'occupied')->count();
        $occupancyRate = $totalRooms > 0 ? ($occupiedRooms / $totalRooms) * 100 : 0;

        // Recent Finalized Shopping Lists
        $recentShopping = ShoppingList::where('status', 'completed')
            ->orderBy('updated_at', 'desc')
            ->limit(5)
            ->get();

        // --- Chart Data: Revenue Last 7 Days ---
        $last7Days = [];
        $revenueData = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = Carbon::today()->subDays($i);
            $last7Days[] = $date->format('D, d M');
            
            $roomRev = Booking::whereIn('status', ['checked_in', 'checked_out'])
                ->whereDate('check_in', '<=', $date)
                ->whereDate('check_out', '>=', $date)
                ->sum('total_bill_tsh');

            $servRev = ServiceRequest::where('status', 'completed')
                ->whereDate('created_at', $date)
                ->sum('total_price_tsh');
                
            $revenueData[] = (float)($roomRev + $servRev);
        }

        // --- Chart Data: Revenue Sources (This Month) ---
        $roomSource = (float)$monthlyRoomRevenue;
        
        $foodCategories = ['food', 'restaurant', 'breakfast', 'lunch', 'dinner'];
        $foodSource = (float)ServiceRequest::where('status', 'completed')
            ->where('created_at', '>=', $monthStart)
            ->whereHas('service', function($q) use ($foodCategories) {
                $q->whereIn('category', $foodCategories);
            })->sum('total_price_tsh');

        $barSource = (float)$monthlyServiceRevenue - $foodSource;


        return view('dashboard.owner-dashboard', array_merge(compact(
            'todayRevenue', 'monthlyRevenue', 'monthlyShopping', 
            'occupancyRate', 'occupiedRooms', 'totalRooms',
            'recentShopping', 'last7Days', 'revenueData',
            'roomSource', 'foodSource', 'barSource'
        ), ['activePage' => 'owner/dashboard']));


    }
}
