@extends('dashboard.layouts.app')

@section('content')
<div class="app-title">
    <div>
        <h1><i class="fa fa-history"></i> My Order History</h1>
        <p>View and track the status of orders you have submitted</p>
    </div>
    <ul class="app-breadcrumb breadcrumb">
        <li class="breadcrumb-item"><i class="fa fa-home fa-lg"></i></li>
        <li class="breadcrumb-item"><a href="{{ route('waiter.dashboard') }}">Dashboard</a></li>
        <li class="breadcrumb-item">History</li>
    </ul>
</div>

<div class="row">
    <div class="col-md-12">
        <div class="tile">
            <h3 class="tile-title">Recent Orders</h3>
            <div class="table-responsive">
                <table class="table table-hover table-bordered">
                    <thead>
                        <tr>
                            <th>DateTime</th>
                            <th>Guest / Room</th>
                            <th>Item Ordered</th>
                            <th>Qty</th>
                            <th>Total (TZS)</th>
                            <th>Payment</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($orders as $order)
                        <tr>
                            <td>{{ $order->requested_at->format('M d, H:i') }}</td>
                            <td>
                                @if($order->is_walk_in)
                                    <span class="badge badge-info">Walk-in</span> {{ $order->walk_in_name }}
                                @else
                                    <span class="badge badge-primary">Room {{ $order->booking->room->room_number ?? 'N/A' }}</span> {{ $order->booking->guest_name }}
                                @endif
                            </td>
                            <td>{{ $order->service->name }}</td>
                            <td>{{ $order->quantity }}</td>
                            <td>{{ number_format($order->total_price_tsh) }}</td>
                            <td>
                                @if($order->payment_status === 'paid')
                                    <span class="badge badge-success">PAID ({{ $order->payment_method }})</span>
                                @else
                                    <span class="badge badge-warning">BILL TO ROOM</span>
                                @endif
                            </td>
                            <td>
                                @php
                                    $statusClass = [
                                        'pending' => 'badge-secondary',
                                        'preparing' => 'badge-info',
                                        'ready' => 'badge-warning',
                                        'completed' => 'badge-success',
                                        'cancelled' => 'badge-danger'
                                    ][$order->status] ?? 'badge-secondary';
                                @endphp
                                <span class="badge {{ $statusClass }}">{{ strtoupper($order->status) }}</span>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="text-center text-muted">No orders found.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="mt-3">
                {{ $orders->links() }}
            </div>
        </div>
    </div>
</div>
@endsection
