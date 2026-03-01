@extends('dashboard.layouts.app')

@section('content')
<div class="app-title mb-0" style="background: #1a1a1a; color: #fff; border-bottom: 2px solid #333; padding: 15px 30px;">
    <div class="d-flex justify-content-between align-items-center w-100">
        <div>
            <h1 class="mb-0 text-white" style="font-size: 1.8rem;"><i class="fa fa-television mr-2 text-warning"></i> KITCHEN DISPLAY MONITOR</h1>
            <p class="mb-0 text-muted" style="font-size: 0.9rem;">READ-ONLY | REAL-TIME UPDATES</p>
        </div>
        <div class="d-flex align-items-center">
            <button id="testVoiceBtn" class="btn btn-sm btn-info mr-3">
                <i class="fa fa-microphone mr-1"></i> TEST KITCHEN VOICE
            </button>
            <div id="connectionStatus" class="badge badge-success mr-3 p-2">
                <i class="fa fa-refresh fa-spin mr-1"></i> LIVE MONITORING
            </div>
            <div id="currentTime" class="h4 mb-0 font-weight-bold" style="min-width: 120px; color: #fff;"></div>
        </div>
    </div>
</div>

<!-- Audio Interaction Overlay -->
<div id="audioOverlay" style="position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.98); z-index: 9999; display: flex; flex-direction: column; align-items: center; justify-content: center; cursor: pointer; text-align: center;">
    <div class="p-5" style="border: 3px solid #f39c12; border-radius: 30px; background: rgba(255,165,0,0.05); max-width: 600px;">
        <i class="fa fa-microphone text-warning mb-4 animate-pulse" style="font-size: 10rem;"></i>
        <h1 class="text-white mb-3" style="font-size: 3.5rem; font-weight: 800;">ACTIVATE VOICE MONITOR</h1>
        <p class="text-white-50 h3 mt-3">TAP SCREEN to enable Automated Kitchen Voice alerts.</p>
        <div class="mt-5 btn btn-warning btn-lg px-5 py-3" style="font-size: 1.5rem; border-radius: 50px; font-weight: bold; box-shadow: 0 10px 30px rgba(243, 156, 18, 0.4);">
            CLICK TO START NOW
        </div>
    </div>
</div>

