@extends('dashboard.layouts.app')

@section('content')
<div class="app-title">
  <div>
    <h1><i class="fa fa-dashboard"></i> Reception Dashboard</h1>
    <p>Welcome back, {{ $userName }}!</p>
  </div>
  <ul class="app-breadcrumb breadcrumb">
    <li class="breadcrumb-item"><i class="fa fa-home fa-lg"></i></li>
    <li class="breadcrumb-item"><a href="#">Dashboard</a></li>
  </ul>
</div>

<!-- Main Statistics Cards -->
<div class="row mb-3">
  <div class="col-md-6 col-lg-3">
    <div class="widget-small primary coloured-icon">
      <i class="icon fa fa-users fa-3x"></i>
      <div class="info">
        <h4>Active Guests</h4>
        <p><b>{{ $stats['total_active_guests'] ?? 0 }}</b></p>
      </div>
    </div>
  </div>
  <div class="col-md-6 col-lg-3">
    <div class="widget-small info coloured-icon">
      <i class="icon fa fa-bed fa-3x"></i>
      <div class="info">
        <h4>Total Rooms</h4>
        <p><b>{{ $stats['total_rooms'] ?? 0 }}</b></p>
      </div>
    </div>
  </div>
  <div class="col-md-6 col-lg-3">
    <div class="widget-small warning coloured-icon">
      <i class="icon fa fa-calendar fa-3x"></i>
      <div class="info">
        <h4>Total Bookings</h4>
        <p><b>{{ $stats['total_bookings'] ?? 0 }}</b></p>
      </div>
    </div>
  </div>
  <div class="col-md-6 col-lg-3">
    <div class="widget-small success coloured-icon">
      <i class="icon fa fa-sign-in fa-3x"></i>
      <div class="info">
        <h4>Today's Check-ins</h4>
        <p><b>{{ $stats['todays_checkins'] ?? 0 }}</b></p>
      </div>
    </div>
  </div>
</div>

<!-- Quick Integration Links -->
<div class="row mb-3">
    <div class="col-md-6 col-lg-3">
        <a href="{{ route('reception.orders.monitor') }}" style="text-decoration: none;">
            <div class="tile shadow-sm border-0 d-flex align-items-center bg-primary text-white p-3" style="border-radius: 12px; position: relative;">
                @if(($stats['active_orders_count'] ?? 0) > 0)
                <span class="badge badge-danger" style="position: absolute; top: -8px; right: -8px; font-size: 14px; padding: 6px 10px; border-radius: 50%; min-width: 30px; height: 30px; display: flex; align-items: center; justify-content: center; box-shadow: 0 2px 4px rgba(0,0,0,0.2);">
                    {{ $stats['active_orders_count'] ?? 0 }}
                </span>
                @endif
                <div class="mr-3 bg-white text-primary rounded-circle d-flex align-items-center justify-content-center" style="width: 50px; height: 50px;">
                    <i class="fa fa-cutlery fa-2x"></i>
                </div>
                <div>
                    <h5 class="mb-0">Pending Orders</h5>
                    <p class="mb-0 small opacity-75">@if(($stats['active_orders_count'] ?? 0) > 0) {{ $stats['active_orders_count'] }} active orders @else All orders cleared @endif</p>
                </div>
            </div>
        </a>
    </div>

    <!-- NEW ROOM STATUS WIDGET -->
    <div class="col-md-6 col-lg-3">
        <a href="{{ route('reception.rooms') }}" style="text-decoration: none;">
            <div class="tile shadow-sm border-0 d-flex align-items-center bg-secondary text-white p-3" style="border-radius: 12px; position: relative; background-color: #6c757d !important;">
                <div class="mr-3 bg-white text-secondary rounded-circle d-flex align-items-center justify-content-center" style="width: 50px; height: 50px;">
                    <i class="fa fa-th fa-2x"></i>
                </div>
                <div>
                    <h5 class="mb-0">Room Status</h5>
                    <p class="mb-0 small opacity-75">{{ $stats['total_active_guests'] ?? 0 }} occupied rooms</p>
                </div>
            </div>
        </a>
    </div>

    <div class="col-md-6 col-lg-3">
        <a href="{{ route('reception.extension-requests') }}" style="text-decoration: none;">
            <div class="tile shadow-sm border-0 d-flex align-items-center bg-info text-white p-3" style="border-radius: 12px; position: relative;">
                @if(($stats['pending_extensions'] ?? 0) > 0)
                <span class="badge badge-danger" style="position: absolute; top: -8px; right: -8px; font-size: 14px; padding: 6px 10px; border-radius: 50%; min-width: 30px; height: 30px; display: flex; align-items: center; justify-content: center; box-shadow: 0 2px 4px rgba(0,0,0,0.2);">
                    {{ $stats['pending_extensions'] ?? 0 }}
                </span>
                @endif
                <div class="mr-3 bg-white text-info rounded-circle d-flex align-items-center justify-content-center" style="width: 50px; height: 50px;">
                    <i class="fa fa-clock-o fa-2x"></i>
                </div>
                <div>
                    <h5 class="mb-0">Stay Extensions</h5>
                    <p class="mb-0 small opacity-75">@if(($stats['pending_extensions'] ?? 0) > 0) {{ $stats['pending_extensions'] }} pending @else No pending extensions @endif</p>
                </div>
            </div>
        </a>
    </div>
    <div class="col-md-6 col-lg-3">
        <a href="{{ route('admin.rooms.issues') }}" style="text-decoration: none;">
            <div class="tile shadow-sm border-0 d-flex align-items-center bg-warning text-white p-3" style="border-radius: 12px; position: relative;">
                @if(($stats['room_issues'] ?? 0) > 0)
                <span class="badge badge-danger" style="position: absolute; top: -8px; right: -8px; font-size: 14px; padding: 6px 10px; border-radius: 50%; min-width: 30px; height: 30px; display: flex; align-items: center; justify-content: center; box-shadow: 0 2px 4px rgba(0,0,0,0.2);">
                    {{ $stats['room_issues'] ?? 0 }}
                </span>
                @endif
                <div class="mr-3 bg-white text-warning rounded-circle d-flex align-items-center justify-content-center" style="width: 50px; height: 50px;">
                    <i class="fa fa-wrench fa-2x"></i>
                </div>
                <div>
                    <h5 class="mb-0">Room Issues</h5>
                    <p class="mb-0 small opacity-75">@if(($stats['room_issues'] ?? 0) > 0) {{ $stats['room_issues'] }} active issues @else All rooms clear @endif</p>
                </div>
            </div>
        </a>
    </div>

