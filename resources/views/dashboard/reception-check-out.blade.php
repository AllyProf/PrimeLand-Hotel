@extends('dashboard.layouts.app')

@section('content')
<div class="app-title">
  <div>
    <h1><i class="fa fa-sign-out"></i> Check Out</h1>
    <p>Check out guests from their reservations</p>
  </div>
  <ul class="app-breadcrumb breadcrumb">
    <li class="breadcrumb-item"><i class="fa fa-home fa-lg"></i></li>
    <li class="breadcrumb-item"><a href="{{ route('reception.dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item"><a href="#">Check Out</a></li>
  </ul>
</div>

<div class="row mb-3">
  <div class="col-md-12">
    <div class="tile">
      <div class="tile-title-w-btn mb-3">
        <h3 class="title">Ready for Check-Out</h3>
      </div>
      
      @php
        // Determine which routes to use based on role
        $checkOutRoute = ($role === 'manager') 
          ? 'admin.reservations.check-out' 
          : 'reception.reservations.check-out';
      @endphp
      
      <!-- Booking Type Tabs -->
      <div class="booking-tabs-wrapper mb-4">
        <ul class="nav nav-pills nav-justified" role="tablist" style="background: #f8f9fa; padding: 8px; border-radius: 8px;">
          <li class="nav-item">
            <a class="nav-link {{ ($bookingType ?? 'individual') == 'individual' ? 'active' : '' }}" 
               href="{{ route($checkOutRoute, array_merge(request()->except(['type']), ['type' => 'individual'])) }}"
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
               href="{{ route($checkOutRoute, array_merge(request()->except(['type']), ['type' => 'corporate'])) }}"
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
        <div class="col-md-4 col-12 mb-2 mb-md-0">
          <input type="text" class="form-control" id="searchInput" placeholder="Search by booking reference, guest name, or email..." onkeyup="filterCheckouts()" oninput="filterCheckouts()" style="font-size: 16px;">
        </div>
        <div class="col-md-3 col-12 mb-2 mb-md-0">
          <select class="form-control" id="statusFilter" onchange="filterCheckouts()" style="font-size: 16px;">
            <option value="all">All Status</option>
            <option value="checked_in">Checked In</option>
            <option value="checked_out">Checked Out</option>
          </select>
        </div>
        <div class="col-md-3 col-12 mb-2 mb-md-0">
          <input type="date" class="form-control" id="checkOutDateFilter" onchange="filterCheckouts()" value="{{ today()->format('Y-m-d') }}" style="font-size: 16px;">
        </div>
        <div class="col-md-2 col-12">
          <button class="btn btn-secondary btn-block" onclick="resetCheckoutFilters()">
            <i class="fa fa-refresh"></i> Reset
          </button>
        </div>
      </div>
      
      @php
        // Determine which routes to use based on role (available for both desktop and mobile views)
        $checkoutBillRoute = ($role === 'manager') 
          ? 'admin.bookings.checkout-bill' 
          : 'reception.bookings.checkout-bill';
        $checkoutPaymentRoute = ($role === 'manager') 
          ? 'admin.checkout-payment' 
          : 'reception.checkout-payment';
        $checkoutPaymentCashRoute = ($role === 'manager') 
          ? 'admin.checkout-payment.cash' 
          : 'reception.checkout-payment.cash';
        $updateCheckInRoute = ($role === 'manager') 
          ? 'admin.bookings.update-checkin' 
          : 'reception.bookings.update-checkin';
      @endphp
      
      <div class="tile-body">
        @if($bookings->count() > 0)
        <!-- Desktop Table View -->
        <div class="table-responsive">
          <table class="table table-hover table-bordered" id="checkOutTable">
            <thead>
              <tr>
                <th>Booking Reference</th>
                <th>{{ ($bookingType ?? 'individual') == 'corporate' ? 'Company' : 'Guest' }}</th>
                <th>{{ ($bookingType ?? 'individual') == 'corporate' ? 'Guests/Rooms' : 'Room' }}</th>
                <th>Check-in Date</th>
                <th>Check-out Date</th>
                <th>Checked In At</th>
                <th>Time Remaining</th>
                <th>Signature Status</th>
                <th>Services Used</th>
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
                  @endphp
                  <tr class="checkout-row corporate-booking-group"
                      data-booking-id="{{ $firstBooking->id ?? 0 }}"
                      data-booking-ref="{{ strtolower($firstBooking->booking_reference ?? '') }}"
                      data-check-out-date="{{ $firstBooking->check_out->format('Y-m-d') ?? '' }}"
                      data-company-name="{{ strtolower($company->name ?? '') }}"
                      data-company-email="{{ strtolower($company->email ?? '') }}">
                    <td><strong>{{ $firstBooking->booking_reference ?? 'N/A' }}</strong></td>
                    <td>
                      @if($company)
                        <strong><i class="fa fa-building"></i> {{ $company->name }}</strong><br>
                        <small>{{ $company->email }}</small>
                        @if($company->phone)
                        <br><small>{{ $company->phone }}</small>
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
                    <td>
                      @if($firstBooking->checked_in_at)
                        {{ $firstBooking->checked_in_at->format('M d, Y H:i') }}
                      @else
                        <span class="text-muted">N/A</span>
                      @endif
                    </td>
                    <td>
                      @php
                        $now = \Carbon\Carbon::now();
                        $checkOutDate = $firstBooking ? \Carbon\Carbon::parse($firstBooking->check_out) : null;
                        
                        $checkedOutCount = $companyBookings->where('check_in_status', 'checked_out')->count();
                        $isFullyCheckedOut = ($checkedOutCount === $totalGuests);

                        if ($checkOutDate) {
                          $diffInDays = (int)$now->diffInDays($checkOutDate, false);
                          $diffInHours = (int)$now->diffInHours($checkOutDate, false);
                          $diffInMinutes = (int)$now->diffInMinutes($checkOutDate, false);
                        }
                      @endphp
                      @if($isFullyCheckedOut)
                        <span class="badge badge-success" style="padding: 6px 12px;">
                          <i class="fa fa-user-times"></i> GUESTS CHECKED OUT
                          <br><small>{{ $checkedOutCount }}/{{ $totalGuests }} Guests Departed</small>
                        </span>
                      @elseif($checkOutDate && $checkOutDate->isPast())
                        @php
                          $daysOverdue = (int)$checkOutDate->diffInDays($now);
                          $hoursOverdue = (int)$checkOutDate->diffInHours($now);
                        @endphp
                        @if($daysOverdue >= 1)
                          <span class="badge badge-danger" title="Check-out date was {{ $daysOverdue }} day(s) ago">
                            <i class="fa fa-exclamation-triangle"></i> Overdue ({{ $daysOverdue }} day{{ $daysOverdue > 1 ? 's' : '' }})
                          </span>
                        @else
                          <span class="badge badge-danger" title="Check-out date was {{ $hoursOverdue }} hour(s) ago">
                            <i class="fa fa-exclamation-triangle"></i> Overdue ({{ $hoursOverdue }} hour{{ $hoursOverdue > 1 ? 's' : '' }})
                          </span>
                        @endif
                      @elseif($checkOutDate && $diffInDays < 1 && $diffInDays >= 0)
                        @php
                          $checkoutTime = $firstBooking->room->checkout_time ?? 
                                         \App\Models\HotelSetting::getValue('default_checkout_time') ?? 
                                         \App\Models\HotelSetting::getValue('default_room_checkout_time') ?? 
                                         '12:00';
                          $timeParts = explode(':', $checkoutTime);
                          $hour = (int)$timeParts[0];
                          $minute = $timeParts[1] ?? '00';
                          $ampm = $hour >= 12 ? 'PM' : 'AM';
                          $hour12 = $hour > 12 ? $hour - 12 : ($hour == 0 ? 12 : $hour);
                          $formattedTime = $hour12 . ':' . $minute . ' ' . $ampm;
                        @endphp
                        @if($diffInHours > 0)
                          <span class="badge badge-warning">
                            <i class="fa fa-clock"></i> {{ (int)$diffInHours }} hour{{ (int)$diffInHours > 1 ? 's' : '' }} remaining<br>
                            <small>Check-out at {{ $formattedTime }}</small>
                          </span>
                        @elseif($diffInMinutes > 0)
                          <span class="badge badge-warning">
                            <i class="fa fa-clock"></i> {{ (int)$diffInMinutes }} minute{{ (int)$diffInMinutes > 1 ? 's' : '' }} remaining<br>
                            <small>Check-out at {{ $formattedTime }}</small>
                          </span>
                        @else
                          <span class="badge badge-success">
                            <i class="fa fa-check-circle"></i> Check-out Time!<br>
                            <small>Due at {{ $formattedTime }}</small>
                          </span>
                        @endif
                      @elseif($checkOutDate && $diffInDays == 1)
                        <span class="badge badge-info">
                          <i class="fa fa-calendar"></i> Tomorrow
                        </span>
                      @elseif($checkOutDate)
                        @php
                          $weeksRemaining = floor((int)$diffInDays / 7);
                          $daysRemaining = (int)$diffInDays;
                        @endphp
                        @if($weeksRemaining > 0)
                          <span class="badge badge-primary">
                            <i class="fa fa-calendar"></i> {{ $weeksRemaining }} week{{ $weeksRemaining > 1 ? 's' : '' }} remaining
                          </span>
                        @else
                          <span class="badge badge-primary">
                            <i class="fa fa-calendar"></i> {{ $daysRemaining }} day{{ $daysRemaining > 1 ? 's' : '' }} remaining
                          </span>
                        @endif
                      @else
                        <span class="text-muted">N/A</span>
                      @endif
                    <td>
                      @if($companyBookings->contains(fn($b) => !empty($b->checkout_signature_path)))
                        <span class="badge badge-info" title="Some guests have signed checkout">
                          <i class="fa fa-file-text-o"></i> Signed
                        </span>
                      @elseif($companyBookings->contains(fn($b) => !empty($b->guest_signature_path)))
                        <span class="badge badge-warning" title="Check-in signatures are on file">
                          <i class="fa fa-pencil"></i> Check-in Sign
                        </span>
                      @else
                        <span class="text-muted small">No Signature</span>
                      @endif
                    </td>
                    <td>
                      <small class="text-muted"><i class="fa fa-info-circle"></i> Expand to view guest services</small>
                    </td>
                    <td>
                      @php
                        $groupRate = $firstBooking->locked_exchange_rate ?? $exchangeRate;
                      @endphp
                      <strong>${{ number_format($totalPrice, 2) }}</strong><br>
                      <small>{{ number_format($totalPrice * $groupRate, 2) }} TZS</small>
                      @php
                        $totalOutstandingTsh = $group['total_outstanding_tsh'] ?? 0;
                        $totalOutstandingUsd = $group['total_outstanding_usd'] ?? 0;

                        $selfPaidGuestsWithBalance = $companyBookings->filter(function($b) {
                            return ($b->payment_responsibility ?? 'company') === 'self' && 
                                   isset($b->outstanding_balance_tsh) && 
                                   $b->outstanding_balance_tsh >= 50;
                        });
                        $guestOutstandingTsh = $selfPaidGuestsWithBalance->sum('outstanding_balance_tsh');
                        $guestOutstandingUsd = $selfPaidGuestsWithBalance->sum('outstanding_balance_usd');
                      @endphp
                      @if($totalOutstandingTsh >= 50)
                        <br><small class="text-danger">
                          <strong>Company Outstanding: ${{ number_format($totalOutstandingUsd, 2) }}</strong><br>
                          <strong>{{ number_format($totalOutstandingTsh, 2) }} TZS</strong>
                        </small>
                      @endif

                      @if($selfPaidGuestsWithBalance->count() > 0)
                        <div class="mt-1">
                          <span class="badge badge-warning" 
                                onclick="viewCompanyBookings({{ $company->id ?? 0 }}, {{ $loop->index }})"
                                style="cursor: pointer; padding: 5px 10px;"
                                title="Click to view and pay individual guest charges">
                            <i class="fa fa-user"></i> Guest Outstanding: ${{ number_format($guestOutstandingUsd, 2) }}
                            <br><small>(Click to Pay)</small>
                          </span>
                        </div>
                      @endif

                      @if($totalOutstandingTsh < 50 && $selfPaidGuestsWithBalance->count() == 0)
                        <div class="mt-2">
                          <span class="badge badge-success" style="padding: 6px 12px; font-size: 13px;">
                            <i class="fa fa-check-circle"></i> FULLY PAID
                          </span>
                        </div>
                      @endif
                    </td>
                    <td>
                      @php
                        $currentRoute = request()->route()->getName() ?? '';
                        $checkoutBillRoute = (str_starts_with($currentRoute, 'admin.')) 
                          ? 'admin.bookings.checkout-bill' 
                          : 'reception.bookings.checkout-bill';
                        $safeCompanyName = $company->name ?? 'Company';
                      @endphp
                        <button class="btn btn-sm btn-info mr-1" onclick="viewCompanyBookings({{ $company->id ?? 0 }}, {{ $loop->index }})" title="View All Bookings">
                        <i class="fa fa-eye"></i> View More
                      </button>
                      @php
                        $anyCheckedIn = $companyBookings->contains(function($b) { return ($b->check_in_status ?? 'pending') == 'checked_in'; });
                      @endphp
                      <a href="{{ route('reception.companies.group-bill', $company->id ?? 0) }}" class="btn btn-sm btn-dark mr-1" target="_blank" title="Print Group Bill">
                        <i class="fa fa-print"></i> Group Bill
                      </a>
                      @php
                        $hasAnyOutstanding = ($totalOutstandingTsh >= 50 || $selfPaidGuestsWithBalance->count() > 0);
                        $outstandingMessage = $totalOutstandingTsh >= 50 
                            ? "Cannot check out - Company has an outstanding balance that must be paid first" 
                            : "Cannot check out - One or more guests have individual outstanding charges that must be paid first";
                      @endphp

                      @if($hasAnyOutstanding)
                        @if($totalOutstandingTsh >= 50)
                          <button class="btn btn-sm btn-success mr-1" onclick="openCashPaymentModalCompany({{ $company->id ?? 0 }}, {{ json_encode($safeCompanyName) }}, {{ $totalOutstandingUsd }}, {{ $totalOutstandingTsh }})" title="Process Payment">
                            <i class="fa fa-money"></i> Pay Company Bill
                          </button>
                        @endif
                        <button class="btn btn-sm btn-danger" disabled title="{{ $outstandingMessage }}">
                          <i class="fa fa-sign-out"></i> Check Out
                        </button>
                      @elseif($isFullyCheckedOut)
                        <button class="btn btn-sm btn-secondary" disabled title="All guests have already checked out">
                          <i class="fa fa-check"></i> Checked Out
                        </button>
                      @else
                        <button class="btn btn-sm btn-danger" onclick="checkOutCompanyGroup({{ $company->id ?? 0 }}, {{ json_encode($safeCompanyName) }})" title="Check Out All Guests">
                          <i class="fa fa-sign-out"></i> Check Out
                        </button>
                      @endif
                    </td>
                  </tr>
                  <!-- Expandable row for individual bookings -->
                  <tr class="company-bookings-detail" id="company-bookings-{{ $company->id ?? 0 }}-{{ $loop->index }}" style="display: none;">
                    <td colspan="10" style="background-color: #f8f9fa; padding: 15px;">
                      <h6 class="mb-3"><strong>Individual Bookings:</strong></h6>
                      <div class="table-responsive">
                        <table class="table table-sm table-bordered">
                          <thead>
                            <tr>
                              <th>Ref</th>
                              <th>Guest</th>
                              <th>Room</th>
                              <th>Status</th>
                              <th>Signature</th>
                               <th>Check-out</th>
                              <th>Services Used</th>
                              <th>Total Price</th>
                              <th>Outstanding</th>
                              <th>Actions</th>
                            </tr>
                          </thead>
                          <tbody>
                            @foreach($companyBookings as $booking)
                            <tr>
                              <td><strong>{{ $booking->booking_reference }}</strong></td>
                              <td>
                                <strong>{{ $booking->guest_name }}</strong><br>
                                <small>{{ $booking->guest_email }}</small>
                              </td>
                              <td>
                                <span class="badge badge-primary">{{ $booking->room->room_type ?? 'N/A' }}</span><br>
                                <small>{{ $booking->room->room_number ?? 'N/A' }}</small>
                              </td>
                              <td>
                                @if($booking->check_in_status === 'checked_out')
                                  <span class="badge badge-secondary"><i class="fa fa-sign-out"></i> Checked Out</span>
                                @else
                                  <span class="badge badge-success"><i class="fa fa-user"></i> Stay</span>
                                @endif
                              </td>
                                <td>
                                  @if($booking->checkout_signature_path)
                                    <span class="badge badge-info" title="Guest has signed for check-out">
                                      <i class="fa fa-file-text-o"></i> Signed
                                    </span>
                                  @elseif($booking->guest_signature_path)
                                    <span class="badge badge-warning" title="Check-in signature exists">
                                      <i class="fa fa-pencil"></i> Check-in Sign
                                    </span>
                                  @else
                                    <span class="text-muted small">No Sign</span>
                                  @endif
                                </td>
                               <td>{{ $booking->check_out->format('M d, Y') }}</td>
                                <td>
                                  @php
                                    // Be more inclusive of statuses for checkout view
                                    $usedSvs = $booking->serviceRequests->whereIn('status', ['pending', 'approved', 'preparing', 'delivered', 'completed']);
                                  @endphp
                                  @forelse($usedSvs as $svc)
                                    <div class="mb-1" style="line-height: 1.2;">
                                      <small><strong>{{ $svc->quantity }}x</strong> {{ $svc->service_specific_data['item_name'] ?? $svc->service->name }}</small>
                                    </div>
                                  @empty
                                    <small class="text-muted">No services</small>
                                  @endforelse
                                </td>
                               <td>
                                @php
                                  $bookingCurrentRate = $booking->locked_exchange_rate ?? $exchangeRate;
                                  $totalGuestBillTsh = $booking->total_bill_tsh ?? ($booking->total_price * $bookingCurrentRate);
                                  $totalGuestBillUsd = $booking->total_bill_usd ?? ($totalGuestBillTsh / $bookingCurrentRate);
                                @endphp
                                <strong>${{ number_format($totalGuestBillUsd, 2) }}</strong><br>
                                <small>{{ number_format($totalGuestBillTsh, 2) }} TZS</small>
                              </td>
                              <td>
                                @php
                                  $paymentResponsibility = $booking->payment_responsibility ?? 'company';
                                  $isSelfPaid = $paymentResponsibility === 'self';
                                  $hasOutstanding = isset($booking->outstanding_balance_tsh) && $booking->outstanding_balance_tsh >= 50;
                                @endphp
                                @if($hasOutstanding)
                                  <span class="text-danger">
                                    <strong>${{ number_format($booking->outstanding_balance_usd ?? 0, 2) }}</strong><br>
                                    <strong>{{ number_format($booking->outstanding_balance_tsh, 2) }} TZS</strong>
                                    @if($isSelfPaid)
                                      <br><small class="badge badge-warning"><i class="fa fa-user"></i> Self-Paid Services</small>
                                    @endif
                                  </span>
                                @elseif($isSelfPaid && isset($booking->outstanding_balance_tsh) && $booking->outstanding_balance_tsh > 0)
                                  <span class="text-success"><i class="fa fa-check-circle"></i> All Paid</span>
                                @elseif(!$isSelfPaid)
                                  <span class="text-muted"><i class="fa fa-building"></i> Company Paid</span>
                                @else
                                  <span class="text-success"><i class="fa fa-check-circle"></i> No Charges</span>
                                @endif
                              </td>
                              <td>
                                 <a href="{{ route($checkoutBillRoute, $booking) }}" class="btn btn-xs btn-info" target="_blank" title="View Bill">
                                  <i class="fa fa-file-text"></i> Bill
                                </a>
                                @if(isset($booking->outstanding_balance_tsh) && $booking->outstanding_balance_tsh >= 50)
                                      <button class="btn btn-xs btn-success" onclick="openCashPaymentModal({{ $booking->id }}, {{ json_encode($booking->booking_reference) }}, {{ $booking->outstanding_balance_usd ?? 0 }}, {{ $booking->outstanding_balance_tsh ?? 0 }})" title="Process Payment">
                                        <i class="fa fa-money"></i> Pay Guest Charge
                                      </button>
                                      <!-- Individual checkout button removed for corporate view to favor group action -->
                                @elseif($booking->check_in_status !== 'checked_out')
                                  <button class="btn btn-xs btn-danger" onclick="checkOutGuest({{ $booking->id }}, '{{ $booking->booking_reference }}')" title="Scan Check Out">
                                    <i class="fa fa-sign-out"></i> Scan
                                  </button>
                                @endif
                              </td>
                            </tr>
                            @endforeach
                          </tbody>
                        </table>
                      </div>
                    </td>
                  </tr>
                  </tr>
                @endforeach
              @else
                @foreach($bookings as $booking)
              <tr class="checkout-row"
                  data-booking-id="{{ $booking->id }}"
                  data-booking-ref="{{ strtolower($booking->booking_reference) }}"
                  data-guest-name="{{ strtolower($booking->guest_name) }}"
                  data-guest-email="{{ strtolower($booking->guest_email) }}"
                  data-check-in-status="{{ $booking->check_in_status }}"
                  data-check-out-date="{{ $booking->check_out->format('Y-m-d') }}">
                <td><strong>{{ $booking->booking_reference }}</strong></td>
                <td>
                  <strong>{{ $booking->guest_name }}</strong><br>
                  <small>{{ $booking->guest_email }}</small>
                </td>
                <td>
                  <span class="badge badge-primary">{{ $booking->room->room_type ?? 'N/A' }}</span><br>
                  <small>{{ $booking->room->room_number ?? 'N/A' }}</small>
                </td>
                <td>{{ $booking->check_in->format('M d, Y') }}</td>
                <td>{{ $booking->check_out->format('M d, Y') }}</td>
                <td>
                  @if($booking->checked_in_at)
                    {{ $booking->checked_in_at->format('M d, Y H:i') }}
                  @else
                    <span class="text-muted">N/A</span>
                  @endif
                </td>
                <td>
                  @php
                    $now = \Carbon\Carbon::now();
                    $checkOutDate = \Carbon\Carbon::parse($booking->check_out);
                    $diffInDays = (int)$now->diffInDays($checkOutDate, false);
                    $diffInHours = (int)$now->diffInHours($checkOutDate, false);
                    $diffInMinutes = (int)$now->diffInMinutes($checkOutDate, false);
                  @endphp
                  @if($booking->check_in_status === 'checked_out')
                    <span class="badge badge-success" style="padding: 6px 12px;">
                      <i class="fa fa-sign-out"></i> GUEST CHECKED OUT
                    </span>
                  @elseif($checkOutDate->isPast())
                    @php
                      $daysOverdue = (int)$checkOutDate->diffInDays($now);
                      $hoursOverdue = (int)$checkOutDate->diffInHours($now);
                    @endphp
                    @if($daysOverdue >= 1)
                      <span class="badge badge-danger" title="Check-out date was {{ $daysOverdue }} day(s) ago">
                        <i class="fa fa-exclamation-triangle"></i> Overdue ({{ $daysOverdue }} day{{ $daysOverdue > 1 ? 's' : '' }})
                      </span>
                    @else
                      <span class="badge badge-danger" title="Check-out date was {{ $hoursOverdue }} hour(s) ago">
                        <i class="fa fa-exclamation-triangle"></i> Overdue ({{ $hoursOverdue }} hour{{ $hoursOverdue > 1 ? 's' : '' }})
                      </span>
                    @endif
                  @elseif($diffInDays < 1 && $diffInDays >= 0)
                    @php
                      // Get checkout time from room, or fall back to hotel settings, or default to 12:00
                      $checkoutTime = $booking->room->checkout_time ?? 
                                     \App\Models\HotelSetting::getValue('default_checkout_time') ?? 
                                     \App\Models\HotelSetting::getValue('default_room_checkout_time') ?? 
                                     '12:00';
                      // Format time for display (convert 24h to 12h format)
                      $timeParts = explode(':', $checkoutTime);
                      $hour = (int)$timeParts[0];
                      $minute = $timeParts[1] ?? '00';
                      $ampm = $hour >= 12 ? 'PM' : 'AM';
                      $hour12 = $hour > 12 ? $hour - 12 : ($hour == 0 ? 12 : $hour);
                      $formattedTime = $hour12 . ':' . $minute . ' ' . $ampm;
                    @endphp
                    @if($diffInHours > 0)
                      <span class="badge badge-warning">
                        <i class="fa fa-clock"></i> {{ (int)$diffInHours }} hour{{ (int)$diffInHours > 1 ? 's' : '' }} remaining<br>
                        <small>Check-out at {{ $formattedTime }}</small>
                      </span>
                    @elseif($diffInMinutes > 0)
                      <span class="badge badge-warning">
                        <i class="fa fa-clock"></i> {{ (int)$diffInMinutes }} minute{{ (int)$diffInMinutes > 1 ? 's' : '' }} remaining<br>
                        <small>Check-out at {{ $formattedTime }}</small>
                      </span>
                    @else
                      <span class="badge badge-success">
                        <i class="fa fa-check-circle"></i> Check-out Time!<br>
                        <small>Due at {{ $formattedTime }}</small>
                      </span>
                    @endif
                  @elseif($diffInDays == 1)
                    <span class="badge badge-info">
                      <i class="fa fa-calendar"></i> Tomorrow
                    </span>
                  @else
                    @php
                      $weeksRemaining = floor((int)$diffInDays / 7);
                      $daysRemaining = (int)$diffInDays;
                    @endphp
                    @if($weeksRemaining > 0)
                      <span class="badge badge-primary">
                        <i class="fa fa-calendar"></i> {{ $weeksRemaining }} week{{ $weeksRemaining > 1 ? 's' : '' }} remaining
                      </span>
                    @else
                      <span class="badge badge-primary">
                        <i class="fa fa-calendar"></i> {{ $daysRemaining }} day{{ $daysRemaining > 1 ? 's' : '' }} remaining
                      </span>
                    @endif
                  @endif
                </td>
                <td>
                  @if($booking->checkout_signature_path)
                    <span class="badge badge-info" title="Guest has signed checkout">
                      <i class="fa fa-file-text-o"></i> Signed
                    </span>
                  @elseif($booking->guest_signature_path)
                    <span class="badge badge-warning" title="Check-in signature exists">
                      <i class="fa fa-pencil"></i> Check-in Sign
                    </span>
                  @else
                    <span class="text-muted small">No Signature</span>
                  @endif
                </td>
                <td>
                  @php
                    $usedSvs = $booking->serviceRequests->whereIn('status', ['pending', 'approved', 'preparing', 'delivered', 'completed']);
                  @endphp
                  @forelse($usedSvs as $svc)
                    <div class="mb-1" style="line-height: 1.2;">
                      <small><strong>{{ $svc->quantity }}x</strong> {{ $svc->service_specific_data['item_name'] ?? $svc->service->name }}</small>
                    </div>
                  @empty
                    <small class="text-muted">No services</small>
                  @endforelse
                </td>
                <td>
                  @php
                    $bookingRate = $booking->locked_exchange_rate ?? $exchangeRate;
                    
                    // Calculate service charges - matching the inclusive status filter
                    $serviceRequests = $booking->serviceRequests->whereIn('status', ['pending', 'approved', 'preparing', 'delivered', 'completed']);
                    $serviceChargesTsh = $serviceRequests->sum('total_price_tsh');
                    $serviceChargesUsd = $serviceChargesTsh / $bookingRate;
                    
                    // Total bill = Room + Services
                    $totalBillUsd = (float)$booking->total_price + $serviceChargesUsd;
                    $totalBillTsh = ($booking->total_price * $bookingRate) + $serviceChargesTsh;
                    
                    // Determine guest type for display
                    $guestType = $booking->guest_type ?? 'international';
                    $isTanzanian = $guestType === 'tanzanian';
                  @endphp
                  
                  @if($isTanzanian)
                    <strong>{{ number_format($totalBillTsh, 2) }} TZS</strong><br>
                    @if($serviceChargesUsd > 0)
                      <small class="text-muted">Room: {{ number_format($booking->total_price * $bookingRate, 2) }} TZS</small><br>
                      <small class="text-muted">Services: {{ number_format($serviceChargesTsh, 2) }} TZS</small>
                    @endif
                  @else
                    <strong>${{ number_format($totalBillUsd, 2) }}</strong><br>
                    <small>{{ number_format($totalBillTsh, 2) }} TZS</small>
                    @if($serviceChargesUsd > 0)
                      <br><small class="text-muted">Room: ${{ number_format($booking->total_price, 2) }}</small>
                      <br><small class="text-muted">Services: ${{ number_format($serviceChargesUsd, 2) }}</small>
                    @endif
                  @endif
                  
                  @if(isset($booking->outstanding_balance_tsh) && $booking->outstanding_balance_tsh >= 50)
                    <br><small class="text-danger">
                      <strong>Outstanding: ${{ number_format($booking->outstanding_balance_usd ?? 0, 2) }}</strong><br>
                      <strong>{{ number_format($booking->outstanding_balance_tsh, 2) }} TZS</strong>
                    </small>
                  @elseif(isset($booking->outstanding_balance_tsh))
                    <br><small class="text-success"><i class="fa fa-check-circle"></i> All Paid</small>
                  @endif
                </td>
                <td>
                    <a href="{{ route($checkoutBillRoute, $booking) }}" class="btn btn-sm btn-info mr-1" target="_blank" title="View Bill">
                      <i class="fa fa-file-text"></i> Bill
                    </a>
                    @if(isset($booking->outstanding_balance_tsh) && $booking->outstanding_balance_tsh >= 50)
                      <button class="btn btn-sm btn-success mr-1" onclick="openCashPaymentModal({{ $booking->id }}, {{ json_encode($booking->booking_reference) }}, {{ $booking->outstanding_balance_usd ?? 0 }}, {{ $booking->outstanding_balance_tsh ?? 0 }})" title="Process Payment">
                        <i class="fa fa-money"></i> Pay
                      </button>
                      <button class="btn btn-sm btn-danger" disabled title="Cannot check out - Outstanding balance must be paid first">
                        <i class="fa fa-sign-out"></i> Check Out
                      </button>
                    @elseif($booking->check_in_status === 'checked_out')
                    <a href="{{ route($checkoutPaymentRoute, $booking) }}" class="btn btn-sm btn-warning mr-1" title="Process Payment">
                      <i class="fa fa-credit-card"></i> Payment
                    </a>
                  @else
                    @php
                      $isSigned = !empty($booking->checkout_signature_path);
                    @endphp
                    <button class="btn btn-sm {{ $isSigned ? 'btn-info' : 'btn-danger' }}" onclick="checkOutGuest({{ $booking->id }}, {{ json_encode($booking->booking_reference) }})" title="{{ $isSigned ? 'Review & Check Out' : 'Check Out Guest' }}">
                      <i class="fa {{ $isSigned ? 'fa-eye' : 'fa-sign-out' }}"></i> {{ $isSigned ? 'Review & Check Out' : 'Check Out' }}
                    </button>
                  @endif
                </td>
              </tr>
              @endforeach
              @endif
            </tbody>
          </table>
        </div>
        
        <!-- Mobile Card View -->
        <div class="mobile-checkout-cards">
          @if(($bookingType ?? 'individual') == 'corporate')
            @foreach($bookings as $group)
              @php
                $company = $group['company'] ?? null;
                $companyBookings = $group['bookings'] ?? collect();
                $firstBooking = $group['first_booking'] ?? $companyBookings->first();
                $totalGuests = $companyBookings->count();
                $totalPrice = $companyBookings->sum('total_price');
                $now = \Carbon\Carbon::now();
                $checkOutDate = $firstBooking ? \Carbon\Carbon::parse($firstBooking->check_out) : null;
                if ($checkOutDate) {
                  $diffInDays = (int)$now->diffInDays($checkOutDate, false);
                  $diffInHours = (int)$now->diffInHours($checkOutDate, false);
                  $diffInMinutes = (int)$now->diffInMinutes($checkOutDate, false);
                }
                
                $checkoutTime = $firstBooking->room->checkout_time ?? 
                               \App\Models\HotelSetting::getValue('default_checkout_time') ?? 
                               \App\Models\HotelSetting::getValue('default_room_checkout_time') ?? 
                               '12:00';
                $timeParts = explode(':', $checkoutTime);
                $hour = (int)$timeParts[0];
                $minute = $timeParts[1] ?? '00';
                $ampm = $hour >= 12 ? 'PM' : 'AM';
                $hour12 = $hour > 12 ? $hour - 12 : ($hour == 0 ? 12 : $hour);
                $formattedTime = $hour12 . ':' . $minute . ' ' . $ampm;
              @endphp
              <div class="mobile-checkout-card checkout-row corporate-booking-group" 
                   style="background: white; border: 1px solid #dee2e6; border-radius: 8px; padding: 15px; margin-bottom: 15px; box-shadow: 0 2px 4px rgba(0,0,0,0.1);"
                   data-booking-id="{{ $firstBooking->id ?? 0 }}"
                   data-booking-ref="{{ strtolower($firstBooking->booking_reference ?? '') }}"
                   data-check-out-date="{{ $firstBooking->check_out->format('Y-m-d') ?? '' }}"
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
                  <span style="font-weight: 600; color: #495057; font-size: 14px; flex: 0 0 40%;">Checked In At:</span>
                  <span style="text-align: right; flex: 1;">
                    @if($firstBooking->checked_in_at)
                      {{ $firstBooking->checked_in_at->format('M d, Y H:i') }}
                    @else
                      <span class="text-muted">N/A</span>
                    @endif
                  </span>
                </div>
                
                <div style="display: flex; justify-content: space-between; padding: 10px 0; border-bottom: 1px solid #f0f0f0;">
                  <span style="font-weight: 600; color: #495057; font-size: 14px; flex: 0 0 40%;">Time Remaining:</span>
                  <span style="text-align: right; flex: 1;">
                    @php
                      $checkedOutCount = $companyBookings->where('check_in_status', 'checked_out')->count();
                      $isFullyCheckedOut = ($checkedOutCount === $totalGuests);
                    @endphp

                    @if($isFullyCheckedOut)
                      <span class="badge badge-success" style="font-size: 11px;">
                        <i class="fa fa-user-times"></i> GUESTS CHECKED OUT
                      </span>
                    @elseif($checkOutDate && $checkOutDate->isPast())
                      @php
                        $daysOverdue = (int)$checkOutDate->diffInDays($now);
                        $hoursOverdue = (int)$checkOutDate->diffInHours($now);
                      @endphp
                      @if($daysOverdue >= 1)
                        <span class="badge badge-danger" title="Check-out date was {{ $daysOverdue }} day(s) ago">
                          <i class="fa fa-exclamation-triangle"></i> Overdue ({{ $daysOverdue }} day{{ $daysOverdue > 1 ? 's' : '' }})
                        </span>
                      @else
                        <span class="badge badge-danger" title="Check-out date was {{ $hoursOverdue }} hour(s) ago">
                          <i class="fa fa-exclamation-triangle"></i> Overdue ({{ $hoursOverdue }} hour{{ $hoursOverdue > 1 ? 's' : '' }})
                        </span>
                      @endif
                    @elseif($checkOutDate && $diffInDays < 1 && $diffInDays >= 0)
                      @if($diffInHours > 0)
                        <span class="badge badge-warning">
                          <i class="fa fa-clock"></i> {{ (int)$diffInHours }} hour{{ (int)$diffInHours > 1 ? 's' : '' }} remaining<br>
                          <small>Check-out at {{ $formattedTime }}</small>
                        </span>
                      @elseif($diffInMinutes > 0)
                        <span class="badge badge-warning">
                          <i class="fa fa-clock"></i> {{ (int)$diffInMinutes }} minute{{ (int)$diffInMinutes > 1 ? 's' : '' }} remaining<br>
                          <small>Check-out at {{ $formattedTime }}</small>
                        </span>
                      @else
                        <span class="badge badge-success">
                          <i class="fa fa-check-circle"></i> Check-out Time!<br>
                          <small>Due at {{ $formattedTime }}</small>
                        </span>
                      @endif
                    @elseif($checkOutDate && $diffInDays == 1)
                      <span class="badge badge-info">
                        <i class="fa fa-calendar"></i> Tomorrow
                      </span>
                    @elseif($checkOutDate)
                      @php
                        $weeksRemaining = floor((int)$diffInDays / 7);
                        $daysRemaining = (int)$diffInDays;
                      @endphp
                      @if($weeksRemaining > 0)
                        <span class="badge badge-primary">
                          <i class="fa fa-calendar"></i> {{ $weeksRemaining }} week{{ $weeksRemaining > 1 ? 's' : '' }} remaining
                        </span>
                      @else
                        <span class="badge badge-primary">
                          <i class="fa fa-calendar"></i> {{ $daysRemaining }} day{{ $daysRemaining > 1 ? 's' : '' }} remaining
                        </span>
                      @endif
                    @else
                      <span class="text-muted">N/A</span>
                    @endif
                  </span>
                </div>
                
                <div style="display: flex; justify-content: space-between; padding: 10px 0; border-bottom: 1px solid #f0f0f0;">
                  <span style="font-weight: 600; color: #495057; font-size: 14px; flex: 0 0 40%;">Signature:</span>
                  <span style="text-align: right; flex: 1;">
                    @if($companyBookings->contains(fn($b) => !empty($b->checkout_signature_path)))
                      <span class="badge badge-info" style="font-size: 11px;"><i class="fa fa-file-text-o"></i> Signed</span>
                    @elseif($companyBookings->contains(fn($b) => !empty($b->guest_signature_path)))
                      <span class="badge badge-warning" style="font-size: 11px;"><i class="fa fa-pencil"></i> Check-in Sign</span>
                    @else
                      <span class="text-muted" style="font-size: 11px;">No Signature</span>
                    @endif
                  </span>
                </div>
                
                <div style="display: flex; justify-content: space-between; padding: 10px 0; border-bottom: 1px solid #f0f0f0;">
                  <span style="font-weight: 600; color: #495057; font-size: 14px; flex: 0 0 40%;">Total Price:</span>
                  <span style="text-align: right; flex: 1;">
                    <strong>${{ number_format($totalPrice, 2) }}</strong><br>
                    <small>{{ number_format($totalPrice * $exchangeRate, 2) }} TZS</small>
                    @php
                      $totalOutstandingTsh = $group['total_outstanding_tsh'] ?? 0;
                      $totalOutstandingUsd = $group['total_outstanding_usd'] ?? 0;

                      $selfPaidGuestsWithBalance = $companyBookings->filter(function($b) {
                          return ($b->payment_responsibility ?? 'company') === 'self' && 
                                 isset($b->outstanding_balance_tsh) && 
                                 $b->outstanding_balance_tsh >= 50;
                      });
                      $guestOutstandingTsh = $selfPaidGuestsWithBalance->sum('outstanding_balance_tsh');
                      $guestOutstandingUsd = $selfPaidGuestsWithBalance->sum('outstanding_balance_usd');
                    @endphp
                    @if($totalOutstandingTsh >= 50)
                      <br><small class="text-danger">
                        <strong>Company Outstanding: ${{ number_format($totalOutstandingUsd, 2) }}</strong><br>
                        <strong>{{ number_format($totalOutstandingTsh, 2) }} TZS</strong>
                      </small>
                    @endif

                    @if($selfPaidGuestsWithBalance->count() > 0)
                      <div class="mt-1">
                        <span class="badge badge-warning" style="font-size: 11px;">
                          <i class="fa fa-user"></i> Guest Outstanding: ${{ number_format($guestOutstandingUsd, 2) }}
                        </span>
                      </div>
                    @endif

                    @if($totalOutstandingTsh < 50 && $selfPaidGuestsWithBalance->count() == 0)
                      <div class="mt-2">
                        <span class="badge badge-success" style="padding: 5px 10px; font-size: 11px;">
                          <i class="fa fa-check-circle"></i> FULLY PAID
                        </span>
                      </div>
                    @endif
                  </span>
                </div>
                
                @if($company)
                <div style="padding: 10px 0; border-bottom: 1px solid #f0f0f0;">
                  <span style="font-weight: 600; color: #495057; font-size: 14px; display: block; margin-bottom: 5px;">Email:</span>
                  <span style="font-size: 13px; color: #666;">{{ $company->email }}</span>
                  @if($company->phone)
                  <br><span style="font-size: 13px; color: #666;"><i class="fa fa-phone"></i> {{ $company->phone }}</span>
                  @endif
                </div>
                @endif
                
                <div style="margin-top: 15px; padding-top: 15px; border-top: 1px solid #dee2e6; display: flex; gap: 8px; flex-wrap: wrap;">
                  <button class="btn btn-sm btn-info" onclick="viewCompanyBookingsMobile({{ $company->id ?? 0 }}, {{ $loop->index }})" title="View All Bookings" style="flex: 1; min-width: calc(50% - 4px);">
                    <i class="fa fa-eye"></i> View More
                  </button>
                  @php
                    $anyCheckedIn = $companyBookings->contains(function($b) { return ($b->check_in_status ?? 'pending') == 'checked_in'; });
                  @endphp
                  <a href="{{ route('reception.companies.group-bill', $company->id ?? 0) }}" class="btn btn-sm btn-dark" target="_blank" title="Print Group Bill" style="flex: 1; min-width: calc(50% - 4px); text-align: center;">
                    <i class="fa fa-print"></i> Group Bill
                  </a>
                  @php
                    $hasAnyOutstanding = ($totalOutstandingTsh >= 50 || $selfPaidGuestsWithBalance->count() > 0);
                    $outstandingMessage = $totalOutstandingTsh >= 50 
                        ? "Cannot check out - Company balance must be paid first" 
                        : "Cannot check out - Guest charges must be paid first";
                  @endphp

                  @if($hasAnyOutstanding)
                    @if($totalOutstandingTsh >= 50)
                      <button class="btn btn-sm btn-success" onclick="openCashPaymentModalCompany({{ $company->id ?? 0 }}, {{ json_encode($safeCompanyName) }}, {{ $totalOutstandingUsd }}, {{ $totalOutstandingTsh }})" title="Process Payment" style="flex: 1; min-width: calc(50% - 4px);">
                        <i class="fa fa-money"></i> Pay Company Bill
                      </button>
                    @endif
                    <button class="btn btn-sm btn-danger" disabled title="{{ $outstandingMessage }}" style="flex: 0 0 100%; margin-top: 8px;">
                      <i class="fa fa-sign-out"></i> Check Out (Blocked)
                    </button>
                  @else
                    <button class="btn btn-sm btn-danger btn-block" onclick="checkOutCompanyGroup({{ $company->id ?? 0 }}, {{ json_encode($safeCompanyName) }})" title="Check Out All Guests" style="flex: 1; min-width: 100%;">
                      <i class="fa fa-sign-out"></i> Check Out All Guests
                    </button>
                  @endif
                </div>
              </div>
            @endforeach
          @else
            @foreach($bookings as $booking)
            @php
              $now = \Carbon\Carbon::now();
              $checkOutDate = \Carbon\Carbon::parse($booking->check_out);
              $diffInDays = (int)$now->diffInDays($checkOutDate, false);
              $diffInHours = (int)$now->diffInHours($checkOutDate, false);
              $diffInMinutes = (int)$now->diffInMinutes($checkOutDate, false);
              
              $checkoutTime = $booking->room->checkout_time ?? 
                             \App\Models\HotelSetting::getValue('default_checkout_time') ?? 
                             \App\Models\HotelSetting::getValue('default_room_checkout_time') ?? 
                             '12:00';
              $timeParts = explode(':', $checkoutTime);
              $hour = (int)$timeParts[0];
              $minute = $timeParts[1] ?? '00';
              $ampm = $hour >= 12 ? 'PM' : 'AM';
              $hour12 = $hour > 12 ? $hour - 12 : ($hour == 0 ? 12 : $hour);
              $formattedTime = $hour12 . ':' . $minute . ' ' . $ampm;
              
              $currentRoute = request()->route()->getName() ?? '';
              $checkoutBillRoute = (str_starts_with($currentRoute, 'admin.')) 
                ? 'admin.bookings.checkout-bill' 
                : 'reception.bookings.checkout-bill';
            @endphp
            <div class="mobile-checkout-card checkout-row" style="background: white; border: 1px solid #dee2e6; border-radius: 8px; padding: 15px; margin-bottom: 15px; box-shadow: 0 2px 4px rgba(0,0,0,0.1);"
                 data-booking-id="{{ $booking->id }}"
                 data-booking-ref="{{ strtolower($booking->booking_reference) }}"
                 data-guest-name="{{ strtolower($booking->guest_name) }}"
                 data-guest-email="{{ strtolower($booking->guest_email) }}"
                 data-check-in-status="{{ $booking->check_in_status }}"
                 data-check-out-date="{{ $booking->check_out->format('Y-m-d') }}">
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
                <span style="font-weight: 600; color: #495057; font-size: 14px; flex: 0 0 40%;">Checked In At:</span>
                <span style="text-align: right; flex: 1;">
                  @if($booking->checked_in_at)
                    {{ $booking->checked_in_at->format('M d, Y H:i') }}
                  @else
                    <span class="text-muted">N/A</span>
                  @endif
                </span>
              </div>
              
              <div style="display: flex; justify-content: space-between; padding: 10px 0; border-bottom: 1px solid #f0f0f0;">
                <span style="font-weight: 600; color: #495057; font-size: 14px; flex: 0 0 40%;">Time Remaining:</span>
                <span style="text-align: right; flex: 1;">
                  @if($booking->check_in_status === 'checked_out')
                    <span class="badge badge-success" style="font-size: 11px;">
                      <i class="fa fa-sign-out"></i> GUEST CHECKED OUT
                    </span>
                  @elseif($checkOutDate->isPast())
                    @php
                      $daysOverdue = (int)$checkOutDate->diffInDays($now);
                      $hoursOverdue = (int)$checkOutDate->diffInHours($now);
                    @endphp
                    @if($daysOverdue >= 1)
                      <span class="badge badge-danger" title="Check-out date was {{ $daysOverdue }} day(s) ago">
                        <i class="fa fa-exclamation-triangle"></i> Overdue ({{ $daysOverdue }} day{{ $daysOverdue > 1 ? 's' : '' }})
                      </span>
                    @else
                      <span class="badge badge-danger" title="Check-out date was {{ $hoursOverdue }} hour(s) ago">
                        <i class="fa fa-exclamation-triangle"></i> Overdue ({{ $hoursOverdue }} hour{{ $hoursOverdue > 1 ? 's' : '' }})
                      </span>
                    @endif
                  @elseif($diffInDays < 1 && $diffInDays >= 0)
                    @if($diffInHours > 0)
                      <span class="badge badge-warning">
                        <i class="fa fa-clock"></i> {{ (int)$diffInHours }} hour{{ (int)$diffInHours > 1 ? 's' : '' }} remaining<br>
                        <small>Check-out at {{ $formattedTime }}</small>
                      </span>
                    @elseif($diffInMinutes > 0)
                      <span class="badge badge-warning">
                        <i class="fa fa-clock"></i> {{ (int)$diffInMinutes }} minute{{ (int)$diffInMinutes > 1 ? 's' : '' }} remaining<br>
                        <small>Check-out at {{ $formattedTime }}</small>
                      </span>
                    @else
                      <span class="badge badge-success">
                        <i class="fa fa-check-circle"></i> Check-out Time!<br>
                        <small>Due at {{ $formattedTime }}</small>
                      </span>
                    @endif
                  @elseif($diffInDays == 1)
                    <span class="badge badge-info">
                      <i class="fa fa-calendar"></i> Tomorrow
                    </span>
                  @else
                    @php
                      $weeksRemaining = floor((int)$diffInDays / 7);
                      $daysRemaining = (int)$diffInDays;
                    @endphp
                    @if($weeksRemaining > 0)
                      <span class="badge badge-primary">
                        <i class="fa fa-calendar"></i> {{ $weeksRemaining }} week{{ $weeksRemaining > 1 ? 's' : '' }} remaining
                      </span>
                    @else
                      <span class="badge badge-primary">
                        <i class="fa fa-calendar"></i> {{ $daysRemaining }} day{{ $daysRemaining > 1 ? 's' : '' }} remaining
                      </span>
                    @endif
                  @endif
                </span>
              </div>
              
              <div style="display: flex; justify-content: space-between; padding: 10px 0; border-bottom: 1px solid #f0f0f0;">
                <span style="font-weight: 600; color: #495057; font-size: 14px; flex: 0 0 40%;">Signature:</span>
                <span style="text-align: right; flex: 1;">
                  @if(!empty($booking->checkout_signature_path))
                    <span class="badge badge-info" style="font-size: 11px;"><i class="fa fa-file-text-o"></i> Signed</span>
                  @else
                    <span class="text-muted" style="font-size: 11px;">No Signature</span>
                  @endif
                </span>
              </div>
              
              <div style="display: flex; justify-content: space-between; padding: 10px 0; border-bottom: 1px solid #f0f0f0;">
                <span style="font-weight: 600; color: #495057; font-size: 14px; flex: 0 0 40%;">Total Price:</span>
                <span style="text-align: right; flex: 1;">
                  @php
                    $bookingCurrentRate = $booking->locked_exchange_rate ?? $exchangeRate;
                    $totalBillTsh = $booking->total_bill_tsh ?? ($booking->total_price * $bookingCurrentRate);
                    $totalBillUsd = $booking->total_bill_usd ?? ($totalBillTsh / $bookingCurrentRate);
                  @endphp
                  <strong>${{ number_format($totalBillUsd, 2) }}</strong><br>
                  <small>{{ number_format($totalBillTsh, 2) }} TZS</small>
                  @if(isset($booking->outstanding_balance_tsh) && $booking->outstanding_balance_tsh >= 50)
                    <br><small class="text-danger">
                      <strong>Outstanding: ${{ number_format($booking->outstanding_balance_usd ?? 0, 2) }}</strong><br>
                      <strong>{{ number_format($booking->outstanding_balance_tsh, 2) }} TZS</strong>
                    </small>
                  @elseif(isset($booking->outstanding_balance_tsh))
                    <br><small class="text-success"><i class="fa fa-check-circle"></i> All Paid</small>
                  @endif
                </span>
              </div>
              
              <div style="padding: 10px 0; border-bottom: 1px solid #f0f0f0;">
                <span style="font-weight: 600; color: #495057; font-size: 14px; display: block; margin-bottom: 5px;">Email:</span>
                <span style="font-size: 13px; color: #666;">{{ $booking->guest_email }}</span>
              </div>
              
              <div style="padding: 10px 0; border-bottom: 1px solid #f0f0f0;">
                <span style="font-weight: 600; color: #495057; font-size: 14px; display: block; margin-bottom: 5px;">Services Used:</span>
                @php
                  $usedSvs = $booking->serviceRequests->whereIn('status', ['approved', 'completed']);
                @endphp
                @forelse($usedSvs as $svc)
                  <div style="font-size: 13px; color: #444; margin-bottom: 2px;">
                    <span class="badge badge-light" style="border: 1px solid #ddd;">{{ $svc->quantity }}x</span> 
                    {{ $svc->service_specific_data['item_name'] ?? $svc->service->name }}
                  </div>
                @empty
                  <span style="font-size: 13px; color: #999;">No services consumed.</span>
                @endforelse
              </div>
              
              <div style="margin-top: 15px; padding-top: 15px; border-top: 1px solid #dee2e6; display: flex; gap: 8px; flex-wrap: wrap;">
                <a href="{{ route($checkoutBillRoute, $booking) }}" class="btn btn-sm btn-info" target="_blank" title="View Bill" style="flex: 1; min-width: calc(50% - 4px); text-align: center;">
                  <i class="fa fa-file-text"></i> Bill
                </a>
                @if(isset($booking->outstanding_balance_tsh) && $booking->outstanding_balance_tsh >= 50)
                <button class="btn btn-sm btn-success" onclick="openCashPaymentModal({{ $booking->id }}, '{{ $booking->booking_reference }}', {{ $booking->outstanding_balance_usd ?? 0 }}, {{ $booking->outstanding_balance_tsh ?? 0 }})" title="Process Payment" style="flex: 1; min-width: calc(50% - 4px);">
                  <i class="fa fa-money"></i> Pay
                </button>
                <button class="btn btn-sm btn-danger" disabled title="Cannot check out - Outstanding balance must be paid first" style="flex: 0 0 100%; margin-top: 8px;">
                  <i class="fa fa-sign-out"></i> Check Out (Disabled)
                </button>
                @elseif($booking->check_in_status === 'checked_out')
                <a href="{{ route($checkoutPaymentRoute, $booking) }}" class="btn btn-sm btn-warning" title="Process Payment" style="flex: 1; min-width: calc(50% - 4px); text-align: center;">
                  <i class="fa fa-credit-card"></i> Payment
                </a>
                @else
                @php
                  $isSigned = !empty($booking->checkout_signature_path);
                @endphp
                <button class="btn btn-sm {{ $isSigned ? 'btn-info' : 'btn-danger' }}" onclick="checkOutGuest({{ $booking->id }}, '{{ $booking->booking_reference }}')" title="{{ $isSigned ? 'Review & Check Out' : 'Check Out Guest' }}" style="flex: 1; min-width: calc(50% - 4px);">
                  <i class="fa {{ $isSigned ? 'fa-eye' : 'fa-sign-out' }}"></i> {{ $isSigned ? 'Review' : 'Check Out' }}
                </button>
                @endif
              </div>
            </div>
            @endforeach
          @endif
        </div>
        
        <div class="d-flex justify-content-center mt-3">
          {{ $bookings->links() }}
        </div>
        @else
        <div class="text-center" style="padding: 50px;">
          <i class="fa fa-sign-out fa-5x text-muted mb-3"></i>
          <h3>No Check-Outs Available</h3>
          <p class="text-muted">No guests ready for check-out at this time.</p>
        </div>
        @endif
      </div>
    </div>
  </div>
