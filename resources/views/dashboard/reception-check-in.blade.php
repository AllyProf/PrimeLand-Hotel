@extends('dashboard.layouts.app')

@section('content')
<div class="app-title">
  <div>
    <h1><i class="fa fa-sign-in"></i> Check In</h1>
    <p>Check in guests for their reservations</p>
  </div>
  <ul class="app-breadcrumb breadcrumb">
    <li class="breadcrumb-item"><i class="fa fa-home fa-lg"></i></li>
    <li class="breadcrumb-item"><a href="{{ route('reception.dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item"><a href="#">Check In</a></li>
  </ul>
</div>

<div class="row mb-3">
  <div class="col-md-12">
    <div class="tile">
      <div class="tile-title-w-btn mb-3">
        <h3 class="title">Ready for Check-In</h3>
      </div>
      
      @php
        // Determine which routes to use based on role
        $checkInRoute = ($role === 'manager') 
          ? 'admin.reservations.check-in' 
          : 'reception.reservations.check-in';
        
        // Get default check-in time from settings
        $defaultCheckInTime = \App\Models\HotelSetting::getValue('default_checkin_time', '14:00');
      @endphp
      
      <!-- Booking Type Tabs -->
      <div class="booking-tabs-wrapper mb-4">
        <ul class="nav nav-pills nav-justified" role="tablist" style="background: #f8f9fa; padding: 8px; border-radius: 8px;">
          <li class="nav-item">
            <a class="nav-link {{ ($bookingType ?? 'individual') == 'individual' ? 'active' : '' }}" 
               href="{{ route($checkInRoute, array_merge(request()->except(['type']), ['type' => 'individual'])) }}"
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
               href="{{ route($checkInRoute, array_merge(request()->except(['type']), ['type' => 'corporate'])) }}"
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
      
      <!-- Search Filter -->
      <div class="row mb-3">
        <div class="col-md-6 col-12 mb-2 mb-md-0">
          <input type="text" class="form-control" id="searchInput" placeholder="Search by reference, name, or email..." onkeyup="filterCheckIns()" oninput="filterCheckIns()" style="font-size: 16px;">
        </div>
        <div class="col-md-4 col-12 mb-2 mb-md-0">
          <input type="date" class="form-control" id="checkInDateFilter" onchange="filterCheckIns()" value="{{ request('check_in_date') }}" style="font-size: 16px;">
        </div>
        <div class="col-md-2 col-12">
          <button class="btn btn-secondary btn-block" onclick="resetCheckInFilters()">
            <i class="fa fa-refresh"></i> Reset
          </button>
        </div>
      </div>
      
      <div class="tile-body">
        @if($bookings->count() > 0)
        <!-- Desktop Table View -->
        <div class="table-responsive">
          <table class="table table-hover table-bordered" id="checkInTable">
            <thead>
              <tr>
                <th>Booking Reference</th>
                <th>{{ ($bookingType ?? 'individual') == 'corporate' ? 'Company' : 'Guest' }}</th>
                <th>{{ ($bookingType ?? 'individual') == 'corporate' ? 'Guests' : 'Room' }}</th>
                <th>Check-in Date</th>
                <th>Check-out Date</th>
                <th>Nights</th>
                <th>Submission Status</th>
                <th>Time Remaining</th>
                <th>Total Price</th>
                <th>Actions</th>
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
                    $totalPrice = $companyBookings->sum('total_price');
                    $totalNights = $firstBooking ? $firstBooking->check_in->diffInDays($firstBooking->check_out) : 0;
                  @endphp
                  <tr class="checkin-row corporate-booking-group"
                      data-booking-ref="{{ strtolower($firstBooking->booking_reference ?? '') }}"
                      data-check-in-date="{{ $firstBooking->check_in->format('Y-m-d') ?? '' }}"
                      data-company-name="{{ strtolower($company->name ?? '') }}"
                      data-company-email="{{ strtolower($company->email ?? '') }}">
                    <td>
                      <strong>{{ $firstBooking->booking_reference ?? 'N/A' }}</strong>
                      <br><small class="text-muted">{{ $firstBooking->created_at->format('M d, Y') ?? 'N/A' }}</small>
                    </td>
                    <td>
                      @if($company)
                        <strong><i class="fa fa-building"></i> {{ $company->name }}</strong><br>
                        <small>{{ $company->email }}</small><br>
                        @if($company->phone)
                        <small>{{ $company->phone }}</small>
                        @endif
                      @else
                        <span class="text-muted">N/A</span>
                      @endif
                    </td>
                    <td>
                      <span class="badge badge-info">{{ $totalGuests }} guest{{ $totalGuests > 1 ? 's' : '' }}</span>
                      <br><small class="text-muted">{{ $companyBookings->pluck('room.room_number')->filter()->implode(', ') }}</small>
                    </td>
                    <td>{{ $firstBooking->check_in->format('M d, Y') ?? 'N/A' }}</td>
                    <td>{{ $firstBooking->check_out->format('M d, Y') ?? 'N/A' }}</td>
                    <td>{{ $totalNights }} nights</td>
                    <td>
                      @if($companyBookings->contains(fn($b) => !empty($b->mobile_checkin_submitted_at)))
                        <span class="badge badge-info" title="Some guests have submitted scans">
                          <i class="fa fa-file-text-o"></i> Submitted
                        </span>
                      @else
                        <span class="text-muted small">No Submission</span>
                      @endif
                    </td>
                    <td>
                      @php
                        $now = \Carbon\Carbon::now();
                        $cTime = ($firstBooking->arrival_time ?? $firstBooking->room->checkin_time ?? $defaultCheckInTime);
                        $tParts = explode(':', $cTime);
                        $checkInDate = $firstBooking ? \Carbon\Carbon::parse($firstBooking->check_in)->setTime((int)$tParts[0], (int)($tParts[1] ?? 0), 0) : null;
                        
                        if ($checkInDate) {
                          $diffInDays = (int)$now->diffInDays($checkInDate, false);
                          $diffInHours = (int)$now->diffInHours($checkInDate, false);
                          $diffInMinutes = (int)$now->diffInMinutes($checkInDate, false);
                        }
                      @endphp
                      @if($checkInDate && $checkInDate->isPast())
                        @php
                          $daysOverdue = (int)$checkInDate->diffInDays($now);
                          $hoursOverdue = (int)$checkInDate->diffInHours($now);
                        @endphp
                        @if($daysOverdue >= 1)
                          <span class="badge badge-danger" title="Check-in date was {{ $daysOverdue }} day(s) ago">
                            <i class="fa fa-exclamation-triangle"></i> Overdue ({{ $daysOverdue }} day{{ $daysOverdue > 1 ? 's' : '' }})
                          </span>
                        @else
                          @if($hoursOverdue > 0)
                            <span class="badge badge-danger" title="Check-in date was {{ $hoursOverdue }} hour(s) ago">
                              <i class="fa fa-exclamation-triangle"></i> Overdue ({{ $hoursOverdue }} hour{{ $hoursOverdue > 1 ? 's' : '' }})
                            </span>
                          @else
                            @php $minutesOverdue = (int)$checkInDate->diffInMinutes($now); @endphp
                            <span class="badge badge-danger" title="Check-in date was {{ $minutesOverdue }} minute(s) ago">
                              <i class="fa fa-exclamation-triangle"></i> Overdue ({{ $minutesOverdue }} min{{ $minutesOverdue > 1 ? 's' : '' }})
                            </span>
                          @endif
                        @endif
                      @elseif($checkInDate && $diffInDays == 0)
                        @if($diffInHours > 0)
                          <span class="badge badge-warning">
                            <i class="fa fa-clock-o"></i> 
                            @php $remMin = $diffInMinutes % 60; @endphp
                            {{ $diffInHours }}h {{ $remMin > 0 ? $remMin.'m' : '' }} remaining
                          </span>
                        @elseif($diffInMinutes > 0)
                          <span class="badge badge-warning">
                            <i class="fa fa-clock-o"></i> {{ $diffInMinutes }} minute(s) remaining
                          </span>
                        @else
                          <span class="badge badge-success">
                            <i class="fa fa-check-circle"></i> Check-in Time!
                          </span>
                        @endif
                      @elseif($checkInDate && $diffInDays > 0)
                        <span class="badge badge-info">
                          <i class="fa fa-calendar"></i> {{ $diffInDays }} day(s) remaining
                        </span>
                      @else
                        <span class="text-muted">N/A</span>
                      @endif
                    </td>
                    <td>
                      <strong>${{ number_format($totalPrice, 2) }}</strong>
                      <br><small class="text-muted">{{ number_format($totalPrice * $exchangeRate, 2) }} TZS</small>
                    </td>
                    <td>
                      <button class="btn btn-sm {{ $companyBookings->contains(fn($b) => !empty($b->mobile_checkin_submitted_at)) ? 'btn-info' : 'btn-primary' }}" onclick="checkInCompanyGroup({{ $company->id ?? 0 }})" title="Check In All Guests">
                        <i class="fa {{ $companyBookings->contains(fn($b) => !empty($b->mobile_checkin_submitted_at)) ? 'fa-eye' : 'fa-sign-in' }}"></i> 
                        {{ $companyBookings->contains(fn($b) => !empty($b->mobile_checkin_submitted_at)) ? 'Review & Check In' : 'Check In' }}
                      </button>
                    </td>
                  </tr>
                @endforeach
              @else
                @foreach($bookings as $booking)
                  <tr class="checkin-row" 
                      data-booking-ref="{{ strtolower($booking->booking_reference) }}"
                      data-guest-name="{{ strtolower($booking->guest_name) }}"
                      data-guest-email="{{ strtolower($booking->guest_email) }}"
                      data-check-in-date="{{ $booking->check_in->format('Y-m-d') }}">
                    <td><strong>{{ $booking->booking_reference }}</strong></td>
                    <td>
                      <strong>{{ $booking->guest_name }}</strong><br>
                      <small>{{ $booking->guest_email }}</small><br>
                      @if($booking->guest_phone)
                      <small>{{ $booking->guest_phone }}</small>
                      @endif
                    </td>
                    <td>
                      <span class="badge badge-primary">{{ $booking->room->room_type ?? 'N/A' }}</span><br>
                      <small>{{ $booking->room->room_number ?? 'N/A' }}</small>
                    </td>
                    <td>{{ $booking->check_in->format('M d, Y') }}</td>
                    <td>{{ $booking->check_out->format('M d, Y') }}</td>
                    <td>{{ $booking->check_in->diffInDays($booking->check_out) }} nights</td>
                    <td>
                      @if($booking->mobile_checkin_submitted_at)
                        <span class="badge badge-info" title="Guest submitted records at {{ $booking->mobile_checkin_submitted_at }}">
                          <i class="fa fa-file-text-o"></i> Records Submitted
                        </span>
                      @else
                        <span class="text-muted small">No Submission</span>
                      @endif
                    </td>
                    <td>
                      @php
                        $now = \Carbon\Carbon::now();
                        $cTime = ($booking->arrival_time ?? $booking->room->checkin_time ?? $defaultCheckInTime);
                        $tParts = explode(':', $cTime);
                        $checkInDate = \Carbon\Carbon::parse($booking->check_in)->setTime((int)$tParts[0], (int)($tParts[1] ?? 0), 0);
                        $diffInDays = (int)$now->diffInDays($checkInDate, false);
                        $diffInHours = (int)$now->diffInHours($checkInDate, false);
                        $diffInMinutes = (int)$now->diffInMinutes($checkInDate, false);
                      @endphp
                      @if($checkInDate->isPast())
                        @php
                          $daysOverdue = (int)$checkInDate->diffInDays($now);
                          $hoursOverdue = (int)$checkInDate->diffInHours($now);
                        @endphp
                        @if($daysOverdue >= 1)
                          <span class="badge badge-danger" title="Check-in date was {{ $daysOverdue }} day(s) ago">
                            <i class="fa fa-exclamation-triangle"></i> Overdue ({{ $daysOverdue }} day{{ $daysOverdue > 1 ? 's' : '' }})
                          </span>
                        @else
                          @if($hoursOverdue > 0)
                            <span class="badge badge-danger" title="Check-in date was {{ $hoursOverdue }} hour(s) ago">
                              <i class="fa fa-exclamation-triangle"></i> Overdue ({{ $hoursOverdue }} hour{{ $hoursOverdue > 1 ? 's' : '' }})
                            </span>
                          @else
                            @php $minutesOverdue = (int)$checkInDate->diffInMinutes($now); @endphp
                            <span class="badge badge-danger" title="Check-in date was {{ $minutesOverdue }} minute(s) ago">
                              <i class="fa fa-exclamation-triangle"></i> Overdue ({{ $minutesOverdue }} min{{ $minutesOverdue > 1 ? 's' : '' }})
                            </span>
                          @endif
                        @endif
                      @elseif($diffInDays == 0)
                        @if($diffInHours > 0)
                          <span class="badge badge-warning">
                            <i class="fa fa-clock-o"></i> 
                            @php $remMin = $diffInMinutes % 60; @endphp
                            {{ $diffInHours }}h {{ $remMin > 0 ? $remMin.'m' : '' }} remaining
                          </span>
                        @elseif($diffInMinutes > 0)
                          <span class="badge badge-warning">
                            <i class="fa fa-clock-o"></i> {{ $diffInMinutes }} minute(s) remaining
                          </span>
                        @else
                          <span class="badge badge-success">
                            <i class="fa fa-check-circle"></i> Check-in Time!
                          </span>
                        @endif
                      @elseif($diffInDays == 1)
                        <span class="badge badge-info">
                          <i class="fa fa-calendar"></i> Tomorrow
                        </span>
                      @else
                        <span class="badge badge-primary">
                          <i class="fa fa-calendar"></i> {{ $diffInDays }} day(s) remaining
                        </span>
                      @endif
                    </td>
                    <td>
                      @if($booking->guest_type === 'tanzanian')
                        <strong>{{ number_format($booking->total_price, 0) }} TZS</strong><br>
                        <small class="text-muted">≈ ${{ number_format($booking->total_price / ($booking->locked_exchange_rate ?? $exchangeRate), 2) }}</small>
                      @else
                        <strong>${{ number_format($booking->total_price, 2) }}</strong><br>
                        <small class="text-muted">{{ number_format($booking->total_price * ($booking->locked_exchange_rate ?? $exchangeRate), 2) }} TZS</small>
                      @endif
                    </td>
                    <td>
                      <button class="btn btn-sm btn-success" onclick="checkInGuest({{ $booking->id }}, '{{ $booking->booking_reference }}', {{ $booking->mobile_checkin_submitted_at ? 'true' : 'false' }})">
                        <i class="fa fa-sign-in"></i> Check In
                      </button>
                      <button class="btn btn-sm btn-info" onclick="viewBookingDetails({{ $booking->id }}, '{{ $booking->booking_reference }}')">
                        <i class="fa fa-eye"></i> View
                      </button>
                    </td>
                  </tr>
                @endforeach
              @endif
            </tbody>
          </table>
        </div>
        
        <!-- Mobile Card View -->
        <div class="mobile-checkin-cards">
          @if(($bookingType ?? 'individual') == 'corporate')
            @foreach($bookings as $group)
              @php
                $company = $group['company'] ?? null;
                $companyBookings = $group['bookings'] ?? collect();
                $firstBooking = $group['first_booking'] ?? $companyBookings->first();
                $totalGuests = $companyBookings->count();
                $totalPrice = $companyBookings->sum('total_price');
                $totalNights = $firstBooking ? $firstBooking->check_in->diffInDays($firstBooking->check_out) : 0;
                $now = \Carbon\Carbon::now();
                $cTime = ($firstBooking->arrival_time ?? $firstBooking->room->checkin_time ?? $defaultCheckInTime);
                $tParts = explode(':', $cTime);
                $checkInDate = $firstBooking ? \Carbon\Carbon::parse($firstBooking->check_in)->setTime((int)$tParts[0], (int)($tParts[1] ?? 0), 0) : null;
                
                if ($checkInDate) {
                  $diffInDays = (int)$now->diffInDays($checkInDate, false);
                  $diffInHours = (int)$now->diffInHours($checkInDate, false);
                  $diffInMinutes = (int)$now->diffInMinutes($checkInDate, false);
                }
              @endphp
              <div class="mobile-checkin-card checkin-row corporate-booking-group" 
                   style="background: white; border: 1px solid #dee2e6; border-radius: 8px; padding: 15px; margin-bottom: 15px; box-shadow: 0 2px 4px rgba(0,0,0,0.1);"
                   data-booking-ref="{{ strtolower($firstBooking->booking_reference ?? '') }}"
                   data-check-in-date="{{ $firstBooking->check_in->format('Y-m-d') ?? '' }}"
                   data-company-name="{{ strtolower($company->name ?? '') }}"
                   data-company-email="{{ strtolower($company->email ?? '') }}">
                <div style="border-bottom: 2px solid #e77a3a; padding-bottom: 10px; margin-bottom: 15px;">
                  <h5 style="color: #e77a3a; font-size: 18px; font-weight: 600; margin: 0;"><i class="fa fa-building"></i> {{ $company->name ?? 'N/A' }}</h5>
                  <div style="font-size: 14px; color: #6c757d; margin-top: 5px;">Ref: {{ $firstBooking->booking_reference ?? 'N/A' }}</div>
                  <span class="badge badge-info mt-2">{{ $totalGuests }} guest{{ $totalGuests > 1 ? 's' : '' }}</span>
                </div>
                
                <div style="display: flex; justify-content: space-between; padding: 10px 0; border-bottom: 1px solid #f0f0f0;">
                  <span style="font-weight: 600; color: #495057; font-size: 14px; flex: 0 0 40%;">Guests:</span>
                  <span style="text-align: right; flex: 1;">{{ $totalGuests }} guest{{ $totalGuests > 1 ? 's' : '' }}</span>
                </div>
                
                <div style="display: flex; justify-content: space-between; padding: 10px 0; border-bottom: 1px solid #f0f0f0;">
                  <span style="font-weight: 600; color: #495057; font-size: 14px; flex: 0 0 40%;">Rooms:</span>
                  <span style="text-align: right; flex: 1;">{{ $companyBookings->pluck('room.room_number')->filter()->implode(', ') }}</span>
                </div>
                
                <div style="display: flex; justify-content: space-between; padding: 10px 0; border-bottom: 1px solid #f0f0f0;">
                  <span style="font-weight: 600; color: #495057; font-size: 14px; flex: 0 0 40%;">Check-in Date:</span>
                  <span style="text-align: right; flex: 1;">{{ $firstBooking->check_in->format('M d, Y') ?? 'N/A' }}</span>
                </div>
                
                <div style="display: flex; justify-content: space-between; padding: 10px 0; border-bottom: 1px solid #f0f0f0;">
                  <span style="font-weight: 600; color: #495057; font-size: 14px; flex: 0 0 40%;">Check-out Date:</span>
                  <span style="text-align: right; flex: 1;">{{ $firstBooking->check_out->format('M d, Y') ?? 'N/A' }}</span>
                </div>
                
                <div style="display: flex; justify-content: space-between; padding: 10px 0; border-bottom: 1px solid #f0f0f0;">
                  <span style="font-weight: 600; color: #495057; font-size: 14px; flex: 0 0 40%;">Nights:</span>
                  <span style="text-align: right; flex: 1;">{{ $totalNights }} nights</span>
                </div>

                <div style="display: flex; justify-content: space-between; padding: 10px 0; border-bottom: 1px solid #f0f0f0;">
                  <span style="font-weight: 600; color: #495057; font-size: 14px; flex: 0 0 40%;">Submission:</span>
                  <span style="text-align: right; flex: 1;">
                      @if($companyBookings->contains(fn($b) => !empty($b->mobile_checkin_submitted_at)))
                        <span class="badge badge-info">Submitted</span>
                      @else
                        <span class="text-muted small">No Submission</span>
                      @endif
                  </span>
                </div>
                
                <div style="display: flex; justify-content: space-between; padding: 10px 0; border-bottom: 1px solid #f0f0f0;">
                  <span style="font-weight: 600; color: #495057; font-size: 14px; flex: 0 0 40%;">Total Price:</span>
                  <span style="text-align: right; flex: 1;">
                    <strong>${{ number_format($totalPrice, 2) }}</strong><br>
                    <small>{{ number_format($totalPrice * $exchangeRate, 2) }} TZS</small>
                  </span>
                </div>
                
                <div style="margin-top: 15px; display: flex; gap: 10px;">
                  <button class="btn btn-sm btn-primary btn-block" onclick="checkInCompanyGroup({{ $company->id ?? 0 }})" style="flex: 1;">
                    <i class="fa fa-sign-in"></i> Check In All
                  </button>
                </div>
              </div>
            @endforeach
          @else
            @foreach($bookings as $booking)
              @php
                $now = \Carbon\Carbon::now();
                $cTime = ($booking->arrival_time ?? $booking->room->checkin_time ?? $defaultCheckInTime);
                $tParts = explode(':', $cTime);
                $checkInDate = \Carbon\Carbon::parse($booking->check_in)->setTime((int)$tParts[0], (int)($tParts[1] ?? 0), 0);
                $diffInDays = (int)$now->diffInDays($checkInDate, false);
                $diffInHours = (int)$now->diffInHours($checkInDate, false);
                $diffInMinutes = (int)$now->diffInMinutes($checkInDate, false);
              @endphp
              <div class="mobile-checkin-card checkin-row" 
                   style="background: white; border: 1px solid #dee2e6; border-radius: 8px; padding: 15px; margin-bottom: 15px; box-shadow: 0 2px 4px rgba(0,0,0,0.1);"
                   data-booking-ref="{{ strtolower($booking->booking_reference) }}"
                   data-guest-name="{{ strtolower($booking->guest_name) }}"
                   data-guest-email="{{ strtolower($booking->guest_email) }}"
                   data-check-in-date="{{ $booking->check_in->format('Y-m-d') }}">
                <div style="border-bottom: 2px solid #e77a3a; padding-bottom: 10px; margin-bottom: 15px;">
                  <h5 style="color: #e77a3a; font-size: 18px; font-weight: 600; margin: 0;">{{ $booking->guest_name }}</h5>
                  <div style="font-size: 14px; color: #6c757d; margin-top: 5px;">Ref: {{ $booking->booking_reference }}</div>
                </div>
            
            <div style="display: flex; justify-content: space-between; padding: 10px 0; border-bottom: 1px solid #f0f0f0;">
              <span style="font-weight: 600; color: #495057; font-size: 14px; flex: 0 0 40%;">Room:</span>
              <span style="text-align: right; flex: 1;">
                <span class="badge badge-primary">{{ $booking->room->room_type ?? 'N/A' }}</span>
                <br><small>{{ $booking->room->room_number ?? 'N/A' }}</small>
              </span>
            </div>
            
            <div style="display: flex; justify-content: space-between; padding: 10px 0; border-bottom: 1px solid #f0f0f0;">
              <span style="font-weight: 600; color: #495057; font-size: 14px; flex: 0 0 40%;">Check-in Date:</span>
              <span style="text-align: right; flex: 1;">{{ $booking->check_in->format('M d, Y') }}</span>
            </div>
            
            <div style="display: flex; justify-content: space-between; padding: 10px 0; border-bottom: 1px solid #f0f0f0;">
              <span style="font-weight: 600; color: #495057; font-size: 14px; flex: 0 0 40%;">Check-out Date:</span>
              <span style="text-align: right; flex: 1;">{{ $booking->check_out->format('M d, Y') }}</span>
            </div>
            
            <div style="display: flex; justify-content: space-between; padding: 10px 0; border-bottom: 1px solid #f0f0f0;">
              <span style="font-weight: 600; color: #495057; font-size: 14px; flex: 0 0 40%;">Nights:</span>
              <span style="text-align: right; flex: 1;">{{ $booking->check_in->diffInDays($booking->check_out) }} nights</span>
            </div>

            <div style="display: flex; justify-content: space-between; padding: 10px 0; border-bottom: 1px solid #f0f0f0;">
              <span style="font-weight: 600; color: #495057; font-size: 14px; flex: 0 0 40%;">Submission:</span>
              <span style="text-align: right; flex: 1;">
                @if($booking->mobile_checkin_submitted_at)
                  <span class="badge badge-info">Submitted</span>
                @else
                  <span class="text-muted small">No Submission</span>
                @endif
              </span>
            </div>
            
            <div style="display: flex; justify-content: space-between; padding: 10px 0; border-bottom: 1px solid #f0f0f0;">
              <span style="font-weight: 600; color: #495057; font-size: 14px; flex: 0 0 40%;">Time Remaining:</span>
              <span style="text-align: right; flex: 1;">
                @if($checkInDate->isPast())
                  @php
                    $daysOverdue = (int)$checkInDate->diffInDays($now);
                    $hoursOverdue = (int)$checkInDate->diffInHours($now);
                  @endphp
                  @if($daysOverdue >= 1)
                    <span class="badge badge-danger" title="Check-in date was {{ $daysOverdue }} day(s) ago">
                      <i class="fa fa-exclamation-triangle"></i> Overdue ({{ $daysOverdue }} day{{ $daysOverdue > 1 ? 's' : '' }})
                    </span>
                  @else
                    @if($hoursOverdue > 0)
                      <span class="badge badge-danger" title="Check-in date was {{ $hoursOverdue }} hour(s) ago">
                        <i class="fa fa-exclamation-triangle"></i> Overdue ({{ $hoursOverdue }} hour{{ $hoursOverdue > 1 ? 's' : '' }})
                      </span>
                    @else
                      @php $minutesOverdue = (int)$checkInDate->diffInMinutes($now); @endphp
                      <span class="badge badge-danger" title="Check-in date was {{ $minutesOverdue }} minute(s) ago">
                        <i class="fa fa-exclamation-triangle"></i> Overdue ({{ $minutesOverdue }} min{{ $minutesOverdue > 1 ? 's' : '' }})
                      </span>
                    @endif
                  @endif
                @elseif($diffInDays == 0)
                  @if($diffInHours > 0)
                    <span class="badge badge-warning">
                      <i class="fa fa-clock-o"></i> 
                      @php $remMin = $diffInMinutes % 60; @endphp
                      {{ $diffInHours }}h {{ $remMin > 0 ? $remMin.'m' : '' }} remaining
                    </span>
                  @elseif($diffInMinutes > 0)
                    <span class="badge badge-warning">
                      <i class="fa fa-clock-o"></i> {{ $diffInMinutes }} minute(s) remaining
                    </span>
                  @else
                    <span class="badge badge-success">
                      <i class="fa fa-check-circle"></i> Check-in Time!
                    </span>
                  @endif
                @elseif($diffInDays == 1)
                  <span class="badge badge-info">
                    <i class="fa fa-calendar"></i> Tomorrow
                  </span>
                @else
                  <span class="badge badge-primary">
                    <i class="fa fa-calendar"></i> {{ $diffInDays }} day(s) remaining
                  </span>
                @endif
              </span>
            </div>
            
            <div style="display: flex; justify-content: space-between; padding: 10px 0; border-bottom: 1px solid #f0f0f0;">
              <span style="font-weight: 600; color: #495057; font-size: 14px; flex: 0 0 40%;">Total Price:</span>
              <span style="text-align: right; flex: 1;">
                @if($booking->guest_type === 'tanzanian')
                  <strong>{{ number_format($booking->total_price, 0) }} TZS</strong><br>
                  <small class="text-muted">≈ ${{ number_format($booking->total_price / ($booking->locked_exchange_rate ?? $exchangeRate), 2) }}</small>
                @else
                  <strong>${{ number_format($booking->total_price, 2) }}</strong><br>
                  <small class="text-muted">{{ number_format($booking->total_price * ($booking->locked_exchange_rate ?? $exchangeRate), 2) }} TZS</small>
                @endif
              </span>
            </div>
            
            <div style="padding: 10px 0; border-bottom: 1px solid #f0f0f0;">
              <span style="font-weight: 600; color: #495057; font-size: 14px; display: block; margin-bottom: 5px;">Email:</span>
              <span style="font-size: 13px; color: #666;">{{ $booking->guest_email }}</span>
            </div>
            
            @if($booking->guest_phone)
            <div style="padding: 10px 0;">
              <span style="font-weight: 600; color: #495057; font-size: 14px; display: block; margin-bottom: 5px;">Phone:</span>
              <span style="font-size: 13px; color: #666;">{{ $booking->guest_phone }}</span>
            </div>
            @endif
            
            <div style="margin-top: 15px; padding-top: 15px; border-top: 1px solid #dee2e6; display: flex; gap: 8px; flex-wrap: wrap;">
              <button class="btn btn-sm btn-success" onclick="checkInGuest({{ $booking->id }}, '{{ $booking->booking_reference }}', {{ $booking->mobile_checkin_submitted_at ? 'true' : 'false' }})" style="flex: 1; min-width: calc(50% - 4px);">
                <i class="fa fa-sign-in"></i> Check In
              </button>
              <button class="btn btn-sm btn-info" onclick="viewBookingDetails({{ $booking->id }}, '{{ $booking->booking_reference }}')" style="flex: 1; min-width: calc(50% - 4px);">
                <i class="fa fa-eye"></i> View
              </button>
            </div>
          </div>
          @endforeach
          @endif
        </div>
        
        <div class="d-flex justify-content-center mt-3" id="paginationContainer">
          {{ $bookings->links() }}
        </div>
        @else
        <div class="text-center" style="padding: 50px;">
          <i class="fa fa-sign-in fa-5x text-muted mb-3"></i>
          <h3>No Check-Ins Available</h3>
          <p class="text-muted">No bookings ready for check-in at this time.</p>
        </div>
        @endif
      </div>
    </div>
  </div>
