@extends('dashboard.layouts.app')

@section('content')
<div class="app-title">
    <div>
        <h1><i class="fa fa-cutlery"></i> Kitchen Dashboard</h1>
        <p>Overview of Kitchen Operations, Stock, and Shopping Lists</p>
    </div>
    <ul class="app-breadcrumb breadcrumb">
        <li class="breadcrumb-item"><i class="fa fa-home fa-lg"></i></li>
        <li class="breadcrumb-item"><a href="#">Kitchen Dashboard</a></li>
    </ul>
</div>

<div class="row">
    <!-- Stats Cards -->
    @if(!$isChef)
    <div class="col-md-6 col-lg-3">
        <div class="widget-small primary coloured-icon shadow-sm"><i class="icon fa fa-shopping-basket fa-3x"></i>
            <div class="info">
                <h4>Shopping Lists</h4>
                <p><b>{{ $stats['shopping_lists'] }}</b></p>
            </div>
        </div>
    </div>
    <div class="col-md-6 col-lg-3">
        <div class="widget-small warning coloured-icon shadow-sm"><i class="icon fa fa-clock-o fa-3x"></i>
            <div class="info">
                <h4>Pending Lists</h4>
                <p><b>{{ $stats['pending_lists'] }}</b></p>
            </div>
        </div>
    </div>
    @endif
    <div class="{{ $isChef ? 'col-md-6 col-lg-3' : 'col-md-6 col-lg-3' }}">
        <div class="widget-small danger coloured-icon shadow-sm"><i class="icon fa fa-cutlery fa-3x"></i>
            <div class="info">
                <h4>Pending Orders</h4>
                <p><b>{{ $totalPendingOrders }}</b></p>
            </div>
        </div>
    </div>
    <div class="col-md-6 col-lg-3">
        <div class="widget-small success coloured-icon shadow-sm"><i class="icon fa fa-check fa-3x"></i>
            <div class="info">
                <h4>Today's Served</h4>
                <p><b>{{ $stats['today_orders'] }}</b></p>
            </div>
        </div>
    </div>
    <div class="col-md-6 col-lg-3">
        <div class="widget-small primary coloured-icon shadow-sm"><i class="icon fa fa-money fa-3x"></i>
            <div class="info">
                <h4>Today's Revenue</h4>
                <p><b>{{ number_format($stats['total_revenue']) }}</b></p>
            </div>
        </div>
    </div>
    <div class="{{ $isChef ? 'col-md-6 col-lg-3' : 'col-md-6 col-lg-3' }}">
        <div class="widget-small info coloured-icon shadow-sm"><i class="icon fa fa-cubes fa-3x"></i>
            <div class="info">
                <h4>Stock Items</h4>
                <p><b>{{ $stats['stock_items'] }}</b></p>
            </div>
        </div>
    </div>
</div>