<div class="container-fluid py-4" style="background: #121212; min-height: 90vh;">
    <!-- Hidden Tracking Fields -->
    <div id="trackingContainer" style="display: none;">
        <div id="currentOrderIds">{{ $pendingOrders->pluck('id')->implode(',') }}</div>
        <div id="currentCancelledIds">{{ $pendingOrders->where('status', 'cancelled')->pluck('id')->implode(',') }}</div>
        <!-- Latest New Order Data for Voice -->
        <div id="latestOrderInfo">
            @php 
                $latest = $pendingOrders->where('status', '!=', 'cancelled')->sortByDesc('requested_at')->first();
                if ($latest) {
                    $lGuest = $latest->is_walk_in ? ($latest->walk_in_name ?? 'Guest') : ($latest->booking->guest_name ?? 'Room Guest');
                    $lItem = $latest->service_specific_data['item_name'] ?? ($latest->service->name ?? 'Dish');
                    echo "New order for $lGuest: $lItem";
                }
            @endphp
        </div>
        <div id="latestCancelInfo">
            @php 
                $latestC = $pendingOrders->where('status', 'cancelled')->sortByDesc('updated_at')->first();
                if ($latestC) {
                    $cGuest = $latestC->is_walk_in ? ($latestC->walk_in_name ?? 'Guest') : ($latestC->booking->guest_name ?? 'Room Guest');
                    echo "Order for $cGuest has been cancelled. Please stop preparation.";
                }
            @endphp
        </div>
    </div>

    <div class="row" id="kdsBoard">
        @php
            $groupedOrders = $pendingOrders->groupBy(function($item) {
                return $item->is_walk_in ? 'w_' . ($item->walk_in_name ?? 'General') : 'b_' . ($item->booking_id ?? 'unknown');
            });
        @endphp

        @forelse($groupedOrders as $groupKey => $orders)
            @php
                $first = $orders->first();
                $oldestRequest = $orders->sortBy('requested_at')->first()->requested_at;
                $minutesElapsed = round(abs(now()->diffInMinutes($oldestRequest)));
                $formattedTime = \Carbon\Carbon::parse($oldestRequest)->format('H:i:s');
                
                $isCancelledGroup = $orders->every(fn($o) => $o->status === 'cancelled');
                
                $cardBorder = 'border-success';
                if ($isCancelledGroup) {
                    $cardBorder = 'border-danger';
                } elseif ($minutesElapsed > 15) {
                    $cardBorder = 'border-warning';
                } elseif ($minutesElapsed > 30) {
                    $cardBorder = 'border-danger';
                }

                $anyPreparing = $orders->contains(fn($o) => $o->status === 'preparing');

                $creator = 'Staff';
                if ($first->reception_notes && str_contains($first->reception_notes, 'Waiter: ')) {
                    $parts = explode('Waiter: ', $first->reception_notes);
                    $raw = $parts[1] ?? 'Waiter';
                    $clean = explode(' - Msg:', $raw);
                    $nameOnly = explode(' | ', $clean[0]);
                    $creator = trim($nameOnly[0]);
                }
            @endphp
            
            <div class="col-md-4 col-lg-3 mb-4 ticket-wrapper">
                <div class="card h-100 shadow-lg border-top {{ $cardBorder }} {{ $isCancelledGroup ? 'dimmed-ticket' : '' }}" style="border-width: 8px; background: #222; border-radius: 8px; position: relative; transition: transform 0.3s ease;">
                    
                    @if($isCancelledGroup)
                    <div style="position: absolute; top:0; left:0; width: 100%; height: 100%; background: rgba(220, 53, 69, 0.1); display: flex; align-items: center; justify-content: center; z-index: 10; pointer-events: none;">
                        <div class="badge badge-danger p-3 shadow-lg" style="transform: rotate(-15deg); font-size: 1.5rem; border: 4px solid #fff; box-shadow: 0 0 20px rgba(0,0,0,0.5);">ORDER CANCELLED</div>
                    </div>
                    @endif

                    <div class="card-header d-flex justify-content-between align-items-start py-2" style="background: rgba(255,255,255,0.05); color: #fff; border-bottom: 1px solid #333;">
                        <div>
                            <span class="badge {{ $first->is_walk_in ? 'badge-secondary' : 'badge-primary' }} mb-1" style="font-size: 0.75rem;">
                                {{ $first->is_walk_in ? 'WALK-IN' : 'ROOM ORDER' }}
                            </span>
                            <h5 class="card-title mb-0 font-weight-bold text-truncate" style="max-width: 150px; color: #fff;">
                                {{ $first->is_walk_in ? ($first->walk_in_name ?? 'Guest') : ($first->booking->guest_name ?? 'Guest') }}
                            </h5>
                            @if(!$first->is_walk_in)
                                <small class="text-info font-weight-bold" style="font-size: 1rem;">ROOM: {{ $first->booking->room->room_number ?? 'Wait List' }}</small>
                            @endif
                        </div>
                        <div class="text-right">
                            <h4 class="mb-0 {{ $minutesElapsed > 15 ? 'text-warning' : 'text-success' }}" style="font-weight: 900; line-height: 1;">{{ $minutesElapsed }}<small>m</small></h4>
                            <div class="text-white-50" style="font-size: 0.7rem;">IN: {{ $formattedTime }}</div>
                        </div>
                    </div>
                    
                    <div class="card-body p-0" style="max-height: 400px; overflow-y: auto;">
                        <ul class="list-group list-group-flush" style="background: transparent;">
                            @php
                                $itemGroups = $orders->groupBy(function($o) {
                                    $name = $o->service_specific_data['item_name'] ?? ($o->service->name ?? 'Unknown');
                                    $note = $o->guest_request;
                                    if (!$note && $o->reception_notes && str_contains($o->reception_notes, '- Msg: ')) {
                                        $parts = explode('- Msg: ', $o->reception_notes);
                                        $note = $parts[1] ?? 'None';
                                    }
                                    return $name . '||' . ($note ?? '');
                                });
                            @endphp

                            @foreach($itemGroups as $groupData => $items)
                                @php
                                    $order = $items->first();
                                    $itemName = explode('||', $groupData)[0];
                                    $totalQty = $items->sum('quantity');
                                    $isItemCancelled = $order->status === 'cancelled';
                                    $isItemPreparing = $items->contains(fn($o) => $o->status === 'preparing');
                                @endphp
                                <li class="list-group-item d-flex justify-content-between align-items-start {{ $isItemPreparing ? 'bg-preparing' : '' }} {{ $isItemCancelled ? 'bg-cancelled' : '' }}" 
                                    style="background: transparent; border-color: #333; color: #eee; padding: 12px 15px;">
                                    <div class="flex-grow-1">
                                        <div class="d-flex align-items-center">
                                            <span class="qty-badge {{ $isItemCancelled ? 'bg-secondary' : 'bg-orange' }}">{{ $totalQty }}</span>
                                            <div>
                                                <div class="h5 mb-0 font-weight-bold {{ $isItemCancelled ? 'text-strikethrough text-muted' : 'text-white' }}">
                                                    {{ $itemName }}
                                                </div>
                                                @php
                                                    $note = $order->guest_request;
                                                    if (!$note && $order->reception_notes && str_contains($order->reception_notes, '- Msg: ')) {
                                                        $parts = explode('- Msg: ', $order->reception_notes);
                                                        $note = $parts[1] ?? null;
                                                    }
                                                @endphp
                                                @if($note)
                                                    <div class="note-box">
                                                        <i class="fa fa-exclamation-triangle"></i> {{ $note }}
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                    @if($isItemPreparing)
                                        <span class="badge badge-primary px-2 py-1"><i class="fa fa-fire"></i></span>
                                    @endif
                                </li>
                            @endforeach
                        </ul>
                    </div>

                    <div class="card-footer p-2 bg-transparent border-top" style="border-color: #333; background: rgba(0,0,0,0.2);">
                        <div class="d-flex justify-content-between align-items-end">
                            <div class="text-left w-50">
                                <small class="text-muted d-block" style="font-size: 0.65rem; text-transform: uppercase;">BY WAITER:</small>
                                <div class="text-white font-weight-bold" style="font-size: 0.9rem;">
                                    <i class="fa fa-user-circle mr-1 text-info"></i> {{ strtoupper($creator) }}
                                </div>
                            </div>
                            <div class="text-right w-50">
                                <small class="text-muted d-block" style="font-size: 0.65rem; text-transform: uppercase;">PREP STATUS:</small>
                                @if($isCancelledGroup)
                                    <span class="badge badge-danger shadow-sm">CANCELLED</span>
                                @elseif($anyPreparing)
                                    <span class="badge badge-primary shadow-sm"><i class="fa fa-fire mr-1"></i> COOKING</span>
                                @else
                                    <span class="badge badge-success shadow-sm">ARRIVED</span>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12 text-center py-5">
                <i class="fa fa-smile-o fa-5x text-success mb-3 opacity-25"></i>
                <h1 class="text-white font-weight-bold">KITCHEN IS CLEAR</h1>
                <p class="text-muted h4 mt-3">All today's orders are served.</p>
            </div>
        @endforelse
    </div>