</div>

<!-- Booking Details Modal -->
<div class="modal fade" id="bookingDetailsModal" tabindex="-1" role="dialog" style="z-index: 1080;">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header" style="background: #e77a3a; color: white;">
        <h5 class="modal-title"><i class="fa fa-calendar-check-o"></i> Booking Details</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close" style="color: white;">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body" id="bookingDetailsContent">
        <div class="text-center">
          <i class="fa fa-spinner fa-spin fa-2x"></i>
          <p>Loading booking details...</p>
        </div>
      </div>
      <div class="modal-footer">
        <div id="modalFooterActions" style="display: inline-block;"></div>
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>

@endsection

@section('scripts')
<script src="{{ asset('dashboard_assets/js/plugins/sweetalert.min.js') }}"></script>
<script src="https://cdn.jsdelivr.net/npm/signature_pad@4.1.7/dist/signature_pad.umd.min.js"></script>
<script src="https://unpkg.com/tesseract.js@v5.0.0/dist/tesseract.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>
<style>
/* New Stepper Styles */
.checkin-stepper .step {
    font-size: 14px;
    font-weight: 600;
}
.checkin-stepper .step.active {
    color: #e77a3a;
}
.checkin-stepper .badge {
    padding: 6px 10px;
    margin-right: 5px;
}
.checkin-step {
    transition: all 0.3s ease;
}

