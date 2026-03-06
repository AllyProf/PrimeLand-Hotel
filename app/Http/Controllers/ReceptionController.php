<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Room;
use App\Services\CurrencyExchangeService;
use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Models\Shift;
use App\Models\ServiceRequest;
use App\Models\Staff;
use App\Models\Product;
use App\Models\DayService;
use App\Models\Recipe;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ReceptionController extends Controller
{
    /**
     * Determine the role based on current route
     */
    private function getRole()
    {
        $user = Auth::guard('staff')->user();
        if ($user && \App\Services\RolePermissionService::hasRole($user, 'head_chef')) {
            return 'head_chef';
        }
        
        $routeName = request()->route()->getName() ?? '';
        return str_starts_with($routeName, 'admin.') ? 'manager' : 'reception';
    }

    /**
     * Show all bookings for reception
     */
    /**
     * Show all bookings for reception
     */
    public function bookings(Request $request)
    {
        $query = Booking::with(['room', 'company', 'serviceRequests'])
            ->where('booking_reference', 'NOT LIKE', 'INV%')
            ->where('booking_reference', 'NOT LIKE', 'CINV%')
            ->orderBy('created_at', 'desc');

        // Filter by status
        if ($request->has('status') && $request->status && $request->status !== 'all') {
            if ($request->status === 'expired') {
                // Show expired bookings (pending bookings that have expired)
                $query->where(function($q) {
                    $q->where(function($subQ) {
                        // Pending bookings that have expired
                        $subQ->where('status', 'pending')
                             ->where('payment_status', 'pending')
                             ->whereNotNull('expires_at')
                             ->where('expires_at', '<=', \Carbon\Carbon::now());
                    })->orWhere(function($subQ) {
                        // Cancelled bookings with expiration reason
                        $subQ->where('status', 'cancelled')
                             ->whereNotNull('cancellation_reason')
                             ->where('cancellation_reason', 'like', '%expired%');
                    });
                });
            } else {
                $query->where('status', $request->status);
            }
        } elseif (!$request->has('status') || !$request->status || $request->status === 'all') {
            // Exclude expired bookings from main list unless specifically requested
            $query->where(function($q) {
                $q->where(function($subQ) {
                    $subQ->where('status', '!=', 'pending')
                         ->orWhere('payment_status', '!=', 'pending')
                         ->orWhereNull('expires_at')
                         ->orWhere('expires_at', '>', \Carbon\Carbon::now());
                });
            });
        }

        // Filter by payment status
        if ($request->has('payment_status') && $request->payment_status && $request->payment_status !== 'all') {
            $query->where('payment_status', $request->payment_status);
        }

        // Filter by check-in status
        if ($request->has('check_in_status') && $request->check_in_status && $request->check_in_status !== 'all') {
            $query->where('check_in_status', $request->check_in_status);
        }

        // Filter by booking type (individual or corporate)
        $bookingType = $request->get('type', 'individual'); // Default to individual
        if ($bookingType === 'corporate') {
            $query->where('is_corporate_booking', true);
            
            // Group corporate bookings by company_id
            // Get unique company IDs first
            $companyIds = (clone $query)->reorder()->whereNotNull('company_id')->distinct()->pluck('company_id');
            
            // Get bookings grouped by company
            $groupedBookings = collect();
            foreach ($companyIds as $companyId) {
                $companyBookings = Booking::with(['room', 'company', 'serviceRequests'])
                    ->where('is_corporate_booking', true)
                    ->where('company_id', $companyId);
                
                // Apply filters
                if ($request->has('status') && $request->status) {
                    if ($request->status === 'expired') {
                        $companyBookings->where(function($q) {
                            $q->where(function($subQ) {
                                $subQ->where('status', 'pending')
                                     ->where('payment_status', 'pending')
                                     ->whereNotNull('expires_at')
                                     ->where('expires_at', '<=', \Carbon\Carbon::now());
                            })->orWhere(function($subQ) {
                                $subQ->where('status', 'cancelled')
                                     ->whereNotNull('cancellation_reason')
                                     ->where('cancellation_reason', 'like', '%expired%');
                            });
                        });
                    } else {
                        $companyBookings->where('status', $request->status);
                    }
                } else {
                    $companyBookings->where(function($q) {
                        $q->where(function($subQ) {
                            $subQ->where('status', '!=', 'pending')
                                 ->orWhere('payment_status', '!=', 'pending')
                                 ->orWhereNull('expires_at')
                                 ->orWhere('expires_at', '>', \Carbon\Carbon::now());
                        });
                    });
                }
                
                if ($request->has('payment_status') && $request->payment_status) {
                    $companyBookings->where('payment_status', $request->payment_status);
                }
                
                if ($request->has('check_in_status') && $request->check_in_status) {
                    $companyBookings->where('check_in_status', $request->check_in_status);
                }
                
                // Search by guest name, booking reference, or company name
                if ($request->has('search') && $request->search) {
                    $search = $request->search;
                    $companyBookings->where(function($q) use ($search) {
                        $q->where('guest_name', 'like', "%{$search}%")
                          ->orWhere('booking_reference', 'like', "%{$search}%")
                          ->orWhere('guest_email', 'like', "%{$search}%")
                          ->orWhereHas('company', function($q) use ($search) {
                              $q->where('name', 'like', "%{$search}%")
                                ->orWhere('email', 'like', "%{$search}%");
                          });
                    });
                }
                
                $bookingsForCompany = $companyBookings->orderBy('created_at', 'desc')->get();
                
                if ($bookingsForCompany->count() > 0) {
                    $groupedBookings->push([
                        'company' => $bookingsForCompany->first()->company,
                        'bookings' => $bookingsForCompany,
                        'first_booking' => $bookingsForCompany->first(), // Use first booking for dates, etc.
                    ]);
                }
            }
            
            // Convert to paginator-like structure
            $bookings = new \Illuminate\Pagination\LengthAwarePaginator(
                $groupedBookings->forPage($request->get('page', 1), 20),
                $groupedBookings->count(),
                20,
                $request->get('page', 1),
                ['path' => $request->url(), 'query' => $request->query()]
            );
        } else {
            // Default to individual bookings (is_corporate_booking is false or null)
            $query->where(function($q) {
                $q->where('is_corporate_booking', false)
                  ->orWhereNull('is_corporate_booking');
            });
            
            // Search by guest name or booking reference
            if ($request->has('search') && $request->search) {
                $search = $request->search;
                $query->where(function($q) use ($search) {
                    $q->where('guest_name', 'like', "%{$search}%")
                      ->orWhere('booking_reference', 'like', "%{$search}%")
                      ->orWhere('guest_email', 'like', "%{$search}%");
                });
            }
            
            $bookings = $query->paginate(20);
        }

        // Get statistics filtered by booking type
        if ($bookingType === 'corporate') {
            // For corporate bookings, count unique companies
            $baseQuery = Booking::where('is_corporate_booking', true);
            
            // Apply status filter if provided
            if ($request->has('status') && $request->status == 'expired') {
                $baseQuery->where(function($q) {
                    $q->where(function($subQ) {
                        $subQ->where('status', 'pending')
                             ->where('payment_status', 'pending')
                             ->whereNotNull('expires_at')
                             ->where('expires_at', '<=', \Carbon\Carbon::now());
                    })->orWhere(function($subQ) {
                        $subQ->where('status', 'cancelled')
                             ->whereNotNull('cancellation_reason')
                             ->where('cancellation_reason', 'like', '%expired%');
                    });
                });
            } else if ($request->has('status') && $request->status) {
                $baseQuery->where('status', $request->status);
            }
            
            // Count unique companies
            $totalCompanies = $baseQuery->whereNotNull('company_id')->distinct('company_id')->count('company_id');
            
            // For other stats, count companies that have bookings matching the criteria
            $confirmedCompanies = (clone $baseQuery)->where('status', 'confirmed')->whereNotNull('company_id')->distinct('company_id')->count('company_id');
            $checkedInCompanies = (clone $baseQuery)->where('check_in_status', 'checked_in')->whereNotNull('company_id')->distinct('company_id')->count('company_id');
            $checkedOutCompanies = (clone $baseQuery)->where('check_in_status', 'checked_out')->whereNotNull('company_id')->distinct('company_id')->count('company_id');
            
            // Also get overall stats for tabs
            $allIndividualQuery = Booking::where(function($q) {
                $q->where('is_corporate_booking', false)
                  ->orWhereNull('is_corporate_booking');
            });
            
            $allCorporateQuery = Booking::where('is_corporate_booking', true);
            
            $stats = [
                'total' => $totalCompanies,
                'individual_total' => $allIndividualQuery->count(),
                'corporate_total' => $allCorporateQuery->whereNotNull('company_id')->distinct('company_id')->count('company_id'),
                'pending' => 0, // Not applicable for corporate view
                'confirmed' => $confirmedCompanies,
                'cancelled' => 0, // Not applicable for corporate view
                'completed' => 0, // Not applicable for corporate view
                'expired' => 0, // Not applicable for corporate view
                'checked_in' => $checkedInCompanies,
                'checked_out' => $checkedOutCompanies,
            ];
        } else {
            // For individual bookings, count individual bookings
            // Base query for individual bookings
            $baseQuery = Booking::where(function($q) {
                $q->where('is_corporate_booking', false)
                  ->orWhereNull('is_corporate_booking');
            });
            
            // Synchronize stats with all current filters
            $statsQuery = clone $baseQuery;
            
            // 1. Apply status filter (excluding expired)
            if ($request->has('status') && $request->status && $request->status !== 'all') {
                if ($request->status === 'expired') {
                    $statsQuery->where(function($q) {
                        $q->where(function($subQ) {
                            $subQ->where('status', 'pending')
                                 ->where('payment_status', 'pending')
                                 ->whereNotNull('expires_at')
                                 ->where('expires_at', '<=', \Carbon\Carbon::now());
                        })->orWhere(function($subQ) {
                            $subQ->where('status', 'cancelled')
                                 ->whereNotNull('cancellation_reason')
                                 ->where('cancellation_reason', 'like', '%expired%');
                        });
                    });
                } else {
                    $statsQuery->where('status', $request->status);
                }
            } else {
                // Default view (no expired)
                $statsQuery->where(function($q) {
                    $q->where('status', '!=', 'pending')
                         ->orWhere('payment_status', '!=', 'pending')
                         ->orWhereNull('expires_at')
                         ->orWhere('expires_at', '>', \Carbon\Carbon::now());
                });
            }

            // 2. Apply payment status filter
            if ($request->has('payment_status') && $request->payment_status && $request->payment_status !== 'all') {
                $statsQuery->where('payment_status', $request->payment_status);
            }

            // 3. Apply check-in status filter
            if ($request->has('check_in_status') && $request->check_in_status && $request->check_in_status !== 'all') {
                $statsQuery->where('check_in_status', $request->check_in_status);
            }

            // 4. Apply search filter
            if ($request->has('search') && $request->search) {
                $search = $request->search;
                $statsQuery->where(function($q) use ($search) {
                    $q->where('guest_name', 'like', "%{$search}%")
                      ->orWhere('booking_reference', 'like', "%{$search}%")
                      ->orWhere('guest_email', 'like', "%{$search}%");
                });
            }
            
            // Also get global totals for the tabs (always unfiltered by status/payment/check-in)
            $allIndividualTotal = Booking::where(function($q) {
                $q->where('is_corporate_booking', false)
                  ->orWhereNull('is_corporate_booking');
            })->count();
            
            $allCorporateTotal = Booking::where('is_corporate_booking', true)
                ->whereNotNull('company_id')
                ->distinct('company_id')
                ->count('company_id');
            
            $stats = [
                'total' => $statsQuery->count(),
                'individual_total' => $allIndividualTotal,
                'corporate_total' => $allCorporateTotal,
                'pending' => (clone $statsQuery)->where('status', 'pending')->count(),
                'confirmed' => (clone $statsQuery)->where('status', 'confirmed')->count(),
                'cancelled' => (clone $statsQuery)->where('status', 'cancelled')->count(),
                'completed' => (clone $statsQuery)->where('status', 'completed')->count(),
                'expired' => (clone $statsQuery)->where('status', 'cancelled')
                    ->whereNotNull('cancellation_reason')
                    ->where('cancellation_reason', 'like', '%expired automatically%')
                    ->count(),
                'checked_in' => (clone $statsQuery)->where('check_in_status', 'checked_in')->count(),
                'checked_out' => (clone $statsQuery)->where('check_in_status', 'checked_out')->count(),
            ];
        }

        $role = $this->getRole();
        
        return view('dashboard.bookings-list', [
            'bookings' => $bookings,
            'role' => $role,
            'userName' => auth()->user()->name ?? ($role === 'manager' ? 'Manager' : 'Reception Staff'),
            'userRole' => $role === 'manager' ? 'Manager' : 'Reception',
            'filters' => $request->only(['status', 'payment_status', 'check_in_status', 'search', 'type']),
            'stats' => $stats,
            'bookingType' => $bookingType,
        ]);
    }

    /**
     * Show new reservation form
     */
    public function newReservation()
    {
        $rooms = Room::orderBy('room_number')->get();
        
        $currencyService = new CurrencyExchangeService();
        $exchangeRate = $currencyService->getUsdToTshRate();

        $role = $this->getRole();
        return view('dashboard.reception-new-reservation', [
            'role' => $role,
            'userName' => auth()->user()->name ?? ($role === 'manager' ? 'Manager' : 'Reception Staff'),
            'userRole' => $role === 'manager' ? 'Manager' : 'Reception',
            'rooms' => $rooms,
            'exchangeRate' => $exchangeRate,
        ]);
    }

    /**
     * Show check-in page
     */
    public function checkIn(Request $request)
    {
        // Filter by booking type (individual or corporate)
        $bookingType = $request->get('type', 'individual'); // Default to individual
        
        $query = Booking::with('room')
            ->where('check_in_status', 'pending')
            ->where(function($q) {
                // Traditionally confirmed/paid bookings
                $q->where(function($q1) {
                    $q1->where('status', 'confirmed')
                       ->whereIn('payment_status', ['paid', 'partial'])
                       ->where(function($q2) {
                           $q2->where('payment_status', 'paid')
                              ->orWhere(function($subQ) {
                                  $subQ->where('payment_status', 'partial')
                                       ->whereNotNull('amount_paid')
                                       ->where('amount_paid', '>', 0);
                              });
                       });
                })
                // OR mobile submissions waiting for review (even if pending payment/status)
                ->orWhereNotNull('mobile_checkin_submitted_at');
            });

        // Filter by booking type
        if ($bookingType === 'corporate') {
            $query->where('is_corporate_booking', true);
            
            // Group corporate bookings by company_id
            $companyIds = $query->whereNotNull('company_id')->distinct()->pluck('company_id');
            
            $groupedBookings = collect();
            foreach ($companyIds as $companyId) {
                $companyBookings = Booking::with(['room', 'company', 'serviceRequests'])
                    ->where('is_corporate_booking', true)
                    ->where('company_id', $companyId)
                    ->where('check_in_status', 'pending')
                    ->where(function($q) {
                        // Traditionally confirmed/paid bookings
                        $q->where(function($q1) {
                            $q1->where('status', 'confirmed')
                               ->whereIn('payment_status', ['paid', 'partial'])
                               ->where(function($q2) {
                                   $q2->where('payment_status', 'paid')
                                      ->orWhere(function($subQ) {
                                          $subQ->where('payment_status', 'partial')
                                               ->whereNotNull('amount_paid')
                                               ->where('amount_paid', '>', 0);
                                      });
                               });
                        })
                        // OR mobile submissions waiting for review
                        ->orWhereNotNull('mobile_checkin_submitted_at');
                    });
                
                // Search functionality
                if ($request->has('search') && $request->search) {
                    $search = $request->search;
                    $companyBookings->where(function($q) use ($search) {
                        $q->where('booking_reference', 'like', "%{$search}%")
                          ->orWhere('guest_name', 'like', "%{$search}%")
                          ->orWhere('guest_email', 'like', "%{$search}%")
                          ->orWhereHas('company', function($q) use ($search) {
                              $q->where('name', 'like', "%{$search}%")
                                ->orWhere('email', 'like', "%{$search}%");
                          });
                    });
                }
                
                // Filter by check-in date
                if ($request->has('check_in_date') && $request->check_in_date) {
                    $companyBookings->whereDate('check_in', '<=', $request->check_in_date);
                } else {
                    // Default: show bookings where check-in is today or tomorrow (1 day before)
                    // OR if they have submitted records for review
                    $companyBookings->where(function($q) {
                        $q->whereDate('check_in', '<=', Carbon::today()->addDays(3))
                          ->orWhereNotNull('mobile_checkin_submitted_at');
                    });
                }
                
                $bookingsForCompany = $companyBookings->orderBy('check_in', 'asc')->get();
                
                if ($bookingsForCompany->count() > 0) {
                    $groupedBookings->push([
                        'company' => $bookingsForCompany->first()->company,
                        'bookings' => $bookingsForCompany,
                        'first_booking' => $bookingsForCompany->first(),
                    ]);
                }
            }
            
            // Paginate grouped bookings manually
            $currentPage = \Illuminate\Pagination\LengthAwarePaginator::resolveCurrentPage();
            $perPage = 20;
            $currentItems = $groupedBookings->slice(($currentPage - 1) * $perPage, $perPage)->values();
            $bookings = new \Illuminate\Pagination\LengthAwarePaginator(
                $currentItems,
                $groupedBookings->count(),
                $perPage,
                $currentPage,
                ['path' => $request->url(), 'query' => $request->query()]
            );
        } else {
            // Individual bookings
            $query->where('is_corporate_booking', false);

            // Search functionality
            if ($request->has('search') && $request->search) {
                $search = $request->search;
                $query->where(function($q) use ($search) {
                    $q->where('booking_reference', 'like', "%{$search}%")
                      ->orWhere('guest_name', 'like', "%{$search}%")
                      ->orWhere('guest_email', 'like', "%{$search}%");
                });
            }

            // Filter by check-in date - show customers 1 day before check-in date
            if ($request->has('check_in_date') && $request->check_in_date) {
                // Show bookings where check-in date is on or before the selected date
                $query->whereDate('check_in', '<=', $request->check_in_date);
            } else {
                // Default: show bookings where check-in is today or tomorrow (1 day before)
                // OR if they have submitted records for review
                $query->where(function($q) {
                    $q->whereDate('check_in', '<=', Carbon::today()->addDays(3))
                      ->orWhereNotNull('mobile_checkin_submitted_at');
                });
            }

            $bookings = $query->orderBy('check_in', 'asc')->paginate(20);
        }

        $currencyService = new CurrencyExchangeService();
        $exchangeRate = $currencyService->getUsdToTshRate();

        // Calculate statistics (must match the main query logic)
        $stats = [
            'individual_total' => Booking::where(function($q) {
                    $q->where('is_corporate_booking', false)
                      ->orWhereNull('is_corporate_booking');
                })
                ->where('check_in_status', 'pending')
                ->where(function($q) {
                    $q->where(function($q1) {
                        $q1->where('status', 'confirmed')
                           ->whereIn('payment_status', ['paid', 'partial'])
                           ->where(function($q2) {
                               $q2->where('payment_status', 'paid')
                                  ->orWhere(function($subQ) {
                                      $subQ->where('payment_status', 'partial')
                                           ->whereNotNull('amount_paid')
                                           ->where('amount_paid', '>', 0);
                                  });
                           });
                    })
                    ->orWhereNotNull('mobile_checkin_submitted_at');
                })
                ->where(function($q) {
                    $q->whereDate('check_in', '<=', Carbon::today()->addDays(3))
                      ->orWhereNotNull('mobile_checkin_submitted_at');
                })
                ->count(),
            'corporate_total' => Booking::where('is_corporate_booking', true)
                ->where('check_in_status', 'pending')
                ->where(function($q) {
                    $q->where(function($q1) {
                        $q1->where('status', 'confirmed')
                           ->whereIn('payment_status', ['paid', 'partial'])
                           ->where(function($q2) {
                               $q2->where('payment_status', 'paid')
                                  ->orWhere(function($subQ) {
                                      $subQ->where('payment_status', 'partial')
                                           ->whereNotNull('amount_paid')
                                           ->where('amount_paid', '>', 0);
                                  });
                           });
                    })
                    ->orWhereNotNull('mobile_checkin_submitted_at');
                })
                ->where(function($q) {
                    $q->whereDate('check_in', '<=', Carbon::today()->addDays(3))
                      ->orWhereNotNull('mobile_checkin_submitted_at');
                })
                ->distinct('company_id')
                ->count('company_id'),
        ];

        $role = $this->getRole();
        $user = auth()->guard('staff')->user() ?? auth()->guard('guest')->user();
        return view('dashboard.reception-check-in', [
            'role' => $role,
            'userName' => $user->name ?? ($role === 'manager' ? 'Manager' : 'Reception Staff'),
            'userRole' => $role === 'manager' ? 'Manager' : 'Reception',
            'user' => $user,
            'bookings' => $bookings,
            'bookingType' => $bookingType,
            'exchangeRate' => $exchangeRate,
            'filters' => $request->only(['search', 'check_in_date']),
            'stats' => $stats,
        ]);
    }

    /**
     * Show check-out page
     */
    public function checkOut(Request $request)
    {
        // Filter by booking type (individual or corporate)
        $bookingType = $request->get('type', 'individual'); // Default to individual
        
        // Show all guests who are checked in OR checked out but not paid
        // Include bookings that are checked in (ready for check-out)
        $query = Booking::with('room')
            ->where('status', 'confirmed') // Only show confirmed bookings
            ->where(function($q) {
                $q->where('check_in_status', 'checked_in')
                  ->orWhere(function($q2) {
                      $q2->where('check_in_status', 'checked_out')
                         ->where('payment_status', '!=', 'paid');
                  });
            });

        // Filter by booking type
        if ($bookingType === 'corporate') {
            $query->where('is_corporate_booking', true);
            
            // Group corporate bookings by company_id
            $companyIds = $query->whereNotNull('company_id')->distinct()->pluck('company_id');
            
            $groupedBookings = collect();
            foreach ($companyIds as $companyId) {
                $companyBookings = Booking::with(['room', 'company', 'serviceRequests.service'])
                    ->where('is_corporate_booking', true)
                    ->where('company_id', $companyId)
                    ->where('status', 'confirmed') // Only show confirmed bookings
                    ->where(function($q) {
                        // Show anyone who is checked in or recently checked out
                        $q->where('check_in_status', 'checked_in')
                          ->orWhere('check_in_status', 'checked_out');
                    });
                
                // Search functionality
                if ($request->has('search') && $request->search) {
                    $search = $request->search;
                    $companyBookings->where(function($q) use ($search) {
                        $q->where('booking_reference', 'like', "%{$search}%")
                          ->orWhere('guest_name', 'like', "%{$search}%")
                          ->orWhere('guest_email', 'like', "%{$search}%")
                          ->orWhereHas('company', function($q) use ($search) {
                              $q->where('name', 'like', "%{$search}%")
                                ->orWhere('email', 'like', "%{$search}%");
                          });
                    });
                }
                
                // Filter by check-out date (optional - if provided)
                if ($request->has('check_out_date') && $request->check_out_date) {
                    $companyBookings->whereDate('check_out', '<=', $request->check_out_date);
                }
                // Show all checked-in bookings regardless of check-out date
                
                $bookingsForCompany = $companyBookings->orderBy('check_out', 'asc')->get();
                
                if ($bookingsForCompany->count() > 0) {
                    $groupedBookings->push([
                        'company' => $bookingsForCompany->first()->company,
                        'bookings' => $bookingsForCompany,
                        'first_booking' => $bookingsForCompany->first(),
                    ]);
                }
            }
            
            // Calculate outstanding balances before pagination
            $currencyService = new CurrencyExchangeService();
            $exchangeRate = $currencyService->getUsdToTshRate();
            
            // Use map to transform the collection and add totals
            $groupedBookings = $groupedBookings->map(function ($group) use ($exchangeRate) {
                $totalOutstandingTsh = 0;
                $totalOutstandingUsd = 0;
                
                foreach ($group['bookings'] as $booking) {
                    // Use locked exchange rate from booking, or fallback to current rate
                    $bookingExchangeRate = $booking->locked_exchange_rate ?? $exchangeRate;
                    
                    // Calculate service charges
                    $serviceRequests = $booking->serviceRequests()
                        ->whereIn('status', ['pending', 'approved', 'preparing', 'completed'])
                        ->with('service')
                        ->get();
                    
                    $totalServiceChargesTsh = $serviceRequests->sum('total_price_tsh');
                    
                    // Calculate extension cost if extension was approved
                    $extensionCostUsd = 0;
                    if ($booking->extension_status === 'approved' && $booking->original_check_out && $booking->extension_requested_to) {
                        $originalCheckOut = \Carbon\Carbon::parse($booking->original_check_out);
                        $requestedCheckOut = \Carbon\Carbon::parse($booking->extension_requested_to);
                        $extensionNights = $originalCheckOut->diffInDays($requestedCheckOut);
                        if ($extensionNights > 0 && $booking->room) {
                            $extensionCostUsd = $booking->room->price_per_night * $extensionNights;
                        }
                    }
                    $extensionCostTsh = $extensionCostUsd * $bookingExchangeRate;
                    
                    // Check payment responsibility - if self-paid, exclude service charges from company bill
                    $paymentResponsibility = $booking->payment_responsibility ?? 'company';
                    $companyResponsibleServiceChargesTsh = 0;
                    
                    if ($paymentResponsibility === 'self') {
                        // Guest pays for services - exclude from company bill
                        $companyResponsibleServiceChargesTsh = 0;
                    } else {
                        // Company pays for services
                        $companyResponsibleServiceChargesTsh = $totalServiceChargesTsh;
                    }
                    
                    // Company's total bill (room + company-responsible services + extensions)
                    // Note: extensionCostTsh is already included in booking->total_price
                    $companyBillTsh = ($booking->total_price * $bookingExchangeRate) + $companyResponsibleServiceChargesTsh;
                    
                    // Identify what portion of amount_paid was for guest-paid services
                    // Logic: If payment_responsibility is 'self', only services paid via 'cash' (bar) or any method at reception 
                    // should be considered part of the booking's amount_paid field and thus subtracted from the company's credit.
                    // For now, let's look at services that are 'paid' and NOT 'room_charge'.
                    $paidServiceRequests = $serviceRequests->where('payment_status', 'paid');
                    
                    // We need to know which of these 'paid' services were recorded into 'amount_paid'.
                    // Based on our system: Reception payments always update it. 
                    // Bar payments update it if they are 'cash' (and now everything else after the fix).
                    $servicePaymentsInAmountPaidTsh = $paidServiceRequests->filter(function($sr) {
                        // If it's paid and not charged to room, it was a direct payment.
                        // We check if it was likely recorded in the booking's amount_paid.
                        return true; // Conservative approach: assume all direct service payments are in amount_paid
                    })->sum('total_price_tsh');
                    
                    // Identify total amount already paid for services by the guest
                    $guestPaidServicesTsh = $serviceRequests->where('payment_status', 'paid')->sum('total_price_tsh');
                    
                    // Total amount recorded in the booking (includes room payments + service payments at reception)
                    $totalPaidTsh = ($booking->amount_paid ?? 0) * $bookingExchangeRate;
                    
                    // The company's contribution to the room is the total paid in the booking MINUS 
                    // anything the guest paid for services.
                    $companyPaidTsh = max(0, $totalPaidTsh - $guestPaidServicesTsh);
                    
                    // Defensive check: Company paid cannot exceed the Room price (unless they prepaid)
                    $roomPriceWithExtensionsTsh = ($booking->total_price * $bookingExchangeRate) + $extensionCostTsh;
                    if ($companyPaidTsh > $roomPriceWithExtensionsTsh) {
                         // Surplus might be from another source, but for checkout display, we cap it
                         // unless we are sure about the company's exact deposit.
                    }
                    
                    // Company's outstanding balance
                    $companyOutstandingBalanceTsh = max(0, $companyBillTsh - $companyPaidTsh);
                    $companyOutstandingBalanceUsd = $companyOutstandingBalanceTsh / $bookingExchangeRate;
                    
                    // Guest's outstanding balance (only what guest is responsible for)
                    if ($paymentResponsibility === 'self') {
                        // Guest owes only their UNPAID service charges
                        $unpaidServiceRequests = $serviceRequests->filter(fn($sr) => ($sr->payment_status ?? 'pending') !== 'paid');
                        $guestOutstandingBalanceTsh = $unpaidServiceRequests->sum('total_price_tsh');
                        $guestOutstandingBalanceUsd = $guestOutstandingBalanceTsh / $bookingExchangeRate;
                    } else {
                        $guestOutstandingBalanceTsh = 0;
                        $guestOutstandingBalanceUsd = 0;
                    }
                    
                    // Total bill for display (room + services)
                    // Note: total_price already includes extensions if they were approved
                    $totalBillTsh = ($booking->total_price * $bookingExchangeRate) + $totalServiceChargesTsh;
                    $totalBillUsd = $totalBillTsh / $bookingExchangeRate;
                    
                    // Treat very small amounts (less than $0.05 or 50 TZS) as fully paid (rounding differences)
                    $minOutstandingThresholdUsd = 0.05;
                    $minOutstandingThresholdTsh = 50;
                    if ($companyOutstandingBalanceUsd < $minOutstandingThresholdUsd || $companyOutstandingBalanceTsh < $minOutstandingThresholdTsh) {
                        $companyOutstandingBalanceTsh = 0;
                        $companyOutstandingBalanceUsd = 0;
                    }
                    // Apply threshold to guest outstanding balance too
                    if ($guestOutstandingBalanceUsd < $minOutstandingThresholdUsd || $guestOutstandingBalanceTsh < $minOutstandingThresholdTsh) {
                        $guestOutstandingBalanceTsh = 0;
                        $guestOutstandingBalanceUsd = 0;
                    }

                    // Self-healing: Finalize status if checked out and balance is cleared
                    if ($companyOutstandingBalanceTsh == 0 && $guestOutstandingBalanceTsh == 0 && 
                        $booking->check_in_status === 'checked_out' && $booking->payment_status !== 'paid') {
                        $booking->update(['payment_status' => 'paid']);
                    }
                    
                    // Add to booking object for view
                    // Show guest's outstanding (only services if self-paid, 0 if company-paid)
                    $booking->outstanding_balance_tsh = $guestOutstandingBalanceTsh;
                    $booking->outstanding_balance_usd = $guestOutstandingBalanceUsd;
                    $booking->total_bill_tsh = $totalBillTsh;
                    $booking->total_bill_usd = $totalBillUsd;
                    
                    // Store company's portion separately for the view
                    $booking->company_outstanding_balance_tsh = $companyOutstandingBalanceTsh;
                    $booking->company_outstanding_balance_usd = $companyOutstandingBalanceUsd;
                    $booking->guest_outstanding_balance_tsh = $guestOutstandingBalanceTsh;
                    $booking->guest_outstanding_balance_usd = $guestOutstandingBalanceUsd;
                    
                    // Accumulate only company's outstanding balance for the group total
                    $totalOutstandingTsh += $companyOutstandingBalanceTsh;
                    $totalOutstandingUsd += $companyOutstandingBalanceUsd;
                }
                
                // Add group totals to the group array
                $group['total_outstanding_tsh'] = $totalOutstandingTsh;
                $group['total_outstanding_usd'] = $totalOutstandingUsd;
                
                return $group;
            });
            
            // Paginate grouped bookings manually
            $currentPage = \Illuminate\Pagination\LengthAwarePaginator::resolveCurrentPage();
            $perPage = 20;
            $currentItems = $groupedBookings->slice(($currentPage - 1) * $perPage, $perPage)->values();
            $bookings = new \Illuminate\Pagination\LengthAwarePaginator(
                $currentItems,
                $groupedBookings->count(),
                $perPage,
                $currentPage,
                ['path' => $request->url(), 'query' => $request->query()]
            );
        } else {
            // Individual bookings
            $query->where('is_corporate_booking', false);

            // Search functionality
            if ($request->has('search') && $request->search) {
                $search = $request->search;
                $query->where(function($q) use ($search) {
                    $q->where('booking_reference', 'like', "%{$search}%")
                      ->orWhere('guest_name', 'like', "%{$search}%")
                      ->orWhere('guest_email', 'like', "%{$search}%");
                });
            }

            // Filter by check-out date (optional - if provided)
            if ($request->has('check_out_date') && $request->check_out_date) {
                // Show bookings where check-out date is on or before the selected date
                $query->whereDate('check_out', '<=', $request->check_out_date);
            }
            // Show all checked-in bookings regardless of check-out date

            $bookings = $query->with(['room', 'serviceRequests.service'])->orderBy('check_out', 'asc')->paginate(20);
        }

        $currencyService = new CurrencyExchangeService();
        $exchangeRate = $currencyService->getUsdToTshRate();

        // Calculate outstanding balance for each booking
        if ($bookingType === 'corporate') {
            // Outstanding balances already calculated before pagination
        } else {
            // For individual bookings
            foreach ($bookings as $booking) {
                // Use locked exchange rate from booking, or fallback to current rate
                $bookingExchangeRate = $booking->locked_exchange_rate ?? $exchangeRate;
                
                // Calculate total bill (room + services + extensions)
                $serviceRequests = $booking->serviceRequests()
                    ->whereIn('status', ['pending', 'approved', 'preparing', 'completed'])
                    ->with('service')
                    ->get();
                
                $totalServiceChargesTsh = $serviceRequests->sum('total_price_tsh');
                
                // Calculate extension cost if extension was approved
                $extensionCostUsd = 0;
                if ($booking->extension_status === 'approved' && $booking->original_check_out && $booking->extension_requested_to) {
                    $originalCheckOut = \Carbon\Carbon::parse($booking->original_check_out);
                    $requestedCheckOut = \Carbon\Carbon::parse($booking->extension_requested_to);
                    $extensionNights = $originalCheckOut->diffInDays($requestedCheckOut);
                    if ($extensionNights > 0 && $booking->room) {
                        $extensionCostUsd = $booking->room->price_per_night * $extensionNights;
                    }
                }
                $extensionCostTsh = $extensionCostUsd * $bookingExchangeRate;
                
                // Total bill
                // Note: extensionCostTsh is already included in booking->total_price
                $totalBillTsh = ($booking->total_price * $bookingExchangeRate) + $totalServiceChargesTsh;
                
                // Amount paid (Booking deposit + any settled service payments)
                $amountPaidTsh = ($booking->amount_paid ?? 0) * $bookingExchangeRate;
                
                // Add payments for completed/paid services to show correct outstanding balance
                foreach ($serviceRequests as $sr) {
                    if ($sr->payment_status === 'paid') {
                        $amountPaidTsh += $sr->total_price_tsh;
                    }
                }
                
                // Outstanding balance
                $outstandingBalanceTsh = max(0, $totalBillTsh - $amountPaidTsh);
                $outstandingBalanceUsd = $outstandingBalanceTsh / $bookingExchangeRate;
                
                // Treat very small amounts (less than $0.05 or 50 TZS) as fully paid (rounding differences)
                $minOutstandingThresholdUsd = 0.05;
                $minOutstandingThresholdTsh = 50;
                if ($outstandingBalanceUsd < $minOutstandingThresholdUsd || $outstandingBalanceTsh < $minOutstandingThresholdTsh) {
                    $outstandingBalanceTsh = 0;
                    $outstandingBalanceUsd = 0;
                    
                    // Self-healing: Finalize status if checked out and balance is cleared
                    if ($booking->check_in_status === 'checked_out' && $booking->payment_status !== 'paid') {
                        $booking->update(['payment_status' => 'paid']);
                        // Refresh booking to get updated status for display
                        $booking->refresh();
                    }
                }
                
                // Add to booking object for view
                $booking->outstanding_balance_tsh = $outstandingBalanceTsh;
                $booking->outstanding_balance_usd = $outstandingBalanceUsd;
                $booking->total_bill_tsh = $totalBillTsh;
                $booking->total_bill_usd = $totalBillTsh / $bookingExchangeRate;
            }
        }

        // Calculate statistics
        $stats = [
            'individual_total' => Booking::where('is_corporate_booking', false)
                ->where(function($q) {
                    $q->where('check_in_status', 'checked_in')
                      ->orWhere(function($q2) {
                          $q2->where('check_in_status', 'checked_out')
                             ->where('payment_status', '!=', 'paid');
                      });
                })
                ->count(),
            'corporate_total' => Booking::where('is_corporate_booking', true)
                ->where(function($q) {
                    $q->where('check_in_status', 'checked_in')
                      ->orWhere(function($q2) {
                          $q2->where('check_in_status', 'checked_out')
                             ->where('payment_status', '!=', 'paid');
                      });
                })
                ->distinct('company_id')
                ->count('company_id'),
        ];

        $role = $this->getRole();
        return view('dashboard.reception-check-out', [
            'role' => $role,
            'userName' => auth()->user()->name ?? ($role === 'manager' ? 'Manager' : 'Reception Staff'),
            'userRole' => $role === 'manager' ? 'Manager' : 'Reception',
            'bookings' => $bookings,
            'bookingType' => $bookingType,
            'exchangeRate' => $exchangeRate,
            'filters' => $request->only(['search', 'check_out_date']),
            'stats' => $stats,
        ]);
    }

    /**
     * Show active reservations
     */
    public function activeReservations(Request $request)
    {
        $query = Booking::with(['room', 'serviceRequests.service'])
            ->where('status', 'confirmed')
            ->whereIn('payment_status', ['paid', 'partial'])
            ->where(function($q) {
                // Include paid bookings
                $q->where('payment_status', 'paid')
                  // Or partial payments where amount_paid > 0
                  ->orWhere(function($subQ) {
                      $subQ->where('payment_status', 'partial')
                           ->whereNotNull('amount_paid')
                           ->where('amount_paid', '>', 0);
                  });
            })
            ->where('check_in_status', '!=', 'checked_out');

        // Search functionality
        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('booking_reference', 'like', "%{$search}%")
                  ->orWhere('guest_name', 'like', "%{$search}%")
                  ->orWhere('guest_email', 'like', "%{$search}%");
            });
        }

        $bookings = $query->orderBy('check_in', 'asc')->paginate(20);

        $currencyService = new CurrencyExchangeService();
        $exchangeRate = $currencyService->getUsdToTshRate();

        $role = $this->getRole();
        return view('dashboard.reception-active-reservations', [
            'role' => $role,
            'userName' => auth()->user()->name ?? ($role === 'manager' ? 'Manager' : 'Reception Staff'),
            'userRole' => $role === 'manager' ? 'Manager' : 'Reception',
            'bookings' => $bookings,
            'exchangeRate' => $exchangeRate,
            'filters' => $request->only(['search']),
        ]);
    }

    /**
     * Show guests list
     */
    public function guests(Request $request)
    {
        $query = Booking::where('booking_reference', 'NOT LIKE', 'INV%')
            ->where('booking_reference', 'NOT LIKE', 'CINV%')
            ->select('guest_name', 'guest_email', 'guest_phone', 'country', 'country_code')
            ->selectRaw('MAX(created_at) as last_booking')
            ->selectRaw('COUNT(*) as total_bookings')
            ->groupBy('guest_email', 'guest_name', 'guest_phone', 'country', 'country_code');

        // Search functionality
        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('guest_name', 'like', "%{$search}%")
                  ->orWhere('guest_email', 'like', "%{$search}%")
                  ->orWhere('guest_phone', 'like', "%{$search}%");
            });
        }

        $guests = $query->orderBy('last_booking', 'desc')->paginate(20);

        $role = $this->getRole();
        return view('dashboard.reception-guests', [
            'role' => $role,
            'userName' => auth()->user()->name ?? ($role === 'manager' ? 'Manager' : 'Reception Staff'),
            'userRole' => $role === 'manager' ? 'Manager' : 'Reception',
            'guests' => $guests,
            'filters' => $request->only(['search']),
        ]);
    }

    /**
     * Show room status
     */
    public function roomStatus(Request $request)
    {
        $now = now();
        $today = $now->copy()->startOfDay();

        // Self-healing: Reset any rooms whose manual status has expired
        Room::whereNotNull('status_until')
            ->where('status_until', '<=', $now)
            ->update([
                'status' => 'to_be_cleaned', // Or available? Usually rooms need checking/cleaning after being "closed" or "occupied"
                'status_until' => null
            ]);
        
        // Load all bookings (confirmed paid/partial, and pending bookings for future dates)
        $rooms = Room::with(['bookings' => function($query) use ($today) {
            $query->with(['serviceRequests.service'])
                  ->where(function($q) use ($today) {
                // Confirmed and paid/partial bookings for current/upcoming dates
                $q->where(function($subQ) use ($today) {
                    $subQ->where('status', 'confirmed')
                          ->whereIn('payment_status', ['paid', 'partial'])
                          ->where(function($paymentQ) {
                              // Include paid bookings
                              $paymentQ->where('payment_status', 'paid')
                                      // Or partial payments where amount_paid > 0
                                      ->orWhere(function($partialQ) {
                                          $partialQ->where('payment_status', 'partial')
                                                   ->whereNotNull('amount_paid')
                                                   ->where('amount_paid', '>', 0);
                                      });
                          })
                          ->where('check_in_status', '!=', 'checked_out')
                          ->whereDate('check_in', '<=', $today)
                          ->whereDate('check_out', '>=', $today);
                })
                // OR pending bookings (waiting for payment) for future dates
                ->orWhere(function($subQ) use ($today) {
                    $subQ->where('status', 'pending')
                          ->where('payment_status', 'pending')
                          ->whereDate('check_in', '>=', $today)
                          ->whereNull('cancelled_at');
                })
                // OR confirmed paid/partial bookings for future dates (upcoming check-ins)
                ->orWhere(function($subQ) use ($today) {
                    $subQ->where('status', 'confirmed')
                          ->whereIn('payment_status', ['paid', 'partial'])
                          ->where(function($paymentQ) {
                              // Include paid bookings
                              $paymentQ->where('payment_status', 'paid')
                                      // Or partial payments where amount_paid > 0
                                      ->orWhere(function($partialQ) {
                                          $partialQ->where('payment_status', 'partial')
                                                   ->whereNotNull('amount_paid')
                                                   ->where('amount_paid', '>', 0);
                                      });
                          })
                          ->where('check_in_status', 'pending')
                          ->whereDate('check_in', '>=', $today);
                });
            });
        }])->orderBy('room_number')->get();

        // Calculate room status
        $rooms = $rooms->map(function($room) use ($today) {
            // Active bookings (checked in AND today is between check-in and check-out dates)
            $activeBookings = $room->bookings->filter(function($booking) use ($today) {
                if ($booking->check_in_status !== 'checked_in') {
                    return false;
                }
                $checkIn = Carbon::parse($booking->check_in);
                $checkOut = Carbon::parse($booking->check_out);
                // Room is occupied only if today is between check-in and check-out dates
                return $today->gte($checkIn) && $today->lte($checkOut);
            });
            $room->is_occupied = $activeBookings->count() > 0 || $room->status === 'occupied';
            $room->current_booking = $activeBookings->first();
            
            // Upcoming bookings (future check-ins or pending payment bookings)
            $upcomingBookings = $room->bookings->filter(function($booking) use ($today) {
                $checkInDate = \Carbon\Carbon::parse($booking->check_in);
                return $checkInDate->gte($today) && 
                       ($booking->check_in_status === 'pending' || 
                        ($booking->status === 'pending' && $booking->payment_status === 'pending'));
            })->sortBy('check_in')->first();
            $room->upcoming_checkin = $upcomingBookings;
            
            // Pending payment booking (for status display) - only show if check-in is within 3 days
            $pendingPaymentBooking = $room->bookings->filter(function($booking) use ($today) {
                $checkInDate = \Carbon\Carbon::parse($booking->check_in);
                $daysUntilCheckIn = $today->diffInDays($checkInDate, false);
                return $booking->status === 'pending' && 
                       $booking->payment_status === 'pending' &&
                       $checkInDate->gte($today) &&
                       $daysUntilCheckIn <= 3 && // Only show if check-in is within 3 days
                       is_null($booking->cancelled_at);
            })->sortBy('check_in')->first();
            $room->pending_payment_booking = $pendingPaymentBooking;
            
            // Check if room has any bookings that affect current availability (today or within next 3 days)
            $room->has_immediate_booking = $room->status === 'reserved';
            if ($room->upcoming_checkin) {
                $checkInDate = \Carbon\Carbon::parse($room->upcoming_checkin->check_in);
                $daysUntilCheckIn = $today->diffInDays($checkInDate, false);
                // Only mark as having immediate booking if check-in is today or within 3 days
                if ($daysUntilCheckIn <= 3) {
                    $room->has_immediate_booking = true;
                }
            }
            
            // Get last checked out booking for rooms that need cleaning
            $room->last_checked_out_booking = $room->bookings()
                ->where('check_in_status', 'checked_out')
                ->orderBy('checked_out_at', 'desc')
                ->first();
            
            return $room;
        });

        // Calculate statistics
        $stats = [
            'total' => $rooms->count(),
            'available' => $rooms->filter(function($room) {
                // Room is available if not occupied, doesn't have immediate bookings, AND not in maintenance or needing cleaning
                return !in_array($room->status, ['maintenance', 'to_be_cleaned']) && 
                       !$room->is_occupied && 
                       !$room->has_immediate_booking;
            })->count(),
            'occupied' => $rooms->filter(function($room) {
                return $room->is_occupied;
            })->count(),
            'needs_cleaning' => $rooms->filter(function($room) {
                return $room->status === 'to_be_cleaned';
            })->count(),
            'maintenance' => $rooms->filter(function($room) {
                return $room->status === 'maintenance';
            })->count(),
            'reserved' => $rooms->filter(function($room) {
                // Reserved if has immediate booking (within 3 days) and not occupied
                return $room->has_immediate_booking && !$room->is_occupied;
            })->count(),
            'waiting_payment' => $rooms->filter(function($room) {
                // Waiting for payment if has immediate booking with pending payment
                return $room->has_immediate_booking && 
                       (($room->pending_payment_booking) || 
                        ($room->upcoming_checkin && $room->upcoming_checkin->payment_status === 'pending'));
            })->count(),
        ];

        // Get rooms with check-out today or overdue
        $roomsWithUrgentCheckout = $rooms->filter(function($room) use ($today) {
            if ($room->current_booking) {
                $checkOutDate = \Carbon\Carbon::parse($room->current_booking->check_out);
                return $checkOutDate->lte($today);
            }
            return false;
        })->pluck('id')->toArray();

        // Get rooms with upcoming check-ins (next 24 hours)
        $tomorrow = \Carbon\Carbon::today()->addDay();
        $roomsWithUpcomingCheckin = $rooms->filter(function($room) use ($today, $tomorrow) {
            if ($room->upcoming_checkin) {
                $checkInDate = \Carbon\Carbon::parse($room->upcoming_checkin->check_in);
                return $checkInDate->between($today, $tomorrow);
            }
            return false;
        })->pluck('id')->toArray();

        $currencyService = new CurrencyExchangeService();
        $exchangeRate = $currencyService->getUsdToTshRate();

        $role = $this->getRole();
        return view('dashboard.reception-room-status', [
            'role' => $role,
            'userName' => auth()->user()->name ?? ($role === 'manager' ? 'Manager' : 'Reception Staff'),
            'userRole' => $role === 'manager' ? 'Manager' : 'Reception',
            'rooms' => $rooms,
            'exchangeRate' => $exchangeRate,
            'stats' => $stats,
            'roomsWithUrgentCheckout' => $roomsWithUrgentCheckout,
            'roomsWithUpcomingCheckin' => $roomsWithUpcomingCheckin,
        ]);
    }

    /**
     * Show rooms that need cleaning
     * Includes: rooms with status 'to_be_cleaned' AND rooms with check-out tomorrow (1 day before)
     */
    public function roomsNeedsCleaning(Request $request)
    {
        $today = Carbon::today();
        $tomorrow = Carbon::today()->addDay();
        
        // Get rooms that need cleaning:
        // 1. Rooms with status 'to_be_cleaned' (already checked out)
        // 2. Rooms with bookings checking out tomorrow (1 day before check-out)
        $query = Room::where(function($q) use ($tomorrow) {
            // Rooms already marked as needing cleaning
            $q->where('status', 'to_be_cleaned')
              // OR rooms with bookings checking out tomorrow
              ->orWhereHas('bookings', function($bookingQuery) use ($tomorrow) {
                  $bookingQuery->where('status', 'confirmed')
                               ->whereIn('payment_status', ['paid', 'partial'])
                               ->where(function($paymentQ) {
                                   $paymentQ->where('payment_status', 'paid')
                                           ->orWhere(function($partialQ) {
                                               $partialQ->where('payment_status', 'partial')
                                                        ->whereNotNull('amount_paid')
                                                        ->where('amount_paid', '>', 0);
                                           });
                               })
                               ->where('check_in_status', 'checked_in')
                               ->whereDate('check_out', $tomorrow);
              });
        })
        ->with(['bookings' => function($q) use ($tomorrow) {
            $q->where(function($bookingQ) use ($tomorrow) {
                // Get checked out bookings (for rooms already cleaned)
                $bookingQ->where('check_in_status', 'checked_out')
                         ->orderBy('checked_out_at', 'desc')
                         ->limit(1);
            })
            ->orWhere(function($tomorrowQ) use ($tomorrow) {
                // Get bookings checking out tomorrow
                $tomorrowQ->where('status', 'confirmed')
                         ->whereIn('payment_status', ['paid', 'partial'])
                         ->where(function($paymentQ) {
                             $paymentQ->where('payment_status', 'paid')
                                     ->orWhere(function($partialQ) {
                                         $partialQ->where('payment_status', 'partial')
                                                  ->whereNotNull('amount_paid')
                                                  ->where('amount_paid', '>', 0);
                                     });
                         })
                         ->where('check_in_status', 'checked_in')
                         ->whereDate('check_out', $tomorrow);
            });
        }])
        ->orderBy('room_number', 'asc');

        // Search functionality
        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('room_number', 'like', "%{$search}%")
                  ->orWhere('room_type', 'like', "%{$search}%");
            });
        }

        $rooms = $query->paginate(20);

        $currencyService = new CurrencyExchangeService();
        $exchangeRate = $currencyService->getUsdToTshRate();

        $role = $this->getRole();
        return view('dashboard.reception-rooms-cleaning', [
            'role' => $role,
            'userName' => auth()->user()->name ?? ($role === 'manager' ? 'Manager' : 'Reception Staff'),
            'userRole' => $role === 'manager' ? 'Manager' : 'Reception',
            'rooms' => $rooms,
            'exchangeRate' => $exchangeRate,
            'filters' => $request->only(['search']),
        ]);
    }

    /**
     * Mark room as cleaned (available)
     */
    public function markRoomCleaned(Room $room)
    {
        $room->update(['status' => 'available', 'status_until' => null]); // Important to clear status_until too

        return response()->json([
            'success' => true,
            'message' => 'Room marked as cleaned and available for booking.',
            'room' => $room->fresh(),
        ]);
    }

    /**
     * Update room status manually (manual occupation, reservation, or closing)
     */
    public function updateRoomManualStatus(Request $request, Room $room)
    {
        $status = $request->status;
        $statusUntil = $request->status_until;

        // Reset status_until if making room available
        if ($status === 'available') {
            $statusUntil = null;
        }

        $room->update([
            'status' => $status,
            'status_until' => $statusUntil,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Room status updated successfully.',
            'room' => $room->fresh(),
        ]);
    }

    /**
     * Show checkout payment page
     */
    public function checkoutPayment(Booking $booking)
    {
        // Verify booking is checked out
        if ($booking->check_in_status !== 'checked_out') {
            abort(404, 'Booking not found or not checked out.');
        }

        // Calculate additional charges only (room booking already paid via PayPal)
        // Additional charges include: services, extensions, transportation
        
        $serviceRequests = $booking->serviceRequests()
            ->whereIn('status', ['pending', 'approved', 'preparing', 'completed'])
            ->with('service')
            ->get();

        $currencyService = new CurrencyExchangeService();
        $exchangeRate = $currencyService->getUsdToTshRate();

        // Calculate extension cost if extension was approved
        $extensionCostUsd = 0;
        $extensionCostTsh = 0;
        $extensionNights = 0;
        
        if ($booking->extension_status === 'approved' && $booking->original_check_out && $booking->extension_requested_to) {
            $originalCheckOut = \Carbon\Carbon::parse($booking->original_check_out);
            $requestedCheckOut = \Carbon\Carbon::parse($booking->extension_requested_to);
            $extensionNights = $originalCheckOut->diffInDays($requestedCheckOut);
            
            if ($extensionNights > 0 && $booking->room) {
                $extensionCostUsd = $booking->room->price_per_night * $extensionNights;
                $extensionCostTsh = $extensionCostUsd * $exchangeRate;
            }
        }

        // Calculate transportation charges (only if there's a service request)
        $transportationChargesTsh = 0;
        $transportationChargesUsd = 0;
        if ($booking->airport_pickup_required) {
            // Check if there's a service request for airport pickup
            $airportPickupService = $serviceRequests->firstWhere('service.category', 'transport');
            if ($airportPickupService) {
                $transportationChargesTsh = $airportPickupService->total_price_tsh;
                $transportationChargesUsd = $transportationChargesTsh / $exchangeRate;
            }
            // Note: If airport_pickup_required is true but no service request exists,
            // we don't charge for it (it might have been handled separately or already paid)
        }

        // Calculate room balance (Target Price - Paid Price)
    $roomBalanceUsd = max(0, ($booking->total_price ?? 0) - ($booking->amount_paid ?? 0));
    $roomBalanceTsh = $roomBalanceUsd * $exchangeRate;

    // Service charges (unpaid only)
    $unpaidServiceRequests = $serviceRequests->filter(fn($sr) => ($sr->payment_status ?? 'pending') !== 'paid');
    $totalServiceBalanceTsh = $unpaidServiceRequests->sum('total_price_tsh');
    
    // Total to pay at checkout
    if ($booking->is_corporate_booking) {
        if ($booking->payment_responsibility === 'self') {
            // Guest pays only for their services
            $totalAdditionalChargesTsh = $totalServiceBalanceTsh;
        } else {
            // Company pays everything (Room + Extensions + Services)
            $totalAdditionalChargesTsh = 0; 
        }
    } else {
        // Individual booking - guest pays for everything remaining
        $totalAdditionalChargesTsh = $roomBalanceTsh + $totalServiceBalanceTsh;
    }
    $totalAdditionalChargesUsd = $totalAdditionalChargesTsh / $exchangeRate;


        $role = $this->getRole();
        return view('dashboard.reception-checkout-payment', [
            'role' => $role,
            'userName' => auth()->user()->name ?? ($role === 'manager' ? 'Manager' : 'Reception Staff'),
            'userRole' => $role === 'manager' ? 'Manager' : 'Reception',
            'booking' => $booking->load('room'),
            'serviceRequests' => $serviceRequests,
            'extensionCostUsd' => $extensionCostUsd,
            'extensionCostTsh' => $extensionCostTsh,
            'extensionNights' => $extensionNights,
            'transportationChargesUsd' => $transportationChargesUsd,
            'transportationChargesTsh' => $transportationChargesTsh,
            'otherServiceChargesTsh' => $totalServiceBalanceTsh, // Assuming this was intended to be totalServiceBalanceTsh
            'roomBalanceTsh' => $roomBalanceTsh ?? 0,
            'totalAdditionalChargesTsh' => $totalAdditionalChargesTsh,
            'totalAdditionalChargesUsd' => $totalAdditionalChargesUsd,
            'exchangeRate' => $exchangeRate,
        ]);
    }

    /**
     * Process cash payment for outstanding balance
     */
    public function processCashPayment(Request $request, Booking $booking)
    {
        $request->validate([
            'amount' => 'required|numeric|min:0.01',
            'payment_method' => 'required|in:cash,mobile,bank,card',
            'payment_provider' => 'nullable|string|max:100',
            'payment_reference' => 'nullable|string|max:100',
        ]);

        // Calculate outstanding balance
        $currencyService = new CurrencyExchangeService();
        $exchangeRate = $booking->locked_exchange_rate ?? $currencyService->getUsdToTshRate();
        
        // Check if this is a corporate booking
        $isCorporate = $booking->is_corporate_booking ?? false;
        $paymentResponsibility = $booking->payment_responsibility ?? 'self';
        
        $serviceRequests = $booking->serviceRequests()
            ->whereIn('status', ['pending', 'approved', 'preparing', 'completed'])
            ->with('service')
            ->get();
        
        $totalServiceChargesTsh = $serviceRequests->sum('total_price_tsh');
        
        // Calculate extension cost if extension was approved
        $extensionCostUsd = 0;
        if ($booking->extension_status === 'approved' && $booking->original_check_out && $booking->extension_requested_to) {
            $originalCheckOut = \Carbon\Carbon::parse($booking->original_check_out);
            $requestedCheckOut = \Carbon\Carbon::parse($booking->extension_requested_to);
            $extensionNights = $originalCheckOut->diffInDays($requestedCheckOut);
            if ($extensionNights > 0 && $booking->room) {
                $extensionCostUsd = $booking->room->price_per_night * $extensionNights;
            }
        }
        $extensionCostTsh = $extensionCostUsd * $exchangeRate;
        
        // Calculate total bill for the whole booking (Room + Services)
        // Note: extensionCostTsh is already included in booking->total_price
        $totalBookingBillTsh = ($booking->total_price * $exchangeRate) + $totalServiceChargesTsh;
        
        // Calculate outstanding balance for THIS SPECIFIC payment action
        if ($isCorporate) {
            if ($paymentResponsibility === 'self') {
                // For a self-paying corporate guest, they only owe for their UNPAID services.
                // We track their debt based on the total of unpaid requests.
                $unpaidServiceRequests = $serviceRequests->filter(fn($sr) => ($sr->payment_status ?? 'pending') !== 'paid');
                $outstandingBalanceTsh = $unpaidServiceRequests->sum('total_price_tsh');
            } else {
                // If company pays everything, guest owes 0 at reception.
                $outstandingBalanceTsh = 0;
            }
        } else {
            // Individual booking - they owe everything
            $amountPaidTsh = ($booking->amount_paid ?? 0) * $exchangeRate;
            $outstandingBalanceTsh = max(0, $totalBookingBillTsh - $amountPaidTsh);
        }
        
        $outstandingBalanceUsd = $outstandingBalanceTsh / $exchangeRate;
        $paymentAmountUsd = (float) $request->amount;
        $paymentAmountTsh = $paymentAmountUsd * $exchangeRate;
        
        // Check if this payment covers the remainder of the guest's debt
        $isGuestPortionCleared = ($paymentAmountTsh >= $outstandingBalanceTsh - 50);
        
        // Update money
        $newAmountPaidUsd = ($booking->amount_paid ?? 0) + $paymentAmountUsd;
        $newAmountPaidTsh = $newAmountPaidUsd * $exchangeRate;
        
        // Calculate remaining balance after this payment
        $remainingBalanceTsh = max(0, $outstandingBalanceTsh - $paymentAmountTsh);
        $remainingBalanceUsd = $remainingBalanceTsh / $exchangeRate;
        
        // Threshold for considering fully paid (50 TZS or $0.05)
        $minOutstandingThresholdUsd = 0.05;
        $minOutstandingThresholdTsh = 50;
        
        // --- Guest Portion Logic (Corporate Self-Payers) ---
        $isGuestPortionCleared = false;
        if ($isCorporate && $paymentResponsibility === 'self') {
            $isGuestPortionCleared = ($remainingBalanceTsh < $minOutstandingThresholdTsh);
        }
        
        // Mark services as paid if this payout covers them
        if ($isCorporate && $paymentResponsibility === 'self' && $isGuestPortionCleared) {
            foreach($serviceRequests as $sr) {
                if (($sr->payment_status ?? 'pending') !== 'paid') {
                    $sr->update([
                        'payment_status' => 'paid',
                        'payment_method' => $request->payment_method,
                        'completed_at' => $sr->completed_at ?? now()
                    ]);
                }
            }
        }
        
        // --- Overall Booking Payment Status (Room + Services) ---
        $totalBillTsh = ($booking->total_price * $exchangeRate) + $totalServiceChargesTsh;
        $newTotalPaidTsh = $newAmountPaidUsd * $exchangeRate;
        $overallRemainingTsh = max(0, $totalBillTsh - $newTotalPaidTsh);
        
        $isOverallFullyPaid = ($overallRemainingTsh < $minOutstandingThresholdTsh);
        
        $finalPaymentStatus = 'partial';
        if ($isOverallFullyPaid) {
            $finalPaymentStatus = 'paid';
        }
        
        // If checked out and balance is cleared, ensure we record it as paid
        if ($booking->check_in_status === 'checked_out' && $isOverallFullyPaid) {
            $finalPaymentStatus = 'paid';
        }
        
        $booking->update([
            'payment_status' => $finalPaymentStatus,
            'payment_method' => $request->payment_method,
            'payment_provider' => $request->payment_provider ?? null,
            'payment_transaction_id' => $request->payment_reference ?? null,
            'amount_paid' => $newAmountPaidUsd,
            'paid_at' => $booking->paid_at ?? now(),
            'total_service_charges_tsh' => $totalServiceChargesTsh,
        ]);
        
        // Only deactivate guest account if they have checked out
        // If still checked in, keep account active so they can access dashboard and services
        if ($booking->check_in_status === 'checked_out') {
            $user = \App\Models\Guest::where('email', $booking->guest_email)->first();
            if ($user) {
                $user->update(['is_active' => false]);
            }
        }
        
        return response()->json([
            'success' => true,
            'message' => 'Payment received successfully. Guest can now check out.',
            'redirect' => route('reception.reservations.check-out'),
        ]);
    }

    /**
     * Process checkout payment
     */
    public function processCheckoutPayment(Request $request, Booking $booking)
    {
        $request->validate([
            'payment_method' => 'required|in:paypal,cash',
        ]);

        // Verify booking is checked out
        if ($booking->check_in_status !== 'checked_out') {
            return response()->json([
                'success' => false,
                'message' => 'Booking is not checked out.',
            ], 400);
        }

        // Calculate additional charges only (room booking already paid)
        $serviceRequests = $booking->serviceRequests()
            ->whereIn('status', ['pending', 'approved', 'preparing', 'completed'])
            ->with('service')
            ->get();

        // Use locked exchange rate from booking, or fallback to current rate if not set (for old bookings)
    $exchangeRate = $booking->locked_exchange_rate ?? (new CurrencyExchangeService())->getUsdToTshRate();
    
    // Calculate total bill for the whole booking (Room + Services)
    // Note: total_price already includes any approved extensions
    $totalServiceChargesTsh = $serviceRequests->sum('total_price_tsh');
    $totalBookingBillTsh = ($booking->total_price * $exchangeRate) + $totalServiceChargesTsh;
    
    // Calculate outstanding balance
    if ($booking->is_corporate_booking) {
        if ($booking->payment_responsibility === 'self') {
            // For a self-paying corporate guest, they only owe for their UNPAID services.
            $unpaidServiceRequests = $serviceRequests->filter(fn($sr) => ($sr->payment_status ?? 'pending') !== 'paid');
            $outstandingBalanceTsh = $unpaidServiceRequests->sum('total_price_tsh');
        } else {
            // If company pays everything, guest owes 0 at reception.
            $outstandingBalanceTsh = 0;
        }
    } else {
        // Individual booking - they owe everything
        $amountPaidTsh = ($booking->amount_paid ?? 0) * $exchangeRate;
        $outstandingBalanceTsh = max(0, $totalBookingBillTsh - $amountPaidTsh);
    }
    
    $outstandingBalanceUsd = $outstandingBalanceTsh / $exchangeRate;

    if ($request->payment_method === 'cash') {
        // Mark services as paid if we are settling them
        if ($outstandingBalanceTsh > 0) {
            foreach($serviceRequests as $sr) {
                if ($sr->payment_status !== 'paid') {
                    $sr->update([
                        'payment_status' => 'paid',
                        'payment_method' => 'cash',
                        'completed_at' => now(),
                        'paid_to' => Auth::guard('staff')->id()
                    ]);
                }
            }
        }
        
        // Update booking
        $booking->update([
            'payment_status' => ($outstandingBalanceTsh <= 50) ? 'paid' : 'partial', 
            'payment_method' => $booking->payment_method ?? 'cash',
            'amount_paid' => ($booking->amount_paid ?? 0) + $outstandingBalanceUsd,
            'paid_at' => now(),
            'total_service_charges_tsh' => $totalServiceChargesTsh,
        ]);

        // Deactivate guest account
        $user = \App\Models\Guest::where('email', $booking->guest_email)->first();
        if ($user) {
            $user->update(['is_active' => false]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Additional charges paid successfully. Guest account has been deactivated.',
            'redirect' => route('reception.reservations.check-out'),
        ]);
    } else {
        // This shouldn't happen as we only allow cash now, but handle it
        return response()->json([
            'success' => false,
            'message' => 'Invalid payment method. Please use cash payment.',
        ], 400);
    }
}

    /**
     * Show payments
     */
    public function payments(Request $request)
    {
        // Include both paid and partial payments
        $query = Booking::with('room')
            ->whereIn('payment_status', ['paid', 'partial'])
            ->whereNotNull('amount_paid')
            ->where('amount_paid', '>', 0)
            ->orderByRaw('COALESCE(paid_at, created_at) DESC');

        // Filter by payment status
        if ($request->has('payment_status') && $request->payment_status && $request->payment_status !== 'all') {
            $query->where('payment_status', $request->payment_status);
        }

        // Filter by date range (use paid_at if available, otherwise created_at)
        if ($request->has('date_from') && $request->date_from) {
            $query->where(function($q) use ($request) {
                $q->where(function($subQ) use ($request) {
                    $subQ->whereNotNull('paid_at')
                         ->whereDate('paid_at', '>=', $request->date_from);
                })->orWhere(function($subQ) use ($request) {
                    $subQ->whereNull('paid_at')
                         ->whereDate('created_at', '>=', $request->date_from);
                });
            });
        }
        if ($request->has('date_to') && $request->date_to) {
            $query->where(function($q) use ($request) {
                $q->where(function($subQ) use ($request) {
                    $subQ->whereNotNull('paid_at')
                         ->whereDate('paid_at', '<=', $request->date_to);
                })->orWhere(function($subQ) use ($request) {
                    $subQ->whereNull('paid_at')
                         ->whereDate('created_at', '<=', $request->date_to);
                });
            });
        }

        // Search functionality
        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('booking_reference', 'like', "%{$search}%")
                  ->orWhere('guest_name', 'like', "%{$search}%")
                  ->orWhere('guest_email', 'like', "%{$search}%")
                  ->orWhere('payment_transaction_id', 'like', "%{$search}%");
            });
        }

        $payments = $query->paginate(20);

        // Statistics (include partial payments)
        $today = Carbon::today();
        $stats = [
            'total_paid' => Booking::whereIn('payment_status', ['paid', 'partial'])
                ->whereNotNull('amount_paid')
                ->where('amount_paid', '>', 0)
                ->sum('amount_paid'),
            'total_paid_today' => Booking::whereIn('payment_status', ['paid', 'partial'])
                ->whereNotNull('amount_paid')
                ->where('amount_paid', '>', 0)
                ->where(function($q) use ($today) {
                    $q->where(function($subQ) use ($today) {
                        $subQ->whereNotNull('paid_at')
                             ->whereDate('paid_at', $today);
                    })->orWhere(function($subQ) use ($today) {
                        $subQ->whereNull('paid_at')
                             ->whereDate('created_at', $today);
                    });
                })
                ->sum('amount_paid'),
            'total_payments_today' => Booking::whereIn('payment_status', ['paid', 'partial'])
                ->whereNotNull('amount_paid')
                ->where('amount_paid', '>', 0)
                ->where(function($q) use ($today) {
                    $q->where(function($subQ) use ($today) {
                        $subQ->whereNotNull('paid_at')
                             ->whereDate('paid_at', $today);
                    })->orWhere(function($subQ) use ($today) {
                        $subQ->whereNull('paid_at')
                             ->whereDate('created_at', $today);
                    });
                })
                ->count(),
        ];

        $currencyService = new CurrencyExchangeService();
        $exchangeRate = $currencyService->getUsdToTshRate();

        $role = $this->getRole();
        return view('dashboard.reception-payments', [
            'role' => $role,
            'userName' => auth()->user()->name ?? ($role === 'manager' ? 'Manager' : 'Reception Staff'),
            'userRole' => $role === 'manager' ? 'Manager' : 'Reception',
            'payments' => $payments,
            'exchangeRate' => $exchangeRate,
            'stats' => $stats,
            'filters' => $request->only(['payment_status', 'date_from', 'date_to', 'search']),
        ]);
    }

    /**
     * Show daily reports
     */
    /**
     * Show extension requests page
     */
    public function extensionRequests(Request $request)
    {
        $query = Booking::with('room')
            ->whereNotNull('extension_status')
            ->orderBy('extension_requested_at', 'desc');

        // Filter by extension status
        if ($request->has('status') && $request->status) {
            $query->where('extension_status', $request->status);
        }

        // Search functionality
        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('booking_reference', 'like', "%{$search}%")
                  ->orWhere('guest_name', 'like', "%{$search}%")
                  ->orWhere('guest_email', 'like', "%{$search}%");
            });
        }

        $extensions = $query->paginate(20);

        // Statistics
        $stats = [
            'pending' => Booking::where('extension_status', 'pending')->count(),
            'approved' => Booking::where('extension_status', 'approved')->count(),
            'rejected' => Booking::where('extension_status', 'rejected')->count(),
            'total' => Booking::whereNotNull('extension_status')->count(),
        ];

        $currencyService = new CurrencyExchangeService();
        $exchangeRate = $currencyService->getUsdToTshRate();

        $role = $this->getRole();
        return view('dashboard.reception-extension-requests', [
            'role' => $role,
            'userName' => auth()->user()->name ?? ($role === 'manager' ? 'Manager' : 'Reception Staff'),
            'userRole' => $role === 'manager' ? 'Manager' : 'Reception',
            'extensions' => $extensions,
            'exchangeRate' => $exchangeRate,
            'stats' => $stats,
            'filters' => $request->only(['status', 'search']),
        ]);
    }

    public function reports(Request $request)
    {
        $reportType = $request->get('report_type', 'daily');
        $reportDate = $request->get('date', today()->format('Y-m-d'));
        $startDate = $request->get('start_date');
        $endDate = $request->get('end_date');
        $download = $request->get('download');
        
        // Calculate date range based on report type
        $dateRange = $this->calculateDateRange($reportType, $reportDate, $startDate, $endDate);
        $start = $dateRange['start'];
        $end = $dateRange['end'];
        
        // Get bookings statistics for the period
        $bookings = [
            'checked_in' => Booking::whereBetween('checked_in_at', [$start, $end])->count(),
            'checked_out' => Booking::whereBetween('checked_out_at', [$start, $end])->count(),
            'new_bookings' => Booking::whereBetween('created_at', [$start, $end])->count(),
            'confirmed' => Booking::whereBetween('created_at', [$start, $end])
                ->where('status', 'confirmed')
                ->count(),
        ];

        // Get payments statistics (include both paid and partial payments)
        $paymentsQuery = Booking::whereIn('payment_status', ['paid', 'partial'])
            ->whereNotNull('amount_paid')
            ->where('amount_paid', '>', 0)
            ->where(function($q) use ($start, $end) {
                // Use paid_at if available, otherwise created_at
                $q->where(function($subQ) use ($start, $end) {
                    $subQ->whereNotNull('paid_at')
                         ->whereDate('paid_at', '>=', $start->format('Y-m-d'))
                         ->whereDate('paid_at', '<=', $end->format('Y-m-d'));
                })->orWhere(function($subQ) use ($start, $end) {
                    $subQ->whereNull('paid_at')
                         ->whereDate('created_at', '>=', $start->format('Y-m-d'))
                         ->whereDate('created_at', '<=', $end->format('Y-m-d'));
                });
            });
        
        $payments = [
            'total_revenue' => $paymentsQuery->get()->sum(function($booking) {
                return $booking->amount_paid ?? 0;
            }),
            'total_count' => $paymentsQuery->count(),
        ];

        // Get service requests statistics
        $serviceRequests = [
            'total' => \App\Models\ServiceRequest::whereBetween('requested_at', [$start, $end])->count(),
            'pending' => \App\Models\ServiceRequest::whereBetween('requested_at', [$start, $end])
                ->where('status', 'pending')
                ->count(),
            'approved' => \App\Models\ServiceRequest::whereBetween('requested_at', [$start, $end])
                ->where('status', 'approved')
                ->count(),
            'completed' => \App\Models\ServiceRequest::whereBetween('completed_at', [$start, $end])
                ->where('status', 'completed')
                ->count(),
            'revenue' => \App\Models\ServiceRequest::whereBetween('completed_at', [$start, $end])
                ->where('status', 'completed')
                ->sum('total_price_tsh'),
        ];

        // Get recent bookings for the period
        $recentBookings = Booking::with('room')
            ->whereBetween('created_at', [$start, $end])
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        $currencyService = new CurrencyExchangeService();
        $exchangeRate = $currencyService->getUsdToTshRate();

        // Handle download request
        if ($download) {
            return $this->downloadReport($dateRange, $bookings, $payments, $serviceRequests, $recentBookings, $exchangeRate);
        }

        $role = $this->getRole();
        return view('dashboard.reception-reports', [
            'role' => $role,
            'userName' => auth()->user()->name ?? ($role === 'manager' ? 'Manager' : 'Reception Staff'),
            'userRole' => $role === 'manager' ? 'Manager' : 'Reception',
            'reportType' => $reportType,
            'reportDate' => $reportDate,
            'startDate' => $startDate,
            'endDate' => $endDate,
            'dateRange' => $dateRange,
            'bookings' => $bookings,
            'payments' => $payments,
            'serviceRequests' => $serviceRequests,
            'recentBookings' => $recentBookings,
            'exchangeRate' => $exchangeRate,
        ]);
    }
    
    /**
     * Download report as HTML (with checkout-bill layout)
     */
    private function downloadReport($dateRange, $bookings, $payments, $serviceRequests, $recentBookings, $exchangeRate)
    {
        $filename = 'report_' . str_replace(' ', '_', strtolower($dateRange['label'])) . '_' . date('Y-m-d_His') . '.html';
        
        $html = view('dashboard.report-download', [
            'dateRange' => $dateRange,
            'bookings' => $bookings,
            'payments' => $payments,
            'serviceRequests' => $serviceRequests,
            'recentBookings' => $recentBookings,
            'exchangeRate' => $exchangeRate,
        ])->render();

        return response($html, 200)
            ->header('Content-Type', 'text/html')
            ->header('Content-Disposition', 'attachment; filename="' . $filename . '"');
    }
    
    /**
     * Calculate date range based on report type
     */
    private function calculateDateRange($reportType, $reportDate = null, $startDate = null, $endDate = null)
    {
        $today = Carbon::today();
        
        switch ($reportType) {
            case 'daily':
                $date = $reportDate ? Carbon::parse($reportDate) : $today;
                return [
                    'start' => $date->copy()->startOfDay(),
                    'end' => $date->copy()->endOfDay(),
                    'label' => $date->format('F d, Y')
                ];
                
            case 'weekly':
                $date = $reportDate ? Carbon::parse($reportDate) : $today;
                return [
                    'start' => $date->copy()->startOfWeek(),
                    'end' => $date->copy()->endOfWeek(),
                    'label' => $date->copy()->startOfWeek()->format('M d') . ' - ' . $date->copy()->endOfWeek()->format('M d, Y')
                ];
                
            case 'monthly':
                $date = $reportDate ? Carbon::parse($reportDate) : $today;
                return [
                    'start' => $date->copy()->startOfMonth(),
                    'end' => $date->copy()->endOfMonth(),
                    'label' => $date->format('F Y')
                ];
                
            case 'yearly':
                $date = $reportDate ? Carbon::parse($reportDate) : $today;
                return [
                    'start' => $date->copy()->startOfYear(),
                    'end' => $date->copy()->endOfYear(),
                    'label' => $date->format('Y')
                ];
                
            case 'custom':
                if ($startDate && $endDate) {
                    $start = Carbon::parse($startDate)->startOfDay();
                    $end = Carbon::parse($endDate)->endOfDay();
                    return [
                        'start' => $start,
                        'end' => $end,
                        'label' => $start->format('M d, Y') . ' - ' . $end->format('M d, Y')
                    ];
                }
                // Fallback to today if custom dates not provided
                return [
                    'start' => $today->copy()->startOfDay(),
                    'end' => $today->copy()->endOfDay(),
                    'label' => $today->format('F d, Y')
                ];
                
            default:
                $date = $reportDate ? Carbon::parse($reportDate) : $today;
                return [
                    'start' => $date->copy()->startOfDay(),
                    'end' => $date->copy()->endOfDay(),
                    'label' => $date->format('F d, Y')
                ];
        }
    }

    /**
     * Check out all guests from a company group
     */
    public function checkoutCompanyGroup($companyId)
    {
        $company = \App\Models\Company::findOrFail($companyId);
        
        // Get all checked-in bookings for this company
        $bookings = \App\Models\Booking::where('company_id', $companyId)
            ->where('is_corporate_booking', true)
            ->where('check_in_status', 'checked_in')
            ->get();
        
        if ($bookings->isEmpty()) {
            return response()->json([
                'success' => false,
                'message' => 'No checked-in bookings found for this company.',
            ], 400);
        }
        
        // Check if all bookings have outstanding balance < 50 TZS
        $currencyService = new CurrencyExchangeService();
        $exchangeRate = $currencyService->getUsdToTshRate();
        
        $hasOutstanding = false;
        foreach ($bookings as $booking) {
            $bookingExchangeRate = $booking->locked_exchange_rate ?? $exchangeRate;
            
            $serviceRequests = $booking->serviceRequests()
                ->whereIn('status', ['pending', 'approved', 'preparing', 'completed'])
                ->get();
            
            $totalServiceChargesTsh = $serviceRequests->sum('total_price_tsh');
            
            // Check payment responsibility - if self-paid, exclude service charges from company bill
            $paymentResponsibility = $booking->payment_responsibility ?? 'company';
            $companyResponsibleServiceChargesTsh = 0;
            
            if ($paymentResponsibility === 'self') {
                // Guest pays for services - exclude from company bill
                $companyResponsibleServiceChargesTsh = 0;
            } else {
                // Company pays for services
                $companyResponsibleServiceChargesTsh = $totalServiceChargesTsh;
            }
            
            $extensionCostUsd = 0;
            if ($booking->extension_status === 'approved' && $booking->original_check_out && $booking->extension_requested_to) {
                $originalCheckOut = \Carbon\Carbon::parse($booking->original_check_out);
                $requestedCheckOut = \Carbon\Carbon::parse($booking->extension_requested_to);
                $extensionNights = $originalCheckOut->diffInDays($requestedCheckOut);
                if ($extensionNights > 0 && $booking->room) {
                    $extensionCostUsd = $booking->room->price_per_night * $extensionNights;
                }
            }
            $extensionCostTsh = $extensionCostUsd * $bookingExchangeRate;
            
            // Company's total bill (only what company is responsible for)
            // Note: extensionCostTsh is already included in booking->total_price
            $companyBillTsh = ($booking->total_price * $bookingExchangeRate) + $companyResponsibleServiceChargesTsh;
            // Identify total amount already paid for services by the guest
            $guestPaidServicesTsh = $serviceRequests->where('payment_status', 'paid')->sum('total_price_tsh');

            // Total amount recorded in the booking (including service payments at reception/bar)
            $totalPaidTsh = ($booking->amount_paid ?? 0) * $bookingExchangeRate;
            
            // The company's contribution is the total paid in the booking MINUS 
            // anything the guest paid for services.
            $companyPaidTsh = max(0, $totalPaidTsh - $guestPaidServicesTsh);
            
            $outstandingBalanceTsh = max(0, $companyBillTsh - $companyPaidTsh);
            
            // Treat very small amounts as fully paid
            if ($outstandingBalanceTsh >= 50) {
                $hasOutstanding = true;
                break;
            }
        }
        
        if ($hasOutstanding) {
            return response()->json([
                'success' => false,
                'message' => 'Cannot check out. Some bookings have outstanding balance. Please process payments first.',
            ], 400);
        }
        
        // Check out all bookings
        foreach ($bookings as $booking) {
            $booking->update([
                'check_in_status' => 'checked_out',
                'checked_out_at' => now(),
                'status' => 'completed',
            ]);

            // Mark room as needing cleaning
            if ($booking->room) {
                $booking->room->update(['status' => 'to_be_cleaned']);
                
                // Create cleaning log entry
                \App\Models\RoomCleaningLog::create([
                    'room_id' => $booking->room->id,
                    'status' => 'needs_cleaning',
                ]);

                // Send SMS/Email notifications to housekeepers
                $housekeepers = \App\Models\Staff::where('role', 'housekeeper')
                    ->where('is_active', true)
                    ->get();
                
                foreach ($housekeepers as $housekeeper) {
                    // Email
                    if ($housekeeper->isNotificationEnabled('room_cleaning')) {
                        try {
                            \Illuminate\Support\Facades\Mail::to($housekeeper->email)->send(
                                new \App\Mail\RoomNeedsCleaningMail($booking->room, $booking)
                            );
                        } catch (\Exception $e) {
                            \Log::error('Failed to send room cleaning email: ' . $e->getMessage());
                        }
                    }

                    // SMS
                    if ($housekeeper->phone) {
                        try {
                            (new \App\Services\SmsService())->notifyHousekeeperCheckout(
                                $housekeeper->phone,
                                $housekeeper->name,
                                $booking->room->room_number ?? 'N/A'
                            );
                        } catch (\Exception $e) {
                            \Log::error('Failed to send checkout SMS to housekeeper: ' . $e->getMessage());
                        }
                    }
                }
            }
        }
        
        return response()->json([
            'success' => true,
            'message' => 'All guests from ' . $company->name . ' have been checked out successfully!',
            'redirect' => route('reception.reservations.check-out', ['type' => 'corporate']),
        ]);
    }

    /**
     * Process payment for all bookings in a company group
     */
    public function processCompanyPayment(Request $request, $companyId)
    {
        $request->validate([
            'amount' => 'required|numeric|min:0.01',
            'payment_method' => 'required|in:cash,mobile,bank,card',
            'payment_provider' => 'nullable|string|max:100',
            'payment_reference' => 'nullable|string|max:100',
        ]);
        
        $company = \App\Models\Company::findOrFail($companyId);
        
        // Get all checked-in bookings for this company
        $bookings = \App\Models\Booking::where('company_id', $companyId)
            ->where('is_corporate_booking', true)
            ->where(function($q) {
                $q->where('check_in_status', 'checked_in')
                  ->orWhere(function($q2) {
                      $q2->where('check_in_status', 'checked_out')
                         ->where('payment_status', '!=', 'paid');
                  });
            })
            ->get();
        
        if ($bookings->isEmpty()) {
            return response()->json([
                'success' => false,
                'message' => 'No bookings found for this company.',
            ], 400);
        }
        
        $currencyService = new CurrencyExchangeService();
        $exchangeRate = $currencyService->getUsdToTshRate();
        
        // Calculate total outstanding balance
        $companyBookingsData = [];
        $totalCompanyOutstandingUsd = 0;
        
        foreach ($bookings as $booking) {
            $bookingExchangeRate = $booking->locked_exchange_rate ?? $exchangeRate;
            $serviceRequests = $booking->serviceRequests()->whereIn('status', ['pending', 'approved', 'preparing', 'completed'])->get();
            $paymentResponsibility = $booking->payment_responsibility ?? 'company';
            $companyServiceChargesTsh = ($paymentResponsibility === 'self') ? 0 : $serviceRequests->sum('total_price_tsh');
            
            $extensionCostUsd = 0;
            if ($booking->extension_status === 'approved' && $booking->original_check_out && $booking->extension_requested_to) {
                $nights = \Carbon\Carbon::parse($booking->original_check_out)->diffInDays($booking->extension_requested_to);
                if ($nights > 0 && $booking->room) $extensionCostUsd = $booking->room->price_per_night * $nights;
            }
            
            // Company's total bill (room + company-responsible services + extensions)
            // Note: extensionCostTsh is already included in booking->total_price according to logic
            $companyBillTsh = ($booking->total_price * $bookingExchangeRate) + $companyServiceChargesTsh;

            // Identify total amount already paid for services by the guest
            $guestPaidServicesTsh = $serviceRequests->where('payment_status', 'paid')->sum('total_price_tsh');

            // Total amount recorded in the booking (including service payments at reception/bar)
            $totalPaidTsh = ($booking->amount_paid ?? 0) * $bookingExchangeRate;
            
            // The company's contribution is the total paid in the booking MINUS 
            // anything the guest paid for services.
            $companyPaidTsh = max(0, $totalPaidTsh - $guestPaidServicesTsh);
            
            $outstandingTsh = max(0, $companyBillTsh - $companyPaidTsh);
            $outstandingUsd = $outstandingTsh / $bookingExchangeRate;
            
            if ($outstandingTsh > 50) {
                $companyBookingsData[] = [
                    'booking' => $booking,
                    'outstanding_usd' => $outstandingUsd,
                    'total_service_charges_tsh' => $serviceRequests->sum('total_price_tsh'),
                    'responsibility' => $paymentResponsibility,
                    'bookingExchangeRate' => $bookingExchangeRate
                ];
                $totalCompanyOutstandingUsd += $outstandingUsd;
            }
        }
        
        $paymentAmountUsd = (float) $request->amount;
        
        // Relaxed validation: Allow any payment up to the total outstanding (allowing partial payments)
        if ($paymentAmountUsd > $totalCompanyOutstandingUsd + 0.1) {
             return response()->json([
                'success' => false,
                'message' => 'Payment amount ($' . number_format($paymentAmountUsd, 2) . ') exceed company outstanding balance ($'.number_format($totalCompanyOutstandingUsd, 2).').',
            ], 400);
        }
        
        // Process payments
        $remainingPaymentUsd = $paymentAmountUsd;
        foreach ($companyBookingsData as $data) {
            if ($remainingPaymentUsd <= 0) break;

            $booking = $data['booking'];
            $bookingExchangeRate = $data['bookingExchangeRate'];
            
            // Pay as much as possible for this booking
            $payForThisBookingUsd = min($remainingPaymentUsd, $data['outstanding_usd']);
            $remainingPaymentUsd -= $payForThisBookingUsd;
            
            // Increment amount_paid
            $newAmountPaidUsd = ($booking->amount_paid ?? 0) + $payForThisBookingUsd;
            
            // Check if fully paid (including services if company-responsible)
            $totalServiceChargesTsh = $data['total_service_charges_tsh'];
            $totalBillTsh = ($booking->total_price * $bookingExchangeRate) + ($data['responsibility'] === 'company' ? $totalServiceChargesTsh : 0);
            
            // For 'self' responsibility, fully paid means room is paid
            if ($data['responsibility'] === 'self') {
                $totalBillTsh = ($booking->total_price * $bookingExchangeRate);
            }

            $isFullyPaid = ( ($newAmountPaidUsd * $bookingExchangeRate) >= ($totalBillTsh - 50) );
            
            $booking->update([
                'payment_status' => $isFullyPaid ? 'paid' : 'partial',
                'payment_method' => $request->payment_method,
                'payment_provider' => $request->payment_provider ?? null,
                'payment_transaction_id' => $request->payment_reference ?? null,
                'amount_paid' => $newAmountPaidUsd,
                'paid_at' => $booking->paid_at ?? now(),
                'total_service_charges_tsh' => $totalServiceChargesTsh,
            ]);
        }
        
        return response()->json([
            'success' => true,
            'message' => 'Company bill paid successfully!',
        ]);
    }

    /**
     * Generate a consolidated bill for a company group
     */
    public function companyGroupBill($companyId)
    {
        try {
            $company = \App\Models\Company::findOrFail($companyId);
            
            // Get all confirmed bookings for this company that are checked in or recently checked out
            $bookings = Booking::with(['room', 'serviceRequests.service'])
                ->where('company_id', $companyId)
                ->where('is_corporate_booking', true)
                ->where('status', 'confirmed')
                ->where(function($q) {
                    $q->where('check_in_status', 'checked_in')
                      ->orWhere('check_in_status', 'checked_out');
                })
                ->orderBy('check_in', 'asc')
                ->get();

            if ($bookings->isEmpty()) {
                return redirect()->back()->with('error', 'No active bookings found for this company.');
            }

            $currencyService = new CurrencyExchangeService();
            $exchangeRate = $currencyService->getUsdToTshRate();
            
            $groupData = [
                'company' => $company,
                'bookings' => [],
                'modifications' => [], // To track stay modifications (extensions/decreases)
                'totals' => [
                    'room_price_usd' => 0,
                    'extension_cost_usd' => 0,
                    'service_charges_tsh' => 0,
                    'total_bill_tsh' => 0,
                    'amount_paid_tsh' => 0,
                    'outstanding_tsh' => 0,
                    'outstanding_usd' => 0,
                ]
            ];

            foreach ($bookings as $booking) {
                $bookingExchangeRate = $booking->locked_exchange_rate ?? $exchangeRate;

                // Track stay modifications
                if ($booking->original_check_out && $booking->original_check_out != $booking->check_out) {
                    $originalDate = \Carbon\Carbon::parse($booking->original_check_out);
                    $newDate = \Carbon\Carbon::parse($booking->check_out);
                    
                    $modType = $newDate->gt($originalDate) ? 'Extension' : 'Stay Reduction';
                    $modNights = abs($originalDate->diffInDays($newDate));
                    $modCostUsd = 0;
                    
                    if ($newDate->gt($originalDate) && $booking->room) {
                        $modCostUsd = ($booking->room->price_per_night ?? 0) * $modNights;
                    }

                    $groupData['modifications'][] = [
                        'guest_name' => $booking->guest_name,
                        'ref' => $booking->booking_reference,
                        'type' => $modType,
                        'nights' => $modNights,
                        'original' => $originalDate->format('M d, Y'),
                        'new' => $newDate->format('M d, Y'),
                        'cost_usd' => $modCostUsd,
                        'reason' => $booking->extension_admin_notes ?: 'Modified by staff'
                    ];
                }
                
                // Calculate service charges
                $serviceRequests = $booking->serviceRequests()
                    ->whereIn('status', ['pending', 'approved', 'preparing', 'completed'])
                    ->get();
                
                // Check payment responsibility
                $paymentResponsibility = $booking->payment_responsibility ?? 'company';
                
                $companyResponsibleServiceTsh = ($paymentResponsibility === 'company') 
                    ? $serviceRequests->sum('total_price_tsh') 
                    : 0;
                
                $guestResponsibleServiceTsh = ($paymentResponsibility === 'self') 
                    ? $serviceRequests->sum('total_price_tsh') 
                    : 0;

                // Extension cost
                $extensionCostUsd = 0;
                if ($booking->extension_status === 'approved' && $booking->original_check_out && $booking->extension_requested_to) {
                    $originalCheckOut = Carbon::parse($booking->original_check_out);
                    $requestedCheckOut = Carbon::parse($booking->extension_requested_to);
                    $extensionNights = $originalCheckOut->diffInDays($requestedCheckOut);
                    if ($extensionNights > 0 && $booking->room) {
                        $extensionCostUsd = $booking->room->price_per_night * $extensionNights;
                    }
                }
                $extensionCostTsh = $extensionCostUsd * $bookingExchangeRate;
                
                // Total room bill (including extensions)
                $roomBillUsd = $booking->total_price; 
                $roomBillTsh = $roomBillUsd * $bookingExchangeRate;

                // Company's bill for THIS booking
                $companyBookingBillTsh = $roomBillTsh + $companyResponsibleServiceTsh;
                
                $totalPaidTsh = ($booking->amount_paid ?? 0) * $bookingExchangeRate;
                
                // Identify total amount already paid for services by the guest
                $guestPaidServicesTsh = $serviceRequests->where('payment_status', 'paid')->sum('total_price_tsh');

                // The company's contribution is the total paid in the booking MINUS 
                // anything the guest paid for services.
                $companyBookingPaidTsh = max(0, $totalPaidTsh - $guestPaidServicesTsh);

                $companyBookingOutstandingTsh = max(0, $companyBookingBillTsh - $companyBookingPaidTsh);

                // Threshold handling (Hide rounding errors under 50 TZS)
                if ($companyBookingOutstandingTsh < 50) {
                    $companyBookingOutstandingTsh = 0;
                    $companyBookingPaidTsh = $companyBookingBillTsh; // Adjust display to show full payment
                }

                // Add to group list
                $groupData['bookings'][] = [
                    'booking' => $booking,
                    'room_bill_usd' => $roomBillUsd,
                    'room_bill_tsh' => $roomBillTsh,
                    'service_charges_tsh' => $companyResponsibleServiceTsh,
                    'guest_charges_tsh' => $guestResponsibleServiceTsh,
                    'total_bill_tsh' => $companyBookingBillTsh,
                    'amount_paid_tsh' => $companyBookingPaidTsh,
                    'outstanding_tsh' => $companyBookingOutstandingTsh,
                ];

                // Update totals
                $groupData['totals']['room_price_usd'] += $roomBillUsd;
                $groupData['totals']['service_charges_tsh'] += $companyResponsibleServiceTsh;
                $groupData['totals']['total_bill_tsh'] += $companyBookingBillTsh;
                $groupData['totals']['amount_paid_tsh'] += $companyBookingPaidTsh;
                $groupData['totals']['outstanding_tsh'] += $companyBookingOutstandingTsh;
                $groupData['totals']['outstanding_usd'] += ($bookingExchangeRate > 0) ? ($companyBookingOutstandingTsh / $bookingExchangeRate) : 0;
            }

            $role = $this->getRole();
            return view('dashboard.company-group-bill', [
                'role' => $role,
                'userName' => auth()->user()->name ?? 'Staff',
                'userRole' => $role === 'manager' ? 'Manager' : 'Reception',
                'groupData' => $groupData,
                'exchangeRate' => $exchangeRate,
            ]);

        } catch (\Exception $e) {
            \Log::error('Error generating company group bill: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Failed to generate bill: ' . $e->getMessage());
        }
    }

    /**
     * Receptionist: Real-time Order Monitor
     * Shows all food and bar orders across the hotel.
     */
    public function allOrders(Request $request)
    {
        $status = $request->get('status', 'all');
        $type = $request->get('type', 'all'); // food or bar
        $search = $request->get('search');
        
        $query = ServiceRequest::with(['booking.room', 'service', 'approvedBy', 'paidBy', 'cancelledBy'])
            ->orderBy('requested_at', 'desc');

        if ($status !== 'all') {
            $query->where('status', $status);
        } else {
            $recentCutoff = now()->subHours(2);
            $query->where(function($q) use ($recentCutoff) {
                // 1. Always show active/pending items
                $q->whereIn('status', ['pending', 'approved', 'preparing', 'ready'])
                  // 2. Completed but unpaid — still need collection
                  ->orWhere(function($q2) {
                      $q2->where('status', 'completed')
                         ->whereIn('payment_status', ['pending', 'unpaid']);
                  })
                  // 3. Recently PAID (within 2 hours) — context/confirmation
                  ->orWhere(function($q2) use ($recentCutoff) {
                      $q2->where('status', 'completed')
                         ->whereIn('payment_status', ['paid', 'room_charge'])
                         ->where('updated_at', '>=', $recentCutoff);
                  })
                  // 4. Recently CANCELLED (within 2 hours) — context
                  ->orWhere(function($q2) use ($recentCutoff) {
                      $q2->where('status', 'cancelled')
                         ->where('updated_at', '>=', $recentCutoff);
                  });
            });
        }

        if ($type === 'food') {
            $query->whereHas('service', function($q) {
                $q->where('category', 'restaurant');
            });
        } elseif ($type === 'bar') {
            $query->whereHas('service', function($q) {
                $q->where('category', 'bar');
            });
        }

        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('id', $search)
                  ->orWhere('walk_in_name', 'LIKE', "%$search%")
                  ->orWhereHas('booking', function($bq) use ($search) {
                      $bq->where('guest_name', 'LIKE', "%$search%")
                        ->orWhereHas('room', function($rq) use ($search) {
                            $rq->where('room_number', 'LIKE', "%$search%");
                        });
                  })
                  ->orWhere('service_specific_data', 'LIKE', "%$search%");
            });
        }

        $orders = $query->paginate(50);
        
        $role = $this->getRole();
        $userRoleLabel = 'Staff';
        if ($role === 'manager') {
            $userRoleLabel = 'Manager';
        } elseif ($role === 'head_chef') {
            $userRoleLabel = 'Head Chef';
        } else {
            $userRoleLabel = 'Reception';
        }

        return view('dashboard.reception.order-monitor', [
            'role' => $role,
            'userName' => Auth::user()->name ?? 'Staff',
            'userRole' => $userRoleLabel,
            'orders' => $orders,
            'currentStatus' => $status,
            'currentType' => $type,
            'searchTerm' => $search
        ]);
    }

    public function printGroupBill(Request $request)
    {
        // Get group key from request
        $isWalkIn = $request->input('is_walk_in', false);
        $identifier = $request->input('identifier'); // walk_in_name or booking_id
        
        // Fetch all orders for this group (No waiter name filter for management)
        $query = ServiceRequest::with(['service', 'booking.room', 'dayService']);
        
        if ($isWalkIn) {
            $query = $query->where('is_walk_in', true)
                ->where('walk_in_name', $identifier);
        } else {
            $query = $query->where('booking_id', $identifier);
        }
        
        $orders = $query->orderBy('requested_at', 'desc')
            ->get();
        
        if ($orders->isEmpty()) {
            abort(404, 'No orders found for this guest.');
        }

        // Calculate total amount from non-cancelled orders only
        $totalAmount = $orders->filter(fn($o) => strtolower($o->status) !== 'cancelled')->sum('total_price_tsh');

        // If walk-in, further filter by date to avoid picking up same name from different days
        if ($isWalkIn) {
            $firstDate = $orders->first()->requested_at->toDateString();
            $orders = $orders->filter(function($o) use ($firstDate) {
                return $o->requested_at->toDateString() === $firstDate;
            });
        }
        
        $first = $orders->first();
        
        // Destination label
        $destination = 'Internal';
        if ($first->is_walk_in) {
            $walkInName = $first->walk_in_name ?? 'Guest';
            $destination = str_contains(strtolower($walkInName), 'walk-in') ? $walkInName : 'WALK-IN (' . $walkInName . ')';
        } elseif ($first->booking) {
            $destination = 'ROOM ' . ($first->booking->room->room_number ?? 'N/A');
        }
        
        // Guest Name
        $guestName = $first->is_walk_in ? ($first->walk_in_name ?? 'General Guest') : ($first->booking->guest_name ?? 'Hotel Guest');
        
        // Requested By (Try to extract from notes)
        $requestedBy = 'Staff';
        if ($first->reception_notes && str_contains($first->reception_notes, 'Waiter: ')) {
            $parts = explode('Waiter: ', $first->reception_notes);
            $byParts = explode(' - Msg:', $parts[1] ?? '');
            $requestedBy = trim(explode('|', $byParts[0] ?? 'Waiter')[0]);
        }
        

        return view('dashboard.print-waiter-group-docket', compact('orders', 'destination', 'guestName', 'requestedBy', 'totalAmount', 'first'));
    }

    /**
     * Shift Management: Shift History (Monitoring for Manager & Owner)
     */
    public function shiftHistory(Request $request)
    {
        $query = Shift::with('staff')->orderBy('opened_at', 'desc');

        // Optional filtering by staff
        if ($request->filled('staff_id')) {
            $query->where('staff_id', $request->staff_id);
        }

        // Optional filtering by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Optional filtering by single date
        if ($request->filled('date')) {
            $query->whereDate('opened_at', $request->date);
        }

        // Optional filtering by date range
        if ($request->filled('date_from')) {
            $query->whereDate('opened_at', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('opened_at', '<=', $request->date_to);
        }

        $shifts = $query->paginate(20)->appends($request->query());
        $role = $this->getRole();
        $allStaff = Staff::where('is_active', true)->whereIn('role', ['reception', 'manager', 'waiter', 'bar_keeper'])->get();

        // Detect if accessed from owner prefix to use correct named routes
        $isOwnerRoute = request()->is('owner/*');
        $historyRoute = $isOwnerRoute ? 'owner.shift.history' : 'reception.shift.history';
        $printRoute   = $isOwnerRoute ? 'owner.shift.print'   : 'reception.shift.print';
        $dashRoute    = $isOwnerRoute ? 'owner.dashboard'      : ($role === 'manager' ? 'admin.dashboard' : 'reception.dashboard');

        return view('dashboard.reception.shift-history', [
            'role'         => $isOwnerRoute ? 'owner' : $role,
            'userName'     => Auth::user()->name ?? 'Staff',
            'userRole'     => $isOwnerRoute ? 'Owner' : ($role === 'manager' ? 'Manager' : 'Reception'),
            'shifts'       => $shifts,
            'allStaff'     => $allStaff,
            'activePage'   => ($isOwnerRoute ? 'owner' : 'reception') . '/shift/history',
            'filters'      => $request->only(['staff_id', 'status', 'date', 'date_from', 'date_to']),
            'historyRoute' => $historyRoute,
            'printRoute'   => $printRoute,
            'dashRoute'    => $dashRoute,
        ]);
    }

    /**
     * Shift Management: Open Shift View
     */
    public function openShiftView()
    {
        // Managers are supervisors only — they cannot operate shifts
        $staff = Auth::guard('staff')->user();
        if ($staff) {
            $rawRole = strtolower(trim($staff->role));
            if ($rawRole === 'manager') {
                return redirect()->route('reception.shift.history')
                    ->with('info', 'Managers can only monitor shifts, not open them. Use Shift Management below to view all sessions.');
            }
        }

        // Check if user already has an open shift
        $activeShift = Shift::where('staff_id', Auth::guard('staff')->id())
            ->where('status', 'open')
            ->first();

        if ($activeShift) {
            $redirectRoute = 'reception.dashboard';
            return redirect()->route($redirectRoute)->with('info', 'You already have an active shift open.');
        }

        $role = $this->getRole();
        return view('dashboard.reception.shift-open', [
            'role' => $role,
            'userName' => Auth::user()->name ?? 'Staff',
            'userRole' => $role === 'manager' ? 'Manager' : 'Reception',
        ]);
    }

    /**
     * Shift Management: Start Shift
     */
    public function startShift(Request $request)
    {
        // Managers cannot open shifts
        $staff = Auth::guard('staff')->user();
        if ($staff && strtolower(trim($staff->role)) === 'manager') {
            return redirect()->route('reception.shift.history')
                ->with('error', 'Managers are not permitted to open shifts.');
        }

        $request->validate([
            'opening_cash' => 'required|numeric|min:0'
        ]);

        $shift = Shift::create([
            'staff_id'     => Auth::guard('staff')->id(),
            'opened_at'    => now(),
            'opening_cash' => $request->opening_cash,
            'status'       => 'open'
        ]);

        // ── Send SMS alert to all active Managers & Owners ──────────────────
        try {
            $receptionist = Auth::guard('staff')->user();
            $openedAt     = now()->format('d M Y, H:i');
            $openingCash  = (float) $request->opening_cash;

            $recipients = Staff::where('is_active', true)
                ->whereIn('role', ['manager', 'owner'])
                ->whereNotNull('phone')
                ->where('phone', '!=', '')
                ->get();

            if ($recipients->isNotEmpty()) {
                $smsService = new \App\Services\SmsService();
                foreach ($recipients as $recipient) {
                    $smsService->sendShiftOpenedNotification(
                        $recipient->phone,
                        $recipient->name,
                        $receptionist->name ?? 'Reception Staff',
                        $openedAt,
                        $openingCash
                    );
                }
                \Log::info('[ShiftSMS] Shift-open alert sent', [
                    'shift_id'     => $shift->id,
                    'receptionist' => $receptionist->name ?? 'unknown',
                    'notified'     => $recipients->pluck('name')->implode(', '),
                ]);
            }
        } catch (\Throwable $e) {
            \Log::warning('[ShiftSMS] Failed to send shift-open SMS: ' . $e->getMessage());
        }
        // ────────────────────────────────────────────────────────────────────

        return redirect()->route('reception.dashboard')->with('success', 'Shift opened successfully! Good luck with your shift.');
    }

    /**
     * Shift Management: Close Shift View
     */
    public function closeShiftView()
    {
        // Managers are supervisors only — they cannot close/operate shifts
        $staff = Auth::guard('staff')->user();
        if ($staff && strtolower(trim($staff->role)) === 'manager') {
            return redirect()->route('reception.shift.history')
                ->with('info', 'Managers can only monitor shifts, not close them. View all sessions from Shift History.');
        }

        $activeShift = Shift::where('staff_id', Auth::guard('staff')->id())
            ->where('status', 'open')
            ->first();

        if (!$activeShift) {
            return redirect()->route('reception.dashboard')->with('error', 'No active shift found. Please open a shift first.');
        }

        // Calculate expected totals since shift opened
        $openedAt = $activeShift->opened_at;
        
        // 1. Booking Payments
        $bookingPayments = Booking::where('paid_at', '>=', $openedAt)
            ->whereNotNull('paid_at')
            ->get();
            
        $cashBookings = 0;
        $cardBookings = 0;
        $mobileBookings = 0;
        $bankBookings = 0;
        $onlineBookings = 0;
        
        $currencyService = new CurrencyExchangeService();
        $rate = $currencyService->getUsdToTshRate();

        foreach ($bookingPayments as $bp) {
            $amountTsh = ($bp->amount_paid ?? 0) * ($bp->locked_exchange_rate ?? $rate);
            $meth = strtolower($bp->payment_method ?? '');
            
            if ($meth === 'cash') {
                $cashBookings += $amountTsh;
            } elseif ($meth === 'card' || str_contains($meth, 'master') || str_contains($meth, 'visa')) {
                $cardBookings += $amountTsh;
            } elseif (in_array($meth, ['mobile', 'mpesa', 'halo', 'tigo', 'airtel', 'halopesa', 'mixx', 'yass'])) {
                $mobileBookings += $amountTsh;
            } elseif ($meth === 'bank' || in_array($meth, ['nmb', 'crdb', 'kcb', 'nbc', 'dtb'])) {
                $bankBookings += $amountTsh;
            } elseif ($meth === 'online' || in_array($meth, ['expedia', 'booking', 'booking.com', 'agoda', 'airbnb'])) {
                $onlineBookings += $amountTsh;
            } else {
                // Fallback for any other methods
                $cashBookings += $amountTsh; 
            }
        }

        // 2. Service/Order Payments
        $servicePayments = ServiceRequest::where('completed_at', '>=', $openedAt)
            ->where('payment_status', 'paid')
            ->with('paidBy')
            ->get();
            
        $cashServices = 0;
        $cardServices = 0;
        $mobileServices = 0;
        $bankServices = 0;
        $onlineServices = 0;

        $servicePaymentsByStaff = [];

        foreach ($servicePayments as $sp) {
            $meth = strtolower($sp->payment_method ?? 'cash');
            $amt = $sp->total_price_tsh;
            $staffName = $sp->paidBy->name ?? 'System/Other';
            $staffId = $sp->paid_to ?? 0;

            if (!isset($servicePaymentsByStaff[$staffId])) {
                $servicePaymentsByStaff[$staffId] = [
                    'name' => $staffName,
                    'cash' => 0,
                    'non_cash' => 0,
                    'total' => 0
                ];
            }

            if ($meth === 'cash') {
                $cashServices += $amt;
                $servicePaymentsByStaff[$staffId]['cash'] += $amt;
            } elseif ($meth === 'card' || str_contains($meth, 'master') || str_contains($meth, 'visa')) {
                $cardServices += $amt;
                $servicePaymentsByStaff[$staffId]['non_cash'] += $amt;
            } elseif (in_array($meth, ['mobile', 'mpesa', 'halo', 'tigo', 'airtel', 'halopesa', 'mixx', 'yass', 'mobile_money'])) {
                $mobileServices += $amt;
                $servicePaymentsByStaff[$staffId]['non_cash'] += $amt;
            } elseif ($meth === 'bank' || in_array($meth, ['nmb', 'crdb', 'kcb', 'nbc', 'dtb'])) {
                $bankServices += $amt;
                $servicePaymentsByStaff[$staffId]['non_cash'] += $amt;
            } elseif ($meth === 'online' || in_array($meth, ['expedia', 'booking', 'booking.com', 'agoda', 'airbnb'])) {
                $onlineServices += $amt;
                $servicePaymentsByStaff[$staffId]['non_cash'] += $amt;
            } else {
                $cashServices += $amt;
                $servicePaymentsByStaff[$staffId]['cash'] += $amt;
            }
            $servicePaymentsByStaff[$staffId]['total'] += $amt;
        }

        // 3. Day Service Payments (Ceremonies, Swimming, etc.)
        $dayServicePayments = \App\Models\DayService::where('paid_at', '>=', $openedAt)
            ->where('payment_status', 'paid')
            ->with('registeredBy')
            ->get();

        foreach ($dayServicePayments as $ds) {
            $meth = strtolower($ds->payment_method ?? 'cash');
            // If TZS, use amount_paid. If USD, convert using exchange_rate
            $amt = ($ds->guest_type === 'tanzanian') ? ($ds->amount_paid ?? 0) : (($ds->amount_paid ?? 0) * ($ds->exchange_rate ?? $rate));
            
            $staffName = $ds->registeredBy->name ?? 'System/Other';
            $staffId = $ds->registered_by ?? 0;

            if (!isset($servicePaymentsByStaff[$staffId])) {
                $servicePaymentsByStaff[$staffId] = [
                    'name' => $staffName,
                    'cash' => 0,
                    'non_cash' => 0,
                    'total' => 0
                ];
            }

            if ($meth === 'cash') {
                $cashServices += $amt;
                $servicePaymentsByStaff[$staffId]['cash'] += $amt;
            } elseif ($meth === 'card' || str_contains($meth, 'master') || str_contains($meth, 'visa')) {
                $cardServices += $amt;
                $servicePaymentsByStaff[$staffId]['non_cash'] += $amt;
            } elseif (in_array($meth, ['mobile', 'mpesa', 'halo', 'tigo', 'airtel', 'halopesa', 'mixx', 'yass', 'mobile_money'])) {
                $mobileServices += $amt;
                $servicePaymentsByStaff[$staffId]['non_cash'] += $amt;
            } elseif ($meth === 'bank' || in_array($meth, ['nmb', 'crdb', 'kcb', 'nbc', 'dtb'])) {
                $bankServices += $amt;
                $servicePaymentsByStaff[$staffId]['non_cash'] += $amt;
            } elseif ($meth === 'online' || in_array($meth, ['expedia', 'booking', 'booking.com', 'agoda', 'airbnb'])) {
                $onlineServices += $amt;
                $servicePaymentsByStaff[$staffId]['non_cash'] += $amt;
            } else {
                $cashServices += $amt;
                $servicePaymentsByStaff[$staffId]['cash'] += $amt;
            }
            $servicePaymentsByStaff[$staffId]['total'] += $amt;
        }

        $expectedCash = $activeShift->opening_cash + $cashBookings + $cashServices;
        $expectedCard = $cardBookings + $cardServices;
        $expectedMobile = $mobileBookings + $mobileServices;
        $expectedBank = $bankBookings + $bankServices;
        $expectedOnline = $onlineBookings + $onlineServices;

        $role = $this->getRole();
        return view('dashboard.reception.shift-close', [
            'role' => $role,
            'userName' => Auth::user()->name ?? 'Staff',
            'userRole' => $role === 'manager' ? 'Manager' : 'Reception',
            'shift' => $activeShift,
            'expectedCash' => $expectedCash,
            'expectedCard' => $expectedCard,
            'expectedMobile' => $expectedMobile,
            'expectedBank' => $expectedBank,
            'expectedOnline' => $expectedOnline,
            'cashBookings' => $cashBookings,
            'cashServices' => $cashServices,
            'mobileBookings' => $mobileBookings,
            'mobileServices' => $mobileServices,
            'bankBookings' => $bankBookings,
            'bankServices' => $bankServices,
            'cardBookings' => $cardBookings,
            'cardServices' => $cardServices,
            'onlineBookings' => $onlineBookings,
            'onlineServices' => $onlineServices,
            'servicePaymentsByStaff' => $servicePaymentsByStaff,
        ]);
    }

    /**
     * Shift Management: Finalize Shift
     */
    public function finalizeShift(Request $request)
    {
        $activeShift = Shift::where('staff_id', Auth::guard('staff')->id())
            ->where('status', 'open')
            ->first();

        if (!$activeShift) {
            return response()->json(['success' => false, 'message' => 'No active shift found.'], 404);
        }

        $request->validate([
            'closing_cash_actual' => 'required|numeric|min:0',
            'notes' => 'nullable|string'
        ]);

        $activeShift->update([
            'closed_at' => now(),
            'closing_cash_actual' => $request->closing_cash_actual,
            'closing_cash_expected' => $request->closing_cash_expected,
            'total_mpesa_expected' => $request->total_mobile_expected, // Keep for overlap/legacy
            'total_mobile_expected' => $request->total_mobile_expected,
            'total_card_expected' => $request->total_card_expected,
            'total_bank_expected' => $request->total_bank_expected,
            'total_online_expected' => $request->total_online_expected,
            'notes' => $request->notes,
            'status' => 'closed'
        ]);

        // Determine redirect route based on role
    $staff = Auth::guard('staff')->user();
    $redirectRoute = 'reception.dashboard';
    
    if ($staff) {
        $rawRole = strtolower(trim($staff->role));
        if (in_array($rawRole, ['manager', 'super_admin', 'super admin', 'superadmin'])) {
            $redirectRoute = 'admin.dashboard';
        }
    }

    return response()->json([
        'success' => true,
        'message' => 'Shift closed successfully!',
        'redirect' => route($redirectRoute),
        'print_url' => route('reception.shift.print', ['shift' => $activeShift->id, 'autoprint' => 'true'])
    ]);
    }
    /**
     * Waiter Sales Tracking Report
     */
    public function waiterSales(Request $request)
    {
        $selectedDate = $request->has('date') ? \Carbon\Carbon::parse($request->date) : today();
        
        // Get all staff with waiter role
        $waiters = Staff::where('role', 'waiter')->where('is_active', true)->get();
        
        $waiterSummaries = [];
        
        foreach ($waiters as $waiter) {
            // Find orders where notes contain this waiter's name
            $orders = ServiceRequest::with('service')
                ->where('status', 'completed')
                ->whereDate('requested_at', $selectedDate)
                ->where('reception_notes', 'LIKE', "%Waiter: {$waiter->name}%")
                ->get();
            
            $summary = [
                'name' => $waiter->name,
                'total_amount' => 0,
                'order_count' => $orders->count(),
                'platforms' => [
                    'cash' => 0,
                    'mobile' => 0,
                    'card' => 0,
                    'room_charge' => 0
                ],
                'categories' => [
                    'food' => 0,
                    'bar' => 0
                ]
            ];
            
            foreach ($orders as $order) {
                $amt = (float)$order->total_price_tsh;
                $summary['total_amount'] += $amt;
                
                // Platforms (Payment Methods)
                $method = strtolower($order->payment_method ?? 'cash');
                if ($method === 'mpesa' || $method === 'mobile_money') $method = 'mobile';
                
                if (isset($summary['platforms'][$method])) {
                    $summary['platforms'][$method] += $amt;
                } else {
                    $summary['platforms']['cash'] += $amt;
                }
                
                // Categories
                $cat = strtolower($order->service->category ?? '');
                if (str_contains($cat, 'bar') || str_contains($cat, 'drink')) {
                    $summary['categories']['bar'] += $amt;
                } else {
                    $summary['categories']['food'] += $amt;
                }
            }
            
            if ($summary['order_count'] > 0) {
                $waiterSummaries[] = (object)$summary;
            }
        }
        
        // Sort by total amount descending
        usort($waiterSummaries, function($a, $b) {
            return $b->total_amount <=> $a->total_amount;
        });

        $role = $this->getRole();
        return view('dashboard.reception.waiter-sales', [
            'role' => $role,
            'userName' => Auth::user()->name ?? 'Staff',
            'userRole' => $role === 'manager' ? 'Manager' : 'Reception',
            'selectedDate' => $selectedDate,
            'waiterSummaries' => $waiterSummaries,
            'activePage' => 'reception/reports/waiters'
        ]);
    }

    /**
     * Print Shift Report
     */
    public function printShiftReport(Shift $shift)
    {
        $shift->load('staff');
        
        $currencyService = new CurrencyExchangeService();
        $rate = $currencyService->getUsdToTshRate();

        $start = $shift->opened_at;
        $end = $shift->closed_at ?? now();

        // 1. Booking Payments
        $bookingPayments = Booking::where('paid_at', '>=', $start)
            ->where('paid_at', '<=', $end)
            ->whereNotNull('paid_at')
            ->get();
            
        // 2. Service/Order Payments
        $servicePayments = ServiceRequest::where('completed_at', '>=', $start)
            ->where('completed_at', '<=', $end)
            ->where('payment_status', 'paid')
            ->get();

        // 3. Day Service Payments
        $dayServicePayments = DayService::where('paid_at', '>=', $start)
            ->where('paid_at', '<=', $end)
            ->where('payment_status', 'paid')
            ->get();

        $platformBreakdown = [
            'mobile' => [
                'm-pesa' => 0, 'halopesa' => 0, 'tigo pesa' => 0, 'airtel money' => 0, 'mixx by yass' => 0, 'other mobile' => 0
            ],
            'bank' => [
                'nmb' => 0, 'crdb' => 0, 'kcb' => 0, 'nbc' => 0, 'dtb' => 0, 'other bank' => 0
            ],
            'online' => [
                 'expedia' => 0, 'booking.com' => 0, 'agoda' => 0, 'airbnb' => 0, 'other online' => 0
            ],
            'card' => [
                'visa/mastercard' => 0
            ],
            'cash' => [
                'total' => 0
            ]
        ];

        // Process Booking Payments
        foreach ($bookingPayments as $bp) {
            $amt = ($bp->amount_paid ?? 0) * ($bp->locked_exchange_rate ?? $rate);
            $meth = strtolower($bp->payment_method ?? '');
            
            $this->categorizePayment($meth, $amt, $platformBreakdown);
        }

        // Process Services & Calculate Staff Breakdown
        $servicePaymentsByStaff = [];
        foreach ($servicePayments as $sp) {
            $amt = $sp->total_price_tsh;
            $meth = strtolower($sp->payment_method ?? 'cash');
            
            $this->categorizePayment($meth, $amt, $platformBreakdown);

            // Staff Breakdown
            $staffName = $sp->paidBy->name ?? 'System/Other';
            $staffId = $sp->paid_to ?? 0;
            if (!isset($servicePaymentsByStaff[$staffId])) {
                $servicePaymentsByStaff[$staffId] = ['name' => $staffName, 'cash' => 0, 'non_cash' => 0, 'total' => 0];
            }
            if ($meth === 'cash' || str_contains($meth, 'cash')) {
                $servicePaymentsByStaff[$staffId]['cash'] += $amt;
            } else {
                $servicePaymentsByStaff[$staffId]['non_cash'] += $amt;
            }
            $servicePaymentsByStaff[$staffId]['total'] += $amt;
        }

        // Process Day Service Payments
        foreach ($dayServicePayments as $ds) {
            $amt = $ds->amount_paid * ($ds->exchange_rate ?? $rate);
            if ($ds->guest_type === 'tanzanian') $amt = $ds->amount_paid;
            $meth = strtolower($ds->payment_method ?? 'cash');
            
            $this->categorizePayment($meth, $amt, $platformBreakdown);

            // Staff Breakdown (Include Day Services)
            $staffName = $ds->registeredBy->name ?? 'System/Other';
            $staffId = $ds->registered_by ?? 0;
            if (!isset($servicePaymentsByStaff[$staffId])) {
                $servicePaymentsByStaff[$staffId] = ['name' => $staffName, 'cash' => 0, 'non_cash' => 0, 'total' => 0];
            }
            if ($meth === 'cash' || str_contains($meth, 'cash')) {
                $servicePaymentsByStaff[$staffId]['cash'] += $amt;
            } else {
                $servicePaymentsByStaff[$staffId]['non_cash'] += $amt;
            }
            $servicePaymentsByStaff[$staffId]['total'] += $amt;
        }

        return view('dashboard.reception.shift-print', [
            'shift' => $shift,
            'rate' => $rate,
            'hotelName' => 'PRIME LAND HOTEL',
            'breakdown' => $platformBreakdown,
            'servicePaymentsByStaff' => $servicePaymentsByStaff
        ]);
    }

    private function categorizePayment($meth, $amt, &$breakdown)
    {
        if (str_contains($meth, 'cash')) {
            $breakdown['cash']['total'] += $amt;
        } elseif (str_contains($meth, 'mpesa')) {
            $breakdown['mobile']['m-pesa'] += $amt;
        } elseif (str_contains($meth, 'halo')) {
            $breakdown['mobile']['halopesa'] += $amt;
        } elseif (str_contains($meth, 'tigo')) {
            $breakdown['mobile']['tigo pesa'] += $amt;
        } elseif (str_contains($meth, 'airtel')) {
            $breakdown['mobile']['airtel money'] += $amt;
        } elseif (str_contains($meth, 'mixx') || str_contains($meth, 'yass')) {
            $breakdown['mobile']['mixx by yass'] += $amt;
        } elseif (str_contains($meth, 'mobile') || str_contains($meth, 'money')) {
            $breakdown['mobile']['other mobile'] += $amt;
        } elseif (str_contains($meth, 'nmb')) {
            $breakdown['bank']['nmb'] += $amt;
        } elseif (str_contains($meth, 'crdb')) {
            $breakdown['bank']['crdb'] += $amt;
        } elseif (str_contains($meth, 'kcb')) {
            $breakdown['bank']['kcb'] += $amt;
        } elseif (str_contains($meth, 'nbc')) {
            $breakdown['bank']['nbc'] += $amt;
        } elseif (str_contains($meth, 'dtb')) {
            $breakdown['bank']['dtb'] += $amt;
        } elseif (str_contains($meth, 'bank')) {
            $breakdown['bank']['other bank'] += $amt;
        } elseif (str_contains($meth, 'expedia')) {
            $breakdown['online']['expedia'] += $amt;
        } elseif (str_contains($meth, 'booking')) {
            $breakdown['online']['booking.com'] += $amt;
        } elseif (str_contains($meth, 'agoda')) {
            $breakdown['online']['agoda'] += $amt;
        } elseif (str_contains($meth, 'airbnb')) {
            $breakdown['online']['airbnb'] += $amt;
        } elseif (str_contains($meth, 'online')) {
            $breakdown['online']['other online'] += $amt;
        } elseif (str_contains($meth, 'card') || str_contains($meth, 'visa') || str_contains($meth, 'master')) {
            $breakdown['card']['visa/mastercard'] += $amt;
        } else {
            // Uncategorized to cash as fallback or skip? Usually cash is safe fallback in this hotel logic
            $breakdown['cash']['total'] += $amt;
        }
    }
}