<!-- Live Food Orders -->
<div class="row mb-4 mt-4" id="orders">
  <div class="col-md-12">
    <div class="tile">
      <div class="tile-title-w-btn">
        <h3 class="title">Live Food Orders</h3>
        <div class="btn-group">
            @php $activeShift = Auth::guard('staff')->user()->currentShift; @endphp
            @if($activeShift)
                <a href="{{ route('chef-master.shift.close') }}" class="btn btn-danger mr-2 shadow-sm"><i class="fa fa-sign-out"></i> CLOSE SHIFT</a>
            @else
                <a href="{{ route('chef-master.shift.open') }}" class="btn btn-success mr-2 shadow-sm"><i class="fa fa-sign-in"></i> OPEN SHIFT</a>
            @endif
            <a href="{{ route('chef-master.kds') }}" class="btn btn-dark mr-2"><i class="fa fa-desktop"></i> KDS MONITOR</a>
            @if(!$isChef)
            <button class="btn btn-primary" onclick="openWalkInModal()"><i class="fa fa-plus"></i> New Walk-in Order</button>
            @endif
        </div>
      </div>
      
      @if($pendingOrders->count() > 0)
      <h4 class="mb-3" style="font-size: 16px; color: #666;">Orders Queue</h4>
      <div class="table-responsive">
        <table class="table table-hover table-bordered">
          <thead>
            <tr>
              <th>Requested At</th>
              <th>By</th>
              <th>Room / Guest</th>
              <th>Item Name</th>
              <th>Qty</th>
              <th>Notes</th>
              <th>Status</th>
              @if(!$isChef)
              <th>Action</th>
              <th>Billing</th>
              @endif
            </tr>
          </thead>
          <tbody>
            @php
              $groupedOrders = $pendingOrders->groupBy(function($item) {
                  return $item->is_walk_in ? 'w_' . ($item->walk_in_name ?? 'General') : 'b_' . ($item->booking_id ?? 'unknown');
              });
            @endphp

            @foreach($groupedOrders as $groupKey => $orders)
              @php
                $first = $orders->first();
                $guestTotal = $orders->where('payment_status', 'pending')->sum('total_price_tsh');
                // Use the most recent request time for the group
                $latestRequest = $orders->sortByDesc('requested_at')->first()->requested_at;
                
                // Check if this is a company-paid booking
                $isCompanyPaid = !$first->is_walk_in && $first->booking && $first->booking->payment_responsibility === 'company';
                
                // Get unique waiters
                $waiters = [];
                foreach($orders as $o) {
                    $requestedBy = 'Staff'; // Default
                    if ($o->reception_notes) {
                        if (str_contains($o->reception_notes, 'Waiter: ')) {
                            $parts = explode('Waiter: ', $o->reception_notes);
                            $byParts = explode(' - Msg:', $parts[1] ?? '');
                            $requestedBy = $byParts[0] ?? 'Waiter';
                        } elseif (str_contains($o->reception_notes, 'Recorded by: ')) {
                            $parts = explode('Recorded by: ', $o->reception_notes);
                            $requestedBy = trim($parts[1] ?? 'Staff');
                        }
                    }
                    
                    // Fallback to approvedBy name if notes are empty but we have an ID
                    if ($requestedBy === 'Staff' && $o->approvedBy) {
                        $requestedBy = $o->approvedBy->name;
                    }
                    $waiters[] = trim($requestedBy);
                }
                $waiters = array_unique($waiters);
              @endphp
              
              <tr style="border-top: 3px solid #e77a31;">
                <td style="vertical-align: top;">
                  <strong>{{ $latestRequest->format('H:i') }}</strong><br>
                  <small class="text-muted">{{ $latestRequest->diffForHumans() }}</small>
                </td>
                <td style="vertical-align: top;">
                  @foreach($waiters as $w)
                    <span class="badge badge-info mb-1">{{ $w }}</span><br>
                  @endforeach
                </td>
                <td style="vertical-align: top;">
                  @if($first->is_walk_in)
                      <span class="badge badge-secondary mb-1">WALK-IN</span><br>
                      <strong>{{ $first->walk_in_name ?? 'General Walk-in' }}</strong>
                      <div class="mt-2" style="font-size: 11px;">
                         <span class="text-muted">Session Bill:</span><br>
                         <span class="badge badge-outline-dark" style="border: 1px solid #ccc;">{{ number_format($orders->sum('total_price_tsh')) }} TZS</span>
                         @php
                            $paidInSession = $orders->whereIn('payment_status', ['paid', 'room_charge'])->sum('total_price_tsh');
                            $pendingInSession = $orders->sum('total_price_tsh') - $paidInSession;
                         @endphp
                         @if($paidInSession > 0)
                            <br><small class="text-success">Paid: {{ number_format($paidInSession) }}</small>
                         @endif
                         @if($pendingInSession > 0)
                            <br><small class="text-danger">Pending: {{ number_format($pendingInSession) }}</small>
                         @endif
                      </div>
                  @else
                      <strong>{{ $first->booking->room->room_number ?? 'N/A' }}</strong><br>
                      <small>{{ $first->booking->guest_name }}</small>
                  @endif
                </td>
                <td colspan="{{ $isChef ? 4 : 5 }}" class="p-0">
                  <table class="table table-sm mb-0 no-border" style="background: transparent;">
                    @foreach($orders as $order)
                    <tr style="background: transparent;">
                      <td style="width: 30%; border-top: none;">
                        <strong>{{ $order->service_specific_data['item_name'] ?? $order->service->name }}</strong>
                      </td>
                      <td style="width: 10%; border-top: none;"><strong>{{ $order->quantity }}</strong></td>
                      <td style="width: 25%; border-top: none;">
                        @php
                          $note = $order->guest_request;
                          if (!$note && $order->reception_notes && str_contains($order->reception_notes, '- Msg: ')) {
                              $parts = explode('- Msg: ', $order->reception_notes);
                              $note = $parts[1] ?? null;
                          }
                        @endphp
                        <small class="text-muted">{{ $note ?: '---' }}</small>
                      </td>
                      <td style="width: 15%; border-top: none;">
                        @if($order->status === 'pending')
                            <span class="badge badge-warning">Pending</span>
                        @elseif($order->status === 'approved')
                            <span class="badge badge-info">Approved</span>
                        @elseif($order->status === 'preparing')
                            <span class="badge badge-primary">Preparing</span>
                        @elseif($order->status === 'completed' && ($order->payment_status === 'pending' || $order->payment_status === 'unpaid'))
                            <span class="badge badge-danger shadow-sm" style="font-size: 8px;"><i class="fa fa-money"></i> Unpaid</span>
                        @else
                            <span class="badge badge-secondary">{{ ucfirst($order->status) }}</span>
                        @endif
                      </td>
                      @if(!$isChef)
                      <td style="width: 20%; border-top: none; text-align: right;">
                        <div class="btn-group">
                          @if($order->status === 'pending')
                            <button class="btn btn-xs btn-primary p-1 px-2 mr-1" onclick="startPreparingDashboard({{ $order->id }}, '{{ addslashes($order->service_specific_data['item_name'] ?? $order->service->name) }}')" title="Start Preparing">
                              <i class="fa fa-fire"></i>
                            </button>
                          @endif
                          @if($order->status !== 'completed' && $order->status !== 'cancelled')
                          <button class="btn btn-xs btn-success p-1 px-2" onclick="markAsServedOnly({{ $order->id }}, '{{ addslashes($order->service_specific_data['item_name'] ?? $order->service->name) }}')" title="Mark Served">
                            <i class="fa fa-check"></i>
                          </button>
                          <button class="btn btn-xs btn-outline-danger p-1 px-2 ml-1" onclick="cancelOrderItem({{ $order->id }}, '{{ addslashes($order->service_specific_data['item_name'] ?? $order->service->name) }}')" title="Cancel Item">
                            <i class="fa fa-times"></i>
                          </button>
                          @endif
                        </div>
                      </td>
                      @endif
                    </tr>
                    @endforeach
                  </table>
                </td>
                <td style="vertical-align: top;">
                  @if(!$isChef)
                  <div class="btn-group-vertical btn-group-sm w-100">
                    @php
                      $printUrl = route('admin.restaurants.kitchen.orders.print-group', [
                          'is_walk_in' => $first->is_walk_in ? 1 : 0,
                          'identifier' => $first->is_walk_in ? $first->walk_in_name : $first->booking_id
                      ]);
                    @endphp
                    
                    <button class="btn btn-sm btn-info mb-1" onclick="window.open('{{ $printUrl }}', 'Print', 'width=800,height=600')" title="Print All Items">
                      <i class="fa fa-print"></i> Print Bill
                    </button>
                    
                    @if(($first->payment_status === 'pending' || $first->payment_status === 'unpaid'))
                        <button class="btn btn-sm btn-warning mb-1" 
                                onclick="openAddItemsToGrouping('{{ $first->is_walk_in ? 1 : 0 }}', '{{ $first->is_walk_in ? addslashes($first->walk_in_name) : $first->booking_id }}', '{{ $first->is_walk_in ? '' : addslashes($first->booking->guest_name ?? '') }}', '{{ $first->is_walk_in ? '' : ($first->booking->room->room_number ?? '') }}')" 
                                title="Add More Items">
                          <i class="fa fa-plus"></i> Add Items
                        </button>
                        
                        <button class="btn btn-sm btn-outline-danger mb-1" 
                                onclick="cancelOrderGroup('{{ $first->is_walk_in ? 1 : 0 }}', '{{ $first->is_walk_in ? addslashes($first->walk_in_name) : $first->booking_id }}')" 
                                title="Cancel Entire Order">
                          <i class="fa fa-times-circle"></i> Cancel All
                        </button>
                    @endif

                    @if($first->payment_status === 'pending' && !$isCompanyPaid)
                        <button class="btn btn-sm btn-outline-success mb-1" onclick="openPaymentModal({{ $first->id }}, {{ $guestTotal }}, '{{ addslashes($first->walk_in_name ?? $first->booking->room->room_number ?? '') }}', {{ $first->is_walk_in ? 1 : 0 }})" title="Record Total Payment">
                          <i class="fa fa-money"></i> PAY: {{ number_format($guestTotal) }}
                        </button>
                    @endif
                    
                    @if($isCompanyPaid && $guestTotal > 0)
                        <span class="badge badge-info p-2 mb-1" style="font-size: 11px;">
                          <i class="fa fa-building"></i> Company Paid
                        </span>
                    @endif
                  </div>
                  @endif
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