</div>

<!-- All Statistics (Collapsible) -->
<div class="row mb-3">
  <div class="col-md-12 text-center">
    <button class="btn btn-primary" type="button" data-toggle="collapse" data-target="#allStatistics" id="toggleStatsBtn">
      <i class="fa fa-bar-chart"></i> Show Operational Stats
    </button>
  </div>
</div>

<div class="collapse" id="allStatistics">
  <div class="row mb-3">
    <div class="col-md-12">
      <div class="tile">
        <h3 class="tile-title">Operational Statistics</h3>
        <div class="tile-body">
            <div class="row">
                <div class="col-md-3">
                    <div class="widget-small info coloured-icon"><i class="icon fa fa-television fa-2x"></i>
                        <div class="info">
                            <h4>Active Orders</h4>
                            <p><b>{{ $stats['active_orders_count'] ?? 0 }} orders</b></p>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="widget-small warning coloured-icon"><i class="icon fa fa-hourglass-half fa-2x"></i>
                        <div class="info">
                            <h4>In Progress</h4>
                            <p><b>{{ $stats['preparing_ready_count'] ?? 0 }} orders</b></p>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="widget-small info coloured-icon"><i class="icon fa fa-shopping-cart fa-2x"></i>
                        <div class="info">
                            <h4>Today's Volume</h4>
                            <p><b>{{ $stats['today_orders_count'] ?? 0 }} total</b></p>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="widget-small success coloured-icon"><i class="icon fa fa-check fa-2x"></i>
                        <div class="info">
                            <h4 style="color: black;">Available Rooms</h4>
                            <p style="color: black;"><b>{{ ($stats['total_rooms'] ?? 0) - ($stats['total_active_guests'] ?? 0) }} rooms</b></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- Charts Row -->
