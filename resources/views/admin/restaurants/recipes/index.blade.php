@extends('dashboard.layouts.app')

@section('content')
<div class="app-title">
    <div>
        <h1><i class="fa fa-utensils"></i> Customer Menu</h1>
        <p>Manage your restaurant menu items</p>
    </div>
    <ul class="app-breadcrumb breadcrumb">
        <li class="breadcrumb-item"><i class="fa fa-home fa-lg"></i></li>
        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
        <li class="breadcrumb-item active">Menu</li>
    </ul>
</div>

<!-- Stats Row -->
<div class="row mb-4">
    <!-- Total Items -->
    <div class="col-md-3 col-sm-6 mb-3">
        <div class="card border-0 shadow-sm rounded-lg overflow-hidden h-100" style="background: linear-gradient(45deg, #4e54c8 0%, #8f94fb 100%);">
            <div class="card-body p-3">
                <div class="d-flex align-items-center mb-2">
                    <div class="rounded-circle d-flex align-items-center justify-content-center mr-3" style="width: 45px; height: 45px; background: rgba(255,255,255,0.2);">
                        <i class="fa fa-utensils text-white fa-lg"></i>
                    </div>
                    <div>
                        <h6 class="text-white-50 text-uppercase mb-0 font-weight-bold" style="font-size: 0.7rem; letter-spacing: 1px;">Total Items</h6>
                        <h3 class="text-white mb-0 font-weight-bold">{{ $totalCount }}</h3>
                    </div>
                </div>
                <div class="mt-3">
                    <div class="progress" style="height: 4px; background: rgba(255,255,255,0.15); border-radius: 10px;">
                        <div class="progress-bar bg-white" role="progressbar" style="width: 100%; border-radius: 10px;" aria-valuenow="100" aria-valuemin="0" aria-valuemax="100"></div>
                    </div>
                    <small class="text-white-50 mt-1 d-block" style="font-size: 0.65rem;">Total catalog size</small>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Available -->
    <div class="col-md-3 col-sm-6 mb-3">
        <div class="card border-0 shadow-sm rounded-lg overflow-hidden h-100" style="background: linear-gradient(45deg, #11998e 0%, #38ef7d 100%);">
            <div class="card-body p-3">
                <div class="d-flex align-items-center mb-2">
                    <div class="rounded-circle d-flex align-items-center justify-content-center mr-3" style="width: 45px; height: 45px; background: rgba(255,255,255,0.2);">
                        <i class="fa fa-check-circle text-white fa-lg"></i>
                    </div>
                    <div>
                        <h6 class="text-white-50 text-uppercase mb-0 font-weight-bold" style="font-size: 0.7rem; letter-spacing: 1px;">Available</h6>
                        <h3 class="text-white mb-0 font-weight-bold">{{ $availableCount }}</h3>
                    </div>
                </div>
                @php
                    $availPercent = $totalCount > 0 ? ($availableCount / $totalCount) * 100 : 0;
                @endphp
                <div class="mt-3">
                    <div class="progress" style="height: 4px; background: rgba(255,255,255,0.15); border-radius: 10px;">
                        <div class="progress-bar bg-white" role="progressbar" style="width: {{ $availPercent }}%; border-radius: 10px;" aria-valuenow="{{ $availPercent }}" aria-valuemin="0" aria-valuemax="100"></div>
                    </div>
                    <small class="text-white-50 mt-1 d-block" style="font-size: 0.65rem;">{{ round($availPercent) }}% of total menu</small>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Unavailable -->
    <div class="col-md-3 col-sm-6 mb-3">
        <div class="card border-0 shadow-sm rounded-lg overflow-hidden h-100" style="background: linear-gradient(45deg, #f85032 0%, #f16232 100%);">
            <div class="card-body p-3">
                <div class="d-flex align-items-center mb-2">
                    <div class="rounded-circle d-flex align-items-center justify-content-center mr-3" style="width: 45px; height: 45px; background: rgba(255,255,255,0.2);">
                        <i class="fa fa-eye-slash text-white fa-lg"></i>
                    </div>
                    <div>
                        <h6 class="text-white-50 text-uppercase mb-0 font-weight-bold" style="font-size: 0.7rem; letter-spacing: 1px;">Unavailable</h6>
                        <h3 class="text-white mb-0 font-weight-bold">{{ $unavailableCount }}</h3>
                    </div>
                </div>
                @php
                    $unavailPercent = $totalCount > 0 ? ($unavailableCount / $totalCount) * 100 : 0;
                @endphp
                <div class="mt-3">
                    <div class="progress" style="height: 4px; background: rgba(255,255,255,0.15); border-radius: 10px;">
                        <div class="progress-bar bg-white" role="progressbar" style="width: {{ $unavailPercent }}%; border-radius: 10px;" aria-valuenow="{{ $unavailPercent }}" aria-valuemin="0" aria-valuemax="100"></div>
                    </div>
                    <small class="text-white-50 mt-1 d-block" style="font-size: 0.65rem;">{{ round($unavailPercent) }}% out of stock</small>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Categories -->
    <div class="col-md-3 col-sm-6 mb-3">
        <div class="card border-0 shadow-sm rounded-lg overflow-hidden h-100" style="background: linear-gradient(45deg, #1d2b64 0%, #f8cdda 100%);">
            <div class="card-body p-3">
                <div class="d-flex align-items-center mb-2">
                    <div class="rounded-circle d-flex align-items-center justify-content-center mr-3" style="width: 45px; height: 45px; background: rgba(255,255,255,0.2);">
                        <i class="fa fa-list text-white fa-lg"></i>
                    </div>
                    <div>
                        <h6 class="text-white-50 text-uppercase mb-0 font-weight-bold" style="font-size: 0.7rem; letter-spacing: 1px;">Categories</h6>
                        <h3 class="text-white mb-0 font-weight-bold">{{ $categories->count() }}</h3>
                    </div>
                </div>
                <div class="mt-3">
                    <div class="progress" style="height: 4px; background: rgba(255,255,255,0.15); border-radius: 10px;">
                        <div class="progress-bar bg-white" role="progressbar" style="width: 100%; border-radius: 10px;" aria-valuenow="100" aria-valuemin="0" aria-valuemax="100"></div>
                    </div>
                    <small class="text-white-50 mt-1 d-block" style="font-size: 0.65rem;">Active menu groups</small>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-12">
        <div class="tile">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h3 class="tile-title mb-0"><i class="fa fa-list"></i> Menu Items</h3>
                <a href="{{ route('admin.recipes.create') }}" class="btn btn-primary">
                    <i class="fa fa-plus"></i> Add New Item
                </a>
            </div>

            <div class="tile-body">
                <!-- Search Area -->
                <div class="row mb-3">
                    <div class="col-md-6">
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text"><i class="fa fa-search"></i></span>
                            </div>
                            <input type="text" id="menuSearch" class="form-control" placeholder="Search menu items...">
                        </div>
                    </div>
                    <div class="col-md-6 text-right pt-2 text-muted">
                        <span id="resultCount">{{ $totalCount }}</span> items found in total
                    </div>
                </div>

                @if($recipes->isEmpty())
                <div class="text-center p-5">
                    <i class="fa fa-utensils fa-3x text-muted mb-3"></i>
                    <h3>No Menu Items Yet</h3>
                    <p>Start building your menu by adding your first item.</p>
                    <a href="{{ route('admin.recipes.create') }}" class="btn btn-primary mt-2">
                        <i class="fa fa-plus"></i> Add First Item
                    </a>
                </div>
                @else
                <!-- Category Tabs (Matching Bar Stock Design) -->
                <ul class="nav nav-pills nav-fill mb-3" role="tablist" style="background: linear-gradient(135deg, #009688 0%, #00695c 100%); padding: 10px; border-radius: 10px;">
                    <li class="nav-item">
                        <a class="nav-link active category-pill" data-category="" href="#" role="tab" style="border-radius: 8px; font-weight: 600; color: white;">
                            <i class="fa fa-th-large"></i> All Items
                            <span class="badge badge-light ml-1" id="totalCount">{{ $totalCount }}</span>
                        </a>
                    </li>
                    @foreach($categories as $category)
                    @php $catCount = \App\Models\Recipe::where('category', $category)->count(); @endphp
                    <li class="nav-item">
                        <a class="nav-link category-pill" data-category="{{ $category }}" href="#" role="tab" style="border-radius: 8px; font-weight: 600; color: rgba(255,255,255,0.8);">
                            {{ ucfirst(str_replace('_', ' ', $category)) }}
                            <span class="badge badge-light ml-1">{{ $catCount }}</span>
                        </a>
                    </li>
                    @endforeach
                </ul>

                <div class="row mt-4" id="menuCards">
                    @foreach($recipes as $recipe)
                    @php
                        $statusColor = $recipe->is_available ? 'success' : 'danger';
                        $headerClass = $recipe->is_available ? 'bg-success text-white' : 'bg-danger text-white';
                        $borderClass = $recipe->is_available ? 'border-success' : 'border-danger';
                    @endphp
                    <div class="col-xl-4 col-lg-6 col-md-6 mb-4 menu-card" 
                         data-name="{{ strtolower($recipe->name) }}" 
                         data-category="{{ strtolower($recipe->category) }}">
                        <div class="card h-100 {{ $borderClass }}" style="box-shadow: 0 2px 4px rgba(0,0,0,0.1); border-radius: 8px; overflow: hidden; border-width: 2px !important;">
                            <!-- Card Header -->
                            <div class="card-header {{ $headerClass }} py-2 px-3">
                                <div class="d-flex justify-content-between align-items-center">
                                    <h6 class="card-title mb-0 text-truncate font-weight-bold" title="{{ $recipe->name }}" style="max-width: 70%; font-size: 14px;">
                                        <i class="fa fa-utensils mr-1"></i> {{ $recipe->name }}
                                    </h6>
                                    <div class="btn-group">
                                        <a href="{{ route('admin.recipes.edit', $recipe) }}" class="btn btn-sm btn-light py-0 px-2" title="Edit">
                                            <i class="fa fa-pencil text-primary" style="font-size: 12px;"></i>
                                        </a>
                                        <button type="button" class="btn btn-sm btn-light py-0 px-2 delete-btn" data-form="delete-form-{{ $recipe->id }}" title="Delete">
                                            <i class="fa fa-trash text-danger" style="font-size: 12px;"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Card Body -->
                            <div class="card-body p-3">
                                <div class="row">
                                    <div class="col-4 pr-1">
                                        @if($recipe->image)
                                            <img src="{{ asset('storage/' . ltrim($recipe->image, '/')) }}" class="rounded shadow-sm" alt="{{ $recipe->name }}" style="width: 100%; height: 90px; object-fit: cover; border: 2px solid #f0f0f0;" onerror="this.onerror=null;this.src='https://ui-avatars.com/api/?name={{ urlencode($recipe->name) }}&background=fff3e0&color=e77a31&size=200'">
                                        @else
                                            @php
                                                $foodIcons = [
                                                    'appetizers' => ['icon' => 'fa-fire', 'grad' => 'linear-gradient(135deg, #FF9966 0%, #FF5E62 100%)'],
                                                    'main_course' => ['icon' => 'fa-cutlery', 'grad' => 'linear-gradient(135deg, #11998e 0%, #38ef7d 100%)'],
                                                    'desserts' => ['icon' => 'fa-birthday-cake', 'grad' => 'linear-gradient(135deg, #ee9ca7 0%, #ffdde1 100%)'],
                                                    'beverages' => ['icon' => 'fa-coffee', 'grad' => 'linear-gradient(135deg, #3D2B1F 0%, #964B00 100%)'],
                                                    'breakfast' => ['icon' => 'fa-sun-o', 'grad' => 'linear-gradient(135deg, #fceabb 0%, #f8b500 100%)'],
                                                    'lunch' => ['icon' => 'fa-shopping-bag', 'grad' => 'linear-gradient(135deg, #4facfe 0%, #00f2fe 100%)'],
                                                    'dinner' => ['icon' => 'fa-moon-o', 'grad' => 'linear-gradient(135deg, #243B55 0%, #141E30 100%)'],
                                                    'snacks' => ['icon' => 'fa-lemon-o', 'grad' => 'linear-gradient(135deg, #f2994a 0%, #f2c94c 100%)'],
                                                    'salads' => ['icon' => 'fa-leaf', 'grad' => 'linear-gradient(135deg, #134E5E 0%, #71B280 100%)'],
                                                    'soups' => ['icon' => 'fa-spoon', 'grad' => 'linear-gradient(135deg, #EB3349 0%, #F45C43 100%)'],
                                                ];
                                                $style = $foodIcons[$recipe->category] ?? ['icon' => 'fa-cutlery', 'grad' => 'linear-gradient(135deg, #009688 0%, #00695c 100%)'];
                                            @endphp
                                            <div class="rounded d-flex align-items-center justify-content-center border shadow-sm" style="width: 100%; height: 90px; background: {!! $style['grad'] !!};">
                                                <i class="fa {!! $style['icon'] !!} fa-2x text-white opacity-50"></i>
                                            </div>
                                        @endif
                                        <div class="text-center mt-2">
                                            <span class="badge badge-light text-uppercase border" style="font-size: 9px; letter-spacing: 0.5px; display: block; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">{{ $recipe->category_name }}</span>
                                        </div>
                                    </div>
                                    <div class="col-8 pl-2">
                                        <div class="mb-2 p-2 bg-light rounded" style="min-height: 80px;">
                                            <div class="d-flex justify-content-between mb-1" style="font-size: 11px;">
                                                <span class="text-muted"><i class="fa fa-clock-o mr-1"></i> Prep Time:</span>
                                                <span class="font-weight-bold text-dark">{{ $recipe->prep_time ?? '-' }} min</span>
                                            </div>
                                            <p class="text-muted small mb-0 mt-1 overflow-hidden" style="font-size: 11px; line-height: 1.3; height: 42px; display: -webkit-box; -webkit-line-clamp: 3; -webkit-box-orient: vertical;">
                                                {{ $recipe->description ?: 'No description available for this delicious menu item.' }}
                                            </p>
                                        </div>
                                        
                                        @if($recipe->is_available)
                                            <span class="badge badge-success w-100 py-1" style="font-size: 10px;"><i class="fa fa-check-circle"></i> Item Available</span>
                                        @else
                                            <span class="badge badge-danger w-100 py-1" style="font-size: 10px;"><i class="fa fa-times-circle"></i> Currently Unavailable</span>
                                        @endif
                                    </div>
                                </div>
                            </div>

                            <!-- Card Footer -->
                            <div class="card-footer bg-white py-2 px-3 border-top-0">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div class="price-info">
                                        <small class="text-muted d-block" style="font-size: 10px;">Selling Price</small>
                                        <h5 class="text-primary font-weight-bold mb-0" id="price-display-{{ $recipe->id }}" style="font-size: 16px;">
                                            {{ number_format($recipe->selling_price) }} <small class="text-muted" style="font-size: 10px;">TSH</small>
                                            @if(!empty($recipe->selling_price_usd) && $recipe->selling_price_usd > 0)
                                                <span class="text-success ml-2" style="font-size: 14px;">${{ rtrim(rtrim(number_format($recipe->selling_price_usd, 2), '0'), '.') }}</span>
                                            @endif
                                        </h5>
                                    </div>
                                    <button type="button" class="btn btn-outline-primary btn-sm rounded-circle quick-price-btn" 
                                            data-id="{{ $recipe->id }}" 
                                            data-name="{{ $recipe->name }}" 
                                            data-price="{{ $recipe->selling_price }}"
                                            data-price-usd="{{ $recipe->selling_price_usd }}"
                                            style="width: 30px; height: 30px; padding: 0;"
                                            title="Update Price">
                                        <i class="fa fa-money" style="font-size: 12px;"></i>
                                    </button>
                                </div>
                            </div>

                            <form action="{{ route('admin.recipes.destroy', $recipe) }}" method="POST" id="delete-form-{{ $recipe->id }}" class="d-none">
                                @csrf
                                @method('DELETE')
                            </form>
                        </div>
                    </div>
                    @endforeach
                </div>

                <div class="mt-3">
                    <div id="clientPaginator" class="d-flex justify-content-center flex-wrap" style="gap: 4px;"></div>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Quick Price Update Modal -->