<div class="row">
@if(!$isChef)
    <!-- Recent Shopping Lists -->
    <div class="col-md-6">
        <div class="tile shadow-sm">
            <h3 class="tile-title">Recent Shopping Lists</h3>
            <div class="table-responsive">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Date</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($recentLists as $list)
                        <tr>
                            <td>{{ $list->name }}</td>
                            <td>{{ $list->created_at->format('d M') }}</td>
                            <td>
                                @if($list->status == 'completed')
                                    <span class="badge badge-success">Completed</span>
                                @else
                                    <span class="badge badge-warning">Pending</span>
                                @endif
                            </td>
                            <td>
                                <a href="{{ route('admin.restaurants.shopping-list.show', $list->id) }}" class="btn btn-sm btn-info"><i class="fa fa-eye"></i></a>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="tile-footer border-top">
                <a href="{{ route('admin.restaurants.shopping-list.index') }}" class="btn btn-primary btn-block shadow-sm">View All Lists</a>
            </div>
        </div>
    </div>
@endif

    <!-- Quick Actions -->
    <div class="{{ $isChef ? 'col-md-12' : 'col-md-6' }}">
        <div class="tile shadow-sm h-100">
            <h3 class="tile-title">Quick Operational Actions</h3>
            <div class="row">
                @if(!$isChef)
                <div class="col-md-6 mb-3">
                    <a href="{{ route('admin.restaurants.shopping-list.create') }}" class="btn btn-outline-primary btn-block p-4 shadow-sm h-100 d-flex flex-column align-items-center justify-content-center">
                        <i class="fa fa-plus-square fa-2x mb-2"></i>
                        <span>New Shopping List</span>
                    </a>
                </div>
                @endif
                <div class="{{ $isChef ? 'col-md-3' : 'col-md-6' }} mb-3">
                    <a href="{{ route('admin.restaurants.kitchen.orders') }}" class="btn btn-warning btn-block p-4 shadow-sm h-100 d-flex flex-column align-items-center justify-content-center">
                        <i class="fa fa-bell fa-2x mb-2"></i>
                        <span>Live Food Orders</span>
                    </a>
                </div>
                <div class="{{ $isChef ? 'col-md-3' : 'col-md-6' }} mb-3">
                    <a href="{{ route('admin.recipes.index') }}" class="btn btn-success btn-block p-4 shadow-sm h-100 d-flex flex-column align-items-center justify-content-center">
                        <i class="fa fa-book fa-2x mb-2"></i>
                        <span>Recipes Catalog</span>
                    </a>
                </div>
                <div class="{{ $isChef ? 'col-md-3' : 'col-md-6' }} mb-3">
                    <a href="{{ $isChef ? route('chef-master.inventory') : route('admin.restaurants.kitchen.stock') }}" class="btn btn-info btn-block p-4 shadow-sm h-100 d-flex flex-column align-items-center justify-content-center">
                        <i class="fa fa-cubes fa-2x mb-2"></i>
                        <span>Inventory Stock</span>
                    </a>
                </div>
                <div class="{{ $isChef ? 'col-md-3' : 'col-md-6' }} mb-3">
                    <a href="{{ $isChef ? route('chef-master.reports') : route('admin.restaurants.reports') }}" class="btn btn-danger btn-block p-4 shadow-sm h-100 d-flex flex-column align-items-center justify-content-center">
                        <i class="fa fa-bar-chart fa-2x mb-2"></i>
                        <span>Kitchen Reports</span>
                    </a>
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
              <div class="input-group">
                <div class="input-group-prepend">
                  <span class="input-group-text bg-white border-right-0"><i class="fa fa-search text-muted"></i></span>
                </div>
                <input type="text" id="itemSearch" class="form-control border-left-0" placeholder="Search food items..." onkeyup="filterItems()">
              </div>
            </div>
            
            <div class="p-3" id="posItemList">
              <div class="mb-4">
                <h6 class="text-primary mb-3 font-weight-bold d-flex align-items-center">
                    <i class="fa fa-cutlery mr-2"></i> Food & Restaurant
                </h6>
                <div class="row">
                  @foreach($recipes as $food)
                  <div class="col-6 mb-3 pos-item-card" data-name="{{ strtolower($food->name) }}">
                    <div class="card h-100 border rounded-lg hover-shadow transition-all" onclick="addToPosCart('{{ addslashes($food->name) }}', {{ $food->price_tsh }}, null, null, 'food', '{{ $food->id }}', '{{ $food->image }}')" style="cursor: pointer;">
                      <div class="card-body p-2 text-center">
                        <img src="{{ $food->image }}" class="img-fluid mb-2 rounded" style="height: 60px; object-fit: contain;" onerror="this.onerror=null;this.src='https://img.icons8.com/color/144/restaurant.png'">
                        <h6 class="card-title mb-1 text-dark" style="font-size: 0.85rem; height: 1.5rem; overflow: hidden;">{{ $food->name }}</h6>
                        <div class="badge badge-light-info px-2 py-1 mb-2">{{ number_format($food->price_tsh) }} TZS</div>
                        <button class="btn btn-sm btn-primary btn-block"><i class="fa fa-plus"></i> ADD</button>
                      </div>
                    </div>
                  </div>
                  @endforeach
                </div>
              </div>
            </div>
          </div>
          
          <!-- Cart/Summary -->
          <div class="col-md-5 d-flex flex-column bg-light" style="height: 70vh;">
            <div class="p-3 border-bottom bg-white">
               <h6 class="font-weight-bold mb-2 d-flex align-items-center">
                   <i class="fa fa-user mr-2 text-primary"></i> Guest Information
               </h6>
               <input type="text" id="walkInGuestName" class="form-control mb-2" placeholder="Guest Name (Optional)">
               <textarea id="walkInSpecialNotes" class="form-control" rows="2" placeholder="Special Notes (e.g. No spicy, Well done)"></textarea>
            </div>
            
            <div class="flex-grow-1 p-3" id="posCartList" style="overflow-y: auto;">
              <div class="text-center text-muted mt-5">
                <i class="fa fa-shopping-basket fa-3x mb-3 opacity-50"></i>
                <p>Click items on the left to add to cart</p>
              </div>
            </div>
            
            <div class="p-3 border-top bg-white mt-auto">
              <div class="d-flex justify-content-between mb-2">
                <span class="font-weight-bold text-muted">Total Amount:</span>
                <span class="font-weight-bold text-primary h5 mb-0" id="posTotalAmount">0 TZS</span>
              </div>
              <div class="row no-gutters">
                  <div class="col-8 pr-1">
                      <button class="btn btn-success btn-lg btn-block shadow-sm py-3" onclick="processWalkInCheckout()" id="btnConfirmSale" disabled>
                        <i class="fa fa-check-circle mr-1"></i> <strong>ADD</strong>
                      </button>
                  </div>
                  <div class="col-4 pl-1">
                      <button class="btn btn-secondary btn-lg btn-block shadow-sm py-3" data-dismiss="modal">
                        <strong>CANCEL</strong>
                      </button>
                  </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<style>