<div class="row">
  <div class="col-md-6">
    <div class="tile">
      <h3 class="tile-title"><i class="fa fa-bar-chart text-primary mr-1"></i> Room Occupancy This Week</h3>
      <p class="text-muted small pl-2">Daily check-ins vs check-outs over the last 7 days</p>
      <div class="embed-responsive embed-responsive-16by9">
        <canvas class="embed-responsive-item" id="occupancyChart"></canvas>
      </div>
    </div>
  </div>
  <div class="col-md-6">
    <div class="tile">
      <h3 class="tile-title"><i class="fa fa-pie-chart text-info mr-1"></i> Booking Status Distribution</h3>
      <div class="embed-responsive embed-responsive-16by9">
        <canvas class="embed-responsive-item" id="bookingStatusChart"></canvas>
      </div>
    </div>
  </div>
</div>

<!-- Live Requests Tables -->
<div class="row">
    <!-- Pending Orders Overview -->
    <div class="col-md-6">
        <div class="tile shadow-sm" style="border-radius: 12px; height: 100%;">
            <div class="tile-title-w-btn">
                <h3 class="title"><i class="fa fa-television text-warning"></i> Pending Orders</h3>
                <a href="{{ route('reception.orders.monitor') }}" class="btn btn-sm btn-primary">Monitor All</a>
            </div>
            <div class="tile-body">
                @if($activeOrders != null && $activeOrders->count() > 0)
                <div class="table-responsive">
                    <table class="table table-sm table-hover">
                        <thead>
                            <tr>
                                <th>Loc</th>
                                <th>Item</th>
                                <th>Status</th>
                                <th class="text-right">Amount</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($activeOrders as $order)
                            @php
                                $statusClass = [
                                    'pending' => 'badge-danger',
                                    'approved' => 'badge-primary',
                                    'preparing' => 'badge-info',
                                    'ready' => 'badge-warning',
                                    'completed' => 'badge-success',
                                    'cancelled' => 'badge-secondary'
                                ][$order->status] ?? 'badge-secondary';
                            @endphp
                            <tr style="{{ $order->payment_status !== 'paid' && $order->status === 'completed' ? 'background-color: #fff9f0;' : '' }}">
                                <td><strong>{{ ($order->booking && $order->booking->room) ? $order->booking->room->room_number : 'W-in' }}</strong></td>
                                <td title="{{ $order->service_specific_data['item_name'] ?? $order->service->name }}">
                                    <small>{{ \Illuminate\Support\Str::limit($order->service_specific_data['item_name'] ?? $order->service->name, 20) }}</small>
                                </td>
                                <td><span class="badge {{ $statusClass }}" style="font-size: 0.65rem;">{{ strtoupper($order->status) }}</span></td>
                                <td class="text-right"><small>{{ number_format($order->total_price_tsh, 0) }}</small></td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <!-- Pagination -->
                <div class="mt-2 d-flex justify-content-center">
                    {!! $activeOrders->appends(request()->except('orders_page'))->links() !!}
                </div>
                @else
                <div class="text-center py-4">
                    <i class="fa fa-check-circle fa-3x text-success mb-2"></i>
                    <p class="text-muted">No pending orders</p>
                </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Active Extensions -->
    <div class="col-md-6">
        <div class="tile shadow-sm" style="border-radius: 12px; height: 100%;">
            <div class="tile-title-w-btn">
                <h3 class="title"><i class="fa fa-clock-o text-info"></i> Pending Extensions</h3>
                <a href="{{ route('reception.extension-requests') }}" class="btn btn-sm btn-primary">Process</a>
            </div>
            <div class="tile-body">
                @if($pendingExtensions->count() > 0)
                <div class="table-responsive">
                    <table class="table table-sm table-hover">
                        <thead>
                            <tr>
                                <th>Guest</th>
                                <th>Room</th>
                                <th>New Date</th>
                                <th>Diff</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($pendingExtensions as $ext)
                            @php
                                $nightsDiff = \Carbon\Carbon::parse($ext->check_out)->diffInDays(\Carbon\Carbon::parse($ext->extension_requested_to), false);
                            @endphp
                            <tr>
                                <td>{{ $ext->guest_name }}</td>
                                <td>{{ $ext->room->room_number ?? 'N/A' }}</td>
                                <td>{{ \Carbon\Carbon::parse($ext->extension_requested_to)->format('M d') }}</td>
                                <td><span class="badge {{ $nightsDiff > 0 ? 'badge-info' : 'badge-warning' }}">{{ $nightsDiff }} nights</span></td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @else
                <div class="text-center py-4">
                    <i class="fa fa-calendar-check-o fa-3x text-muted mb-2"></i>
                    <p class="text-muted">No pending extensions</p>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Recent Bookings Table -->