/* Mobile Responsive Styles */
@media (max-width: 767px) {
  /* Search Filter */
  #searchInput,
  #checkInDateFilter {
    margin-bottom: 10px;
  }
  
  /* Table - Hide on Mobile */
  #checkInTable {
    display: none;
  }
  
  /* Mobile Cards - Show on Mobile */
  .mobile-checkin-cards {
    display: block;
  }
  
  /* Pagination */
  .pagination {
    justify-content: center;
    flex-wrap: wrap;
  }
  
  .pagination .page-link {
    padding: 8px 12px;
    font-size: 14px;
  }
  
  /* Modal - Mobile */
  .modal-dialog.modal-lg {
    max-width: calc(100% - 20px);
    margin: 10px;
  }
  
  .modal-body .row .col-md-6 {
    flex: 0 0 100%;
    max-width: 100%;
    margin-bottom: 20px;
  }
  
  .modal-footer {
    flex-direction: column;
  }
  
  .modal-footer .btn {
    width: 100%;
    margin-bottom: 10px;
  }
  
  .modal-footer .btn:last-child {
    margin-bottom: 0;
  }
}

/* Desktop - Hide mobile cards */
@media (min-width: 768px) {
  .mobile-checkin-cards {
    display: none;
  }
  
  #checkInTable {
    display: table;
  }
}

