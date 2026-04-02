@extends('dashboard.layouts.app')

@section('content')
@php
  $isReception = ($role ?? '') === 'reception';
  $bookingsRoute = $isReception ? 'reception.bookings' : 'admin.bookings.index';
  $manualCreateRoute = $isReception ? 'reception.bookings.manual.create' : 'admin.bookings.manual.create';
  $dashboardRoute = $isReception ? route('reception.dashboard') : route('admin.dashboard');

  $activeQuick = request('quick_filter');
  $pageTitle = 'Bookings Management';
  $pageDesc = 'View and manage all hotel bookings';
  
  if ($activeQuick) {
      $titles = [
          'checkin_today' => 'Check-in Today',
          'checkout_today' => 'Check-out Today',
          'in_house' => 'In-House Now',
          'arriving_week' => 'Arriving This Week',
          'pending' => 'Pending (Unconfirmed)',
          'overdue' => 'Overdue Checkout'
      ];
      
      $pageTitle = $titles[$activeQuick] ?? 'Filtered Bookings';
      $pageDesc = 'Operational view: Showing ' . strtolower($pageTitle);
      
      $icons = [
          'checkin_today' => 'fa-sign-in',
          'checkout_today' => 'fa-sign-out',
          'in_house' => 'fa-home',
          'arriving_week' => 'fa-calendar',
          'pending' => 'fa-clock-o',
          'overdue' => 'fa-exclamation-triangle'
      ];
      $pageIcon = $icons[$activeQuick] ?? 'fa-calendar-check-o';
  } else {
      $pageIcon = 'fa-calendar-check-o';
  }
@endphp
<div class="app-title">
  <div>
    <h1><i class="fa {{ $pageIcon }}"></i> {{ $pageTitle }}</h1>
    <p>{{ $pageDesc }}</p>
  </div>
  <ul class="app-breadcrumb breadcrumb">
    <li class="breadcrumb-item"><i class="fa fa-home fa-lg"></i></li>
    <li class="breadcrumb-item"><a href="{{ $dashboardRoute }}">Dashboard</a></li>
    <li class="breadcrumb-item"><a href="#">Bookings</a></li>
  </ul>
</div>