<div class="row mt-4">
    <div class="col-md-12">
        <div class="tile shadow-sm" style="border-radius: 12px;">
            <div class="tile-title-w-btn">
                <h3 class="title"><i class="fa fa-calendar-check-o"></i> Recent Activity</h3>
                <a href="{{ route('reception.bookings') }}" class="btn btn-primary">View All Bookings</a>
            </div>
            <div class="tile-body">
                <div class="table-responsive">
                    <table class="table table-hover table-bordered">
                        <thead class="bg-light">
                            <tr>
                                <th>Reference</th>
                                <th>Guest</th>
                                <th>Room</th>
                                <th>Check In/Out</th>
                                <th>Total</th>
                                <th>Status</th>
                                <th>Payment</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($recentBookings as $booking)
                            <tr>
                                <td><strong>{{ $booking->booking_reference }}</strong></td>
                                <td>{{ $booking->guest_name }}</td>
                                <td>{{ $booking->room->room_number ?? 'N/A' }}</td>
                                <td>
                                    <small>{{ $booking->check_in->format('M d') }} - {{ $booking->check_out->format('M d') }}</small>
                                </td>
                                <td>
                                    <strong>TZS {{ number_format($booking->total_price, 0) }}</strong>
                                </td>
                                <td>
                                    @if($booking->status === 'confirmed') <span class="badge badge-success">Confirmed</span>
                                    @elseif($booking->status === 'pending') <span class="badge badge-warning">Pending</span>
                                    @else <span class="badge badge-secondary">{{ ucfirst($booking->status) }}</span> @endif
                                </td>
                                <td>
                                    @if($booking->payment_status === 'paid') <span class="badge badge-success">Paid</span>
                                    @elseif($booking->payment_status === 'partial') <span class="badge badge-info">Partial</span>
                                    @else <span class="badge badge-warning">Pending</span> @endif
                                </td>
                                <td>
                                    <button onclick="viewBookingDetails({{ $booking->id }})" class="btn btn-sm btn-info icon-btn p-1"><i class="fa fa-eye"></i></button>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Booking Details Modal -->
<div class="modal fade" id="bookingDetailsModal" tabindex="-1" role="dialog">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Booking Details</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body" id="bookingDetailsContent">
        <div class="text-center">
          <i class="fa fa-spinner fa-spin fa-2x"></i>
          <p>Loading details...</p>
        </div>
      </div>
    </div>
  </div>
</div>

@endsection