</div>

<!-- Payment Modal -->
<div class="modal fade" id="cashPaymentModal" tabindex="-1" role="dialog">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header" style="background-color: #e07632; color: white;">
        <h5 class="modal-title"><i class="fa fa-money"></i> Receive Payment</h5>
        <button type="button" class="close" data-dismiss="modal" style="color: white;">
          <span>&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <form id="cashPaymentForm">
          <input type="hidden" id="cash_booking_id" name="booking_id">
          <input type="hidden" id="cash_company_id" name="company_id" value="">
          <div class="form-group">
            <label><strong>Booking Reference:</strong></label>
            <p id="cash_booking_ref" class="form-control-plaintext"></p>
          </div>
          <div class="form-group">
            <label><strong>Outstanding Balance:</strong></label>
            <p id="cash_outstanding_balance" class="form-control-plaintext" style="color: #e07632; font-size: 18px;"></p>
          </div>
          <div class="form-group">
            <label for="cash_payment_method">Payment Method <span class="text-danger">*</span></label>
            <select class="form-control" id="cash_payment_method" name="payment_method" required onchange="handlePaymentMethodChange()">
              <option value="">Select Payment Method</option>
              <option value="cash">Cash</option>
              <option value="mobile">Mobile Money</option>
              <option value="bank">Bank Transfer</option>
              <option value="card">Card Payment</option>
            </select>
          </div>
          <div class="form-group" id="cash_payment_provider_group" style="display: none;">
            <label for="cash_payment_provider">Payment Provider <span id="cash_provider_required" class="text-danger"></span></label>
            <select class="form-control" id="cash_payment_provider" name="payment_provider">
              <option value="">Select provider...</option>
            </select>
          </div>
          <div class="form-group" id="cash_payment_reference_group" style="display: none;">
            <label for="cash_payment_reference">Transaction Reference <span id="cash_reference_required" class="text-danger"></span></label>
            <input type="text" class="form-control" id="cash_payment_reference" name="payment_reference" placeholder="Enter transaction reference">
          </div>
          <div class="form-group">
            <label for="cash_amount">Amount Received (USD) *</label>
            <input type="number" step="0.01" class="form-control" id="cash_amount" name="amount" required>
            <small class="form-text text-muted">Enter the amount received in USD</small>
          </div>
          <div id="cashPaymentAlert"></div>
        </form>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
        <button type="button" class="btn btn-success" onclick="processCashPayment()">
          <i class="fa fa-check"></i> Mark as Paid
        </button>
      </div>
    </div>
  </div>
