@extends('dashboard.layouts.app')

@section('content')
<style>
    :root { --primary: #e77a31; --primary-light: #fff3e0; }

    .content-wrapper { background: #f2f4f7 !important; }
    .app-title { display: none; }
    .main-footer { display: none !important; }

    /* Sticky top bar */
    .orders-header {
        position: sticky; top: 0; z-index: 200;
        background: white;
        padding: 14px 16px 10px;
        box-shadow: 0 2px 12px rgba(0,0,0,0.08);
        display: flex; align-items: center; justify-content: space-between;
    }
    .orders-header h5 { margin: 0; font-weight: 800; font-size: 1.05rem; color: #222; }
    .back-btn {
        width: 36px; height: 36px; border-radius: 50%;
        background: var(--primary-light); color: var(--primary);
        display: flex; align-items: center; justify-content: center;
        font-size: 1rem; cursor: pointer; text-decoration: none;
    }

    /* Feed */
    .orders-feed {
        padding: 12px 12px 100px;
        display: flex; flex-direction: column; gap: 14px;
    }

    /* Order card */
    .order-card {
        background: white;
        border-radius: 18px;
        box-shadow: 0 4px 16px rgba(0,0,0,0.06);
        overflow: hidden;
    }
    .order-card-header {
        padding: 12px 16px;
        display: flex; align-items: center; justify-content: space-between;
        border-bottom: 1px solid #f0f0f0;
    }
    .order-card-header.paid-h   { background: #e8f5e9; }
    .order-card-header.pending-h { background: #fff8e1; }
    .order-card-header.room-h   { background: #e3f2fd; }

    .guest-name { font-weight: 800; font-size: 0.95rem; line-height: 1.2; color: #222; }
    .guest-meta { font-size: 0.73rem; color: #888; margin-top: 2px; }

    .pay-pill-badge {
        padding: 4px 12px; border-radius: 20px;
        font-size: 0.68rem; font-weight: 800; letter-spacing: 0.5px; white-space: nowrap;
    }
    .paid-badge    { background: #c8e6c9; color: #1b5e20; }
    .pending-badge { background: #fff9c4; color: #f57f17; }
    .room-badge    { background: #bbdefb; color: #0d47a1; }

    /* Items */
    .order-items { padding: 6px 16px 4px; }
    .order-item-row {
        display: flex; align-items: flex-start; justify-content: space-between;
        padding: 9px 0; border-bottom: 1px dashed #f0f0f0;
    }
    .order-item-row:last-child { border-bottom: none; }
    .item-left { flex: 1; padding-right: 8px; }
    .item-name  { font-weight: 700; font-size: 0.85rem; line-height: 1.3; }
    .item-qty   { font-size: 0.73rem; color: #aaa; margin-top: 2px; }
    .item-right { text-align: right; flex-shrink: 0; }
    .item-price { font-weight: 800; font-size: 0.85rem; color: var(--primary); }
    .item-status {
        display: inline-block; margin-top: 3px;
        padding: 2px 8px; border-radius: 6px;
        font-size: 0.62rem; font-weight: 800; letter-spacing: 0.3px;
    }
    .s-pending   { background: #e0e0e0; color: #555; }
    .s-preparing { background: #b3e5fc; color: #01579b; }
    .s-ready     { background: #ffe0b2; color: #e65100; }
    .s-completed { background: #c8e6c9; color: #1b5e20; }
    .s-cancelled { background: #ffcdd2; color: #b71c1c; }
    .cancel-x {
        background: none; border: none; cursor: pointer;
        font-size: 0.65rem; font-weight: 900; color: #b71c1c;
        padding: 2px 6px; border-radius: 6px; margin-left: 3px;
        background: #ffcdd2;
    }

    /* Footer */
    .order-card-footer {
        padding: 10px 16px 14px;
        background: #fafafa;
        border-top: 1px solid #f0f0f0;
    }
    .order-total-row {
        display: flex; justify-content: space-between; align-items: center;
        margin-bottom: 10px;
    }
    .order-total-label { font-size: 0.75rem; color: #999; font-weight: 700; text-transform: uppercase; }
    .order-total-value { font-weight: 900; font-size: 1rem; color: #222; }
    .order-method-text { font-size: 0.7rem; color: #27ae60; font-weight: 700; text-align: right; margin-top: 2px; }

    .action-btns { display: flex; gap: 8px; }
    .action-btn {
        flex: 1; padding: 9px 6px; border-radius: 12px;
        font-size: 0.75rem; font-weight: 800; border: none; cursor: pointer;
        text-align: center; display: flex; align-items: center;
        justify-content: center; gap: 5px; transition: 0.15s;
        text-decoration: none;
    }
    .action-btn:active { transform: scale(0.96); }
    .btn-print  { background: var(--primary-light); color: var(--primary); }
    .btn-pay    { background: #c8e6c9; color: #1b5e20; }
    .btn-add    { background: #fff9c4; color: #f57f17; }

    /* Empty state */
    .empty-state {
        text-align: center; padding: 80px 20px; color: #ccc;
    }
    .empty-state i { font-size: 4rem; margin-bottom: 16px; display: block; }
    .empty-state p { font-size: 0.95rem; font-weight: 600; }

    .pagination-wrap { padding: 0 12px 30px; }

    /* New Filtering UI Styles */
    .filter-section { padding: 8px 12px 0; background: #fff; margin-bottom: 8px; border-bottom: 1px solid #eee; }
    .search-bar { position: relative; margin-bottom: 12px; }
    .search-bar input {
        width: 100%; padding: 10px 15px 10px 40px; border-radius: 15px; border: 1px solid #ddd;
        background: #f9f9f9; font-size: 0.85rem; font-weight: 600;
    }
    .search-bar i { position: absolute; left: 15px; top: 12px; color: #aaa; font-size: 0.9rem; }
    
    .filter-tabs { display: flex; overflow-x: auto; gap: 8px; padding-bottom: 10px; scrollbar-width: none; }
    .filter-tabs::-webkit-scrollbar { display: none; }
    .tab-link {
        white-space: nowrap; padding: 6px 14px; border-radius: 20px; font-size: 0.75rem;
        font-weight: 700; color: #777; background: #f0f0f0; text-decoration: none; border: 1px solid transparent;
        transition: 0.2s;
    }
    .tab-link.active { background: #feeae0; color: #e77a31; border-color: #fcc9ae; }
</style>

{{-- Sticky header --}}
<div class="orders-header">
    <a href="{{ route('waiter.dashboard') }}" class="back-btn">
        <i class="fa fa-arrow-left"></i>
    </a>
    <h5><i class="fa fa-history mr-1 text-muted"></i> Order History</h5>
    <div style="width:36px;"></div>
</div>

{{-- Filter & Search Section --}}
<div class="filter-section">
    <div class="search-bar">
        <i class="fa fa-search"></i>
        <input type="text" id="liveSearch" value="{{ $search }}" placeholder="Search guest name or room..." autocomplete="off">
    </div>
    
    <div class="filter-tabs">
        <a href="javascript:void(0)" class="tab-link {{ $tab === 'all' ? 'active' : '' }}" data-tab="all">All</a>
        <a href="javascript:void(0)" class="tab-link {{ $tab === 'pending' ? 'active' : '' }}" data-tab="pending">Pending</a>
        <a href="javascript:void(0)" class="tab-link {{ $tab === 'unpaid' ? 'active' : '' }}" data-tab="unpaid">Unpaid</a>
        <a href="javascript:void(0)" class="tab-link {{ $tab === 'paid' ? 'active' : '' }}" data-tab="paid">Paid</a>
        <a href="javascript:void(0)" class="tab-link {{ $tab === 'completed' ? 'active' : '' }}" data-tab="completed">Completed</a>
        <a href="javascript:void(0)" class="tab-link {{ $tab === 'cancelled' ? 'active' : '' }}" data-tab="cancelled">Cancelled</a>
    </div>
</div>

<div id="ordersContainer" class="orders-feed">
    @include('dashboard.waiter-orders-partial')
</div>

{{-- Cancel Modal --}}
<div class="modal fade" id="cancelOrderModal" tabindex="-1" role="dialog" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered" role="document">
    <div class="modal-content" style="border-radius: 18px; overflow: hidden;">
      <form id="cancelOrderForm" method="POST">
        @csrf
        <div class="modal-header bg-danger text-white">
          <h5 class="modal-title"><i class="fa fa-times-circle mr-1"></i> Cancel Order Item</h5>
          <button type="button" class="close text-white" data-dismiss="modal"><span>&times;</span></button>
        </div>
        <div class="modal-body">
          <p class="mb-2">Cancel <strong id="cancelItemName"></strong>?</p>
          <div class="form-group mb-0">
            <label for="cancelReason" class="small font-weight-bold">Reason <span class="text-danger">*</span></label>
            <textarea class="form-control" id="cancelReason" name="reason" rows="3" required
                placeholder="e.g. Guest changed mind, Out of stock..."></textarea>
          </div>
        </div>
        <div class="modal-footer" style="gap:8px;">
          <button type="button" class="btn btn-secondary flex-fill" data-dismiss="modal">Keep</button>
          <button type="submit" class="btn btn-danger flex-fill">Yes, Cancel</button>
        </div>
      </form>
    </div>
  </div>
</div>

{{-- Payment Modal --}}
<div class="modal fade" id="registerPaymentModal" tabindex="-1" role="dialog" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered" role="document">
    <div class="modal-content" style="border-radius: 18px; overflow: hidden;">
      <form id="registerPaymentForm">
        @csrf
        <input type="hidden" id="payIsWalkIn" name="is_walk_in">
        <input type="hidden" id="payIsCeremony" name="is_ceremony">
        <input type="hidden" id="payIdentifier" name="identifier">
        <input type="hidden" name="payment_method" id="payMethod" value="cash">

        <div class="modal-header bg-success text-white">
          <h5 class="modal-title"><i class="fa fa-money mr-1"></i> Record Payment</h5>
          <button type="button" class="close text-white" data-dismiss="modal"><span>&times;</span></button>
        </div>
        <div class="modal-body">
          <div class="text-center mb-4">
            <p class="text-muted mb-1 small font-weight-bold">TOTAL BILL</p>
            <h2 class="text-success font-weight-bold mb-0" id="payTotalAmount">0 TZS</h2>
          </div>

          <div class="form-group mb-3">
            <label class="font-weight-bold small mb-2 d-block">PAYMENT CATEGORY</label>
            <div class="d-flex flex-wrap" style="gap: 8px;">
                <div class="pay-opt active" data-m="cash" onclick="selPayCategory('cash', this)">
                    <i class="fa fa-money d-block mb-1"></i> Cash
                </div>
                <div class="pay-opt" data-m="mobile" onclick="selPayCategory('mobile', this)">
                    <i class="fa fa-mobile d-block mb-1"></i> Mobile
                </div>
                <div class="pay-opt" data-m="bank" onclick="selPayCategory('bank', this)">
                    <i class="fa fa-university d-block mb-1"></i> Bank
                </div>
                <div class="pay-opt" data-m="card" onclick="selPayCategory('card', this)">
                    <i class="fa fa-credit-card d-block mb-1"></i> Card
                </div>
                <div class="pay-opt" data-m="online" onclick="selPayCategory('online', this)">
                    <i class="fa fa-globe d-block mb-1"></i> Online
                </div>
                <div class="pay-opt" id="roomChargeOpt" data-m="room_charge" onclick="selPayCategory('room_charge', this)" style="display:none; border-color: #0d47a1; color: #0d47a1; background: #e3f2fd;">
                    <i class="fa fa-bed d-block mb-1"></i> Room Charge
                </div>
            </div>
          </div>

          <div id="modalSubPanelMobile" class="modal-sub-panel">
            <label class="small font-weight-bold text-muted mb-1 d-block">Select Mobile Platform:</label>
            <div class="d-flex flex-wrap" style="gap:6px;">
                <div class="plat-opt" onclick="selPayPlatform('mpesa','M-PESA',this)">M-PESA</div>
                <div class="plat-opt" onclick="selPayPlatform('halopesa','HaloPesa',this)">HaloPesa</div>
                <div class="plat-opt" onclick="selPayPlatform('tigopesa','Tigo Pesa',this)">Tigo Pesa</div>
                <div class="plat-opt" onclick="selPayPlatform('airtel','Airtel Money',this)">Airtel Money</div>
                <div class="plat-opt" onclick="selPayPlatform('mixx','Mixx by Yass',this)">Mixx by Yass</div>
            </div>
          </div>
          <div id="modalSubPanelBank" class="modal-sub-panel">
            <label class="small font-weight-bold text-muted mb-1 d-block">Select Bank:</label>
            <div class="d-flex flex-wrap" style="gap:6px;">
                <div class="plat-opt" onclick="selPayPlatform('nmb','NMB Bank',this)">NMB</div>
                <div class="plat-opt" onclick="selPayPlatform('crdb','CRDB Bank',this)">CRDB</div>
                <div class="plat-opt" onclick="selPayPlatform('kcb','KCB Bank',this)">KCB</div>
                <div class="plat-opt" onclick="selPayPlatform('nbc','NBC Bank',this)">NBC</div>
                <div class="plat-opt" onclick="selPayPlatform('dtb','DTB Bank',this)">DTB</div>
            </div>
          </div>
          <div id="modalSubPanelCard" class="modal-sub-panel">
            <label class="small font-weight-bold text-muted mb-1 d-block">Select Card Type:</label>
            <div class="d-flex flex-wrap" style="gap:6px;">
                <div class="plat-opt" onclick="selPayPlatform('visa','Visa Card',this)">Visa</div>
                <div class="plat-opt" onclick="selPayPlatform('mastercard','MasterCard',this)">MasterCard</div>
                <div class="plat-opt" onclick="selPayPlatform('amex','Amex',this)">Amex</div>
            </div>
          </div>
          <div id="modalSubPanelOnline" class="modal-sub-panel">
            <label class="small font-weight-bold text-muted mb-1 d-block">Select Online Platform:</label>
            <div class="d-flex flex-wrap" style="gap:6px;">
                <div class="plat-opt" onclick="selPayPlatform('booking','Booking.com',this)">Booking.com</div>
                <div class="plat-opt" onclick="selPayPlatform('expedia','Expedia',this)">Expedia</div>
                <div class="plat-opt" onclick="selPayPlatform('agoda','Agoda',this)">Agoda</div>
                <div class="plat-opt" onclick="selPayPlatform('airbnb','Airbnb',this)">Airbnb</div>
            </div>
          </div>

          <div id="confirmedMethodBadge" class="mt-3 text-center" style="display:none;">
            <span class="badge badge-success px-3 py-2" style="font-size:0.85rem; border-radius:8px;">
                ✔ <span id="confirmedMethodText">Cash</span>
            </span>
          </div>

          <div class="form-group mt-3" id="refGroup" style="display:none;">
            <label for="payRef" class="small font-weight-bold">Reference / Transaction ID</label>
            <input type="text" class="form-control" name="payment_reference" id="payRef" placeholder="e.g. QWE123RTY">
          </div>
        </div>
        <div class="modal-footer" style="gap:8px;">
          <button type="button" class="btn btn-secondary flex-fill" data-dismiss="modal">Cancel</button>
          <button type="submit" class="btn btn-success flex-fill" id="paySumbitBtn">
            <i class="fa fa-check mr-1"></i> Confirm Payment
          </button>
        </div>
      </form>
    </div>
  </div>
</div>

<style>
    .pay-opt {
        cursor: pointer; transition: 0.2s; background: #f8f9fa;
        border: 1.5px solid #dee2e6; border-radius: 10px;
        padding: 10px 12px; text-align: center;
        font-size: 0.78rem; font-weight: 700; min-width: 65px;
    }
    .pay-opt.active { border-color: #28a745 !important; background: #e8f5e9; color: #1b5e20; }

    .modal-sub-panel {
        display: none; margin-top: 10px; padding: 12px;
        background: #f0fff4; border: 1.5px solid #28a745; border-radius: 12px;
    }
    .modal-sub-panel.visible { display: block; }

    .plat-opt {
        display: inline-block; padding: 6px 14px;
        border: 1.5px solid #28a745; border-radius: 8px;
        font-size: 0.73rem; font-weight: 700; color: #28a745;
        cursor: pointer; background: white; transition: 0.2s; margin-bottom: 4px;
    }
    .plat-opt.active { background: #28a745; color: white; }
    .modal-footer { gap: 8px; }
</style>

@endsection

@section('scripts')
<script>
function openCancelModal(orderId, itemName) {
    document.getElementById('cancelItemName').textContent = itemName;
    document.getElementById('cancelOrderForm').action = `/waiter/orders/${orderId}/cancel`;
    $('#cancelOrderModal').modal('show');
}

function openPaymentModal(isWalkIn, identifier, amount, isCeremony = 0, resp = 'self', isCorp = 0) {
    $('#payIsWalkIn').val(isWalkIn);
    $('#payIsCeremony').val(isCeremony);
    $('#payIdentifier').val(identifier);
    $('#payTotalAmount').text(amount + ' TZS');
    
    // 1. Reset everything to clean state
    $('.pay-opt').hide().removeClass('active');
    $('.modal-sub-panel').removeClass('visible');
    $('.plat-opt').removeClass('active');
    $('#refGroup').hide();
    $('#payRef').val('');
    $('#confirmedMethodBadge').hide();

    // 2. Apply rules: Self-payer vs Company-payer vs Walk-in
    if (parseInt(isWalkIn) === 1) {
        // Walk-ins are SELF-PAYERS but no room
        // Options: Cash, Mobile, Bank, Card
        $('.pay-opt[data-m="cash"]').show();
        $('.pay-opt[data-m="mobile"]').show();
        $('.pay-opt[data-m="bank"]').show();
        $('.pay-opt[data-m="card"]').show();
        
        selPayCategory('cash', $('.pay-opt[data-m="cash"]'));
    } else {
        // Residents: check responsibility
        if (resp === 'company') {
            // FORCE ROOM CHARGE
            $('#roomChargeOpt').show().addClass('active');
            selPayCategory('room_charge', $('#roomChargeOpt'));
            
            // Show notification
            Swal.fire({
                toast: true, position: 'top', timer: 3000, icon: 'info',
                title: 'Company Responsible: Forced Room Charge', background: '#e3f2fd'
            });
        } else {
            // SELF PAYER (Private Resident or Corporate Guest with self-paid services)
            // Options: Cash, Mobile, Bank, Card, Room Charge
            $('.pay-opt[data-m="cash"]').show();
            $('.pay-opt[data-m="mobile"]').show();
            $('.pay-opt[data-m="bank"]').show();
            $('.pay-opt[data-m="card"]').show();
            $('#roomChargeOpt').show();
            
            selPayCategory('cash', $('.pay-opt[data-m="cash"]'));

            // Show notification
            Swal.fire({
                toast: true, position: 'top', timer: 3000, icon: 'info',
                title: 'Self-Payer: Cash / Mobile / Bank / Card / Room Charge', background: '#fff'
            });
        }
    }
    
    $('#registerPaymentModal').modal('show');
}

function selPayCategory(category, el) {
    $('.pay-opt').removeClass('active');
    $(el).addClass('active');
    $('.modal-sub-panel').removeClass('visible');
    $('.plat-opt').removeClass('active');
    $('#refGroup').hide();
    $('#confirmedMethodBadge').hide();

    if (category === 'cash') {
        $('#payMethod').val('cash');
        $('#confirmedMethodText').text('Cash');
        $('#confirmedMethodBadge').show();
    } else if (category === 'mobile') {
        $('#payMethod').val('');
        $('#modalSubPanelMobile').addClass('visible');
    } else if (category === 'bank') {
        $('#payMethod').val('');
        $('#modalSubPanelBank').addClass('visible');
    } else if (category === 'card') {
        $('#payMethod').val('');
        $('#modalSubPanelCard').addClass('visible');
    } else if (category === 'online') {
        $('#payMethod').val('');
        $('#modalSubPanelOnline').addClass('visible');
    } else if (category === 'room_charge') {
        $('#payMethod').val('room_charge');
        $('#confirmedMethodText').text('Room Charge');
        $('#confirmedMethodBadge').show();
    }
}

function selPayPlatform(method, label, el) {
    $('#payMethod').val(method);
    $(el).closest('.modal-sub-panel').find('.plat-opt').removeClass('active');
    $(el).addClass('active');
    $('#refGroup').show();
    $('#confirmedMethodText').text(label);
    $('#confirmedMethodBadge').show();
}

// Generic modal reset removed to avoid conflict with guest-specific rules in openPaymentModal
@php /* Reset logic moved to openPaymentModal */ @endphp

document.getElementById('registerPaymentForm').addEventListener('submit', function(e) {
    e.preventDefault();
    const method = $('#payMethod').val();
    if (!method) {
        Swal.fire("Select Platform", "Please choose a specific payment platform before confirming.", "warning");
        return;
    }
    const btn = document.getElementById('paySumbitBtn');
    btn.disabled = true;
    btn.innerHTML = '<i class="fa fa-spinner fa-spin"></i> Processing...';

    $.ajax({
        url: "{{ route('waiter.orders.register-payment') }}",
        method: "POST",
        data: $(this).serialize(),
        success: function(r) {
            if (r.success) {
                Swal.fire("Paid!", r.message, "success").then(() => window.location.reload());
            } else {
                Swal.fire("Error", r.message, "error");
                btn.disabled = false;
                btn.innerHTML = '<i class="fa fa-check mr-1"></i> Confirm Payment';
            }
        },
        error: function() {
            Swal.fire("Error", "Server error. Try again.", "error");
            btn.disabled = false;
            btn.innerHTML = '<i class="fa fa-check mr-1"></i> Confirm Payment';
        }
    });
});

$(function() {
    let currentSearch = '{{ $search }}';
    let currentTab = '{{ $tab }}';
    let searchTimer;

    function updateOrdersList(page = 1) {
        const $container = $('#ordersContainer');
        $container.css({ opacity: 0.5, 'pointer-events': 'none' });

        const url = "{{ route('waiter.orders') }}";
        const data = {
            tab: currentTab,
            search: currentSearch,
            page: page
        };

        $.ajax({
            url: url,
            method: 'GET',
            data: data,
            headers: { 'X-Requested-With': 'XMLHttpRequest' },
            success: function(html) {
                $container.html(html);
                $container.css({ opacity: 1, 'pointer-events': 'auto' });
                
                // Update browser URL without reload
                const newUrl = url + '?' + $.param(data);
                window.history.pushState({}, '', newUrl);
            },
            error: function(xhr) {
                console.error('AJAX Error:', xhr);
                $container.css({ opacity: 1, 'pointer-events': 'auto' });
            }
        });
    }

    // Auto-refresh every 5 seconds
    setInterval(() => {
        // Only refresh if no modal is open to avoid UI disruption during actions
        if ($('.modal.show').length === 0) {
            updateOrdersList();
        }
    }, 5000);

    // Live Search
    $(document).on('input', '#liveSearch', function() {
        currentSearch = $(this).val();
        clearTimeout(searchTimer);
        searchTimer = setTimeout(() => {
            updateOrdersList();
        }, 400);
    });

    // Tab Switching
    $(document).on('click', '.tab-link', function(e) {
        e.preventDefault();
        $('.tab-link').removeClass('active');
        $(this).addClass('active');
        currentTab = $(this).data('tab');
        updateOrdersList();
    });

    // Pagination
    $(document).on('click', '.ajax-pagination a', function(e) {
        e.preventDefault();
        const url = new URL($(this).attr('href'));
        const page = url.searchParams.get('page');
        updateOrdersList(page);
    });
});
</script>
@endsection
