@extends('dashboard.layouts.app')

@php
  $currentUser = Auth::guard('staff')->user();
  $userRole = strtolower($currentUser->role ?? '');
  $isObserver = $currentUser && ($userRole === 'manager' || $userRole === 'reception' || $userRole === 'super_admin');
@endphp

@section('content')
<div class="app-title mb-3 mb-md-4">
  <div>
    <h1><i class="fa fa-dashboard"></i> Housekeeper</h1>
    <p class="d-none d-md-block">Welcome back, {{ $currentUser->name }}!@if($isObserver) <span class="badge badge-info">Read-Only Mode</span>@endif</p>
  </div>
  <ul class="app-breadcrumb breadcrumb d-none d-md-flex">
    <li class="breadcrumb-item"><i class="fa fa-home fa-lg"></i></li>
    <li class="breadcrumb-item"><a href="#">Dashboard</a></li>
  </ul>
</div>

<!-- Statistics Cards -->
<div class="row mb-2 mb-md-3">
  <div class="col-6 col-lg-3 mb-3">
    <div class="widget-small warning coloured-icon mb-0 h-100 shadow-sm">
      <i class="icon fa fa-bed fa-2x"></i>
      <div class="info">
        <h4 class="small-text">To Clean</h4>
        <p><b>{{ $stats['rooms_needing_cleaning'] ?? 0 }}</b></p>
      </div>
    </div>
  </div>
  <div class="col-6 col-lg-3 mb-3">
    <div class="widget-small danger coloured-icon mb-0 h-100 shadow-sm">
      <i class="icon fa fa-exclamation-triangle fa-2x"></i>
      <div class="info">
        <h4 class="small-text">Low Stock</h4>
        <p><b>{{ $stats['low_stock_items'] ?? 0 }}</b></p>
      </div>
    </div>
  </div>
  <div class="col-6 col-lg-3 mb-3">
    <div class="widget-small info coloured-icon mb-0 h-100 shadow-sm">
      <i class="icon fa fa-wrench fa-2x"></i>
      <div class="info">
        <h4 class="small-text">Issues</h4>
        <p><b>{{ $stats['pending_issues'] ?? 0 }}</b></p>
      </div>
    </div>
  </div>
  <div class="col-6 col-lg-3 mb-3">
    <div class="widget-small success coloured-icon mb-0 h-100 shadow-sm">
      <i class="icon fa fa-check-circle fa-2x"></i>
      <div class="info">
        <h4 class="small-text">Cleaned</h4>
        <p><b>{{ $stats['cleaned_today'] ?? 0 }}</b></p>
      </div>
    </div>
  </div>
</div>