.hover-shadow:hover {
    box-shadow: 0 4px 15px rgba(0,0,0,0.1);
    border-color: #007bff !important;
}
.transition-all {
    transition: all 0.2s ease-in-out;
}
.badge-light-primary {
    background-color: #e3f2fd;
    color: #1976d2;
}
.badge-light-info {
    background-color: #e0f7fa;
    color: #0097a7;
}
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
<script>
// POS Logic
let posCart = [];
let currentBookingId = null;

function openWalkInModal(residentRoom, residentName, dayServiceId, bookingId) {
    $('#walkInModal').modal('show');
    posCart = [];
    currentBookingId = bookingId || null;
    document.getElementById('posModalTitle').innerHTML = '<i class="fa fa-shopping-cart mr-2"></i> New Walk-in Order';
    
    // Reset inputs
    document.getElementById('walkInGuestName').value = residentName || '';
    if(document.getElementById('walkInSpecialNotes')) {
        document.getElementById('walkInSpecialNotes').value = '';
    }
    
    // Store dayServiceId in a global variable for checkout
    window.currentDayServiceId = dayServiceId || null;
    renderPosCart();
}

function openAddItemsToGrouping(isWalkIn, identifier, guestName, roomNumber) {
    posCart = [];
    renderPosCart();
    
    if (isWalkIn == '1') {
        currentBookingId = null;
        document.getElementById('walkInGuestName').value = identifier;
        document.getElementById('posModalTitle').innerHTML = '<i class="fa fa-plus mr-2"></i> Add Items to: ' + identifier;
    } else {
        currentBookingId = identifier; 
        document.getElementById('walkInGuestName').value = guestName || '';
        document.getElementById('posModalTitle').innerHTML = '<i class="fa fa-plus mr-2"></i> Add Items for Room: ' + (roomNumber || identifier);
    }
    
    $('#walkInModal').modal('show');
}