<div class="row">
  <div class="col-md-12">
    <div class="tile">
      <div class="tile-title-w-btn mb-3">
        <h3 class="title">
          @if(request('status') == 'expired')
            Expired Bookings
          @else
            @if(($bookingType ?? 'individual') == 'corporate')
              Company Bookings
            @else
              Individual Bookings
            @endif
          @endif
        </h3>
        <div class="btn-group">
          @if(request('status') == 'expired')
            @if(($bookingType ?? 'individual') == 'corporate')
              <a class="btn btn-primary" href="{{ route('admin.bookings.corporate.create') }}">
                <i class="fa fa-plus"></i> Create Corporate Booking
              </a>
            @else
              <a class="btn btn-primary" href="{{ route($manualCreateRoute) }}">
                <i class="fa fa-plus"></i> Create Manual Booking
              </a>
            @endif
            <a class="btn btn-info" href="{{ route($bookingsRoute, request()->only(['type'])) }}">
              <i class="fa fa-list"></i> View All Bookings
            </a>
          @else
            @if(($bookingType ?? 'individual') == 'corporate')
              <a class="btn btn-primary" href="{{ route('admin.bookings.corporate.create') }}">
                <i class="fa fa-plus"></i> Create Corporate Booking
              </a>
            @else
              <a class="btn btn-primary" href="{{ route($manualCreateRoute) }}">
                <i class="fa fa-plus"></i> Create Manual Booking
              </a>
            @endif
          @endif
        </div>
      </div>
      
      <!-- Booking Type Tabs -->
      <div class="booking-tabs-wrapper mb-4">
        <ul class="nav nav-pills nav-justified" role="tablist" style="background: #f8f9fa; padding: 8px; border-radius: 8px;">
          <li class="nav-item">
            <a class="nav-link {{ ($bookingType ?? 'individual') == 'individual' ? 'active' : '' }}" 
               href="{{ route($bookingsRoute, array_merge(request()->except(['type']), ['type' => 'individual'])) }}"
               style="
                 color: {{ ($bookingType ?? 'individual') == 'individual' ? '#fff' : '#6c757d' }}; 
                 background-color: {{ ($bookingType ?? 'individual') == 'individual' ? '#e77a3a' : 'transparent' }};
                 border-radius: 6px;
                 padding: 10px 20px;
                 font-weight: {{ ($bookingType ?? 'individual') == 'individual' ? '600' : '400' }};
                 transition: all 0.3s ease;
               "
               onmouseover="this.style.backgroundColor='{{ ($bookingType ?? 'individual') == 'individual' ? '#e77a3a' : '#e9ecef' }}'"
               onmouseout="this.style.backgroundColor='{{ ($bookingType ?? 'individual') == 'individual' ? '#e77a3a' : 'transparent' }}'">
              <i class="fa fa-user"></i> Individual Bookings
              <span class="badge {{ ($bookingType ?? 'individual') == 'individual' ? 'badge-light' : 'badge-secondary' }} ml-2">{{ $stats['individual_total'] ?? 0 }}</span>
            </a>
          </li>
          <li class="nav-item">
            <a class="nav-link {{ ($bookingType ?? 'individual') == 'corporate' ? 'active' : '' }}" 
               href="{{ route($bookingsRoute, array_merge(request()->except(['type']), ['type' => 'corporate'])) }}"
               style="
                 color: {{ ($bookingType ?? 'individual') == 'corporate' ? '#fff' : '#6c757d' }}; 
                 background-color: {{ ($bookingType ?? 'individual') == 'corporate' ? '#e77a3a' : 'transparent' }};
                 border-radius: 6px;
                 padding: 10px 20px;
                 font-weight: {{ ($bookingType ?? 'individual') == 'corporate' ? '600' : '400' }};
                 transition: all 0.3s ease;
               "
               onmouseover="this.style.backgroundColor='{{ ($bookingType ?? 'individual') == 'corporate' ? '#e77a3a' : '#e9ecef' }}'"
               onmouseout="this.style.backgroundColor='{{ ($bookingType ?? 'individual') == 'corporate' ? '#e77a3a' : 'transparent' }}'">
              <i class="fa fa-building"></i> Company Bookings
              <span class="badge {{ ($bookingType ?? 'individual') == 'corporate' ? 'badge-light' : 'badge-secondary' }} ml-2">{{ $stats['corporate_total'] ?? 0 }}</span>
            </a>
          </li>
        </ul>
      </div>
      
      <!-- Statistics Cards -->
      @php
        $typeParam   = ($bookingType ?? 'individual') == 'corporate' ? 'corporate' : 'individual';
        $activeQuick = request('quick_filter', '');
        $isCorporate = ($typeParam == 'corporate');
      @endphp
      
      <!-- Consolidated Operational Statistics Cards -->
      <div class="row mb-4">
        {{-- Arriving Today --}}
        <div class="col-lg-3 col-md-6">
          <a href="{{ route($bookingsRoute, ['type' => $typeParam, 'quick_filter' => 'checkin_today']) }}" class="text-decoration-none">
            <div class="widget-small primary coloured-icon shadow-sm" style="border-radius: 12px; overflow: hidden; transition: transform 0.2s;">
              <i class="icon fa fa-plane fa-2x" style="background-color: #009688;"></i>
              <div class="info">
                <h4 class="text-uppercase font-weight-bold" style="font-size: 11px; letter-spacing: 1px;">Arriving Today</h4>
                <p style="font-size: 24px;"><b>{{ $stats['checkin_today'] ?? 0 }}</b></p>
                <small class="text-muted">Expected Check-ins</small>
              </div>
            </div>
          </a>
        </div>

        {{-- Departing Today --}}
        <div class="col-lg-3 col-md-6">
          <a href="{{ route($bookingsRoute, ['type' => $typeParam, 'quick_filter' => 'checkout_today']) }}" class="text-decoration-none">
            <div class="widget-small warning coloured-icon shadow-sm" style="border-radius: 12px; overflow: hidden; transition: transform 0.2s;">
              <i class="icon fa fa-sign-out fa-2x" style="background-color: #ff9800;"></i>
              <div class="info">
                <h4 class="text-uppercase font-weight-bold" style="font-size: 11px; letter-spacing: 1px;">Departing Today</h4>
                <p style="font-size: 24px;"><b>{{ $stats['checkout_today'] ?? 0 }}</b></p>
                <small class="text-muted">Expected Check-outs</small>
              </div>
            </div>
          </a>
        </div>

        {{-- In-House Now --}}
        <div class="col-lg-3 col-md-6">
          <a href="{{ route($bookingsRoute, ['type' => $typeParam, 'quick_filter' => 'in_house']) }}" class="text-decoration-none">
            <div class="widget-small info coloured-icon shadow-sm" style="border-radius: 12px; overflow: hidden; transition: transform 0.2s;">
              <i class="icon fa fa-users fa-2x" style="background-color: #17a2b8;"></i>
              <div class="info">
                <h4 class="text-uppercase font-weight-bold" style="font-size: 11px; letter-spacing: 1px;">In-House Now</h4>
                <p style="font-size: 24px;"><b>{{ $stats['checked_in'] ?? 0 }}</b></p>
                <small class="text-muted">Currently Staying</small>
              </div>
            </div>
          </a>
        </div>

        {{-- Overdue Checkout --}}
        <div class="col-lg-3 col-md-6">
          <a href="{{ route($bookingsRoute, ['type' => $typeParam, 'quick_filter' => 'overdue']) }}" class="text-decoration-none">
            <div class="widget-small danger coloured-icon shadow-sm" style="border-radius: 12px; overflow: hidden; transition: transform 0.2s;">
              <i class="icon fa fa-exclamation-triangle fa-2x" style="background-color: #e91e63;"></i>
              <div class="info">
                <h4 class="text-uppercase font-weight-bold" style="font-size: 11px; letter-spacing: 1px;">Overdue</h4>
                <p style="font-size: 24px;"><b>{{ $stats['overdue'] ?? 0 }}</b></p>
                <small class="text-muted">Late Check-outs</small>
              </div>
            </div>
          </a>
        </div>
      </div>
      
      <!-- Filters -->
      <!-- Sleek Modern Filters -->
      <div class="row mb-3">
        <div class="col-md-12">
          <div class="d-flex flex-wrap align-items-center bg-white p-3 shadow-sm border" style="border-radius: 12px; gap: 15px;">
            <div style="flex: 1; min-width: 250px;">
              <div class="input-group">
                <div class="input-group-prepend">
                  <span class="input-group-text bg-light border-right-0"><i class="fa fa-search text-muted"></i></span>
                </div>
                <input type="text" id="searchInput" class="form-control border-left-0 bg-light" 
                       placeholder="Find guests, email or reference..." 
                       value="{{ $filters['search'] ?? '' }}"
                       onkeyup="filterBookings()" onchange="applyServerFilters()" style="border-radius: 0 8px 8px 0;">
              </div>
            </div>
            
            <div style="width: 150px;">
              <select id="statusFilter" class="form-control custom-select bg-light" onchange="applyServerFilters()" style="border-radius: 8px;">
                <option value="all">All Status</option>
                <option value="pending" {{ ($filters['status'] ?? '') == 'pending' ? 'selected' : '' }}>Pending</option>
                <option value="confirmed" {{ ($filters['status'] ?? '') == 'confirmed' ? 'selected' : '' }}>Confirmed</option>
                <option value="cancelled" {{ ($filters['status'] ?? '') == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                <option value="completed" {{ ($filters['status'] ?? '') == 'completed' ? 'selected' : '' }}>Completed</option>
                <option value="expired" {{ ($filters['status'] ?? '') == 'expired' ? 'selected' : '' }}>Expired</option>
              </select>
            </div>
            
            <div style="width: 180px;">
              <select id="checkInStatusFilter" class="form-control custom-select bg-light" onchange="applyServerFilters()" style="border-radius: 8px;">
                <option value="all">Check-in Status</option>
                <option value="pending" {{ ($filters['check_in_status'] ?? '') == 'pending' ? 'selected' : '' }}>Pending Check-in</option>
                <option value="checked_in" {{ ($filters['check_in_status'] ?? '') == 'checked_in' ? 'selected' : '' }}>Checked In</option>
                <option value="checked_out" {{ ($filters['check_in_status'] ?? '') == 'checked_out' ? 'selected' : '' }}>Checked Out</option>
              </select>
            </div>
            
            <div style="width: 160px;">
              <select id="paymentStatusFilter" class="form-control custom-select bg-light" onchange="applyServerFilters()" style="border-radius: 8px;">
                <option value="all">Payment Status</option>
                <option value="pending" {{ ($filters['payment_status'] ?? '') == 'pending' ? 'selected' : '' }}>Unpaid</option>
                <option value="paid" {{ ($filters['payment_status'] ?? '') == 'paid' ? 'selected' : '' }}>Fully Paid</option>
                <option value="partial" {{ ($filters['payment_status'] ?? '') == 'partial' ? 'selected' : '' }}>Partial</option>
              </select>
            </div>
            
            {{-- Date Range Filter --}}
            <div class="d-flex align-items-center" style="gap: 5px;">
              <span class="text-muted small font-weight-bold" style="text-transform: uppercase; font-size: 10px; letter-spacing: 0.5px; white-space: nowrap;">Arrival Period:</span>
              <input type="date" id="startDate" class="form-control form-control-sm bg-light border" 
                     style="width: 135px; border-radius: 8px; font-size: 13px;" 
                     value="{{ $filters['start_date'] ?? '' }}" title="Start Date">
              <span class="text-muted small">&rarr;</span>
              <input type="date" id="endDate" class="form-control form-control-sm bg-light border" 
                     style="width: 135px; border-radius: 8px; font-size: 13px;" 
                     value="{{ $filters['end_date'] ?? '' }}" title="End Date">
              <button type="button" class="btn btn-primary btn-sm shadow-sm" onclick="applyServerFilters()" 
                      style="border-radius: 8px; padding: 5px 12px; font-weight: 600;">
                Go
              </button>
            </div>
            
            <div class="ml-auto">
              <button type="button" class="btn btn-outline-secondary btn-sm px-3 shadow-sm" onclick="resetFilters()" 
                      style="border-radius: 8px; padding: 5px 12px;" title="Reset Filters">
                <i class="fa fa-refresh mr-1"></i> Reset
              </button>
            </div>
          </div>
        </div>
      </div>

      <!-- ⚡ Quick Operational Filters -->
      @php
        // Variables defined above in the stats section
      @endphp
      <div class="mb-4 p-3 bg-white shadow-sm border" style="border-radius: 12px;">
        <div class="d-flex flex-wrap align-items-center" style="gap: 8px;">
          <span class="text-muted small font-weight-bold mr-1" style="letter-spacing: 0.5px; text-transform: uppercase; white-space: nowrap; font-size: 11px;">
            <i class="fa fa-bolt text-warning mr-1"></i> Quick View:
          </span>

          {{-- All --}}
          <a href="{{ route($bookingsRoute, ['type' => $typeParam]) }}"
             class="btn btn-sm {{ $activeQuick == '' ? 'btn-dark' : 'btn-outline-secondary' }}"
             style="border-radius: 20px; padding: 4px 14px; font-size: 12px; font-weight: 600;">
            <i class="fa fa-list mr-1"></i> All Bookings
          </a>

          {{-- Checking In Today --}}
          <a href="{{ route($bookingsRoute, ['type' => $typeParam, 'quick_filter' => 'checkin_today']) }}"
             class="btn btn-sm {{ $activeQuick == 'checkin_today' ? 'btn-primary' : 'btn-outline-primary' }}"
             style="border-radius: 20px; padding: 4px 14px; font-size: 12px; font-weight: 600;">
            <i class="fa fa-sign-in mr-1"></i> Check-in Today
            <span class="badge {{ $activeQuick == 'checkin_today' ? 'badge-light' : 'badge-primary' }} ml-1">{{ $stats['checkin_today'] ?? 0 }}</span>
          </a>

          {{-- Checking Out Today --}}
          <a href="{{ route($bookingsRoute, ['type' => $typeParam, 'quick_filter' => 'checkout_today']) }}"
             class="btn btn-sm {{ $activeQuick == 'checkout_today' ? 'btn-warning text-dark' : 'btn-outline-warning' }}"
             style="border-radius: 20px; padding: 4px 14px; font-size: 12px; font-weight: 600;">
            <i class="fa fa-sign-out mr-1"></i> Check-out Today
            <span class="badge {{ $activeQuick == 'checkout_today' ? 'badge-dark' : 'badge-warning' }} ml-1">{{ $stats['checkout_today'] ?? 0 }}</span>
          </a>

          {{-- Currently In-House --}}
          <a href="{{ route($bookingsRoute, ['type' => $typeParam, 'quick_filter' => 'in_house']) }}"
             class="btn btn-sm {{ $activeQuick == 'in_house' ? 'btn-success' : 'btn-outline-success' }}"
             style="border-radius: 20px; padding: 4px 14px; font-size: 12px; font-weight: 600;">
            <i class="fa fa-home mr-1"></i> In-House Now
            <span class="badge {{ $activeQuick == 'in_house' ? 'badge-light' : 'badge-success' }} ml-1">{{ $stats['checked_in'] ?? 0 }}</span>
          </a>

          {{-- Arriving This Week --}}
          <a href="{{ route($bookingsRoute, ['type' => $typeParam, 'quick_filter' => 'arriving_week']) }}"
             class="btn btn-sm {{ $activeQuick == 'arriving_week' ? 'btn-info' : 'btn-outline-info' }}"
             style="border-radius: 20px; padding: 4px 14px; font-size: 12px; font-weight: 600;">
            <i class="fa fa-calendar mr-1"></i> Arriving This Week
            <span class="badge {{ $activeQuick == 'arriving_week' ? 'badge-light' : 'badge-info' }} ml-1">{{ $stats['arriving_week'] ?? 0 }}</span>
          </a>

          {{-- Pending (Unconfirmed) --}}
          <a href="{{ route($bookingsRoute, ['type' => $typeParam, 'quick_filter' => 'pending']) }}"
             class="btn btn-sm {{ $activeQuick == 'pending' ? 'btn-secondary' : 'btn-outline-secondary' }}"
             style="border-radius: 20px; padding: 4px 14px; font-size: 12px; font-weight: 600;">
            <i class="fa fa-clock-o mr-1"></i> Pending
            <span class="badge {{ $activeQuick == 'pending' ? 'badge-light' : 'badge-secondary' }} ml-1">{{ $stats['pending'] ?? 0 }}</span>
          </a>

          {{-- Overdue Checkout --}}
          <a href="{{ route($bookingsRoute, ['type' => $typeParam, 'quick_filter' => 'overdue']) }}"
             class="btn btn-sm {{ $activeQuick == 'overdue' ? 'btn-danger' : 'btn-outline-danger' }}"
             style="border-radius: 20px; padding: 4px 14px; font-size: 12px; font-weight: 600;">
            <i class="fa fa-exclamation-triangle mr-1"></i> Overdue
            <span class="badge {{ $activeQuick == 'overdue' ? 'badge-light' : 'badge-danger' }} ml-1">{{ $stats['overdue'] ?? 0 }}</span>
          </a>

          @if($activeQuick)
            <span class="text-muted small ml-auto" style="font-size: 11px;">
              <i class="fa fa-filter mr-1"></i> Filtered View &mdash; <a href="{{ route($bookingsRoute, ['type' => $typeParam]) }}" class="text-danger">Clear</a>
            </span>
          @endif
        </div>
      </div>


      @if($bookings->count() > 0)
      <!-- Desktop Table View -->
      <div class="table-responsive">
        <table class="table table-hover table-bordered" id="bookingsTable">
          <thead>
            <tr>
              <th>Booking & Guest</th>
              <th>Room Details</th>
              <th>Stay Period</th>
              <th>Billing & Payment</th>
              <th>Current Status</th>
              <th class="text-center">Actions</th>
            </tr>
          </thead>
          <tbody>
            @if(($bookingType ?? 'individual') == 'corporate')
              @foreach($bookings as $group)
                @php
                  $company = $group['company'] ?? null;
                  $companyBookings = $group['bookings'] ?? collect();
                  $firstBooking = $group['first_booking'] ?? $companyBookings->first();
                  $totalGuests = $companyBookings->count();
                  $totalPrice = $companyBookings->sum(function($b) {
                      $svc = $b->serviceRequests ? $b->serviceRequests->whereIn('status', ['approved', 'completed']) : collect();
                      $svcTsh = $svc->sum('total_price_tsh');
                      $rate = $b->locked_exchange_rate ?? 2500;
                      return (float)$b->total_price + ($b->payment_responsibility !== 'self' ? ($svcTsh / $rate) : 0);
                  });
                  $roomCount = $companyBookings->unique('room_id')->count();
                  $totalNights = $firstBooking ? $firstBooking->check_in->diffInDays($firstBooking->check_out) : 0;
                  $allCheckedOut = $companyBookings->every(function($b) { return ($b->check_in_status ?? 'pending') == 'checked_out'; });
                @endphp
                <tr class="booking-row corporate-booking-group"
                    data-status="{{ $firstBooking->status ?? 'pending' }}"
                    data-check-in-status="{{ $firstBooking->check_in_status ?? 'pending' }}"
                    data-payment-status="{{ $firstBooking->payment_status ?? 'pending' }}"
                    data-company-id="{{ $company->id ?? '' }}"
                    style="background-color: #fcfcfc;">
                  <td class="align-middle">
                    <div class="d-flex align-items-center">
                      <div class="bg-light rounded-circle p-2 mr-3 d-flex align-items-center justify-content-center" style="width: 40px; height: 40px; border: 1px solid #e2e8f0;">
                        <i class="fa fa-building text-info"></i>
                      </div>
                      <div>
                        <strong class="text-dark">{{ $firstBooking->booking_reference ?? 'N/A' }}</strong>
                        <br><strong class="text-primary">{{ $company->name ?? 'N/A' }}</strong>
                        <br><small class="badge badge-info">{{ $totalGuests }} Guests</small>
                      </div>
                    </div>
                  </td>
                  <td class="align-middle">
                    <span class="badge badge-primary px-3 shadow-sm mb-1" style="border-radius: 10px;">{{ $roomCount }} Rooms</span>
                    <br><small class="text-muted">Mixed Room Types</small>
                  </td>
                  <td class="align-middle">
                     <div class="text-dark font-weight-bold">{{ $firstBooking->check_in->format('M d') }} - {{ $firstBooking->check_out->format('M d, Y') }}</div>
                     <div class="text-muted small"><i class="fa fa-moon-o"></i> {{ $totalNights }} Nights</div>
                  </td>
                  <td class="align-middle">
                     <div class="font-weight-bold text-dark" style="font-size: 1.1em;">${{ number_format($totalPrice, 2) }}</div>
                     @php
                        $hasCompanyPays = $companyBookings->where('payment_responsibility', 'company')->count() > 0;
                        $hasSelfPays = $companyBookings->where('payment_responsibility', 'self')->count() > 0;
                     @endphp
                     <span class="badge badge-light border mt-1">{{ $hasCompanyPays && $hasSelfPays ? 'Mixed Billing' : ($hasCompanyPays ? 'Company Pays' : 'Self-Paid') }}</span>
                  </td>
                  <td class="align-middle">
                    @php
                      $allCompleted = $companyBookings->every(function($b) { return $b->status == 'completed' || ($b->check_in_status == 'checked_out'); });
                      $allConfirmed = $companyBookings->every(function($b) { return $b->status == 'confirmed' && $b->check_in_status != 'checked_out'; });
                    @endphp
                    <div class="mb-1">
                      <span class="badge border-{{ $allCompleted ? 'info' : ($allConfirmed ? 'success' : 'warning') }} text-{{ $allCompleted ? 'info' : ($allConfirmed ? 'success' : 'warning') }} border" style="width: 100px;">
                        {{ $allCompleted ? 'COMPLETED' : ($allConfirmed ? 'CONFIRMED' : 'PENDING') }}
                      </span>
                    </div>
                    <span class="badge border-{{ $allCheckedOut ? 'success' : 'secondary' }} text-{{ $allCheckedOut ? 'success' : 'secondary' }} border" style="width: 100px;">
                      {{ $allCheckedOut ? 'CHECKED OUT' : 'IN PROGRESS' }}
                    </span>
                  </td>
                  <td class="text-center align-middle">
                    <div class="dropdown">
                      <button class="btn btn-primary btn-sm dropdown-toggle shadow-sm text-white border-0" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        Actions
                      </button>
                      <div class="dropdown-menu dropdown-menu-right shadow border-0" style="border-radius: 8px;">
                        <a class="dropdown-item py-2" href="javascript:void(0)" onclick="viewCompanyBookingGroup({{ $company->id ?? 'null' }}, {{ $firstBooking->id }})">
                          <i class="fa fa-eye text-primary mr-2"></i> View Group
                        </a>
                        
                        @if(!$allCheckedOut)
                          <a class="dropdown-item py-2" href="javascript:void(0)" onclick="openGroupExtensionModal({{ $company->id ?? 'null' }}, '{{ $firstBooking->check_in->format('Y-m-d') }}', '{{ $firstBooking->check_out->format('Y-m-d') }}')">
                            <i class="fa fa-calendar-plus-o text-info mr-2"></i> Extend Stay
                          </a>
                        @endif

                        @if($allConfirmed)
                          <a class="dropdown-item py-2" href="{{ route('payment.receipt.download', $firstBooking) }}?download=1" target="_blank">
                            <i class="fa fa-download text-success mr-2"></i> Download Receipt
                          </a>
                        @endif

                        <div class="dropdown-divider"></div>

                        @if(!$allCheckedOut && ($firstBooking->status ?? '') != 'cancelled')
                          <a class="dropdown-item py-2 text-danger" href="javascript:void(0)" onclick="openCancelGroupModal({{ $company->id ?? 'null' }})">
                            <i class="fa fa-times mr-2"></i> Cancel Entire Group
                          </a>
                        @endif
                      </div>
                    </div>
                  </td>
>
                </tr>
              @endforeach
            @endif
            @if(($bookingType ?? 'individual') == 'individual')
              @foreach($bookings as $booking)
              @php
                $isTanzanian = ($booking->guest_type === 'tanzanian');
                $rate = $booking->locked_exchange_rate ?? $exchangeRate ?? 2500;
                $serviceChargesTsh = $booking->serviceRequests->where('status', '!=', 'cancelled')->sum('total_price_tsh');
                
                if ($isTanzanian) {
                    $totalBillTsh = (float)$booking->total_price + $serviceChargesTsh;
                    $displayPrice = "TZS " . number_format($totalBillTsh, 0);
                } else {
                    $totalBillUsd = (float)$booking->total_price + ($serviceChargesTsh / $rate);
                    $displayPrice = "$" . number_format($totalBillUsd, 2);
                }
                $totalNights = $booking->check_in->diffInDays($booking->check_out);
              @endphp
              <tr class="booking-row"
                  data-status="{{ $booking->status }}"
                  data-check-in-status="{{ $booking->check_in_status ?? 'pending' }}"
                  data-payment-status="{{ $booking->payment_status ?? 'pending' }}"
                  data-booking-ref="{{ strtolower($booking->booking_reference) }}"
                  data-guest-name="{{ strtolower($booking->guest_name) }}"
                  data-guest-email="{{ strtolower($booking->guest_email) }}">
                <td class="align-middle">
                  <div class="d-flex align-items-center">
                    <div class="bg-light rounded-circle p-2 mr-3 d-flex align-items-center justify-content-center" style="width: 40px; height: 40px; border: 1px solid #e2e8f0;">
                      <i class="fa fa-{{ ($bookingType ?? 'individual') == 'corporate' ? 'building text-info' : 'user text-primary' }}"></i>
                    </div>
                    <div>
                      <strong class="text-dark">{{ $booking->booking_reference }}</strong>
                      <br><strong>{{ $booking->guest_name }}</strong>
                      <br><small class="text-muted"><i class="fa fa-envelope-o"></i> {{ $booking->guest_email }}</small>
                      @if($booking->guest_phone)
                        <br><small class="text-muted"><i class="fa fa-phone"></i> {{ $booking->guest_phone }}</small>
                      @endif
                    </div>
                  </div>
                </td>
                <td class="align-middle">
                  <span class="badge badge-primary px-3 shadow-sm mb-1" style="border-radius: 10px;">{{ $booking->room->room_type ?? 'N/A' }}</span>
                  <br><strong class="text-dark" style="font-size: 1.1em;">Room {{ $booking->room->room_number ?? 'N/A' }}</strong>
                </td>
                <td class="align-middle">
                   <div class="text-dark font-weight-bold">{{ $booking->check_in->format('M d') }} - {{ $booking->check_out->format('M d, Y') }}</div>
                   <div class="text-muted small"><i class="fa fa-moon-o"></i> {{ $totalNights }} Nights</div>
                   @php
                      $today = \Carbon\Carbon::today();
                      $checkOut = \Carbon\Carbon::parse($booking->check_out);
                      $daysRemaining = $today->diffInDays($checkOut, false);
                   @endphp
                   @if(($booking->check_in_status ?? 'pending') === 'checked_out')
                      <span class="badge badge-light text-success border-success border mt-1"><i class="fa fa-check"></i> Finished</span>
                   @elseif($daysRemaining > 0)
                      <span class="badge badge-light text-info border-info border mt-1"><i class="fa fa-clock-o"></i> {{ $daysRemaining }} days left</span>
                   @elseif($daysRemaining == 0)
                      <span class="badge badge-light text-warning border-warning border mt-1"><i class="fa fa-exclamation-circle"></i> Out Today</span>
                   @else
                      <span class="badge badge-light text-danger border-danger border mt-1"><i class="fa fa-warning"></i> {{ abs($daysRemaining) }}d Overdue</span>
                   @endif
                </td>
                <td class="align-middle">
                    <div class="font-weight-bold text-dark" style="font-size: 1.1em;">
                      {{ $displayPrice }}
                    </div>
                    @if($serviceChargesTsh > 0)
                      <div class="small text-muted" style="line-height: 1.1;">
                        Room: {{ $isTanzanian ? number_format($booking->total_price, 0) . ' TZS' : '$' . number_format($booking->total_price, 2) }}
                        @foreach($booking->serviceRequests->where('status', '!=', 'cancelled') as $req)
                          @php
                            $svcName = $req->service ? $req->service->name : 'Other Service';
                            $svcPrice = $isTanzanian ? number_format($req->total_price_tsh, 0) . ' TZS' : '$' . number_format($req->total_price_tsh / $rate, 2);
                          @endphp
                          <br>{{ $svcName }}: {{ $svcPrice }}
                        @endforeach
                      </div>
                    @endif
                    @php
                      $pStatus = $booking->payment_status;
                      $pClass = match($pStatus) {
                        'paid' => 'success',
                        'partial' => 'info',
                        'pending' => 'warning',
                        default => 'secondary'
                      };
                    @endphp
                    <span class="badge badge-{{ $pClass }} mt-1 px-3" style="border-radius: 4px;">{{ strtoupper($pStatus) }}</span>
                    @if($booking->payment_method)
                      <div class="small text-muted mt-1">{{ ucfirst($booking->payment_method) }}</div>
                    @endif
                </td>
                <td class="align-middle">
                  @php
                    $bStatus = $booking->status;
                    $cStatus = $booking->check_in_status ?? 'pending';
                    $bClass = match($bStatus) {
                      'confirmed' => 'success',
                      'pending' => 'warning',
                      'completed' => 'info',
                      'cancelled' => 'danger',
                      default => 'secondary'
                    };
                  @endphp
                  <div class="mb-1">
                    <span class="badge border-{{ $bClass }} text-{{ $bClass }} border" style="width: 100px;">{{ strtoupper($bStatus) }}</span>
                  </div>
                  <div>
                    <span class="badge border-{{ $cStatus == 'checked_in' ? 'info' : ($cStatus == 'checked_out' ? 'success' : 'secondary') }} text-{{ $cStatus == 'checked_in' ? 'info' : ($cStatus == 'checked_out' ? 'success' : 'secondary') }} border" style="width: 100px;">
                      {{ strtoupper(str_replace('_', ' ', $cStatus)) }}
                    </span>
                  </div>
                </td>
                <td class="text-center align-middle">
                  <div class="dropdown">
                    <button class="btn btn-info btn-sm dropdown-toggle shadow-sm text-white border-0" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" style="background-color: #3e819b;">
                      Actions
                    </button>
                    <div class="dropdown-menu dropdown-menu-right shadow border-0" style="border-radius: 8px;">
                      <a class="dropdown-item py-2" href="javascript:void(0)" onclick="viewBooking({{ $booking->id }})">
                        <i class="fa fa-eye text-primary mr-2"></i> View Details
                      </a>

                      @if(($booking->check_in_status ?? 'pending') === 'checked_in')
                        <a class="dropdown-item py-2" href="javascript:void(0)" onclick="openManagerExtensionModal({{ $booking->id }}, '{{ $booking->check_in->format('Y-m-d') }}', '{{ $booking->check_out->format('Y-m-d') }}')">
                          <i class="fa fa-calendar-plus-o text-info mr-2"></i> Extend Stay
                        </a>
                        <a class="dropdown-item py-2" href="javascript:void(0)" onclick="openLateCheckoutModal({{ $booking->id }}, '{{ $booking->guest_name }}', '{{ $booking->room->room_number ?? '' }}')">
                          <i class="fa fa-clock-o text-warning mr-2"></i> Late Check-out
                        </a>
                      @endif

                      @if(in_array($booking->payment_status, ['paid', 'partial']) || $booking->status == 'confirmed')
                        <a class="dropdown-item py-2" href="{{ route('payment.receipt.download', $booking) }}?download=1" target="_blank">
                          <i class="fa fa-download text-success mr-2"></i> Receipt
                        </a>
                      @endif

                      <a class="dropdown-item py-2" href="javascript:void(0)" onclick="showNotesModal({{ $booking->id }})">
                        <i class="fa fa-sticky-note text-secondary mr-2"></i> Notes
                      </a>

                      <div class="dropdown-divider"></div>

                      @if($booking->status != 'cancelled' && ($booking->check_in_status ?? 'pending') === 'pending')
                        <a class="dropdown-item py-2 text-danger font-weight-bold" href="javascript:void(0)" onclick="openCancelModal({{ $booking->id }})">
                          <i class="fa fa-times mr-2"></i> Cancel Booking
                        </a>
                      @endif
                    </div>
                  </div>
                </td>
              </tr>
              @endforeach
            @endif
          </tbody>
        </table>
      </div>
      
      <!-- Sleek Mobile Card View (Hidden on Desktop) -->
      <div class="mobile-booking-cards d-md-none">
        @if(($bookingType ?? 'individual') == 'corporate')
          @foreach($bookings as $group)
            @php
              $company = $group['company'] ?? null;
              $companyBookings = $group['bookings'] ?? collect();
              $firstBooking = $group['first_booking'] ?? $companyBookings->first();
              $totalGuests = $companyBookings->count();
              $totalPrice = $companyBookings->sum(function($b) {
                  $svc = $b->serviceRequests ? $b->serviceRequests->whereIn('status', ['approved', 'completed']) : collect();
                  $rate = $b->locked_exchange_rate ?? 2500;
                  return (float)$b->total_price + ($b->payment_responsibility !== 'self' ? ($svc->sum('total_price_tsh') / $rate) : 0);
              });
              $allCheckedOut = $companyBookings->every(function($b) { return ($b->check_in_status ?? 'pending') == 'checked_out'; });
            @endphp
            <div class="card shadow-sm mb-3" style="border-radius: 12px; border: none; overflow: hidden; border-left: 4px solid #17a2b8;">
              <div class="card-header bg-light border-bottom-0 d-flex justify-content-between align-items-center p-3">
                <div style="max-width: 70%;">
                  <small class="text-muted d-block text-uppercase font-weight-bold" style="font-size: 10px; letter-spacing: 0.5px;">{{ $firstBooking->booking_reference }}</small>
                  <h6 class="mb-0 text-primary font-weight-bold text-truncate">{{ $company->name ?? 'N/A' }}</h6>
                </div>
                <span class="badge badge-info shadow-sm" style="border-radius: 20px; padding: 5px 12px;">{{ $totalGuests }} Guests</span>
              </div>
              <div class="card-body p-3">
                <div class="row align-items-center">
                  <div class="col-7">
                    <div class="small text-muted mb-1"><i class="fa fa-calendar-o"></i> {{ $firstBooking->check_in->format('M d') }} - {{ $firstBooking->check_out->format('M d') }}</div>
                    <div class="font-weight-bold text-dark" style="font-size: 1.1rem;">${{ number_format($totalPrice, 2) }}</div>
                  </div>
                  <div class="col-5 text-right">
                    <span class="badge border-{{ $allCheckedOut ? 'success' : 'primary' }} text-{{ $allCheckedOut ? 'success' : 'primary' }} border px-2 py-1" style="font-size: 10px; letter-spacing: 0.5px;">
                      {{ strtoupper($allCheckedOut ? 'FINISHED' : 'IN PROGRESS') }}
                    </span>
                  </div>
                </div>
              </div>
              <div class="card-footer bg-white border-top-0 p-2 d-flex justify-content-between px-3">
                <button class="btn btn-link btn-sm text-primary font-weight-bold p-0" onclick="viewCompanyBookingGroup({{ $company->id ?? 'null' }}, {{ $firstBooking->id }})">
                  <i class="fa fa-eye"></i> Details
                </button>
                <a href="{{ route('payment.receipt.download', $firstBooking) }}?download=1" class="btn btn-link btn-sm text-success font-weight-bold p-0">
                  <i class="fa fa-file-text-o"></i> Receipt
                </a>
              </div>
            </div>
          @endforeach
        @else
          @foreach($bookings as $booking)
            @php
              $isTanzanian = ($booking->guest_type === 'tanzanian');
              $rate = $booking->locked_exchange_rate ?? $exchangeRate ?? 2500;
              $serviceChargesTsh = $booking->serviceRequests->where('status', '!=', 'cancelled')->sum('total_price_tsh');
              
              if ($isTanzanian) {
                  $totalBillTsh = (float)$booking->total_price + $serviceChargesTsh;
                  $displayPrice = "TZS " . number_format($totalBillTsh, 0);
              } else {
                  $totalBillUsd = (float)$booking->total_price + ($serviceChargesTsh / $rate);
                  $displayPrice = "$" . number_format($totalBillUsd, 2);
              }
            @endphp
            <div class="card shadow-sm mb-3" style="border-radius: 12px; border: none; overflow: hidden; border-left: 4px solid {{ $booking->status == 'confirmed' ? '#28a745' : '#ffc107' }};">
              <div class="card-header bg-light border-bottom-0 d-flex justify-content-between align-items-center p-3">
                <div style="max-width: 70%;">
                  <small class="text-muted d-block text-uppercase font-weight-bold" style="font-size: 10px; letter-spacing: 0.5px;">{{ $booking->booking_reference }}</small>
                  <h6 class="mb-0 font-weight-bold text-truncate text-dark">{{ $booking->guest_name }}</h6>
                </div>
                <span class="badge badge-primary px-3 shadow-sm" style="border-radius: 20px;">Room {{ $booking->room->room_number ?? 'N/A' }}</span>
              </div>
              <div class="card-body p-3">
                <div class="row align-items-center mb-2">
                  <div class="col-7 text-muted small">
                    <i class="fa fa-calendar-o"></i> {{ $booking->check_in->format('M d') }} - {{ $booking->check_out->format('M d, Y') }}
                  </div>
                  <div class="col-5 text-right font-weight-bold text-dark" style="font-size: 1.05rem;">
                    {{ $displayPrice }}
                  </div>
                </div>
                <div class="d-flex justify-content-between align-items-center mt-3">
                  <span class="badge border-{{ $booking->status == 'confirmed' ? 'success' : 'warning' }} text-{{ $booking->status == 'confirmed' ? 'success' : 'warning' }} border px-2 py-1" style="font-size: 10px; letter-spacing: 0.5px;">
                    {{ strtoupper($booking->status) }}
                  </span>
                  <span class="badge badge-{{ $booking->payment_status == 'paid' ? 'success' : ($booking->payment_status == 'partial' ? 'info' : 'light border') }} px-2 py-1" style="font-size: 10px; letter-spacing: 0.5px;">
                    {{ strtoupper($booking->payment_status) }}
                  </span>
                </div>
              </div>
              <div class="card-footer bg-white border-top-0 p-2 d-flex justify-content-between px-3">
                <button class="btn btn-link btn-sm text-primary font-weight-bold p-0" onclick="viewBooking({{ $booking->id }})"><i class="fa fa-eye"></i> View</button>
                @if(in_array($booking->payment_status, ['paid', 'partial']) || $booking->status == 'confirmed')
                  <a href="{{ route('payment.receipt.download', $booking) }}?download=1" class="btn btn-link btn-sm text-success font-weight-bold p-0">
                    <i class="fa fa-file-text-o"></i> Receipt
                  </a>
                @endif
                <button class="btn btn-link btn-sm text-secondary font-weight-bold p-0" onclick="showNotesModal({{ $booking->id }})"><i class="fa fa-sticky-note-o"></i> Notes</button>
              </div>
            </div>
          @endforeach
        @endif
      </div>
      
      <!-- Pagination -->
      <div class="d-flex justify-content-center mt-3">
        {{ $bookings->appends(request()->query())->links('pagination::bootstrap-4') }}
      </div>
      @else
      <div class="alert alert-info text-center">
        <i class="fa fa-info-circle fa-2x mb-3"></i>
        <h4>No bookings found</h4>
        <p>There are no bookings matching your criteria.</p>
      </div>
      @endif
    </div>
  </div>
</div>

<!-- Booking Details Modal -->
<div class="modal fade" id="bookingDetailsModal" tabindex="-1" role="dialog">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header" style="background: #e77a3a; color: white;">
        <h5 class="modal-title"><i class="fa fa-info-circle"></i> Booking Details & Financials</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close" style="color: white;">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body" id="bookingDetailsContent">
        <!-- Content will be loaded here -->
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>

<!-- Manager Extension Modal -->
<div class="modal fade" id="managerExtensionModal" tabindex="-1" role="dialog">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header" style="background: #17a2b8; color: white;">
        <h5 class="modal-title"><i class="fa fa-calendar-plus-o"></i> Extend Booking</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close" style="color: white;">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <form id="managerExtensionForm">
          <input type="hidden" id="manager_extension_booking_id" name="booking_id">
          <input type="hidden" id="manager_extension_company_id" name="company_id">
          <div class="alert alert-info">
            <i class="fa fa-info-circle"></i> <strong>Note:</strong> The booking will be extended and the price will be adjusted automatically.
          </div>
          <div class="form-group">
            <label for="manager_extension_new_check_out">New Check-out Date *</label>
            <input type="date" class="form-control" id="manager_extension_new_check_out" name="new_check_out" required>
            <small class="form-text text-muted">Select a date after the current check-out date.</small>
          </div>
          <div class="form-group">
            <label for="manager_extension_reason">Reason (Optional)</label>
            <textarea class="form-control" id="manager_extension_reason" name="reason" rows="3" placeholder="Reason for extending the booking..."></textarea>
          </div>
          <div id="managerExtensionCostPreview" style="display: none; padding: 15px; background: #f8f9fa; border-radius: 5px; margin-bottom: 15px;">
            <p class="mb-0">
              <span id="managerExtensionNights">0</span> additional night(s) × 
              $<span id="managerExtensionRoomPrice">0</span> per night = 
              <strong>Additional Cost: $<span id="managerExtensionTotalCost">0</span></strong>
            </p>
          </div>
          <div id="managerExtensionAlert"></div>
        </form>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
        <button type="button" class="btn btn-info" onclick="submitManagerExtension(this)">
          <i class="fa fa-save"></i> Extend Booking
        </button>
      </div>
    </div>
  </div>
</div>

<!-- Manager Decrease Modal -->
<div class="modal fade" id="managerDecreaseModal" tabindex="-1" role="dialog">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header" style="background: #ffc107; color: white;">
        <h5 class="modal-title"><i class="fa fa-calendar-minus-o"></i> Decrease Booking</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close" style="color: white;">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <form id="managerDecreaseForm">
          <input type="hidden" id="manager_decrease_booking_id" name="booking_id">
          <input type="hidden" id="manager_decrease_company_id" name="company_id">
          <div class="alert alert-warning">
            <i class="fa fa-info-circle"></i> <strong>Note:</strong> The booking stay will be decreased. Please note that no refund or price adjustment will be applied as per the hotel policy.
          </div>
          <div class="form-group">
            <label for="manager_decrease_new_check_out">New Check-out Date *</label>
            <input type="date" class="form-control" id="manager_decrease_new_check_out" name="new_check_out" required>
            <small class="form-text text-muted">Select a date before the current check-out date.</small>
          </div>
          <div class="form-group">
            <label for="manager_decrease_reason">Reason (Optional)</label>
            <textarea class="form-control" id="manager_decrease_reason" name="reason" rows="3" placeholder="Reason for decreasing the booking..."></textarea>
          </div>
          <div id="managerDecreaseAlert"></div>
        </form>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
        <button type="button" class="btn btn-warning" onclick="submitManagerDecrease(this)">
          <i class="fa fa-save"></i> Decrease Booking
        </button>
      </div>
    </div>
  </div>
</div>

<!-- Admin Notes Modal -->
<div class="modal fade" id="notesModal" tabindex="-1" role="dialog">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Admin Notes</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <form id="notesForm">
          <input type="hidden" id="notes_booking_id" name="booking_id">
          <div class="form-group">
            <label for="admin_notes">Notes (Internal use only)</label>
            <textarea class="form-control" id="admin_notes" name="admin_notes" rows="5" placeholder="Add internal notes about this booking..."></textarea>
            <small class="form-text text-muted">These notes are only visible to managers.</small>
          </div>
        </form>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
        <button type="button" class="btn btn-primary" onclick="saveNotes()">Save Notes</button>
      </div>
    </div>
  </div>
</div>

<!-- Company Booking Group Modal -->
<div class="modal fade" id="companyBookingGroupModal" tabindex="-1" role="dialog">
  <div class="modal-dialog" role="document" style="max-width: 95%; width: 95%;">
    <div class="modal-content">
      <div class="modal-header" style="background: #e77a3a; color: white;">
        <h5 class="modal-title"><i class="fa fa-building"></i> Company Booking Details</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close" style="color: white;">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body" id="companyBookingGroupContent">
        <div class="text-center">
          <i class="fa fa-spinner fa-spin fa-3x"></i>
          <p>Loading booking details...</p>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>

<!-- Cancel Booking Modal -->
<div class="modal fade" id="cancelBookingModal" tabindex="-1" role="dialog">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Cancel Booking</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <form id="cancelBookingForm">
          <input type="hidden" id="cancel_booking_id" name="booking_id">
          <input type="hidden" id="cancel_company_id" name="company_id">
          <div class="form-group">
            <label for="cancellation_reason">Cancellation Reason *</label>
            <textarea class="form-control" id="cancellation_reason" name="cancellation_reason" rows="4" required placeholder="Enter reason for cancellation..."></textarea>
            <small class="form-text text-muted">This reason will be visible to the customer.</small>
          </div>
        </form>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
        <button type="button" class="btn btn-danger" onclick="confirmCancel()">Cancel Booking</button>
      </div>
    </div>
  </div>
</div>

<!-- Late Check-out Modal -->
<div class="modal fade" id="lateCheckoutModal" tabindex="-1" role="dialog">
  <div class="modal-dialog" role="document">
    <div class="modal-content shadow-lg border-0" style="border-radius: 12px;">
      <div class="modal-header bg-warning text-dark" style="border-radius: 12px 12px 0 0;">
        <h5 class="modal-title font-weight-bold"><i class="fa fa-clock-o mr-2"></i> Late Check-out Charge</h5>
        <button type="button" class="close text-dark" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body p-4">
        <div class="alert alert-info py-2 small mb-3">
          <i class="fa fa-info-circle mr-1"></i> Add additional hours and the associated fee for late checkout.
        </div>
        <form id="lateCheckoutForm">
          <input type="hidden" id="late_checkout_booking_id">
          
          <div class="form-group mb-3 text-center">
            <span class="badge badge-light px-3 py-2 border" id="lateCheckoutGuestInfo" style="font-size: 0.9rem;"></span>
          </div>

          <div class="row">
            <div class="col-md-6">
              <div class="form-group">
                <label class="font-weight-bold small text-uppercase" for="late_checkout_hours">Extra Hours *</label>
                <div class="input-group">
                  <input type="number" class="form-control text-center" id="late_checkout_hours" step="0.5" min="0" placeholder="e.g. 2" required>
                  <div class="input-group-append">
                    <span class="input-group-text bg-light border-left-0"><i class="fa fa-hourglass-half"></i></span>
                  </div>
                </div>
              </div>
            </div>
            <div class="col-md-6">
              <div class="form-group">
                <label class="font-weight-bold small text-uppercase" for="late_checkout_amount">Charge Amount (TZS) *</label>
                <div class="input-group">
                  <input type="number" class="form-control text-right pr-3" id="late_checkout_amount" min="0" placeholder="e.g. 20000" required>
                  <div class="input-group-append">
                    <span class="input-group-text bg-light border-left-0"><strong>TSh</strong></span>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <div class="form-group mb-0">
            <label class="font-weight-bold small text-uppercase" for="late_checkout_notes">Internal Notes</label>
            <textarea class="form-control" id="late_checkout_notes" rows="2" placeholder="Reason for late check-out..."></textarea>
          </div>
        </form>
      </div>
      <div class="modal-footer border-top-0 pt-0 px-4 pb-4">
        <button type="button" class="btn btn-light px-4 border" data-dismiss="modal">Cancel</button>
        <button type="button" class="btn btn-warning px-4 font-weight-bold shadow-sm" onclick="submitLateCheckout()">Apply Charge</button>
      </div>
    </div>
  </div>
</div>
@endsection

@section('scripts')
<script src="{{ asset('dashboard_assets/js/plugins/sweetalert.min.js') }}"></script>
<style>
.badge-warning {
  background-color: #ffc107;
  color: #212529;
}

.badge-success {
  background-color: #28a745;
  color: white;
}

.badge-danger {
  background-color: #dc3545;
  color: white;
}

.badge-info {
  background-color: #17a2b8;
  color: white;
}

.badge-secondary {
  background-color: #6c757d;
  color: white;
}

/* Rounded borders for all widget-small cards */
.widget-small,
.widget-small.success,
.widget-small.success.coloured-icon,
.widget-small.primary,
.widget-small.primary.coloured-icon,
.widget-small.info,
.widget-small.info.coloured-icon,
.widget-small.warning,
.widget-small.warning.coloured-icon,
.widget-small.danger,
.widget-small.danger.coloured-icon,
.widget-small.dark,
.widget-small.dark.coloured-icon {
  border-radius: 8px !important;
  overflow: hidden;
}

/* Dark widget-small style for zero values */
.widget-small.dark.coloured-icon {
  background-color: #fff;
  color: #2a2a2a;
  border: 1px solid #2a2a2a;
}

.widget-small.dark.coloured-icon .icon {
  background-color: #2a2a2a;
  color: #fff;
  border-radius: 8px 0 0 8px !important;
}

/* Ensure icon border-radius matches card border-radius */
.widget-small.success.coloured-icon .icon,
.widget-small.primary.coloured-icon .icon,
.widget-small.info.coloured-icon .icon,
.widget-small.warning.coloured-icon .icon,
.widget-small.danger.coloured-icon .icon {
  border-radius: 8px 0 0 8px !important;
}

.btn-group {
  display: flex;
  gap: 5px;
}

.btn-group .btn {
  margin: 0;
}

.booking-details-view {
  padding: 10px;
}

.preview-container {
  background-color: #f8f9fa;
  padding: 15px;
  border-radius: 8px;
}

.preview-section {
  background-color: white;
  padding: 20px;
  border-radius: 8px;
  box-shadow: 0 2px 4px rgba(0,0,0,0.1);
  margin-bottom: 20px;
}

.preview-section:last-child {
  margin-bottom: 0;
}

.preview-section h5 {
  color: #e77a3a;
  margin-bottom: 15px;
  font-weight: 600;
  border-bottom: 2px solid #e77a3a;
  padding-bottom: 8px;
}

.preview-section table {
  margin-bottom: 0;
}

.preview-section th {
  background-color: #fcfcfc;
  width: 35%;
  color: #666;
  font-weight: 600;
}

.booking-details-view .table-sm td {
  padding: 8px;
}

.company-booking-group-view .card {
  margin-bottom: 20px;
}

.company-booking-group-view .card-header h6 {
  font-size: 16px;
  font-weight: 600;
}

.company-booking-group-view .table-sm {
  font-size: 13px;
}

.company-booking-group-view .table-sm td {
  padding: 8px 10px;
  vertical-align: middle;
}

.company-booking-group-view h6 {
  font-size: 14px;
  font-weight: 600;
  margin-bottom: 10px;
}

#bookingsTable {
  font-size: 14px;
}

#bookingsTable td {
  vertical-align: middle;
}