</div>

<!-- Company Group Check-Out Modal -->
<div class="modal fade" id="companyGroupCheckOutModal" tabindex="-1" role="dialog" aria-hidden="true" style="z-index: 1050;">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content border-0 shadow-lg">
      <div class="modal-header bg-dark text-white border-0">
        <h5 class="modal-title"><i class="fa fa-building"></i> Company Group Check-Out</h5>
        <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body p-4">
        <div class="mb-4 d-flex justify-content-between align-items-start">
          <div>
            <h4 id="checkoutModalCompanyName" class="font-weight-bold mb-1">Company Name</h4>
            <p id="checkoutModalCompanyInfo" class="text-muted small mb-0"></p>
          </div>
          <div class="text-right">
            <span class="badge badge-info p-2"><i class="fa fa-users"></i> <span id="checkoutModalGuestCount">0</span> Guests</span>
          </div>
        </div>
        
        <div class="table-responsive">
          <table class="table table-hover border">
            <thead class="bg-light">
              <tr>
                <th>Guest</th>
                <th>Room</th>
                <th>Status</th>
                <th>Signature</th>
                <th class="text-right">Actions</th>
              </tr>
            </thead>
            <tbody id="companyCheckoutGuestListBody">
              <!-- Content populated via JS -->
            </tbody>
          </table>
        </div>
      </div>
      <div class="modal-footer bg-light border-0">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
        @if($role === 'manager')
          <button type="button" class="btn btn-danger" id="bulkCheckOutBtn" style="display: none;">
            <i class="fa fa-sign-out"></i> Check Out All Guests
          </button>
        @endif
      </div>
    </div>
  </div>
