@extends('dashboard.layouts.app')

@section('content')
<div class="app-title">
    <div>
        <h1><i class="fa fa-cubes"></i> My Stock Inventory</h1>
        <p>Manage your bar inventory and track stock levels</p>
    </div>
    <ul class="app-breadcrumb breadcrumb">
        <li class="breadcrumb-item"><i class="fa fa-home fa-lg"></i></li>
        <li class="breadcrumb-item"><a href="{{ route('bar-keeper.dashboard') }}">Dashboard</a></li>
        <li class="breadcrumb-item active">Stock</li>
    </ul>
</div>

<!-- Stats Row -->
<div class="row">
    <div class="col-md-3">
        <div class="widget-small primary coloured-icon">
            <i class="icon fa fa-glass fa-3x"></i>
            <div class="info">
                <h4>Products</h4>
                <p><b>{{ $myStock->count() }}</b></p>
            </div>
        </div>
    </div>
    
    <div class="col-md-3">
        <div class="widget-small info coloured-icon">
            <i class="icon fa fa-cubes fa-3x"></i>
            <div class="info">
                <h4>In Stock</h4>
                <p><b>{{ number_format($myStock->sum('current_stock_pics'), 0) }}</b></p>
            </div>
        </div>
    </div>

    @php
        $isManager = in_array(strtolower(Auth::guard('staff')->user()->role), ['manager', 'admin', 'super_admin', 'owner']);
    @endphp

    @if($isManager)
    <div class="col-md-3">
        <div class="widget-small warning coloured-icon">
            <i class="icon fa fa-money fa-3x"></i>
            <div class="info">
                <h4>Revenue</h4>
                <p><b>{{ number_format($myStock->sum('revenue_generated'), 0) }}</b></p>
            </div>
        </div>
    </div>
    
    <div class="col-md-3">
        <div class="widget-small danger coloured-icon">
            <i class="icon fa fa-line-chart fa-3x"></i>
            <div class="info">
                <h4>Stock Value</h4>
                <p><b>{{ number_format($myStock->sum('revenue_serving'), 0) }}</b></p>
            </div>
        </div>
    </div>
    @else
    @php
        $lowStockCount = $myStock->filter(function($item) {
            return $item['current_stock_pics'] <= ($item['minimum_stock'] ?: 3);
        })->count();
    @endphp
    <div class="col-md-3">
        <div class="widget-small danger coloured-icon">
            <i class="icon fa fa-exclamation-triangle fa-3x"></i>
            <div class="info">
                <h4>Low Stock</h4>
                <p><b>{{ $lowStockCount }}</b></p>
            </div>
        </div>
    </div>
    
    <div class="col-md-3">
        <div class="widget-small success coloured-icon">
            <i class="icon fa fa-list fa-3x"></i>
            <div class="info">
                <h4>Categories</h4>
                <p><b>{{ $categories->count() }}</b></p>
            </div>
        </div>
    </div>
    @endif
</div>

