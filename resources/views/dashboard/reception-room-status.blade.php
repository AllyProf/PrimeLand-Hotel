@extends('dashboard.layouts.app')

@section('content')
<style>
    /* Status Card Styles */
    .room-card-status {
        transition: transform 0.2s, box-shadow 0.2s;
        border: none !important;
        color: #fff !important;
    }
    .room-card-status:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 20px rgba(0,0,0,0.15) !important;
    }
    
    .room-card-status .card-body {
        padding: 1.25rem !important;
    }
    
    .room-card-status .text-muted {
        color: rgba(255,255,255,0.8) !important;
    }
    
    .room-card-status .text-dark {
        color: #fff !important;
    }
    
    .status-bg-available { background-color: #28a745 !important; }
    .status-bg-occupied { background-color: #dc3545 !important; }
    .status-bg-reserved { background-color: #007bff !important; }
    .status-bg-cleaning { background-color: #ffc107 !important; color: #333 !important; }
    .status-bg-maintenance { background-color: #6c757d !important; }
    .status-bg-closed { background-color: #343a40 !important; }
    
    .room-card-status.status-bg-cleaning .text-muted,
    .room-card-status.status-bg-cleaning .text-dark,
    .room-card-status.status-bg-cleaning .small {
        color: #333 !important;
    }
    
    .room-card-status .btn-outline-primary, 
    .room-card-status .btn-outline-danger,
    .room-card-status .btn-outline-success,
    .room-card-status .btn-outline-info {
        background: rgba(255,255,255,0.2);
        border-color: rgba(255,255,255,0.5);
        color: #fff !important;
    }
    
    .room-card-status .btn-outline-primary:hover, 
    .room-card-status .btn-outline-danger:hover,
    .room-card-status .btn-outline-success:hover,
    .room-card-status .btn-outline-info:hover {
        background: rgba(255,255,255,0.4);
        border-color: #fff;
    }
    
    .room-card-status.status-bg-cleaning .btn-outline-secondary {
        border-color: #333;
        color: #333 !important;
    }
</style>

<div class="app-title">
  <div>
    <h1><i class="fa fa-th"></i> Room Status & Occupancy</h1>
    <p>Real-time view of room availability and status</p>
  </div>
  <ul class="app-breadcrumb breadcrumb">
    <li class="breadcrumb-item"><i class="fa fa-home fa-lg"></i></li>
    <li class="breadcrumb-item"><a href="{{ $role === 'manager' ? route('admin.dashboard') : route('reception.dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item"><a href="#">Room Status</a></li>
  </ul>
</div>

<!-- Modals moved to top for better z-index handling -->
<div class="modal fade" id="quickInfoModal" tabindex="-1" role="dialog" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered" role="document">
    <div class="modal-content">
      <div class="modal-header bg-primary text-white">
        <h5 class="modal-title" id="modalRoomTitle">Room Details</h5>
        <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body p-0">
         <table class="table table-striped mb-0">
             <tbody>
                 <tr>
                     <th width="40%" class="text-right pr-4">Room Type:</th>
                     <td width="60%" id="modalRoomType">-</td>
                 </tr>
                 <tr>
                     <th class="text-right pr-4">Status:</th>
                     <td id="modalStatus">-</td>
                 </tr>
                 <tr id="rowStatusUntil" style="display:none">
                     <th class="text-right pr-4">Manual Status Until:</th>
                     <td id="modalStatusUntil" class="text-primary font-weight-bold">-</td>
                 </tr>
                 <tr>
                     <th class="text-right pr-4">Price / Night:</th>
                     <td id="modalPrice">-</td>
                 </tr>
                 <tr>
                     <th class="text-right pr-4">Capacity:</th>
                     <td id="modalCapacity">-</td>
                 </tr>
                 <tr id="rowGuest" style="display:none">
                     <th class="text-right pr-4">Current Guest:</th>
                     <td id="modalGuest" class="font-weight-bold">-</td>
                 </tr>
                 <tr id="rowCheckin" style="display:none">
                     <th class="text-right pr-4">Check-In:</th>
                     <td id="modalCheckin">-</td>
                 </tr>
                 <tr id="rowCheckout" style="display:none">
                     <th class="text-right pr-4">Check-Out:</th>
                     <td id="modalCheckout">-</td>
                 </tr>
                 <tr id="rowPayment" style="display:none">
                     <th class="text-right pr-4">Payment:</th>
                     <td>
                         <span class="badge badge-info" id="modalPayment"></span>
                     </td>
                 </tr>
             </tbody>
         </table>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">Close</button>
        <a href="#" id="modalViewBookingBtn" class="btn btn-primary btn-sm" style="display:none">Full Details</a>
      </div>
    </div>
  </div>
</div>

<div class="modal fade" id="statusUpdateModal" tabindex="-1" role="dialog" aria-hidden="true" data-backdrop="static">
  <div class="modal-dialog modal-dialog-centered" role="document">
    <div class="modal-content">
      <div class="modal-header bg-dark text-white">
        <h5 class="modal-title" id="statusModalTitle">Update Room Status</h5>
        <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <form id="statusUpdateForm">
        @csrf
        <div class="modal-body">
            <div class="form-group">
                <label for="newStatus">Room Status</label>
                <select class="form-control" id="newStatus" name="status" required>
                    <option value="available">Available</option>
                    <option value="to_be_cleaned">Needs Cleaning</option>
                    <option value="maintenance">Maintenance</option>
                    <option value="occupied">Occupied (Manual)</option>
                    <option value="reserved">Reserved (Manual)</option>
                    <option value="closed">Closed / Out of Service</option>
                </select>
            </div>
            <div id="untilGroup" class="form-group" style="display:none">
                <label for="statusUntil">Available Again At (Optional)</label>
                <input type="datetime-local" class="form-control" id="statusUntil" name="status_until">
                <small class="form-text text-muted">Leave empty if status is indefinite (manual reset required).</small>
            </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
          <button type="submit" class="btn btn-primary" id="saveStatusBtn">Save Changes</button>
        </div>
      </form>
    </div>
  </div>
</div>


<!-- Statistics Cards -->
<div class="row mb-4">
  <div class="col-md-3 col-lg-3">
    <div class="widget-small success coloured-icon shadow-sm">
      <i class="icon fa fa-check-circle fa-3x"></i>
      <div class="info">
        <h4>Available</h4>
        <p><b>{{ $stats['available'] ?? 0 }}</b></p>
      </div>
    </div>
  </div>
  <div class="col-md-3 col-lg-3">
    <div class="widget-small danger coloured-icon shadow-sm">
      <i class="icon fa fa-user fa-3x"></i>
      <div class="info">
        <h4>Occupied</h4>
        <p><b>{{ $stats['occupied'] ?? 0 }}</b></p>
      </div>
    </div>
  </div>
  <div class="col-md-3 col-lg-3">
    <div class="widget-small info coloured-icon shadow-sm">
      <i class="icon fa fa-calendar-check-o fa-3x"></i>
      <div class="info">
        <h4>Reserved</h4>
        <p><b>{{ $stats['reserved'] ?? 0 }}</b></p>
      </div>
    </div>
  </div>
  <div class="col-md-3 col-lg-3">
    <div class="widget-small warning coloured-icon shadow-sm">
      <i class="icon fa fa-broom fa-3x"></i>
      <div class="info">
        <h4>Cleaning</h4>
        <p><b>{{ $stats['needs_cleaning'] ?? 0 }}</b></p>
      </div>
    </div>
  </div>
</div>

<div class="row">
    <div class="col-md-12">
        <div class="tile">
            <!-- Filter Controls -->
            <div class="row mb-4">
                <div class="col-md-3">
                    <select class="form-control" id="statusFilter">
                        <option value="all">All Statuses</option>
                        <option value="available">Available</option>
                        <option value="occupied">Occupied</option>
                        <option value="reserved">Reserved (Today)</option>
                        <option value="dirty">Needs Cleaning</option>
                        <option value="maintenance">Maintenance</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <select class="form-control" id="typeFilter">
                        <option value="all">All Room Types</option>
                        <option value="Single">Single</option>
                        <option value="Double">Double</option>
                        <option value="Twins">Twins</option>
                    </select>
                </div>
                <div class="col-md-4">
                    <input type="text" class="form-control" id="searchRoom" placeholder="Search Room Number...">
                </div>
            </div>

            <div class="row" id="roomsGrid">
                @foreach($rooms as $room)
                    @php
                        // Determine card class and status text based on logic
                        $bgClass = 'status-bg-available';
                        $statusBadge = 'badge-success';
                        $statusText = 'Available';
                        $filterStatus = 'available';
                        $statusIcon = 'fa-check-circle';

                        if ($room->status === 'closed') {
                            $bgClass = 'status-bg-closed';
                            $statusBadge = 'badge-dark';
                            $statusText = 'Closed';
                            $filterStatus = 'all';
                            $statusIcon = 'fa-ban';
                        } elseif ($room->status === 'maintenance') {
                            $bgClass = 'status-bg-maintenance';
                            $statusBadge = 'badge-danger';
                            $statusText = 'Maintenance';
                            $filterStatus = 'maintenance';
                            $statusIcon = 'fa-wrench';
                        } elseif ($room->status === 'to_be_cleaned') {
                            $bgClass = 'status-bg-cleaning';
                            $statusBadge = 'badge-warning';
                            $statusText = 'Needs Cleaning';
                            $filterStatus = 'dirty';
                            $statusIcon = 'fa-broom';
                        } elseif ($room->is_occupied) {
                            $bgClass = 'status-bg-occupied';
                            $statusBadge = 'badge-danger';
                            $statusText = 'Occupied';
                            $filterStatus = 'occupied';
                            $statusIcon = 'fa-user';
                        } elseif ($room->has_immediate_booking) {
                            $bgClass = 'status-bg-reserved';
                            $statusBadge = 'badge-primary';
                            $statusText = 'Reserved';
                            $filterStatus = 'reserved';
                            $statusIcon = 'fa-calendar-check-o';
                        }

                        // Check specific flags for urgent indicators
                        $isUrgentCheckout = in_array($room->id, $roomsWithUrgentCheckout ?? []);
                        $isUpcomingCheckin = in_array($room->id, $roomsWithUpcomingCheckin ?? []);
                        
                        // Image Handling - find the first image that actually exists
                        $bgImage = null;
                        if($room->images) {
                            $images = is_string($room->images) ? json_decode($room->images, true) : $room->images;
                            if(is_array($images) && count($images) > 0) {
                                foreach($images as $path) {
                                    $imagePath = is_string($path) ? trim($path, '/') : '';
                                    if($imagePath && file_exists(public_path('storage/' . $imagePath))) {
                                        $bgImage = asset('storage/' . $imagePath);
                                        break;
                                    }
                                }
                            }
                        }
                        
                        // Prepare data for modal
                        $roomData = [
                            'id' => $room->id,
                            'number' => $room->room_number,
                            'type' => $room->room_type,
                            'status' => $statusText,
                            'db_status' => $room->status,
                            'status_until' => $room->status_until ? $room->status_until->format('Y-m-d\TH:i') : null,
                            'price' => $room->price_per_night,
                            'capacity' => $room->capacity,
                            'guest' => null,
                            'checkin' => null,
                            'checkout' => null,
                            'booking_ref' => null,
                            'paid' => null,
                            'total' => null
                        ];

                        if ($room->is_occupied && $room->current_booking) {
                            $booking = $room->current_booking;
                            $roomData['guest'] = $booking->guest_name;
                            $roomData['checkin'] = \Carbon\Carbon::parse($booking->check_in)->format('M d, Y H:i');
                            $roomData['checkout'] = \Carbon\Carbon::parse($booking->check_out)->format('M d, Y H:i');
                            $roomData['booking_ref'] = $booking->booking_reference;
                            
                            // Calculate actual total (Room + Services) for accurate modal display
                            $roomBillUsd = $booking->total_price;
                            $servicesBillTsh = $booking->serviceRequests
                                ->whereIn('status', ['approved', 'completed'])
                                ->sum('total_price_tsh');
                            
                            $bookingExchangeRate = $booking->locked_exchange_rate ?? ($exchangeRate ?? 2500);
                            $servicesBillUsd = ($bookingExchangeRate > 0) ? ($servicesBillTsh / $bookingExchangeRate) : 0;
                            $grandTotalUsd = $roomBillUsd + $servicesBillUsd;

                            $roomData['paid'] = number_format($booking->amount_paid ?? 0, 2);
                            $roomData['total'] = number_format($grandTotalUsd, 2);
                        } elseif ($room->has_immediate_booking && $room->upcoming_checkin) {
                            $roomData['guest'] = $room->upcoming_checkin->guest_name;
                            $roomData['checkin'] = \Carbon\Carbon::parse($room->upcoming_checkin->check_in)->format('M d, Y H:i');
                            $roomData['checkout'] = \Carbon\Carbon::parse($room->upcoming_checkin->check_out)->format('M d, Y H:i');
                            $roomData['booking_ref'] = $room->upcoming_checkin->booking_reference;
                            $roomData['status'] = 'Reserved'; 
                        }
                    @endphp

                    <div class="col-md-3 col-sm-6 mb-4 room-card-container" 
                         data-status="{{ $filterStatus }}" 
                         data-type="{{ $room->room_type }}" 
                         data-number="{{ $room->room_number }}">
                        <div class="card shadow-sm room-card-status {{ $bgClass }} h-100">
                           <!-- Info Button Overlay -->
                           <div class="position-absolute d-flex flex-column" style="top: 10px; left: 10px; z-index: 10;">
                                <button type="button" class="btn btn-sm btn-light shadow-sm rounded-circle mb-2" 
                                        data-toggle="modal" 
                                        data-target="#quickInfoModal"
                                        data-room-info="{{ json_encode($roomData) }}"
                                        title="Quick Info">
                                    <i class="fa fa-info" style="width: 10px;"></i>
                                </button>
                                <button type="button" class="btn btn-sm btn-light shadow-sm rounded-circle" 
                                        data-toggle="modal" 
                                        data-target="#statusUpdateModal"
                                        data-room-id="{{ $room->id }}"
                                        data-room-number="{{ $room->room_number }}"
                                        data-status="{{ $room->status }}"
                                        data-until="{{ $room->status_until ? $room->status_until->format('Y-m-d\TH:i') : '' }}"
                                        title="Manual Status Update">
                                    <i class="fa fa-cog" style="width: 10px;"></i>
                                </button>
                           </div>

                            <!-- Room Image Section -->
                            <div class="position-relative" style="height: 160px; overflow: hidden; background-color: rgba(255,255,255,0.05);">
                                @if($bgImage)
                                    <img src="{{ $bgImage }}" alt="Room {{ $room->room_number }}" class="w-100 h-100" style="object-fit: cover; transition: transform 0.5s;">
                                @else
                                    <div class="w-100 h-100 d-flex align-items-center justify-content-center flex-column" style="color: rgba(255,255,255,0.7) !important;">
                                        <i class="fa fa-bed fa-4x mb-2" style="opacity: 0.5;"></i>
                                        <span class="small font-weight-bold" style="font-size: 10px; letter-spacing: 2px; opacity: 0.8;">PRIME LAND HOTEL</span>
                                    </div>
                                @endif
                                <div class="position-absolute" style="top: 10px; right: 10px;">
                                    <span class="badge {{ $statusBadge }} p-2 shadow-sm">
                                        <i class="fa {{ $statusIcon }}"></i> {{ $statusText }}
                                    </span>
                                </div>
                                <div class="position-absolute" style="bottom: 0px; left: 0px; background: {{ $bgImage ? 'rgba(0,0,0,0.6)' : 'rgba(0,0,0,0.2)' }}; width: 100%; padding: 8px 15px;">
                                    <h4 class="text-white mb-0">
                                        <i class="fa fa-bed mr-2"></i>{{ $room->room_number }} 
                                        <span class="badge badge-light ml-2" style="font-size: 0.6em; vertical-align: middle;">
                                            <i class="fa fa-tag mr-1"></i>{{ $room->room_type }}
                                        </span>
                                    </h4>
                                </div>
                                
                                <!-- Urgent Indicators Overlay -->
                                @if($isUrgentCheckout && $room->is_occupied)
                                    <div class="position-absolute" style="top: 45px; right: 10px;">
                                        <span class="badge badge-danger p-2 shadow-sm">
                                            <i class="fa fa-clock-o"></i> Out Today
                                        </span>
                                    </div>
                                @elseif($isUpcomingCheckin && !$room->is_occupied)
                                    <div class="position-absolute" style="top: 45px; right: 10px;">
                                        <span class="badge badge-info p-2 shadow-sm">
                                            <i class="fa fa-suitcase"></i> In Today
                                        </span>
                                    </div>
                                @endif
                            </div>

                            <div class="card-body p-3">
                                
                                <div class="room-details mb-3" style="min-height: 60px;">
                                    @if($room->status === 'maintenance')
                                        <div class="small text-danger font-weight-bold">
                                            <i class="fa fa-wrench"></i> Under Maintenance
                                        </div>
                                    @elseif($room->status === 'to_be_cleaned')
                                        <div class="small text-warning font-weight-bold">
                                            <i class="fa fa-broom"></i> Needs Cleaning
                                        </div>
                                        @if($room->last_checked_out_booking)
                                            <small class="d-block mt-1 text-muted">
                                                Checkout: {{ \Carbon\Carbon::parse($room->last_checked_out_booking->checked_out_at)->format('H:i') }}
                                            </small>
                                        @endif
                                    @elseif($room->is_occupied && $room->current_booking)
                                        <div class="small">
                                            <strong class="text-dark"><i class="fa fa-user"></i> {{ Str::limit($room->current_booking->guest_name, 18) }}</strong>
                                            <div class="d-flex justify-content-between mt-1 text-muted">
                                                <span>Out: {{ \Carbon\Carbon::parse($room->current_booking->check_out)->format('M d') }}</span>
                                                @if($isUrgentCheckout)
                                                    <span class="text-danger font-weight-bold">Today!</span>
                                                @endif
                                            </div>
                                        </div>
                                    @elseif($room->has_immediate_booking && $room->upcoming_checkin)
                                        <div class="small">
                                            <strong class="text-primary"><i class="fa fa-suitcase"></i> {{ Str::limit($room->upcoming_checkin->guest_name, 18) }}</strong>
                                            <div class="d-flex justify-content-between mt-1 text-muted">
                                                <span>In: {{ \Carbon\Carbon::parse($room->upcoming_checkin->check_in)->format('M d') }}</span>
                                                @if($isUpcomingCheckin)
                                                    <span class="text-info font-weight-bold">Today!</span>
                                                @endif
                                            </div>
                                        </div>
                                    @elseif($room->status === 'closed')
                                         <div class="small text-dark font-weight-bold">
                                            <i class="fa fa-ban"></i> Room Closed
                                        </div>
                                    @else
                                        <div class="small text-success">
                                            <i class="fa fa-check-circle"></i> Ready for booking
                                        </div>
                                    @endif
                                </div>

                                <!-- Action Buttons -->
                                <div class="d-flex justify-content-between">
                                    @if($room->is_occupied && $room->current_booking)
                                        <a href="{{ route('reception.bookings') }}?search={{ $room->current_booking->booking_reference }}" class="btn btn-sm btn-outline-primary w-100 mr-1" title="View Booking"><i class="fa fa-eye"></i> View</a>
                                        @if($isUrgentCheckout)
                                             <a href="{{ route('reception.reservations.check-out') }}" class="btn btn-sm btn-outline-danger w-100 ml-1" title="Checkout"><i class="fa fa-sign-out"></i> Out</a>
                                        @endif
                                    @elseif($room->status === 'available')
                                        <a href="{{ route('reception.bookings.manual.create') }}?room={{$room->id}}" class="btn btn-sm btn-outline-success w-100" title="Book Now"><i class="fa fa-plus"></i> Book Now</a>
                                    @elseif($room->has_immediate_booking)
                                         <a href="{{ route('reception.reservations.check-in') }}" class="btn btn-sm btn-outline-info w-100" title="Check In"><i class="fa fa-sign-in"></i> Check In</a>
                                    @elseif($room->status === 'to_be_cleaned')
                                         <button disabled class="btn btn-sm btn-outline-secondary w-100" title="Cleaning in Progress"><i class="fa fa-hourglass-half"></i> Cleaning...</button>
                                    @else
                                        <button disabled class="btn btn-sm btn-outline-secondary w-100">Unavailable</button>
                                    @endif
                                </div>

                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</div>

@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const statusFilter = document.getElementById('statusFilter');
        const typeFilter = document.getElementById('typeFilter');
        const searchInput = document.getElementById('searchRoom');
        const cards = document.querySelectorAll('.room-card-container');

        function filterRooms() {
            const status = statusFilter.value;
            const type = typeFilter.value;
            const search = searchInput.value.toLowerCase();

            cards.forEach(card => {
                const cardStatus = card.getAttribute('data-status');
                const cardType = card.getAttribute('data-type');
                const cardNumber = card.getAttribute('data-number').toLowerCase();

                let show = true;

                if (status !== 'all' && cardStatus !== status) show = false;
                if (type !== 'all' && cardType !== type) show = false;
                if (search && !cardNumber.includes(search)) show = false;

                card.style.display = show ? 'block' : 'none';
            });
        }

        statusFilter.addEventListener('change', filterRooms);
        typeFilter.addEventListener('change', filterRooms);
        searchInput.addEventListener('keyup', filterRooms);
    });

    $('#quickInfoModal').on('show.bs.modal', function (event) {
        const button = $(event.relatedTarget);
        const data = button.data('room-info');
        const modal = $(this);
        
        // Set basic info
        modal.find('#modalRoomTitle').text('Room ' + data.number);
        modal.find('#modalRoomType').text(data.type);
        modal.find('#modalStatus').html('<span class="badge badge-secondary">' + data.status + '</span>');
        modal.find('#modalPrice').text('$' + data.price);
        modal.find('#modalCapacity').text(data.capacity + ' Person(s)');

        // Reset visibility
        modal.find('#rowGuest, #rowCheckin, #rowCheckout, #rowPayment, #rowStatusUntil, #modalViewBookingBtn').hide();

        // Set manual status info
        if (data.status_until) {
            const date = new Date(data.status_until);
            modal.find('#modalStatusUntil').text(date.toLocaleString());
            modal.find('#rowStatusUntil').show();
        }

        // Set detailed booking info if available
        if (data.guest) {
            modal.find('#modalGuest').text(data.guest);
            modal.find('#rowGuest').show();
            
            modal.find('#modalCheckin').text(data.checkin);
            modal.find('#rowCheckin').show();

            modal.find('#modalCheckout').text(data.checkout);
            modal.find('#rowCheckout').show();

            if (data.paid !== null) {
                 modal.find('#modalPayment').text('Paid: $' + data.paid + ' / Total: $' + data.total);
                 modal.find('#rowPayment').show();
            }
            
            if(data.booking_ref) {
                const btn = modal.find('#modalViewBookingBtn');
                btn.attr('href', '{{ route("reception.bookings") }}?search=' + data.booking_ref);
                btn.show();
            }
        }
    });

    $('#statusUpdateModal').on('show.bs.modal', function (event) {
        const button = $(event.relatedTarget);
        const roomId = button.data('room-id');
        const roomNumber = button.data('room-number');
        const status = button.data('status');
        const until = button.data('until');
        
        window.currentRoomIdForStatus = roomId;
        const modal = $(this);
        modal.find('#statusModalTitle').text('Room ' + roomNumber + ' Status');
        modal.find('#newStatus').val(status || 'available');
        modal.find('#statusUntil').val(until || '');
        
        toggleUntilField();
    });

    function toggleUntilField() {
        const status = document.getElementById('newStatus').value;
        const untilGroup = document.getElementById('untilGroup');
        if (['available', 'to_be_cleaned'].includes(status)) {
            untilGroup.style.display = 'none';
        } else {
            untilGroup.style.display = 'block';
        }
    }

    document.getElementById('newStatus').addEventListener('change', toggleUntilField);

    document.getElementById('statusUpdateForm').addEventListener('submit', function(e) {
        e.preventDefault();
        const saveBtn = document.getElementById('saveStatusBtn');
        saveBtn.disabled = true;
        saveBtn.innerHTML = '<i class="fa fa-spinner fa-spin"></i> Saving...';

        const formData = new FormData(this);
        const url = `{{ route('reception.rooms') }}/${window.currentRoomIdForStatus}/update-status`;

        fetch(url, {
            method: 'POST',
            body: formData,
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            } else {
                alert('Error: ' + (data.message || 'Something went wrong'));
                saveBtn.disabled = false;
                saveBtn.innerText = 'Save Changes';
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('An unexpected error occurred.');
            saveBtn.disabled = false;
            saveBtn.innerText = 'Save Changes';
        });
    });
</script>
@endsection