</div>

<!-- Enhanced Check-Out Modal (QR Bridge) -->
<div class="modal fade" id="enhancedCheckOutModal" tabindex="-1" role="dialog" aria-hidden="true" style="z-index: 1100;">
  <div class="modal-dialog" role="document">
    <div class="modal-content border-0 shadow-lg">
      <div class="modal-header" style="background: #e77a3a; color: white;">
        <h5 class="modal-title font-weight-bold"><i class="fa fa-qrcode"></i> Digital Guest Check-Out</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close" style="color: white;">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body text-center p-5">
        <div id="checkout-qr-step">
            <h5 class="font-weight-bold mb-4">Please ask the guest to scan:</h5>
            <div id="checkout-qrcode" class="d-inline-block p-4 bg-white border mb-4 rounded shadow-sm" style="min-height: 260px; min-width: 260px;">
                <!-- QR Code Appears Here -->
            </div>
            <div class="mt-2">
                <p class="text-info mb-1"><i class="fa fa-spinner fa-spin"></i> <strong>Live Connection Active</strong></p>
                <p class="text-muted small">Guest uses their own phone to review their bill and sign digital records. This screen will automatically refresh once they finish.</p>
            </div>
        </div>

        <div id="checkout-preview-step" style="display: none;">
            <h5 class="font-weight-bold mb-4 text-success"><i class="fa fa-check-circle"></i> Signature Received!</h5>
            <div class="p-4 bg-white border rounded shadow-sm mb-4" style="min-height: 200px; display: flex; align-items: center; justify-content: center;">
                <img id="checkout-sig-preview" src="" style="max-width: 100%; max-height: 180px;" alt="Signature Preview">
            </div>
            <p class="text-muted small">The guest has signed the checkout records. Please verify the signature above before finalizing.</p>
        </div>
      </div>
      <div class="modal-footer bg-light py-2 d-flex justify-content-between align-items-center">
        <small class="text-muted"><i class="fa fa-lock"></i> Secure Check-Out Bridge</small>
        <div id="checkout-footer-actions"></div>
      </div>
    </div>
  </div>