.dropdown-menu {
  min-width: 150px;
}

.dropdown-item {
  cursor: pointer;
}

.dropdown-item:hover {
  background-color: #f8f9fa;
}

/* Mobile Responsive Styles */
@media (max-width: 767px) {
  /* Title and Button Group - Mobile */
  .tile-title-w-btn {
    flex-direction: column;
    align-items: flex-start !important;
  }
  
  .tile-title-w-btn .title {
    margin-bottom: 15px;
    width: 100%;
  }
  
  .tile-title-w-btn .btn-group {
    width: 100%;
    flex-direction: column;
    gap: 10px;
  }
  
  .tile-title-w-btn .btn-group .btn {
    width: 100%;
    margin: 0;
  }
  
  /* Statistics Cards - Mobile */
  .col-lg-3.col-md-6.col-sm-6 {
    margin-bottom: 15px;
  }
  
  /* Filters - Mobile */
  .row .col-md-2,
  .row .col-md-4 {
    flex: 0 0 100%;
    max-width: 100%;
    margin-bottom: 15px;
  }
  
  .form-group label {
    font-size: 14px;
    font-weight: 500;
    margin-bottom: 5px;
  }
  
  select.form-control,
  input.form-control {
    font-size: 16px; /* Prevents zoom on iOS */
    padding: 12px;
    min-height: 48px;
  }
  
  /* Table - Convert to Cards on Mobile */
  .table-responsive {
    overflow-x: visible;
  }
  
  #bookingsTable {
    display: none; /* Hide table on mobile */
  }
  
  /* Mobile Booking Cards */
  .mobile-booking-cards {
    display: block;
  }
  
  .mobile-booking-card {
    background: white;
    border: 1px solid #dee2e6;
    border-radius: 8px;
    padding: 15px;
    margin-bottom: 15px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
  }
  
  .mobile-booking-card-header {
    border-bottom: 2px solid #e77a3a;
    padding-bottom: 10px;
    margin-bottom: 15px;
  }
  
  .mobile-booking-card-header h5 {
    color: #e77a3a;
    font-size: 18px;
    font-weight: 600;
    margin: 0;
  }
  
  .mobile-booking-card-header .booking-ref {
    font-size: 14px;
    color: #6c757d;
    margin-top: 5px;
  }
  
  .mobile-booking-info-row {
    display: flex;
    justify-content: space-between;
    padding: 10px 0;
    border-bottom: 1px solid #f0f0f0;
  }
  
  .mobile-booking-info-row:last-child {
    border-bottom: none;
  }
  
  .mobile-booking-info-label {
    font-weight: 600;
    color: #495057;
    font-size: 14px;
    flex: 0 0 40%;
  }
  
  .mobile-booking-info-value {
    color: #212529;
    font-size: 14px;
    text-align: right;
    flex: 1;
  }
  
  .mobile-booking-actions {
    margin-top: 15px;
    padding-top: 15px;
    border-top: 1px solid #dee2e6;
    display: flex;
    flex-wrap: wrap;
    gap: 8px;
  }
  
  .mobile-booking-actions .btn {
    flex: 1;
    min-width: calc(50% - 4px);
    font-size: 13px;
    padding: 8px 12px;
  }
  
  .mobile-booking-actions .btn i {
    margin-right: 5px;
  }
  
  /* Badges in mobile cards */
  .mobile-booking-info-value .badge {
    font-size: 12px;
    padding: 4px 8px;
  }
  
  /* Pagination - Mobile */
  .pagination {
    justify-content: center;
    flex-wrap: wrap;
  }
  
  .pagination .page-link {
    padding: 8px 12px;
    font-size: 14px;
  }
  
  /* Modal - Mobile */
  .modal-dialog {
    margin: 10px;
  }
  
  .modal-dialog.modal-lg {
    max-width: calc(100% - 20px);
  }
  
  .booking-details-view .row .col-md-6 {
    flex: 0 0 100%;
    max-width: 100%;
    margin-bottom: 20px;
  }
}