<div class="modal fade" id="quickPriceModal" tabindex="-1" role="dialog" aria-hidden="true" style="z-index: 2000;">
    <div class="modal-dialog modal-dialog-centered modal-sm" role="document">
        <div class="modal-content" style="border-radius: 12px; border: none; box-shadow: 0 10px 30px rgba(0,0,0,0.2);">
            <div class="modal-header border-0 pb-0">
                <h6 class="modal-title font-weight-bold" id="quickPriceTitle">Update Price</h6>
                <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
            </div>
            <div class="modal-body pt-3">
                <input type="hidden" id="qp_recipe_id">
                <div class="form-group mb-3">
                    <label class="small font-weight-bold text-muted mb-1">TSH Price <span class="text-danger">*</span></label>
                    <input type="number" id="qp_price_tsh" class="form-control" placeholder="e.g. 15000">
                </div>
                <div class="form-group mb-0">
                    <label class="small font-weight-bold text-muted mb-1">USD Price ($)</label>
                    <input type="number" step="0.01" id="qp_price_usd" class="form-control" placeholder="e.g. 7.50">
                </div>
            </div>
            <div class="modal-footer border-0 pt-1 pb-3">
                <button type="button" class="btn btn-light btn-sm font-weight-bold" data-dismiss="modal" style="border-radius: 8px;">Cancel</button>
                <button type="button" class="btn btn-primary btn-sm font-weight-bold" id="btnSaveQuickPrice" style="border-radius: 8px;">Save Changes</button>
            </div>
        </div>
    </div>
