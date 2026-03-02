@extends('dashboard.layouts.app')

@section('content')
<div class="app-title">
  <div>
    <h1><i class="fa fa-dashboard"></i> Bar Keeper Dashboard</h1>
    <p>Manage stock transfers and inventory</p>
  </div>
  <ul class="app-breadcrumb breadcrumb">
    <li class="breadcrumb-item"><i class="fa fa-home fa-lg"></i></li>
    <li class="breadcrumb-item"><a href="#">Dashboard</a></li>
  </ul>
</div>

<!-- Statistics Cards -->
<div class="row mb-4">
  <div class="col-md-3 col-sm-6">
    <div class="widget-small danger coloured-icon">
      <i class="icon fa fa-cutlery fa-3x"></i>
      <div class="info">
        <h4>Pending Orders</h4>
        <p><b>{{ $totalPendingOrders }}</b></p>
      </div>
    </div>
  </div>
  <div class="col-md-3 col-sm-6">
    <div class="widget-small danger coloured-icon" style="filter: grayscale(0.5);">
      <i class="icon fa fa-times-circle fa-3x"></i>
      <div class="info">
        <h4>Cancelled Today</h4>
        <p><b>{{ $totalCancelledToday }}</b></p>
      </div>
    </div>
  </div>
  <div class="col-md-3 col-sm-6">
    <div class="widget-small success coloured-icon">
      <i class="icon fa fa-money fa-3x"></i>
      <div class="info">
        <h4>Today's Revenue</h4>
        <p><b>{{ number_format($todayRevenue) }} <small>TZS</small></b></p>
      </div>
    </div>
  </div>
  <div class="col-md-3 col-sm-6">
    <div class="widget-small {{ $lowStockCount > 0 ? 'warning' : 'primary' }} coloured-icon">
      <i class="icon fa fa-exclamation-triangle fa-3x"></i>
      <div class="info">
        <h4>Low Stock Alerts</h4>
        <p><b>{{ $lowStockCount }} <small>item(s)</small></b></p>
      </div>
    </div>
  </div>
</div>

<style>
@media (min-width: 992px) {
  /* Simplified layout */
}
</style>