function cancelOrderItem(id, itemName) {
    Swal.fire({
        title: "Cancel Item?",
        text: "Are you sure you want to cancel " + itemName + "?",
        icon: "warning",
        input: 'text',
        inputPlaceholder: 'Reason for cancellation...',
        showCancelButton: true,
        confirmButtonColor: "#d33",
        confirmButtonText: "Yes, Cancel It",
        preConfirm: function(reason) {
            if (!reason) {
                Swal.showValidationMessage('A reason is required to cancel this item');
                return false;
            }
            return reason;
        }
    }).then(function(result) {
        if (!result.isConfirmed) return;
        var reason = result.value;

        fetch('{{ url("/restaurant/food/orders") }}/' + id + '/cancel', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Accept': 'application/json',
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({ reason: reason })
        }).then(function(response) {
            return response.json();
        }).then(function(res) {
            if (res.success) {
                showSuccessToast(res.message);
                setTimeout(function() { location.reload(); }, 1500);
            } else {
                Swal.fire("Error", res.message, "error");
            }
        }).catch(function(e) {
            Swal.fire("Error", "Connectivity issue", "error");
        });
    });
}

function cancelOrderGroup(isWalkIn, identifier) {
    Swal.fire({
        title: "Cancel Entire Order?",
        text: "This will cancel ALL pending items for " + identifier + ".",
        icon: "warning",
        input: 'text',
        inputPlaceholder: 'Reason for cancellation...',
        showCancelButton: true,
        confirmButtonColor: "#d33",
        confirmButtonText: "Yes, Cancel All",
        preConfirm: function(reason) {
            if (!reason) {
                Swal.showValidationMessage('A reason is required to cancel this order group');
                return false;
            }
            return reason;
        }
    }).then(function(result) {
        if (!result.isConfirmed) return;
        var reason = result.value;

        fetch('{{ route("admin.restaurants.kitchen.orders.cancel-group") }}', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Accept': 'application/json',
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({
                is_walk_in: isWalkIn,
                identifier: identifier,
                reason: reason
            })
        }).then(function(response) {
            return response.json();
        }).then(function(res) {
            if (res.success) {
                showSuccessToast(res.message);
                setTimeout(function() { location.reload(); }, 1500);
            } else {
                Swal.fire("Error", res.message, "error");
            }
        }).catch(function(e) {
            Swal.fire("Error", "Connectivity issue", "error");
        });
    });
}