<!-- All Rooms Overview -->
<div class="row mb-3">
  <div class="col-md-12">
    <div class="tile shadow-sm border-0" style="border-radius: 12px;">
      <div class="tile-title-w-btn flex-column flex-md-row">
        <h3 class="title mb-3 mb-md-0"><i class="fa fa-bed"></i> Room Status</h3>
        <div class="btn-group btn-group-sm flex-wrap w-100 w-md-auto" role="group">
          <button class="btn btn-secondary px-3 active-filter" id="filterAll" onclick="filterRooms('all')">All</button>
          <button class="btn btn-warning px-3" id="filterNeedsCleaning" onclick="filterRooms('needs_cleaning')">Clean</button>
          <button class="btn btn-success px-3" id="filterAvailable" onclick="filterRooms('available')">Free</button>
          <button class="btn btn-danger px-3" id="filterOccupied" onclick="filterRooms('occupied')">Busy</button>
          <button class="btn btn-primary px-3" id="filterReserved" onclick="filterRooms('reserved')">Booked</button>
        </div>
      </div>
      
      <div class="tile-body mt-2">
        <div class="row" id="roomsGrid">
          @foreach($allRooms as $room)
          @php
            $roomStatus = $room->status;
            $statusBadge = 'badge-success';
            $statusIcon = 'fa-check-circle';
            $bgClass = 'status-bg-available';
            $statusText = 'Available';
            
            if ($roomStatus === 'closed') {
              $statusBadge = 'dark';
              $statusIcon = 'fa-ban';
              $statusText = 'Closed';
              $bgClass = 'status-bg-closed';
            } elseif ($roomStatus === 'maintenance') {
              $statusBadge = 'danger';
              $statusIcon = 'fa-wrench';
              $statusText = 'Maintenance';
              $bgClass = 'status-bg-maintenance';
            } elseif ($roomStatus === 'to_be_cleaned' || $roomStatus === 'needs_cleaning') {
              $statusBadge = 'warning';
              $statusIcon = 'fa-broom';
              $statusText = 'Clean Me';
              $bgClass = 'status-bg-cleaning';
            } elseif ($room->is_occupied) {
                // Determine if they are checking out today
                $isCheckingOutToday = false;
                if ($room->currentBooking && $room->currentBooking->check_out) {
                    $isCheckingOutToday = \Carbon\Carbon::parse($room->currentBooking->check_out)->isToday();
                }
              $statusBadge = 'danger';
              $statusIcon = 'fa-user';
              $statusText = $isCheckingOutToday ? 'Checkout today' : 'Occupied';
              $bgClass = $isCheckingOutToday ? 'status-bg-urgent' : 'status-bg-occupied';
            } elseif ($room->has_immediate_booking) {
              $statusBadge = 'primary';
              $statusIcon = 'fa-calendar';
              $statusText = 'Arriving today';
              $bgClass = 'status-bg-reserved';
            }
            
            $hasIssues = $room->activeIssues && $room->activeIssues->count() > 0;
            if ($hasIssues) {
                $statusText = 'Fix Needed';
                $bgClass = 'status-bg-maintenance';
                $statusIcon = 'fa-exclamation-triangle';
            }

            $bgImage = null;
            if ($room->images && is_array($room->images) && count($room->images) > 0) {
                $bgImage = asset('storage/' . ltrim($room->images[0], '/'));
            }

            $statusClass = 'available';
            if ($roomStatus === 'maintenance') $statusClass = 'maintenance';
            if ($roomStatus === 'to_be_cleaned' || $roomStatus === 'needs_cleaning') $statusClass = 'needs_cleaning';
            if ($room->is_occupied) $statusClass = 'occupied';
            if ($roomStatus === 'closed') $statusClass = 'closed';
            if ($room->has_immediate_booking) $statusClass = 'reserved';
          @endphp
          
          <div class="col-6 col-md-4 col-lg-3 mb-3 room-card px-2" data-status="{{ $statusClass }}">
            <div class="card shadow-sm border-0 room-card-status {{ $bgClass }} h-100" style="border-radius: 12px; overflow: hidden;">
               <!-- Mini Header -->
               <div class="p-2 d-flex justify-content-between align-items-center bg-black-transparent">
                  <span class="font-weight-bold text-white">#{{ $room->room_number }}</span>
                  <i class="fa {{ $statusIcon }} text-white-50 small"></i>
               </div>

               <!-- Status Overlay -->
               <div class="card-body p-2 d-flex flex-column justify-content-center text-center text-white" style="min-height: 100px;">
                  <div class="mb-2" style="opacity: 0.3;">
                      <i class="fa fa-bed fa-2x"></i>
                  </div>
                  <span class="small font-weight-bold text-uppercase d-block mb-1" style="font-size: 10px; opacity: 0.9;">{{ $statusText }}</span>
                  <div class="small font-italic opacity-75" style="font-size: 11px; line-height: 1.2;">
                      @if($room->is_occupied && $room->currentBooking)
                          {{ Str::limit($room->currentBooking->guest_name, 15) }}
                      @elseif($room->has_immediate_booking && ($room->currentBooking ?: $room->upcoming_checkin))
                         Arr: {{ ($room->currentBooking ?: $room->upcoming_checkin)->guest_name }}
                      @elseif($roomStatus === 'to_be_cleaned' || $roomStatus === 'needs_cleaning')
                          Needs attention
                      @else
                          &nbsp;
                      @endif
                  </div>
               </div>

               <!-- Mobile Quick Actions -->
               <div class="card-footer p-1 bg-white border-0">
                  @if(!$isObserver && ($roomStatus === 'to_be_cleaned' || $roomStatus === 'needs_cleaning'))
                  <button class="btn btn-sm btn-success btn-block py-2 font-weight-bold mb-1 mark-cleaned-btn" 
                          data-room-id="{{ $room->id }}" 
                          data-room-number="{{ $room->room_number }}" style="border-radius: 8px;">
                    <i class="fa fa-check mr-1"></i> CLEAN
                  </button>
                  @else
                    <div class="py-2 text-center text-muted small"><i class="fa fa-info-circle"></i> Tap for info</div>
                  @endif
                  
                  <div class="d-none">
                      <button class="view-details-btn" 
                              data-room-id="{{ $room->id }}"
                              data-room-number="{{ $room->room_number }}"
                              data-room-type="{{ $room->room_type }}"
                              data-status="{{ $statusText }}"></button>
                  </div>
               </div>
            </div>
          </div>
          @endforeach
        </div>
      </div>
    </div>
  </div>