<div class="row">
    <div class="col-md-12">
        <div class="tile">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h3 class="tile-title mb-0"><i class="fa fa-cubes"></i> Inventory Balance</h3>
            </div>

            <div class="tile-body">
                <!-- Search Area -->
                <div class="row mb-3">
                    <div class="col-md-6">
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text"><i class="fa fa-search"></i></span>
                            </div>
                            <input type="text" id="inventorySearch" class="form-control" placeholder="Search products...">
                        </div>
                    </div>
                    <div class="col-md-6 text-right">
                        <div id="searchResults" class="text-muted pt-2">
                            <span id="resultCount">{{ $myStock->count() }}</span> items found
                        </div>
                    </div>
                </div>

                @if($myStock->isEmpty())
                <div class="text-center p-5">
                    <i class="fa fa-box-open fa-3x text-muted mb-3"></i>
                    <h3>No Stock Available</h3>
                    <p>You haven't received any stock transfers yet.</p>
                </div>
                @else
                <div class="table-responsive mt-3">
                    <table class="table table-hover table-bordered bg-white" id="stockTable">
                        <thead class="bg-light">
                            <tr>
                                <th style="width: 40px;" class="text-center">
                                    <div class="custom-control custom-checkbox">
                                        <input type="checkbox" class="custom-control-input" id="masterCheckbox">
                                        <label class="custom-control-label" for="masterCheckbox"></label>
                                    </div>
                                </th>
                                <th style="width: 50px;">Image</th>
                                <th>Product Details</th>
                                <th>Category</th>
                                <th>Current Stock</th>
                                @if($isManager)
                                <th>Revenue</th>
                                <th>Potential</th>
                                @endif
                                <th class="text-center">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($myStock as $item)
                            <tr class="inventory-card" data-name="{{ strtolower($item['product_name']) }}" data-brand="{{ strtolower($item['brand_name']) }}" data-variant="{{ strtolower($item['variant_name']) }}">
                                <td class="text-center align-middle">
                                    <div class="custom-control custom-checkbox">
                                        <input type="checkbox" class="custom-control-input stock-checkbox" id="check-{{ $item['variant_id'] }}" value="{{ $item['variant_id'] }}">
                                        <label class="custom-control-label" for="check-{{ $item['variant_id'] }}"></label>
                                    </div>
                                </td>
                                <td class="align-middle">
                                    @if($item['product_image'])
                                        <img src="{{ asset('storage/' . ltrim($item['product_image'], '/')) }}" class="rounded shadow-sm" style="width: 40px; height: 40px; object-fit: cover;">
                                    @else
                                        <div class="rounded bg-light d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                                            <i class="fa fa-glass text-muted"></i>
                                        </div>
                                    @endif
                                </td>
                                <td class="align-middle">
                                    <div class="font-weight-bold text-primary">{{ $item['product_name'] }}</div>
                                    <small class="text-muted">{{ $item['brand_name'] }} | {{ $item['variant_name'] }}</small>
                                </td>
                                <td class="align-middle text-uppercase">
                                    <span class="badge badge-light border">{{ $item['category_name'] }}</span>
                                </td>
                                <td class="align-middle">
                                    @php
                                        $isLow = $item['current_stock_pics'] <= $item['minimum_stock'];
                                        $stockColor = $isLow ? 'text-danger' : 'text-success';
                                    @endphp
                                    <div class="{{ $stockColor }} font-weight-bold" style="font-size: 1.1rem;">
                                        {{ $item['full_bottles'] }} {{ ucfirst($item['packaging']) }}
                                        @if($item['servings_per_pic'] > 1)
                                            <small class="text-muted ml-1" style="font-size: 0.75rem;">(+{{ $item['open_servings'] }} servings)</small>
                                        @endif
                                    </div>
                                    @if($isLow)
                                        <span class="badge badge-danger" style="font-size: 9px;">LOW STOCK</span>
                                    @endif
                                </td>
                                @if($isManager)
                                <td class="align-middle font-weight-bold">
                                    {{ number_format($item['revenue_generated'], 0) }}
                                </td>
                                <td class="align-middle text-info font-weight-bold">
                                    {{ number_format($item['revenue_serving'], 0) }}
                                </td>
                                @endif
                                <td class="text-center align-middle">
                                    <div class="btn-group">
                                        <button type="button" class="btn btn-sm btn-info view-track-btn" 
                                                data-variant-id="{{ $item['variant_id'] }}" 
                                                data-item-name="{{ $item['product_name'] }}"
                                                title="Usage History">
                                            <i class="fa fa-history"></i>
                                        </button>
                                        <button type="button" class="btn btn-sm btn-primary settings-stock-btn" 
                                                data-variant-id="{{ $item['variant_id'] }}" 
                                                data-item-name="{{ $item['product_name'] }}"
                                                data-minimum-stock="{{ $item['minimum_stock'] }}"
                                                data-price-pic="{{ $item['selling_price_per_pic'] }}"
                                                data-price-glass="{{ $item['selling_price_per_serving'] }}"
                                                data-price-pic-usd="{{ $item['selling_price_per_pic_usd'] ?? 0 }}"
                                                data-price-glass-usd="{{ $item['selling_price_per_serving_usd'] ?? 0 }}"
                                                title="Pricing & Alerts">
                                            <i class="fa fa-cog"></i>
                                        </button>
                                        <a href="{{ route('bar-keeper.purchase-requests.create', ['ids' => $item['variant_id']]) }}" class="btn btn-sm btn-warning" title="Restock">
                                            <i class="fa fa-shopping-cart"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Product Settings Modal -->
