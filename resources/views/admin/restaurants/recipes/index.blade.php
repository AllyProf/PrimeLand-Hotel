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
                        <h3 class="text-white mb-0 font-weight-bold">{{ $recipes->total() }}</h3>
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
                        <h3 class="text-white mb-0 font-weight-bold">{{ $recipes->where('is_available', true)->count() }}</h3>
                    </div>
                </div>
                @php
                    $totalCount = $recipes->total() > 0 ? $recipes->total() : 1;
                    $availPercent = ($recipes->where('is_available', true)->count() / $totalCount) * 100;
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
                        <h3 class="text-white mb-0 font-weight-bold">{{ $recipes->where('is_available', false)->count() }}</h3>
                    </div>
                </div>
                @php
                    $unavailPercent = ($recipes->where('is_available', false)->count() / $totalCount) * 100;
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
                        <span id="resultCount">{{ $recipes->count() }}</span> items found in total
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
                            <span class="badge badge-light ml-1">{{ $recipes->count() }}</span>
                        </a>
                    </li>
                    @foreach($categories as $category)
                    <li class="nav-item">
                        <a class="nav-link category-pill" data-category="{{ $category }}" href="#" role="tab" style="border-radius: 8px; font-weight: 600; color: rgba(255,255,255,0.8);">
                            {{ ucfirst(str_replace('_', ' ', $category)) }}
                            <span class="badge badge-light ml-1">{{ $recipes->where('category', $category)->count() }}</span>
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
                                            <div class="bg-light rounded d-flex align-items-center justify-content-center border" style="width: 100%; height: 90px;">
                                                <i class="fa fa-utensils fa-2x text-muted opacity-50"></i>
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
                                        </h5>
                                    </div>
                                    <button type="button" class="btn btn-outline-primary btn-sm rounded-circle quick-price-btn" 
                                            data-id="{{ $recipe->id }}" 
                                            data-name="{{ $recipe->name }}" 
                                            data-price="{{ $recipe->selling_price }}"
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
                    {{ $recipes->links() }}
                </div>
                @endif
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

    // Search and Filter Function
    function filterCards() {
        var searchTerm = $('#menuSearch').val().toLowerCase().trim();
        var visibleCount = 0;
        
        $('.menu-card').each(function() {
            var $card = $(this);
            var name = $card.data('name') || '';
            var category = $card.data('category') || '';
            
            var matchesSearch = searchTerm === '' || name.includes(searchTerm);
            var matchesCategory = selectedCategory === '' || category === selectedCategory;
            
            if (matchesSearch && matchesCategory) {
                $card.show();
                visibleCount++;
            } else {
                $card.hide();
            }
        });
        $('#resultCount').text(visibleCount);
    }

    // Category Pill Click
    $('.category-pill').on('click', function(e) {
        e.preventDefault();
        $('.category-pill').removeClass('active').css('color', 'rgba(255,255,255,0.8)');
        $(this).addClass('active').css('color', 'black'); // Black text for active white pill
        
        selectedCategory = $(this).data('category');
        filterCards();
    });

    $('#menuSearch').on('input', filterCards);

    // Initial styles for active pill
    $('.category-pill.active').css('color', 'black');

    // Delete confirmation
    $('.delete-btn').on('click', function(e) {
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
        }, function() {
            $('#' + formId).submit();
        });
    });

    // Quick Price Update
    $('.quick-price-btn').on('click', function() {
        var id = $(this).data('id');
        var name = $(this).data('name');
        var currentPrice = $(this).data('price');

        swal({
            title: "Update Price",
            text: "Set a new price for " + name + " (TSH):",
            type: "input",
            showCancelButton: true,
            closeOnConfirm: false,
            inputPlaceholder: "Enter new price...",
            inputValue: currentPrice
        }, function(inputValue) {
            if (inputValue === false) return false;
            if (inputValue === "" || isNaN(inputValue)) {
                swal.showInputError("Please enter a valid numeric price!");
                return false;
            }

            $.ajax({
                url: '/manager/restaurants/recipes/' + id + '/update-price',
                type: 'PUT',
                data: {
                    _token: '{{ csrf_token() }}',
                    selling_price: inputValue
                },
                success: function(response) {
                    if (response.success) {
                        $('#price-display-' + id).html(response.new_price + ' <small class="text-muted font-weight-normal" style="font-size: 0.55rem;">TSH</small>');
                        swal("Updated!", "Price for " + name + " has been updated to " + response.new_price + " TSH", "success");
                        $('.quick-price-btn[data-id="' + id + '"]').data('price', inputValue);
                    } else {
                        swal("Error!", response.message, "error");
                    }
                },
                error: function(xhr) {
                    swal("Error!", "Something went wrong!", "error");
                }
            });
        });
    });
});
</script>
@endsection