</div>

<!-- Low Stock & Issues (Mobile Optimized Table) -->
@if($lowStockItems->count() > 0 || $recentIssues->count() > 0)
<div class="row">
    @if($lowStockItems->count() > 0)
    <div class="col-md-6 mb-3">
        <div class="tile shadow-sm border-0" style="border-radius: 12px;">
            <h4 class="tile-title small font-weight-bold text-danger"><i class="fa fa-warning"></i> Low Stock Alert</h4>
            <div class="table-responsive">
                <table class="table table-sm table-hover mb-0">
                    <thead>
                        <tr><th>Item</th><th>Stock</th><th>Action</th></tr>
                    </thead>
                    <tbody>
                        @foreach($lowStockItems as $item)
                        <tr>
                            <td>{{ $item->name }}</td>
                            <td class="text-danger font-weight-bold">{{ $item->current_stock }} {{ $item->unit }}</td>
                            <td>
                                <a href="{{ route('housekeeper.purchase-requests.create', ['housekeeping_ids' => $item->id]) }}" class="btn btn-xs btn-primary p-1 px-2">
                                    <i class="fa fa-cart-plus"></i>
                                </a>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    @endif

    @if($recentIssues->count() > 0)
    <div class="col-md-6 mb-3">
        <div class="tile shadow-sm border-0" style="border-radius: 12px;">
            <h4 class="tile-title small font-weight-bold text-info"><i class="fa fa-wrench"></i> Recent Issues</h4>
            <div class="table-responsive">
                <table class="table table-sm table-hover mb-0">
                    <thead>
                        <tr><th>Room</th><th>Issue</th><th>Status</th></tr>
                    </thead>
                    <tbody>
                        @foreach($recentIssues as $issue)
                        <tr>
                            <td><strong>{{ $issue->room->room_number }}</strong></td>
                            <td><small>{{ Str::limit($issue->issue_type, 15) }}</small></td>
                            <td><span class="badge badge-{{ $issue->status === 'reported' ? 'warning' : 'info' }}">{{ $issue->status }}</span></td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    @endif
</div>
@endif