/* Very Small Screens */
@media (max-width: 480px) {
  .mobile-checkin-card {
    padding: 12px !important;
  }
  
  .mobile-checkin-card h5 {
    font-size: 16px !important;
  }
  
  .mobile-checkin-card .btn {
    flex: 0 0 100% !important;
    min-width: 100% !important;
    margin-bottom: 8px;
  }
  
  .mobile-checkin-card .btn:last-child {
    margin-bottom: 0;
  }
}
</style>
<script>
let currentBookingId = null;
let currentBookingRef = null;
let signaturePad = null;
let videoStream = null;
let pollingInterval = null;

function startPollingStatus(bookingId) {
    if (pollingInterval) clearInterval(pollingInterval);
    
    pollingInterval = setInterval(async () => {
        try {
            const response = await fetch(`/check-in/status/${bookingId}`);
            const data = await response.json();
            
            if (data.is_submitted && !data.is_checked_in) {
                // Signature submitted but not yet finalized by reception
                // STOP polling - we found what we want
                clearInterval(pollingInterval);
                
                // Add to processed set so background monitor doesn't double-alert
                if (typeof processedSubmissionIds !== 'undefined') {
                    processedSubmissionIds.add(parseInt(bookingId));
                }

                // Automatically transition to details view for review
                $('#enhancedCheckInModal').modal('hide');
                
                // Delay opening the next modal to ensure Bootstrap body classes (modal-open) are handled correctly
                setTimeout(() => {
                    viewBookingDetails(bookingId, currentBookingRef);
                }, 500); 

            } else if (data.is_checked_in) {
                // Already checked in
                clearInterval(pollingInterval);
                $('#enhancedCheckInModal').modal('hide');
                location.reload();
            }
        } catch (err) {
            console.error("Polling error:", err);
        }
    }, 3000);
}


function checkInGuest(bookingId, bookingReference, isSubmitted = false) {
    currentBookingId = bookingId;
    currentBookingRef = bookingReference;
    
    // Show QR Modal for scanning
    $('#enhancedCheckInModal').modal('show');

    // Automatically trigger Mobile Bridge (QR Generation)
    // Small timeout to ensure modal is ready
    setTimeout(() => {
        triggerMobileBridgeAuto();
    }, 300);
}

async function triggerMobileBridgeAuto() {
    if (!currentBookingId) return;
    
    const qrContainer = document.getElementById('checkin-qrcode');
    qrContainer.innerHTML = '<div class="p-5 text-center"><i class="fa fa-spinner fa-spin fa-2x text-primary"></i><br>Generating QR Code...</div>';
    
    @php
        $tokenRoute = ($role === 'manager') ? 'admin.bookings.generate-token' : 'reception.bookings.generate-token';
    @endphp

    try {
        const response = await fetch('{{ route($tokenRoute, "99999", false) }}'.replace('99999', currentBookingId), {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            }
        });
        
        const data = await response.json();
        
        if (data.success) {
            // Clear container
            qrContainer.innerHTML = '';
            
            // Generate New QR
            new QRCode(qrContainer, {
                text: data.url,
                width: 260,
                height: 260,
                colorDark : "#000000",
                colorLight : "#ffffff",
                correctLevel : QRCode.CorrectLevel.H
            });
            
            // Start Polling for status
            startPollingStatus(currentBookingId);
        } else {
            qrContainer.innerHTML = `<div class="alert alert-danger">${data.message || "Failed to generate token."}</div>`;
        }
    } catch (err) {
        console.error("Token error:", err);
        qrContainer.innerHTML = `<div class="alert alert-danger">Could not connect to the server.</div>`;
    }
}

function initSignaturePad() {
    const canvas = document.getElementById('signature-pad');
    signaturePad = new SignaturePad(canvas, {
        backgroundColor: 'rgb(255, 255, 255)'
    });
    
    // Adjust canvas for display size
    const ratio = Math.max(window.devicePixelRatio || 1, 1);
    canvas.width = canvas.offsetWidth * ratio;
    canvas.height = canvas.offsetHeight * ratio;
    canvas.getContext("2d").scale(ratio, ratio);
    signaturePad.clear();
}

// Global variable for OCR worker to avoid re-initializing
let tesseractWorker = null;