<div class="modal fade" id="productSettingsModal" tabindex="-1">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header bg-dark text-white">
        <h5 class="modal-title"><i class="fa fa-cog"></i> Product Settings & Pricing</h5>
        <button type="button" class="close text-white" data-dismiss="modal"><span>&times;</span></button>
      </div>
      <div class="modal-body">
          <!-- Item Info -->
          <div class="mb-4 text-center">
              <h4 id="settings_item_name_display" class="mb-1"></h4>
              <span class="badge badge-info shadow-sm" id="variant_pill"></span>
          </div>

          <!-- Section 1: Stock Alert (Only for Bar Keepers, not Managers) -->
          @if($role !== 'manager')
          <div class="p-3 mb-3 rounded" style="background: #f8f9fa; border-left: 5px solid #17a2b8;">
              <h6 class="font-weight-bold text-info"><i class="fa fa-bell"></i> Inventory Alert</h6>
              <form id="minimumStockForm">
                  <input type="hidden" class="settings_variant_id" name="variant_id">
                  <div class="form-group mb-0 mt-2">
                    <label class="small font-weight-bold">Min. Stock Alert Level (PICs)</label>
                    <div class="input-group">
                        <input type="number" step="0.01" class="form-control" id="minimum_stock" name="minimum_stock" required min="0">
                        <div class="input-group-append">
                            <button type="submit" class="btn btn-info">Update Alert</button>
                        </div>
                    </div>
                  </div>
              </form>
          </div>
          @endif

          <!-- Section 2: Pricing -->
          <div class="p-3 mb-3 rounded shadow-sm" style="background: #fff; border-top: 3px solid #e77a31; border-right: 1px solid #eee; border-bottom: 1px solid #eee; border-left: 1px solid #eee;">
               <h6 class="font-weight-bold text-dark"><i class="fa fa-money"></i> Selling Prices</h6>
               <form id="priceUpdateForm">
                  <input type="hidden" class="settings_variant_id" name="variant_id">
                  
                  <div class="row mt-3">
                      <div class="col-6">
                           <div class="form-group mb-2">
                                <label class="small font-weight-bold text-muted mb-1">Per Bottle (TSH)</label>
                                <input type="number" class="form-control form-control-sm border-primary" id="price_pic" name="selling_price_per_pic" required min="0">
                           </div>
                      </div>
                      <div class="col-6">
                           <div class="form-group mb-2">
                                <label class="small text-success mb-1" style="font-weight:800;">USD ($)</label>
                                <input type="number" step="0.01" class="form-control form-control-sm border-success" id="price_pic_usd" name="selling_price_per_pic_usd" min="0">
                           </div>
                      </div>
                  </div>

                  <div class="row mt-1 pb-2 border-bottom mb-3" id="glass_price_section">
                      <div class="col-6">
                           <div class="form-group mb-0">
                                <label class="small font-weight-bold text-muted mb-1">Per Glass (TSH)</label>
                                <input type="number" class="form-control form-control-sm border-primary" id="price_glass" name="selling_price_per_serving" min="0">
                           </div>
                      </div>
                      <div class="col-6">
                           <div class="form-group mb-0">
                                <label class="small text-success mb-1" style="font-weight:800;">USD ($)</label>
                                <input type="number" step="0.01" class="form-control form-control-sm border-success" id="price_glass_usd" name="selling_price_per_serving_usd" min="0">
                           </div>
                      </div>
                  </div>

                  <button type="submit" class="btn btn-block font-weight-bold text-white shadow-sm mt-3" style="background-color: #e77a31;">
                      Save Changes
                  </button>
               </form>
          </div>
      </div>
      <div class="modal-footer bg-light">
          <button type="button" class="btn btn-link text-muted" data-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>