<!-- Quick Actions Grid (Mobile Specific) -->
@if(!$isObserver)
<div class="row mb-4">
  <div class="col-md-12">
    <div class="tile shadow-sm border-0" style="border-radius: 12px; background: #f8f9fa;">
      <h4 class="tile-title small font-weight-bold mb-3"><i class="fa fa-bolt"></i> Menu Essentials</h4>
      <div class="row no-gutters">
        <div class="col-4 p-1">
          <a href="{{ route('housekeeper.rooms.cleaning') }}" class="btn btn-light btn-block py-3 shadow-none border bg-white h-100 d-flex flex-column align-items-center justify-content-center">
            <i class="fa fa-bed text-warning fa-lg mb-1"></i><span class="extra-small font-weight-bold">CLEANING</span>
          </a>
        </div>
        <div class="col-4 p-1">
          <a href="{{ route('housekeeper.inventory') }}" class="btn btn-light btn-block py-3 shadow-none border bg-white h-100 d-flex flex-column align-items-center justify-content-center">
            <i class="fa fa-cubes text-info fa-lg mb-1"></i><span class="extra-small font-weight-bold">STOCKS</span>
          </a>
        </div>
        <div class="col-4 p-1">
          <a href="{{ route('housekeeper.room-issues') }}" class="btn btn-light btn-block py-3 shadow-none border bg-white h-100 d-flex flex-column align-items-center justify-content-center">
            <i class="fa fa-wrench text-danger fa-lg mb-1"></i><span class="extra-small font-weight-bold">ISSUES</span>
          </a>
        </div>
        <div class="col-4 p-1">
          <a href="{{ route('housekeeper.purchase-requests.create') }}" class="btn btn-light btn-block py-3 shadow-none border bg-white h-100 d-flex flex-column align-items-center justify-content-center">
            <i class="fa fa-shopping-cart text-primary fa-lg mb-1"></i><span class="extra-small font-weight-bold">PURCHASE</span>
          </a>
        </div>
        <div class="col-4 p-1">
          <a href="{{ route('housekeeper.lost-found.index') }}" class="btn btn-light btn-block py-3 shadow-none border bg-white h-100 d-flex flex-column align-items-center justify-content-center">
            <i class="fa fa-search text-secondary fa-lg mb-1"></i><span class="extra-small font-weight-bold">LOST & FOUND</span>
          </a>
        </div>
        <div class="col-4 p-1">
            <a href="{{ route('housekeeper.lost-found.create') }}" class="btn btn-primary btn-block py-3 shadow-none h-100 d-flex flex-column align-items-center justify-content-center">
              <i class="fa fa-plus-circle fa-lg mb-1"></i><span class="extra-small font-weight-bold">REPORT FOUND</span>
            </a>
          </div>
      </div>
    </div>
  </div>
</div>
@endif

<!-- Mark Room Cleaned Modal -->
<div class="modal fade" id="markCleanedModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-dialog-centered" role="document">
      <div class="modal-content border-0 shadow-lg" style="border-radius: 15px;">
        <div class="modal-header bg-success text-white border-0 py-3">
          <h5 class="modal-title font-weight-bold"><i class="fa fa-check-circle"></i> Confirm Cleaning</h5>
          <button type="button" class="close text-white" data-dismiss="modal">&times;</button>
        </div>
        <form id="markCleanedForm">
          <div class="modal-body py-4">
            <input type="hidden" id="room_id" name="room_id">
            <p class="mb-3">Are you sure room <strong id="room_number_display" class="text-primary h4"></strong> is now clean and available?</p>
            <div class="form-group">
              <label for="notes" class="small font-weight-bold text-muted">HOUSEKEEPER NOTES (OPTIONAL)</label>
              <textarea class="form-control bg-light border-0" id="notes" name="notes" rows="2" placeholder="e.g., Linens changed, Mini-bar restocked" style="border-radius: 8px;"></textarea>
            </div>
          </div>
          <div class="modal-footer border-0 pt-0">
            <button type="button" class="btn btn-light px-4 mr-2" data-dismiss="modal">Cancel</button>
            <button type="submit" class="btn btn-success px-4 font-weight-bold shadow-sm">CONFIRM CLEAN</button>
          </div>
        </form>
      </div>
    </div>
  </div>
  
  <!-- Room Details Mini Modal -->
  <div class="modal fade" id="roomDetailsModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-dialog-centered" role="document">
      <div class="modal-content border-0 shadow-lg" style="border-radius: 15px;">
        <div class="modal-header border-0 py-3 bg-secondary text-white">
          <h5 class="modal-title font-weight-bold"><i class="fa fa-bed"></i> Room <span id="detail_room_number"></span></h5>
          <button type="button" class="close text-white" data-dismiss="modal">&times;</button>
        </div>
        <div class="modal-body py-3">
            <div class="row no-gutters mb-3 pb-3 border-bottom">
                <div class="col-6">
                    <small class="text-muted d-block uppercase font-weight-bold" style="font-size: 10px;">ROOM TYPE</small>
                    <span id="detail_room_type" class="font-weight-bold"></span>
                </div>
                <div class="col-6">
                    <small class="text-muted d-block uppercase font-weight-bold" style="font-size: 10px;">CURRENT STATUS</small>
                    <span id="detail_status" class="badge"></span>
                </div>
            </div>
            <div class="booking-info p-3 bg-light rounded" style="border-left: 4px solid #6c757d;">
                <small class="text-muted d-block uppercase font-weight-bold mb-2" style="font-size: 10px;">GUEST DETAILS</small>
                <div class="row">
                    <div class="col-12">
                        <span id="detail_guest_name" class="font-weight-bold h6 mb-0 text-dark"></span>
                    </div>
                </div>
            </div>
        </div>
        <div class="modal-footer border-0 pt-0">
          <button type="button" class="btn btn-secondary px-5 w-100" data-dismiss="modal">Close</button>
        </div>
      </div>
    </div>
  </div>