/* Desktop - Hide mobile cards */
@media (min-width: 768px) {
  .mobile-booking-cards {
    display: none;
  }
  
  #bookingsTable {
    display: table;
  }
}

/* Very Small Screens */
@media (max-width: 480px) {
  .mobile-booking-card {
    padding: 12px;
  }
  
  .mobile-booking-card-header h5 {
    font-size: 16px;
  }
  
  .mobile-booking-info-label,
  .mobile-booking-info-value {
    font-size: 13px;
  }
  
  .mobile-booking-actions .btn {
    flex: 0 0 100%;
    min-width: 100%;
    margin-bottom: 8px;
  }
  
  .mobile-booking-actions .btn:last-child {
    margin-bottom: 0;
  }
  
  .tile-title-w-btn .title {
    font-size: 18px;
  }
  
  .widget-small {
    padding: 10px;
  }
  
  .widget-small .icon {
    font-size: 1.5rem !important;
  }
  
  .widget-small .info h4 {
    font-size: 14px;
  }
  
  .widget-small .info p {
    font-size: 20px;
  }
  
  /* Rounded borders for all widget-small cards */
  .widget-small,
  .widget-small.success,
  .widget-small.success.coloured-icon,
  .widget-small.primary,
  .widget-small.primary.coloured-icon,
  .widget-small.info,
  .widget-small.info.coloured-icon,
  .widget-small.warning,
  .widget-small.warning.coloured-icon,
  .widget-small.danger,
  .widget-small.danger.coloured-icon,
  .widget-small.dark,
  .widget-small.dark.coloured-icon {
    border-radius: 8px !important;
    overflow: hidden;
  }
  
  /* Dark widget-small style for zero values */
  .widget-small.dark.coloured-icon {
    background-color: #fff;
    color: #2a2a2a;
    border: 1px solid #2a2a2a;
  }
  
  .widget-small.dark.coloured-icon .icon {
    background-color: #2a2a2a;
    color: #fff;
  }
}
</style>

<script>
const baseUrl = '{{ rtrim(asset(""), "/") }}/';
const isReception = {{ ($role ?? '') === 'reception' ? 'true' : 'false' }};

function viewBooking(bookingId) {
  console.log('Loading booking:', bookingId);
  fetch('{{ url("/manager/bookings") }}/' + bookingId, {
    method: 'GET',
    headers: {
      'Accept': 'application/json',
      'X-CSRF-TOKEN': '{{ csrf_token() }}'
    }
  })
  .then(response => {
    console.log('Response status:', response.status);
    if (!response.ok) {
      return response.json().then(err => {
        throw new Error(err.message || 'HTTP error! status: ' + response.status);
      }).catch(() => {
        throw new Error('HTTP error! status: ' + response.status);
      });
    }
    return response.json();
  })
  .then(data => {
    console.log('Booking data:', data);
    if (data.success) {
      const booking = data.booking;
      const room = booking.room || {};
      
      // Helper function to format dates safely
      function formatDate(dateString) {
        if (!dateString) return 'N/A';
        try {
          // Extract date part only (YYYY-MM-DD) to avoid timezone issues
          let datePart;
          if (dateString.includes('T')) {
            // Extract date part before 'T' (e.g., "2025-12-11T00:00:00.000000Z" -> "2025-12-11")
            datePart = dateString.split('T')[0];
          } else if (dateString.includes(' ')) {
            datePart = dateString.split(' ')[0]; // In case there's a space
          } else {
            datePart = dateString; // Already just a date
          }
          
          // Parse date parts (YYYY-MM-DD)
          const parts = datePart.split('-');
          if (parts.length !== 3) {
            return dateString; // Return original if can't parse
          }
          
          const year = parseInt(parts[0]);
          const month = parseInt(parts[1]) - 1; // JavaScript months are 0-indexed
          const day = parseInt(parts[2]);
          
          if (isNaN(year) || isNaN(month) || isNaN(day)) {
            return dateString; // Return original if invalid
          }
          
          // Create date using local timezone (not UTC) - this ensures the date displays correctly
          const date = new Date(year, month, day);
          
          if (isNaN(date.getTime())) {
            return dateString; // Return original if invalid
          }
          
          return date.toLocaleDateString('en-US', { year: 'numeric', month: 'long', day: 'numeric' });
        } catch (e) {
          console.error('Date formatting error:', e, dateString);
          return dateString; // Return original if parsing fails
        }
      }
      
      // Helper function to calculate nights
      function calculateNights(checkIn, checkOut) {
        if (!checkIn || !checkOut) return 'N/A';
        try {
          // Extract date parts to avoid timezone issues
          const getDatePart = (dateString) => {
            if (dateString.includes('T')) {
              return dateString.split('T')[0];
            }
            return dateString.split(' ')[0];
          };
          
          const checkInPart = getDatePart(checkIn);
          const checkOutPart = getDatePart(checkOut);
          
          const [yearIn, monthIn, dayIn] = checkInPart.split('-');
          const [yearOut, monthOut, dayOut] = checkOutPart.split('-');
          
          if (!yearIn || !monthIn || !dayIn || !yearOut || !monthOut || !dayOut) {
            return 'N/A';
          }
          
          const checkInDate = new Date(parseInt(yearIn), parseInt(monthIn) - 1, parseInt(dayIn));
          const checkOutDate = new Date(parseInt(yearOut), parseInt(monthOut) - 1, parseInt(dayOut));
          
          if (isNaN(checkInDate.getTime()) || isNaN(checkOutDate.getTime())) {
            return 'N/A';
          }
          
          const diffTime = checkOutDate - checkInDate;
          const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24));
          return diffDays + (diffDays === 1 ? ' night' : ' nights');
        } catch (e) {
          return 'N/A';
        }
      }
      
      // Helper function to calculate nights between two dates (returns number)
      function calculateNightsNumber(checkIn, checkOut) {
        if (!checkIn || !checkOut) return 0;
        try {
          const getDatePart = (dateString) => {
            if (dateString.includes('T')) {
              return dateString.split('T')[0];
            }
            return dateString.split(' ')[0];
          };
          
          const checkInPart = getDatePart(checkIn);
          const checkOutPart = getDatePart(checkOut);
          
          const [yearIn, monthIn, dayIn] = checkInPart.split('-');
          const [yearOut, monthOut, dayOut] = checkOutPart.split('-');
          
          if (!yearIn || !monthIn || !dayIn || !yearOut || !monthOut || !dayOut) {
            return 0;
          }
          
          const checkInDate = new Date(parseInt(yearIn), parseInt(monthIn) - 1, parseInt(dayIn));
          const checkOutDate = new Date(parseInt(yearOut), parseInt(monthOut) - 1, parseInt(dayOut));
          
          if (isNaN(checkInDate.getTime()) || isNaN(checkOutDate.getTime())) {
            return 0;
          }
          
          const diffTime = checkOutDate - checkInDate;
          const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24));
          return diffDays;
        } catch (e) {
          return 0;
        }
      }
      
      // Check if booking was extended or decreased
      let isExtended = false;
      let isDecreased = false;
      let extendedNights = 0;
      let decreasedNights = 0;
      
      if (booking.original_check_out && booking.original_check_out !== booking.check_out) {
        const originalCheckOut = new Date(booking.original_check_out + 'T00:00:00');
        const currentCheckOut = new Date(booking.check_out + 'T00:00:00');
        
        if (currentCheckOut > originalCheckOut) {
          // Extended
          isExtended = true;
          extendedNights = calculateNightsNumber(booking.original_check_out, booking.check_out);
        } else if (currentCheckOut < originalCheckOut) {
          // Decreased
          isDecreased = true;
          decreasedNights = calculateNightsNumber(booking.check_out, booking.original_check_out);
        }
      }
      
      const originalNights = (isExtended || isDecreased) ? calculateNightsNumber(booking.check_in, booking.original_check_out) : calculateNightsNumber(booking.check_in, booking.check_out);
      
      // Calculate extension cost or decrease refund
      let extensionCost = 0;
      let decreaseRefund = 0;
      let originalPrice = parseFloat(booking.total_price || 0);
      
      if (isExtended && booking.room && extendedNights > 0 && booking.room.price_per_night) {
        extensionCost = parseFloat(booking.room.price_per_night) * extendedNights;
        // Calculate original price by subtracting extension cost from total price
        originalPrice = parseFloat(booking.total_price || 0) - extensionCost;
      } else if (isDecreased && booking.room && decreasedNights > 0 && booking.room.price_per_night) {
        decreaseRefund = parseFloat(booking.room.price_per_night) * decreasedNights;
        // Calculate original price by adding decrease refund to total price
        originalPrice = parseFloat(booking.total_price || 0) + decreaseRefund;
      }
      
      const detailsHtml = `
        <div class="booking-details-view">
          <div class="preview-container">
            
            <!-- Top Status Bar -->
            <div class="preview-section mb-4 d-flex justify-content-between align-items-center">
              <div>
                 <h4 class="mb-0 text-primary">${booking.guest_name}</h4>
                 <span class="text-muted small"><i class="fa fa-hashtag"></i> ${booking.booking_reference}</span>
              </div>
              <div class="text-right">
                 <span class="badge badge-${booking.status === 'confirmed' ? 'success' : booking.status === 'pending' ? 'warning' : 'danger'} p-2" style="font-size: 0.9rem;">
                    ${booking.status ? booking.status.toUpperCase() : 'N/A'}
                 </span>
                 <div class="mt-1 small text-muted">
                    ${booking.check_in_status === 'checked_in' ? '<i class="fa fa-check-circle text-success"></i> Checked In' : 
                      booking.check_in_status === 'checked_out' ? '<i class="fa fa-check-circle text-secondary"></i> Checked Out' : 
                      '<i class="fa fa-clock-o"></i> Status: ' + (booking.check_in_status || 'Pending')}
                 </div>
              </div>
            </div>

            <div class="row">
              <!-- Left Column -->
              <div class="col-md-6">
                
                <!-- Guest Details -->
                <div class="preview-section h-100">
                  <h5><i class="fa fa-user-circle"></i> Guest Details</h5>
                  <table class="table table-bordered table-sm">
                    <tr>
                      <th>Name:</th>
                      <td>
                        <div class="font-weight-bold">${booking.first_name || ''} ${booking.last_name || ''}</div>
                        <div class="text-muted small">${booking.booking_for === 'me' ? '(Main Guest)' : '(Booking for someone else)'}</div>
                      </td>
                    </tr>
                    <tr>
                      <th>Email:</th>
                      <td>${booking.guest_email}</td>
                    </tr>
                    <tr>
                      <th>Phone:</th>
                      <td>${booking.guest_phone || 'N/A'}</td>
                    </tr>
                    <tr>
                      <th>Country:</th>
                      <td>${booking.country || 'N/A'}</td>
                    </tr>
                    <tr>
                      <th>Guests:</th>
                      <td>${booking.number_of_guests || 1} Person(s)</td>
                    </tr>
                  </table>
                </div>
              </div>

              <!-- Right Column -->
              <div class="col-md-6">
                <!-- Stay Dates -->
                <div class="preview-section h-100">
                  <h5><i class="fa fa-calendar"></i> Stay Dates</h5>
                  <div class="d-flex justify-content-between align-items-center bg-light rounded p-3 mb-3">
                    <div class="text-center">
                       <div class="text-muted small text-uppercase">Check In</div>
                       <div class="font-weight-bold text-primary">${formatDate(booking.check_in)}</div>
                       ${booking.arrival_time ? '<div class="small text-muted">' + booking.arrival_time + '</div>' : ''}
                    </div>
                    <div class="text-muted"><i class="fa fa-arrow-right"></i></div>
                    <div class="text-center">
                       <div class="text-muted small text-uppercase">Check Out</div>
                       <div class="font-weight-bold text-primary">${formatDate(booking.check_out)}</div>
                       ${booking.departure_time ? '<div class="small text-muted">' + booking.departure_time + '</div>' : ''}
                    </div>
                  </div>
                  
                  <div class="text-center mb-0">
                    <span class="badge badge-pill badge-info px-4 py-2" style="font-size: 0.9rem;">${calculateNights(booking.check_in, booking.check_out)} Stay</span>
                  </div>

                  ${isExtended && extendedNights > 0 ? `<div class="alert alert-info py-2 mt-3 mb-0 small"><i class="fa fa-plus-circle"></i> Extended by ${extendedNights} nights</div>` : ''}
                  ${isDecreased && decreasedNights > 0 ? `<div class="alert alert-warning py-2 mt-3 mb-0 small"><i class="fa fa-minus-circle"></i> Decreased by ${decreasedNights} nights</div>` : ''}
                  ${(isExtended || isDecreased) && booking.original_check_out ? `<div class="text-muted small text-center mt-2">Original Checkout: ${formatDate(booking.original_check_out)}</div>` : ''}


                </div>
              </div>
            </div>

            <div class="row mt-4">
              <!-- Room details -->
              <div class="col-md-6">
                <div class="preview-section h-100">
                  <h5><i class="fa fa-bed"></i> Room Assigned</h5>
                  <table class="table table-bordered table-sm">
                    <tr>
                      <th>Room Number:</th>
                      <td class="h5 mb-0 text-primary">${room.room_number || 'Unassigned'}</td>
                    </tr>
                    <tr>
                      <th>Room Type:</th>
                      <td>${room.room_type || 'Standard'}</td>
                    </tr>
                    <tr>
                      <th>Floor:</th>
                      <td>${room.floor_location || 'N/A'}</td>
                    </tr>
                    <tr>
                      <th>Capacity:</th>
                      <td>${room.capacity || 'N/A'} ${parseInt(room.capacity) === 1 ? 'Guest' : 'Guests'}</td>
                    </tr>
                  </table>
                </div>
              </div>

              <!-- Financial Info -->
              <div class="col-md-6">
                <div class="preview-section h-100">
                  <h5><i class="${booking.guest_type === 'tanzanian' ? 'fa fa-money' : 'fa fa-dollar'}"></i> Financial Info</h5>
                  <table class="table table-bordered table-sm">
                    ${(() => {
                      const isTanzanian = booking.guest_type === 'tanzanian';
                      const currencySymbol = isTanzanian ? '' : '$';
                      const currencySuffix = isTanzanian ? ' TZS' : '';
                      const deco = isTanzanian ? 0 : 2;
                      
                      const serviceCharges = parseFloat(booking.service_charges_usd || 0);
                      const totalRoomCharge = parseFloat(booking.total_price || 0);
                      const totalCharges = parseFloat(booking.total_bill_usd || totalRoomCharge);
                      const nights = calculateNightsNumber(booking.check_in, booking.check_out) || 1;
                      const pricePerNight = totalRoomCharge / nights;
                      
                      let html = '';
                      
                      // Price per Night
                      html += `<tr>
                        <th>Price/Night:</th>
                        <td>${currencySymbol}${pricePerNight.toLocaleString('en-US', {minimumFractionDigits: deco, maximumFractionDigits: deco})}${currencySuffix}</td>
                      </tr>`;

                      // Total Room Charge
                      html += `<tr>
                        <th>Room Charge:</th>
                        <td>${currencySymbol}${totalRoomCharge.toLocaleString('en-US', {minimumFractionDigits: deco, maximumFractionDigits: deco})}${currencySuffix}</td>
                      </tr>`;
                      
                      // Service Charges (if any)
                      if (serviceCharges > 0) {
                        const sVal = isTanzanian ? parseFloat(booking.service_charges_tsh || 0) : serviceCharges;
                        html += `<tr>
                          <th>Service Charges:</th>
                          <td>${currencySymbol}${sVal.toLocaleString('en-US', {minimumFractionDigits: deco, maximumFractionDigits: deco})}${currencySuffix}</td>
                        </tr>`;
                      }
                      
                      // Total Charges
                      html += `<tr class="table-active">
                        <th class="font-weight-bold">Total Bill:</th>
                        <td class="font-weight-bold h5 mb-0 text-primary">${currencySymbol}${totalCharges.toLocaleString('en-US', {minimumFractionDigits: deco, maximumFractionDigits: deco})}${currencySuffix}</td>
                      </tr>`;
                      
                      return html;
                    })()}
                    
                    ${(() => {
                        const isTanzanian = booking.guest_type === 'tanzanian';
                        const currencySymbol = isTanzanian ? '' : '$';
                        const currencySuffix = isTanzanian ? ' TZS' : '';
                        const deco = isTanzanian ? 0 : 2;
                        
                        let html = '';
                        if (isExtended && extensionCost > 0) {
                            const eCost = isTanzanian ? (extensionCost * (booking.locked_exchange_rate || 2500)) : extensionCost;
                            html += `<tr><th class="text-info">Extension:</th><td class="text-info">+${currencySymbol}${eCost.toLocaleString('en-US', {minimumFractionDigits: deco, maximumFractionDigits: deco})}${currencySuffix}</td></tr>`;
                        }
                        if (isDecreased && decreaseRefund > 0) {
                            const dRefund = isTanzanian ? (decreaseRefund * (booking.locked_exchange_rate || 2500)) : decreaseRefund;
                            html += `<tr><th class="text-warning">Refund:</th><td class="text-warning">-${currencySymbol}${dRefund.toLocaleString('en-US', {minimumFractionDigits: deco, maximumFractionDigits: deco})}${currencySuffix}</td></tr>`;
                        }
                        return html;
                    })()}

                    <tr>
                      <th>${booking.is_corporate_booking ? 'Paid by Company:' : 'Total Amount Paid:'}</th>
                      <td class="text-success font-weight-bold">
                        ${(() => {
                            const isTanzanian = booking.guest_type === 'tanzanian';
                            const currencySymbol = isTanzanian ? '' : '$';
                            const currencySuffix = isTanzanian ? ' TZS' : '';
                            const deco = isTanzanian ? 0 : 2;
                            const paidVal = parseFloat(booking.total_paid_usd || booking.amount_paid || 0);
                            
                            return `${currencySymbol}${paidVal.toLocaleString('en-US', {minimumFractionDigits: deco, maximumFractionDigits: deco})}${currencySuffix}`;
                        })()}
                      </td>
                    </tr>
                    ${(() => {
                      const isTanzanian = booking.guest_type === 'tanzanian';
                      const currencySymbol = isTanzanian ? '' : '$';
                      const currencySuffix = isTanzanian ? ' TZS' : '';
                      const deco = isTanzanian ? 0 : 2;
                      
                      const totalCharges = parseFloat(booking.total_bill_usd || booking.total_price || 0);
                      const totalPaid = parseFloat(booking.total_paid_usd || booking.amount_paid || 0);
                      const balance = totalCharges - totalPaid;
                      
                      const dispBalance = balance;
                      
                      // If there's a positive balance (guest owes money)
                      if (balance > 0.01) {
                        return `<tr class="table-active">
                          <th class="font-weight-bold">Balance Due:</th>
                          <td class="font-weight-bold text-danger h5 mb-0">${currencySymbol}${dispBalance.toLocaleString('en-US', {minimumFractionDigits: deco, maximumFractionDigits: deco})}${currencySuffix}</td>
                        </tr>`;
                      } 
                      // If fully paid or overpaid
                      else if (totalPaid >= (totalCharges - 0.05) && totalCharges > 0) {
                        let html = `<tr class="table-active">
                          <th class="font-weight-bold">Balance:</th>
                          <td><span class="badge badge-success px-3 py-1" style="font-size: 0.85rem;"><i class="fa fa-check-circle"></i> ALL PAID</span></td>
                        </tr>`;
                        
                        // If overpaid, show the overpayment amount as additional info
                        if (balance < -0.01) {
                          html += `<tr>
                            <th class="text-muted">Overpayment:</th>
                            <td class="text-info">${currencySymbol}${Math.abs(dispBalance).toLocaleString('en-US', {minimumFractionDigits: deco, maximumFractionDigits: deco})}${currencySuffix} <small class="text-muted">(Credit)</small></td>
                          </tr>`;
                        }
                        return html;
                      }
                      return '';
                    })()}
                    <tr>
                      <th>Payment Status:</th>
                      <td>
                        <span class="badge badge-${booking.payment_status === 'paid' ? 'success' : booking.payment_status === 'partial' ? 'info' : 'warning'}">
                          ${booking.payment_status ? booking.payment_status.toUpperCase() : 'N/A'}
                          ${booking.payment_status === 'partial' ? '(' + parseFloat(booking.payment_percentage).toFixed(0) + '%)' : ''}
                        </span>
                      </td>
                    </tr>
                  </table>
                ${booking.payment_transaction_id ? `<div class="mt-2 text-center"><small class="bg-light p-1 px-2 rounded font-monospace text-muted">ID: ${booking.payment_transaction_id}</small></div>` : ''}
                </div>
              </div>
            </div>

            <!-- Exchange Rate Override Panel -->
            <div class="row mt-4">
              <div class="col-md-12">
                <div class="preview-section">
                  <h5 style="color: #e77a3a; border-bottom: 2px solid #e77a3a; padding-bottom: 5px; margin-bottom: 15px;">
                    <i class="fa fa-exchange"></i> Exchange Rate
                    ${booking.rate_source && booking.rate_source !== 'system' ? `<span class="badge badge-info ml-2" style="font-size:0.75rem;"><i class="fa fa-pencil"></i> Overridden</span>` : ''}
                  </h5>
                  <div class="row">
                    <div class="col-md-5">
                      <table class="table table-bordered table-sm mb-0">
                        <tr>
                          <th>Current Rate:</th>
                          <td><strong class="text-primary">1 USD = ${parseFloat(booking.locked_exchange_rate || 2500).toLocaleString()} TZS</strong></td>
                        </tr>
                        ${booking.original_exchange_rate ? `
                        <tr>
                          <th>Original Rate:</th>
                          <td class="text-muted">1 USD = ${parseFloat(booking.original_exchange_rate).toLocaleString()} TZS</td>
                        </tr>` : ''}
                        ${booking.rate_source ? `
                        <tr>
                          <th>Rate Source:</th>
                          <td><span class="badge badge-secondary">${booking.rate_source}</span></td>
                        </tr>` : ''}
                        ${booking.exchange_rate_note ? `
                        <tr>
                          <th>Note:</th>
                          <td class="small text-muted">${booking.exchange_rate_note}</td>
                        </tr>` : ''}
                        ${booking.exchange_rate_overridden_by ? `
                        <tr>
                          <th>Changed by:</th>
                          <td class="small text-muted">${booking.exchange_rate_overridden_by} &mdash; ${booking.exchange_rate_overridden_at || ''}</td>
                        </tr>` : ''}
                      </table>
                    </div>
                    <div class="col-md-7">
                      <div class="card border-warning" id="rateOverrideCard_${booking.id}">
                        <div class="card-header bg-warning-light py-2" style="background: #fff8e1; cursor:pointer;"
                             onclick="toggleRateForm(${booking.id})">
                          <i class="fa fa-pencil-square-o text-warning"></i>
                          <strong class="text-warning"> Override Exchange Rate</strong>
                          <i class="fa fa-chevron-down float-right mt-1" id="rateFormChevron_${booking.id}"></i>
                        </div>
                        <div class="card-body p-3" id="rateOverrideForm_${booking.id}" style="display:none;">
                          <div class="alert alert-warning py-2 small mb-3">
                            <i class="fa fa-exclamation-triangle"></i>
                            Override when the guest paid using a different rate (e.g., Booking.com, bank transfer).
                            This recalculates all TZS amounts for this booking.
                          </div>
                          <div class="form-group mb-2">
                            <label class="small font-weight-bold">New Exchange Rate <span class="text-danger">*</span></label>
                            <div class="input-group input-group-sm">
                              <div class="input-group-prepend"><span class="input-group-text">1 USD =</span></div>
                              <input type="number" class="form-control" id="newRate_${booking.id}"
                                     value="${parseFloat(booking.locked_exchange_rate || 2500).toFixed(0)}"
                                     min="100" max="10000000" step="1" placeholder="e.g. 2650">
                              <div class="input-group-append"><span class="input-group-text">TZS</span></div>
                            </div>
                          </div>
                          <div class="form-group mb-2">
                            <label class="small font-weight-bold">Rate Source <span class="text-danger">*</span></label>
                            <select class="form-control form-control-sm" id="rateSource_${booking.id}">
                              <option value="manual">Manual Override</option>
                              <option value="booking.com">Booking.com</option>
                              <option value="agoda">Agoda</option>
                              <option value="expedia">Expedia</option>
                              <option value="airbnb">Airbnb</option>
                              <option value="paypal">PayPal</option>
                              <option value="bank">Bank Transfer</option>
                              <option value="other">Other</option>
                            </select>
                          </div>
                          <div class="form-group mb-3">
                            <label class="small font-weight-bold">Note <span class="text-muted">(optional)</span></label>
                            <input type="text" class="form-control form-control-sm" id="rateNote_${booking.id}"
                                   placeholder="e.g. Guest paid via Booking.com at rate of 2650"
                                   maxlength="500">
                          </div>
                          <div id="rateOverrideAlert_${booking.id}"></div>
                          <button type="button" class="btn btn-warning btn-sm btn-block"
                                  onclick="submitRateOverride(${booking.id})">
                            <i class="fa fa-save"></i> Save New Rate
                          </button>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>

            <!-- Identity Records Section -->
            ${(booking.id_document_type || booking.id_scan_path || booking.guest_signature_path || booking.checkout_signature_path) ? `
              <div class="row mt-4">
                <div class="col-md-12">
                  <h5 style="color: #e77a3a; border-bottom: 2px solid #e77a3a; padding-bottom: 5px; margin-bottom: 15px;"><i class="fa fa-id-card"></i> Identity & Signature Records</h5>
                </div>
                <div class="col-md-6">
                  <div class="card bg-light border-0 h-100">
                    <div class="card-body">
                      <h6><strong>Identity Document</strong></h6>
                      <p class="mb-2"><strong>Type:</strong> ${booking.id_document_type || 'N/A'}</p>
                      <p class="mb-2"><strong>Number:</strong> ${booking.id_document_number || 'N/A'}</p>
                      <div class="row mt-2">
                        <div class="col-6 text-center">
                          ${booking.id_scan_path ? `
                            <a href="${baseUrl}${booking.id_scan_path}" target="_blank">
                              <img src="${baseUrl}${booking.id_scan_path}" 
                                   style="max-width: 100%; border-radius: 4px; border: 1px solid #ddd; max-height: 120px; cursor: pointer;"
                                   onerror="this.outerHTML='<p class=text-danger small>Image not found</p>'">
                            </a>
                            <br><small class="text-muted">Front Side</small>
                          ` : '<p class="text-muted small"><i class="fa fa-image"></i> No Front Scan</p>'}
                        </div>
                        <div class="col-6 text-center">
                          ${booking.id_scan_back_path ? `
                            <a href="${baseUrl}${booking.id_scan_back_path}" target="_blank">
                              <img src="${baseUrl}${booking.id_scan_back_path}" 
                                   style="max-width: 100%; border-radius: 4px; border: 1px solid #ddd; max-height: 120px; cursor: pointer;"
                                   onerror="this.outerHTML='<p class=text-danger small>Image not found</p>'">
                            </a>
                            <br><small class="text-muted">Back Side</small>
                          ` : '<p class="text-muted small"><i class="fa fa-image"></i> No Back Scan</p>'}
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
                <div class="col-md-6">
                  <div class="card bg-light border-0 h-100">
                    <div class="card-body">
                      <h6><strong><i class="fa fa-pencil text-primary"></i> Check-In Signature</strong></h6>
                      ${booking.guest_signature_path ? `
                        <div class="mt-2 text-center" style="background: white; border-radius: 4px; padding: 10px; border: 1px solid #ddd;">
                          <img src="${baseUrl}${booking.guest_signature_path}" 
                               style="max-width: 100%; max-height: 150px;"
                               onerror="this.outerHTML='<p class=text-danger small>Signature image not found</p>'">
                        </div>
                        <p class="small text-muted mt-2"><i class="fa fa-clock-o"></i> Captured: ${booking.identity_captured_at ? new Date(booking.identity_captured_at).toLocaleString() : 'At check-in'}</p>
                      ` : '<p class="text-muted small mt-2"><i class="fa fa-pencil"></i> No check-in signature captured</p>'}
                    </div>
                  </div>
                </div>
              </div>

              ${booking.checkout_signature_path ? `
              <div class="row mt-3">
                <div class="col-md-12">
                  <h6 style="color: #28a745; border-bottom: 1px solid #28a745; padding-bottom: 4px; margin-bottom: 12px;">
                    <i class="fa fa-sign-out text-success"></i> Check-Out Signature
                  </h6>
                </div>
                <div class="col-md-6">
                  <div class="card border-success h-100" style="border-width: 1px;">
                    <div class="card-body text-center" style="background: #f8fff9;">
                      <div style="background: white; border-radius: 4px; padding: 10px; border: 1px solid #c3e6cb;">
                        <img src="${baseUrl}${booking.checkout_signature_path}" 
                             style="max-width: 100%; max-height: 150px;"
                             onerror="this.outerHTML='<p class=text-danger small>Signature image not found</p>'">
                      </div>
                      <p class="small text-success mt-2"><i class="fa fa-check-circle"></i> Guest signed at check-out</p>
                    </div>
                  </div>
                </div>
                <div class="col-md-6 d-flex align-items-center">
                  <div class="alert alert-success mb-0 w-100">
                    <i class="fa fa-info-circle"></i> <strong>Check-Out Completed</strong>
                    <p class="mb-0 small mt-1">The guest has digitally signed the check-out confirmation. The signature above confirms their departure agreement.</p>
                  </div>
                </div>
              </div>
              ` : `
              <div class="row mt-3">
                <div class="col-md-12">
                  <div class="alert alert-secondary py-2 mb-0">
                    <i class="fa fa-sign-out"></i> <strong>Check-Out Signature:</strong> Not yet captured
                    ${booking.check_in_status === 'checked_out' ? ' <span class="badge badge-secondary ml-2">Guest checked out without digital signature</span>' : ''}
                  </div>
                </div>
              </div>
              `}
            ` : ''}

            <!-- Notes & Requests -->
            <div class="row mt-4">
              <div class="col-12">
                <div class="preview-section">
                  <h5><i class="fa fa-sticky-note"></i> Notes & Requests</h5>
                  ${booking.special_requests ? `<div class="alert alert-info mb-2"><i class="fa fa-comment-o"></i> <strong>Guest Request:</strong> ${booking.special_requests}</div>` : '<p class="text-muted small mb-2">No special requests from guest.</p>'}
                  <hr>
                  ${booking.admin_notes ? `<div class="alert alert-warning mb-2"><i class="fa fa-sticky-note-o"></i> <strong>Admin Notes:</strong> ${booking.admin_notes}</div>` : '<p class="text-muted small mb-0">No internal admin notes.</p>'}
                  ${booking.cancellation_reason ? `<div class="alert alert-danger mt-2"><i class="fa fa-ban"></i> <strong>Cancellation Reason:</strong> ${booking.cancellation_reason}</div>` : ''}
                </div>
              </div>
            </div>

          </div>
        </div>
      `;
      
      document.getElementById('bookingDetailsContent').innerHTML = detailsHtml;
      $('#bookingDetailsModal').modal('show');
    } else {
      swal({
        title: "Error",
        text: "Failed to load booking details",
        type: "error",
        confirmButtonColor: "#e77a3a"
      });
    }
  })
  .catch(error => {
    console.error('Error:', error);
    swal({
      title: "Error",
      text: "An error occurred while loading booking details",
      type: "error",
      confirmButtonColor: "#e77a3a"
    });
  });
}

