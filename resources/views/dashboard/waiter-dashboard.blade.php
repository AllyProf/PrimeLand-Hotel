@extends('dashboard.layouts.app')

@section('content')
<style>
    :root {
        --primary: #e77a31;
        --primary-dark: #cc6a27;
        --primary-light: #fff3e0;
        --bg-gray: #f2f4f7;
        --text-main: #2d3436;
        --radius: 20px;
    }

    /* Mobile First Layout Overrides */
    body { background-color: var(--bg-gray); }
    .content-wrapper { padding: 0 !important; margin: 0 !important; }
    .main-footer { display: none !important; }

    /* Sticky Header */
    .pos-header {
        position: sticky;
        top: 0;
        z-index: 1000;
        background: white;
        padding: 10px 15px;
        box-shadow: 0 2px 10px rgba(0,0,0,0.05);
    }

    .search-pill {
        background: var(--bg-gray);
        border-radius: 50px;
        padding: 8px 20px;
        display: flex;
        align-items: center;
        gap: 10px;
        margin-top: 10px;
    }
    .search-pill input {
        border: none;
        background: transparent;
        width: 100%;
        outline: none;
        font-weight: 600;
    }

    /* Categories / Tabs */
    .cat-nav {
        display: flex;
        overflow-x: auto;
        padding: 15px;
        gap: 12px;
        scrollbar-width: none;
    }
    .cat-nav::-webkit-scrollbar { display: none; }
    
    .cat-item {
        flex: 0 0 auto;
        padding: 10px 20px;
        background: white;
        border-radius: 50px;
        font-weight: 800;
        color: var(--text-main);
        box-shadow: 0 4px 6px rgba(0,0,0,0.02);
        cursor: pointer;
        transition: 0.3s;
        border: 2px solid transparent;
        font-size: 0.9rem;
    }
    .cat-item.active {
        background: var(--primary);
        color: white;
        box-shadow: 0 8px 15px rgba(231,122,49,0.2);
    }

    /* Menu Grid */
    .menu-grid {
        padding: 10px 15px 120px;
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 15px;
    }
    @media (max-width: 767px) {
        #ceremonyGrid {
            grid-template-columns: 1fr;
        }
    }
    @media (min-width: 768px) {
        .menu-grid { grid-template-columns: repeat(3, 1fr); }
    }
    @media (min-width: 1200px) {
        .menu-grid { grid-template-columns: repeat(4, 1fr); }
    }

    .item-card {
        background: white;
        border-radius: var(--radius);
        overflow: hidden;
        box-shadow: 0 5px 15px rgba(0,0,0,0.03);
        display: flex;
        flex-direction: column;
        border: 1px solid #eee;
    }
    .item-img {
        height: 110px;
        background: #f9f9f9;
        display: flex;
        align-items: center;
        justify-content: center;
        position: relative;
    }
    .item-img img { width: 100%; height: 100%; object-fit: cover; }
    .item-price-tag {
        position: absolute;
        bottom: 8px;
        right: 8px;
        background: white;
        padding: 2px 8px;
        border-radius: 8px;
        font-weight: 900;
        font-size: 0.75rem;
        color: var(--primary);
        box-shadow: 0 2px 5px rgba(0,0,0,0.1);
    }
    .item-info { padding: 10px; }
    .item-name {
        font-size: 0.85rem;
        font-weight: 800;
        margin-bottom: 8px;
        line-height: 1.2;
        min-height: 2rem;
    }
    
    .add-btn-small {
        width: 100%;
        padding: 6px;
        border-radius: 10px;
        background: var(--primary-light);
        color: var(--primary);
        border: 1.5px solid var(--primary);
        font-weight: 800;
        font-size: 0.7rem;
        transition: 0.2s;
    }
    .add-btn-small:active { transform: scale(0.95); background: var(--primary); color: white; }

    /* Bottom Navigation / Float Action Button */
    .bottom-bar {
        position: fixed;
        bottom: 20px;
        left: 50%;
        transform: translateX(-50%);
        width: 90%;
        background: white;
        border-radius: 30px;
        box-shadow: 0 10px 30px rgba(0,0,0,0.2);
        padding: 10px 20px;
        display: flex;
        justify-content: space-between;
        align-items: center;
        z-index: 1000;
    }
    .view-cart-btn {
        background: var(--primary);
        color: white;
        padding: 12px 25px;
        border-radius: 20px;
        font-weight: 900;
        display: flex;
        align-items: center;
        gap: 10px;
        box-shadow: 0 5px 15px rgba(231,122,49,0.3);
    }

    /* Cart Drawer (Overlay) */
    .cart-drawer {
        position: fixed;
        bottom: -100%;
        left: 0;
        width: 100%;
        height: 90vh;
        background: white;
        z-index: 1040;
        border-radius: 30px 30px 0 0;
        transition: bottom 0.4s cubic-bezier(0.4, 0, 0.2, 1);
        display: flex;
        flex-direction: column;
        box-shadow: 0 -10px 40px rgba(0,0,0,0.2);
    }
    .cart-drawer.open { bottom: 0; }
    .drawer-handle {
        width: 40px;
        height: 4px;
        background: #ddd;
        border-radius: 10px;
        margin: 15px auto 5px;
    }
    .drawer-header { padding: 10px 25px 20px; border-bottom: 1px solid #f0f0f0; }
    .cart-body { flex: 1; overflow-y: auto; padding: 20px; }
    .drawer-footer { padding: 20px 25px 40px; background: #fafafa; border-top: 1px solid #eee; }

    /* Quantity Controls */
    .qty-box {
        display: flex;
        align-items: center;
        gap: 15px;
        background: var(--bg-gray);
        padding: 5px 12px;
        border-radius: 12px;
    }

    /* Selection Pills */
    .type-pill {
        flex: 1;
        padding: 10px;
        border: 2px solid #eee;
        border-radius: 15px;
        text-align: center;
        font-weight: 800;
        cursor: pointer;
    }
    .type-pill.active { border-color: var(--primary); color: var(--primary); background: var(--primary-light); }

    .overlay-backdrop {
        position: fixed;
        top: 0; left: 0; width: 100%; height: 100%;
        background: rgba(0,0,0,0.5);
        z-index: 1039;
        display: none;
    }

    .pay-pill {
        flex: 0 0 auto;
        padding: 8px 14px;
        border: 1.5px solid #ddd;
        border-radius: 12px;
        text-align: center;
        font-weight: 700;
        font-size: 0.75rem;
        cursor: pointer;
        background: #f8f9fa;
        white-space: nowrap;
        transition: 0.2s;
    }
    .pay-pill.active {
        background: #27ae60;
        color: white;
        border-color: #27ae60;
    }
    .pay-pill[data-method="later"].active {
        background: var(--primary);
        border-color: var(--primary);
    }

    /* Payment Sub-panel (platform pickers) */
    .pay-sub-panel {
        display: none;
        margin-top: 10px;
        padding: 10px;
        background: #f0fff4;
        border: 1.5px solid #27ae60;
        border-radius: 14px;
        flex-wrap: wrap;
        gap: 8px;
    }
    .pay-sub-panel.visible { display: flex; }
    .platform-pill {
        padding: 7px 12px;
        border: 1.5px solid #27ae60;
        border-radius: 10px;
        font-size: 0.72rem;
        font-weight: 700;
        color: #27ae60;
        cursor: pointer;
        background: white;
        white-space: nowrap;
        transition: 0.2s;
    }
    .platform-pill.active {
        background: #27ae60;
        color: white;
    }

    .selected-method-badge {
        display: none;
        margin-top: 8px;
        padding: 5px 12px;
        background: #eafaf1;
        border-radius: 8px;
        font-size: 0.78rem;
        font-weight: 700;
        color: #27ae60;
    }
    .selected-method-badge.visible { display: block; }
</style>

<!-- POS Header -->
<div class="pos-header">
    <div class="d-flex align-items-center justify-content-between">
        <div>
            <span class="text-muted small font-weight-bold">WAITER POS</span>
            <h4 class="font-weight-bold mb-0">PrimeLand Hotel</h4>
            <div id="servingIndicator" class="badge badge-info mt-1" style="display: none; border-radius: 50px; font-size: 0.7rem; padding: 4px 10px;">
                <i class="fa fa-user mr-1"></i> Serving: <span id="servingName">...</span>
            </div>
        </div>
        <div class="d-flex align-items-center">
            <div onclick="window.location.href='{{ route('waiter.sales-summary') }}'" class="mr-3" title="Sales Summary">
                <i class="fa fa-line-chart fa-lg text-primary"></i>
            </div>
            <div onclick="window.location.href='{{ route('waiter.orders') }}'" title="Order History">
                <i class="fa fa-history fa-lg text-muted"></i>
            </div>
        </div>
    </div>
    <div class="search-pill">
        <i class="fa fa-search text-muted"></i>
        <input type="text" id="globalSearch" placeholder="Find food or drinks...">
    </div>
</div>

<!-- Category Navbar -->
<div class="cat-nav">
    <div class="cat-item active" onclick="setMasterTab('food', this)">🍳 Food</div>
    <div class="cat-item" onclick="setMasterTab('drinks', this)">🍷 Drinks</div>
    <div class="cat-item" onclick="setMasterTab('ceremony_list', this)">🎉 Events / Ceremony</div>
</div>

<!-- Drink Sub-cats (Visible only when drinks active) -->
<div id="drinkSubCats" class="cat-nav pt-0" style="display: none;">
    <div class="cat-item active" style="font-size: 0.75rem; padding: 6px 15px;" onclick="setDrinkSub('all', this)">All</div>
    @php
        $catLabels = [
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
            $label = $catLabels[$catKey] ?? ucfirst(str_replace(['_', '-'], ' ', $catKey)); 
        @endphp
        <div class="cat-item" style="font-size: 0.75rem; padding: 6px 15px;" onclick="setDrinkSub('{{ $catKey }}', this)">{{ $label }}</div>
    @endforeach
</div>

<!-- Menu Grid -->
<div class="menu-grid" id="menuGrid">
    <!-- Food Items -->
    @foreach($foodItems as $food)
    <div class="item-card food-item" data-name="{{ strtolower($food['name']) }}">
        <div class="item-img">
            @if(isset($food['image']))
                <img src="{{ asset('storage/' . $food['image']) }}" alt="{{ $food['name'] }}" onerror="this.src='https://ui-avatars.com/api/?name={{ urlencode($food['name']) }}&background=fff3e0&color=e77a31'">
            @else
                <i class="fa fa-cutlery fa-2x text-muted"></i>
            @endif
            @if(!empty($food['price_usd']) && $food['price_usd'] > 0)
                <span class="item-price-tag">${{ rtrim(rtrim(number_format($food['price_usd'], 2), '0'), '.') }}</span>
            @else
                <span class="item-price-tag">{{ number_format($food['price']) }}</span>
            @endif
        </div>
        <div class="item-info">
            <h6 class="item-name">{{ $food['name'] }}</h6>
            <button class="add-btn-small" onclick="fastAdd('food', {{ json_encode($food) }})">ADD TO BASKET</button>
        </div>
    </div>
    @endforeach

    <!-- Drink Items -->
    @foreach($drinks as $drink)
    @php
        $isOut = !($drink->in_stock);
        $stockClass = $isOut ? 'opacity-50' : '';
    @endphp
    <div class="item-card drink-item {{ $stockClass }}" style="display: none;" data-name="{{ strtolower($drink->name) }}" data-cat="{{ $drink->category }}">
        <div class="item-img">
            @if(isset($drink->image))
                <img src="{{ asset('storage/' . $drink->image) }}" alt="{{ $drink->name }}" onerror="this.src='https://ui-avatars.com/api/?name={{ urlencode($drink->name) }}&background=fff3e0&color=e77a31'">
            @else
                <div class="d-flex align-items-center justify-content-center w-100 h-100 bg-light">
                    <i class="fa fa-glass fa-2x text-muted"></i>
                </div>
            @endif
            
            @if($isOut)
                <div class="position-absolute bg-danger text-white px-2 py-1 rounded" style="top: 10px; left: 10px; font-size: 0.6rem; font-weight: 800; z-index: 5;">
                    OUT OF STOCK
                </div>
            @else
                <div class="position-absolute bg-success text-white px-2 py-1 rounded" style="top: 10px; left: 10px; font-size: 0.61rem; font-weight: 800; z-index: 5; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
                    @php
                        $ratio = (float)($drink->servings_per_pic ?? 1);
                        $stockVal = (float)$drink->current_stock;
                        $stockText = "";
                        if ($ratio > 1) {
                            $sFull = floor($stockVal + 0.001);
                            $sGls = round(($stockVal - $sFull) * $ratio);
                            $stockText = "{$sFull}B {$sGls}G";
                        } else {
                            $stockText = round($stockVal, 1) . " " . ($drink->unit ?? 'Pcs');
                        }
                    @endphp
                    IN STOCK: {{ $stockText }}
                </div>
            @endif
        </div>
        <div class="item-info">
            <h6 class="item-name mb-2">{{ $drink->name }}</h6>
            @foreach($drink->options as $opt)
            <button class="add-btn-small mb-1 d-flex justify-content-between align-items-center" 
                    {{ $isOut ? 'disabled' : '' }}
                    onclick="fastAdd('drink', {{ json_encode($drink) }}, '{{ $opt['type'] }}', {{ $opt['price'] }}, '{{ $opt['method'] }}')">
                <span>{{ strtoupper($opt['type']) }}</span>
                <span>
                    @if(!empty($opt['price_usd']) && $opt['price_usd'] > 0)
                        ${{ rtrim(rtrim(number_format($opt['price_usd'], 2), '0'), '.') }}
                    @else
                        {{ number_format($opt['price']) }}
                    @endif
                </span>
            </button>
            @endforeach
        </div>
    </div>
    @endforeach
</div>

<!-- Ceremony Grid (Active Today) -->
<div id="ceremonyGrid" class="menu-grid" style="display: none;">
    @forelse($activeCeremonies as $ceremony)
    <div class="item-card bg-white p-3" style="min-height: 180px;">
        <div class="d-flex justify-content-between align-items-<ctrl94>">
            <div class="badge badge-primary px-2 py-1 mb-2" style="font-size: 0.65rem;">ACTIVE TODAY</div>
            <span class="text-muted small">#{{ $ceremony['reference'] }}</span>
        </div>
        <h6 class="font-weight-bold mb-1" style="font-size: 0.95rem;">{{ $ceremony['guest_name'] }}</h6>
        
        <div class="mt-2 mb-3">
            <span class="text-muted small font-weight-bold d-block mb-1 uppercase" style="letter-spacing: 0.5px; font-size: 0.65rem;">INCLUDED PACKAGE:</span>
            <div class="d-flex flex-wrap gap-1" style="gap: 4px;">
                @php 
                    $items = is_array($ceremony['package_items']) ? $ceremony['package_items'] : (is_string($ceremony['package_items']) ? json_decode($ceremony['package_items'], true) : []); 
                    $labels = ['food' => 'Food', 'drinks' => 'Drinks', 'swimming' => 'Swimming', 'photos' => 'Photos', 'decoration' => 'Decoration'];
                @endphp
                @forelse($items as $key => $val)
                    @php 
                        $displayLabel = isset($labels[$key]) ? $labels[$key] : (is_numeric($key) ? $val : $key); 
                        // If $val is a descriptor array, use that
                        if (is_array($val) && isset($val['name'])) $displayLabel = $val['name'];
                    @endphp
                    <span class="badge badge-light border text-primary" style="font-size: 0.65rem; padding: 3px 8px;">{{ $displayLabel }}</span>
                @empty
                    <span class="text-muted italic small">No fixed items</span>
                @endforelse
            </div>
        </div>
        
        <div class="mt-auto">
            <div class="d-flex" style="gap: 8px;">
                <button class="btn btn-outline-primary btn-sm font-weight-bold py-2" 
                        onclick="viewCeremonyUsage({{ $ceremony['id'] }}, '{{ addslashes($ceremony['guest_name']) }}')"
                        style="border-radius: 12px; font-size: 0.7rem; flex: 1;">
                    <i class="fa fa-eye"></i> USAGE
                </button>
                <button class="btn btn-primary btn-sm font-weight-bold py-2" 
                        onclick="selectCeremonyForUsage({{ $ceremony['id'] }}, '{{ addslashes($ceremony['guest_name']) }}')"
                        style="border-radius: 12px; font-size: 0.7rem; flex: 2;">
                    <i class="fa fa-plus-circle"></i> ADD USAGE
                </button>
            </div>
        </div>
    </div>
    @empty
    <div class="col-12 text-center py-5">
        <i class="fa fa-calendar-o fa-3x text-muted mb-3"></i>
        <h6 class="text-muted">No ceremonies registered for today.</h6>
    </div>
    @endforelse
</div>

<!-- Floating Bottom Bar -->
<div class="bottom-bar">
    <div class="d-flex flex-column">
        <span class="text-muted small font-weight-bold">TOTAL</span>
        <span class="font-weight-bold h5 mb-0" id="floatTotal">0 TSH</span>
    </div>
    <div class="view-cart-btn" onclick="toggleCart()">
        <i class="fa fa-shopping-basket"></i>
        <span>BASKET</span>
        <span class="badge badge-light" id="cartCount">0</span>
    </div>
</div>

<!-- Cart Drawer -->
<div class="overlay-backdrop" id="backdrop" onclick="toggleCart()"></div>
<div class="cart-drawer" id="cartDrawer">
    <div class="drawer-handle"></div>
    <div class="drawer-header">
        <h5 class="font-weight-bold">Current Order</h5>
        <div class="d-flex gap-10 mt-3" style="gap: 10px; overflow-x: auto; padding-bottom: 5px;">
            <div class="type-pill active" id="pillResident" onclick="setGuest('resident')">RESIDENT</div>
            <div class="type-pill" id="pillCeremony" onclick="setGuest('ceremony')">CEREMONY</div>
            <div class="type-pill" id="pillWalkIn" onclick="setGuest('walk_in')">WALK-IN</div>
        </div>
    </div>
    
    <div class="cart-body">
        <!-- Identity Selector -->
        <div id="boxResident" class="mb-4">
            <label class="small font-weight-bold text-muted uppercase">Select Room Number</label>
            <select id="booking_id" class="form-control select2-mobile" style="width: 100%;">
                <option value="">-- Select Room --</option>
                @forelse($activeBookings as $booking)
                    <option value="{{ $booking['id'] }}">{{ $booking['room_type'] }}-{{ $booking['room_number'] }} ({{ $booking['guest_name'] }})</option>
                @empty
                    <option value="" disabled>No active rooms</option>
                @endforelse
            </select>
        </div>
        <div id="boxCeremony" class="mb-4" style="display: none;">
            <label class="small font-weight-bold text-muted uppercase">Select Active Ceremony</label>
            <select id="day_service_id" class="form-control select2-mobile" style="width: 100%;">
                <option value="">-- Select Ceremony --</option>
                @forelse($activeCeremonies as $ceremony)
                    <option value="{{ $ceremony['id'] }}" data-package="{{ json_encode($ceremony['package_items']) }}">
                        {{ $ceremony['reference'] }} - {{ $ceremony['guest_name'] }}
                    </option>
                @empty
                    <option value="" disabled>No active ceremonies found</option>
                @endforelse
            </select>
            <div id="packageHint" class="mt-2 p-2 bg-light border rounded" style="display: none; font-size: 0.8rem;">
                <strong class="text-primary small">Included in Package:</strong>
                <div id="packageList" class="d-flex flex-wrap gap-2 mt-1" style="gap: 5px;"></div>
            </div>
        </div>
        <div id="boxWalkIn" class="mb-4" style="display: none;">
            <label class="small font-weight-bold text-muted uppercase">Guest Info</label>
            <input type="text" id="walk_in_name" class="form-control" placeholder="Table # / Guest Name">
        </div>

        <div id="cartList" class="mt-2">
            <!-- Items injected here -->
        </div>
    </div>

    <div class="drawer-footer">
        <!-- Payment Section Removed: Payment is handled after order -->
        <input type="hidden" id="pay_ref" value="">

        <div class="d-flex justify-content-between align-items-center mb-3 px-1">
            <span class="font-weight-bold text-muted">GRAND TOTAL</span>
            <span class="h4 font-weight-bold mb-0 text-primary" id="drawerTotal">0 TSH</span>
        </div>
        
        <button class="btn btn-primary btn-block py-3 font-weight-bold" id="btnPlaceOrder" onclick="submitOrder()" disabled style="border-radius: 15px; background: var(--primary);">
            SEND TO PREPARATION
        </button>
    </div>
</div>

<!-- Ceremony Usage Modal -->
<div class="modal fade" id="ceremonyUsageModal" tabindex="-1" role="dialog" aria-hidden="true" style="z-index: 2000;">
    <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
        <div class="modal-content" style="border-radius: 20px;">
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title font-weight-bold" id="cUsageTitle">Ceremony Usage</h5>
                <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
            </div>
            <div class="modal-body">
                <div id="usageLoading" class="text-center py-4">
                    <i class="fa fa-spinner fa-spin fa-2x text-primary"></i>
                    <p class="text-muted small mt-2">Loading usage records...</p>
                </div>
                <div id="usageContent" style="display: none;">
                    <div class="table-responsive">
                        <table class="table table-sm" style="font-size: 0.85rem;">
                            <thead>
                                <tr class="text-muted uppercase" style="font-size: 0.65rem;">
                                    <th>Item</th>
                                    <th class="text-center">Waiter</th>
                                    <th class="text-center">Qty</th>
                                    <th class="text-right">Price</th>
                                    <th class="text-right">Total</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody id="usageTableBody"></tbody>
                        </table>
                    </div>
                    <div class="d-flex justify-content-between mt-3 p-3 bg-light rounded" style="border-radius: 15px;">
                        <span class="font-weight-bold">Total Consumption</span>
                        <span class="font-weight-bold text-primary" id="usageGrandTotal">0 TSH</span>
                    </div>
                </div>
            </div>
            <div class="modal-footer border-0 pt-0">
                <button type="button" class="btn btn-secondary btn-block py-2" data-dismiss="modal" style="border-radius: 12px;">Close</button>
            </div>
        </div>
    </div>
</div>

@endsection

@section('scripts')
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
    // Cache Buster: {{ now()->timestamp }} - Fixed drink order submission v2.1
    let posItems = [];
    let curMaster = 'food';
    let curGuest = 'resident';
    let curPayIntent = 'later';
    let curPayMethod = null;
    const activeBookings = {!! json_encode($activeBookings) !!};
    const activeCeremonies = {!! json_encode($activeCeremonies) !!};

    $(document).ready(function() {
        $('.select2-mobile').select2({ placeholder: "Room Search..." });

        // Room Selection Change - Removed summary box update logic
        $('#booking_id').on('change', function() {
            // Summary logic removed as requested
        });

        // Ceremony Selection Change
        $('#day_service_id').on('change', function() {
            const selected = $(this).find('option:selected');
            const packageItems = selected.data('package');
            
            if (packageItems && Object.keys(packageItems).length > 0) {
                const list = $('#packageList');
                list.empty();
                
                // packageItems can be array or object
                const items = Array.isArray(packageItems) ? packageItems : Object.values(packageItems);
                
                items.forEach(item => {
                    list.append(`<span class="badge badge-pill badge-outline-primary" style="border: 1px solid var(--primary); color: var(--primary); padding: 5px 10px;">${item}</span>`);
                });
                $('#packageHint').fadeIn();
            } else {
                $('#packageHint').hide();
            }
            updateServingIndicator();
        });

        // Search Logic
        $('#globalSearch').on('input', function() {
            const term = $(this).val().toLowerCase();
            const selector = curMaster === 'food' ? '.food-item' : '.drink-item';
            
            $(selector).each(function() {
                $(this).toggle($(this).data('name').includes(term));
            });
        });

        // Handle pre-fill from query params (Add Items flow)
        const urlParams = new URLSearchParams(window.location.search);
        const roomId = urlParams.get('room_id');
        const walkIn = urlParams.get('walk_in');

        if (roomId || walkIn) {
            if (roomId) {
                setGuest('resident');
                $('#booking_id').val(roomId).trigger('change');
                
                // Visual feedback without opening cart
                const roomText = $(`#booking_id option[value="${roomId}"]`).text() || 'Selected Room';
                Swal.fire({
                    toast: true, position: 'top', timer: 3000, showConfirmButton: false,
                    icon: 'info', title: `Adding to ${roomText}`, background: '#2d3436', color: '#fff'
                });
            } else if (walkIn) {
                setGuest('walk_in');
                $('#walk_in_name').val(walkIn);
                
                Swal.fire({
                    toast: true, position: 'top', timer: 3000, showConfirmButton: false,
                    icon: 'info', title: `Adding to Guest: ${walkIn}`, background: '#2d3436', color: '#fff'
                });
            }
        }

        // Remove payment flow change handler as it's gone
    });

    function setMasterTab(tab, el) {
        curMaster = tab;
        $('.cat-item').not($(el).siblings()).removeClass('active');
        $(el).addClass('active');
        
        // Hide all grids first
        $('#menuGrid').hide();
        $('#ceremonyGrid').hide();
        $('.food-item').hide();
        $('.drink-item').hide();
        $('#drinkSubCats').hide();

        if (tab === 'food') {
            $('#menuGrid').show();
            $('.food-item').show();
        } else if (tab === 'drinks') {
            $('#menuGrid').show();
            $('.drink-item').show();
            $('#drinkSubCats').show();
        } else if (tab === 'ceremony_list') {
            $('#ceremonyGrid').show();
        }
    }

    function selectCeremonyForUsage(id, name) {
        setGuest('ceremony');
        $('#day_service_id').val(id).trigger('change');
        
        // Switch to Kitchen tab so they can add items
        setMasterTab('food', $('.cat-item').first());
        
        Swal.fire({
            toast: true, position: 'top', timer: 3000, showConfirmButton: false,
            icon: 'info', title: `Usage for: ${name}`, background: '#2d3436', color: '#fff'
        });
    }

    function setDrinkSub(sub, el) {
        $(el).siblings().removeClass('active');
        $(el).addClass('active');
        
        if (sub === 'all') {
            $('.drink-item').show();
        } else {
            $('.drink-item').hide();
            
            // Strict exact match to avoid "non_alcoholic" matching "alcoholic"
            $(`.drink-item`).each(function() {
                if ($(this).data('cat') === sub) {
                    $(this).show();
                }
            });
        }
    }

    function setGuest(type) {
        curGuest = type;
        $('.type-pill').removeClass('active');
        
        if (type === 'resident') {
            $('#pillResident').addClass('active');
            $('#boxResident').show();
            $('#boxCeremony').hide();
            $('#boxWalkIn').hide();
            $('#btnPlaceOrder').text('SEND TO PREPARATION');
        } else if (type === 'ceremony') {
            $('#pillCeremony').addClass('active');
            $('#boxResident').hide();
            $('#boxCeremony').show();
            $('#boxWalkIn').hide();
            
            // Force Pay Later
            curPayIntent = 'later';
            curPayMethod = null;
            $('#btnPlaceOrder').text('SEND TO PREPARATION');
        } else {
            $('#pillWalkIn').addClass('active');
            $('#boxResident').hide();
            $('#boxCeremony').hide();
            $('#boxWalkIn').show();
            
             // Payment logic simplified: always session-based
            curPayIntent = 'later';
            curPayMethod = null;
            $('#btnPlaceOrder').text('SEND TO PREPARATION');
        }
        updateServingIndicator();
    }

    function selectPlatform(method, label, el) {
        // Redundant but kept as empty for compatibility if needed
    }

    function setPaymentIntent(intent, method = null) {
        curPayIntent = intent;
        curPayMethod = method;
    }

    function updateServingIndicator() {
        let name = '';
        let info = ''; // For responsibility
        if (curGuest === 'resident') {
            const val = $('#booking_id').val();
            const selected = activeBookings.find(b => b.id == val);
            if (selected) {
                name = selected.room_type + '-' + selected.room_number;
                info = selected.payment_responsibility === 'company' || selected.is_corporate 
                    ? '<span class="text-primary ml-1"> (COMPANY SPONSORED)</span>' 
                    : '<span class="text-success ml-1"> (SELF-PAYER)</span>';
            }
        } else if (curGuest === 'ceremony') {
            const selected = $('#day_service_id option:selected');
            if (selected.val()) {
                name = selected.text().split('-')[1].trim();
                info = '<span class="text-muted ml-1"> (CEREMONY)</span>';
            }
        } else {
            name = $('#walk_in_name').val().trim() || 'New Walk-in';
            info = '<span class="text-success ml-1"> (SELF-PAYER)</span>';
        }

        if (name) {
            $('#servingName').html(name + info);
            $('#servingIndicator').fadeIn();
        } else {
            $('#servingIndicator').hide();
        }
    }

    $(document).on('change input', '#booking_id, #walk_in_name', updateServingIndicator);

    function toggleCart() {
        $('#cartDrawer').toggleClass('open');
        $('#backdrop').toggle();
        
        if ($('#cartDrawer').hasClass('open')) {
            // Re-initialize select2 with proper parent when drawer opens
            $('.select2-mobile').select2({
                placeholder: "Type Room or Name...",
                dropdownParent: $('#cartDrawer'),
                allowClear: true
            });
        }
    }

    function fastAdd(type, data, optName = '', optPrice = 0, method = '') {
        console.log('fastAdd called:', {type, data, optName, optPrice, method});
        
        const cartId = type === 'food' ? `f_${data.id}` : `d_${data.variant_id}_${method}`;
        const existing = posItems.find(i => i.cartId === cartId);
        
        // Stock Validation for Bar Items
        if (type === 'drink') {
            const currentQty = existing ? existing.qty : 0;
            const requestedQty = currentQty + 1;
            
            // Calculate requested pics
            const requestedPics = (method === 'glass') 
                ? (requestedQty / (data.servings_per_pic || 1)) 
                : requestedQty;

            if (requestedPics > (data.current_stock + 0.001)) {
                return Swal.fire({
                    icon: 'error',
                    title: 'Out of Stock',
                    text: `Only ${data.current_stock} ${data.unit || 'Pcs'} available. You cannot add more.`,
                    confirmButtonColor: '#e77a31'
                });
            }
        }

        if (existing) {
            existing.qty++;
        } else {
            const newItem = {
                cartId,
                id: data.id,
                name: type === 'food' ? data.name : `${data.name} (${optName})`,
                price: type === 'food' ? data.price : optPrice,
                qty: 1,
                isFood: type === 'food',
                variantId: type === 'food' ? null : data.variant_id,
                productId: type === 'food' ? null : data.id,
                method: type === 'food' ? null : method,
                // Store metadata for re-validation in changeQty
                servings_per_pic: data.servings_per_pic || 1,
                current_stock: data.current_stock || 0,
                unit: data.unit || 'Pcs',
                note: ''
            };
            console.log('Adding new item to cart:', newItem);
            posItems.push(newItem);
        }
        renderCart();
        
        // Visual Feedback
        Swal.fire({
            toast: true, position: 'top', timer: 1000, showConfirmButton: false,
            icon: 'success', title: 'Added: ' + (data.variant_name || data.name), background: '#2d3436', color: '#fff'
        });
    }

    function renderCart() {
        const list = $('#cartList');
        list.empty();
        
        let total = 0;
        let count = 0;

        posItems.forEach((item, idx) => {
            total += (item.price * item.qty);
            count += item.qty;
            list.append(`
                <div class="mb-3 p-3 bg-light rounded" style="border-radius: 15px;">
                    <div class="d-flex justify-content-between align-items-center">
                        <div style="flex: 1">
                            <span class="d-block font-weight-bold" style="font-size: 0.9rem;">${item.name}</span>
                            <span class="text-primary font-weight-bold">${item.price.toLocaleString()} TSH</span>
                        </div>
                        <div class="qty-box">
                            <i class="fa fa-minus-circle fa-lg text-muted" onclick="changeQty(${idx}, -1)"></i>
                            <span class="font-weight-bold">${item.qty}</span>
                            <i class="fa fa-plus-circle fa-lg text-primary" onclick="changeQty(${idx}, 1)"></i>
                        </div>
                    </div>
                    <input type="text" class="form-control form-control-sm mt-2" placeholder="Item note..." 
                        value="${item.note}" onchange="posItems[${idx}].note = this.value"
                        style="border-radius: 10px; border: 1px dashed #ccc; font-size: 0.8rem;">
                </div>
            `);
        });

        $('#floatTotal, #drawerTotal').text(total.toLocaleString() + ' TSH');
        $('#cartCount').text(count);
        $('#btnPlaceOrder').prop('disabled', posItems.length === 0);
    }

    function changeQty(idx, delta) {
        const item = posItems[idx];
        
        // Validate stock if increasing
        if (delta > 0 && !item.isFood && item.variantId) {
            const requestedQty = item.qty + delta;
            const requestedPics = (item.method === 'glass') 
                ? (requestedQty / (item.servings_per_pic || 1)) 
                : requestedQty;

            if (requestedPics > (item.current_stock + 0.001)) {
                 return Swal.fire({
                    icon: 'error',
                    title: 'Limit Reached',
                    text: `Insufficient inventory for ${item.name}.`,
                    confirmButtonColor: '#e77a31'
                });
            }
        }

        item.qty += delta;
        if (item.qty <= 0) posItems.splice(idx, 1);
        renderCart();
    }

    function submitOrder() {
        const bookingId = $('#booking_id').val();
        const ceremonyId = $('#day_service_id').val();
        const walkIn = $('#walk_in_name').val();
        
        if (curGuest === 'resident' && !bookingId) return Swal.fire("Required", "Please select a room guest.", "warning");
        if (curGuest === 'ceremony' && !ceremonyId) return Swal.fire("Required", "Please select a ceremony event.", "warning");

        // Payment validation removed as it is now always session-based (pay later)

        const flow = $('#payment_flow').val();
        
        Swal.fire({
            title: "Confirm Order",
            text: "Order will be sent for preparation and added to guest usage.",
            icon: "question",
            showCancelButton: true,
            confirmButtonColor: '#e77a31',
            confirmButtonText: "YES, SEND TO PREPARATION"
        }).then((res) => {
            if (res.isConfirmed) {
                Swal.fire({ title: "Sending...", allowOutsideClick: false, didOpen: () => Swal.showLoading() });
                
                const now = new Date();
                const timeStr = now.getHours().toString().padStart(2, '0') + ':' + now.getMinutes().toString().padStart(2, '0');
                const finalWalkInName = walkIn || 'Walk-in (' + timeStr + ')';

                const payload = {
                    _token: "{{ csrf_token() }}",
                    order_type: curGuest,
                    booking_id: bookingId,
                    day_service_id: ceremonyId,
                    walk_in_name: finalWalkInName,
                    items: posItems,
                    payment_intent: curPayIntent,
                    payment_method: curPayMethod,
                    payment_reference: $('#pay_ref').val()
                };
                
                console.log('Submitting order with payload:', payload);
                console.log('Items being sent:', JSON.stringify(posItems, null, 2));

                $.ajax({
                    url: "{{ route('waiter.order.store') }}",
                    method: "POST",
                    data: payload,
                    success: function(r) {
                        if (r.success) {
                            Swal.fire("Sent!", r.message, "success").then(() => {
                                window.location.href = "{{ route('waiter.orders') }}";
                            });
                        }
                    },
                    error: function(e) {
                        Swal.fire("Error", "Check your inputs or network.", "error");
                    }
                });
            }
        });
    }

    function printGuestBill() {
        const bid = $('#booking_id').val();
        if (!bid) return;
        const url = `/customer/bookings/${bid}/checkout-bill`;
        window.open(url, 'BillPrint', 'width=800,height=600');
    }

    function viewCeremonyUsage(id, name) {
        $('#cUsageTitle').text(`Usage: ${name}`);
        $('#usageLoading').show();
        $('#usageContent').hide();
        $('#ceremonyUsageModal').modal('show');
        
        $.ajax({
            url: `/waiter/day-services/${id}`,
            method: 'GET',
            success: function(r) {
                if (r.success && r.day_service) {
                    const tbody = $('#usageTableBody');
                    tbody.empty();
                    let total = 0;
                    
                    // 1. Add Package Items from Registration
                    const pItems = r.day_service.package_items ? (typeof r.day_service.package_items === 'string' ? JSON.parse(r.day_service.package_items) : r.day_service.package_items) : {};
                    const pPaid = r.day_service.package_items_paid ? (typeof r.day_service.package_items_paid === 'string' ? JSON.parse(r.day_service.package_items_paid) : r.day_service.package_items_paid) : {};
                    const pLabels = {'food': 'Food', 'drinks': 'Drinks', 'swimming': 'Swimming', 'photos': 'Photos', 'decoration': 'Decoration'};
                    const requests = (r.day_service.service_requests || []).filter(req => (req.status || '').toLowerCase() !== 'cancelled');
                    
                    if (Object.keys(pItems).length > 0) {
                        tbody.append('<tr class="bg-light"><td colspan="6" class="font-weight-bold py-2"><i class="fa fa-gift mr-1"></i> Pre-selected during Registration</td></tr>');
                        Object.entries(pItems).forEach(([key, val]) => {
                            const label = pLabels[key] || key;
                            const price = parseFloat(val) || 0;
                            const isPaid = pPaid[key] == 1;
                            
                            // Check if used in consumption
                            const usedBy = requests.find(req => {
                                const itemName = (req.service_specific_data?.item_name || req.service?.name || '').toLowerCase();
                                return itemName.includes(label.toLowerCase()) || itemName.includes(key.toLowerCase());
                            });

                            total += price;
                            tbody.append(`
                                <tr class="text-muted">
                                    <td class="pl-3">${label}</td>
                                    <td class="text-center small">Reception</td>
                                    <td class="text-center">1</td>
                                    <td class="text-right">${price.toLocaleString()}</td>
                                    <td class="text-right">${price.toLocaleString()}</td>
                                    <td>
                                        <span class="badge ${isPaid ? 'badge-success' : 'badge-danger'} mb-1">
                                            ${isPaid ? 'PAID' : 'UNPAID'}
                                        </span>
                                        <br>
                                        <span class="badge ${usedBy ? 'badge-warning' : 'badge-light border'}">
                                            <i class="fa ${usedBy ? 'fa-check-circle' : 'fa-clock-o'} mr-1"></i>
                                            ${usedBy ? 'USED' : 'NOT USED'}
                                        </span>
                                    </td>
                                </tr>
                            `);
                        });
                    }

                    // 2. Add Service Requests (Waiter Orders)
                    if (requests.length > 0) {
                        tbody.append('<tr class="bg-light"><td colspan="6" class="font-weight-bold py-2"><i class="fa fa-cutlery mr-1"></i> Additional Consumption</td></tr>');
                        requests.forEach(req => {
                            const itemName = req.service_specific_data?.item_name || req.service?.name || 'Item';
                            const waiterName = req.approved_by?.name || 'Reception';
                            const price = parseFloat(req.unit_price_tsh);
                            const subtotal = parseFloat(req.total_price_tsh);
                            total += subtotal;
                            
                            tbody.append(`
                                <tr>
                                    <td class="font-weight-bold pl-3">${itemName}</td>
                                    <td class="text-center small">${waiterName}</td>
                                    <td class="text-center">×${req.quantity}</td>
                                    <td class="text-right">${price.toLocaleString()}</td>
                                    <td class="text-right font-weight-bold">${subtotal.toLocaleString()}</td>
                                    <td><span class="badge ${req.status === 'completed' ? 'badge-success' : 'badge-light border'}">${req.status}</span></td>
                                </tr>
                            `);
                        });
                    }

                    // Post-processing section removed as we now handle it inside the loop
                     
                    if (Object.keys(pItems).length === 0 && requests.length === 0) {
                        tbody.append('<tr><td colspan="6" class="text-center py-4 text-muted">No usage records found yet.</td></tr>');
                    }
                    
                    $('#usageGrandTotal').text(total.toLocaleString() + ' TSH');
                    $('#usageTotalLabel').text('Total Consumption');
                    $('#usageLoading').hide();
                    $('#usageContent').fadeIn();
                }
            },
            error: function() {
                Swal.fire("Error", "Could not fetch usage records.", "error");
                $('#ceremonyUsageModal').modal('hide');
            }
        });
    }
</script>
@endsection