<!-- Pending Guest Orders -->
<div class="row mb-4" id="orders">
  <div class="col-md-12">
    <div class="tile">
      <div class="tile-title-w-btn">
        <h3 class="title">Pending Guest Orders</h3>
        <button class="btn btn-primary" onclick="openWalkInModal()"><i class="fa fa-plus"></i> New Walk-in Sale</button>
      </div>
      
      @if($pendingOrders->count() > 0)
      <div class="table-responsive">
        <table class="table table-hover table-bordered">
          <thead>
            <tr>
              <th>Time</th>
               <th>By</th>
              <th>Guest / Room</th>
              <th>Item</th>
              <th>Qty</th>
              <th>Price (TZS)</th>
              <th>Notes</th>
              <th>Actions</th>
            </tr>
          </thead>
          <tbody>
            @php
              $groupedOrders = $pendingOrders->groupBy(function($item) {
                  if ($item->is_walk_in) {
                      return 'w_' . ($item->walk_in_name ?? 'General');
                  }
                  if ($item->day_service_id) {
                      return 'c_' . $item->day_service_id;
                  }
                  return 'b_' . ($item->booking_id ?? 'unknown');
              });
            @endphp
            
            @foreach($groupedOrders as $groupKey => $orderGroup)
              @php
                $first = $orderGroup->first();
                // guestTotal = only items that still need payment at the bar
                $cancelledItems = $orderGroup->filter(fn($o) => $o->status === 'cancelled');
                $activeItems    = $orderGroup->filter(fn($o) => $o->status !== 'cancelled');
                $paidItems      = $activeItems->filter(fn($o) => in_array($o->payment_status, ['paid', 'room_charge']));
                $unpaidItems    = $activeItems->filter(fn($o) => !in_array($o->payment_status, ['paid', 'room_charge']));
                $allCancelled      = $activeItems->isEmpty();                              // Every item was cancelled
                $guestTotal        = $unpaidItems->sum('total_price_tsh');                // Actual amount to collect NOW
                $guestTotalAll     = $activeItems->sum('total_price_tsh');                // Full session total (for reference)
                $guestAlreadyPaid  = $paidItems->sum('total_price_tsh');                 // Already settled
                $latestRequest     = $orderGroup->sortByDesc('requested_at')->first()->requested_at;
                // Check if this is a company-paid booking
                $isCompanyPaid = !$first->is_walk_in && $first->booking && $first->booking->payment_responsibility === 'company';
              @endphp
              <tr style="border-top: 3px solid {{ $allCancelled ? '#6c757d' : ($guestTotal > 0 ? '#e77a3a' : '#28a745') }};">
               <td style="vertical-align: top;">
                 <strong>{{ $latestRequest->format('H:i') }}</strong><br>
                 <small class="text-muted">({{ $latestRequest->diffForHumans() }})</small>
                 @if($allCancelled)
                   <br><span class="badge badge-secondary mt-1" style="font-size:10px;"><i class="fa fa-times"></i> Cancelled</span>
                 @elseif($guestTotal > 0)
                   <br><span class="badge mt-1" style="background:#e77a3a;color:#fff;font-size:10px;">DUE: {{ number_format($guestTotal) }}</span>
                 @else
                   <br><span class="badge badge-success mt-1" style="font-size:10px;"><i class="fa fa-check"></i> Settled</span>
                 @endif
               </td>
               <td style="vertical-align: top;">
                 @php
                   // Get unique waiters/staff for this group
                   $waiters = [];
                    foreach($orderGroup as $o) {
                        $requestedBy = 'Staff';
                        if ($o->reception_notes) {
                            $notes = $o->reception_notes;
                            
                            if (str_contains($notes, 'Order by Waiter:')) {
                                $prefix = str_contains($notes, 'POS Order by Waiter:') ? 'POS Order by Waiter:' : 'Order by Waiter:';
                                $parts = explode($prefix, $notes);
                                $textAfter = $parts[1] ?? '';
                                $requestedBy = trim(explode('|', explode('[', explode(' - Msg:', $textAfter)[0])[0])[0] ?? 'Staff');
                            } elseif (str_contains($notes, 'Recorded by: ')) {
                                $parts = explode('Recorded by: ', $notes);
                                $requestedBy = trim(explode('|', explode('[', explode(' - Msg:', $parts[1] ?? 'Staff')[0])[0])[0]);
                            } elseif (str_contains($notes, 'Recorded by ')) {
                                $parts = explode('Recorded by ', $notes);
                                $byParts = explode(':', $parts[1] ?? '');
                                $requestedBy = trim(explode('|', explode('[', $byParts[1] ?? 'Staff')[0])[0]);
                            }
                        }
                        
                        // Fallback to approvedBy
                        if (($requestedBy === 'Staff' || $requestedBy === 'N/A') && $o->approvedBy) {
                            $requestedBy = $o->approvedBy->name;
                        }
                        $waiters[] = trim($requestedBy);
                    }
                   $waiters = array_unique($waiters);
                 @endphp
                 
                 @foreach($waiters as $w)
                   <span class="badge badge-info mb-1">{{ $w }}</span><br>
                 @endforeach
               </td>
              <td style="vertical-align: top;">
                @if($first->is_walk_in)
                    <span class="badge badge-secondary mb-1">WALK-IN</span><br>
                    <strong>{{ $first->walk_in_name ?? 'General Walk-in' }}</strong>
                    <div class="mt-2" style="font-size: 11px;">
                        <div class="text-muted">Session Bill:</div>
                        <div style="font-weight: bold;">{{ number_format($guestTotalAll) }} TZS</div>
                        @if($guestAlreadyPaid > 0)
                            <div class="text-success" style="font-size: 10px;">
                                - Paid: {{ number_format($guestAlreadyPaid) }}
                            </div>
                        @endif
                        @if($allCancelled)
                           <div class="text-muted border-top mt-1 pt-1">
                               <i class="fa fa-times"></i> All Cancelled
                           </div>
                        @elseif($guestTotal > 0)
                           <div class="text-danger border-top mt-1 pt-1" style="font-weight: bold;">
                               Due: {{ number_format($guestTotal) }}
                           </div>
                        @else
                           <div class="text-success border-top mt-1 pt-1">
                               <i class="fa fa-check"></i> Fully Paid
                           </div>
                        @endif
                    </div>
                @elseif($first->day_service_id)
                    <span class="badge badge-info mb-1">CEREMONY</span><br>
                    <strong>{{ $first->dayService?->guest_name ?? 'Ceremony Guest' }}</strong>
                    <div class="mt-2" style="font-size: 11px;">
                        <div class="text-muted">Ref: {{ $first->dayService?->service_reference ?? 'N/A' }}</div>
                        <div style="font-weight: bold;">{{ number_format($guestTotalAll) }} TZS</div>
                        @if($guestAlreadyPaid > 0)
                            <div class="text-success" style="font-size: 10px;">
                                - Paid: {{ number_format($guestAlreadyPaid) }}
                            </div>
                        @endif
                        @if($allCancelled)
                           <div class="text-muted border-top mt-1 pt-1">
                               <i class="fa fa-times"></i> All Cancelled
                           </div>
                        @elseif($guestTotal > 0)
                           <div class="text-danger border-top mt-1 pt-1" style="font-weight: bold;">
                               Due: {{ number_format($guestTotal) }}
                           </div>
                        @else
                           <div class="text-success border-top mt-1 pt-1">
                               <i class="fa fa-check"></i> Fully Paid
                           </div>
                        @endif
                    </div>
                @else
                    <span class="badge badge-primary mb-1">Room {{ $first->booking?->room?->room_number ?? 'N/A' }}</span><br>
                    <strong>{{ $first->booking?->guest_name ?? 'N/A' }}</strong>
                    <div class="mt-2" style="font-size: 11px;">
                        <div class="text-muted">Session Bill:</div>
                        <div style="font-weight: bold;">{{ number_format($guestTotalAll) }} TZS</div>
                        @if($guestAlreadyPaid > 0)
                            <div class="text-success" style="font-size: 10px;">
                                - Paid: {{ number_format($guestAlreadyPaid) }}
                            </div>
                        @endif
                        @if($allCancelled)
                           <div class="text-muted border-top mt-1 pt-1">
                               <i class="fa fa-times"></i> All Cancelled
                           </div>
                        @elseif($guestTotal > 0)
                           <div class="text-danger border-top mt-1 pt-1" style="font-weight: bold;">
                               Due: {{ number_format($guestTotal) }}
                           </div>
                        @else
                           <div class="text-success border-top mt-1 pt-1">
                               <i class="fa fa-check"></i> Fully Paid
                           </div>
                        @endif
                    </div>
                @endif
              </td>
              <td colspan="4" class="p-0">
                <table class="table table-sm mb-0" style="background: transparent;">
                  @foreach($orderGroup as $order)
                  @php
                    $isDimmed = ($order->status === 'cancelled') || ($order->status === 'completed') || in_array($order->payment_status, ['paid', 'room_charge']);
                  @endphp
                  <tr style="background: transparent; {{ $isDimmed ? 'opacity: 0.5;' : '' }}">
                    <td style="width: 35%; border-top: none;">
                      <strong class="{{ $order->status === 'cancelled' ? 'text-muted' : '' }}">{{ $order->service_specific_data['item_name'] ?? $order->service->name }}</strong>
                      @if($order->status === 'cancelled')
                          <br><span class="badge badge-danger" style="font-size: 9px;">CANCELLED</span>
                          @if($order->cancelledBy)
                              <div class="text-danger mt-1" style="font-size: 9px; line-height: 1;">By: {{ $order->cancelledBy->name }}</div>
                          @endif
                      @elseif(in_array($order->payment_status, ['paid', 'room_charge']))
                          <br><span class="badge badge-success" style="font-size: 9px;">PAID</span>
                          @if($order->paidBy)
                              <div class="text-success mt-1" style="font-size: 9px; line-height: 1;">To: {{ $order->paidBy->name }}</div>
                          @endif
                      @else
                          <br><span class="badge badge-warning" style="font-size: 9px;">UNPAID</span>
                      @endif
                    </td>
                    <td style="width: 10%; border-top: none;">x {{ $order->quantity }}</td>
                    <td style="width: 20%; border-top: none;">{{ number_format($order->total_price_tsh) }}</td>
                    <td style="width: 35%; border-top: none;">
                      @php
                        $note = $order->guest_request;
                        $recNote = $order->reception_notes ?? '';
                        
                        // Extract message part if exists
                        $msgFromRec = '';
                        if (str_contains($recNote, '- Msg: ')) {
                            $parts = explode('- Msg: ', $recNote);
                            $msgFromRec = $parts[1] ?? '';
                        }
                        
                        // Use extracted message if no guest request
                        if (!$note) $note = $msgFromRec;
                        
                        // Handle "Served by" info nicely
                        if (str_contains($recNote, '| Served by')) {
                             $servedInfo = trim(substr($recNote, strpos($recNote, '| Served by') + 2));
                             // If note is empty, just use served info
                             if (!$note) $note = $servedInfo;
                             // If note doesn't already contain the served info, append it
                             elseif (!str_contains($note, $servedInfo)) {
                                 $note .= ' | ' . $servedInfo;
                             }
                        }
                      @endphp
                      <span class="text-muted" style="font-size: 11px;">
                          {!! str_replace('(Pending Payment)', '<span class="text-danger font-weight-bold" style="color: #dc3545 !important; background: #fff3f3; padding: 1px 4px; border-radius: 4px; border: 1px solid #fcc;">(Pending Payment)</span>', e($note ?: '-')) !!}
                      </span>
                      
                      @if($order->status === 'cancelled' || $order->status === 'completed' || in_array($order->payment_status, ['paid', 'room_charge']))
                          <button class="btn btn-xs btn-outline-secondary pull-right ml-1" disabled title="Cannot cancel — order is {{ ucfirst($order->status) }}" style="opacity:0.38; cursor:not-allowed;">
                            <i class="fa fa-times"></i>
                          </button>
                      @elseif($order->status === 'pending' || $order->status === 'approved')
                          <button class="btn btn-xs btn-outline-danger pull-right ml-1" onclick="cancelSingleOrder({{ $order->id }}, '{{ addslashes($order->service_specific_data['item_name'] ?? 'Item') }}')" title="Cancel this item">
                            <i class="fa fa-times"></i>
                          </button>
                          <button class="btn btn-xs btn-outline-info pull-right ml-1" onclick="serveOrder({{ $order->id }}, '{{ $order->service_specific_data['item_name'] ?? 'Item' }}')" title="Mark as Served (Taken)">
                            <i class="fa fa-hand-holding-water"></i> Serve
                          </button>
                      @endif


                    </td>
                  </tr>
                  @endforeach
                  @if($guestTotalAll > 0)
                  <tr style="background: #f9f9f9; font-size: 11px;">
                    <td colspan="2" style="border-top: 1px solid #ddd; text-align: right; color:#555;">Session Total:</td>
                    <td colspan="2" style="border-top: 1px solid #ddd; color:#555;">{{ number_format($guestTotalAll) }} TZS</td>
                  </tr>
                  @endif
                  @if($guestAlreadyPaid > 0)
                  <tr style="background: #f0fff4; font-size: 11px;">
                    <td colspan="2" style="text-align: right; color:#28a745;">Already Paid:</td>
                    <td colspan="2" style="color:#28a745;">- {{ number_format($guestAlreadyPaid) }} TZS</td>
                  </tr>
                  @endif
                  @if($allCancelled)
                  <tr style="background: #f8f9fa;">
                    <td colspan="4" style="border-top: 1px solid #dee2e6; text-align: center; color:#6c757d;"><i class="fa fa-times-circle"></i> <strong>All Cancelled</strong></td>
                  </tr>
                  @elseif($guestTotal > 0)
                  <tr style="background: #fff3e0;">
                    <td colspan="2" style="border-top: 2px solid #e77a3a; text-align: right;"><strong style="color:#e77a3a;">Amount Due:</strong></td>
                    <td colspan="2" style="border-top: 2px solid #e77a3a;"><strong style="color:#e77a3a;">{{ number_format($guestTotal) }} TZS</strong></td>
                  </tr>
                  @else
                  <tr style="background: #d4edda;">
                    <td colspan="4" style="border-top: 1px solid #c3e6cb; text-align: center; color:#155724;"><i class="fa fa-check-circle"></i> <strong>Fully Settled</strong></td>
                  </tr>
                  @endif
                </table>
              </td>
              <td style="vertical-align: top;">
                <div class="btn-group-vertical btn-group-sm w-100">
                  @php
                    $printUrl = route('bar-keeper.orders.print-group', [
                        'is_walk_in' => $first->is_walk_in ? 1 : 0,
                        'identifier' => $first->is_walk_in ? $first->walk_in_name : $first->booking_id
                    ]);
                  @endphp
                  
                  <button class="btn btn-sm btn-info mb-1" onclick="window.open('{{ $printUrl }}', 'Print', 'width=800,height=600')" title="Print Group Bill">
                    <i class="fa fa-print"></i> Print Bill
                  </button>

                  <button class="btn btn-sm btn-outline-primary mb-1" onclick="openWalkInModal('{{ $first->is_walk_in ? ($first->walk_in_name ?? 'Walk-in') : ($first->booking->guest_name ?? 'Guest') }}', {{ $first->booking_id ?? 'null' }})" title="Add Item to Order">
                    <i class="fa fa-plus"></i> Add Items
                  </button>

                  @if($guestTotal > 0 && !$first->day_service_id)
                      @if($isCompanyPaid)
                        <button class="btn btn-sm btn-outline-success mb-1" onclick="openPaymentModal({{ $first->id }}, {{ $guestTotal }}, 0, '{{ $first->booking->guest_name }}', 1)" title="Charge to Room">
                          <i class="fa fa-bed"></i> Charge Room: {{ number_format($guestTotal) }}
                        </button>
                      @else
                        <button class="btn btn-sm btn-success mb-1" onclick="openPaymentModal({{ $first->id }}, {{ $guestTotal }}, {{ $first->is_walk_in ? 1 : 0 }}, '{{ $first->is_walk_in ? ($first->walk_in_name ?? 'Walk-in') : ($first->booking->guest_name ?? 'Guest') }}')" title="Record Payment">
                          <i class="fa fa-money"></i> PAY: {{ number_format($guestTotal) }}
                        </button>
                      @endif
                  @endif
                  
                   @php
                    $hasCancellableItems = $orderGroup->contains(function($o) {
                        return !in_array($o->status, ['cancelled']) && !in_array($o->payment_status, ['paid', 'room_charge']);
                    });
                   @endphp
                   @if($hasCancellableItems)
                  <button class="btn btn-sm btn-outline-danger" onclick="cancelOrderGroup('{{ $first->is_walk_in ? ($first->walk_in_name ?? 'General') : ($first->booking_id ?? 'unknown') }}', {{ $first->is_walk_in ? 1 : 0 }})" title="Cancel Entire Order">
                    <i class="fa fa-times"></i> Cancel All
                  </button>
                  @endif
                </div>
              </td>
            </tr>
            @endforeach
          </tbody>
        </table>
      </div>
      @else
      <div class="text-center" style="padding: 30px;">
        <i class="fa fa-coffee fa-4x text-muted mb-3"></i>
        <h3>No Pending Orders</h3>
        <p class="text-muted">New orders from guests will appear here.</p>
      </div>
      @endif
    </div>
  </div>
