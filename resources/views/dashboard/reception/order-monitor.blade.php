@extends('dashboard.layouts.app')

@section('content')
<div class="content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="page-title-box">
                    <div class="page-title-right d-flex align-items-center">
                        <!-- Search Box -->
                        <div class="me-3" style="display: flex;">
                            <div class="input-group">
                                <input type="text" id="orderSearchInput" class="form-control" placeholder="Real-time search..." value="{{ $searchTerm ?? '' }}">
                                <span class="input-group-text bg-primary text-white">
                                    <i class="fa fa-search"></i>
                                </span>
                            </div>
                        </div>

                        <div class="btn-group">
                            <button type="button" class="btn btn-secondary dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <i class="fa fa-filter mr-1"></i> Status: {{ ucfirst($currentStatus) }}
                            </button>
                            <div class="dropdown-menu">
                                <a class="dropdown-item" href="{{ route('reception.orders.monitor', ['status' => 'all', 'type' => $currentType, 'search' => $searchTerm]) }}">All Statuses</a>
                                <a class="dropdown-item" href="{{ route('reception.orders.monitor', ['status' => 'pending', 'type' => $currentType, 'search' => $searchTerm]) }}">Pending</a>
                                <a class="dropdown-item" href="{{ route('reception.orders.monitor', ['status' => 'preparing', 'type' => $currentType, 'search' => $searchTerm]) }}">Preparing</a>
                                <a class="dropdown-item" href="{{ route('reception.orders.monitor', ['status' => 'completed', 'type' => $currentType, 'search' => $searchTerm]) }}">Completed</a>
                                <a class="dropdown-item" href="{{ route('reception.orders.monitor', ['status' => 'cancelled', 'type' => $currentType, 'search' => $searchTerm]) }}">Cancelled</a>
                            </div>
                        </div>
                        <div class="btn-group ml-2">
                            <button type="button" class="btn btn-info dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <i class="fa fa-cutlery mr-1"></i> Category: {{ ucfirst($currentType) }}
                            </button>
                            <div class="dropdown-menu">
                                <a class="dropdown-item" href="{{ route('reception.orders.monitor', ['status' => $currentStatus, 'type' => 'all', 'search' => $searchTerm]) }}">All Categories</a>
                                <a class="dropdown-item" href="{{ route('reception.orders.monitor', ['status' => $currentStatus, 'type' => 'food', 'search' => $searchTerm]) }}">Food Only</a>
                                <a class="dropdown-item" href="{{ route('reception.orders.monitor', ['status' => $currentStatus, 'type' => 'bar', 'search' => $searchTerm]) }}">Drinks Only</a>
                            </div>
                        </div>
                    </div>
                    <h4 class="page-title"><i class="fa fa-television"></i> Live Order Monitor</h4>
                </div>
            </div>
        </div>

        <div class="row mt-3">
            <div class="col-12">
                <div class="card shadow-sm">
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-centered table-nowrap table-hover mb-0">
                                <thead style="background: #f8f9fa;">
                                    <tr>
                                        <th>Order ID</th>
                                        <th>Time</th>
                                        <th>Category</th>
                                        <th>Item(s)</th>
                                        <th>Location</th>
                                        <th>Guest / Customer</th>
                                        <th>Amount</th>
                                        <th>Status</th>
                                        <th>Staff / Handled By</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody id="orderTableBody">
                                    @php
                                        // Pre-group orders by guest key to compute subtotals
                                        $monitorGroups = $orders->groupBy(function($o) {
                                            return $o->is_walk_in
                                                ? 'w_' . ($o->walk_in_name ?? 'general')
                                                : 'b_' . ($o->booking_id ?? 'unknown');
                                        });
                                    @endphp

                                    @forelse($monitorGroups as $gKey => $gOrders)
                                    @php
                                        $gFirst       = $gOrders->first();
                                        $activeGOrders = $gOrders->filter(fn($o) => strtolower($o->status) !== 'cancelled');
                                        $gPaidOrders  = $activeGOrders->filter(fn($o) => in_array($o->payment_status, ['paid', 'room_charge']));
                                        $gDueOrders   = $activeGOrders->filter(fn($o) => !in_array($o->payment_status, ['paid', 'room_charge']));
                                        $gTotalAll    = $activeGOrders->sum('total_price_tsh');  // Full session
                                        $gAlreadyPaid = $gPaidOrders->sum('total_price_tsh');    // Already settled
                                        $gDue         = $gDueOrders->sum('total_price_tsh');     // Amount still owed
                                        $gAllCancelled= $activeGOrders->isEmpty();               // All items were cancelled
                                        $gCount       = $gOrders->count();                       // Including cancelled (for item count)
                                        $gGuest       = ($gFirst->booking) ? $gFirst->booking->guest_name : ($gFirst->walk_in_name ?? 'Walk-in Guest');
                                        $gLocation    = ($gFirst->booking && $gFirst->booking->room) ? $gFirst->booking->room->room_number : 'Walk-in';
                                    @endphp

                                    {{-- Group header row --}}
                                    <tr style="background: {{ $gAllCancelled ? '#f8f9fa' : ($gDue > 0 ? '#fff8f0' : '#f0fff4') }}; border-top: 3px solid {{ $gAllCancelled ? '#6c757d' : ($gDue > 0 ? '#e77a3a' : '#28a745') }};">
                                        <td colspan="10" style="padding: 6px 12px;">
                                            <div class="d-flex align-items-center justify-content-between">
                                                <div>
                                                    <span class="font-weight-bold" style="font-size:0.85rem;">
                                                        @if($gFirst->is_walk_in)
                                                            🚶 {{ $gGuest }}
                                                        @else
                                                            🏨 Room {{ $gLocation }} &mdash; {{ $gGuest }}
                                                        @endif
                                                    </span>
                                                    <span class="badge badge-secondary ml-2">{{ $gCount }} item{{ $gCount > 1 ? 's' : '' }}</span>
                                                    @if($gAllCancelled)
                                                        <span class="badge badge-secondary ml-2"><i class="fa fa-times"></i> All Cancelled</span>
                                                    @elseif($gDue > 0)
                                                        <span class="badge ml-2" style="background:#e77a3a;color:#fff;">DUE: {{ number_format($gDue) }} TZS</span>
                                                    @else
                                                        <span class="badge badge-success ml-2"><i class="fa fa-check"></i> Settled</span>
                                                    @endif
                                                </div>
                                                <span class="font-weight-bold text-muted" style="font-size:0.85rem;">
                                                    Session: {{ number_format($gTotalAll) }} TZS
                                                </span>
                                            </div>
                                        </td>
                                    </tr>

                                    @foreach($gOrders->sortByDesc('requested_at') as $order)
                                    @php
                                        $isDimmedRow = ($order->status === 'cancelled') || in_array($order->payment_status, ['paid', 'room_charge']);
                                    @endphp
                                    <tr style="{{ $isDimmedRow ? 'opacity:0.5;' : '' }}">
                                        <td>#<strong>{{ $order->id }}</strong></td>
                                        <td>{{ $order->requested_at->format('H:i') }} <small class="text-muted">({{ $order->requested_at->diffForHumans() }})</small></td>
                                        <td>
                                            @php
                                              $cat = strtolower($order->service->category ?? '');
                                              $isDrink = ($cat === 'bar' || str_contains($cat, 'beverage') || str_contains($cat, 'drink'));
                                            @endphp

                                            @if($isDrink)
                                                <span class="badge badge-info"><i class="fa fa-glass"></i> Drink</span>
                                            @else
                                                <span class="badge badge-warning"><i class="fa fa-cutlery"></i> Food</span>
                                            @endif
                                        </td>
                                        <td style="{{ $order->status === 'cancelled' ? 'text-decoration: line-through; opacity: 0.6;' : '' }}">
                                            <strong>{{ $order->service_specific_data['item_name'] ?? $order->service->name }}</strong>
                                            @if($order->quantity > 1)
                                                <span class="badge badge-secondary rounded-pill">x{{ $order->quantity }}</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($order->booking && $order->booking->room)
                                                <span class="badge badge-outline-primary">Room {{ $order->booking->room->room_number }}</span>
                                            @elseif($order->is_walk_in)
                                                <span class="badge badge-dark">Walk-in</span>
                                            @else
                                                <span class="text-muted">N/A</span>
                                            @endif
                                        </td>
                                        <td>{{ $order->booking->guest_name ?? $order->walk_in_name ?? 'Walk-in Guest' }}</td>
                                        <td><strong style="{{ $order->status === 'cancelled' ? 'text-decoration: line-through; opacity: 0.6;' : '' }}">{{ number_format($order->total_price_tsh) }} TZS</strong></td>
                                        <td>
                                            @php
                                                $statusClass = [
                                                    'pending' => 'badge-danger',
                                                    'approved' => 'badge-primary',
                                                    'preparing' => 'badge-info',
                                                    'ready' => 'badge-warning',
                                                    'completed' => 'badge-success',
                                                    'cancelled' => 'badge-danger'
                                                ][$order->status] ?? 'badge-secondary';
                                            @endphp
                                            <span class="badge {{ $statusClass }}">{{ ucfirst($order->status) }}</span>
                                            @if(in_array($order->payment_status, ['paid', 'room_charge']))
                                                <div class="mt-1"><span class="badge badge-success small" style="font-size: 0.7rem;"><i class="fa fa-check-circle"></i> PAID</span></div>
                                            @else
                                                <div class="mt-1"><span class="badge badge-warning small" style="font-size: 0.7rem;"><i class="fa fa-clock-o"></i> UNPAID</span></div>
                                            @endif
                                        </td>
                                        <td>
                                            @php
                                                $madeBy = 'System/Self';
                                                if ($order->reception_notes && (str_contains($order->reception_notes, 'POS Order by Waiter:') || str_contains($order->reception_notes, 'Order by Waiter:'))) {
                                                     $prefix = str_contains($order->reception_notes, 'POS Order by Waiter:') ? 'POS Order by Waiter:' : 'Order by Waiter:';
                                                     $parts = explode($prefix, $order->reception_notes);
                                                     $textAfterWaiter = $parts[1] ?? '';
                                                     $madeBy = trim(explode('|', explode('[', explode(' - Msg:', $textAfterWaiter)[0])[0])[0] ?? 'Staff');
                                                 }
                                            @endphp
                                            <div class="staff-info">
                                                <div title="Ordered By"><small><strong>Req:</strong> {{ $madeBy }}</small></div>
                                                
                                                @if($order->approvedBy)
                                                    <div title="Served/Approved By"><small><strong>Srv:</strong> {{ $order->approvedBy->name }}</small></div>
                                                @endif

                                                @if($order->paidBy)
                                                    <div title="Payment Received By" class="text-success">
                                                        <small><strong>Paid:</strong> {{ $order->paidBy->name }}</small>
                                                        @if($order->payment_method)
                                                            <br><small class="badge badge-success px-1 py-0" style="font-size: 0.6rem;">{{ strtoupper($order->payment_method) }}</small>
                                                        @endif
                                                    </div>
                                                @endif

                                                @if($order->cancelledBy)
                                                    <div title="Cancelled By" class="text-danger">
                                                        <small><strong>Can:</strong> {{ $order->cancelledBy->name }}</small>
                                                    </div>
                                                @endif
                                            </div>
                                        </td>
                                        <td class="text-muted text-center" style="font-size:0.8rem;">—</td>
                                    </tr>
                                    @endforeach

                                    {{-- Group subtotal footer row with single Print Bill --}}
                                    @php
                                        $printGroupUrl = route('reception.orders.print-group', [
                                            'is_walk_in' => $gFirst->is_walk_in ? 1 : 0,
                                            'identifier' => $gFirst->is_walk_in ? $gFirst->walk_in_name : $gFirst->booking_id
                                        ]);
                                    @endphp
                                    <tr style="background: {{ $gAllCancelled ? '#f8f9fa' : ($gDue > 0 ? '#fff3e0' : '#d4edda') }}; border-bottom: 3px solid {{ $gAllCancelled ? '#6c757d' : ($gDue > 0 ? '#e77a3a' : '#28a745') }};">
                                        <td colspan="3" style="padding: 8px 12px; text-align: right; vertical-align: middle;">
                                            <span class="text-muted small font-weight-bold">SUBTOTAL &mdash;
                                                @if($gFirst->is_walk_in) {{ $gGuest }}
                                                @else Room {{ $gLocation }}
                                                @endif
                                            </span>
                                        </td>
                                        <td colspan="3" style="padding: 8px 12px; vertical-align: middle;">
                                            @if($gAllCancelled)
                                            <div style="font-size:0.9rem; color:#6c757d; font-weight:bold;"><i class="fa fa-times-circle"></i> All Cancelled</div>
                                            @else
                                                @if($gTotalAll > 0)
                                                <div style="font-size:0.8rem; color:#555;">Session: <strong>{{ number_format($gTotalAll) }}</strong> TZS</div>
                                                @endif
                                                @if($gAlreadyPaid > 0)
                                                <div style="font-size:0.8rem; color:#28a745;">Paid: &minus; {{ number_format($gAlreadyPaid) }} TZS</div>
                                                @endif
                                                @if($gDue > 0)
                                                <div style="font-size:1rem; color:#e77a3a; font-weight:bold;">Due: {{ number_format($gDue) }} TZS</div>
                                                @else
                                                <div style="font-size:0.9rem; color:#28a745; font-weight:bold;"><i class="fa fa-check-circle"></i> Fully Settled</div>
                                                @endif
                                            @endif
                                        </td>
                                        <td colspan="4" style="padding: 8px 12px; vertical-align: middle;">
                                            <button type="button" class="btn btn-sm btn-primary"
                                                onclick="window.open('{{ $printGroupUrl }}', 'Print', 'width=800,height=600')"
                                                title="Print full bill for this guest">
                                                <i class="fa fa-print mr-1"></i> Print Bill
                                            </button>
                                        </td>
                                    </tr>

                                    @empty
                                    <tr id="noResultsRow">
                                        <td colspan="10" class="text-center py-5">
                                            <div class="text-muted">
                                                <i class="fa fa-clipboard fa-4x mb-3" style="opacity: 0.3;"></i>
                                                <p class="h5">No active orders found.</p>
                                                <small>Try changing filters or search terms.</small>
                                            </div>
                                        </td>
                                    </tr>
                                    @endforelse
                                    <tr id="jsNoResults" style="display: none;">
                                        <td colspan="10" class="text-center py-5">
                                            <div class="text-muted">
                                                <p class="h5">No matching orders found.</p>
                                            </div>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        <div class="mt-4">
                            {{ $orders->appends(request()->except('page'))->links() }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    function printDocket(id) {
        // Use the waiter print route which is more permissive or Ensure the kitchen one is fixed
        let url = "{{ route('admin.restaurants.kitchen.orders.print-docket', ':id') }}".replace(':id', id);
        const printWindow = window.open(url, '_blank', 'width=400,height=600');
        printWindow.onload = function() {
            printWindow.print();
        };
    }

    // Real-time Client Side Search
    document.getElementById('orderSearchInput').addEventListener('input', function() {
        let value = this.value.toLowerCase();
        let rows = document.querySelectorAll('#orderTableBody tr:not(#jsNoResults)');
        let hasResults = false;
        
        rows.forEach(row => {
            if (row.id !== 'noResultsRow') { 
                let text = row.innerText.toLowerCase();
                let match = text.indexOf(value) > -1;
                row.style.display = match ? '' : 'none';
                if(match) hasResults = true;
            }
        });

        // Show/Hide JS No Results message
        let jsNoResults = document.getElementById('jsNoResults');
        if (jsNoResults) {
            jsNoResults.style.display = (hasResults || value === '') ? 'none' : '';
        }
    });

    // Auto-refresh every 30 seconds for real-time monitoring
    // Only refresh if search is empty to avoid interrupting user search
    setTimeout(() => {
        let searchVal = document.getElementById('orderSearchInput').value;
        if (!searchVal) {
            window.location.reload();
        }
    }, 30000);
</script>

<style>
    .badge-outline-primary {
        border: 1px solid #007bff;
        color: #007bff;
        background: transparent;
    }
    .page-title-box .input-group {
        width: 250px;
    }
    .table-centered td {
        vertical-align: middle !important;
    }
</style>
@endsection