document.addEventListener('DOMContentLoaded', function() {
    // Signature Clear
    document.getElementById('clear-signature')?.addEventListener('click', () => signaturePad?.clear());
    
    // Navigation
    document.getElementById('next-to-step2')?.addEventListener('click', () => goToStep(2));
    document.getElementById('back-to-step1')?.addEventListener('click', () => goToStep(1));
    
    // Camera Logic
    document.getElementById('start-camera-btn')?.addEventListener('click', async function() {
        try {
            // Try with environment/back camera first (for tablets/laptops with dual cams)
            const constraints = { 
                video: { 
                    facingMode: "environment", 
                    width: { ideal: 1280 }, 
                    height: { ideal: 720 } 
                } 
            };
            
            try {
                videoStream = await navigator.mediaDevices.getUserMedia(constraints);
            } catch (e) {
                console.warn("Back camera failed, trying any available camera:", e);
                // Fallback to any available camera
                videoStream = await navigator.mediaDevices.getUserMedia({ video: true });
            }

            const video = document.getElementById('checkin-video');
            video.srcObject = videoStream;
            document.getElementById('camera-overlay').style.display = 'none';
            this.style.display = 'none';
            document.getElementById('capture-scan-btn').style.display = 'block';
        } catch (err) {
            console.error("Camera access failed:", err);
            let errorMsg = "Could not access the camera.";
            
            if (window.location.protocol !== 'https:' && window.location.hostname !== 'localhost') {
                errorMsg = "Camera access requires a secure connection (HTTPS). Please ensure your site has an SSL certificate.";
            } else if (err.name === 'NotAllowedError' || err.name === 'PermissionDeniedError') {
                errorMsg = "Permission denied. Please click the camera icon in your browser's address bar and allow access.";
            } else if (err.name === 'NotFoundError') {
                errorMsg = "No camera found on this device.";
            }
            
            swal("Camera Error", errorMsg, "error");
        }
    });

    document.getElementById('capture-scan-btn')?.addEventListener('click', function() {
        const video = document.getElementById('checkin-video');
        const canvas = document.getElementById('checkin-canvas');
        const context = canvas.getContext('2d');
        
        // Draw the current video frame to canvas
        context.drawImage(video, 0, 0, canvas.width, canvas.height);
        
        const imageData = canvas.toDataURL('image/jpeg');
        document.getElementById('id-preview-img').src = imageData;
        document.getElementById('id-preview-container').style.display = 'block';
        
        // Intelligence: Hide camera once scanned successfully to show result
        document.getElementById('camera-container').style.display = 'none';
        this.style.display = 'none';
        
        // Show Retake Button
        if (!document.getElementById('retake-scan-btn')) {
            const retakeBtn = document.createElement('button');
            retakeBtn.id = 'retake-scan-btn';
            retakeBtn.className = 'btn btn-outline-secondary btn-block mt-2';
            retakeBtn.innerHTML = '<i class="fa fa-refresh"></i> Retake Scan';
            retakeBtn.onclick = function() {
                document.getElementById('camera-container').style.display = 'block';
                document.getElementById('id-preview-container').style.display = 'none';
                document.getElementById('capture-scan-btn').style.display = 'block';
                this.remove();
            };
            this.parentNode.appendChild(retakeBtn);
        }

        // OCR Processing
        processOCR(imageData);
    });

    async function processOCR(base64Image) {
        document.getElementById('ocr-status').style.display = 'block';
        
        try {
            if (!tesseractWorker) {
                tesseractWorker = await Tesseract.createWorker('eng');
            }
            
            const { data: { text } } = await tesseractWorker.recognize(base64Image);
            console.log("OCR Result:", text);
            
            // Basic regex to find ID-like numbers (mix of letters and numbers)
            // Looking for patterns like PN123456, 12345-6789, etc.
            const patterns = [
                /[A-Z]{1,2}[0-9]{6,}/g, // Passports like AB123456
                /[0-9]{5,}-[0-9]{3,}-[0-9]{1}/g, // Some national IDs
                /[0-9]{8,}/g // Long digit strings
            ];
            
            let foundNumber = null;
            for (let pattern of patterns) {
                const matches = text.match(pattern);
                if (matches && matches.length > 0) {
                    foundNumber = matches[0];
                    break;
                }
            }

            if (foundNumber) {
                document.getElementById('id_document_number').value = foundNumber;
                document.getElementById('ocr-status').innerHTML = '<i class="fa fa-check text-success"></i> ID Number Extracted';
            } else {
                document.getElementById('ocr-status').innerHTML = '<i class="fa fa-info-circle text-info"></i> Could not find clear ID number. Please enter manually.';
            }
        } catch (err) {
            console.error("OCR error:", err);
            document.getElementById('ocr-status').style.display = 'none';
        }
    }

    // Modal close cleanup
    $('#enhancedCheckInModal').on('hidden.bs.modal', function () {
        if (videoStream) {
            videoStream.getTracks().forEach(track => track.stop());
            videoStream = null;
        }
    });

    // Final Completion
    document.getElementById('complete-checkin-btn')?.addEventListener('click', function() {
        if (signaturePad.isEmpty()) {
            swal("Required", "Please ask the guest to provide their signature.", "warning");
            return;
        }

        const idScan = document.getElementById('id-preview-img').src;
        const signature = signaturePad.toDataURL();
        const idType = document.getElementById('id_document_type').value;
        const idNumber = document.getElementById('id_document_number').value;

        swal({
            title: "Finalizing Check-In",
            text: "Are you sure you want to complete this check-in?",
            type: "info",
            showCancelButton: true,
            closeOnConfirm: false,
            showLoaderOnConfirm: true
        }, function(isConfirm) {
            if (isConfirm) {
                @php
                    $checkInRoute = ($role === 'manager') ? 'admin.bookings.update-checkin' : 'reception.bookings.update-checkin';
                @endphp
                
                fetch('{{ route($checkInRoute, ":id") }}'.replace(':id', currentBookingId), {
                    method: 'PUT',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({
                        check_in_status: 'checked_in',
                        id_document_type: idType,
                        id_document_number: idNumber,
                        id_scan: idScan.startsWith('data:image') ? idScan : null,
                        guest_signature: signature
                    })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        // Smart Success UI in Modal
                        const modalBody = document.querySelector('#enhancedCheckInModal .modal-body');
                        modalBody.innerHTML = `
                            <div class="text-center p-5 animate__animated animate__zoomIn">
                                <div class="mb-4">
                                    <i class="fa fa-check-circle fa-5x text-success"></i>
                                </div>
                                <h3 class="font-weight-bold">Check-In Successful!</h3>
                                <p class="text-muted">Digital records have been stored. The guest is officially registered.</p>
                                <div class="mt-4 p-3 bg-light rounded border">
                                    <h5 class="mb-1 text-primary">Room ${data.booking?.room?.room_number || 'Confirmed'}</h5>
                                    <p class="small mb-0 text-muted">${data.booking?.guest_name || 'Guest'} is now active.</p>
                                </div>
                                <p class="mt-4 small text-info"><i class="fa fa-info-circle"></i> Page will refresh in 2 seconds...</p>
                            </div>
                        `;
                        
                        setTimeout(() => {
                            location.reload();
                        }, 2500);
                    } else {
                        swal("Error", data.message || "Something went wrong.", "error");
                    }
                })
                .catch(err => {
                    console.error("Fetch error:", err);
                    swal("Connection Error", "Could not reach the server.", "error");
                });
            }
        });
    });

    // Modal close cleanup
    $('#enhancedCheckInModal').on('hidden.bs.modal', function () {
        if (pollingInterval) clearInterval(pollingInterval);
    });
});

function checkInGuest_old(bookingId, bookingReference) {
    swal({
        title: "Check In Guest?",
        text: "Are you sure you want to check in booking " + bookingReference + "?",
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
                $checkInRoute = ($role === 'manager') ? 'admin.bookings.update-checkin' : 'reception.bookings.update-checkin';
            @endphp
            fetch('{{ route($checkInRoute, ":id") }}'.replace(':id', bookingId), {
                method: 'PUT',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json'
                },
                body: JSON.stringify({
                    check_in_status: 'checked_in'
                })
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
                        text: data.message || "Failed to check in. Please try again.",
                        type: "error",
                        confirmButtonColor: "#d33"
                    });
                }
            })
            .catch(error => {
                console.error('Error:', error);
                swal({
                    title: "Error!",
                    text: "An error occurred. Please try again.",
                    type: "error",
                    confirmButtonColor: "#d33"
                });
            });
        }
    });
}