function updateStatus(bookingId, status) {
  swal({
    title: "Update Status?",
    text: `Are you sure you want to change the booking status to "${status}"?`,
    type: "warning",
    showCancelButton: true,
    confirmButtonColor: "#e77a3a",
    cancelButtonColor: "#d33",
    confirmButtonText: "Yes, update it!",
    cancelButtonText: "Cancel"
  }, function(isConfirm) {
    if (isConfirm) {
      fetch('{{ url("/manager/bookings") }}/' + bookingId + '/status', {
        method: 'PUT',
        headers: {
          'Accept': 'application/json',
          'X-CSRF-TOKEN': '{{ csrf_token() }}',
          'Content-Type': 'application/json'
        },
        body: JSON.stringify({ status: status })
      })
      .then(response => response.json())
      .then(data => {
        if (data.success) {
          swal({
            title: "Updated!",
            text: data.message || "Booking status has been updated.",
            type: "success",
            confirmButtonColor: "#e77a3a"
          }, function() {
            location.reload();
          });
        } else {
          swal({
            title: "Error!",
            text: data.message || "Failed to update booking status.",
            type: "error",
            confirmButtonColor: "#e77a3a"
          });
        }
      })
      .catch(error => {
        console.error('Error:', error);
        swal({
          title: "Error!",
          text: "An error occurred while updating the booking.",
          type: "error",
          confirmButtonColor: "#e77a3a"
        });
      });
    }
  });
}

// ─── Exchange Rate Override helpers ─────────────────────────────────────────

function toggleRateForm(bookingId) {
  const form    = document.getElementById('rateOverrideForm_' + bookingId);
  const chevron = document.getElementById('rateFormChevron_' + bookingId);
  if (!form) return;
  const isOpen = form.style.display !== 'none';
  form.style.display    = isOpen ? 'none' : 'block';
  chevron.className     = isOpen
    ? 'fa fa-chevron-down float-right mt-1'
    : 'fa fa-chevron-up float-right mt-1';
}

function submitRateOverride(bookingId) {
  const rateInput  = document.getElementById('newRate_' + bookingId);
  const srcSelect  = document.getElementById('rateSource_' + bookingId);
  const noteInput  = document.getElementById('rateNote_' + bookingId);
  const alertDiv   = document.getElementById('rateOverrideAlert_' + bookingId);
  const newRate    = parseFloat(rateInput ? rateInput.value : 0);

  // Client-side validation
  if (!newRate || newRate < 100) {
    if (alertDiv) alertDiv.innerHTML = '<div class="alert alert-danger py-2 small"><i class="fa fa-times-circle"></i> Please enter a valid exchange rate (minimum 100 TZS per USD).</div>';
    return;
  }

  // Resolve route based on role
  const overrideUrl = isReception
    ? '{{ url("/reception/bookings") }}/' + bookingId + '/override-exchange-rate'
    : '{{ url("/manager/bookings") }}/' + bookingId + '/override-exchange-rate';

  const btn = event.currentTarget;
  const originalHtml = btn.innerHTML;
  btn.disabled  = true;
  btn.innerHTML = '<i class="fa fa-spinner fa-spin"></i> Saving...';
  if (alertDiv) alertDiv.innerHTML = '';

  fetch(overrideUrl, {
    method: 'POST',
    headers: {
      'Accept':       'application/json',
      'Content-Type': 'application/json',
      'X-CSRF-TOKEN': '{{ csrf_token() }}'
    },
    body: JSON.stringify({
      exchange_rate:      newRate,
      rate_source:        srcSelect  ? srcSelect.value  : 'manual',
      exchange_rate_note: noteInput  ? noteInput.value  : ''
    })
  })
  .then(r => r.json())
  .then(data => {
    btn.disabled  = false;
    btn.innerHTML = originalHtml;

    if (data.success) {
      if (alertDiv) {
        alertDiv.innerHTML = `<div class="alert alert-success py-2 small"><i class="fa fa-check-circle"></i> Rate updated to <strong>${parseFloat(data.new_rate).toLocaleString()} TZS/USD</strong> by ${data.overridden_by} at ${data.overridden_at}.</div>`;
      }
      // Update the displayed "Current Rate" in the table row immediately
      const rateCell = document.querySelector(`#rateOverrideCard_${bookingId}`);
      if (rateCell) {
        const tableCell = rateCell.closest('.col-md-7')?.previousElementSibling;
        if (tableCell) {
          const firstTd = tableCell.querySelector('td strong.text-primary');
          if (firstTd) firstTd.textContent = '1 USD = ' + parseFloat(data.new_rate).toLocaleString() + ' TZS';
        }
      }
      // Notify user with SweetAlert after a short delay
      setTimeout(() => {
        swal({
          title: 'Exchange Rate Updated!',
          text: '1 USD = ' + parseFloat(data.new_rate).toLocaleString() + ' TZS — saved successfully.',
          type: 'success',
          confirmButtonColor: '#28a745'
        });
      }, 300);
    } else {
      if (alertDiv) {
        alertDiv.innerHTML = '<div class="alert alert-danger py-2 small"><i class="fa fa-times-circle"></i> ' + (data.message || 'Failed to update rate. Please try again.') + '</div>';
      }
    }
  })
  .catch(err => {
    btn.disabled  = false;
    btn.innerHTML = originalHtml;
    console.error('Rate override error:', err);
    if (alertDiv) alertDiv.innerHTML = '<div class="alert alert-danger py-2 small"><i class="fa fa-times-circle"></i> Network error. Please try again.</div>';
  });
}

// ────────────────────────────────────────────────────────────────────────────