<!-- Usage Tracking Modal -->
<div class="modal fade" id="usageTrackModal" tabindex="-1">
  <div class="modal-dialog modal-lg">
    <div class="modal-content border-0 shadow-lg" style="border-radius: 15px;">
      <div class="modal-header bg-dark text-white" style="border-radius: 15px 15px 0 0;">
        <h5 class="modal-title"><i class="fa fa-history"></i> Usage Tracking: <span id="track_item_name"></span>
          <span id="track_current_stock_badge" class="badge badge-success ml-2" style="font-size:12px; display:none;"></span>
        </h5>
        <button type="button" class="close text-white" data-dismiss="modal"><span>&times;</span></button>
      </div>
      <div class="modal-body">
        <div class="table-responsive">
          <table class="table table-hover table-striped" id="usageTimelineTable">
            <thead class="bg-light">
              <tr>
                <th>Date &amp; Time</th>
                <th>Type</th>
                <th>Change</th>
                <th>In Stock</th>
                <th>Staff</th>
                <th>Notes</th>
              </tr>
            </thead>
            <tbody id="usageTrackContent">
              <!-- Content loaded via AJAX -->
            </tbody>
          </table>
          <div id="noUsageData" class="text-center py-4 d-none">
            <i class="fa fa-info-circle fa-2x text-muted mb-2"></i>
            <p>No recorded movements for this item yet.</p>
          </div>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>

@endsection

@section('styles')
<style>
  .card { transition: all 0.3s cubic-bezier(.25,.8,.25,1); border-width: 2px !important; }
  .card:hover { transform: translateY(-3px); box-shadow: 0 10px 20px rgba(0,0,0,0.15) !important; }
  .badge { font-size: 0.85rem; }
  .nav-pills .nav-link.active { 
    background: white !important; 
    color: #667eea !important; 
    box-shadow: 0 4px 6px rgba(0,0,0,0.1);
  }
  .nav-pills .nav-link:hover { 
    background: rgba(255,255,255,0.2); 
    color: white !important;
  }
  .nav-pills .nav-link {
    transition: all 0.3s ease;
  }
</style>
@endsection

