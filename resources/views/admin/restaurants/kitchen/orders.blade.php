@extends('dashboard.layouts.app')

@section('content')
<div class="app-title">
    <div>
        <h1><i class="fa fa-bell"></i> Live Food Orders</h1>
        <p>Manage and prepare guest food requests</p>
    </div>
    <ul class="app-breadcrumb breadcrumb">
        <li class="breadcrumb-item"><i class="fa fa-home fa-lg"></i></li>
        <li class="breadcrumb-item"><a href="{{ route('chef-master.dashboard') }}">Kitchen</a></li>
        <li class="breadcrumb-item">Orders</li>
    </ul>
</div>

<div class="row">
    <div class="col-md-3">
        <div class="widget-small primary coloured-icon"><i class="icon fa fa-clock-o fa-3x"></i>
            <div class="info">
                <h4>Pending</h4>
                <p><b>{{ $stats['pending_count'] }}</b></p>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="widget-small success coloured-icon"><i class="icon fa fa-check fa-3x"></i>
            <div class="info">
                <h4>Served Today</h4>
                <p><b>{{ $stats['completed_today'] }}</b></p>
            </div>
        </div>
    </div>
    <div class="col-md-6 text-right">
        <a href="{{ route('admin.restaurants.kitchen.orders.history') }}" class="btn btn-secondary mt-3">
            <i class="fa fa-history"></i> View Order History
        </a>
    </div>
</div>

<div class="row">
    <div class="col-md-12">
        <div class="tile">
            <h3 class="tile-title">Orders Queue</h3>
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Requested At</th>
                            <th>Room / Guest</th>
                            <th>Item Name</th>
                            <th>Qty</th>
                            <th>Notes</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($pendingOrders as $order)
                        @php 
                            $foodId = $order->service_specific_data['food_id'] ?? null;
                            $recipe = $foodId ? \App\Models\Recipe::find($foodId) : null;
                            $itemName = $recipe ? $recipe->name : ($order->service->name ?? 'Unknown Item');
                        @endphp
                        <tr>
                            <td>{{ $order->requested_at->format('H:i') }} <br> <small class="text-muted">{{ $order->requested_at->diffForHumans() }}</small></td>
                            <td>
                                @if($order->is_walk_in)
                                    <span class="badge badge-secondary mb-1">WALK-IN</span><br>
                                    <strong>{{ $order->walk_in_name ?? 'Guest' }}</strong>
                                @else
                                    <strong>Room {{ $order->booking->room->room_number ?? 'Wait List' }}</strong><br>
                                    <small>{{ $order->booking->guest_name }}</small>
                                @endif
                                @if($order->payment_status === 'unpaid')
                                    <br><span class="badge badge-danger small">UNPAID</span>
                                @endif
                            </td>
                            <td>
                                <span class="text-primary font-weight-bold">{{ $itemName }}</span>
                                @if($recipe)
                                    {{-- Recipe details could go here --}}
                                @endif
                            </td>
                            <td><span class="badge badge-info shadow-sm px-3 py-2" style="font-size: 1rem;">{{ $order->quantity }}</span></td>
                            <td><i class="text-danger italic">{{ $order->guest_request ?? 'No special requests' }}</i></td>
                            <td>
                                @if($order->status === 'preparing')
                                    <span class="badge badge-primary">
                                        <i class="fa fa-spinner fa-spin"></i> Preparing
                                    </span>
                                @elseif($order->status === 'approved')
                                    <span class="badge badge-success">Approved</span>
                                @else
                                    <span class="badge badge-warning">{{ ucfirst($order->status) }}</span>
                                @endif
                            </td>
                            <td>
                                <div class="btn-group-vertical w-100">
                                    @if($order->status === 'preparing')
                                        <button class="btn btn-success btn-sm mb-1" onclick="completeOrder({{ $order->id }}, '{{ $itemName }}')">
                                            <i class="fa fa-check"></i> Mark Served
                                        </button>
                                    @else
                                        <button class="btn btn-primary btn-sm mb-1" onclick="startPreparing({{ $order->id }}, '{{ $itemName }}')">
                                            <i class="fa fa-fire"></i> Start Preparing
                                        </button>
                                    @endif
                                    
                                    <button class="btn btn-info btn-sm" onclick="printDocket({{ $order->id }})">
                                        <i class="fa fa-print"></i> Print Docket
                                    </button>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="text-center p-5">
                                <i class="fa fa-smile-o fa-4x text-muted mb-3"></i>
                                <h4>No pending orders!</h4>
                                <p>Kitchen is currently quiet. All orders have been served.</p>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

@endsection

@section('scripts')
<script>
    function startPreparing(id, name) {
        Swal.fire({
            title: 'Start Preparation',
            text: "Are you ready to start preparing " + name + "?",
            icon: 'info',
            showCancelButton: true,
            confirmButtonColor: '#007bff',
            confirmButtonText: 'Yes, Start!'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: "{{ url('restaurant/food/orders') }}/" + id + "/preparing",
                    type: 'POST',
                    data: {
                        _token: "{{ csrf_token() }}"
                    },
                    success: function(response) {
                        if (response.success) {
                            Swal.fire('Success', response.message, 'success').then(() => {
                                window.location.reload();
                            });
                        } else {
                            Swal.fire('Error', response.message, 'error');
                        }
                    },
                    error: function(xhr) {
                        console.error(xhr);
                        var msg = (xhr.responseJSON && xhr.responseJSON.message) ? xhr.responseJSON.message : 'Error starting preparation.';
                        Swal.fire('Error', msg, 'error');
                    }
                });
            }
        });
    }

    function printDocket(orderId) {
        const url = `/restaurant/food/orders/${orderId}/print-docket`;
        window.open(url, 'KitchenDocketPrint', 'width=400,height=600');
    }

    function completeOrder(id, name) {
        Swal.fire({
            title: 'Confirm Service',
            text: "Are you sure you have prepared and served " + name + "?",
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#28a745',
            confirmButtonText: 'Yes, Served!'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: "{{ url('restaurant/food/orders') }}/" + id + "/complete",
                    type: 'POST',
                    data: {
                        _token: "{{ csrf_token() }}"
                    },
                    success: function(response) {
                        if (response.success) {
                            Swal.fire('Success', response.message, 'success').then(() => {
                                window.location.reload();
                            });
                        } else {
                            Swal.fire('Error', response.message, 'error');
                        }
                    },
                    error: function(xhr) {
                        console.error(xhr);
                        var msg = (xhr.responseJSON && xhr.responseJSON.message) ? xhr.responseJSON.message : 'Error completing order.';
                        Swal.fire('Error', msg, 'error');
                    }
                });
            }
        });
    }

    // Auto refresh every 30 seconds
    setTimeout(function() {
        window.location.reload();
    }, 30000);
</script>
@endsection