</div>



{{-- Active Ceremonies Section --}}
<div class="row mt-4">
  <div class="col-md-12">
    <div class="tile shadow-sm border-0 mb-4">
        <div class="tile-title-w-btn">
            <h3 class="title"><i class="fa fa-birthday-cake mr-2 text-primary"></i> Active Ceremonies</h3>
            <div class="btn-group">
                <a href="{{ route('bar-keeper.day-services.index', ['tab' => 'ceremony']) }}" class="btn btn-outline-primary btn-sm rounded-pill mr-2">
                    <i class="fa fa-history mr-1"></i> View History
                </a>
                <span class="badge badge-info p-2 d-flex align-items-center" style="border-radius: 20px;">{{ $activeCeremonies->count() }} ACTIVE</span>
            </div>
        </div>
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>Reference</th>
                        <th>Guest Name</th>
                        <th>Ceremony Type</th>
                        <th>Pax</th>
                        <th>Notes</th>
                        <th class="text-center">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($activeCeremonies as $ceremony)
                        <tr>
                            <td><span class="badge badge-light border">{{ $ceremony->service_reference }}</span></td>
                            <td><strong>{{ $ceremony->guest_name }}</strong></td>
                            <td>{{ $ceremony->service_type_name }}</td>
                            <td>{{ $ceremony->number_of_people }}</td>
                            <td><small class="text-muted">{{ Str::limit($ceremony->notes, 40) }}</small></td>
                            <td class="text-center">
                                @php
                                    $unpaidUsage = $ceremony->serviceRequests
                                        ->where('payment_status', 'pending')
                                        ->filter(function($req) {
                                            return $req->service && in_array($req->service->category, ['alcoholic_beverage', 'non_alcoholic_beverage', 'water', 'juices', 'energy_drinks', 'soft_drinks', 'beers', 'wines', 'spirits', 'cocktails', 'drinks', 'liquor']);
                                        })
                                        ->sum('total_price_tsh');
                                @endphp
                                <div class="btn-group">
                                    <button class="btn btn-primary btn-sm rounded-pill px-3" onclick="openWalkInModal(null, '{{ $ceremony->guest_name }}', {{ $ceremony->id }})">
                                        <i class="fa fa-plus-circle mr-1"></i> Add Usage
                                    </button>
                                    <a href="{{ route('bar-keeper.day-services.docket', $ceremony->id) }}" target="_blank" class="btn btn-secondary btn-sm rounded-pill px-3 ml-2" title="Print Docket">
                                        <i class="fa fa-print mr-1"></i> Print Docket
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center py-4 text-muted italic">
                                <i class="fa fa-info-circle mr-1"></i> No active ceremonies found.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
  </div>