function deleteBooking(bookingId) {

  swal({
    title: "Are you sure?",
    text: "You will not be able to recover this booking!",
    type: "warning",
    showCancelButton: true,
    confirmButtonColor: "#e77a3a",
    cancelButtonColor: "#d33",
    confirmButtonText: "Yes, delete it!",
    cancelButtonText: "Cancel"
  }, function(isConfirm) {
    if (isConfirm) {
      fetch('{{ url("/manager/bookings") }}/' + bookingId, {
        method: 'DELETE',
        headers: {
          'Accept': 'application/json',
          'X-CSRF-TOKEN': '{{ csrf_token() }}',
          'Content-Type': 'application/json'
        }
      })
      .then(response => response.json())
      .then(data => {
        if (data.success) {
          swal({
            title: "Deleted!",
            text: data.message || "Booking has been deleted.",
            type: "success",
            confirmButtonColor: "#e77a3a"
          }, function() {
            location.reload();
          });
        } else {
          swal({
            title: "Error!",
            text: data.message || "Failed to delete booking.",
            type: "error",
            confirmButtonColor: "#e77a3a"
          });
        }
      })
      .catch(error => {
        console.error('Error:', error);
        swal({
          title: "Error!",
          text: "An error occurred while deleting the booking.",
          type: "error",
          confirmButtonColor: "#e77a3a"
        });
      });
    }
  });
}

function showNotesModal(bookingId) {
  console.log('Loading booking for notes:', bookingId);
  fetch('{{ url("/manager/bookings") }}/' + bookingId, {
    method: 'GET',
    headers: {
      'Accept': 'application/json',
      'X-CSRF-TOKEN': '{{ csrf_token() }}'
    }
  })
  .then(response => {
    console.log('Notes response status:', response.status);
    if (!response.ok) {
      return response.json().then(err => {
        throw new Error(err.message || 'HTTP error! status: ' + response.status);
      }).catch(() => {
        throw new Error('HTTP error! status: ' + response.status);
      });
    }
    return response.json();
  })
  .then(data => {
    console.log('Notes booking data:', data);
    if (data.success) {
      document.getElementById('notes_booking_id').value = bookingId;
      document.getElementById('admin_notes').value = data.booking.admin_notes || '';
      $('#notesModal').modal('show');
    } else {
      swal({
        title: "Error",
        text: "Failed to load booking details",
        type: "error",
        confirmButtonColor: "#e77a3a"
      });
    }
  })
  .catch(error => {
    console.error('Error:', error);
    swal({
      title: "Error",
      text: "An error occurred while loading booking details",
      type: "error",
      confirmButtonColor: "#e77a3a"
    });
  });
}

function filterBookings() {
  const statusFilter = document.getElementById('statusFilter').value;
  const checkInStatusFilter = document.getElementById('checkInStatusFilter').value;
  const paymentStatusFilter = document.getElementById('paymentStatusFilter').value;
  const searchInput = document.getElementById('searchInput').value.toLowerCase();
  
  // Filter both table rows and mobile cards
  const rows = document.querySelectorAll('.booking-row');
  let visibleCount = 0;
  
  rows.forEach(row => {
    const status = row.getAttribute('data-status');
    const checkInStatus = row.getAttribute('data-check-in-status');
    const paymentStatus = row.getAttribute('data-payment-status');
    const bookingRef = row.getAttribute('data-booking-ref');
    const guestName = row.getAttribute('data-guest-name');
    const guestEmail = row.getAttribute('data-guest-email');
    
    let show = true;
    
    // Status filter
    if (statusFilter !== 'all' && status !== statusFilter) {
      show = false;
    }
    
    // Check-in status filter
    if (show && checkInStatusFilter !== 'all' && checkInStatus !== checkInStatusFilter) {
      show = false;
    }
    
    // Payment status filter
    if (show && paymentStatusFilter !== 'all' && paymentStatus !== paymentStatusFilter) {
      show = false;
    }
    
    // Search filter
    if (show && searchInput) {
      if (!bookingRef.includes(searchInput) && 
          !guestName.includes(searchInput) && 
          !guestEmail.includes(searchInput)) {
        show = false;
      }
    }
    
    row.style.display = show ? '' : 'none';
    if (show) visibleCount++;
  });
  
  // Show/hide "no results" message
  const tbody = document.querySelector('#bookingsTable tbody');
  if (tbody) {
    let noResultsRow = tbody.querySelector('.no-results-row');
    
    if (visibleCount === 0) {
      if (!noResultsRow) {
        noResultsRow = document.createElement('tr');
        noResultsRow.className = 'no-results-row';
        noResultsRow.innerHTML = `
          <td colspan="11" class="text-center">
            <i class="fa fa-search fa-3x text-muted mb-2"></i>
            <p>No bookings found matching your filters</p>
          </td>
        `;
        tbody.appendChild(noResultsRow);
      }
    } else {
      if (noResultsRow) {
        noResultsRow.remove();
      }
    }
  }
}

function applyServerFilters() {
    const startDate = document.getElementById('startDate').value;
    const endDate = document.getElementById('endDate').value;
    const search = document.getElementById('searchInput').value;
    const status = document.getElementById('statusFilter').value;
    const checkInStatus = document.getElementById('checkInStatusFilter').value;
    const paymentStatus = document.getElementById('paymentStatusFilter').value;
    
    let url = new URL(window.location.href);
    
    if (startDate) url.searchParams.set('start_date', startDate); else url.searchParams.delete('start_date');
    if (endDate) url.searchParams.set('end_date', endDate); else url.searchParams.delete('end_date');
    if (search) url.searchParams.set('search', search); else url.searchParams.delete('search');
    
    if (status && status !== 'all') url.searchParams.set('status', status); else url.searchParams.delete('status');
    if (checkInStatus && checkInStatus !== 'all') url.searchParams.set('check_in_status', checkInStatus); else url.searchParams.delete('check_in_status');
    if (paymentStatus && paymentStatus !== 'all') url.searchParams.set('payment_status', paymentStatus); else url.searchParams.delete('payment_status');
    
    // Clear quick filter if manual filters are applied
    url.searchParams.delete('quick_filter');
    
    // Reset to page 1 when filtering
    url.searchParams.set('page', '1');
    
    window.location.href = url.toString();
}

function resetFilters() {
    let url = new URL(window.location.href);
    const type = url.searchParams.get('type');
    
    // Clear all except type
    url.search = '';
    if (type) url.searchParams.set('type', type);
    
    window.location.href = url.pathname + url.search;
}

function viewCompanyBookingGroup(companyId, firstBookingId) {
  if (!companyId) {
    Swal.fire('Error', 'Company ID is missing', 'error');
    return;
  }
  
  $('#companyBookingGroupModal').modal('show');
  $('#companyBookingGroupContent').html('<div class="text-center"><i class="fa fa-spinner fa-spin fa-3x"></i><p>Loading booking details...</p></div>');
  
  fetch('{{ url("/manager/bookings/company") }}/' + companyId, {
    method: 'GET',
    headers: {
      'Accept': 'application/json',
      'X-CSRF-TOKEN': '{{ csrf_token() }}'
    }
  })
  .then(response => {
    if (!response.ok) {
      throw new Error('HTTP error! status: ' + response.status);
    }
    return response.json();
  })
  .then(data => {
    if (data.success && data.bookings) {
      const bookings = data.bookings;
      const company = data.company || {};
      
      // Update global managerBookingData for modal bookings to support individual extension/decrease
      bookings.forEach(function(booking) {
        if (booking.check_in_status === 'checked_in' && booking.room) {
          managerBookingData[booking.id] = {
            id: booking.id,
            roomPrice: parseFloat(booking.room.price_per_night || 0),
            currentCheckOut: booking.check_out.substring(0, 10),
            checkIn: booking.check_in.substring(0, 10)
          };
        }
      });

      let html = '<div class="company-booking-group-view">';
      
      // Calculate company payment breakdown
      let totalCompanyCharges = 0;
      let totalCompanyPaid = 0;
      let totalRemaining = 0;
      const exchangeRate = bookings.length > 0 ? (bookings[0].locked_exchange_rate || 2500) : 2500;
      
      bookings.forEach(function(booking) {
        totalCompanyCharges += parseFloat(booking.total_price || 0);
        // Only include service charges if company is responsible (Mixed or Company)
        if (booking.payment_responsibility !== 'self') {
          totalCompanyCharges += parseFloat(booking.service_charges_usd || 0);
        }
        totalCompanyPaid += parseFloat(booking.amount_paid || 0);
      });
      totalRemaining = totalCompanyCharges - totalCompanyPaid;
      
      // --- Top Summary Section (Company & Financials) ---
      html += '<div class="row px-2 mb-3">';
      
      // Top Grid
      html += '<div class="col-md-12">';
      html += '<div class="card border border-primary shadow-sm rounded-lg">';
      html += '<div class="card-body p-3 bg-light d-flex flex-wrap justify-content-between align-items-center">';
      
      html += '<div class="mb-2 mb-md-0 d-flex align-items-center" style="flex: 1; min-width: 250px;">';
      html += '<div class="bg-primary text-white rounded-circle d-flex justify-content-center align-items-center mr-3" style="width: 45px; height: 45px; flex-shrink:0;"><i class="fa fa-building-o" style="font-size:1.2rem;"></i></div>';
      html += '<div><div class="small text-muted text-uppercase font-weight-bold" style="letter-spacing:0.5px;">Company Details</div>';
      html += '<h6 class="mb-0 text-dark font-weight-bold">' + (company.name || 'N/A') + '</h6>';
      html += '<div class="small text-muted mt-1"><i class="fa fa-envelope-o mr-1"></i>' + (company.email || 'N/A') + ' <span class="mx-1">|</span> <i class="fa fa-phone mr-1"></i>' + (company.phone || 'N/A') + '</div></div>';
      html += '</div>';
      
      html += '<div class="mb-2 mb-md-0 border-left pl-3 ml-3 d-flex align-items-center" style="flex: 1; min-width: 250px;">';
      html += '<div class="bg-info text-white rounded-circle d-flex justify-content-center align-items-center mr-3" style="width: 45px; height: 45px; flex-shrink:0;"><i class="fa fa-user-circle" style="font-size:1.2rem;"></i></div>';
      html += '<div><div class="small text-muted text-uppercase font-weight-bold" style="letter-spacing:0.5px;">Group Leader</div>';
      html += '<h6 class="mb-0 text-dark font-weight-bold">' + (company.contact_person || 'N/A') + '</h6>';
      html += '<div class="small text-muted mt-1"><i class="fa fa-envelope-o mr-1"></i>' + (company.guider_email || 'N/A') + ' <span class="mx-1">|</span> <i class="fa fa-phone mr-1"></i>' + (company.guider_phone || 'N/A') + '</div></div>';
      html += '</div>';

      html += '<div class="pl-3 ml-3 text-right" style="flex: 1; min-width: 200px; border-left: 2px solid #e9ecef;">';
      if (Math.abs(totalRemaining) < 0.005) totalRemaining = 0;
      html += '<div class="small text-muted text-uppercase font-weight-bold mb-1" style="letter-spacing:0.5px;">Financial Balance</div>';
      html += '<div style="font-size:0.85rem;"><span class="text-muted">Total:</span> <strong class="text-dark">$' + totalCompanyCharges.toFixed(2) + '</strong> <span class="mx-1 text-muted">|</span> ';
      html += '<span class="text-muted">Paid:</span> <strong class="text-success">$' + totalCompanyPaid.toFixed(2) + '</strong></div>';
      html += '<div class="mt-1 pt-1 border-top"><span class="font-weight-bold">Remaining:</span> <strong class="h5 mb-0 ml-1 ' + (totalRemaining > 0.01 ? 'text-danger' : 'text-success') + '">$' + totalRemaining.toFixed(2) + '</strong></div>';
      html += '<div class="small text-muted">~ ' + (totalRemaining * exchangeRate).toLocaleString('en-US', {maximumFractionDigits: 0}) + ' TZS</div>';
      html += '</div>';
      
      html += '</div>'; // End card body
      html += '<div class="card-footer bg-white p-2 border-top-0 border-primary" style="font-size:0.8rem;"><i class="fa fa-info-circle text-info mx-1"></i> <strong>Note:</strong> Corporate covers room charges. Individual personal services are billed separately to guest if self-pay.</div>';
      html += '</div></div></div>'; // End Top Grid row
      
      // --- Guests Section with Tabs ---
      html += '<div class="card border-0 shadow-sm mx-2">';
      html += '<div class="card-header bg-white pt-3 pb-0 px-3 border-bottom-0">';
      html += '<h6 class="text-primary font-weight-bold mb-3"><i class="fa fa-users mr-2"></i>Guest Bookings (' + bookings.length + ')</h6>';
      html += '<ul class="nav nav-tabs" id="companyGuestTabs" role="tablist">';
      bookings.forEach(function(booking, index) {
        const isActive = index === 0 ? 'active' : '';
        const tabId = 'gx-tab-' + booking.id;
        const panelId = 'gx-panel-' + booking.id;
        const guestName = (booking.guest_name || 'Guest ' + (index + 1)).split(' ')[0];
        
        html += '<li class="nav-item">';
        html += '<a class="nav-link font-weight-bold ' + isActive + '" id="' + tabId + '" data-toggle="tab" data-target="#' + panelId + '" href="#' + panelId + '" role="tab" style="color: #495057; padding: 10px 20px;"><i class="fa fa-user-o text-muted mr-1"></i> ' + guestName + '</a></li>';
      });
      html += '</ul></div>';

      // Tabs Content
      html += '<div class="card-body p-0 border border-top-0 rounded-bottom">';
      html += '<div class="tab-content" id="companyGuestTabsContent">';
      
      bookings.forEach(function(booking, index) {
        const isActive = index === 0 ? 'show active' : '';
        const panelId = 'gx-panel-' + booking.id;
        
        // Data prep
        const checkIn = booking.check_in ? new Date(booking.check_in).toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' }) : 'N/A';
        const checkOut = booking.check_out ? new Date(booking.check_out).toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' }) : 'N/A';
        const room = booking.room || {};
        const roomTotal = parseFloat(booking.total_price || 0);
        const paidAmount = parseFloat(booking.amount_paid || 0);
        
        // Badges
        let statusText = booking.status;
        if (booking.check_in_status === 'checked_out' && statusText === 'confirmed') statusText = 'completed';
        
        const statusBadge = statusText === 'confirmed' ? '<span class="badge badge-success px-2 py-1"><i class="fa fa-check"></i> Confirmed</span>' :
                            statusText === 'completed' ? '<span class="badge badge-primary px-2 py-1"><i class="fa fa-flag-checkered"></i> Completed</span>' :
                            statusText === 'pending' ? '<span class="badge badge-warning px-2 py-1"><i class="fa fa-clock-o"></i> Pending</span>' :
                            '<span class="badge badge-secondary px-2 py-1">' + (statusText ? statusText.charAt(0).toUpperCase() + statusText.slice(1) : 'N/A') + '</span>';
                            
        const paymentBadge = booking.payment_status === 'paid' ? '<span class="badge badge-success px-2 py-1"><i class="fa fa-check-circle"></i> Paid</span>' :
                            booking.payment_status === 'partial' ? '<span class="badge badge-info px-2 py-1">Partial</span>' :
                            '<span class="badge badge-warning px-2 py-1">Unpaid</span>';
                            
        const checkInBadge = booking.check_in_status === 'checked_in' ? '<span class="badge badge-info px-2 py-1"><i class="fa fa-sign-in"></i> Checked In</span>' :
                            booking.check_in_status === 'checked_out' ? '<span class="badge badge-success px-2 py-1"><i class="fa fa-sign-out"></i> Checked Out</span>' :
                            '<span class="badge badge-light border px-2 py-1 text-muted"><i class="fa fa-clock-o"></i> Check-in Pending</span>';

        html += '<div class="tab-pane fade ' + isActive + '" id="' + panelId + '" role="tabpanel">';
        html += '<div class="p-4 bg-white rounded-bottom">';
        
        // Guest Header
        html += '<div class="d-flex align-items-center mb-4 pb-2 border-bottom">';
        html += '<div><h4 class="mb-0 text-dark font-weight-bold">' + (booking.guest_name || 'N/A') + '</h4>';
        html += '<div class="text-muted mt-1 w-100"><i class="fa fa-hashtag"></i> Ref: ' + booking.booking_reference + ' <span class="mx-2">|</span> <i class="fa fa-phone"></i> ' + (booking.guest_phone || 'N/A') + ' <span class="mx-2">|</span> <i class="fa fa-envelope-o"></i> ' + (booking.guest_email || 'N/A') + '</div></div>';
        html += '<div class="ml-auto">' + statusBadge + ' <span class="mx-1"></span> ' + paymentBadge + ' <span class="mx-1"></span> ' + checkInBadge + '</div>';
        html += '</div>';

        html += '<div class="row">';
        
        // Col 1: Stay & Room
        html += '<div class="col-md-6">';
        html += '<div class="card bg-light border-0 mb-3 h-100">';
        html += '<div class="card-body p-3">';
        html += '<h6 class="text-uppercase text-muted small mb-3 font-weight-bold" style="letter-spacing: 0.5px;"><i class="fa fa-bed mr-2 text-primary"></i> Stay & Room Info</h6>';
        html += '<ul class="list-group list-group-flush border-top-0 border-bottom-0">';
        html += '<li class="list-group-item px-0 py-2 bg-transparent border-light d-flex justify-content-between align-items-center"><span class="text-muted small"><strong>Room</strong></span> <span class="text-dark font-weight-bold">' + (room.room_number || 'TBD') + ' &mdash; <span class="text-muted font-weight-normal">' + (room.room_type || 'Standard') + '</span></span></li>';
        html += '<li class="list-group-item px-0 py-2 bg-transparent border-light d-flex justify-content-between align-items-center"><span class="text-muted small"><strong>Check In</strong></span> <span class="text-dark">' + checkIn + '</span></li>';
        html += '<li class="list-group-item px-0 py-2 bg-transparent border-light d-flex justify-content-between align-items-center"><span class="text-muted small"><strong>Check Out</strong></span> <span class="text-dark">' + checkOut + '</span></li>';
        html += '<li class="list-group-item px-0 py-2 bg-transparent border-light d-flex justify-content-between align-items-center"><span class="text-muted small"><strong>Duration</strong></span> <span class="badge badge-primary badge-pill">' + (booking.nights || calculateNightsNumber(booking.check_in, booking.check_out)) + ' Nights</span></li>';
        html += '<li class="list-group-item px-0 py-2 bg-transparent border-light d-flex justify-content-between align-items-center"><span class="text-muted small"><strong>Guests In Room</strong></span> <span class="text-dark">' + (booking.number_of_guests || 1) + '</span></li>';
        html += '</ul>';
        html += '</div></div></div>';

        // Col 2: Financials
        html += '<div class="col-md-6">';
        html += '<div class="card bg-light border-0 mb-3 h-100">';
        html += '<div class="card-body p-3">';
        html += '<h6 class="text-uppercase text-muted small mb-3 font-weight-bold" style="letter-spacing: 0.5px;"><i class="fa fa-dollar mr-2 text-success"></i> Room Billing Info</h6>';
        
        const serviceCharges = parseFloat(booking.service_charges_usd || 0);
        const isSelfPay = booking.payment_responsibility === 'self';
        const totalBookingBill = roomTotal + (isSelfPay ? 0 : serviceCharges);
        const guestRemaining = totalBookingBill - paidAmount;

        html += '<ul class="list-group list-group-flush border-top-0 border-bottom-0">';
        html += '<li class="list-group-item px-0 py-2 bg-transparent border-light d-flex justify-content-between align-items-center"><span class="text-muted small"><strong>Price / Night</strong></span> <span class="text-dark">$' + parseFloat(room.price_per_night || 0).toFixed(2) + '</span></li>';
        html += '<li class="list-group-item px-0 py-2 bg-transparent border-light d-flex justify-content-between align-items-center"><span class="text-muted small"><strong>Total Room Charge</strong></span> <span class="text-dark font-weight-bold">$' + roomTotal.toFixed(2) + '</span></li>';
        if (serviceCharges > 0) {
            html += '<li class="list-group-item px-0 py-2 bg-transparent border-light d-flex justify-content-between align-items-center"><span class="text-muted small"><strong>Service Charges</strong> ' + (isSelfPay ? '<span class="badge badge-warning ml-1">Self Pay</span>' : '<span class="badge badge-secondary ml-1">Company Covers</span>') + '</span> <span class="' + (isSelfPay ? 'text-dark' : 'text-danger font-weight-bold') + '">+$' + serviceCharges.toFixed(2) + '</span></li>';
        }
        
        html += '<li class="list-group-item px-0 py-2 bg-transparent border-secondary d-flex justify-content-between align-items-center mt-2 border-top"><span class="text-dark font-weight-bold">Total Bill</span> <strong class="text-primary" style="font-size:1.15em;">$' + totalBookingBill.toFixed(2) + '</strong></li>';
        
        if (guestRemaining > 0.01) {
            html += '<li class="list-group-item px-0 py-2 bg-transparent border-0 d-flex justify-content-between align-items-center"><span class="text-dark font-weight-bold">Balance Due</span> <strong class="text-danger" style="font-size:1.15em;">$' + guestRemaining.toFixed(2) + '</strong></li>';
        } else if (paidAmount >= totalBookingBill && totalBookingBill > 0) {
            html += '<li class="list-group-item px-0 py-2 bg-transparent border-0 d-flex justify-content-end align-items-center pt-3"><span class="badge badge-success px-3 py-2" style="font-size:1em;"><i class="fa fa-check-circle mr-1"></i> Balance fully resolved</span></li>';
        }
        
        html += '</ul>';
        html += '</div></div></div>'; // End Right Col // End Card
        
        html += '</div>'; // End Row

        // Identity Records Footer - Check-In
        if (booking.id_document_type || booking.id_scan_path || booking.guest_signature_path) {
          html += '<div class="alert alert-secondary border border-secondary mt-3 mb-0 px-3 py-3 rounded">';
          html += '<div class="mb-3 border-bottom pb-2 border-secondary"><span class="text-uppercase text-muted font-weight-bold mr-2"><i class="fa fa-id-card-o mr-1"></i> Check-in Records:</span>';
          html += '<span class="small text-dark mt-1 d-inline-block"><strong>ID Type:</strong> ' + (booking.id_document_type || 'N/A') + ' <span class="mx-1 text-muted">|</span> <strong>ID No:</strong> ' + (booking.id_document_number || 'N/A') + '</span></div>';
          
          html += '<div class="row align-items-center justify-content-center">';
          if (booking.id_scan_path) {
            html += '<div class="col-sm-4 text-center mb-2">';
            html += '<div class="small font-weight-bold text-muted mb-1 text-uppercase" style="letter-spacing: 0.5px;">Front ID</div>';
            html += '<a href="' + baseUrl + booking.id_scan_path + '" target="_blank">';
            html += '<img src="' + baseUrl + booking.id_scan_path + '" class="img-fluid rounded bg-white p-1 shadow-sm border" style="max-height: 120px; object-fit: contain;" onerror="this.outerHTML=\'<span class=text-danger small><i class=\\\'fa fa-exclamation-triangle\\\'></i> Missing Image</span>\'">';
            html += '</a></div>';
          }
          if (booking.id_scan_back_path) {
            html += '<div class="col-sm-4 text-center mb-2">';
            html += '<div class="small font-weight-bold text-muted mb-1 text-uppercase" style="letter-spacing: 0.5px;">Back ID</div>';
            html += '<a href="' + baseUrl + booking.id_scan_back_path + '" target="_blank">';
            html += '<img src="' + baseUrl + booking.id_scan_back_path + '" class="img-fluid rounded bg-white p-1 shadow-sm border" style="max-height: 120px; object-fit: contain;" onerror="this.outerHTML=\'<span class=text-danger small><i class=\\\'fa fa-exclamation-triangle\\\'></i> Missing Image</span>\'">';
            html += '</a></div>';
          }
          if (booking.guest_signature_path) {
             html += '<div class="col-sm-4 text-center mb-2">';
             html += '<div class="small font-weight-bold text-muted mb-1 text-uppercase" style="letter-spacing: 0.5px;"><i class="fa fa-pencil text-primary mr-1"></i> Check-In Signature</div>';
             html += '<div class="bg-white rounded border shadow-sm p-1 d-inline-block">';
             html += '<a href="' + baseUrl + booking.guest_signature_path + '" target="_blank">';
             html += '<img src="' + baseUrl + booking.guest_signature_path + '" class="img-fluid" style="max-height: 110px; object-fit: contain;" onerror="this.outerHTML=\'<span class=text-danger small><i class=\\\'fa fa-exclamation-triangle\\\'></i> Missing Image</span>\'">';
             html += '</a></div></div>';
          }
          html += '</div>'; // end row inside alert
          html += '</div>'; // end alert
        }

        // Checkout Signature Section
        if (booking.checkout_signature_path) {
          html += '<div class="alert alert-info border border-info mt-3 mb-0 px-3 py-3 rounded">';
          html += '<div class="mb-3 border-bottom pb-2 border-info"><span class="text-uppercase text-muted font-weight-bold mr-2"><i class="fa fa-sign-out mr-1 text-info"></i> Check-Out Records:</span>';
          if (booking.check_in_status === 'checked_out') {
            html += '<span class="badge badge-success ml-2"><i class="fa fa-check-circle"></i> Fully Checked Out</span>';
          } else {
            html += '<span class="badge badge-warning ml-2"><i class="fa fa-clock-o"></i> Signature Received – Pending Finalization</span>';
          }
          html += '</div>';
          
          html += '<div class="row align-items-center justify-content-center">';
          html += '<div class="col-sm-5 text-center mb-2">';
          html += '<div class="small font-weight-bold text-muted mb-1 text-uppercase" style="letter-spacing: 0.5px;"><i class="fa fa-pencil text-info mr-1"></i> Check-Out Signature</div>';
          html += '<div class="bg-white rounded border shadow-sm p-1 d-inline-block">';
          html += '<a href="' + baseUrl + booking.checkout_signature_path + '" target="_blank">';
          html += '<img src="' + baseUrl + booking.checkout_signature_path + '" class="img-fluid" style="max-height: 130px; object-fit: contain;" onerror="this.outerHTML=\'<span class=text-danger small><i class=\\\'fa fa-exclamation-triangle\\\'></i> Missing Image</span>\'">';
          html += '</a></div></div>';
          html += '</div>'; // end row
          html += '</div>'; // end alert
        } else if (booking.check_in_status === 'checked_in') {
          html += '<div class="alert alert-light border mt-3 mb-0 px-3 py-2 rounded text-muted small">';
          html += '<i class="fa fa-info-circle mr-1"></i> No check-out signature recorded yet for this guest.';
          html += '</div>';
        }

        // Footer Actions for Booking
        html += '<div class="d-flex justify-content-end mt-4 pt-3 border-top">';
        if (booking.check_in_status === 'checked_in') {
          html += '<button class="btn btn-outline-warning btn-sm px-4 mr-2" onclick="openLateCheckoutModal(' + booking.id + ', \'' + (booking.guest_name || 'Guest').replace(/'/g, "\\'") + '\', \'' + (room.room_number || '') + '\')"><i class="fa fa-clock-o mr-1"></i> Late Check-out</button>';
        }
        if (booking.status !== 'cancelled' && booking.check_in_status === 'pending') {
          html += '<button class="btn btn-outline-danger btn-sm px-4" onclick="openCancelModal(' + booking.id + ')"><i class="fa fa-times mr-1"></i> Cancel Guest Booking</button>';
        }
        html += '</div>';

        html += '</div></div>'; // end tab pane
      });
      
      html += '</div></div></div>'; // End card body & tabs container
      html += '</div>'; // End view container

      
       $('#companyBookingGroupContent').html(html);

       // Update Print Button
       var printBtn = '<button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>';
       $('#companyBookingGroupModal .modal-footer').html(printBtn);

    } else {
      $('#companyBookingGroupContent').html('<div class="alert alert-danger">Failed to load booking details.</div>');
    }
  })
  .catch(error => {
    console.error('Error:', error);
    $('#companyBookingGroupContent').html('<div class="alert alert-danger">Error loading booking details: ' + error.message + '</div>');
  });
}

// Modify Dates Functions
function openModifyDatesModal(bookingId, checkIn, currentCheckOut) {
  document.getElementById('modify_booking_id').value = bookingId;
  const checkInInput = document.getElementById('modify_check_in');
  const checkOutInput = document.getElementById('modify_new_check_out');
  
  checkInInput.value = checkIn;
  checkOutInput.value = currentCheckOut;
  checkOutInput.min = checkIn;
  document.getElementById('modify_reason').value = '';
  document.getElementById('modifyDatesCostPreview').style.display = 'none';
  document.getElementById('modifyDatesAlert').innerHTML = '';
  
  // Add event listener for date change
  checkOutInput.onchange = function() {
    const checkInDate = new Date(checkInInput.value);
    const checkOutDate = new Date(checkOutInput.value);
    if (checkInDate && checkOutDate && checkOutDate > checkInDate) {
      const checkInParts = checkIn.split('-');
      const currentCheckOutParts = currentCheckOut.split('-');
      const currentCheckInDate = new Date(parseInt(checkInParts[0]), parseInt(checkInParts[1]) - 1, parseInt(checkInParts[2]));
      const currentCheckOutDate = new Date(parseInt(currentCheckOutParts[0]), parseInt(currentCheckOutParts[1]) - 1, parseInt(currentCheckOutParts[2]));
      const currentNights = Math.ceil((currentCheckOutDate - currentCheckInDate) / (1000 * 60 * 60 * 24));
      const diffTime = checkOutDate - checkInDate;
      const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24));
      const nightsDiff = diffDays - currentNights;
      
      if (nightsDiff !== 0) {
        document.getElementById('modifyNightsDiff').textContent = nightsDiff > 0 ? '+' + nightsDiff : nightsDiff;
        document.getElementById('modifyDatesCostPreview').style.display = 'block';
      } else {
        document.getElementById('modifyDatesCostPreview').style.display = 'none';
      }
    }
  };
  
  $('#modifyDatesModal').modal('show');
}