</div>


@endsection

@section('scripts')
<script src="{{ asset('dashboard_assets/js/plugins/sweetalert.min.js') }}"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>
<style>
/* Mobile Responsive Styles */
@media (max-width: 767px) {
  /* Filters */
  .row .col-md-4,
  .row .col-md-3,
  .row .col-md-2 {
    margin-bottom: 10px;
  }
  
  /* Table - Hide on Mobile */
  #checkOutTable {
    display: none;
  }
  
  /* Mobile Cards - Show on Mobile */
  .mobile-checkout-cards {
    display: block;
  }
  
  /* Modal - Mobile */
  .modal-dialog {
    margin: 10px;
    max-width: calc(100% - 20px);
  }
  
  .modal-body .form-group {
    margin-bottom: 15px;
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
  
  /* Pagination */
  .pagination {
    justify-content: center;
    flex-wrap: wrap;
  }
  
  .pagination .page-link {
    padding: 8px 12px;
    font-size: 14px;
  }
}

/* Desktop - Hide mobile cards */
@media (min-width: 768px) {
  .mobile-checkout-cards {
    display: none;
  }
  
  #checkOutTable {
    display: table;
  }
}

/* Very Small Screens */
@media (max-width: 480px) {
  .mobile-checkout-card {
    padding: 12px !important;
  }
  
  .mobile-checkout-card h5 {
    font-size: 16px !important;
  }
  
  .mobile-checkout-card .btn {
    flex: 0 0 100% !important;
    min-width: 100% !important;
    margin-bottom: 8px;
  }
  
  .mobile-checkout-card .btn:last-child {
    margin-bottom: 0;
  }
}
</style>
<script>
const currentRole = '{{ $role }}';
const isReception = currentRole === 'reception';
let currentBookingId = null;
let currentBookingRef = null;
let pollingInterval = null;

