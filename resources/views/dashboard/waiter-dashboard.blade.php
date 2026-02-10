@extends('dashboard.layouts.app')

@section('content')
<style>
    .pos-container {
        display: grid;
        grid-template-columns: 1fr 350px;
        gap: 20px;
        height: calc(100vh - 150px);
    }
    .menu-section {
        overflow-y: auto;
        padding-right: 10px;
    }
    .cart-section {
        background: #fff;
        border-radius: 12px;
        box-shadow: 0 4px 15px rgba(0,0,0,0.05);
        display: flex;
        flex-direction: column;
        height: 100%;
        border: 1px solid #eee;
    }
    .category-btn {
        margin-right: 10px;
        margin-bottom: 10px;
        border-radius: 25px;
        padding: 8px 20px;
        white-space: nowrap;
    }
    .menu-item-card {
        cursor: pointer;
        transition: transform 0.2s, box-shadow 0.2s;
        border-radius: 12px;
        border: 1px solid #eee;
        background: #fff;
    }
    .menu-item-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 8px 20px rgba(0,0,0,0.1);
        border-color: #009688;
    }
    .cart-items {
        flex-grow: 1;
        overflow-y: auto;
        padding: 15px;
    }
    .cart-item {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 10px 0;
        border-bottom: 1px solid #f8f9fa;
    }
    .cart-total {
        padding: 20px;
        background: #f8f9fa;
        border-top: 1px solid #eee;
        border-bottom-left-radius: 12px;
        border-bottom-right-radius: 12px;
    }
    .active-category {
        background: #009688 !important;
        color: white !important;
        border-color: #009688 !important;
    }
    .guest-type-btn {
        flex: 1;
        border-radius: 8px;
    }
    #roomSelector {
        display: none;
    }
    .search-box {
        position: sticky;
        top: 0;
        z-index: 10;
        background: #f4f7f6;
        padding-bottom: 15px;
    }
    @media (max-width: 992px) {
        .pos-container {
            grid-template-columns: 1fr;
            height: auto;
        }
        .cart-section {
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            height: 60vh;
            z-index: 1050;
            display: none;
        }
        .cart-visible {
            display: flex !important;
        }
    }
</style>

<div class="app-title">
    <div>
        <h1><i class="fa fa-cutlery"></i> Waiter Service (POS)</h1>
        <p>Order food and drinks for Guests</p>
    </div>
    <ul class="app-breadcrumb breadcrumb">
        <li class="breadcrumb-item"><i class="fa fa-home fa-lg"></i></li>
        <li class="breadcrumb-item"><a href="#">Waiter Dashboard</a></li>
    </ul>
</div>