@endsection

@section('styles')
<style>
    .status-bg-available { background: linear-gradient(135deg, #28a745, #218838); }
    .status-bg-cleaning { background: linear-gradient(135deg, #ffc107, #e0a800); }
    .status-bg-occupied { background: linear-gradient(135deg, #dc3545, #c82333); }
    .status-bg-reserved { background: linear-gradient(135deg, #007bff, #0069d9); }
    .status-bg-closed { background: linear-gradient(135deg, #343a40, #23272b); }
    .status-bg-maintenance { background: linear-gradient(135deg, #6c757d, #5a6268); }
    .status-bg-urgent { background: linear-gradient(135deg, #fd7e14, #e8590c); }

    .bg-black-transparent { background-color: rgba(0,0,0,0.15); }
    .extra-small { font-size: 9px; letter-spacing: 0.5px; }
    .small-text { font-size: 12px; margin-bottom: 2px; }
    .active-filter { border-bottom: 3px solid #fff !important; }
    
    .room-card-status { transition: transform 0.2s; cursor: pointer; }
    .room-card-status:active { transform: scale(0.95); }
    
    @media (max-width: 768px) {
        .widget-small .info h4 { font-size: 11px; }
        .widget-small .info p { font-size: 16px; }
        .app-title h1 { font-size: 20px; }
    }
</style>
@endsection

@section('scripts')
<script>
    function filterRooms(status) {
        $('.btn-group .btn').removeClass('active-filter bg-dark text-white');
        if (status === 'all') {
            $('.room-card').fadeIn();
            $('#filterAll').addClass('active-filter');
        } else {
            $('.room-card').hide();
            $('.room-card[data-status="' + status + '"]').fadeIn();
            $('#filter' + status.charAt(0).toUpperCase() + status.slice(1).replace('_', '')).addClass('active-filter');
        }
    }

    $(document).ready(function() {
        // Mark Cleaned
        $('.mark-cleaned-btn').on('click', function(e) {
            e.stopPropagation();
            const id = $(this).data('room-id');
            const num = $(this).data('room-number');
            $('#room_id').val(id);
            $('#room_number_display').text(num);
            $('#markCleanedModal').modal('show');
        });

        $('#markCleanedForm').on('submit', function(e) {
            e.preventDefault();
            const id = $('#room_id').val();
            const notes = $('#notes').val();
            
            $.ajax({
                url: "{{ url('housekeeper/rooms') }}/" + id + "/mark-cleaned",
                method: "POST",
                data: {
                    _token: "{{ csrf_token() }}",
                    notes: notes
                },
                success: function() {
                    location.reload();
                }
            });
        });

        // View Details
        $('.view-details-btn').on('click', function(e) {
            e.stopPropagation();
            const data = $(this).data();
            $('#detail_room_number').text(data.roomNumber);
        });

        // Room Card Click -> Details
        $('.room-card').on('click', function() {
            const data = $(this).find('.view-details-btn').data();
            $('#detail_room_number').text(data.roomNumber);
            $('#detail_room_type').text(data.roomType);
            $('#detail_status').text(data.status);
            $('#detail_status').removeClass('badge-success badge-warning badge-danger badge-info');
            
            const guestName = $(this).find('.opacity-75').text().trim();
            $('#detail_guest_name').text(guestName || 'No guest presently');
            
            $('#roomDetailsModal').modal('show');
        });
    });
</script>
@endsection