// Stay Modification Data
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
        companyId: null,
        roomId: {{ $booking->room_id }},
        roomPrice: {{ $booking->room->price_per_night ?? 0 }},
        currentCheckOut: '{{ $booking->check_out->format('Y-m-d') }}',
        checkIn: '{{ $booking->check_in->format('Y-m-d') }}'
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
  $('#managerExtensionModal .alert-info').text('The booking stay will be extended and the price will be adjusted automatically.');

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
  $('#managerDecreaseModal .alert-warning').html('<i class="fa fa-exclamation-triangle"></i> <strong>Note:</strong> This will decrease the stay for ALL guests in this group who are currently checked in.');
  
  $('#managerDecreaseModal').modal('show');
}

$('#managerExtensionModal').on('shown.bs.modal', function() {
  const dateInputEl = document.getElementById('manager_extension_new_check_out');
  $(dateInputEl).off('change input').on('change input', calculateManagerExtensionCost);
});

$('#managerDecreaseModal').on('shown.bs.modal', function() {
  const dateInputEl = document.getElementById('manager_decrease_new_check_out');
  $(dateInputEl).off('change input');
});

function calculateManagerExtensionCost() {
  const bookingId = document.getElementById('manager_extension_booking_id').value;
  const companyId = document.getElementById('manager_extension_company_id').value;
  const isGroup = !!companyId;
  const alertDiv = document.getElementById('managerExtensionAlert');
  alertDiv.innerHTML = ''; // Clear previous alerts

  let bookingData = null;
  let currentCheckOutDate = null;
  let totalDailyRate = 0;

  if (isGroup) {
    // For group, sum all checked-in bookings for this company
    for (const id in managerBookingData) {
      if (managerBookingData[id].companyId == companyId) {
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
      const previewP = document.querySelector('#managerExtensionCostPreview p');
      if (previewP) {
        previewP.innerHTML = 
          `<span id="managerExtensionNights">${diffDays}</span> night(s) extension × 
          $<span id="managerExtensionRoomPrice">${totalDailyRate.toFixed(2)}</span> (Group Total Rate) = 
          <strong>Additional Cost: $<span id="managerExtensionTotalCost">${totalCost.toFixed(2)}</span></strong>`;
      }
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

function startPollingCheckoutStatus(bookingId) {
    if (pollingInterval) clearInterval(pollingInterval);

    const doPoll = async () => {
        try {
            const response = await fetch(`/check-in/status/${bookingId}`);
            const data = await response.json();

            console.log('[Checkout Poll]', bookingId, data);

            if (data.is_checkout_submitted && !data.is_checked_out) {
                // Signature submitted — switch to preview immediately
                clearInterval(pollingInterval);
                pollingInterval = null;

                const sigUrl = data.checkout_signature_path || data.signature_path;

                // Switch modal panels
                document.getElementById('checkout-qr-step').style.display = 'none';
                document.getElementById('checkout-preview-step').style.display = 'block';

                const sigImg = document.getElementById('checkout-sig-preview');
                if (sigImg && sigUrl) {
                    sigImg.src = sigUrl + '?t=' + Date.now(); // cache-bust
                }

                // Show action buttons
                document.getElementById('checkout-footer-actions').innerHTML = `
                    <button class="btn btn-sm btn-outline-danger mr-2" onclick="clearCheckoutSignature(${bookingId})">
                        <i class="fa fa-undo"></i> Clear &amp; Retry
                    </button>
                    <button class="btn btn-sm btn-success" onclick="finalizeCheckOut(${bookingId})">
                        <i class="fa fa-check"></i> Finalize Check-Out
                    </button>
                `;

            } else if (data.is_checked_out) {
                // Already fully checked out
                clearInterval(pollingInterval);
                pollingInterval = null;
                $('#enhancedCheckOutModal').modal('hide');
                location.reload();
            }
        } catch (err) {
            console.error('[Checkout Poll] Error:', err);
        }
    };

    // Immediate first check then every 2 seconds
    doPoll();
    pollingInterval = setInterval(doPoll, 2000);
}

async function finalizeCheckOut(bookingId) {
    try {
        const response = await fetch('{{ route($updateCheckInRoute, ":id") }}'.replace(':id', bookingId), {
            method: 'PUT',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json'
            },
            body: JSON.stringify({
                check_in_status: 'checked_out'
            })
        });
        
        const data = await response.json();
        
        if (data.success) {
            swal({
                title: "Checked Out!",
                text: data.message || "Guest checked out successfully.",
                type: "success"
            }, function() {
                location.reload();
            });
        } else {
            swal("Error", data.message || "Failed to finalize checkout.", "error");
        }
    } catch (err) {
        console.error("Finalize error:", err);
        swal("Error", "Connection error. Please try again.", "error");
    }
}

function checkOutGuest(bookingId, bookingReference) {
    currentBookingId = bookingId;
    currentBookingRef = bookingReference;
    
    // Reset Modal UI to QR step
    document.getElementById('checkout-qr-step').style.display = 'block';
    document.getElementById('checkout-preview-step').style.display = 'none';
    document.getElementById('checkout-footer-actions').innerHTML = '';
    
    // Show QR Modal
    $('#enhancedCheckOutModal').modal('show');

    // Generate QR
    setTimeout(() => {
        triggerMobileBridgeCheckoutAuto();
        startPollingCheckoutStatus(bookingId);
    }, 300);
}

async function triggerMobileBridgeCheckoutAuto() {
    if (!currentBookingId) return;
    
    const qrContainer = document.getElementById('checkout-qrcode');
    qrContainer.innerHTML = '<div class="p-5 text-center"><i class="fa fa-spinner fa-spin fa-2x text-primary"></i><br>Generating QR Code...</div>';
    
    @php
        $tokenRoute = ($role === 'manager') ? 'admin.bookings.generate-checkout-token' : 'reception.bookings.generate-checkout-token';
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
            qrContainer.innerHTML = '';
            new QRCode(qrContainer, {
                text: data.url,
                width: 260,
                height: 260,
                colorDark : "#000000",
                colorLight : "#ffffff",
                correctLevel : QRCode.CorrectLevel.H
            });
        } else {
            qrContainer.innerHTML = '<div class="text-danger p-4">Error generating QR. Please try again.</div>';
        }
    } catch (err) {
        console.error("QR Error:", err);
        qrContainer.innerHTML = '<div class="text-danger p-4">Connection error.</div>';
    }
}

// Stop polling when modal is closed
$(document).ready(function() {
    $('#enhancedCheckOutModal').on('hidden.bs.modal', function () {
        if (pollingInterval) clearInterval(pollingInterval);
    });
});

async function clearCheckoutSignature(bookingId) {
    try {
        const response = await fetch(`/check-in/status/${bookingId}/clear`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            }
        });
        
        const data = await response.json();
        
        if (data.success) {
            // Reset modal UI
            document.getElementById('checkout-qr-step').style.display = 'block';
            document.getElementById('checkout-preview-step').style.display = 'none';
            document.getElementById('checkout-sig-preview').src = '';
            document.getElementById('checkout-footer-actions').innerHTML = '';
            
            // Re-generate QR and start polling again
            triggerMobileBridgeCheckoutAuto();
            startPollingCheckoutStatus(bookingId);
        } else {
            swal("Error", data.message || "Failed to clear signature.", "error");
        }
    } catch (err) {
        console.error("Clear error:", err);
        swal("Error", "Connection error. Please try again.", "error");
    }
}