function submitModifyDates() {
  const form = document.getElementById('modifyDatesForm');
  const alertDiv = document.getElementById('modifyDatesAlert');
  const submitBtn = event.target;
  const originalText = submitBtn.innerHTML;
  
  if (alertDiv) {
    alertDiv.innerHTML = '';
  }
  
  if (!form.checkValidity()) {
    form.reportValidity();
    return;
  }
  
  const checkIn = document.getElementById('modify_check_in').value;
  const newCheckOut = document.getElementById('modify_new_check_out').value;
  
  if (new Date(newCheckOut) <= new Date(checkIn)) {
    if (alertDiv) {
      alertDiv.innerHTML = '<div class="alert alert-danger">Check-out date must be after check-in date.</div>';
    }
    return;
  }
  
  submitBtn.disabled = true;
  submitBtn.innerHTML = '<i class="fa fa-spinner fa-spin"></i> Saving...';
  
  const bookingId = document.getElementById('modify_booking_id').value;
  
  @php
    $modifyRoute = (str_starts_with(request()->route()->getName() ?? '', 'admin.')) 
      ? 'admin.bookings.modify-dates' 
      : 'reception.bookings.modify-dates';
  @endphp
  
  fetch('{{ route($modifyRoute, ":id") }}'.replace(':id', bookingId), {
    method: 'PUT',
    headers: {
      'X-CSRF-TOKEN': '{{ csrf_token() }}',
      'Accept': 'application/json',
      'Content-Type': 'application/json'
    },
    body: JSON.stringify({
      new_check_out: newCheckOut,
      reason: document.getElementById('modify_reason').value
    })
  })
  .then(response => response.json())
  .then(data => {
    if (data.success) {
      swal({
        title: "Success!",
        text: data.message || "Booking dates modified successfully!",
        type: "success",
        confirmButtonColor: "#28a745"
      }, function() {
        $('#modifyDatesModal').modal('hide');
        location.reload();
      });
    } else {
      let errorMsg = data.message || 'An error occurred. Please try again.';
      if (data.errors) {
        const errorList = Object.values(data.errors).flat().join('<br>');
        errorMsg = errorList;
      }
      if (alertDiv) {
        alertDiv.innerHTML = '<div class="alert alert-danger">' + errorMsg + '</div>';
      }
      submitBtn.disabled = false;
      submitBtn.innerHTML = originalText;
    }
  })
  .catch(error => {
    console.error('Error:', error);
    if (alertDiv) {
      alertDiv.innerHTML = '<div class="alert alert-danger">An error occurred. Please try again.</div>';
    }
    submitBtn.disabled = false;
    submitBtn.innerHTML = originalText;
  });
}

function updateCheckIn(bookingId, checkInStatus) {
  swal({
    title: "Check In Guest?",
    text: "Are you sure you want to check in this guest?",
    type: "warning",
    showCancelButton: true,
    confirmButtonColor: "#28a745",
    cancelButtonColor: "#d33",
    confirmButtonText: "Yes, Check In!",
    cancelButtonText: "Cancel",
    closeOnConfirm: false,
    showLoaderOnConfirm: true
  }, function(isConfirm) {
    if (isConfirm) {
      @php
        $checkInUpdateRoute = (str_starts_with(request()->route()->getName() ?? '', 'admin.')) 
          ? 'admin.bookings.update-checkin' 
          : 'reception.bookings.update-checkin';
      @endphp
      fetch('{{ route($checkInUpdateRoute, ":id") }}'.replace(':id', bookingId), {
        method: 'PUT',
        headers: {
          'Accept': 'application/json',
          'X-CSRF-TOKEN': '{{ csrf_token() }}',
          'Content-Type': 'application/json'
        },
        body: JSON.stringify({ check_in_status: checkInStatus })
      })
      .then(response => response.json())
      .then(data => {
        if (data.success) {
          swal({
            title: "Success!",
            text: data.message || "Guest checked in successfully!",
            type: "success",
            confirmButtonColor: "#28a745"
          }, function() {
            location.reload();
          });
        } else {
          swal({
            title: "Error!",
            text: data.message || "Failed to check in guest.",
            type: "error",
            confirmButtonColor: "#e77a3a"
          });
        }
      })
      .catch(error => {
        console.error('Error:', error);
        swal({
          title: "Error!",
          text: "An error occurred while checking in the guest.",
          type: "error",
          confirmButtonColor: "#e77a3a"
        });
      });
    }
  });
}

function saveNotes() {
  const bookingId = document.getElementById('notes_booking_id').value;
  const notes = document.getElementById('admin_notes').value;
  
  fetch('{{ url("/manager/bookings") }}/' + bookingId + '/notes', {
    method: 'PUT',
    headers: {
      'Accept': 'application/json',
      'X-CSRF-TOKEN': '{{ csrf_token() }}',
      'Content-Type': 'application/json'
    },
    body: JSON.stringify({ admin_notes: notes })
  })
  .then(response => response.json())
  .then(data => {
    if (data.success) {
      swal({
        title: "Saved!",
        text: data.message || "Admin notes saved successfully.",
        type: "success",
        confirmButtonColor: "#e77a3a"
      }, function() {
        $('#notesModal').modal('hide');
        location.reload();
      });
    } else {
      swal({
        title: "Error!",
        text: data.message || "Failed to save notes.",
        type: "error",
        confirmButtonColor: "#e77a3a"
      });
    }
  })
  .catch(error => {
    console.error('Error:', error);
    swal({
      title: "Error!",
      text: "An error occurred while saving notes.",
      type: "error",
      confirmButtonColor: "#e77a3a"
    });
  });
}

// Countdown timer for pending bookings
function updateCountdownTimers() {
  document.querySelectorAll('.countdown-timer').forEach(function(timer) {
    const expiresTimestamp = parseInt(timer.getAttribute('data-expires'));
    const now = Date.now();
    const remaining = expiresTimestamp - now;
    
    if (remaining > 0) {
      const minutes = Math.floor(remaining / 60000);
      const seconds = Math.floor((remaining % 60000) / 1000);
      timer.textContent = minutes + 'm ' + seconds + 's';
    } else {
      timer.textContent = 'Expired';
      timer.parentElement.classList.add('text-danger');
      // Reload page after 5 seconds if expired
      setTimeout(function() {
        location.reload();
      }, 5000);
    }
  });
}

// Update countdown timers every second
setInterval(updateCountdownTimers, 1000);
updateCountdownTimers();


// Send reminder function (sends both email and SMS)
function sendReminder(bookingId) {
  Swal.fire({
    title: 'Send Reminders?',
    text: 'Are you sure you want to send payment reminders via Email and SMS?',
    icon: 'question',
    showCancelButton: true,
    confirmButtonColor: '#e77a3a',
    cancelButtonColor: '#6c757d',
    confirmButtonText: 'Yes, Send Both!',
    cancelButtonText: 'Cancel'
  }).then((result) => {
    if (result.isConfirmed) {
      // Show loading
      Swal.fire({
        title: 'Sending...',
        text: 'Please wait while we send Email and SMS reminders.',
        allowOutsideClick: false,
        didOpen: () => {
          Swal.showLoading();
        }
      });
      
      fetch('{{ url("/manager/bookings") }}/' + bookingId + '/send-reminder', {
        method: 'POST',
        headers: {
          'Accept': 'application/json',
          'X-CSRF-TOKEN': '{{ csrf_token() }}',
          'Content-Type': 'application/json'
        },
        body: JSON.stringify({ reminder_type: 'both' })
      })
      .then(response => response.json())
      .then(data => {
        if (data.success) {
          Swal.fire({
            icon: 'success',
            title: 'Reminders Sent!',
            text: data.message,
            confirmButtonColor: '#e77a3a',
            confirmButtonText: 'OK'
          });
        } else {
          Swal.fire({
            icon: data.success === false ? 'warning' : 'error',
            title: data.success ? 'Partially Sent' : 'Failed to Send',
            text: data.message || 'Failed to send reminders.',
            confirmButtonColor: '#e77a3a',
            confirmButtonText: 'OK'
          });
        }
      })
      .catch(error => {
        console.error('Error:', error);
        Swal.fire({
          icon: 'error',
          title: 'Error',
          text: 'An error occurred while sending the reminders.',
          confirmButtonColor: '#e77a3a',
          confirmButtonText: 'OK'
        });
      });
    }
  });
}