function addToPosCart(name, price, pid, vid, type, foodId, image) {
    var key = foodId ? foodId : (pid + "_" + vid);
    var existing = null;
    for (var i = 0; i < posCart.length; i++) {
        if (posCart[i].key === key) {
            existing = posCart[i];
            break;
        }
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
            qty: 1
        });
    }
    renderPosCart();
}

function removeFromPosCart(key) {
    var newCart = [];
    for (var i = 0; i < posCart.length; i++) {
        if (posCart[i].key !== key) {
            newCart.push(posCart[i]);
        }
    }
    posCart = newCart;
    renderPosCart();
}

function updatePosQty(key, delta) {
    var item = null;
    for (var i = 0; i < posCart.length; i++) {
        if (posCart[i].key === key) {
            item = posCart[i];
            break;
        }
    }
    if (item) {
        item.qty += delta;
        if (item.qty <= 0) {
            removeFromPosCart(key);
        } else {
            renderPosCart();
        }
    }
}

function renderPosCart() {
    const list = document.getElementById('posCartList');
    const totalEl = document.getElementById('posTotalAmount');
    const btn = document.getElementById('btnConfirmSale');
    
    if (posCart.length === 0) {
        list.innerHTML = '<div class="text-center text-muted mt-5"><i class="fa fa-shopping-basket fa-3x mb-2"></i><p>Cart is empty</p></div>';
        totalEl.innerText = '0 TZS';
        btn.disabled = true;
        return;
    }
    
    let total = 0;
    let html = '';
    for (let i = 0; i < posCart.length; i++) {
        let item = posCart[i];
        const itemTotal = item.price * item.qty;
        total += itemTotal;
        html += '<div class="d-flex justify-content-between align-items-center mb-3 bg-white p-2 rounded shadow-sm">';
        html += '<div class="d-flex align-items-center" style="max-width: 65%;">';
        if (item.image) {
            html += '<img src="' + item.image + '" class="mr-2 rounded" style="width: 40px; height: 40px; object-fit: cover;" onerror="this.onerror=null;this.src=\'https://img.icons8.com/color/144/restaurant.png\'">';
        }
        html += '<div>';
        html += '<div class="font-weight-bold" style="font-size: 0.85rem; line-height: 1.2;">' + item.name + '</div>';
        html += '<small class="text-muted">' + item.price.toLocaleString() + ' x ' + item.qty + '</small>';
        html += '</div></div>';
        html += '<div class="d-flex align-items-center">';
        html += '<div class="btn-group btn-group-sm mr-2">';
        html += '<button class="btn btn-outline-secondary px-2" onclick="updatePosQty(\'' + item.key + '\', -1)">-</button>';
        html += '<button class="btn btn-outline-secondary px-2" onclick="updatePosQty(\'' + item.key + '\', 1)">+</button>';
        html += '</div>';
        html += '<div class="text-right mr-2" style="min-width: 70px;">';
        html += '<span class="font-weight-bold d-block" style="font-size: 0.85rem;">' + itemTotal.toLocaleString() + '</span>';
        html += '</div>';
        html += '<button class="btn btn-sm btn-outline-danger border-0" onclick="removeFromPosCart(\'' + item.key + '\')">';
        html += '<i class="fa fa-trash"></i></button></div></div>';
    }
    
    list.innerHTML = html;
    totalEl.innerText = total.toLocaleString() + ' TZS';
    btn.disabled = false;
}

function filterItems() {
    var query = document.getElementById('itemSearch').value.toLowerCase();
    var cards = document.querySelectorAll('.pos-item-card');
    for (var i = 0; i < cards.length; i++) {
        var card = cards[i];
        if (card.getAttribute('data-name').indexOf(query) !== -1) {
            card.style.display = '';
        } else {
            card.style.display = 'none';
        }
    }
}