function handlePaymentMethodChange() {
    const paymentMethod = document.getElementById('cash_payment_method').value;
    const providerGroup = document.getElementById('cash_payment_provider_group');
    const referenceGroup = document.getElementById('cash_payment_reference_group');
    const providerSelect = document.getElementById('cash_payment_provider');
    const referenceInput = document.getElementById('cash_payment_reference');
    const providerRequired = document.getElementById('cash_provider_required');
    const referenceRequired = document.getElementById('cash_reference_required');
    
    // Reset
    providerSelect.innerHTML = '<option value="">Select provider...</option>';
    providerSelect.required = false;
    referenceInput.required = false;
    providerRequired.textContent = '';
    referenceRequired.textContent = '';
    referenceInput.value = '';
    
    if (paymentMethod === 'mobile') {
        providerGroup.style.display = 'block';
        referenceGroup.style.display = 'block';
        providerSelect.innerHTML = `
            <option value="">Select provider...</option>
            <option value="m-pesa">M-PESA</option>
            <option value="halopesa">HALOPESA</option>
            <option value="mixx-by-yas">MIXX BY YAS</option>
            <option value="airtel-money">AIRTEL MONEY</option>
            <option value="tigo-pesa">TIGO PESA</option>
        `;
        providerSelect.required = true;
        referenceInput.required = true;
        providerRequired.textContent = '*';
        referenceRequired.textContent = '*';
        referenceInput.placeholder = 'Enter transaction reference (Required)';
    } else if (paymentMethod === 'bank') {
        providerGroup.style.display = 'block';
        referenceGroup.style.display = 'block';
        providerSelect.innerHTML = `
            <option value="">Select provider...</option>
            <option value="nmb">NMB</option>
            <option value="crdb">CRDB</option>
            <option value="kcb">KCB Bank</option>
            <option value="exim">Exim Bank</option>
            <option value="equity">Equity Bank</option>
            <option value="stanbic">Stanbic Bank</option>
            <option value="barclays">Barclays Bank</option>
            <option value="diamond">Diamond Trust Bank</option>
        `;
        providerSelect.required = true;
        referenceInput.required = true;
        providerRequired.textContent = '*';
        referenceRequired.textContent = '*';
        referenceInput.placeholder = 'Enter transaction reference (Required)';
    } else if (paymentMethod === 'card') {
        providerGroup.style.display = 'block';
        referenceGroup.style.display = 'block';
        providerSelect.innerHTML = `
            <option value="">Select provider...</option>
            <option value="visa">Visa</option>
            <option value="mastercard">Mastercard</option>
            <option value="amex">American Express</option>
        `;
        providerSelect.required = true;
        referenceInput.required = true;
        providerRequired.textContent = '*';
        referenceRequired.textContent = '*';
        referenceInput.placeholder = 'Enter card transaction reference (Required)';
    } else {
        // Cash or other
        providerGroup.style.display = 'none';
        referenceGroup.style.display = 'none';
    }
}

function openCashPaymentModal(bookingId, bookingRef, outstandingUsd, outstandingTsh) {
    document.getElementById('cash_booking_id').value = bookingId;
    document.getElementById('cash_company_id').value = ''; // Clear company ID for individual payments
    document.getElementById('cash_booking_ref').textContent = bookingRef;
    document.getElementById('cash_outstanding_balance').innerHTML = 
        '<strong>$' + parseFloat(outstandingUsd).toFixed(2) + '</strong> (' + 
        parseFloat(outstandingTsh).toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2}) + ' TZS)';
    document.getElementById('cash_amount').value = parseFloat(outstandingUsd).toFixed(2);
    document.getElementById('cash_payment_method').value = '';
    document.getElementById('cash_payment_provider').value = '';
    document.getElementById('cash_payment_reference').value = '';
    document.getElementById('cash_payment_provider_group').style.display = 'none';
    document.getElementById('cash_payment_reference_group').style.display = 'none';
    document.getElementById('cashPaymentAlert').innerHTML = '';
    $('#cashPaymentModal').modal('show');
}

function processCashPayment() {
    const bookingId = document.getElementById('cash_booking_id').value;
    const companyId = document.getElementById('cash_company_id').value;
    const paymentMethod = document.getElementById('cash_payment_method').value;
    const paymentProvider = document.getElementById('cash_payment_provider').value;
    const paymentReference = document.getElementById('cash_payment_reference').value;
    const amount = parseFloat(document.getElementById('cash_amount').value);
    const alertDiv = document.getElementById('cashPaymentAlert');
    
    if (!paymentMethod) {
        alertDiv.innerHTML = '<div class="alert alert-danger">Please select a payment method.</div>';
        return;
    }
    
    if (!amount || amount <= 0) {
        alertDiv.innerHTML = '<div class="alert alert-danger">Please enter a valid amount.</div>';
        return;
    }
    
    // Validate provider and reference for mobile/bank/card payments
    if ((paymentMethod === 'mobile' || paymentMethod === 'bank' || paymentMethod === 'card')) {
        if (!paymentProvider) {
            alertDiv.innerHTML = '<div class="alert alert-danger">Please select a payment provider.</div>';
            return;
        }
        if (!paymentReference || paymentReference.trim() === '') {
            alertDiv.innerHTML = '<div class="alert alert-danger">Please enter a transaction reference.</div>';
            return;
        }
    }
    
    alertDiv.innerHTML = '<div class="alert alert-info"><i class="fa fa-spinner fa-spin"></i> Processing payment...</div>';
    
    // Determine if this is a company payment or individual payment
    let paymentUrl;
    if (companyId && companyId !== '') {
        @php
          $currentRoute = request()->route()->getName() ?? '';
          $checkoutCompanyPaymentRoute = (str_starts_with($currentRoute, 'admin.')) 
            ? 'admin.bookings.checkout-company-payment' 
            : 'reception.bookings.checkout-company-payment';
        @endphp
        paymentUrl = '{{ route($checkoutCompanyPaymentRoute, ":id") }}'.replace(':id', companyId);
    } else {
        paymentUrl = '{{ route($checkoutPaymentCashRoute, ":id") }}'.replace(':id', bookingId);
    }
    
    const paymentData = {
        amount: amount,
        payment_method: paymentMethod
    };
    
    if (paymentProvider) {
        paymentData.payment_provider = paymentProvider;
    }
    if (paymentReference) {
        paymentData.payment_reference = paymentReference;
    }
    
    fetch(paymentUrl, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Accept': 'application/json'
        },
        body: JSON.stringify(paymentData)
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            $('#cashPaymentModal').modal('hide');
            swal({
                title: "Success!",
                text: data.message || "Payment received successfully!",
                type: "success",
                confirmButtonColor: "#28a745"
            }, function() {
                // Refresh the page to recalculate outstanding balances with new threshold
                location.reload();
            });
        } else {
            alertDiv.innerHTML = '<div class="alert alert-danger">' + (data.message || 'Payment failed. Please try again.') + '</div>';
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alertDiv.innerHTML = '<div class="alert alert-danger">An error occurred. Please try again.</div>';
    });
}

function updateBookingAfterPayment(bookingId) {
    // Find the row for this booking using the data-booking-id attribute
    const row = document.querySelector(`tr[data-booking-id="${bookingId}"]`);
    
    if (row) {
        // Update the price cell to show "All Paid"
        const priceCell = row.querySelector('td:nth-child(7)'); // 7th column is Total Price
        if (priceCell) {
            const existingContent = priceCell.innerHTML;
            // Remove outstanding balance if present (handle both text-danger and any outstanding text)
            let newContent = existingContent.replace(
                /<br><small class="text-danger">[\s\S]*?<\/small>/g,
                ''
            );
            // Add "All Paid" if not already present
            if (!newContent.includes('All Paid')) {
                newContent += '<br><small class="text-success"><i class="fa fa-check-circle"></i> All Paid</small>';
            }
            priceCell.innerHTML = newContent;
        }
        
        // Update the actions cell
        const actionsCell = row.querySelector('td:last-child');
        if (actionsCell) {
            // Get booking reference from the row
            const bookingRef = row.querySelector('td:first-child strong').textContent.trim();
            
            // Replace Pay Cash button and disabled checkout with active checkout button
            @php
              $currentRoute = request()->route()->getName() ?? '';
              $checkoutBillRoute = (str_starts_with($currentRoute, 'admin.')) 
                ? 'admin.bookings.checkout-bill' 
                : 'reception.bookings.checkout-bill';
            @endphp
            const billUrl = '{{ route($checkoutBillRoute, ":id") }}'.replace(':id', bookingId);
            
            let actionBtnHtml = '';
            // For corporate bookings in dashboard expansion, we don't show individual checkout buttons anymore
            const isInCorporateExpansion = !!row.closest('.company-bookings-detail');
            
            if (isInCorporateExpansion) {
                actionBtnHtml = '<span class="text-muted small">Group Action</span>';
            } else {
                actionBtnHtml = `
                    <button class="btn btn-sm btn-danger" onclick="checkOutGuest(${bookingId}, '${bookingRef}')" title="Check Out Guest">
                        <i class="fa fa-sign-out"></i> Check Out
                    </button>
                `;
            }

            actionsCell.innerHTML = `
                <a href="${billUrl}" class="btn btn-sm btn-info mr-1" target="_blank" title="View Bill">
                    <i class="fa fa-file-text"></i> View Bill
                </a>
                ${actionBtnHtml}
            `;
        }
    } else {
        // If row not found, reload the page
        location.reload();
    }
}