</div>

<!-- Quick Operational Actions -->
<div class="row mt-4">
    <div class="col-md-12">
        <div class="tile shadow-sm h-100">
            <h3 class="tile-title">Quick Operational Actions</h3>
            <div class="row">
                <div class="col-md-3 mb-3">
                    <button onclick="openWalkInModal()" class="btn btn-primary btn-block p-4 shadow-sm h-100 d-flex flex-column align-items-center justify-content-center">
                        <i class="fa fa-glass fa-2x mb-2"></i>
                        <span>New Walk-in Sale</span>
                    </button>
                </div>
                <div class="col-md-3 mb-3">
                    <a href="{{ route('bar-keeper.stock.index') }}" class="btn btn-info btn-block p-4 shadow-sm h-100 d-flex flex-column align-items-center justify-content-center">
                        <i class="fa fa-cubes fa-2x mb-2"></i>
                        <span>Bar Inventory</span>
                    </a>
                </div>
                <div class="col-md-3 mb-3">
                    <a href="{{ route('bar-keeper.reports') }}" class="btn btn-danger btn-block p-4 shadow-sm h-100 d-flex flex-column align-items-center justify-content-center">
                        <i class="fa fa-bar-chart fa-2x mb-2"></i>
                        <span>Daily Stock Sheet</span>
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
  </div>
</div>

<!-- Walk-in POS Modal -->
<div class="modal fade" id="walkInModal" tabindex="-1" role="dialog" aria-hidden="true">
  <div class="modal-dialog modal-lg" role="document" style="max-width: 900px;">
    <div class="modal-content border-0 shadow-lg">
            <div class="modal-header bg-primary text-white border-0 py-3">
                <h5 class="modal-title font-weight-bold" id="posModalTitle">
                    <i class="fa fa-shopping-cart mr-2"></i> New Walk-in Order
                </h5>
        <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body p-0">
        <div class="row no-gutters">
          <!-- Item Selection -->
          <div class="col-md-7 border-right" style="max-height: 70vh; overflow-y: auto; background: #fff;">
            <div class="p-3 sticky-top bg-white border-bottom shadow-sm">
              <div class="input-group mb-3">
                <div class="input-group-prepend">
                  <span class="input-group-text bg-white border-right-0"><i class="fa fa-search text-muted"></i></span>
                </div>
                <input type="text" id="itemSearch" class="form-control border-left-0" placeholder="Search drinks..." onkeyup="filterPosItems()">
              </div>
              
              <div class="d-flex flex-wrap pb-2" id="posCategoryTabs">
                <button class="btn btn-sm btn-primary rounded-pill mr-2 mb-2 px-3 active" onclick="filterByCategory('all', this)">All Items</button>
                @php
                    $posCatLabels = [
                        'spirits' => 'Spirits',
                        'wines' => 'Wines',
                        'alcoholic_beverage' => 'Beers / Ciders',
                        'cocktails' => 'Cocktails',
                        'non_alcoholic_beverage' => 'Soft Drinks / Sodas',
                        'energy_drinks' => 'Energy Drinks',
                        'water' => 'Water',
                        'juices' => 'Juices',
                        'hot_beverages' => 'Hot Beverages',
                        'food' => 'Food / Snacks',
                        'other' => 'Other'
                    ];
                @endphp
                @foreach($drinkCategories as $catKey => $items)
                    @php 
                        $label = $posCatLabels[$catKey] ?? ucfirst(str_replace(['_', '-'], ' ', $catKey)); 
                    @endphp
                    <button class="btn btn-sm btn-outline-primary rounded-pill mr-2 mb-2 px-3" onclick="filterByCategory('{{ $catKey }}', this)">{{ $label }}</button>
                @endforeach
              </div>
            </div>
            
            <div class="p-3" id="posItemList">
                <div class="row">
                  @foreach($drinks as $drink)
                  @php
                    $catClass = strtolower($drink->category ?? 'other');
                  @endphp
                  <div class="col-6 mb-3 pos-item-card" 
                       data-name="{{ strtolower($drink->name) }}" 
                       data-category="{{ $catClass }}">
                    <div class="card h-100 border-0 shadow-sm rounded-lg transition-all {{ $drink->current_stock <= 0 && $drink->is_product ? 'opacity-75' : '' }}" 
                         style="background: #fff; overflow: hidden; position: relative; border: 1px solid #eee !important;">
                      
                      @if($drink->current_stock <= 0 && $drink->is_product)
                      <div class="out-of-stock-overlay d-flex align-items-center justify-content-center" style="position: absolute; top:0; left:0; width:100%; height:100%; background: rgba(255,255,255,0.7); z-index: 5;">
                        <span class="badge badge-danger px-3 py-2 shadow-sm">OUT OF STOCK</span>
                      </div>
                      @endif

                      <div class="card-body p-3 d-flex flex-column h-100">
                        <div class="bg-light rounded p-2 mb-3 mx-auto d-flex align-items-center justify-content-center" style="width: 100px; height: 100px;">
                            <img src="{{ $drink->image ? asset('storage/'.$drink->image) : 'https://img.icons8.com/color/144/glass-with-straw.png' }}" class="img-fluid" style="max-height: 80px; object-fit: contain;" onerror="this.onerror=null;this.src='https://img.icons8.com/color/144/glass-with-straw.png'">
                        </div>
                        
                        <div class="mb-3 text-center">
                            <h6 class="mb-1 text-dark font-weight-bold truncate" style="font-size: 0.95rem;">{{ $drink->name }}</h6>
                            @if($drink->is_product)
                            <span class="badge {{ $drink->current_stock > 10 ? 'badge-success' : ($drink->current_stock > 0 ? 'badge-warning' : 'badge-danger') }} px-2" style="font-size: 0.65rem;">Stock: {{ number_format($drink->current_stock) }}</span>
                            @endif
                        </div>

                        <div class="options-area mt-auto">
                            @foreach($drink->options as $option)
                            <div class="d-flex align-items-center justify-content-between bg-light rounded p-2 mb-1 border" style="font-size: 0.8rem;">
                                <div>
                                    <div class="font-weight-bold text-muted" style="font-size: 0.65rem; text-transform: uppercase;">{{ $option->type }}</div>
                                    <div class="text-primary font-weight-bold">{{ number_format($option->price) }} TSH</div>
                                </div>
                                <button class="btn btn-sm btn-primary rounded px-2" onclick="addToPosCart('{{ addslashes($drink->name) }} ({{ $option->type }})', {{ $option->price }}, {{ $drink->product_id ?? 'null' }}, {{ $drink->variant_id ?? 'null' }}, '{{ isset($drink->recipe_id) ? 'food' : 'bar' }}', {{ $drink->recipe_id ?? 'null' }}, '{{ ($drink->image ? asset('storage/'.$drink->image) : 'https://img.icons8.com/color/144/glass-with-straw.png') }}', '{{ $option->method }}', {{ $drink->current_stock ?? 0 }}, {{ $drink->servings_per_pic ?? 1 }})" {{ ($drink->current_stock ?? 0) <= 0 && $drink->is_product ? 'disabled' : '' }}>
                                    <i class="fa fa-plus"></i>
                                </button>
                            </div>
                            @endforeach
                        </div>
                      </div>
                    </div>
                  </div>
                  @endforeach
                </div>
            </div>
          </div>
          
          <!-- Cart/Summary -->
          <div class="col-md-5 d-flex flex-column bg-light" style="height: 70vh;">
            <div class="p-3 border-bottom bg-white">
               <h6 class="font-weight-bold mb-2 d-flex align-items-center">
                   <i class="fa fa-user mr-2 text-primary"></i> Guest Information
               </h6>
               <div class="mb-3">
                   <div class="btn-group btn-group-toggle w-100 mb-2" id="customerTypeGroup">
                       <button type="button" id="btnTypeWalkIn" class="btn btn-primary btn-sm active" onclick="setCustomerType('walk-in')" style="width: 33%;">Walk-In</button>
                       <button type="button" id="btnTypeResident" class="btn btn-outline-primary btn-sm" onclick="setCustomerType('resident')" style="width: 33%;">Resident</button>
                       <button type="button" id="btnTypeCeremony" class="btn btn-outline-primary btn-sm" onclick="setCustomerType('ceremony')" style="width: 34%;"><i class="fa fa-birthday-cake mr-1"></i>Ceremony</button>
                   </div>
                   
                   <div id="walkInNameContainer">
                       <input type="text" id="walkInGuestName" class="form-control" placeholder="Guest Name (Optional)">
                   </div>
                   
                   <div id="residentSelectContainer" style="display: none;">
                       <select id="posResidentBooking" class="form-control" onchange="syncResidentName()">
                           <option value="">-- Select Room / Guest --</option>
                           @foreach($activeBookings as $booking)
                               <option value="{{ $booking->id }}" data-guest="{{ $booking->guest_name }}">
                                   Room {{ $booking->room->room_number ?? 'N/A' }}: {{ $booking->guest_name }}
                               </option>
                           @endforeach
                       </select>
                   </div>
                   
                   <div id="ceremonyGuestContainer" style="display: none;">
                       <div class="alert alert-info p-2 mb-0">
                           <i class="fa fa-birthday-cake mr-1"></i>
                           Adding drinks for ceremony: <strong id="ceremonyGuestLabel"></strong>
                       </div>
                   </div>
               </div>
            </div>
            
            <div class="flex-grow-1 p-3" id="posCartList" style="overflow-y: auto;">
              <div class="text-center text-muted mt-5">
                <i class="fa fa-shopping-basket fa-3x mb-3 opacity-50"></i>
                <p>Click items on the left to add to cart</p>
              </div>
            </div>
            
            <div class="p-3 border-top bg-white mt-auto">
              <div class="d-flex justify-content-between mb-3">
                <span class="font-weight-bold text-muted">Total Amount:</span>
                <span class="font-weight-bold text-primary h5 mb-0" id="posTotalAmount">0 TZS</span>
              </div>
              <button class="btn btn-success btn-lg btn-block shadow-sm py-3" onclick="processWalkInCheckout()" id="btnConfirmSale" disabled>
                <i class="fa fa-check-circle mr-1"></i> <strong>RECORD WALK-IN ORDER</strong>
              </button>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<style>
