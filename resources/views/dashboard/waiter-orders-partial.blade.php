@php
    $groupedOrders = $orders->groupBy(function($item) {
        $id = $item->is_walk_in ? 'w_' . ($item->walk_in_name ?? 'General') : 
              ($item->day_service_id ? 'c_' . $item->day_service_id : 'b_' . ($item->booking_id ?? 'unknown'));
        return $id . '_' . $item->payment_status;
    });
@endphp

@forelse($groupedOrders as $groupKey => $orderGroup)
    @php
        $first         = $orderGroup->first();
        $latestRequest = $orderGroup->sortByDesc('requested_at')->first()->requested_at;
        $totalAmount   = $orderGroup->filter(fn($o) => strtolower($o->status) !== 'cancelled')->sum('total_price_tsh');
        $payStatus     = $first->payment_status;

        // Skip "Empty Unpaid" cards (All items cancelled) unless specifically viewing cancelled tab
        if ($payStatus !== 'paid' && $payStatus !== 'room_charge' && $totalAmount <= 0) {
            continue;
        }

        $headerClass   = $payStatus === 'paid' ? 'paid-h' : ($payStatus === 'room_charge' ? 'room-h' : 'pending-h');
        $badgeClass    = $payStatus === 'paid' ? 'paid-badge' : ($payStatus === 'room_charge' ? 'room-badge' : 'pending-badge');
        $badgeLabel    = $payStatus === 'paid' ? '✔ PAID' : ($payStatus === 'room_charge' ? '🏨 ROOM CHARGE' : '⏳ UNPAID');

        $printUrl = route('waiter.orders.print-group', [
            'is_walk_in' => $first->is_walk_in ? 1 : 0,
            'is_ceremony' => $first->day_service_id ? 1 : 0,
            'identifier' => $first->is_walk_in ? $first->walk_in_name : ($first->day_service_id ?: $first->booking_id)
        ]);
    @endphp

    <div class="order-card">
        {{-- Header --}}
        <div class="order-card-header {{ $headerClass }}">
            <div>
                @if($first->is_walk_in)
                    <div class="guest-name">{{ $first->walk_in_name ?? 'Walk-in Guest' }}</div>
                    <div class="guest-meta">🚶 Walk-in &nbsp;·&nbsp; {{ $latestRequest->format('d M, H:i') }}</div>
                @elseif($first->day_service_id)
                    <div class="guest-name">{{ $first->dayService->guest_name ?? 'Ceremony Guest' }}</div>
                    <div class="guest-meta">🎉 Ceremony: {{ $first->dayService->service_reference ?? 'N/A' }} &nbsp;·&nbsp; {{ $latestRequest->format('d M, H:i') }}</div>
                @else
                    <div class="guest-name">Room {{ $first->booking->room->room_number ?? 'N/A' }}</div>
                    <div class="guest-meta">{{ $first->booking->guest_name ?? '' }} &nbsp;·&nbsp; {{ $latestRequest->format('d M, H:i') }}</div>
                @endif
            </div>
            <span class="pay-pill-badge {{ $badgeClass }}">{{ $badgeLabel }}</span>
        </div>

        {{-- Items --}}
        <div class="order-items">
            @foreach($orderGroup as $order)
                @php
                    $isCancelled = strtolower($order->status) === 'cancelled';
                    $sClass = 's-' . $order->status;
                    $itemName = $order->service_specific_data['item_name'] ?? ($order->service->name ?? 'Item');
                    $rowStyle = $isCancelled ? 'opacity: 0.5; text-decoration: line-through;' : '';
                @endphp
                <div class="order-item-row" style="{{ $rowStyle }}">
                    <div class="item-left">
                        <div class="item-name">{{ $itemName }}</div>
                        <div class="item-qty">×{{ $order->quantity }}</div>
                    </div>
                    <div class="item-right">
                        <div class="item-price">{{ number_format($order->total_price_tsh) }} <small style="font-weight:500; color:#bbb;">TZS</small></div>
                        <div style="margin-top:4px; display: flex; align-items: center; gap: 6px;">
                            <span class="item-status {{ $sClass }}">{{ strtoupper($order->status) }}</span>
                            
                            @php
                                $minutesSinceOrder = $order->requested_at->diffInMinutes(now());
                                $isLikelyCooking = $minutesSinceOrder >= 15;
                                $canCancel = in_array($order->status, ['pending', 'preparing']) && in_array($order->payment_status, ['pending', 'unpaid']);
                            @endphp

                            @if($canCancel)
                                <button class="cancel-x @if($isLikelyCooking) warn-old @endif" 
                                    onclick="openCancelModal({{ $order->id }}, '{{ addslashes($itemName) }}')" 
                                    title="{{ $isLikelyCooking ? 'Likely Cooking ( > 15 mins)' : 'Cancel' }}">
                                    ✕ @if($isLikelyCooking) <small style="font-size:0.5rem; font-weight:bold;">LOCK</small> @endif
                                </button>
                                @if($isLikelyCooking)
                                    <span style="font-size:0.55rem; color:#ffc107; font-weight:800; letter-spacing:0.5px;">PROBABLY COOKED</span>
                                @endif
                            @elseif(in_array($order->payment_status, ['paid', 'room_charge']) && in_array($order->status, ['pending', 'preparing', 'completed']))
                                <span class="badge badge-light-success" style="font-size:0.6rem; color:#28a745; background:rgba(40,167,69,0.1); border-radius:4px; padding:2px 6px;"><i class="fa fa-lock"></i> SECURED</span>
                            @endif
                        </div>    {{-- Staff info --}}
                            @if($order->status === 'cancelled' && $order->cancelledBy)
                                <div class="text-danger mt-1" style="font-size: 0.6rem; font-weight: 700;">Can by: {{ $order->cancelledBy->name }}</div>
                            @elseif($order->payment_status === 'paid' && $order->paidBy)
                                <div class="text-success mt-1" style="font-size: 0.6rem; font-weight: 700;">Paid to: {{ $order->paidBy->name }}</div>
                            @endif
                    </div>
                </div>
            @endforeach
        </div>

        {{-- Footer --}}
        <div class="order-card-footer">
            <div class="order-total-row">
                <span class="order-total-label">Order Total</span>
                <div>
                    <div class="order-total-value">{{ number_format($totalAmount) }} TZS</div>
                    @if($payStatus === 'paid' && $first->payment_method)
                        <div class="order-method-text">via {{ strtoupper(str_replace('_', ' ', $first->payment_method)) }}</div>
                    @endif
                </div>
            </div>

            <div class="action-btns">
                <button class="action-btn btn-print" onclick="window.open('{{ $printUrl }}', 'Print', 'width=800,height=600')">
                    <i class="fa fa-print"></i> Bill
                </button>
                @if(!in_array($payStatus, ['paid', 'room_charge']) && !$first->day_service_id)
                        @php
                        $resp = ($first->booking) ? ($first->booking->payment_responsibility ?? 'self') : 'self';
                        $isCorp = ($first->booking && $first->booking->is_corporate_booking) ? 1 : 0;
                    @endphp
                    <button class="action-btn btn-pay"
                        onclick="openPaymentModal({{ $first->is_walk_in ? 1 : 0 }}, '{{ $first->is_walk_in ? $first->walk_in_name : ($first->day_service_id ?: $first->booking_id) }}', '{{ number_format($totalAmount) }}', {{ $first->day_service_id ? 1 : 0 }}, '{{ $resp }}', {{ $isCorp }})">
                        <i class="fa fa-money"></i> Pay
                    </button>
                <a class="action-btn btn-add"
                    href="{{ route('waiter.dashboard', [
                        $first->is_walk_in ? 'walk_in' : ($first->day_service_id ? 'day_service_id' : 'room_id') => 
                        $first->is_walk_in ? $first->walk_in_name : ($first->day_service_id ?: $first->booking_id)
                    ]) }}">
                    <i class="fa fa-plus"></i> Add
                </a>
                @endif
            </div>
        </div>
    </div>
@empty
    <div class="empty-state">
        <i class="fa fa-inbox"></i>
        <p>No orders yet.<br>Head to POS to take an order.</p>
    </div>
@endforelse

<div class="pagination-wrap ajax-pagination">
    {{ $orders->links() }}
</div>