</div>
@endsection

@section('styles')
<style>
  .card { 
      transition: all 0.3s cubic-bezier(.25,.8,.25,1); 
  }
  .card:hover { 
      transform: translateY(-5px); 
      box-shadow: 0 10px 20px rgba(0,0,0,0.15) !important; 
  }
  .badge { 
      font-size: 0.85rem; 
  }
  .quick-price-btn {
      transition: all 0.2s;
  }
  .quick-price-btn:hover {
      background-color: #009688;
      color: white !important;
      border-color: #009688;
  }
  .category-pill.active {
      background: white !important;
      color: #00695c !important;
      box-shadow: 0 4px 6px rgba(0,0,0,0.1);
  }
  .category-pill:hover:not(.active) {
      background: rgba(255,255,255,0.1);
      color: white !important;
  }
</style>
@endsection

@section('scripts')
<script src="{{ asset('dashboard_assets/js/plugins/sweetalert.min.js') }}"></script>
<script>
$(document).ready(function() {
    var selectedCategory = '';
    var ITEMS_PER_PAGE = 12;
    var currentPage = 1;

    // Collect all cards once
    var allCards = [];
    $('.menu-card').each(function() { allCards.push(this); });

    // ── Core render ──────────────────────────────────────────────
    function applyFilterAndPage() {
        var searchTerm = $('#menuSearch').val().toLowerCase().trim();

        var matched = allCards.filter(function(card) {
            var name  = $(card).data('name') || '';
            var cat   = $(card).data('category') || '';
            var okS   = searchTerm === '' || name.includes(searchTerm);
            var okC   = selectedCategory === '' || cat === selectedCategory;
            return okS && okC;
        });

        $(allCards).hide();
        var total      = matched.length;
        var totalPages = Math.max(1, Math.ceil(total / ITEMS_PER_PAGE));
        if (currentPage > totalPages) currentPage = 1;

        var start = (currentPage - 1) * ITEMS_PER_PAGE;
        $(matched.slice(start, start + ITEMS_PER_PAGE)).show();

        $('#resultCount').text(total);
        renderPaginator(totalPages);
    }

    // ── Paginator ────────────────────────────────────────────────
    function renderPaginator(totalPages) {
        var $pager = $('#clientPaginator');
        $pager.empty();
        if (totalPages <= 1) return;

        function mkBtn(label, page, disabled, active) {
            return $('<button>')
                .addClass('btn btn-sm ' + (active ? 'btn-primary' : 'btn-outline-secondary'))
                .prop('disabled', disabled)
                .css({ borderRadius: '6px', fontWeight: '600', minWidth: '36px', margin: '2px' })
                .text(label)
                .on('click', function() {
                    currentPage = page;
                    applyFilterAndPage();
                    $('html,body').animate({ scrollTop: $('#menuCards').offset().top - 80 }, 200);
                });
        }

        $pager.append(mkBtn('‹', currentPage - 1, currentPage === 1, false));

        var pages = [];
        if (totalPages <= 7) {
            for (var i = 1; i <= totalPages; i++) pages.push(i);
        } else {
            pages = [1];
            if (currentPage > 3) pages.push('...');
            for (var i = Math.max(2, currentPage-1); i <= Math.min(totalPages-1, currentPage+1); i++) pages.push(i);
            if (currentPage < totalPages - 2) pages.push('...');
            pages.push(totalPages);
        }

        pages.forEach(function(p) {
            if (p === '...') {
                $pager.append($('<span>').text('…').css({ padding: '4px 8px', color: '#999' }));
            } else {
                $pager.append(mkBtn(p, p, false, p === currentPage));
            }
        });

        $pager.append(mkBtn('›', currentPage + 1, currentPage === totalPages, false));
    }

    // ── Events ───────────────────────────────────────────────────
    $('.category-pill').on('click', function(e) {
        e.preventDefault();
        $('.category-pill').removeClass('active').css('color', 'rgba(255,255,255,0.8)');
        $(this).addClass('active').css('color', 'black');
        selectedCategory = $(this).data('category');
        currentPage = 1;
        applyFilterAndPage();
    });

    $('#menuSearch').on('input', function() {
        currentPage = 1;
        applyFilterAndPage();
    });

    // Initial styles & render
    $('.category-pill.active').css('color', 'black');
    applyFilterAndPage();

    // ── Delete ───────────────────────────────────────────────────
    $(document).on('click', '.delete-btn', function(e) {
        e.preventDefault();
        var formId = $(this).data('form');
        swal({
            title: "Are you sure?",
            text: "This menu item will be permanently deleted!",
            type: "warning",
            showCancelButton: true,
            confirmButtonText: "Yes, delete it!",
            cancelButtonText: "Cancel",
            closeOnConfirm: false
        }, function() { $('#' + formId).submit(); });
    });

    // ── Quick Price ──────────────────────────────────────────────
    $(document).on('click', '.quick-price-btn', function() {
        var id   = $(this).data('id');
        var name = $(this).data('name');
        var curTsh  = $(this).data('price');
        var curUsd  = $(this).data('price-usd') || '';
        
        $('#quickPriceTitle').text('Update: ' + name);
        $('#qp_recipe_id').val(id);
        $('#qp_price_tsh').val(curTsh);
        $('#qp_price_usd').val(curUsd);
        
        $('#quickPriceModal').modal('show');
    });

    $('#btnSaveQuickPrice').on('click', function() {
        var id = $('#qp_recipe_id').val();
        var valTsh = $('#qp_price_tsh').val();
        var valUsd = $('#qp_price_usd').val();
        var name = $('.quick-price-btn[data-id="' + id + '"]').data('name');

        if (!valTsh || isNaN(valTsh) || valTsh < 0) {
            swal("Invalid Input!", "Please enter a valid TSH price.", "error");
            return;
        }

        var btn = $(this);
        btn.prop('disabled', true).text('Saving...');

        $.ajax({
            url: '/manager/restaurants/recipes/' + id + '/update-price',
            type: 'PUT',
            data: { 
                _token: '{{ csrf_token() }}', 
                selling_price: valTsh,
                selling_price_usd: valUsd
            },
            success: function(r) {
                btn.prop('disabled', false).text('Save Changes');
                if (r.success) {
                    $('#quickPriceModal').modal('hide');
                    
                    var newHtml = r.new_price + ' <small class="text-muted" style="font-size: 10px;">TSH</small>';
                    if (r.new_price_usd) {
                        newHtml += ' <span class="text-success ml-2" style="font-size: 14px;">$' + r.new_price_usd + '</span>';
                    }
                    
                    $('#price-display-' + id).html(newHtml);
                    swal("Updated!", name + " price updated to " + r.new_price + " TSH", "success");
                    
                    $('.quick-price-btn[data-id="' + id + '"]')
                        .data('price', valTsh)
                        .data('price-usd', valUsd);
                } else { 
                    swal("Error!", r.message, "error"); 
                }
            },
            error: function() { 
                btn.prop('disabled', false).text('Save Changes');
                swal("Error!", "Something went wrong!", "error"); 
            }
        });
    });
});
</script>
@endsection