// This function is now superseded by the QR bridge flow, but kept for reference or direct usage if needed
function originalCheckOutGuest(bookingId, bookingReference) {
    swal({
        title: "Check Out Guest?",
        text: "Are you sure you want to check out booking " + bookingReference + "?",
        type: "warning",
        showCancelButton: true,
        confirmButtonColor: "#d33",
        cancelButtonColor: "#6c757d",
        confirmButtonText: "Yes, Check Out!",
        cancelButtonText: "Cancel",
        closeOnConfirm: false,
        showLoaderOnConfirm: true
    }, function(isConfirm) {
        if (isConfirm) {
            fetch('{{ route($updateCheckInRoute, ":id") }}'.replace(':id', bookingId), {
                method: 'PUT',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json'
                },
                body: JSON.stringify({
                    check_in_status: 'checked_out'
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    swal({
                        title: "Success!",
                        text: data.message || "Guest checked out successfully!",
                        type: "success",
                        confirmButtonColor: "#28a745"
                    }, function() {
                        if (data.redirect) {
                            window.location.href = data.redirect;
                        } else {
                            location.reload();
                        }
                    });
                } else {
                    swal({
                        title: "Error!",
                        text: data.message || "Failed to check out. Please try again.",
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

function filterCheckouts() {
  const searchInput = document.getElementById('searchInput').value.toLowerCase();
  const statusFilter = document.getElementById('statusFilter').value;
  const checkOutDateFilter = document.getElementById('checkOutDateFilter').value;
  
  // Filter both table rows and mobile cards
  const rows = document.querySelectorAll('.checkout-row');
  rows.forEach(row => {
    const bookingRef = row.getAttribute('data-booking-ref');
    const guestName = row.getAttribute('data-guest-name');
    const guestEmail = row.getAttribute('data-guest-email');
    const checkInStatus = row.getAttribute('data-check-in-status');
    const checkOutDate = row.getAttribute('data-check-out-date');
    
    let show = true;
    
    // Status filter
    if (statusFilter !== 'all' && checkInStatus !== statusFilter) {
      show = false;
    }
    
    // Check-out date filter - only apply if date is selected
    if (checkOutDateFilter && checkOutDateFilter.trim() !== '') {
      if (checkOutDate > checkOutDateFilter) {
        show = false;
      }
    }
    
    // Search filter
    if (searchInput) {
      if (!bookingRef.includes(searchInput) && 
          !guestName.includes(searchInput) && 
          !guestEmail.includes(searchInput)) {
        show = false;
      }
    }
    
    row.style.display = show ? '' : 'none';
  });
}

function resetCheckoutFilters() {
  // Clear all filters
  document.getElementById('searchInput').value = '';
  document.getElementById('statusFilter').value = 'all';
  document.getElementById('checkOutDateFilter').value = '';
  
  // Show all rows first
  const rows = document.querySelectorAll('.checkout-row');
  rows.forEach(row => {
    row.style.display = '';
  });
  
  // Then apply filters (which will show all since filters are cleared)
  filterCheckouts();
}

function viewCompanyBookings(companyId, index) {
    const detailRow = document.getElementById(`company-bookings-${companyId}-${index}`);
    if (detailRow) {
        if (detailRow.style.display === 'none') {
            detailRow.style.display = '';
        } else {
            detailRow.style.display = 'none';
        }
    }
}

function viewCompanyBookingsMobile(companyId, index) {
    // Same function for mobile
    viewCompanyBookings(companyId, index);
}

function openCashPaymentModalCompany(companyId, companyName, outstandingUsd, outstandingTsh) {
    // Store company info for payment processing
    document.getElementById('cash_booking_id').value = '';
    document.getElementById('cash_company_id').value = companyId;
    document.getElementById('cash_booking_ref').textContent = companyName + ' (All Guests)';
    document.getElementById('cash_outstanding_balance').innerHTML = 
        '<strong>$' + parseFloat(outstandingUsd).toFixed(2) + '</strong> (' + 
        parseFloat(outstandingTsh).toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2}) + ' TZS)';
    document.getElementById('cash_amount').value = parseFloat(outstandingUsd).toFixed(2);
    document.getElementById('cash_payment_method').value = '';
    document.getElementById('cash_payment_provider').value = '';
    document.getElementById('cash_payment_reference').value = '';
    document.getElementById('cash_payment_provider_group').style.display = 'none';
    document.getElementById('cash_payment_reference_group').style.display = 'none';
    document.getElementById('cashPaymentAlert').innerHTML = '';
    $('#cashPaymentModal').modal('show');
}

function checkOutCompanyGroup(companyId, companyName) {
    console.log('checkOutCompanyGroup called with companyId:', companyId);
    
    if (!companyId || companyId === 0) {
        swal({ title: "Error!", text: "Invalid company ID", type: "error" });
        return;
    }

    // Show Modal
    $('#companyGroupCheckOutModal').modal('show');
    document.getElementById('checkoutModalCompanyName').textContent = companyName;
    
    const listBody = document.getElementById('companyCheckoutGuestListBody');
    listBody.innerHTML = '<tr><td colspan="5" class="text-center p-4"><i class="fa fa-spinner fa-spin fa-2x"></i><br>Fetching guest list...</td></tr>';

    // Fetch Bookings
    const companyBookingsUrl = '{{ route("admin.bookings.company", ":id") }}'.replace(':id', companyId);
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
        
        document.getElementById('checkoutModalCompanyName').textContent = company.name;
        document.getElementById('checkoutModalCompanyInfo').textContent = `${company.email} | ${company.phone || 'No phone'}`;
        document.getElementById('checkoutModalGuestCount').textContent = bookings.length;
        
        listBody.innerHTML = '';
        
        if (bookings.length === 0) {
            listBody.innerHTML = '<tr><td colspan="5" class="text-center text-muted p-4">No bookings found for this company.</td></tr>';
            return;
        }

        // Handle Bulk Button display for Manager
        if (currentRole === 'manager') {
            const hasPending = bookings.some(b => b.check_in_status === 'checked_in');
            const bulkBtn = document.getElementById('bulkCheckOutBtn');
            if (bulkBtn) {
                bulkBtn.style.display = hasPending ? 'inline-block' : 'none';
                bulkBtn.onclick = () => bulkCheckOutCompanyGuests(companyId);
            }
        }

        bookings.forEach(booking => {
            const isCheckedOut = booking.check_in_status === 'checked_out';
            const isSigned = !!booking.checkout_signature_path;
            
            let statusBadge = '';
            if (isCheckedOut) {
                statusBadge = '<span class="badge badge-secondary"><i class="fa fa-check"></i> Checked Out</span>';
            } else if (booking.check_in_status === 'checked_in') {
                statusBadge = '<span class="badge badge-success"><i class="fa fa-user"></i> In House</span>';
            } else {
                statusBadge = `<span class="badge badge-warning">${booking.check_in_status || 'Pending'}</span>`;
            }

            let sigBadge = isSigned 
                ? '<span class="badge badge-info"><i class="fa fa-file-text-o"></i> Signed</span>' 
                : '<span class="text-muted small">No Sign</span>';

            let actionBtn = '';
            if (isCheckedOut) {
                actionBtn = '<span class="text-muted small"><i class="fa fa-check"></i> Complete</span>';
            } else {
                const btnClass = isSigned ? 'btn-info' : 'btn-danger';
                const btnIcon = isSigned ? 'fa-eye' : 'fa-sign-out';
                const btnText = isSigned ? 'Review' : 'Check Out';
                
                const canIndividualCheckOut = true; // All roles can trigger individual scan checkout
                
                if (canIndividualCheckOut) {
                    actionBtn = `
                        <button class="btn btn-xs ${btnClass}" onclick="checkOutGuest(${booking.id}, '${booking.booking_reference}')">
                            <i class="fa ${btnIcon}"></i> ${btnText}
                        </button>
                    `;
                }
            }

            const row = document.createElement('tr');
            row.innerHTML = `
                <td>
                    <strong>${booking.guest_name}</strong><br>
                    <small class="text-muted">${booking.booking_reference}</small>
                </td>
                <td>
                    <span class="badge badge-primary">${booking.room.room_type || 'N/A'}</span><br>
                    <small>${booking.room.room_number || 'N/A'}</small>
                </td>
                <td>${statusBadge}</td>
                <td>${sigBadge}</td>
                <td class="text-right">${actionBtn}</td>
            `;
            listBody.appendChild(row);
        });
    })
    .catch(err => {
        console.error("Fetch error:", err);
        listBody.innerHTML = `<tr><td colspan="5" class="text-center text-danger p-4"><i class="fa fa-exclamation-triangle"></i> Error: ${err.message}</td></tr>`;
    });
}

async function bulkCheckOutCompanyGuests(companyId) {
    const companyName = document.getElementById('checkoutModalCompanyName').textContent;
    
    swal({
        title: "Check Out All Guests?",
        text: "Are you sure you want to check out all remaining guests for " + companyName + "?",
        type: "warning",
        showCancelButton: true,
        confirmButtonColor: "#d33",
        confirmButtonText: "Yes, Check Out All!",
        closeOnConfirm: false,
        showLoaderOnConfirm: true
    }, async function(isConfirm) {
        if (!isConfirm) return;
        
        try {
            @php
              $currentRoute = request()->route()->getName() ?? '';
              $checkoutCompanyRoute = (str_starts_with($currentRoute, 'admin.')) 
                ? 'admin.bookings.checkout-company-group' 
                : 'reception.bookings.checkout-company-group';
            @endphp
            
            const response = await fetch('{{ route($checkoutCompanyRoute, ":id") }}'.replace(':id', companyId), {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json'
                }
            });
            
            const data = await response.json();
            
            if (data.success) {
                swal({
                    title: "Success!",
                    text: data.message || "All guests checked out successfully.",
                    type: "success"
                }, function() {
                    $('#companyGroupCheckOutModal').modal('hide');
                    location.reload();
                });
            } else {
                swal("Error", data.error || data.message || "Bulk check-out failed.", "error");
            }
        } catch (err) {
            console.error("Bulk error:", err);
            swal("Error", "An unexpected error occurred. Please try again.", "error");
        }
    });
}
</script>
@endsection