@section('scripts')
<script type="text/javascript" src="{{ asset('dashboard_assets/js/plugins/chart.js') }}"></script>
<script src="{{ asset('dashboard_assets/js/plugins/sweetalert.min.js') }}"></script>
<script>
    // Stats Toggle
    $('#allStatistics').on('show.bs.collapse', function () {
        $('#toggleStatsBtn').html('<i class="fa fa-chevron-up"></i> Hide Operational Stats');
    });
    $('#allStatistics').on('hide.bs.collapse', function () {
        $('#toggleStatsBtn').html('<i class="fa fa-bar-chart"></i> Show Operational Stats');
    });

    // Room Occupancy Chart (Check-ins vs Check-outs per day this week)
    var occupancyData = {
        labels: {!! json_encode(array_column($occupancyData, 'day')) !!},
        datasets: [
            {
                label: "Check-ins",
                fillColor: "rgba(70, 191, 189, 0.7)",
                strokeColor: "rgba(70, 191, 189, 1)",
                pointColor: "rgba(70, 191, 189, 1)",
                pointStrokeColor: "#fff",
                pointHighlightFill: "#fff",
                pointHighlightStroke: "rgba(70, 191, 189, 1)",
                data: {!! json_encode(array_column($occupancyData, 'checkins')) !!}
            },
            {
                label: "Check-outs",
                fillColor: "rgba(247, 70, 74, 0.7)",
                strokeColor: "rgba(247, 70, 74, 1)",
                pointColor: "rgba(247, 70, 74, 1)",
                pointStrokeColor: "#fff",
                pointHighlightFill: "#fff",
                pointHighlightStroke: "rgba(247, 70, 74, 1)",
                data: {!! json_encode(array_column($occupancyData, 'checkouts')) !!}
            }
        ]
    };

    var occupancyCtx = document.getElementById('occupancyChart').getContext('2d');
    new Chart(occupancyCtx).Bar(occupancyData, {
        responsive: true,
        scaleBeginAtZero: true,
        maintainAspectRatio: false
    });

    // Booking Status Pie Chart
    var bookingStatusData = [
        @foreach($bookingStatusData as $status => $count)
        {
            value: {{ $count }},
            color: @if($status == 'Pending')"#F7464A"@elseif($status == 'Confirmed')"#46BFBD"@elseif($status == 'Completed')"#FDB45C"@else"#949FB1"@endif,
            label: "{{ $status }}"
        }@if(!$loop->last),@endif
        @endforeach
    ];
    new Chart($("#bookingStatusChart").get(0).getContext("2d")).Pie(bookingStatusData);

    // Quick Actions
    function quickApprove(requestId) {
        swal({
            title: "Approve Request?",
            text: "This will add the service to the guest bill.",
            type: "warning",
            showCancelButton: true,
            confirmButtonText: "Yes, Approve",
            closeOnConfirm: false
        }, function() {
            updateStatus(requestId, 'approved');
        });
    }

    function updateStatus(id, status) {
        fetch(`{{ url('reception/service-requests') }}/${id}/status`, {
            method: 'PUT',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({ status: status })
        }).then(r => r.json()).then(data => {
            if(data.success) {
                swal("Updated!", "Status changed successfully", "success");
                location.reload();
            } else {
                swal("Error", data.message, "error");
            }
        });
    }

    function viewBookingDetails(id) {
        $('#bookingDetailsModal').modal('show');
        $('#bookingDetailsContent').html('<div class="text-center py-5"><i class="fa fa-spinner fa-spin fa-3x text-primary"></i><p class="mt-2 text-muted">Retrieving booking profile...</p></div>');
        
        fetch(`{{ url('reception/bookings') }}/${id}`)
            .then(r => r.json())
            .then(data => {
                if(data.success) {
                    let b = data.booking;
                    
                    // Helpers for badges
                    let statusClass = b.status === 'confirmed' ? 'success' : (b.status === 'pending' ? 'warning' : 'secondary');
                    let payClass = b.payment_status === 'paid' ? 'success' : (b.payment_status === 'partial' ? 'info' : 'warning');
                    
                    // Format amounts
                    let exchangeRate = {{ $exchangeRate ?? 2500 }};
                    let lockedRate = parseFloat(b.locked_exchange_rate) || exchangeRate;
                    
                    // Financial breakdown
                    let roomTotalTsh = parseFloat(b.total_price) || 0;
                    let serviceTotalTsh = parseFloat(b.total_service_charges_tsh) || 0;
                    let grandTotalTsh = roomTotalTsh + serviceTotalTsh;
                    let paidTsh = parseFloat(b.amount_paid) || 0;
                    
                    let balanceTsh = Math.max(0, grandTotalTsh - paidTsh);
                    
                    // Dynamic percentage calculation
                    let calcPercent = grandTotalTsh > 0 ? Math.min(Math.round((paidTsh / grandTotalTsh) * 100), 100) : 0;
                    
                    let html = `
                        <div class="row">
                            <div class="col-md-12 mb-3">
                                <div class="d-flex justify-content-between align-items-center bg-light p-3 border-left-info" style="border-left: 5px solid #17a2b8; border-radius: 4px;">
                                    <div>
                                        <h4 class="mb-0 text-primary font-weight-bold">#${b.booking_reference}</h4>
                                        <small class="text-muted">Booking Reference</small>
                                    </div>
                                    <div class="text-right">
                                        <span class="badge badge-${statusClass} p-2 px-3">${b.status.toUpperCase()}</span><br>
                                        <span class="badge badge-${payClass} mt-1 p-1 px-2">${b.payment_status.toUpperCase()} PAYMENT</span>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Guest Info -->
                            <div class="col-md-6">
                                <div class="card h-100 border-0 shadow-sm bg-white" style="border-radius: 10px;">
                                    <div class="card-header bg-white border-0 pt-3 pb-0">
                                        <h6 class="text-muted text-uppercase mb-0 font-weight-bold"><i class="fa fa-user-circle"></i> Guest Profile</h6>
                                    </div>
                                    <div class="card-body">
                                        <h5 class="mb-1">${b.guest_name}</h5>
                                        <p class="mb-1 text-muted small"><i class="fa fa-envelope-o text-info width-20"></i> ${b.guest_email}</p>
                                        <p class="mb-0 text-muted small"><i class="fa fa-phone text-success width-20"></i> ${b.guest_phone}</p>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Stay Info -->
                            <div class="col-md-6">
                                <div class="card h-100 border-0 shadow-sm bg-white" style="border-radius: 10px;">
                                    <div class="card-header bg-white border-0 pt-3 pb-0">
                                        <h6 class="text-muted text-uppercase mb-0 font-weight-bold"><i class="fa fa-key"></i> Accommodation</h6>
                                    </div>
                                    <div class="card-body">
                                        <h5 class="mb-1">Room ${b.room ? b.room.room_number : 'N/A'}</h5>
                                        <p class="mb-0 text-muted small"><i class="fa fa-tag text-warning width-20"></i> ${b.room ? b.room.room_type : 'Room Type Not Set'}</p>
                                        <p class="mb-0 text-muted small"><i class="fa fa-users text-primary width-20"></i> Max ${b.room ? b.room.capacity : '2'} Guests</p>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="col-md-12 mt-4">
                                <div class="card border-0 shadow-sm bg-white" style="border-radius: 10px;">
                                    <div class="card-body p-0">
                                        <div class="row no-gutters">
                                            <div class="col-md-3 p-3 border-right text-center">
                                                <small class="text-muted d-block text-uppercase">Check-In</small>
                                                <strong class="h6 mb-0 text-success">${b.check_in}</strong>
                                            </div>
                                            <div class="col-md-3 p-3 border-right text-center">
                                                <small class="text-muted d-block text-uppercase">Check-Out</small>
                                                <strong class="h6 mb-0 text-danger">${b.check_out}</strong>
                                            </div>
                                            <div class="col-md-3 p-3 border-right text-center bg-light">
                                                <small class="text-muted d-block text-uppercase">Bill Breakdown</small>
                                                <small class="d-block text-primary">Stay: TZS ${roomTotalTsh.toLocaleString()}</small>
                                                <small class="d-block text-info">Service: TZS ${serviceTotalTsh.toLocaleString()}</small>
                                            </div>
                                            <div class="col-md-3 p-3 text-center bg-primary text-white">
                                                <small class="text-white-50 d-block text-uppercase font-weight-bold">Grand Total</small>
                                                <strong class="h4 mb-0">TZS ${grandTotalTsh.toLocaleString()}</strong>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-12 mt-3 mb-2">
                                <div class="d-flex justify-content-between mb-1">
                                    <small class="text-muted">Payment Progress</small>
                                    <small class="font-weight-bold ${calcPercent >= 100 ? 'text-success' : 'text-primary'}">${calcPercent}% Paid</small>
                                </div>
                                <div class="progress" style="height: 12px; border-radius: 6px; background-color: #eee;">
                                    <div class="progress-bar progress-bar-striped progress-bar-animated bg-${calcPercent >= 100 ? 'success' : payClass}" role="progressbar" style="width: ${calcPercent}%"></div>
                                </div>
                                <div class="d-flex justify-content-between mt-2">
                                    <div>
                                        <small class="text-muted d-block">RECEIVED AMOUNT</small>
                                        <span class="font-weight-bold text-success">TZS ${paidTsh.toLocaleString()}</span>
                                    </div>
                                    <div class="text-right">
                                        <small class="text-muted d-block uppercase">Outstanding Balance</small>
                                        <span class="h5 mb-0 font-weight-bold ${balanceTsh > 0 ? 'text-danger' : 'text-success'}">
                                            ${balanceTsh > 0 ? 'TZS ' + balanceTsh.toLocaleString() : 'SETTLED'}
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    `;
                    document.getElementById('bookingDetailsContent').innerHTML = html;
                } else {
                    document.getElementById('bookingDetailsContent').innerHTML = `
                        <div class="alert alert-danger">
                            <i class="fa fa-exclamation-triangle"></i> ${data.message}
                        </div>
                    `;
                }
            })
            .catch(error => {
                document.getElementById('bookingDetailsContent').innerHTML = `
                    <div class="alert alert-danger">
                        <i class="fa fa-exclamation-triangle"></i> Connection error. Please try again.
                    </div>
                `;
            });
    }
</script>
@endsection