<div class="pos-container">
    <!-- Menu Section -->
    <div class="menu-section">
        <div class="search-box">
            <div class="input-group">
                <div class="input-group-prepend">
                    <span class="input-group-text"><i class="fa fa-search"></i></span>
                </div>
                <input type="text" id="menuSearch" class="form-control" placeholder="Search menu items...">
            </div>
            
            <div class="mt-3 overflow-auto d-flex pb-1" id="categoryTabs">
                <button class="btn btn-outline-secondary category-btn active-category" data-category="all">All Items</button>
                <button class="btn btn-outline-secondary category-btn" data-category="food">Kitchen / Food</button>
                <button class="btn btn-outline-secondary category-btn" data-category="drinks">Bar / Drinks</button>
            </div>
        </div>

        <div class="row" id="menuGrid">
            @foreach($menuItems as $item)
            <div class="col-md-4 col-lg-3 col-6 mb-4 menu-item-row" data-name="{{ strtolower($item->name) }}" data-category="{{ in_array($item->category, ['food', 'restaurant']) ? 'food' : 'drinks' }}">
                <div class="card menu-item-card h-100" onclick="addToCart({{ $item->id }}, '{{ addslashes($item->name) }}', {{ $item->price_tsh }})">
                    <div class="card-body text-center p-3">
                        <div class="mb-2">
                            <i class="fa {{ in_array($item->category, ['food', 'restaurant', 'Generic Food Order']) ? 'fa-cutlery text-orange' : 'fa-glass text-info' }} fa-2x"></i>
                        </div>
                        <h6 class="mb-1 font-weight-bold" style="font-size: 0.9rem;">{{ $item->name }}</h6>
                        <p class="text-success mb-0 font-weight-bold">TZS {{ number_format($item->price_tsh) }}</p>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>

    <!-- Right Column: Cart & Guest Setup -->
    <div class="cart-section" id="cartSection">
        <div class="p-3 border-bottom bg-light d-flex justify-content-between align-items-center">
            <h5 class="mb-0"><i class="fa fa-shopping-cart"></i> Current Order</h5>
            <button class="btn btn-sm btn-outline-danger d-lg-none" onclick="toggleCart()">Close</button>
        </div>

        <!-- Guest Type Selector -->
        <div class="p-3 bg-white border-bottom">
            <div class="btn-group w-100 mb-3" role="group">
                <button type="button" class="btn btn-outline-primary active guest-type-btn" id="btnResident" onclick="setGuestType('resident')">
                    <i class="fa fa-bed"></i> Resident
                </button>
                <button type="button" class="btn btn-outline-primary guest-type-btn" id="btnWalkIn" onclick="setGuestType('walk_in')">
                    <i class="fa fa-user"></i> Walk-in
                </button>
            </div>

            <!-- Room Selector (for Residents) -->
            <div id="roomSelector">
                <label class="small font-weight-bold text-muted">SELECT ROOM <span class="text-danger">*</span></label>
                <select class="form-control select2" id="booking_id" style="width: 100%;">
                    <option value="">-- Select Guest / Room --</option>
                    @foreach($activeBookings as $booking)
                        <option value="{{ $booking->id }}">
                            Room {{ $booking->room->room_number ?? 'N/A' }} - {{ $booking->guest_name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- Name Input (for Walk-ins) -->
            <div id="walkInInput" style="display: none;">
                <label class="small font-weight-bold text-muted">GUEST NAME/REF <span class="text-danger">*</span></label>
                <input type="text" id="walk_in_name" class="form-control" placeholder="e.g. Table 5 / Mr. Ally">
            </div>
        </div>

        <!-- Cart Items List -->
        <div class="cart-items" id="cartItemsList">
            <div class="text-center text-muted mt-5">
                <i class="fa fa-shopping-basket fa-3x mb-3 opacity-25"></i>
                <p>No items in cart</p>
            </div>
        </div>

        <!-- Total and Submission -->
        <div class="cart-total">
            <div class="d-flex justify-content-between mb-2">
                <span class="text-muted">Subtotal</span>
                <span id="subtotalText">TZS 0</span>
            </div>
            <div class="d-flex justify-content-between mb-3">
                <h5 class="font-weight-bold">Total</h5>
                <h5 class="font-weight-bold text-primary" id="totalText">TZS 0</h5>
            </div>

            <!-- Payment Setup -->
            <div class="form-group mb-3">
                <label class="small font-weight-bold text-muted">PAYMENT STATUS</label>
                <select class="form-control" id="payment_status" onchange="togglePaymentMethod()">
                    <option value="pending">Bill to Room / Pay Later</option>
                    <option value="paid">Paid Now</option>
                </select>
            </div>

            <div id="paymentMethodSection" style="display: none;">
                <div class="form-group mb-3">
                    <label class="small font-weight-bold text-muted">PAYMENT METHOD</label>
                    <select class="form-control" id="payment_method">
                        <option value="Cash">Cash</option>
                        <option value="M-Pesa">M-Pesa</option>
                        <option value="KCB">KCB</option>
                        <option value="POS/Card">POS / Card</option>
                        <option value="Airtel Money">Airtel Money</option>
                        <option value="Tigo Pesa">Tigo Pesa</option>
                    </select>
                </div>
                <div class="form-group mb-3">
                    <label class="small font-weight-bold text-muted">REFERENCE (Optional)</label>
                    <input type="text" id="payment_reference" class="form-control" placeholder="Trans ID / Slip #">
                </div>
            </div>

            <button class="btn btn-primary btn-block btn-lg" id="btnSubmitOrder" onclick="submitOrder()" disabled>
                <i class="fa fa-send"></i> SUBMIT TO KITCHEN/BAR
            </button>
        </div>
    </div>
</div>

<!-- Mobile Float Button -->
<button class="btn btn-primary d-lg-none" style="position: fixed; bottom: 20px; right: 20px; border-radius: 50%; width: 60px; height: 60px; box-shadow: 0 4px 15px rgba(0,0,0,0.3); z-index: 1000;" onclick="toggleCart()">
    <i class="fa fa-shopping-cart fa-lg"></i>
    <span class="badge badge-danger" id="mobileCartCount" style="position: absolute; top: 5px; right: 5px; display: none;">0</span>
</button>

@endsection

@section('scripts')
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script src="{{ asset('dashboard_assets/js/plugins/sweetalert.min.js') }}"></script>

<script>
    let cart = [];
    let guestType = 'resident';

    $(document).ready(function() {
        $('.select2').select2();
        setGuestType('resident');

        // Search functionality
        $('#menuSearch').on('input', function() {
            filterMenu();
        });

        // Category filter
        $('.category-btn').on('click', function() {
            $('.category-btn').removeClass('active-category');
            $(this).addClass('active-category');
            filterMenu();
        });
    });

    function setGuestType(type) {
        guestType = type;
        if (type === 'resident') {
            $('#btnResident').addClass('active btn-primary').removeClass('btn-outline-primary');
            $('#btnWalkIn').removeClass('active btn-primary').addClass('btn-outline-primary');
            $('#roomSelector').show();
            $('#walkInInput').hide();
            // Resident defaults to Pending (Bill to Room)
            $('#payment_status').val('pending').trigger('change');
        } else {
            $('#btnWalkIn').addClass('active btn-primary').removeClass('btn-outline-primary');
            $('#btnResident').removeClass('active btn-primary').addClass('btn-outline-primary');
            $('#roomSelector').hide();
            $('#walkInInput').show();
            // Walk-in defaults to Paid
            $('#payment_status').val('paid').trigger('change');
        }
    }

    function togglePaymentMethod() {
        const status = $('#payment_status').val();
        if (status === 'paid') {
            $('#paymentMethodSection').slideDown();
        } else {
            $('#paymentMethodSection').slideUp();
        }
    }

    function filterMenu() {
        const query = $('#menuSearch').val().toLowerCase();
        const category = $('.active-category').data('category');

        $('.menu-item-row').each(function() {
            const name = $(this).data('name');
            const itemCategory = $(this).data('category');
            
            const matchesSearch = name.includes(query);
            const matchesCategory = (category === 'all') || (category === itemCategory);

            if (matchesSearch && matchesCategory) {
                $(this).show();
            } else {
                $(this).hide();
            }
        });
    }

    function addToCart(id, name, price) {
        const existingItem = cart.find(i => i.id === id);
        if (existingItem) {
            existingItem.quantity += 1;
        } else {
            cart.push({ id, name, price, quantity: 1 });
        }
        updateCartUI();
        
        // Mobile feedback
        $('#mobileCartCount').text(cart.length).show();
    }

    function removeFromCart(index) {
        cart.splice(index, 1);
        updateCartUI();
        if (cart.length === 0) $('#mobileCartCount').hide();
    }

    function updateQuantity(index, delta) {
        cart[index].quantity += delta;
        if (cart[index].quantity <= 0) {
            removeFromCart(index);
        } else {
            updateCartUI();
        }
    }

    function updateCartUI() {
        const list = $('#cartItemsList');
        list.empty();

        if (cart.length === 0) {
            list.html(`<div class="text-center text-muted mt-5">
                <i class="fa fa-shopping-basket fa-3x mb-3 opacity-25"></i>
                <p>No items in cart</p>
            </div>`);
            $('#btnSubmitOrder').prop('disabled', true);
            $('#subtotalText').text('TZS 0');
            $('#totalText').text('TZS 0');
            return;
        }

        let total = 0;
        cart.forEach((item, index) => {
            const itemTotal = item.price * item.quantity;
            total += itemTotal;
            list.append(`
                <div class="cart-item">
                    <div style="flex: 1;">
                        <span class="d-block font-weight-bold">${item.name}</span>
                        <small class="text-muted">TZS ${item.price.toLocaleString()} x ${item.quantity}</small>
                    </div>
                    <div class="d-flex align-items-center">
                        <button class="btn btn-sm btn-light border" onclick="updateQuantity(${index}, -1)">-</button>
                        <span class="mx-2 font-weight-bold">${item.quantity}</span>
                        <button class="btn btn-sm btn-light border" onclick="updateQuantity(${index}, 1)">+</button>
                        <button class="btn btn-sm text-danger ml-2" onclick="removeFromCart(${index})"><i class="fa fa-trash"></i></button>
                    </div>
                </div>
            `);
        });

        $('#subtotalText').text('TZS ' + total.toLocaleString());
        $('#totalText').text('TZS ' + total.toLocaleString());
        $('#btnSubmitOrder').prop('disabled', false);
    }

    function toggleCart() {
        $('#cartSection').toggleClass('cart-visible');
    }

    function submitOrder() {
        if (cart.length === 0) return;

        const data = {
            _token: '{{ csrf_token() }}',
            order_type: guestType,
            items: cart,
            payment_status: $('#payment_status').val(),
            payment_method: $('#payment_method').val(),
            payment_reference: $('#payment_reference').val(),
        };

        if (guestType === 'resident') {
            data.booking_id = $('#booking_id').val();
            if (!data.booking_id) {
                swal("Wait!", "Please select a room/guest first.", "warning");
                return;
            }
        } else {
            data.walk_in_name = $('#walk_in_name').val();
            if (!data.walk_in_name) {
                swal("Wait!", "Please enter guest name or table reference.", "warning");
                return;
            }
        }

        $('#btnSubmitOrder').prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> Submitting...');

        $.ajax({
            url: "{{ route('waiter.order.store') }}",
            method: "POST",
            data: data,
            success: function(response) {
                if (response.success) {
                    swal({
                        title: "Excellent!",
                        text: response.message,
                        type: "success",
                        timer: 2000,
                        showConfirmButton: false
                    });
                    
                    // Reset
                    cart = [];
                    updateCartUI();
                    $('#walk_in_name').val('');
                    $('#payment_reference').val('');
                    $('#mobileCartCount').hide();
                }
            },
            error: function(xhr) {
                const msg = xhr.responseJSON?.message || 'Something went wrong';
                swal("Error!", msg, "error");
            },
            complete: function() {
                $('#btnSubmitOrder').prop('disabled', false).html('<i class="fa fa-send"></i> SUBMIT TO KITCHEN/BAR');
            }
        });
    }
</script>
@endsection