@section('scripts')
<script src="{{ asset('dashboard_assets/js/plugins/sweetalert.min.js') }}"></script>
<script>
$(document).ready(function() {
    // Bulk Selection Logic
    function updateBulkBar() {
        var checkedCount = $('.stock-checkbox:checked').length;
        if (checkedCount > 0) {
            $('#bulkActionBar').fadeIn();
            $('#selectedCount').text(checkedCount);
        } else {
            $('#bulkActionBar').fadeOut();
        }
    }

    $(document).on('change', '.stock-checkbox', function() {
        if ($(this).is(':checked')) {
            $(this).closest('.inventory-card').addClass('table-warning');
        } else {
            $(this).closest('.inventory-card').removeClass('table-warning');
            $('#masterCheckbox').prop('checked', false);
        }
        updateBulkBar();
    });

    $('#masterCheckbox').on('change', function() {
        var isChecked = $(this).is(':checked');
        $('.inventory-card:not([style*="display: none"]) .stock-checkbox').prop('checked', isChecked).trigger('change');
    });

    $('#selectAllBtn').on('click', function() {
        $('.inventory-card:not([style*="display: none"]) .stock-checkbox').prop('checked', true).trigger('change');
        $('#masterCheckbox').prop('checked', true);
    });

    $('#clearSelectionBtn').on('click', function() {
        $('.stock-checkbox').prop('checked', false).trigger('change');
    });

    $('#bulkRequestBtn').on('click', function() {
        var selectedIds = $('.stock-checkbox:checked').map(function() {
            return $(this).val();
        }).get().join(',');
        
        if (selectedIds) {
            window.location.href = "{{ route('bar-keeper.purchase-requests.create') }}?ids=" + selectedIds;
        }
    });

    // Real-time Search
    function filterCards() {
        var searchTerm = $('#inventorySearch').val().toLowerCase().trim();
        var visibleCount = 0;
        
        $('.inventory-card').each(function() {
            var $row = $(this);
            var name = $row.data('name') || '';
            var variant = $row.data('variant') || '';
            var brand = $row.data('brand') || '';
            
            var matchesSearch = searchTerm === '' || name.includes(searchTerm) || variant.includes(searchTerm) || brand.includes(searchTerm);
            
            if (matchesSearch) {
                $row.show();
                visibleCount++;
            } else {
                $row.hide();
            }
        });
        $('#resultCount').text(visibleCount);
    }

    $('#inventorySearch').on('input', filterCards);

    // Product Settings Trigger
    $(document).on('click', '.settings-stock-btn', function() {
        var btn = $(this);
        $('.settings_variant_id').val(btn.data('variant-id'));
        $('#settings_item_name_display').text(btn.data('item-name'));
        $('#minimum_stock').val(btn.data('minimum-stock'));
        $('#price_pic').val(btn.data('price-pic'));
        $('#price_glass').val(btn.data('price-glass'));
        $('#price_pic_usd').val(btn.data('price-pic-usd'));
        $('#price_glass_usd').val(btn.data('price-glass-usd'));
        $('#productSettingsModal').modal('show');
    });

    // Min Stock Submit
    $('#minimumStockForm').on('submit', function(e) {
        e.preventDefault();
        var variantId = $(this).find('.settings_variant_id').val();
        
        $.ajax({
            url: '/bar-keeper/stock/update-minimum/' + variantId,
            method: 'POST',
            data: $(this).serialize() + '&_token={{ csrf_token() }}',
            success: function(response) {
                if(response.success) {
                    swal("Updated!", "Inventory threshold has been adjusted.", "success");
                    setTimeout(function() { location.reload(); }, 1000);
                }
            },
            error: function(xhr) {
                swal("Error!", xhr.responseJSON.message || "Failed to update threshold", "error");
            }
        });
    });

    // Price Update Submit
    $('#priceUpdateForm').on('submit', function(e) {
        e.preventDefault();
        var variantId = $(this).find('.settings_variant_id').val();
        
        $.ajax({
            url: '/bar-keeper/stock/update-prices/' + variantId,
            method: 'POST',
            data: $(this).serialize() + '&_token={{ csrf_token() }}',
            success: function(response) {
                if(response.success) {
                    swal("Success!", "Daily prices updated successfully.", "success");
                    setTimeout(function() { location.reload(); }, 1000);
                }
            },
            error: function(xhr) {
                swal("Error!", xhr.responseJSON.message || "Failed to update prices", "error");
            }
        });
    });

    // Usage Tracking Logic
    $(document).on('click', '.view-track-btn', function() {
        var variantId = $(this).data('variant-id');
        var itemName = $(this).data('item-name');
        
        $('#track_item_name').text(itemName);
        $('#usageTrackContent').html('<tr><td colspan="6" class="text-center font-italic">Loading tracking data...</td></tr>');
        $('#noUsageData').addClass('d-none');
        $('#usageTrackModal').modal('show');
        
        $.ajax({
            url: '/bar-keeper/stock/' + variantId + '/usage-track',
            method: 'GET',
            success: function(response) {
                if(response.success && response.movements.length > 0) {
                    var html = '';
                    response.movements.forEach(function(m) {
                        var badgeClass = 'secondary';
                        var icon = '';
                        var typeLower = m.type.toLowerCase();
                        
                        if(typeLower.indexOf('sale') !== -1 || typeLower.indexOf('service') !== -1) {
                            badgeClass = 'info';
                        }
                        if(typeLower.indexOf('receive') !== -1) {
                            badgeClass = 'success';
                        }
                        if(m.is_price_change) {
                            badgeClass = 'primary';
                            icon = '<i class="fa fa-dollar-sign"></i> ';
                        }
                        
                        var colorClass = 'danger';
                        if (m.is_price_change) {
                            colorClass = 'dark';
                        } else if (m.is_addition) {
                            colorClass = 'success';
                        }

                        var inStockCell = '-';
                        if (m.in_stock !== null && m.in_stock !== undefined) {
                            var stockNum = parseFloat(m.in_stock);
                            var stockColor = stockNum > 0 ? '#27ae60' : '#e74c3c';
                            inStockCell = '<strong style="color:' + stockColor + '">' + m.in_stock + '</strong>';
                        }
                        
                        html += '<tr>' +
                            '<td>' + m.date + '</td>' +
                            '<td><span class="badge badge-' + badgeClass + '">' + icon + m.type + '</span></td>' +
                            '<td class="text-' + colorClass + '"><strong>' + m.quantity + '</strong></td>' +
                            '<td>' + inStockCell + '</td>' +
                            '<td>' + m.user + '</td>' +
                            '<td class="small">' + (m.notes || '-') + '</td>' +
                            '</tr>';
                    });
                    $('#usageTrackContent').html(html);
                    // Show current stock badge
                    if (response.current_stock !== undefined) {
                        $('#track_current_stock_badge').text('Current: ' + response.current_stock + ' Pic').show();
                    }
                } else {
                    $('#usageTrackContent').empty();
                    $('#noUsageData').removeClass('d-none');
                }
            },
            error: function() {
                $('#usageTrackContent').html('<tr><td colspan="6" class="text-center text-danger">Failed to load tracking data.</td></tr>');
            }
        });
    });
});
</script>
@endsection