function checkInCompanyGroup(companyId, isRefresh = false) {
    console.log('checkInCompanyGroup called with companyId:', companyId);
    
    if (!companyId || companyId === 0) {
        swal({ title: "Error!", text: "Invalid company ID", type: "error" });
        return;
    }

    // Prepare Modal
    const modalElement = document.getElementById('companyGroupCheckInModal');
    modalElement.setAttribute('data-company-id', companyId);
    
    if (!isRefresh) {
        $('#companyGroupCheckInModal').modal('show');
    }
    
    const listBody = document.getElementById('companyGuestListBody');
    if (!isRefresh) {
        listBody.innerHTML = '<tr><td colspan="4" class="text-center p-4"><i class="fa fa-spinner fa-spin fa-2x"></i><br>Fetching guest list...</td></tr>';
        document.getElementById('modalCompanyName').textContent = 'Loading...';
        document.getElementById('modalCompanyInfo').textContent = '';
    }

    // Fetch Bookings
    const companyBookingsUrl = '{{ url("/manager/bookings/company") }}/' + companyId;
    fetch(companyBookingsUrl, {
        method: 'GET',
        headers: {
            'Accept': 'application/json',
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (!data.success) throw new Error(data.message || 'Failed to fetch');
        
        const company = data.company;
        const bookings = data.bookings;
        
        document.getElementById('modalCompanyName').textContent = company.name;
        document.getElementById('modalCompanyInfo').textContent = `${company.email} | ${company.phone || 'No phone'}`;
        
        // Setup Bulk Button
        const pendingBookings = bookings.filter(b => b.check_in_status === 'pending' || !b.check_in_status);
        const bulkBtn = document.getElementById('bulkCheckInBtn');
        
        if (pendingBookings.length > 0) {
            bulkBtn.style.display = 'block';
            bulkBtn.onclick = () => performBulkCheckIn(companyId, pendingBookings);
        } else {
            bulkBtn.style.display = 'none';
        }

        // Update processed set with currently submitted records to avoid background alerts
        bookings.forEach(b => {
            if (b.mobile_checkin_submitted_at && typeof processedSubmissionIds !== 'undefined') {
                processedSubmissionIds.add(parseInt(b.id));
            }
        });

        // Render Guest Rows
        listBody.innerHTML = '';
        bookings.forEach(booking => {
            const isCheckedIn = booking.check_in_status === 'checked_in';
            const isSubmitted = !!booking.mobile_checkin_submitted_at || !!booking.id_scan_path;
            
            let statusBadge = '';
            if (isCheckedIn) {
                statusBadge = '<span class="badge badge-success"><i class="fa fa-check"></i> Checked In</span>';
            } else if (isSubmitted) {
                statusBadge = '<span class="badge badge-info"><i class="fa fa-file-text"></i> Records Submitted</span>';
            } else {
                statusBadge = '<span class="badge badge-warning">Pending</span>';
            }

            let actionBtn = '';
            // Always have a View button
            const viewBtn = `<button class="btn btn-xs btn-info mr-1" onclick="viewBookingDetails(${booking.id}, '${booking.booking_reference}')" title="View Details"><i class="fa fa-eye"></i> View</button>`;
            
            if (isCheckedIn) {
                actionBtn = viewBtn;
            } else {
                actionBtn = viewBtn + `<button class="btn btn-xs btn-primary" onclick="checkInGuest(${booking.id}, '${booking.booking_reference}', ${isSubmitted})">
                                <i class="fa ${isSubmitted ? 'fa-sign-in' : 'fa-qrcode'}"></i> ${isSubmitted ? 'Check In' : 'Registration (QR)'}
                             </button>`;
            }

            const row = `
                <tr>
                    <td>
                        <strong>${booking.guest_name}</strong><br>
                        <small class="text-muted">${booking.guest_email || 'No email'}</small>
                    </td>
                    <td>
                        <span class="badge badge-primary">${booking.room.room_number || 'N/A'}</span><br>
                        <small>${booking.room.room_type || ''}</small>
                    </td>
                    <td class="align-middle">${statusBadge}</td>
                    <td class="text-right align-middle">${actionBtn}</td>
                </tr>
            `;
            listBody.insertAdjacentHTML('beforeend', row);
        });
    })
    .catch(error => {
        console.error('Error:', error);
        listBody.innerHTML = `<tr><td colspan="4" class="text-center text-danger p-4"><i class="fa fa-exclamation-triangle"></i> Error: ${error.message}</td></tr>`;
    });
}

function performBulkCheckIn(companyId, pendingBookings) {
    swal({
        title: "Bulk Check In?",
        text: `Check in all ${pendingBookings.length} pending guest(s) for this company?`,
        type: "warning",
        showCancelButton: true,
        confirmButtonColor: "#28a745",
        confirmButtonText: "Yes, Check In All!",
        closeOnConfirm: false,
        showLoaderOnConfirm: true
    }, function(isConfirm) {
        if (isConfirm) {
            @php
                $checkInRoute = ($role === 'manager') ? 'admin.bookings.update-checkin' : 'reception.bookings.update-checkin';
            @endphp
            
            let completed = 0;
            let failed = 0;
            const total = pendingBookings.length;
            
            pendingBookings.forEach(booking => {
                fetch('{{ route($checkInRoute, ":id") }}'.replace(':id', booking.id), {
                    method: 'PUT',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({ check_in_status: 'checked_in' })
                })
                .then(r => r.json())
                .then(result => {
                    if (result.success) completed++; else failed++;
                    if (completed + failed === total) {
                        swal({
                            title: failed === 0 ? "Success!" : "Completed with errors",
                            text: `${completed} guest(s) checked in successfully.`,
                            type: failed === 0 ? "success" : "warning"
                        }, () => location.reload());
                    }
                })
                .catch(() => {
                    failed++;
                    if (completed + failed === total) location.reload();
                });
            });
        }
    });
}

function viewBookingDetails(bookingId, bookingRef) {
  $('#bookingDetailsModal').modal('show');
  // Extra safety for standard Bootstrap transition bugs
  document.body.classList.add('modal-open');
  document.getElementById('modalFooterActions').innerHTML = '';
  document.getElementById('bookingDetailsContent').innerHTML = `
    <div class="text-center">
      <i class="fa fa-spinner fa-spin fa-2x"></i>
      <p>Loading booking details...</p>
    </div>
  `;
  
  @php
    // Determine which route to use based on role
    $showBookingRoute = ($role === 'manager') 
      ? 'admin.bookings.show' 
      : 'reception.bookings.show';
    $resubmissionRoute = ($role === 'manager') 
      ? 'admin.bookings.request-resubmission' 
      : 'reception.bookings.request-resubmission';
  @endphp
  fetch('{{ route($showBookingRoute, ":id") }}'.replace(':id', bookingId), {
    method: 'GET',
    headers: {
      'Accept': 'application/json',
      'X-CSRF-TOKEN': '{{ csrf_token() }}',
      'X-Requested-With': 'XMLHttpRequest'
    }
  })
  .then(response => {
    if (!response.ok) {
      throw new Error('Network response was not ok: ' + response.status);
    }
    return response.json();
  })
  .then(data => {
    if (data.success) {
      const booking = data.booking;
      
      // Mark as processed so background monitor doesn't popup
      if (typeof processedSubmissionIds !== 'undefined') {
          processedSubmissionIds.add(parseInt(bookingId));
      }

      const room = booking.room || {};
      const globalExchangeRate = {{ $exchangeRate ?? 2500 }};
      const bookingExchangeRate = booking.locked_exchange_rate ? parseFloat(booking.locked_exchange_rate) : globalExchangeRate;
      const fallbackImage = '{{ asset("landing_page_assets/img/bg-img/1.jpg") }}';
      // Base URL with guaranteed trailing slash so 'storage/...' paths work
      const baseUrl = '{{ rtrim(asset(""), "/") }}/';
      
      let detailsHtml = `
        <div class="booking-details-view">
          <div class="row">
            <div class="col-md-6">
              <h5 style="color: #e77a3a; border-bottom: 2px solid #e77a3a; padding-bottom: 5px; margin-bottom: 15px;"><i class="fa fa-user"></i> Guest Information</h5>
              <table class="table table-sm table-bordered">
                <tr><td width="40%"><strong>Name:</strong></td><td>${booking.guest_name || 'N/A'}</td></tr>
                <tr><td><strong>Guest ID:</strong></td><td>${booking.guest_id || 'N/A'}</td></tr>
                <tr><td><strong>Email:</strong></td><td>${booking.guest_email || 'N/A'}</td></tr>
                <tr><td><strong>Phone:</strong></td><td>${booking.guest_phone || 'N/A'}</td></tr>
                <tr><td><strong>Country:</strong></td><td>${booking.country || 'N/A'}</td></tr>
                <tr><td><strong>Number of Guests:</strong></td><td>${booking.number_of_guests || 1}</td></tr>
                ${booking.mobile_checkin_submitted_at ? `
                <tr><td><strong>Mobile Check-in Submitted:</strong></td><td><span class="text-success"><i class="fa fa-mobile"></i> ${new Date(booking.mobile_checkin_submitted_at).toLocaleString('en-US', {month: 'short', day: 'numeric', year: 'numeric', hour: '2-digit', minute: '2-digit'})}</span></td></tr>
                ` : ''}
              </table>
            </div>
            <div class="col-md-6">
              <h5 style="color: #e77a3a; border-bottom: 2px solid #e77a3a; padding-bottom: 5px; margin-bottom: 15px;"><i class="fa fa-calendar"></i> Booking Information</h5>
              <table class="table table-sm table-bordered">
                <tr><td width="40%"><strong>Booking Reference:</strong></td><td><strong>${booking.booking_reference || 'N/A'}</strong></td></tr>
                <tr><td><strong>Check-in:</strong></td><td>${booking.check_in ? new Date(booking.check_in).toLocaleDateString('en-US', {year: 'numeric', month: 'long', day: 'numeric'}) : 'N/A'}</td></tr>
                <tr><td><strong>Check-out:</strong></td><td>${booking.check_out ? new Date(booking.check_out).toLocaleDateString('en-US', {year: 'numeric', month: 'long', day: 'numeric'}) : 'N/A'}</td></tr>
                <tr><td><strong>Status:</strong></td><td>
                  ${booking.status === 'pending' ? '<span class="badge badge-warning">Pending</span>' : ''}
                  ${booking.status === 'confirmed' ? '<span class="badge badge-success">Confirmed</span>' : ''}
                  ${booking.status === 'cancelled' ? '<span class="badge badge-danger">Cancelled</span>' : ''}
                </td></tr>
                <tr><td><strong>Payment Status:</strong></td><td>
                  ${booking.payment_status === 'paid' ? '<span class="badge badge-success">Paid</span>' : ''}
                  ${booking.payment_status === 'partial' ? '<span class="badge badge-info">Partial</span>' : ''}
                  ${booking.payment_status === 'pending' ? '<span class="badge badge-warning">Pending</span>' : ''}
                </td></tr>
                <tr><td><strong>Check-in Status:</strong></td><td>
                  ${booking.check_in_status === 'checked_in' ? '<span class="badge badge-success">Checked In</span>' : ''}
                  ${booking.check_in_status === 'checked_out' ? '<span class="badge badge-info">Checked Out</span>' : ''}
                  ${booking.check_in_status === 'pending' ? '<span class="badge badge-warning">Not Checked In</span>' : ''}
                </td></tr>
              </table>
            </div>
          </div>

          <!-- Room Information Row -->
          <div class="row mt-3">
            <div class="col-md-6">
              <h5 style="color: #e77a3a; border-bottom: 2px solid #e77a3a; padding-bottom: 5px; margin-bottom: 15px;"><i class="fa fa-bed"></i> Room Information</h5>
              <table class="table table-sm table-bordered">
                <tr><td width="40%"><strong>Room Number:</strong></td><td><strong>${room.room_number || 'N/A'}</strong></td></tr>
                <tr><td><strong>Room Type:</strong></td><td>${room.room_type || 'N/A'}</td></tr>
                <tr><td><strong>Capacity:</strong></td><td>${room.capacity || 'N/A'} guests</td></tr>
                <tr><td><strong>Price per Night:</strong></td><td>$${parseFloat(room.price_per_night || 0).toFixed(2)} USD</td></tr>
              </table>
            </div>
            <div class="col-md-6">
              <h5 style="color: #e77a3a; border-bottom: 2px solid #e77a3a; padding-bottom: 5px; margin-bottom: 15px;"><i class="fa fa-dollar"></i> Payment Information</h5>
              <table class="table table-sm table-bordered">
                <tr><td width="40%"><strong>Total Price:</strong></td><td><strong>$${parseFloat(booking.total_price || 0).toFixed(2)} USD</strong></td></tr>
                <tr><td><strong>Exchange Rate:</strong></td><td>1 USD = ${bookingExchangeRate.toLocaleString()} TZS ${booking.original_exchange_rate ? '<span class="badge badge-info ml-1">Overridden</span>' : ''}</td></tr>
                <tr><td><strong>Total Price (TZS):</strong></td><td><strong>${(parseFloat(booking.total_price || 0) * bookingExchangeRate).toLocaleString()} TZS</strong></td></tr>
                <tr><td><strong>Amount Paid:</strong></td><td>${booking.amount_paid ? '$' + parseFloat(booking.amount_paid).toFixed(2) + ' USD' : 'N/A'}</td></tr>
                ${booking.payment_status === 'partial' && booking.amount_paid ? `
                <tr><td><strong>Remaining Amount:</strong></td><td><strong style="color: #dc3545;">$${parseFloat((booking.total_price || 0) - (booking.amount_paid || 0)).toFixed(2)} USD</strong></td></tr>
                <tr><td><strong>Payment Percentage:</strong></td><td><span class="badge badge-info">${parseFloat(((booking.amount_paid || 0) / (booking.total_price || 1)) * 100).toFixed(0)}%</span></td></tr>
                ` : ''}
                <tr><td><strong>Payment Method:</strong></td><td>${booking.payment_method ? booking.payment_method.charAt(0).toUpperCase() + booking.payment_method.slice(1) : 'N/A'}</td></tr>
                ${booking.original_exchange_rate ? `
                <tr><td colspan="2" class="bg-light">
                    <div style="font-size: 0.8rem; color: #666;">
                        <strong><i class="fa fa-info-circle text-info"></i> Exchange Rate Override Details:</strong><br>
                        Original Rate: 1 USD = ${parseFloat(booking.original_exchange_rate).toLocaleString()} TZS<br>
                        By: ${booking.exchange_rate_overridden_by || 'Staff'} on ${new Date(booking.exchange_rate_overridden_at).toLocaleDateString()}<br>
                        ${booking.exchange_rate_note ? `Note: ${booking.exchange_rate_note}` : ''}
                    </div>
                </td></tr>
                ` : ''}
              </table>
            </div>
          </div>`;

      // Room Image Section
      if (room.images && room.images.length > 0) {
        let imgPath = room.images[0];
        if (imgPath.startsWith('rooms/') || imgPath.startsWith('storage/rooms/')) {
          imgPath = imgPath.replace(/^storage\//, '');
        } else if (!imgPath.startsWith('http') && !imgPath.startsWith('/')) {
          imgPath = 'rooms/' + imgPath;
        }
        const storageBase = '{{ asset("storage") }}';
        const imageUrl = imgPath.startsWith('http') ? imgPath : storageBase + '/' + imgPath;
        
        detailsHtml += `
          <div class="row mt-3">
            <div class="col-md-12">
              <h5 style="color: #e77a3a; border-bottom: 2px solid #e77a3a; padding-bottom: 5px; margin-bottom: 15px;"><i class="fa fa-image"></i> Room Image</h5>
              <div class="text-center">
                <img src="${imageUrl}" alt="${room.room_type || 'Room'}" class="img-fluid" style="max-height: 300px; border-radius: 8px; box-shadow: 0 2px 8px rgba(0,0,0,0.1);" onerror="this.src='${fallbackImage}'">
              </div>
            </div>
          </div>`;
      }

      // Identity Records Section
      if (booking.id_document_type || booking.id_scan_path || booking.guest_signature_path) {
        detailsHtml += `
          <div class="row mt-4">
            <div class="col-md-12">
              <h5 style="color: #e77a3a; border-bottom: 2px solid #e77a3a; padding-bottom: 5px; margin-bottom: 15px;"><i class="fa fa-id-card"></i> Check-In Identity Records</h5>
            </div>
            <div class="col-md-6">
              <div class="card bg-light border-0">
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
              <div class="card bg-light border-0">
                <div class="card-body">
                  <h6><strong>Guest Signature</strong></h6>
                  ${booking.guest_signature_path ? `
                    <div class="mt-2 text-center" style="background: white; border-radius: 4px; padding: 10px; border: 1px solid #ddd;">
                      <img src="${baseUrl}${booking.guest_signature_path}" 
                           style="max-width: 100%; max-height: 150px;"
                           onerror="this.outerHTML='<p class=text-danger small>Signature image not found</p>'">
                    </div>
                    <p class="small text-muted mt-2">Captured on: ${booking.identity_captured_at ? new Date(booking.identity_captured_at).toLocaleString() : 'Check-in time'}</p>
                  ` : '<p class="text-muted small"><i class="fa fa-pencil"></i> No digital signature captured</p>'}
                </div>
              </div>
            </div>
          </div>`;
      }

      // Show clear records option if needed
      if ((booking.mobile_checkin_submitted_at || booking.id_scan_path) && booking.check_in_status === 'pending') {
        detailsHtml += `
          <div class="row mt-3">
             <div class="col-md-12">
                <button class="btn btn-outline-danger btn-block" onclick="requestResubmission(${booking.id}, '${booking.booking_reference}')">
                   <i class="fa fa-trash"></i> Documents incorrect? Clear & Enable Re-scan
                </button>
             </div>
          </div>`;
      }

      detailsHtml += `</div>`; // Close booking-details-view

      document.getElementById('bookingDetailsContent').innerHTML = detailsHtml;

      // Add Check-In button to footer if status is pending
      if (booking.check_in_status === 'pending') {
        const isSubmitted = !!booking.mobile_checkin_submitted_at;
        document.getElementById('modalFooterActions').innerHTML = `
          <button type="button" class="btn btn-success" onclick="verifyAndCompleteCheckIn(${booking.id}, '${booking.booking_reference}')">
             <i class="fa fa-check-circle"></i> ${isSubmitted ? 'Verify & Complete Check-In' : 'Complete Manual Check-In'}
          </button>
        `;
      } else {
        document.getElementById('modalFooterActions').innerHTML = `
           <span class="badge badge-secondary mr-2" style="padding: 8px 12px; font-size: 14px;">
              <i class="fa fa-info-circle"></i> Already ${booking.check_in_status.replace('_', ' ').toUpperCase()}
           </span>
        `;
      }
    } else {
      document.getElementById('bookingDetailsContent').innerHTML = `
        <div class="alert alert-danger">
          <i class="fa fa-exclamation-triangle"></i> Failed to load booking details.
        </div>
      `;
    }
  })
  .catch(error => {
    console.error('Error:', error);
    document.getElementById('bookingDetailsContent').innerHTML = `
      <div class="alert alert-danger">
        <i class="fa fa-exclamation-triangle"></i> An error occurred while loading booking details: ${error.message || 'Unknown error'}
      </div>
    `;
  });
}

// Real-time filtering functions
function filterCheckIns() {
  const searchInput = document.getElementById('searchInput').value.toLowerCase();
  const checkInDateFilter = document.getElementById('checkInDateFilter').value;
  
  // Filter both table rows and mobile cards
  const rows = document.querySelectorAll('.checkin-row');
  let visibleCount = 0;
  
  rows.forEach(row => {
    const bookingRef = row.getAttribute('data-booking-ref') || '';
    const guestName = row.getAttribute('data-guest-name') || '';
    const guestEmail = row.getAttribute('data-guest-email') || '';
    const checkInDate = row.getAttribute('data-check-in-date') || '';
    
    // Check if this is a corporate booking
    const isCorporate = row.classList.contains('corporate-booking-group');
    
    let show = true;
    
    // Search filter
    if (searchInput && searchInput.trim() !== '') {
      const searchLower = searchInput.toLowerCase();
      let matchesSearch = false;
      
      // Check booking reference
      if (bookingRef && bookingRef.includes(searchLower)) {
        matchesSearch = true;
      }
      
      // For individual bookings, check guest name and email
      if (!isCorporate) {
        if ((guestName && guestName.includes(searchLower)) || 
            (guestEmail && guestEmail.includes(searchLower))) {
          matchesSearch = true;
        }
      } else {
        // For corporate bookings, check company name and email from data attributes
        const companyName = row.getAttribute('data-company-name') || '';
        const companyEmail = row.getAttribute('data-company-email') || '';
        if ((companyName && companyName.includes(searchLower)) || 
            (companyEmail && companyEmail.includes(searchLower))) {
          matchesSearch = true;
        } else {
          // Fallback: check row text content
          const rowText = row.textContent || row.innerText || '';
          if (rowText.toLowerCase().includes(searchLower)) {
            matchesSearch = true;
          }
        }
      }
      
      if (!matchesSearch) {
        show = false;
      }
    }
    
    // Date filter
    if (checkInDateFilter && checkInDateFilter.trim() !== '') {
      if (checkInDate !== checkInDateFilter) {
        show = false;
      }
    }
    
    // Show/hide row
    if (show) {
      row.style.display = '';
      visibleCount++;
    } else {
      row.style.display = 'none';
    }
  });
  
  // Show/hide "no results" message
  const noResultsMsg = document.getElementById('noResultsMessage');
  if (visibleCount === 0 && rows.length > 0 && (searchInput || checkInDateFilter)) {
    if (!noResultsMsg) {
      const tileBody = document.querySelector('.tile-body');
      const msgDiv = document.createElement('div');
      msgDiv.id = 'noResultsMessage';
      msgDiv.className = 'text-center';
      msgDiv.style.padding = '50px';
      msgDiv.innerHTML = `
        <i class="fa fa-search fa-3x text-muted mb-3"></i>
        <p class="text-muted">No bookings found matching your search criteria.</p>
      `;
      tileBody.appendChild(msgDiv);
    } else {
      noResultsMsg.style.display = 'block';
    }
  } else {
    if (noResultsMsg) {
      noResultsMsg.style.display = 'none';
    }
  }
  
  // Hide pagination when filtering
  const paginationContainer = document.getElementById('paginationContainer');
  if (paginationContainer) {
    if (searchInput || checkInDateFilter) {
      paginationContainer.style.display = 'none';
    } else {
      paginationContainer.style.display = 'flex';
    }
  }
}

function resetCheckInFilters() {
  document.getElementById('searchInput').value = '';
  document.getElementById('checkInDateFilter').value = '';
  filterCheckIns();
}

// Initialize filters on page load
document.addEventListener('DOMContentLoaded', function() {
  // Clear any pre-filled filters if not requested
  const searchInput = document.getElementById('searchInput');
  if (searchInput && !searchInput.value) searchInput.value = '';
});

function requestResubmission(bookingId, bookingRef) {
    @php
        $resubmissionRoute = ($role === 'manager') 
            ? 'admin.bookings.request-resubmission' 
            : 'reception.bookings.request-resubmission';
    @endphp
    fetch('{{ route($resubmissionRoute, "99999", false) }}'.replace('99999', bookingId), {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
                showSuccessToast("Records cleared. Please have the guest scan the QR code to re-submit.", "Ready for Re-scan");
                
                // Intelligent transition: Hide details and open scanner automatically
                $('#bookingDetailsModal').modal('hide');
                    
                    // Small delay to ensure smooth transition
                    setTimeout(() => {
                        checkInGuest(bookingId, bookingRef);
                    }, 800);
        } else {
            swal("Error", data.message || "Failed to request re-submission.", "error");
        }
    })
    .catch(err => {
        console.error("Resubmission error:", err);
        swal("Error", "Could not connect to the server.", "error");
    });
}

function verifyAndCompleteCheckIn(bookingId, bookingRef) {
    swal({
        title: "Confirm Check-In",
        text: `The records for ${bookingRef} look correct. Complete check-in?`,
        type: "info",
        showCancelButton: true,
        confirmButtonColor: "#28a745",
        confirmButtonText: "Yes, Finalize Now",
        closeOnConfirm: false,
        showLoaderOnConfirm: true
    }, function(isConfirm) {
        if (isConfirm) {
             @php
                $checkInRoute = ($role === 'manager') ? 'admin.bookings.update-checkin' : 'reception.bookings.update-checkin';
            @endphp
            fetch('{{ route($checkInRoute, "99999", false) }}'.replace('99999', bookingId), {
                method: 'PUT',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json'
                },
                body: JSON.stringify({
                    check_in_status: 'checked_in'
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    $('#bookingDetailsModal').modal('hide');
                    $('#enhancedCheckInModal').modal('hide');
                    swal({
                        title: "Completed!",
                        text: "Guest is checked in.",
                        type: "success"
                    }, function() {
                        location.reload();
                    });
                } else {
                    swal("Error", data.message || "Something went wrong.", "error");
                }
            })
            .catch(err => {
                console.error("Fetch error:", err);
                swal("Connection Error", "Could not reach the server.", "error");
            });
        }
    });
}

// --- Smart Check-In Monitor ---
let lastCheckedSubmission = new Date().toISOString();
let processedSubmissionIds = new Set();
let isMonitoringEnabled = true;

function startSmartMonitor() {
    if (!isMonitoringEnabled) return;
    
    setInterval(async () => {
        try {
            @php
                $recentSubmissionsRoute = ($role === 'manager') 
                    ? 'admin.bookings.recent-submissions' 
                    : 'reception.bookings.recent-submissions';
            @endphp
            const response = await fetch('{{ route($recentSubmissionsRoute, [], false) }}' + `?since=${lastCheckedSubmission}`);
            const data = await response.json();
            
            if (data.success && data.submissions.length > 0) {
                data.submissions.forEach(submission => {
                    if (!processedSubmissionIds.has(submission.id)) {
                        processedSubmissionIds.add(submission.id);
                        showSubmissionAlert(submission);
                    }
                });
                // Update last checked to the most recent submission time
                lastCheckedSubmission = data.submissions[0].mobile_checkin_submitted_at;
            }
        } catch (err) {
            console.error("Smart Monitor Error:", err);
        }
    }, 5000); // Check every 5 seconds for faster response
}

function showSubmissionAlert(booking) {
    // Suppress if corporate modal is open (hub handles its own alerts)
    if ($('#companyGroupCheckInModal').hasClass('show')) {
        return;
    }
    
    // Play a subtle notification sound if available
    try {
        const audio = new Audio('https://assets.mixkit.co/active_storage/sfx/2869/2869-preview.mp3');
        audio.volume = 0.5;
        audio.play().catch(e => console.log("Sound blocked by browser"));
    } catch(e) {}

    // Use a non-intrusive toast instead of a modal
    if (typeof showSuccessToast === 'function') {
        showSuccessToast(`Registration received for ${booking.guest_name}.`, "Update");
    }

    // Also highlight the row in the table if it's visible
    const row = document.querySelector(`.checkin-row[data-booking-ref="${booking.booking_reference.toLowerCase()}"]`);
    if (row) {
        row.style.backgroundColor = '#fff3e0';
        row.style.transition = 'background-color 1s ease';
        setTimeout(() => row.style.backgroundColor = '', 10000);
    }
}

// Start monitoring when page loads
document.addEventListener('DOMContentLoaded', startSmartMonitor);
</script>

<!-- Company Group Check-In Modal -->
<div class="modal fade" id="companyGroupCheckInModal" tabindex="-1" role="dialog" aria-hidden="true" style="z-index: 1060;">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header" style="background: #e77a3a; color: white;">
        <h5 class="modal-title"><i class="fa fa-users"></i> Company Group Check-In</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close" style="color: white;">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <div id="companyInfoCard" class="mb-4 p-3 bg-light rounded border">
          <h5 id="modalCompanyName" class="text-primary mb-1"></h5>
          <p id="modalCompanyInfo" class="small text-muted mb-0"></p>
        </div>
        
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h6 class="mb-0 font-weight-bold">Guest Registrations</h6>
            <button class="btn btn-sm btn-success" id="bulkCheckInBtn">
                <i class="fa fa-sign-in"></i> Bulk Check-In All Pending
            </button>
        </div>

        <div class="table-responsive">
          <table class="table table-sm table-hover border">
            <thead class="bg-light">
              <tr>
                <th>Guest Name</th>
                <th>Room</th>
                <th>Registration Status</th>
                <th class="text-right">Action</th>
              </tr>
            </thead>
            <tbody id="companyGuestListBody">
              <!-- Guest rows will be populated here -->
            </tbody>
          </table>
        </div>
      </div>
      <div class="modal-footer bg-light">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>

<!-- Simplified QR Check-In Modal -->
<div class="modal fade" id="enhancedCheckInModal" tabindex="-1" role="dialog" aria-hidden="true" style="z-index: 1100;">
  <div class="modal-dialog" role="document">
    <div class="modal-content border-0 shadow-lg">
      <div class="modal-header" style="background: #e77a3a; color: white;">
        <h5 class="modal-title font-weight-bold"><i class="fa fa-qrcode"></i> Digital Guest Registration</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close" style="color: white;">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body text-center p-5">
        <h5 class="font-weight-bold mb-4">Please ask the guest to scan:</h5>
        
        <div id="checkin-qrcode" class="d-inline-block p-4 bg-white border mb-4 rounded shadow-sm" style="min-height: 260px; min-width: 260px;">
            <!-- QR Code Appears Here -->
        </div>

        <div class="mt-2">
            <p class="text-info mb-1"><i class="fa fa-spinner fa-spin"></i> <strong>Live Connection Active</strong></p>
            <p class="text-muted small">Guest uses their own phone to scan ID and sign digital records. This screen will automatically refresh once they finish.</p>
        </div>
      </div>
      <div class="modal-footer bg-light py-2 d-flex justify-content-between align-items-center">
        <small class="text-muted"><i class="fa fa-lock"></i> Secure Registration Bridge</small>
        <div id="checkin-footer-actions"></div>
      </div>
    </div>
  </div>
</div>
@endsection





