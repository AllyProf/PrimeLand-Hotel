@extends('dashboard.layouts.app')

@php
use Illuminate\Support\Facades\Storage;
$routePrefix = request()->is('bar-keeper*') ? 'bar-keeper' : 'admin';
@endphp

@section('content')
<style>
    .tile { border-radius: 15px; overflow: hidden; }
    .transition-all { transition: all 0.3s ease; }
    .bg-primary-light { background: #eef2ff !important; }
    .product-row:hover { background-color: #f8f9ff !important; }
    .category-header td { background: #fdfdfd; }
    .badge-success-light { background: #e6ffed; color: #28a745; border: none; }
    .badge-info-light { background: #e8f4ff; color: #007bff; border: none; }
    .category-tabs-scrollable::-webkit-scrollbar { display: none; }
    .btn-light.rounded-circle:hover { background: #eef2ff !important; transform: scale(1.1); }
</style>
<div class="app-title">
  <div>
    <h1><i class="fa fa-cube"></i> Products</h1>
    <p>Manage restaurant products (drinks and food)</p>
  </div>
  <ul class="app-breadcrumb breadcrumb">
    <li class="breadcrumb-item"><i class="fa fa-home fa-lg"></i></li>
    <li class="breadcrumb-item"><a href="{{ route($routePrefix === 'admin' ? 'admin.dashboard' : 'bar-keeper.dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item"><a href="#">Products</a></li>
  </ul>
</div>

<!-- Statistics Section -->
<div class="row mb-4">
  <div class="col-md-4">
    <div class="widget-small primary coloured-icon bg-white shadow-sm border-0" style="border-radius: 12px;">
      <i class="icon fa fa-tags fa-2x" style="background-color: #f0f7ff; color: #007bff; border-radius: 12px 0 0 12px;"></i>
      <div class="info">
        <h4 class="text-muted small text-uppercase font-weight-bold">Product Brands</h4>
        <p class="mb-0 h3"><b>{{ $summaryStats['brands'] }}</b></p>
      </div>
    </div>
  </div>
  <div class="col-md-4">
    <div class="widget-small info coloured-icon bg-white shadow-sm border-0" style="border-radius: 12px;">
      <i class="icon fa fa-folder fa-2x" style="background-color: #fffaf0; color: #ff9800; border-radius: 12px 0 0 12px;"></i>
      <div class="info">
        <h4 class="text-muted small text-uppercase font-weight-bold">Categories</h4>
        <p class="mb-0 h3"><b>{{ $summaryStats['categories'] }}</b></p>
      </div>
    </div>
  </div>
  <div class="col-md-4">
    <div class="widget-small success coloured-icon bg-white shadow-sm border-0" style="border-radius: 12px;">
      <i class="icon fa fa-check-circle fa-2x" style="background-color: #f0fff4; color: #28a745; border-radius: 12px 0 0 12px;"></i>
      <div class="info">
        <h4 class="text-dark small text-uppercase font-weight-bold">Active Items</h4>
        <p class="mb-0 h3 text-dark"><b>{{ $summaryStats['active'] }}</b></p>
      </div>
    </div>
  </div>
</div>

<div class="row">
  <div class="col-md-12">
    <div class="tile border-0 shadow-sm" style="border-radius: 15px;">
      <div class="tile-title-w-btn mb-4">
        <div class="d-flex align-items-center">
            <div class="bg-primary-light text-primary rounded-circle mr-3 d-flex align-items-center justify-content-center" style="width: 45px; height: 45px; background: #eef2ff;">
                <i class="fa fa-list-ul fa-lg"></i>
            </div>
            <h3 class="title mb-0">Product Inventory</h3>
        </div>
        <div class="btn-group">
          <a class="btn btn-primary rounded-pill shadow-sm px-4" href="{{ route($routePrefix . '.products.create') }}">
            <i class="fa fa-plus-circle"></i> Register New Product
          </a>
        </div>
      </div>
      
      @php 
        $type = request('type');
        
        // Merge specific categories into "Soft Drinks" as requested
        $groupedProducts = $products->groupBy(function($item) {
            if (in_array($item->category, ['non_alcoholic_beverage', 'energy_drinks', 'juices'])) {
                return 'soft_drinks';
            }
            return $item->category;
        });
        
        $categoryOrder = ['soft_drinks', 'water', 'alcoholic_beverage', 'wines', 'spirits', 'hot_beverages', 'cocktails'];
        
        $sortedCategories = $groupedProducts->keys()->sortBy(function($key) use ($categoryOrder) {
            $pos = array_search($key, $categoryOrder);
            return $pos === false ? 999 : $pos;
        });

        $categoryStyles = [
            'soft_drinks' => ['icon' => 'fa-flask', 'color' => '#ff4757', 'bg' => '#fff0f0'],
            'water' => ['icon' => 'fa-tint', 'color' => '#2f3542', 'bg' => '#f1f2f6'],
            'alcoholic_beverage' => ['icon' => 'fa-beer', 'color' => '#ffa502', 'bg' => '#fff9e6'],
            'wines' => ['icon' => 'fa-vine', 'color' => '#6c5ce7', 'bg' => '#f3f0ff'],
            'spirits' => ['icon' => 'fa-glass', 'color' => '#2d3436', 'bg' => '#f5f5f5'],
            'hot_beverages' => ['icon' => 'fa-coffee', 'color' => '#747d8c', 'bg' => '#f8f9fa'],
            'cocktails' => ['icon' => 'fa-magic', 'color' => '#ff6b81', 'bg' => '#fff0f3']
        ];
      @endphp

      <!-- Navigation Tabs (Template Style) -->
      <div class="row mb-0 mt-3">
        <div class="col-md-12">
          <div class="d-flex align-items-end justify-content-between bg-light rounded-top p-2" style="border: 1px solid #dee2e6; border-bottom: none;">
            <ul class="nav nav-tabs border-0" role="tablist" style="gap: 5px;">
              <li class="nav-item">
                <a class="nav-link py-2 px-3 {{ !request('category') ? 'active font-weight-bold' : '' }}" 
                   id="all-tab" href="#" onclick="setCategoryFilter('', this); return false;"
                   style="{{ !request('category') ? 'color: #e77a3a; background: #fff; border-color: #dee2e6 #dee2e6 #fff;' : 'color: #6c757d; border: 1px solid transparent;' }}">
                   <i class="fa fa-th-large"></i> All Items
                </a>
              </li>
              @foreach($sortedCategories as $categoryKey)
                @php
                    $style = $categoryStyles[$categoryKey] ?? ['icon' => 'fa-cube', 'color' => '#747d8c', 'bg' => '#f8f9fa'];
                    $cName = ($categoryKey === 'soft_drinks') ? 'Soft Drinks' : ucfirst(str_replace('_', ' ', $categoryKey));
                    $isActive = request('category') == $categoryKey;
                @endphp
                <li class="nav-item">
                  <a class="nav-link py-2 px-3 {{ $isActive ? 'active font-weight-bold' : '' }}" 
                     id="tab-{{ $categoryKey }}" href="#" onclick="setCategoryFilter('{{ $categoryKey }}', this); return false;"
                     style="{{ $isActive ? 'color: #e77a3a; background: #fff; border-color: #dee2e6 #dee2e6 #fff;' : 'color: #6c757d; border: 1px solid transparent;' }}">
                     <i class="fa {{ $style['icon'] }} mr-1"></i> {{ $cName }}
                     <span class="badge badge-light border ml-1">{{ $groupedProducts[$categoryKey]->count() }}</span>
                  </a>
                </li>
              @endforeach
            </ul>
            
            <div class="pb-1 pr-2" style="width: 300px;">
              <div class="input-group input-group-sm rounded bg-white border">
                <div class="input-group-prepend">
                  <span class="input-group-text bg-transparent border-0"><i class="fa fa-search text-muted"></i></span>
                </div>
                <input type="text" class="form-control border-0" id="searchInput" placeholder="Search products..." 
                       value="{{ request('search') }}" oninput="filterProducts()" style="box-shadow: none;">
                <input type="hidden" id="categoryFilter" value="{{ request('category') }}">
              </div>
            </div>
          </div>
        </div>
      </div>
      
      @if($products->count() > 0)
      <div id="productsWrapper" class="tab-content" style="background: #fff; border: 1px solid #dee2e6; border-top: none; border-radius: 0 0 8px 8px; padding: 20px;">
        <div class="table-responsive">
          <table class="table table-hover table-bordered mb-0">
              <thead class="bg-light text-dark">
                  <tr>
                      <th style="width: 60px;" class="text-center">#</th>
                      <th>Product Name & Brand</th>
                      <th>Supplier</th>
                      <th>Volume/Size</th>
                      <th>Sale Config</th>
                      <th class="text-center">Actions</th>
                  </tr>
              </thead>
              <tbody>
                  @foreach($sortedCategories as $categoryKey)
                      @php 
                          $items = $groupedProducts[$categoryKey]; 
                          $style = $categoryStyles[$categoryKey] ?? ['icon' => 'fa-cube', 'color' => '#747d8c', 'bg' => '#f8f9fa'];
                          $displayName = ($categoryKey === 'soft_drinks') ? 'Soft Drinks & Sodas' : (ucfirst(str_replace('_', ' ', $categoryKey)));
                      @endphp
                      
                      <tr class="category-header bg-light" data-section-category="{{ $categoryKey }}">
                          <td colspan="6" class="py-2 px-3">
                              <div class="d-flex align-items-center justify-content-between">
                                  <span class="font-weight-bold text-dark" style="font-size: 13px;">
                                      <i class="fa {{ $style['icon'] }} mr-2 text-primary"></i> {{ $displayName }}
                                  </span>
                                  <span class="badge badge-pill badge-secondary">{{ $items->count() }} items</span>
                              </div>
                          </td>
                      </tr>
  
                      @foreach($items as $product)
                          @foreach($product->variants as $variant)
                              <tr class="product-row" 
                                  data-product-name="{{ strtolower($variant->variant_name . ' ' . $product->name) }}"
                                  data-product-supplier="{{ strtolower($product->supplier->name ?? '') }}"
                                  data-product-category="{{ $product->category }}"
                                  data-section-category="{{ $categoryKey }}">
                                  <td class="text-center align-middle">
                                      <div class="rounded-circle d-flex align-items-center justify-content-center mx-auto shadow-xs" style="width: 35px; height: 35px; background: {{ $style['bg'] }}; color: {{ $style['color'] }};">
                                          <i class="fa {{ $style['icon'] }}"></i>
                                      </div>
                                  </td>
                                  <td class="align-middle">
                                      <div class="font-weight-bold text-dark">{{ $variant->variant_name }}</div>
                                      <div class="small text-muted">{{ $product->name }}</div>
                                  </td>
                                  <td class="align-middle px-3">
                                      <span class="text-muted small font-weight-bold">{{ $product->supplier->name ?? 'Direct' }}</span>
                                  </td>
                                  <td class="align-middle">
                                      <span class="badge badge-light border text-dark px-2 font-weight-bold">{{ $variant->measurement }}</span>
                                  </td>
                                  <td class="align-middle">
                                      @if($variant->can_sell_as_pic)<span class="badge badge-success-light mr-1"><i class="fa fa-shopping-bag mr-1"></i> Bottle</span>@endif
                                      @if($variant->can_sell_as_serving)<span class="badge badge-info-light"><i class="fa fa-glass mr-1"></i> {{ ucfirst($variant->selling_unit ?? 'Glass') }}</span>@endif
                                  </td>
                                  <td class="text-center align-middle">
                                      <div class="btn-group">
                                          <button class="btn btn-sm btn-light rounded-circle border p-2 mr-2" onclick="viewProduct({{ $product->id }})" title="View Details">
                                              <i class="fa fa-eye text-primary"></i>
                                          </button>
                                          <a href="{{ route($routePrefix . '.products.edit', $product->id) }}" class="btn btn-sm btn-light rounded-circle border p-2 mr-2" title="Edit" style="display: inline-flex; align-items: center; justify-content: center;">
                                              <i class="fa fa-edit text-info"></i>
                                          </a>
                                          <button class="btn btn-sm btn-light rounded-circle border p-2" onclick="deleteVariant({{ $variant->id }})" title="Delete Variant">
                                              <i class="fa fa-trash text-danger"></i>
                                          </button>
                                      </div>
                                  </td>
                              </tr>
                          @endforeach
                      @endforeach
                  @endforeach
              </tbody>
          </table>
        </div>
      </div>
      
      <div class="d-flex justify-content-center mt-4">
        {{ $products->appends(request()->input())->links() }}
      </div>
      @else
      <div class="text-center py-5">
        <i class="fa fa-cube fa-5x text-muted mb-4 opacity-25"></i>
        <h3 class="mb-2">No Products Registered Yet</h3>
        <p class="text-muted mb-4">Start your bar inventory by registering your products and sizes.</p>
        <a href="{{ route($routePrefix . '.products.create') }}" class="btn btn-primary btn-lg px-5 shadow">
          <i class="fa fa-plus"></i> Register First Product
        </a>
      </div>
      @endif
      
      <div id="noResultsMessage" class="text-center py-5" style="display: none;">
        <i class="fa fa-search fa-5x text-muted mb-4 opacity-25"></i>
        <h3 class="mb-2">No Matches Found</h3>
        <p class="text-muted mb-4">Try adjusting your search terms or filters.</p>
        <button class="btn btn-secondary btn-lg px-4" onclick="resetFilters()">
          <i class="fa fa-refresh"></i> Show All Products
        </button>
      </div>
    </div>
  </div>
</div>

<!-- Details Modal -->
<div class="modal fade" id="productDetailsModal" tabindex="-1" role="dialog" aria-hidden="true">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content shadow-lg border-0">
      <div class="modal-header bg-light">
        <h5 class="modal-title font-weight-bold">
          <i class="fa fa-cube text-primary mr-2"></i> Product Details
        </h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body p-4" id="productDetailsContent">
        <!-- Load via JS -->
      </div>
      <div class="modal-footer bg-light border-0">
        <button type="button" class="btn btn-secondary px-4" data-dismiss="modal">Close Window</button>
      </div>
    </div>
  </div>
</div>

@endsection

@section('scripts')
<script>
// Beautiful default icon helper
function getCategoryStyle(category) {
    const styles = {
        'non_alcoholic_beverage': {icon: 'fa-flask', grad: 'linear-gradient(135deg, #ff0844 0%, #ffb199 100%)'},
        'soft_drinks': {icon: 'fa-flask', grad: 'linear-gradient(135deg, #ff0844 0%, #ffb199 100%)'},
        'energy_drinks': {icon: 'fa-flask', grad: 'linear-gradient(135deg, #ff0844 0%, #ffb199 100%)'},
        'juices': {icon: 'fa-flask', grad: 'linear-gradient(135deg, #ff0844 0%, #ffb199 100%)'},
        'water': {icon: 'fa-tint', grad: 'linear-gradient(135deg, #4facfe 0%, #00f2fe 100%)'},
        'alcoholic_beverage': {icon: 'fa-beer', grad: 'linear-gradient(135deg, #fceabb 0%, #f8b500 100%)'},
        'wines': {icon: 'fa-vine', grad: 'linear-gradient(135deg, #8E24AA 0%, #D81B60 100%)'},
        'spirits': {icon: 'fa-glass', grad: 'linear-gradient(135deg, #243B55 0%, #141E30 100%)'},
        'hot_beverages': {icon: 'fa-coffee', grad: 'linear-gradient(135deg, #3D2B1F 0%, #964B00 100%)'},
        'cocktails': {icon: 'fa-magic', grad: 'linear-gradient(135deg, #F093FB 0%, #F5576C 100%)'}
    };
    return styles[category] || {icon: 'fa-cube', grad: 'linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%)'};
}

// Filter logic
let searchTimeout;
let triggerFromSearch = false;

function setCategoryFilter(val, el) {
    $('#categoryFilter').val(val);
    
    // Reset styles for all navigation links
    $('.nav-link').each(function() {
        $(this).removeClass('active font-weight-bold');
        $(this).css({
            'color': '#6c757d',
            'background': 'transparent',
            'border-color': 'transparent'
        });
    });
    
    // Apply active styles to the clicked tab
    $(el).addClass('active font-weight-bold');
    $(el).css({
        'color': '#e77a3a',
        'background': '#fff',
        'border-color': '#dee2e6 #dee2e6 #fff'
    });
    
    if (!triggerFromSearch) {
        document.getElementById('searchInput').value = '';
    }
    triggerFromSearch = false;
    filterProducts();
}

function filterProducts() {
  clearTimeout(searchTimeout);
  searchTimeout = setTimeout(function() {
    const searchTerm = document.getElementById('searchInput').value.toLowerCase();
    const categoryFilter = document.getElementById('categoryFilter').value;
    
    // Auto-switch to "All" tab if searching while inside a specific tab
    if (searchTerm.length > 0 && categoryFilter !== '') {
        triggerFromSearch = true;
        setCategoryFilter('', document.getElementById('all-tab'));
        return;
    }

    const rows = document.querySelectorAll('.product-row');
    const headers = document.querySelectorAll('.category-header');
    let totalVisible = 0;
    
    // 1. Filter Rows
    rows.forEach(row => {
      const name = row.dataset.productName || '';
      const supplier = row.dataset.productSupplier || '';
      const cat = row.dataset.productCategory || '';
      
      const matchSearch = (!searchTerm || name.includes(searchTerm) || supplier.includes(searchTerm));
      const matchCategory = (!categoryFilter || row.dataset.sectionCategory === categoryFilter);
      
      const isVisible = matchSearch && matchCategory;
      row.style.display = isVisible ? 'table-row' : 'none';
      if (isVisible) totalVisible++;
    });
    
    // 2. Filter Headers (only show if they have visible items)
    headers.forEach(header => {
        const sectionCat = header.dataset.sectionCategory;
        const matchCategory = !categoryFilter || sectionCat === categoryFilter;
        
        let hasVisibleRows = false;
        let next = header.nextElementSibling;
        while (next && next.classList.contains('product-row')) {
            if (next.style.display !== 'none') {
                hasVisibleRows = true;
                break;
            }
            next = next.nextElementSibling;
        }
        
        header.style.display = (matchCategory && hasVisibleRows) ? 'table-row' : 'none';
    });
    
    document.getElementById('noResultsMessage').style.display = (totalVisible === 0) ? 'block' : 'none';
    document.getElementById('productsWrapper').style.display = (totalVisible === 0) ? 'none' : 'block';
  }, 250);
}

function resetFilters() {
  document.getElementById('searchInput').value = '';
  setCategoryFilter('', document.getElementById('all-tab'));
}

function viewProduct(id) {
  const content = $('#productDetailsContent');
  $('#productDetailsModal').modal('show');
  content.html('<div class="text-center py-5"><i class="fa fa-spinner fa-spin fa-3x text-primary mb-3"></i><p>Loading information...</p></div>');
  
    fetch(`{{ route($routePrefix . ".products.show", ":id") }}`.replace(':id', id), {
    headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' }
  })
  .then(res => res.json())
  .then(data => {
    if (!data.success) throw new Error('Failed');
    const p = data.product;
    const variants = p.variants || [];
    const catStyle = getCategoryStyle(p.category);
    
    const iconHtml = `<div class="d-flex align-items-center justify-content-center rounded-circle shadow-sm border mx-auto mb-3" style="height: 120px; width: 120px; background: ${catStyle.bg}; color: ${catStyle.color}; border: 2px solid ${catStyle.color}44 !important;">
                            <i class="fa ${catStyle.icon} fa-4x"></i>
                         </div>`;
    
    let html = `
      <div class="text-center mb-4">
          ${iconHtml}
          <h3 class="font-weight-bold mb-1">${p.name}</h3>
          <p class="text-muted"><i class="fa fa-tag"></i> ${p.category_name || p.category}</p>
      </div>

      <div class="row mb-4">
        <div class="col-md-6">
            <div class="p-3 bg-light rounded border-left" style="border-left: 4px solid ${catStyle.color} !important;">
                <label class="small text-muted text-uppercase font-weight-bold mb-1">Supplier Info</label>
                <div class="h6 mb-0 font-weight-bold">${p.supplier ? p.supplier.name : 'Direct Supply'}</div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="p-3 bg-light rounded border-left" style="border-left: 4px solid #6c757d !important;">
                <label class="small text-muted text-uppercase font-weight-bold mb-1">Product Type</label>
                <div class="h6 mb-0 font-weight-bold">${p.type.toUpperCase()}</div>
            </div>
        </div>
      </div>

      <div class="variants-section">
        <h6 class="font-weight-bold text-uppercase small text-muted mb-3">Variants & Sizes</h6>
        <div class="list-group">
          ${variants.map(v => `
            <div class="list-group-item list-group-item-action d-flex justify-content-between align-items-center border-0 mb-2 rounded bg-white shadow-sm transition-all p-3">
              <div class="d-flex align-items-center">
                <div class="mr-3 text-center" style="width: 40px;">
                    <i class="fa ${catStyle.icon} text-muted fa-lg"></i>
                </div>
                <div>
                    <div class="font-weight-bold">${v.variant_name}</div>
                    <div class="small text-muted">${v.measurement}</div>
                </div>
              </div>
              <div class="text-right">
                ${v.can_sell_as_pic ? `<span class="badge badge-success-light px-2 py-1 mb-1">BOTTLE: ${Number(v.selling_price_per_pic || 0).toLocaleString()} TZS</span><br>` : ''}
                ${v.can_sell_as_serving ? `<span class="badge badge-info-light px-2 py-1">GLASS: ${Number(v.selling_price_per_serving || 0).toLocaleString()} TZS</span>` : ''}
              </div>
            </div>
          `).join('')}
        </div>
      </div>
    `;
    content.html(html);
  })
  .catch(err => {
    content.html('<div class="alert alert-danger">Error loading product details.</div>');
  });
}

function deleteVariant(id) {
    Swal.fire({
        title: 'Are you sure?',
        text: "You are about to delete this specific product variant.",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'Yes, delete variant!'
    }).then((result) => {
        if (result.isConfirmed) {
            fetch(`{{ route($routePrefix . ".products.variants.destroy", ":id") }}`.replace(':id', id), {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json'
                }
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    Swal.fire('Deleted!', data.message, 'success').then(() => location.reload());
                } else {
                    Swal.fire('Failed!', data.message || 'Error occurred', 'error');
                }
            });
        }
    });
}

function deleteProduct(id) {
    Swal.fire({
        title: 'Are you sure?',
        text: "You won't be able to revert this! This product will be removed from inventory lists.",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'Yes, delete it!'
    }).then((result) => {
        if (result.isConfirmed) {
            fetch(`{{ route($routePrefix . ".products.destroy", ":id") }}`.replace(':id', id), {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json'
                }
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    Swal.fire('Deleted!', data.message, 'success').then(() => location.reload());
                } else {
                    Swal.fire('Failed!', data.message || 'Error occurred', 'error');
                }
            });
        }
    });
}
</script>
@endsection