function processWalkInCheckout() {
    var guestNameInput = document.getElementById('walkInGuestName').value.trim();
    var guestName = currentBookingId ? '' : (guestNameInput || 'General Walk-in');
    var specialNotes = document.getElementById('walkInSpecialNotes').value.trim();
    
    Swal.fire({
        title: "Confirm Order?",
        text: "Record items for " + (guestName || 'Resident Guest') + "?",
        icon: "question",
        showCancelButton: true,
        confirmButtonText: "Yes, Record Order"
    }).then(function(confirm) {
        if (!confirm.isConfirmed) return;

        $('#walkInModal').modal('hide');
        Swal.fire({
            title: 'Processing...',
            didOpen: function() { Swal.showLoading(); }
        });
        
        var successCount = 0;
        var totalItems = posCart.length;
        var lastError = 'Unknown error';
        
        var processPromises = posCart.map(function(item) {
            var payload = {
                service_id: 4, // ID 4 is Generic Food Order
                quantity: item.qty,
                is_walk_in: currentBookingId ? 0 : 1,
                booking_id: currentBookingId,
                walk_in_name: guestName,
                day_service_id: window.currentDayServiceId || null,
                payment_timing: 'later',
                item_name: item.name,
                guest_request: specialNotes, // Map special notes to guest_request
                service_specific_data: {
                    food_id: item.foodId,
                    item_name: item.name,
                    special_notes: specialNotes
                }
            };

            return fetch("{{ route('customer.services.request') }}", {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Accept': 'application/json',
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify(payload)
            }).then(function(response) { 
                return response.json(); 
            }).then(function(res) {
                if (res.success) {
                    successCount++;
                } else {
                    // Extract detailed validation errors if they exist
                    if (res.errors) {
                        var errorMsgs = [];
                        for (var field in res.errors) {
                            errorMsgs.push(res.errors[field].join(', '));
                        }
                        lastError = errorMsgs.join('; ');
                    } else {
                        lastError = res.message || 'Validation failed';
                    }
                    console.error('Order Item Failed:', res);
                }
            }).catch(function(e) {
                console.error(e);
                lastError = 'Network or Server Error';
            });
        });

        Promise.all(processPromises).then(function() {
            if (successCount === totalItems) {
                showSuccessToast("Walk-in order recorded successfully!");
                setTimeout(function() {
                    location.reload();
                }, 1500);
            } else {
                Swal.fire({
                    title: "Completed with issues",
                    html: "Only " + successCount + " of " + totalItems + " items were recorded.<br><br><span class='text-danger'>Last Error: " + lastError + "</span>",
                    icon: "warning"
                }).then(function() {
                    location.reload();
                });
            }
        });
    });
}

function completeOrder(orderId, paymentMethod, reference) {
    if (!paymentMethod) paymentMethod = 'room_charge';
    if (!reference) reference = '';
    
    var title = paymentMethod === 'cash' ? "Record Cash Payment" : "Charge to Room";
    var text = paymentMethod === 'cash' ? "Record this walk-in sale as PAID (Cash)?" : "Mark this order as served and charge to the ROOM bill?";
    var icon = paymentMethod === 'cash' ? "success" : "info";
    var btnColor = paymentMethod === 'cash' ? "#28a745" : "#007bff";

    if (paymentMethod !== 'cash' && paymentMethod !== 'room_charge') {
        title = "Record " + paymentMethod.replace('_', ' ').toUpperCase() + " Payment";
        text = "Record this sale as PAID via " + paymentMethod.replace('_', ' ') + (reference ? " (Ref: " + reference + ")" : "") + "?";
        icon = "warning";
    }
    
    Swal.fire({
        title: title,
        text: text,
        icon: icon,
        showCancelButton: true,
        confirmButtonColor: btnColor,
        cancelButtonColor: "#6c757d",
        confirmButtonText: "Yes, Proceed",
        cancelButtonText: "Cancel"
    }).then(function(result) {
        if (result.isConfirmed) {
            // Use the kitchen order complete route
            var url = '/restaurant/food/orders/' + orderId + '/complete';
            fetch(url, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Accept': 'application/json',
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({
                    payment_method: paymentMethod,
                    payment_reference: reference
                })
            })
            .then(function(response) { return response.json(); })
            .then(function(data) {
                if (data.success) {
                    showSuccessToast(data.message);
                    setTimeout(function() { location.reload(); }, 1500);
                } else {
                    Swal.fire("Error!", data.message, "error");
                }
            })
            .catch(function(error) {
                console.error('Error:', error);
                Swal.fire("Error!", "An error occurred. Please try again.", "error");
            });
        }
    });
}