.truncate {
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}
.hover-shadow:hover {
    box-shadow: 0 8px 25px rgba(0,0,0,0.1) !important;
    transform: translateY(-2px);
}
.transition-all {
    transition: all 0.2s ease-in-out;
}
.badge-success { background-color: #d4edda; color: #155724; }
.badge-warning { background-color: #fff3cd; color: #856404; }
.badge-danger { background-color: #f8d7da; color: #721c24; }
</style>

<!-- Payment Modal HTML -->
<div class="modal fade" id="paymentModal" tabindex="-1" role="dialog" aria-labelledby="paymentModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="paymentModalLabel">Record Payment</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <input type="hidden" id="paymentOrderId">
                <p class="h4 text-center mb-4">Amount: <span id="paymentAmountDisplay" class="font-weight-bold text-primary"></span></p>
                
                <div class="form-group">
                    <label for="paymentMethod">Payment Method</label>
                    <select class="form-control" id="paymentMethod" onchange="toggleRefField()">
                        <option value="cash">Cash</option>
                        <option value="room_charge">Room Charge</option>
                        <option value="mpesa">M-Pesa</option>
                        <option value="halopesa">Halopesa</option>
                        <option value="airtel_money">Airtel Money</option>
                        <option value="mixx_by_yass">Mixx by Yass</option>
                        <option value="nmb">NMB Bank</option>
                        <option value="crdb">CRDB Bank</option>
                        <option value="kcb">KCB Bank</option>
                    </select>
                </div>
                <div class="form-group" id="refFieldContainer" style="display: none;">
                    <label for="paymentReference">Reference Number</label>
                    <input type="text" class="form-control" id="paymentReference" placeholder="Enter reference number">
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" onclick="submitPayment()">Record Payment</button>
            </div>
        </div>
    </div>
</div>

@endsection

@section('scripts')
<script src="{{ asset('dashboard_assets/js/plugins/sweetalert.min.js') }}"></script>
<script>


function completeOrder(orderId, paymentMethod = 'room_charge') {
    let title = paymentMethod === 'cash' ? "Record Cash Payment" : "Charge to Room";
    let text = paymentMethod === 'cash' ? "Record this walk-in sale as PAID (Cash)?" : "Mark this order as served and charge to the ROOM bill?";
    let btnColor = paymentMethod === 'cash' ? "#28a745" : "#007bff";
    
    swal({
        title: title,
        text: text,
        type: paymentMethod === 'cash' ? "success" : "info",
        showCancelButton: true,
        confirmButtonColor: btnColor,
        cancelButtonColor: "#6c757d",
        confirmButtonText: "Yes, Proceed",
        cancelButtonText: "Cancel",
        closeOnConfirm: false
    }, function(isConfirm) {
        if (isConfirm) {
            callApi(`{{ route("bar-keeper.orders.complete", ":id") }}`.replace(':id', orderId), 'POST', { payment_method: paymentMethod });
        }
    });
}

// POS Logic
let posCart = [];



function addToPosCart(name, price, pid, vid, type, foodId = null, image = null, unit = 'pic', currentStock = null, servingsPerPic = 1) {
    const key = foodId ? foodId : `${pid}_${vid}_${unit}`;
    const existing = posCart.find(item => item.key === key);
    
    // Calculate how much we're trying to add
    let newQty = existing ? existing.qty + 1 : 1;
    
    // Convert to bottles if selling by serving/glass
    let bottlesNeeded = newQty;
    if (unit === 'serving' && servingsPerPic > 0) {
        bottlesNeeded = newQty / servingsPerPic;
    }
    
    // Check stock availability (only for products with stock tracking)
    if (currentStock !== null && currentStock > 0 && bottlesNeeded > currentStock) {
        const maxAllowed = unit === 'serving' ? Math.floor(currentStock * servingsPerPic) : Math.floor(currentStock);
        Swal.fire({
            title: 'Stock Limit Exceeded',
            html: `<p>Cannot add more of this item.</p>
                   <p><strong>Current Stock:</strong> ${currentStock.toFixed(1)} bottles</p>
                   <p><strong>Maximum ${unit === 'serving' ? 'Glasses' : 'Units'}:</strong> ${maxAllowed}</p>`,
            icon: 'warning',
            confirmButtonText: 'OK'
        });
        return;
    }
    
    if (existing) {
        existing.qty++;
    } else {
        posCart.push({
            key: key,
            name: name,
            price: price,
            pid: pid,
            vid: vid,
            type: type,
            foodId: foodId,
            image: image,
            unit: unit,
            qty: 1,
            currentStock: currentStock,
            servingsPerPic: servingsPerPic
        });
    }
    renderPosCart();
}

function removeFromPosCart(key) {
    posCart = posCart.filter(item => item.key !== key);
    renderPosCart();
}

function updatePosQty(key, delta) {
    const item = posCart.find(i => i.key === key);
    if (item) {
        const newQty = item.qty + delta;
        
        // If increasing quantity, check stock
        if (delta > 0 && item.currentStock !== null && item.currentStock !== undefined) {
            let bottlesNeeded = newQty;
            if (item.unit === 'serving' && item.servingsPerPic > 0) {
                bottlesNeeded = newQty / item.servingsPerPic;
            }
            
            if (bottlesNeeded > item.currentStock) {
                const maxAllowed = item.unit === 'serving' ? Math.floor(item.currentStock * item.servingsPerPic) : Math.floor(item.currentStock);
                Swal.fire({
                    title: 'Stock Limit Exceeded',
                    html: `<p>Cannot add more of this item.</p>
                           <p><strong>Current Stock:</strong> ${item.currentStock.toFixed(1)} bottles</p>
                           <p><strong>Maximum ${item.unit === 'serving' ? 'Glasses' : 'Bottles'}:</strong> ${maxAllowed}</p>`,
                    icon: 'warning',
                    confirmButtonText: 'OK'
                });
                return;
            }
        }
        
        item.qty = newQty;
        if (item.qty <= 0) removeFromPosCart(key);
        else renderPosCart();
    }
}

function renderPosCart() {
    const list = document.getElementById('posCartList');
    const totalEl = document.getElementById('posTotalAmount');
    const btn = document.getElementById('btnConfirmSale');
    
    if (posCart.length === 0) {
        list.innerHTML = `<div class="text-center text-muted mt-5"><i class="fa fa-shopping-basket fa-3x mb-2"></i><p>Cart is empty</p></div>`;
        totalEl.innerText = '0 TZS';
        btn.disabled = true;
        return;
    }
    
    let total = 0;
    let html = '';
    posCart.forEach(item => {
        const itemTotal = item.price * item.qty;
        total += itemTotal;
        html += `
            <div class="d-flex justify-content-between align-items-center mb-3 bg-white p-2 rounded shadow-sm">
                <div class="d-flex align-items-center" style="max-width: 65%;">
                    ${item.image ? `<img src="${item.image}" class="mr-2 rounded" style="width: 40px; height: 40px; object-fit: cover;" onerror="this.onerror=null;this.src='https://img.icons8.com/color/144/glass-with-straw.png'">` : ''}
                    <div>
                        <div class="font-weight-bold" style="font-size: 0.85rem; line-height: 1.2;">${item.name}</div>
                        <small class="text-muted">${item.price.toLocaleString()} x ${item.qty}</small>
                    </div>
                </div>
                <div class="d-flex align-items-center">
                    <div class="btn-group btn-group-sm mr-2">
                        <button class="btn btn-outline-secondary px-2" onclick="updatePosQty('${item.key}', -1)">-</button>
                        <button class="btn btn-outline-secondary px-2" onclick="updatePosQty('${item.key}', 1)">+</button>
                    </div>
                    <div class="text-right mr-2" style="min-width: 80px;">
                        <span class="font-weight-bold d-block" style="font-size: 0.85rem;">${itemTotal.toLocaleString()}</span>
                    </div>
                    <button class="btn btn-sm btn-outline-danger border-0" onclick="removeFromPosCart('${item.key}')">
                        <i class="fa fa-trash"></i>
                    </button>
                </div>
            </div>
        `;
    });
    
    list.innerHTML = html;
    totalEl.innerText = `${total.toLocaleString()} TZS`;
    btn.disabled = false;
}

let currentCategory = 'all';

function filterByCategory(category, btn) {
    currentCategory = category;
    
    // Update UI active state
    const tabs = document.getElementById('posCategoryTabs').querySelectorAll('button');
    tabs.forEach(t => {
        t.classList.remove('btn-primary', 'active');
        t.classList.add('btn-outline-primary');
    });
    btn.classList.remove('btn-outline-primary');
    btn.classList.add('btn-primary', 'active');
    
    filterPosItems();
}

function filterPosItems() {
    const query = document.getElementById('itemSearch').value.toLowerCase();
    const cards = document.querySelectorAll('.pos-item-card');
    
    cards.forEach(card => {
        const name = card.dataset.name;
        const category = card.dataset.category;
        
        const matchesQuery = name.includes(query);
        const matchesCategory = currentCategory === 'all' || category === currentCategory;
        
        if (matchesQuery && matchesCategory) {
            card.style.display = '';
        } else {
            card.style.display = 'none';
        }
    });
}

// Global state
let currentCustomerType = 'walk-in';
window.currentDayServiceId = null;

function setCustomerType(type) {
    currentCustomerType = type;
    document.getElementById('walkInNameContainer').style.display = (type === 'walk-in') ? 'block' : 'none';
    document.getElementById('residentSelectContainer').style.display = (type === 'resident') ? 'block' : 'none';
    document.getElementById('ceremonyGuestContainer').style.display = (type === 'ceremony') ? 'block' : 'none';
    
    const btnWalkIn = document.getElementById('btnTypeWalkIn');
    const btnResident = document.getElementById('btnTypeResident');
    const btnCeremony = document.getElementById('btnTypeCeremony');
    
    [btnWalkIn, btnResident, btnCeremony].forEach(b => { if(b) { b.classList.remove('btn-primary', 'active'); b.classList.add('btn-outline-primary'); } });
    
    const activeBtn = type === 'resident' ? btnResident : type === 'ceremony' ? btnCeremony : btnWalkIn;
    if (activeBtn) { activeBtn.classList.remove('btn-outline-primary'); activeBtn.classList.add('btn-primary', 'active'); }
    
    // Update button label
    const confirmBtn = document.getElementById('btnConfirmSale');
    if (type === 'ceremony') {
        confirmBtn.innerHTML = '<i class="fa fa-check-circle mr-1"></i> <strong>RECORD CEREMONY DRINKS</strong>';
    } else if (type === 'resident') {
        confirmBtn.innerHTML = '<i class="fa fa-check-circle mr-1"></i> <strong>CHARGE TO ROOM</strong>';
    } else {
        confirmBtn.innerHTML = '<i class="fa fa-check-circle mr-1"></i> <strong>RECORD WALK-IN ORDER</strong>';
    }
}

function openWalkInModal(guestName = null, existingName = null, dayServiceId = null) {
    posCart = [];
    renderPosCart();
    window.currentDayServiceId = dayServiceId;
    
    // Reset modal state
    document.getElementById('walkInGuestName').value = '';
    document.getElementById('posResidentBooking').value = '';
    document.getElementById('itemSearch').value = '';
    filterPosItems();
    
    const titleEl = document.getElementById('posModalTitle');
    
    if (dayServiceId) {
        // Ceremony mode
        setCustomerType('ceremony');
        document.getElementById('ceremonyGuestLabel').textContent = existingName || 'Ceremony Guest';
        titleEl.innerHTML = '<i class="fa fa-birthday-cake mr-2"></i> Add Drinks: ' + (existingName || 'Ceremony');
    } else if (existingName && !guestName) {
        // Pre-fill name for add-item flow
        setCustomerType('walk-in');
        document.getElementById('walkInGuestName').value = existingName;
        titleEl.innerHTML = '<i class="fa fa-shopping-cart mr-2"></i> Add Drinks: ' + existingName;
    } else {
        setCustomerType('walk-in');
        if (guestName) document.getElementById('walkInGuestName').value = guestName;
        titleEl.innerHTML = '<i class="fa fa-shopping-cart mr-2"></i> New Walk-in Order';
    }
    
    $('#walkInModal').modal('show');
}

function syncResidentName() {
    const select = document.getElementById('posResidentBooking');
    const selected = select.options[select.selectedIndex];
    if (selected && selected.dataset.guest) {
        document.getElementById('walkInGuestName').value = selected.dataset.guest;
    }
}

async function processWalkInCheckout() {
    const now = new Date();
    const timeStr = now.getHours().toString().padStart(2, '0') + ':' + now.getMinutes().toString().padStart(2, '0');
    let guestName = document.getElementById('walkInGuestName').value.trim();
    const residentBookingId = document.getElementById('posResidentBooking').value;
    const isWalkIn = currentCustomerType === 'walk-in' ? 1 : 0;
    const isCeremony = currentCustomerType === 'ceremony';

    if (isCeremony && !window.currentDayServiceId) {
        Swal.fire("Error", "No ceremony selected.", "error");
        return;
    }

    if (!isWalkIn && !isCeremony && !residentBookingId) {
        Swal.fire("Incomplete", "Please select a resident room/guest.", "warning");
        return;
    }

    if (isCeremony) {
        guestName = document.getElementById('ceremonyGuestLabel').textContent || 'Ceremony Guest';
    } else if (!guestName) {
        guestName = isWalkIn ? 'Walk-in (' + timeStr + ')' : 'Resident (' + timeStr + ')';
    }
    
    const confirm = await Swal.fire({
        title: "Confirm Sale?",
        text: `Record order for ${guestName}?`,
        icon: "question",
        showCancelButton: true,
        confirmButtonText: "Yes, Record Sale"
    });

    if (!confirm.isConfirmed) return;

    $('#walkInModal').modal('hide');
    Swal.fire({
        title: 'Processing...',
        didOpen: () => { Swal.showLoading(); }
    });
    
    let successCount = 0;
    for (const item of posCart) {
        const payload = {
            service_id: item.type === 'bar' ? 3 : 4,
            product_id: item.pid,
            product_variant_id: item.vid,
            quantity: item.qty,
            is_walk_in: (isWalkIn || isCeremony) ? 1 : 0,
            booking_id: (isWalkIn || isCeremony) ? null : residentBookingId,
            walk_in_name: (isWalkIn && !isCeremony) ? guestName : null,
            day_service_id: isCeremony ? window.currentDayServiceId : null,
            payment_timing: 'later',
            item_name: item.name,
            service_specific_data: {
                food_id: item.foodId,
                item_name: item.name,
                product_id: item.pid,
                product_variant_id: item.vid,
                selling_method: item.unit || 'pic'
            }
        };

        try {
            const response = await fetch("{{ route('customer.services.request') }}", {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Accept': 'application/json',
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify(payload)
            });
            const res = await response.json();
            if (res.success) successCount++;
        } catch (e) {
            console.error(e);
        }
    }

    if (successCount === posCart.length) {
        await Swal.fire({
            title: "Success!",
            text: "Walk-in sale recorded successfully!",
            icon: "success",
            timer: 2000,
            showConfirmButton: false
        });
        location.reload();
    } else {
        await Swal.fire("Completed with issues", `Only ${successCount} of ${posCart.length} items were recorded.`, "warning");
        location.reload();
    }
}

function openPaymentModal(orderId, amount, isWalkIn = 0, guestName = 'Guest', isCompany = 0) {
    document.getElementById('paymentOrderId').value = orderId;
    document.getElementById('paymentAmountDisplay').innerText = amount.toLocaleString() + ' TZS';
    
    const methodSelect = document.getElementById('paymentMethod');
    const roomChargeOption = methodSelect.querySelector('option[value="room_charge"]');
    
    // Reset options visibility
    Array.from(methodSelect.options).forEach(opt => opt.style.display = 'block');

    if (isCompany === 1) {
        // FORCE ROOM CHARGE
        methodSelect.value = 'room_charge';
        // Hide others
        Array.from(methodSelect.options).forEach(opt => {
            if (opt.value !== 'room_charge') opt.style.display = 'none';
        });
        
        swal({
            title: "Company Responsible",
            text: `Mandatory Room Charge for ${guestName}.`,
            icon: "info",
            timer: 2000,
            showConfirmButton: false
        });
    } else if (isWalkIn) {
        if (roomChargeOption) roomChargeOption.style.display = 'none';
        if (methodSelect.value === 'room_charge') methodSelect.value = 'cash';
        else methodSelect.value = 'cash';
    } else {
        methodSelect.value = 'cash';
    }

    document.getElementById('paymentReference').value = '';
    toggleRefField();
    $('#paymentModal').modal('show');
}

function toggleRefField() {
    const method = document.getElementById('paymentMethod').value;
    const container = document.getElementById('refFieldContainer');
    if (method === 'cash' || method === 'room_charge') { // Room charge also doesn't need a ref
        container.style.display = 'none';
    } else {
        container.style.display = 'block';
    }
}

function submitPayment() {
    const orderId = document.getElementById('paymentOrderId').value;
    const method = document.getElementById('paymentMethod').value;
    const reference = document.getElementById('paymentReference').value.trim();
    
    if (method !== 'cash' && method !== 'room_charge' && !reference) {
        swal("Missing Info", "Please enter a reference number for " + method.replace('_', ' ').toUpperCase(), "warning");
        return;
    }
    
    $('#paymentModal').modal('hide');
    
    // If it's a room charge, use the legacy completeOrder
    if (method === 'room_charge') {
        completeOrder(orderId, method, reference);
    } else {
        // Use the new POS settlement route
        settlePOSPayment(orderId, method, reference);
    }
}

function settlePOSPayment(orderId, method, reference = '') {
    swal({
        title: "Confirm Payment?",
        text: `Record ${method.toUpperCase()} payment of ${document.getElementById('paymentAmountDisplay').innerText}?`,
        type: "success",
        showCancelButton: true,
        confirmButtonColor: "#28a745",
        confirmButtonText: "Yes, Paid!",
        closeOnConfirm: false
    }, function(isConfirm) {
        if (isConfirm) {
            const url = `/customer/pos/settle-payment/${orderId}`;
            fetch(url, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Accept': 'application/json',
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({
                    payment_method: method,
                    payment_reference: reference
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    swal("Success!", data.message, "success");
                    setTimeout(() => location.reload(), 1500);
                } else {
                    swal("Error!", data.message, "error");
                }
            })
            .catch(error => {
                console.error('Error:', error);
                swal("Error!", "Failed to record payment.", "error");
            });
        }
    });
}

function serveOrder(orderId, itemName) {
    swal({
        title: "Mark as Served?",
        text: "Confirm that '" + itemName + "' has been taken/served? Payment will remain PENDING.",
        type: "info",
        showCancelButton: true,
        confirmButtonColor: "#17a2b8",
        cancelButtonColor: "#6c757d",
        confirmButtonText: "Yes, Served!",
        cancelButtonText: "Cancel",
        closeOnConfirm: false
    }, function(isConfirm) {
        if (isConfirm) {
            const url = `/bar-keeper/orders/${orderId}/serve`;
            fetch(url, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Accept': 'application/json',
                    'Content-Type': 'application/json'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    swal({
                        title: "Served!",
                        text: data.message,
                        type: "success",
                        timer: 1500,
                        showConfirmButton: false
                    });
                    setTimeout(() => location.reload(), 1500);
                } else {
                    swal("Error!", data.message, "error");
                }
            })
            .catch(error => {
                console.error('Error:', error);
                swal("Error!", "Failed to update order. Please try again.", "error");
            });
        }
    });
}

function completeOrder(orderId, method, reference = '') {
    let title = "Confirm Payment?";
    let text = "Record payment via " + method.replace('_', ' ').toUpperCase() + "?";
    let icon = "warning";
    let btnColor = "#28a745";

    if (method === 'room_charge') {
        title = "Charge to Room?";
        text = "This will add the amount to the guest's room bill.";
        icon = "info";
        btnColor = "#007bff";
    }

    swal({
        title: title,
        text: text,
        type: icon,
        showCancelButton: true,
        confirmButtonColor: btnColor,
        cancelButtonColor: "#6c757d",
        confirmButtonText: "Yes, Confirm!",
        cancelButtonText: "No, Cancel",
        closeOnConfirm: false
    }, function(isConfirm) {
        if (isConfirm) {
            const url = `/bar-keeper/orders/${orderId}/complete`;
            fetch(url, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Accept': 'application/json',
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({
                    payment_method: method,
                    payment_reference: reference
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    swal({
                        title: "Success!",
                        text: data.message,
                        type: "success",
                        timer: 2000,
                        showConfirmButton: false
                    });
                    setTimeout(() => location.reload(), 1500);
                } else {
                    swal("Error!", data.message, "error");
                }
            })
            .catch(error => {
                console.error('Error:', error);
                swal("Error!", "An error occurred. Please try again.", "error");
            });
        }
    });
}

function printWalkInDocket(orderId) {
    const url = `/bar-keeper/orders/${orderId}/print-docket`;
    window.open(url, 'DocketPrint', 'width=800,height=600');
}

function callApi(url, method, data) {
    fetch(url, {
        method: method,
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Accept': 'application/json',
            'Content-Type': 'application/json'
        },
        body: JSON.stringify(data)
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            swal({
                title: "Success!",
                text: data.message || "Action completed successfully!",
                type: "success",
                timer: 2000,
                showConfirmButton: false
            });
            setTimeout(() => location.reload(), 1500);
        } else {
            swal("Error!", data.message || "Action failed.", "error");
        }
    })
    .catch(error => {
        console.error('Error:', error);
        swal("Error!", "An error occurred. Please try again.", "error");
    });
}