@section('styles')
<style>
#bulkActionBar {
    position: fixed;
    bottom: 20px;
    left: 50%;
    transform: translateX(-50%);
    z-index: 2000;
    background: #333;
    color: white;
    padding: 15px 30px;
    border-radius: 50px;
    box-shadow: 0 10px 30px rgba(0,0,0,0.5);
    display: none;
    width: auto;
    min-width: 450px;
}
.custom-checkbox .custom-control-input:checked ~ .custom-control-label::before {
    background-color: #e77a31;
    border-color: #e77a31;
}

/* Mobile Optimizations */
@media (max-width: 768px) {
    /* Make Action Bar Responsive */
    #bulkActionBar {
        width: 95%;
        min-width: auto;
        padding: 10px 15px;
        bottom: 60px; /* Above bottom nav if exists, or just spaced */
        border-radius: 15px;
        flex-direction: column;
        align-items: stretch !important;
    }
    
    #bulkActionBar .d-flex {
        flex-direction: column;
        align-items: center;
        width: 100%;
    }
    
    #bulkActionBar .mr-4 {
        margin-right: 0 !important;
        margin-bottom: 10px;
        text-align: center;
        width: 100%;
        display: flex;
        justify-content: center;
        align-items: center;
    }
    
    #bulkActionBar .d-flex.align-items-center {
        width: 100%;
        justify-content: space-between;
    }
    
    #bulkActionBar button {
        flex: 1;
        margin: 0 5px;
        font-size: 11px;
        padding: 8px 5px;
        white-space: nowrap;
    }
    
    #bulkActionBar button i {
        display: none; /* Hide icons to save space */
    }

    /* Scrollable Tabs */
    .nav-pills {
        display: flex;
        flex-wrap: nowrap;
        overflow-x: auto;
        -webkit-overflow-scrolling: touch;
        padding-bottom: 5px; /* Space for scrollbar */
    }
    
    .nav-pills .nav-item {
        flex: 0 0 auto;
        margin-right: 5px;
    }
    
    .nav-pills .nav-link {
        font-size: 13px;
        padding: 8px 12px;
        white-space: nowrap;
    }
    
    /* Stats Widgets */
    .widget-small {
        margin-bottom: 10px;
    }
    .widget-small .icon {
        min-width: 60px;
        width: 60px;
    }
    .widget-small .info {
        padding: 10px;
    }
    .widget-small .info h6 {
        font-size: 10px;
    }
    .widget-small .info p {
        font-size: 14px;
    }
}
</style>

<div id="bulkActionBar" class="animated fadeInUp">
    <div class="d-flex justify-content-between align-items-center">
        <div class="mr-4">
            <span class="badge badge-warning mr-2" id="selectedCount" style="font-size: 16px;">0</span>
            <strong style="font-size: 14px;">Selected</strong>
        </div>
        <div class="d-flex align-items-center w-100 justify-content-center">
            <button class="btn btn-sm btn-light mr-2" id="selectAllBtn"><i class="fa fa-check-square"></i> All</button>
            <button class="btn btn-sm btn-outline-danger mr-2" id="clearSelectionBtn" style="color: #ff9d9d; border-color: #ff9d9d;"><i class="fa fa-times"></i> Clear</button>
            <button class="btn btn-sm btn-warning font-weight-bold flex-grow-1" id="bulkRequestBtn" style="background: #e77a31; border-color: #e77a31; color: white;">
                <i class="fa fa-shopping-cart"></i> Restock
            </button>
        </div>
    </div>
</div>