function markAsServedOnly(orderId, itemName) {
    Swal.fire({
        title: "Confirm Service",
        text: "Confirm that " + itemName + " has been served? (Payment status will not be changed)",
        icon: "success",
        showCancelButton: true,
        confirmButtonColor: "#28a745",
        confirmButtonText: "Yes, Served"
    }).then(function(result) {
        if (result.isConfirmed) {
            var url = '/restaurant/food/orders/' + orderId + '/complete';
            fetch(url, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Accept': 'application/json',
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({}) // Empty body to skip payment update
            })
            .then(function(response) { return response.json(); })
            .then(function(data) {
                if (data.success) {
                    showSuccessToast("Order marked as served!");
                    setTimeout(function() { location.reload(); }, 1500);
                } else {
                    Swal.fire("Error!", data.message, "error");
                }
            })
            .catch(function(e) {
                console.error(e);
                Swal.fire("Error", "Failed to communicate with server", "error");
            });
        }
    });
}

function startPreparingDashboard(orderId, itemName) {
    Swal.fire({
        title: "Start Preparation?",
        text: "Begin preparing " + itemName + "?",
        icon: "info",
        showCancelButton: true,
        confirmButtonColor: "#3498db",
        confirmButtonText: "Yes, Start!"
    }).then(function(result) {
        if (result.isConfirmed) {
            var url = '/restaurant/food/orders/' + orderId + '/preparing';
            fetch(url, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Accept': 'application/json',
                    'Content-Type': 'application/json'
                }
            })
            .then(function(response) { return response.json(); })
            .then(function(data) {
                if (data.success) {
                    showSuccessToast("Preparation timer begun.");
                    setTimeout(function() { location.reload(); }, 1500);
                } else {
                    Swal.fire("Error!", data.message, "error");
                }
            })
            .catch(function(e) {
                console.error(e);
                Swal.fire("Error", "Failed to communicate with server", "error");
            });
        }
    });
}

function openPaymentModal(orderId, amount, guestName, isWalkIn) {
    if (!guestName) guestName = '';
    if (!isWalkIn) isWalkIn = 0;
    
    document.getElementById('paymentOrderId').value = orderId;
    
    var displayAmount = Number(amount).toLocaleString() + ' TZS';
    if (guestName) {
        displayAmount += ' (Guest Total)';
        console.log("Group payment for: " + guestName);
    }
    
    document.getElementById('paymentAmountDisplay').innerText = displayAmount;
    
    var methodSelect = document.getElementById('paymentMethod');
    var roomChargeOption = methodSelect.querySelector('option[value="room_charge"]');
    
    if (isWalkIn) {
        if (roomChargeOption) roomChargeOption.style.display = 'none';
        if (methodSelect.value === 'room_charge') methodSelect.value = 'cash';
    } else {
        if (roomChargeOption) roomChargeOption.style.display = 'block';
    }

    document.getElementById('paymentMethod').value = 'cash';
    document.getElementById('paymentReference').value = '';
    toggleRefField();
    $('#paymentModal').modal('show');
}

function toggleRefField() {
    var method = document.getElementById('paymentMethod').value;
    var container = document.getElementById('refFieldContainer');
    if (method === 'cash' || method === 'room_charge') {
        container.style.display = 'none';
    } else {
        container.style.display = 'block';
    }
}

function submitPayment() {
    var orderId = document.getElementById('paymentOrderId').value;
    var method = document.getElementById('paymentMethod').value;
    var reference = document.getElementById('paymentReference').value.trim();
    
    if (method !== 'cash' && method !== 'room_charge' && !reference) {
        Swal.fire("Missing Info", "Please enter a reference number for " + method.replace('_', ' ').toUpperCase(), "warning");
        return;
    }
    
    $('#paymentModal').modal('hide');
    
    // For the dashboard, we use a unified settlement function
    settlePOSPayment(orderId, method, reference);
}

function settlePOSPayment(orderId, method, reference) {
    if (!reference) reference = '';
    
    Swal.fire({
        title: "Confirm Payment?",
        text: "Record " + method.toUpperCase() + " payment of " + document.getElementById('paymentAmountDisplay').innerText + "?",
        icon: "success",
        showCancelButton: true,
        confirmButtonColor: "#28a745",
        confirmButtonText: "Yes, Paid!"
    }).then(function(result) {
        if (result.isConfirmed) {
            var url = '/customer/pos/settle-payment/' + orderId;
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
            .then(function(response) { return response.json(); })
            .then(function(data) {
                if (data.success) {
                    Swal.fire("Success!", data.message, "success");
                    setTimeout(function() { location.reload(); }, 1500);
                } else {
                    Swal.fire("Error!", data.message, "error");
                }
            })
            .catch(function(error) {
                console.error('Error:', error);
                Swal.fire("Error!", "Failed to record payment.", "error");
            });
        }
    });
}



    function printDocket(orderId) {
        var url = '/restaurant/food/orders/' + orderId + '/print-docket';
        window.open(url, 'KitchenDocketPrint', 'width=400,height=600');
    }
</script>
@endsection