function cancelOrderGroup(identifier, isWalkIn) {
    swal({
        title: 'Cancel Entire Order?',
        text: "This will cancel all pending items for this order. Please provide a reason:",
        type: "input",
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Yes, Cancel All',
        inputPlaceholder: "Reason for cancellation",
        closeOnConfirm: false
    }, function(reason) {
        if (reason === false) return false;
        if (reason === "") {
            swal.showInputError("You need to write a reason!");
            return false
        }
        
        swal({
            title: 'Processing...',
            text: 'Please wait while we cancel the orders.',
            showConfirmButton: false
        });

        fetch('{{ route("admin.restaurants.kitchen.orders.cancel-group") }}', { 
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({
                identifier: identifier,
                is_walk_in: isWalkIn,
                reason: reason
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                swal('Cancelled!', data.message, 'success');
                setTimeout(() => location.reload(), 1500);
            } else {
                swal('Error', data.message, 'error');
            }
        })
        .catch(error => {
            console.error(error);
            swal('Error', 'Server communication failed', 'error');
        });
    });
}

function cancelSingleOrder(orderId, itemName) {
    swal({
        title: 'Cancel Item?',
        text: `Are you sure you want to cancel "${itemName}"? Please provide a reason:`,
        type: "input",
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Yes, Cancel Item',
        inputPlaceholder: "Reason for cancellation",
        closeOnConfirm: false
    }, function(reason) {
        if (reason === false) return false;
        if (reason === "") {
            swal.showInputError("You need to write a reason!");
            return false
        }
        
        swal({
            title: 'Processing...',
            text: 'Please wait while we cancel the item.',
            showConfirmButton: false
        });

        fetch(`{{ url('/restaurant/food/orders') }}/${orderId}/cancel`, { 
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({
                reason: reason
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                swal('Cancelled!', data.message, 'success');
                setTimeout(() => location.reload(), 1500);
            } else {
                swal('Error', data.message, 'error');
            }
        })
        .catch(error => {
            console.error(error);
            swal('Error', 'Server communication failed', 'error');
        });
    });
}
</script>
@endsection