</div>

<style>
    body { background-color: #121212 !important; overflow-x: hidden; }
    .app-sidebar, .app-header { display: none !important; }
    .app { padding-left: 0 !important; }
    main.app-content { margin-left: 0 !important; padding: 0 !important; }
    
    .bg-preparing { background-color: rgba(0, 123, 255, 0.15) !important; border-left: 5px solid #007bff !important; }
    .bg-cancelled { background-color: rgba(220, 53, 69, 0.15) !important; border-left: 5px solid #dc3545 !important; }
    .dimmed-ticket { opacity: 0.6; filter: grayscale(0.8); }
    .text-strikethrough { text-decoration: line-through; }
    
    .qty-badge {
        font-size: 1.2rem;
        padding: 4px 14px;
        border-radius: 6px;
        font-weight: 900;
        margin-right: 15px;
        color: #fff;
    }
    .bg-orange { background: #e67e22; border: 1px solid #d35400; }
    
    .note-box {
        background: rgba(255, 193, 7, 0.1);
        color: #ffc107;
        padding: 5px 10px;
        border-radius: 4px;
        font-size: 0.85rem;
        margin-top: 6px;
        border-left: 4px solid #ffc107;
        font-weight: bold;
    }

    .ticket-wrapper:hover { transform: translateY(-5px); }
    .animate-pulse { animation: pulse 2s cubic-bezier(0.4, 0, 0.6, 1) infinite; }
    @keyframes pulse { 0%, 100% { opacity: 1; } 50% { opacity: .5; } }
</style>

@endsection

@section('scripts')
<script>
    // Voice Context
    let currentOrderIds = document.getElementById('currentOrderIds').innerText.split(',').filter(id => id !== '');
    let currentCancelledIds = document.getElementById('currentCancelledIds').innerText.split(',').filter(id => id !== '');
    let speechEnabled = false;

    // Automated Voice System
    function speak(text, priority = false) {
        if (!speechEnabled) return;

        // Cancel existing speech for priority messages
        if (priority) window.speechSynthesis.cancel();

        const utterance = new SpeechSynthesisUtterance(text);
        utterance.rate = 0.9; // Slightly slower for clarity
        utterance.pitch = 1.0;
        utterance.volume = 1.0;
        
        // Find a better voice if available
        const voices = window.speechSynthesis.getVoices();
        const preferred = voices.find(v => v.name.includes('English') || v.lang.includes('en'));
        if (preferred) utterance.voice = preferred;

        window.speechSynthesis.speak(utterance);
    }

    document.getElementById('audioOverlay').addEventListener('click', function() {
        this.style.display = 'none';
        speechEnabled = true;
        speak("Voice Monitor Activated. Welcome back, Chef.");
    });

    document.getElementById('testVoiceBtn').addEventListener('click', function() {
        speechEnabled = true;
        speak("Testing kitchen voice system. Sound is working correctly.");
    });

    function updateTime() {
        const now = new Date();
        const timeString = now.toLocaleTimeString('en-GB', { hour: '2-digit', minute: '2-digit', second: '2-digit' });
        document.getElementById('currentTime').innerText = timeString;
    }
    
    setInterval(updateTime, 1000);
    updateTime();

    function refreshKDS() {
        $.ajax({
            url: window.location.href,
            type: 'GET',
            cache: false,
            success: function(html) {
                const parser = new DOMParser();
                const doc = parser.parseFromString(html, 'text/html');
                
                const newOrderIds = doc.getElementById('currentOrderIds').innerText.split(',').filter(id => id !== '');
                const newCancelledIds = doc.getElementById('currentCancelledIds').innerText.split(',').filter(id => id !== '');

                // NEW ORDERS
                const trulyNew = newOrderIds.filter(id => !currentOrderIds.includes(id));
                if (trulyNew.length > 0) {
                    const voiceMsg = doc.getElementById('latestOrderInfo').innerText;
                    speak(voiceMsg, true);
                    showNotification('New Order Arrived!', 'bg-success');
                }

                // CANCELLATIONS
                const trulyCancelled = newCancelledIds.filter(id => !currentCancelledIds.includes(id));
                if (trulyCancelled.length > 0) {
                    const cancelMsg = doc.getElementById('latestCancelInfo').innerText;
                    speak(cancelMsg, true);
                    showNotification('Order CANCELLED!', 'bg-danger');
                }

                currentOrderIds = newOrderIds;
                currentCancelledIds = newCancelledIds;

                document.getElementById('kdsBoard').innerHTML = doc.getElementById('kdsBoard').innerHTML;
                document.getElementById('latestOrderInfo').innerHTML = doc.getElementById('latestOrderInfo').innerHTML;
                document.getElementById('latestCancelInfo').innerHTML = doc.getElementById('latestCancelInfo').innerHTML;
                
                $('#connectionStatus').html('<i class="fa fa-refresh fa-spin mr-1"></i> LIVE MONITORING').removeClass('badge-danger').addClass('badge-success');
            },
            error: function() {
                $('#connectionStatus').html('<i class="fa fa-warning mr-1"></i> CONNECTION LOST').removeClass('badge-success').addClass('badge-danger');
            }
        });
    }

    setInterval(refreshKDS, 10000);

    function showNotification(text, bgColor) {
        Swal.fire({
            toast: true,
            position: 'top-end',
            showConfirmButton: false,
            timer: 4000,
            timerProgressBar: true,
            icon: bgColor.includes('success') ? 'info' : 'warning',
            title: text,
            background: bgColor === 'bg-danger' ? '#721c24' : '#155724',
            color: '#fff'
        });
    }
</script>
@endsection