// Manager Extension/Decrease Functions
let managerBookingData = {};

@if(($bookingType ?? 'individual') == 'corporate')
  @foreach($bookings as $group)
    @php
      $companyBookings = $group['bookings'] ?? collect();
    @endphp
    @foreach($companyBookings as $booking)
      @if($booking->check_in_status === 'checked_in' && $booking->room)
        @php
          $foundCompanyId = $booking->company_id ?? ($group['company_id'] ?? ($group['company']->id ?? null));
        @endphp
        managerBookingData[{{ $booking->id }}] = {
          id: {{ $booking->id }},
          companyId: {{ $foundCompanyId ?? 'null' }},
          roomId: {{ $booking->room_id }},
          roomPrice: {{ $booking->room->price_per_night ?? 0 }},
          currentCheckOut: '{{ $booking->check_out->format('Y-m-d') }}',
          checkIn: '{{ $booking->check_in->format('Y-m-d') }}'
        };
      @endif
    @endforeach
  @endforeach
@else
  @foreach($bookings as $booking)
    @if($booking->check_in_status === 'checked_in' && $booking->room)
      managerBookingData[{{ $booking->id }}] = {
        id: {{ $booking->id }},
        roomId: {{ $booking->room_id }},
        roomPrice: {{ $booking->room->price_per_night ?? 0 }},
        currentCheckOut: '{{ $booking->check_out->format('Y-m-d') }}',
        checkIn: '{{ $booking->check_in->format('Y-m-d') }}',
        companyId: null
      };
    @endif
  @endforeach
@endif

function openManagerExtensionModal(bookingId, checkIn, currentCheckOut) {
  const bookingData = managerBookingData[bookingId];
  if (!bookingData) {
    swal("Error", "Booking information not found.", "error");
    return;
  }
  
  document.getElementById('manager_extension_booking_id').value = bookingId;
  document.getElementById('manager_extension_company_id').value = '';
  const dateInput = document.getElementById('manager_extension_new_check_out');
  dateInput.value = '';
  dateInput.min = currentCheckOut;
  document.getElementById('manager_extension_reason').value = '';
  document.getElementById('managerExtensionCostPreview').style.display = 'none';
  
  $('#managerExtensionModal .modal-title').html('<i class="fa fa-calendar-plus-o"></i> Extend Booking');
  $('#managerExtensionModal .alert-info strong').text('Note:');
  $('#managerExtensionModal .alert-info').text('The booking will be extended and the price will be adjusted automatically.');

  $('#managerExtensionModal').modal('show');
}

function openGroupExtensionModal(companyId, checkIn, currentCheckOut) {
  document.getElementById('manager_extension_booking_id').value = '';
  document.getElementById('manager_extension_company_id').value = companyId;
  
  const dateInput = document.getElementById('manager_extension_new_check_out');
  dateInput.value = '';
  dateInput.min = currentCheckOut;
  document.getElementById('manager_extension_reason').value = '';
  document.getElementById('managerExtensionCostPreview').style.display = 'none';
  
  $('#managerExtensionModal .modal-title').html('<i class="fa fa-calendar-plus-o"></i> Extend Group Stay');
  $('#managerExtensionModal .alert-info').html('<i class="fa fa-info-circle"></i> <strong>Note:</strong> This will extend the stay for ALL guests in this group who are currently checked in.');

  $('#managerExtensionModal').modal('show');
}

function openManagerDecreaseModal(bookingId, checkIn, currentCheckOut) {
  const bookingData = managerBookingData[bookingId];
  if (!bookingData) {
    swal("Error", "Booking information not found.", "error");
    return;
  }
  
  document.getElementById('manager_decrease_booking_id').value = bookingId;
  document.getElementById('manager_decrease_company_id').value = '';
  const dateInput = document.getElementById('manager_decrease_new_check_out');
  dateInput.value = '';
  dateInput.max = currentCheckOut;
  document.getElementById('manager_decrease_reason').value = '';
  
  $('#managerDecreaseModal .modal-title').html('<i class="fa fa-calendar-minus-o"></i> Decrease Booking');
  $('#managerDecreaseModal .alert-warning strong').text('Note:');
  
  $('#managerDecreaseModal').modal('show');
}

function openGroupDecreaseModal(companyId, checkIn, currentCheckOut) {
  document.getElementById('manager_decrease_booking_id').value = '';
  document.getElementById('manager_decrease_company_id').value = companyId;
  
  const dateInput = document.getElementById('manager_decrease_new_check_out');
  dateInput.value = '';
  dateInput.max = currentCheckOut;
  document.getElementById('manager_decrease_reason').value = '';
  
  $('#managerDecreaseModal .modal-title').html('<i class="fa fa-calendar-minus-o"></i> Decrease Group Stay');
  $('#managerDecreaseModal .alert-warning').html('<i class="fa fa-exclamation-triangle"></i> <strong>Note:</strong> This will decrease the stay for ALL guests in this group who are currently checked in. No refund will be applied.');
  
  $('#managerDecreaseModal').modal('show');
}

// Attach event listeners after modals are shown to ensure they work
$('#managerExtensionModal').on('shown.bs.modal', function() {
  const dateInputEl = document.getElementById('manager_extension_new_check_out');
  $(dateInputEl).off('change input').on('change input', calculateManagerExtensionCost);
});

$('#managerDecreaseModal').on('shown.bs.modal', function() {
  const dateInputEl = document.getElementById('manager_decrease_new_check_out');
  $(dateInputEl).off('change input');
});

// Event listeners are attached when modals are opened for better reliability

function calculateManagerExtensionCost() {
  const bookingId = document.getElementById('manager_extension_booking_id').value;
  const companyId = document.getElementById('manager_extension_company_id').value;
  const isGroup = !!companyId;
  const alertDiv = document.getElementById('managerExtensionAlert');
  alertDiv.innerHTML = ''; // Clear previous alerts

  let bookingData = null;
  let groupBookings = [];
  let currentCheckOutDate = null;
  let totalDailyRate = 0;

  if (isGroup) {
    // For group, sum all checked-in bookings for this company
    for (const id in managerBookingData) {
      if (managerBookingData[id].companyId == companyId) {
        groupBookings.push(managerBookingData[id]);
        totalDailyRate += managerBookingData[id].roomPrice;
        if (!currentCheckOutDate) {
          currentCheckOutDate = new Date(managerBookingData[id].currentCheckOut + 'T00:00:00');
        }
      }
    }
  } else {
    bookingData = managerBookingData[bookingId];
    if (bookingData) {
      totalDailyRate = bookingData.roomPrice;
      currentCheckOutDate = new Date(bookingData.currentCheckOut + 'T00:00:00');
    }
  }

  if (!currentCheckOutDate) {
    document.getElementById('managerExtensionCostPreview').style.display = 'none';
    return;
  }
  
  const newDateVal = document.getElementById('manager_extension_new_check_out').value;
  if (!newDateVal) {
    document.getElementById('managerExtensionCostPreview').style.display = 'none';
    return;
  }
  
  const requestedDate = new Date(newDateVal + 'T00:00:00');
  
  if (requestedDate <= currentCheckOutDate) {
    document.getElementById('managerExtensionCostPreview').style.display = 'none';
    return;
  }

  // Live availability check (for single booking)
  if (!isGroup && bookingData) {
    checkManagerExtensionAvailability(bookingData.id, bookingData.checkIn, newDateVal);
  }
  
  const diffTime = requestedDate - currentCheckOutDate;
  const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24));
  
  if (diffDays > 0) {
    const totalCost = totalDailyRate * diffDays;
    document.getElementById('managerExtensionNights').textContent = diffDays;
    document.getElementById('managerExtensionRoomPrice').textContent = totalDailyRate.toFixed(2);
    document.getElementById('managerExtensionTotalCost').textContent = totalCost.toFixed(2);
    
    // Update labels if it's a group
    if (isGroup) {
      document.querySelector('#managerExtensionCostPreview p').innerHTML = 
        `<span id="managerExtensionNights">${diffDays}</span> night(s) extension × 
        $<span id="managerExtensionRoomPrice">${totalDailyRate.toFixed(2)}</span> (Group Total Rate) = 
        <strong>Additional Cost: $<span id="managerExtensionTotalCost">${totalCost.toFixed(2)}</span></strong>`;
    }

    document.getElementById('managerExtensionCostPreview').style.display = 'block';
  } else {
    document.getElementById('managerExtensionCostPreview').style.display = 'none';
  }
}

function checkManagerExtensionAvailability(bookingId, checkIn, newCheckOut) {
    const bookingData = managerBookingData[bookingId];
    if (!bookingData) return;

    const alertDiv = document.getElementById('managerExtensionAlert');
    const roomId = bookingData.room_id || bookingData.roomId; // Ensure we have the room ID
    
    // Attempt to get room ID if not in data (fallback for some views)
    const effectiveRoomId = roomId || bookingData.id; // This is a fallback, ideally roomId is there

    const url = `{{ route('admin.bookings.check-availability') }}?room_id=${effectiveRoomId}&check_in=${checkIn}&check_out=${newCheckOut}&exclude_booking_id=${bookingId}`;

    fetch(url)
    .then(response => response.json())
    .then(data => {
        if (data.success && !data.available) {
            alertDiv.innerHTML = `<div class="alert alert-danger"><i class="fa fa-exclamation-triangle"></i> <strong>Conflict Detected:</strong> ${data.message}</div>`;
        } else if (data.success && data.available) {
            alertDiv.innerHTML = `<div class="alert alert-success"><i class="fa fa-check-circle"></i> Room is available for the extended period.</div>`;
        }
    })
    .catch(error => {
        console.error('Error checking availability:', error);
    });
}


function submitManagerExtension(btn) {
  const bookingId = document.getElementById('manager_extension_booking_id').value;
  const companyId = document.getElementById('manager_extension_company_id').value;
  const newCheckOut = document.getElementById('manager_extension_new_check_out').value;
  const reason = document.getElementById('manager_extension_reason').value;
  const submitBtn = btn || (typeof event !== 'undefined' ? event.target : null);
  
  if (!newCheckOut) {
    swal("Error", "Please select a new check-out date.", "error");
    return;
  }

  submitBtn.disabled = true;
  submitBtn.innerHTML = '<i class="fa fa-spinner fa-spin"></i> Processing...';

  const isGroup = !!companyId;
  const url = isGroup 
    ? (isReception ? '{{ url("/reception/companies") }}/' + companyId + '/modify-dates' : '{{ url("/manager/companies") }}/' + companyId + '/modify-dates')
    : (isReception ? '{{ url("/reception/bookings") }}/' + bookingId + '/modify-dates' : '{{ url("/manager/bookings") }}/' + bookingId + '/modify-dates');

  fetch(url, {
    method: 'PUT',
    headers: {
      'X-CSRF-TOKEN': '{{ csrf_token() }}',
      'Accept': 'application/json',
      'Content-Type': 'application/json'
    },
    body: JSON.stringify({
      new_check_out: newCheckOut,
      reason: reason
    })
  })
  .then(response => response.json())
  .then(data => {
    if (data.success) {
      swal({
        title: "Success!",
        text: data.message,
        type: "success"
      }, function() {
        location.reload();
      });
    } else {
      swal("Error", data.message || "Failed to update stay.", "error");
      submitBtn.disabled = false;
      submitBtn.innerHTML = 'Save Extension';
    }
  })
  .catch(error => {
    console.error('Error:', error);
    swal("Error", "A system error occurred.", "error");
    submitBtn.disabled = false;
    submitBtn.innerHTML = 'Save Extension';
  });
}

function submitManagerDecrease(btn) {
  const bookingId = document.getElementById('manager_decrease_booking_id').value;
  const companyId = document.getElementById('manager_decrease_company_id').value;
  const newCheckOut = document.getElementById('manager_decrease_new_check_out').value;
  const reason = document.getElementById('manager_decrease_reason').value;
  const submitBtn = btn || (typeof event !== 'undefined' ? event.target : null);
  
  if (!newCheckOut) {
    swal("Error", "Please select a new check-out date.", "error");
    return;
  }

  submitBtn.disabled = true;
  submitBtn.innerHTML = '<i class="fa fa-spinner fa-spin"></i> Processing...';
  
  const isGroup = !!companyId;
  const url = isGroup
    ? (isReception ? '{{ url("/reception/companies") }}/' + companyId + '/modify-dates' : '{{ url("/manager/companies") }}/' + companyId + '/modify-dates')
    : (isReception ? '{{ url("/reception/bookings") }}/' + bookingId + '/modify-dates' : '{{ url("/manager/bookings") }}/' + bookingId + '/modify-dates');

  fetch(url, {
    method: 'PUT',
    headers: {
      'X-CSRF-TOKEN': '{{ csrf_token() }}',
      'Accept': 'application/json',
      'Content-Type': 'application/json'
    },
    body: JSON.stringify({
      new_check_out: newCheckOut,
      reason: reason
    })
  })
  .then(response => response.json())
  .then(data => {
    if (data.success) {
      swal({
        title: "Success!",
        text: data.message,
        type: "success"
      }, function() {
        location.reload();
      });
    } else {
      swal("Error", data.message || "Failed to update stay.", "error");
      submitBtn.disabled = false;
      submitBtn.innerHTML = 'Save Changes';
    }
  })
  .catch(error => {
    console.error('Error:', error);
    swal("Error", "A system error occurred.", "error");
    submitBtn.disabled = false;
    submitBtn.innerHTML = 'Save Changes';
  });
}

/**
 * Cancellation Logic
 */
function openCancelModal(bookingId) {
  document.getElementById('cancel_booking_id').value = bookingId;
  document.getElementById('cancel_company_id').value = '';
  document.getElementById('cancellation_reason').value = '';
  $('#cancelBookingModal .modal-title').html('<i class="fa fa-times-circle"></i> Cancel Booking');
  $('#cancelBookingModal .btn-danger').text('Cancel Booking');
  $('#cancelBookingModal').modal('show');
}

function openCancelGroupModal(companyId) {
  document.getElementById('cancel_booking_id').value = '';
  document.getElementById('cancel_company_id').value = companyId;
  document.getElementById('cancellation_reason').value = '';
  $('#cancelBookingModal .modal-title').html('<i class="fa fa-users"></i> Cancel Group Booking');
  $('#cancelBookingModal .btn-danger').text('Cancel Entire Group');
  $('#cancelBookingModal').modal('show');
}

function confirmCancel() {
  const bookingId = document.getElementById('cancel_booking_id').value;
  const companyId = document.getElementById('cancel_company_id').value;
  const reason = document.getElementById('cancellation_reason').value;
  
  if (!reason) {
    swal("Required", "Please provide a reason for cancellation.", "warning");
    return;
  }

  const isGroup = !!companyId;
  const isReception = {{ $isReception ? 'true' : 'false' }};
  
  let url;
  if (isGroup) {
      url = isReception 
        ? '{{ url("/reception/companies") }}/' + companyId + '/cancel'
        : '{{ url("/manager/companies") }}/' + companyId + '/cancel';
  } else {
      url = isReception
        ? '{{ url("/reception/bookings") }}/' + bookingId + '/status'
        : '{{ url("/manager/bookings") }}/' + bookingId + '/status';
  }

  swal({
    title: "Are you sure?",
    text: isGroup ? "You are about to cancel ALL bookings for this corporate group." : "You are about to cancel this booking.",
    type: "warning",
    showCancelButton: true,
    confirmButtonColor: "#dc3545",
    confirmButtonText: "Yes, Cancel Now!",
    closeOnConfirm: false,
    showLoaderOnConfirm: true
  }, function(isConfirm) {
    if (isConfirm) {
      fetch(url, {
        method: isGroup ? 'POST' : 'PUT',
        headers: {
          'Accept': 'application/json',
          'X-CSRF-TOKEN': '{{ csrf_token() }}',
          'Content-Type': 'application/json'
        },
        body: JSON.stringify({ 
            status: isGroup ? null : 'cancelled', 
            cancellation_reason: reason 
        })
      })
      .then(response => response.json())
      .then(data => {
        if (data.success) {
          swal({
            title: "Cancelled!",
            text: data.message,
            type: "success"
          }, function() {
            location.reload();
          });
        } else {
          swal("Failed", data.message || "Cancellation failed.", "error");
        }
      })
      .catch(error => {
        console.error('Error:', error);
        swal("Error", "A system error occurred while processing the request.", "error");
      });
    }
  });
}

/**
 * Late Check-out Logic
 */
function openLateCheckoutModal(bookingId, guestName, roomNumber) {
  document.getElementById('late_checkout_booking_id').value = bookingId;
  document.getElementById('lateCheckoutGuestInfo').innerHTML = '<i class="fa fa-user"></i> ' + guestName + ' <span class="mx-2">|</span> <i class="fa fa-bed"></i> Room ' + roomNumber;
  document.getElementById('late_checkout_hours').value = '';
  document.getElementById('late_checkout_amount').value = '';
  document.getElementById('late_checkout_notes').value = '';
  $('#lateCheckoutModal').modal('show');
}

function submitLateCheckout() {
  const bookingId = document.getElementById('late_checkout_booking_id').value;
  const hours = document.getElementById('late_checkout_hours').value;
  const amount = document.getElementById('late_checkout_amount').value;
  const notes = document.getElementById('late_checkout_notes').value;
  
  if (!hours || isNaN(hours) || hours <= 0) {
    swal("Required", "Please enter valid number of extra hours.", "warning");
    return;
  }
  
  if (!amount || isNaN(amount) || amount < 0) {
    swal("Required", "Please enter a valid charge amount.", "warning");
    return;
  }

  const isReception = {{ empty($isReception) ? 'false' : 'true' }};
  const url = isReception 
    ? '{{ url("/reception/bookings") }}/' + bookingId + '/late-checkout'
    : '{{ url("/manager/bookings") }}/' + bookingId + '/late-checkout';

  swal({
    title: "Confirm Late Check-out?",
    text: "Add " + hours + " hours extra stay for " + Number(amount).toLocaleString() + " TZS?",
    type: "info",
    showCancelButton: true,
    confirmButtonColor: "#ffc107",
    confirmButtonText: "Yes, Apply Charge",
    closeOnConfirm: false,
    showLoaderOnConfirm: true
  }, function(isConfirm) {
    if (isConfirm) {
      fetch(url, {
        method: 'POST',
        headers: {
          'Accept': 'application/json',
          'X-CSRF-TOKEN': '{{ csrf_token() }}',
          'Content-Type': 'application/json'
        },
        body: JSON.stringify({ 
            hours: hours, 
            amount: amount,
            notes: notes 
        })
      })
      .then(response => response.json())
      .then(data => {
        if (data.success) {
          swal({
            title: "Applied!",
            text: data.message,
            type: "success"
          }, function() {
            location.reload();
          });
        } else {
          swal("Failed", data.message || "Operation failed.", "error");
        }
      })
      .catch(error => {
        console.error('Error:', error);
        swal("Error", "A system error occurred while processing the request.", "error");
      });
    }
  });
}
</script>
@endsection

