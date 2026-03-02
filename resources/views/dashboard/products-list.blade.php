@extends('dashboard.layouts.app')

@php
use Illuminate\Support\Facades\Storage;
$routePrefix = request()->is('bar-keeper*') ? 'bar-keeper' : 'admin';
@endphp

@section('content')
<style>
    .product-card {
        border: none !important;
        background: #ffffff !important;
    }
    .product-card:hover {
        cursor: pointer;
    }
    .btn-white {
        background: #ffffff !important;
        border: none !important;
        background-color: #fff !important;
        transition: all 0.2s ease;
    }
    .btn-white:hover {
        background: #f8f9fa !important;
        color: #000 !important;
        transform: scale(1.05);
    }
    .category-section h4 {
        color: #333 !important;
        text-transform: uppercase;
        letter-spacing: 1px;
        font-size: 1.1rem;
    }
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
  <div class="col-md-3">
    <div class="widget-small primary coloured-icon text-white" style="background: linear-gradient(135deg, #4b6cb7 0%, #182848 100%);">
      <i class="icon fa fa-tags fa-3x"></i>
      <div class="info">
        <h4 class="text-white">Product Brands</h4>
        <p class="text-white"><b>{{ $summaryStats['brands'] }}</b></p>
      </div>
    </div>
  </div>
  <div class="col-md-3">
    <div class="widget-small info coloured-icon text-white" style="background: linear-gradient(135deg, #00c6ff 0%, #0072ff 100%);">
      <i class="icon fa fa-barcode fa-3x"></i>
      <div class="info">
        <h4 class="text-white">Total SKUs</h4>
        <p class="text-white"><b>{{ $summaryStats['variants'] }}</b></p>
      </div>
    </div>
  </div>
  <div class="col-md-3">
    <div class="widget-small warning coloured-icon text-white" style="background: linear-gradient(135deg, #f2994a 0%, #f2c94c 100%);">
      <i class="icon fa fa-folder fa-3x"></i>
      <div class="info">
        <h4 class="text-white">Categories</h4>
        <p class="text-white"><b>{{ $summaryStats['categories'] }}</b></p>
      </div>
    </div>
  </div>
  <div class="col-md-3">
    <div class="widget-small success coloured-icon text-white" style="background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);">
      <i class="icon fa fa-check-circle fa-3x"></i>
      <div class="info">
        <h4 class="text-white">Active Items</h4>
        <p class="text-white"><b>{{ $summaryStats['active'] }}</b></p>
      </div>
    </div>
  </div>
</div>

<div class="row">
  <div class="col-md-12">
    <div class="tile shadow-sm">
      <div class="tile-title-w-btn mb-4">
        <h3 class="title">Product Inventory</h3>
        <div class="btn-group">
          <a class="btn btn-primary shadow-sm" href="{{ route($routePrefix . '.products.create') }}">
            <i class="fa fa-plus"></i> Register New Product
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
            'soft_drinks' => ['icon' => 'fa-flask', 'grad' => 'linear-gradient(135deg, #ff0844 0%, #ffb199 100%)'],
            'water' => ['icon' => 'fa-tint', 'grad' => 'linear-gradient(135deg, #4facfe 0%, #00f2fe 100%)'],
            'alcoholic_beverage' => ['icon' => 'fa-beer', 'grad' => 'linear-gradient(135deg, #fceabb 0%, #f8b500 100%)'],
            'wines' => ['icon' => 'fa-vine', 'grad' => 'linear-gradient(135deg, #8E24AA 0%, #D81B60 100%)'],
            'spirits' => ['icon' => 'fa-glass', 'grad' => 'linear-gradient(135deg, #243B55 0%, #141E30 100%)'],
            'hot_beverages' => ['icon' => 'fa-coffee', 'grad' => 'linear-gradient(135deg, #3D2B1F 0%, #964B00 100%)'],
            'cocktails' => ['icon' => 'fa-magic', 'grad' => 'linear-gradient(135deg, #F093FB 0%, #F5576C 100%)']
        ];
      @endphp

      <!-- Filters & Category Tabs -->
      <div class="row border-bottom pb-4 mb-4 mx-0 align-items-center">
        <div class="col-md-8 pl-0 mb-3 mb-md-0">
          <ul class="nav nav-pills category-tabs-scrollable" id="categoryTabs" role="tablist" style="flex-wrap: nowrap; overflow-x: auto; padding-bottom: 5px; -webkit-overflow-scrolling: touch;">
            <li class="nav-item pr-2">
              <a class="nav-link active font-weight-bold px-3 py-2 shadow-sm border text-white btn-primary" id="all-tab" data-toggle="tab" href="#all" role="tab" onclick="setCategoryFilter('', this); return false;" style="border-radius: 25px; white-space: nowrap;">
                  <i class="fa fa-th-large mr-1"></i> All Items
              </a>
            </li>
            @foreach($sortedCategories as $categoryKey)
              @php
                  $style = $categoryStyles[$categoryKey] ?? ['icon' => 'fa-cube', 'grad' => 'linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%)'];
                  $cName = ($categoryKey === 'soft_drinks') ? 'Soft Drinks & Sodas' : ucfirst(str_replace('_', ' ', $categoryKey));
              @endphp
              <li class="nav-item pr-2">
                <a class="nav-link px-3 py-2 font-weight-bold shadow-sm text-dark bg-white border" id="tab-{{ $categoryKey }}" data-toggle="tab" href="#pane-{{ $categoryKey }}" role="tab" onclick="setCategoryFilter('{{ $categoryKey }}', this); return false;" style="border-radius: 25px; white-space: nowrap;">
                    <i class="fa {{ $style['icon'] }} mr-1"></i> {{ $cName }} <span class="badge badge-light border text-muted ml-1">{{ $groupedProducts[$categoryKey]->count() }}</span>
                </a>
              </li>
            @endforeach
          </ul>
          <input type="hidden" id="categoryFilter" value="">
        </div>
        <div class="col-md-4 pr-0">
          <div class="input-group shadow-sm">
            <div class="input-group-prepend">
              <span class="input-group-text bg-white border-right-0"><i class="fa fa-search text-muted"></i></span>
            </div>
            <input type="text" class="form-control border-left-0 border-right-0" id="searchInput" placeholder="Search product..." 
                   value="{{ request('search') }}" 
                   oninput="filterProducts()">
            <div class="input-group-append">
               <button class="btn border border-left-0 bg-white text-muted" type="button" onclick="resetFilters()">
                 <i class="fa fa-refresh"></i>
               </button>
            </div>
          </div>
        </div>
      </div>
      
      @if($products->count() > 0)
      <div id="productsWrapper">

        @foreach($sortedCategories as $categoryKey)
          @php 
            $items = $groupedProducts[$categoryKey]; 
            $categoryStyles = [
                'soft_drinks' => ['icon' => 'fa-flask', 'grad' => 'linear-gradient(135deg, #ff0844 0%, #ffb199 100%)'],
                'water' => ['icon' => 'fa-tint', 'grad' => 'linear-gradient(135deg, #4facfe 0%, #00f2fe 100%)'],
                'alcoholic_beverage' => ['icon' => 'fa-beer', 'grad' => 'linear-gradient(135deg, #fceabb 0%, #f8b500 100%)'],
                'wines' => ['icon' => 'fa-vine', 'grad' => 'linear-gradient(135deg, #8E24AA 0%, #D81B60 100%)'],
                'spirits' => ['icon' => 'fa-glass', 'grad' => 'linear-gradient(135deg, #243B55 0%, #141E30 100%)'],
                'hot_beverages' => ['icon' => 'fa-coffee', 'grad' => 'linear-gradient(135deg, #3D2B1F 0%, #964B00 100%)'],
                'cocktails' => ['icon' => 'fa-magic', 'grad' => 'linear-gradient(135deg, #F093FB 0%, #F5576C 100%)']
            ];
            $style = $categoryStyles[$categoryKey] ?? ['icon' => 'fa-cube', 'grad' => 'linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%)'];
            $icon = $style['icon'];
            $grad = $style['grad'];
            $displayName = ($categoryKey === 'soft_drinks') ? 'Soft Drinks & Sodas' : (ucfirst(str_replace('_', ' ', $categoryKey)));
          @endphp
          
          <div class="category-section mb-5" data-section-category="{{ $categoryKey }}">
            <div class="d-flex align-items-center mb-3 pb-2 border-bottom">
              <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center mr-3" style="width: 40px; height: 40px;">
                <i class="fa {{ $icon }} fa-lg"></i>
              </div>
              <h4 class="m-0 font-weight-bold" style="letter-spacing: 0.5px;">
                {{ $displayName }}
                <span class="badge badge-pill badge-light border ml-2 text-muted" style="font-size: 14px; font-weight: normal;">{{ $items->count() }} items</span>
              </h4>
            </div>
            
            <div class="row">
              @foreach($items as $product)
                @foreach($product->variants as $variant)
                  <div class="col-md-4 col-lg-3 mb-4 product-card-wrapper" 
                       data-product-name="{{ strtolower($variant->variant_name . ' ' . $product->name) }}"
                       data-product-supplier="{{ strtolower($product->supplier->name ?? '') }}"
                       data-product-brand="{{ strtolower($product->brand_or_type ?? '') }}"
                       data-product-category-name="{{ strtolower($product->category_name) }}"
                       data-category="{{ $product->category }}"
                       data-product-type="{{ strtolower($product->type) }}">
                    <div class="card h-100 product-card border-0 shadow-sm" style="transition: all 0.3s cubic-bezier(0.25, 0.8, 0.25, 1); border-radius: 15px; overflow: hidden; background: #fff;" 
                         onmouseover="this.style.transform='translateY(-8px)'; this.style.boxShadow='0 15px 30px rgba(0,0,0,0.1)';" 
                         onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='0 2px 4px rgba(0,0,0,0.05)';" 
                         onclick="viewProduct({{ $product->id }})">
                      
                      <div class="card-header border-0 p-0 position-relative" style="height: 180px; background: #f0f2f5; cursor: pointer;">
                        @if($variant->image)
                          <img src="{{ asset('storage/' . ltrim($variant->image, '/')) }}" alt="{{ $variant->variant_name }}" 
                               class="w-100 h-100" style="object-fit: cover;"
                               onerror="this.onerror=null; this.src='{{ asset('dashboard_assets/images/room-placeholder.jpg') }}';">
                        @else
                          <div class="d-flex align-items-center justify-content-center h-100" style="background: {!! $grad !!};">
                            <i class="fa {{ $icon }} fa-4x text-white opacity-50"></i>
                          </div>
                        @endif
                        
                        <!-- Category Bubble -->
                        <div class="position-absolute" style="top: 12px; left: 12px;">
                            <span class="badge badge-light shadow-sm px-2 py-1" style="font-size: 10px; text-transform: uppercase; font-weight: 700; color: #555; background: rgba(255,255,255,0.9);">
                               <i class="fa {{ $icon }} mr-1"></i> {{ substr($product->category_name, 0, 15) }}
                            </span>
                        </div>
                        
                        <!-- Measurement Badge -->
                         <div class="position-absolute" style="bottom: 12px; right: 12px;">
                            <span class="badge badge-primary shadow-sm px-2 py-1" style="font-size: 11px;">
                               {{ $variant->measurement }}
                            </span>
                        </div>
                      </div>

                      <div class="card-body p-3 d-flex flex-column">
                        <h5 class="card-title mb-1 font-weight-bold text-dark" style="font-size: 1.05rem; line-height: 1.2;">
                          {{ $variant->variant_name }}
                        </h5>
                        <p class="small text-muted mb-2 font-italic">{{ $product->name }}</p>
                        
                        <div class="small text-muted mb-3">
                           <i class="fa fa-building-o mr-1"></i> {{ $product->supplier->name ?? 'Direct Supply' }}
                        </div>
                        
                        <div class="mt-auto pt-2 d-flex justify-content-between align-items-center border-top">
                            <div class="small font-weight-bold">
                               @if($variant->can_sell_as_pic) <span class="badge badge-success">BOTTLE</span> @endif
                               @if($variant->can_sell_as_serving) <span class="badge badge-info">GLASS</span> @endif
                            </div>
                            <!-- Price or Status could go here -->
                        </div>
                      </div>

                      <div class="card-footer bg-white border-top-0 p-3 d-flex">
                        <div class="btn-group w-100 shadow-sm border rounded">
                            <button class="btn btn-sm btn-white text-primary rounded-left py-2" onclick="event.stopPropagation(); viewProduct({{ $product->id }})" title="View Details" style="border: none;">
                              <i class="fa fa-eye"></i>
                            </button>
                            <a href="{{ route($routePrefix . '.products.edit', $product) }}" class="btn btn-sm btn-white text-info py-2" onclick="event.stopPropagation();" title="Edit Family" style="border: none; border-left: 1px solid #eee; border-right: 1px solid #eee;">
                              <i class="fa fa-edit"></i>
                            </a>
                            <!-- Deleting single variant via main list is tricky, might delete main product if not careful. For now keeping link to main deletion or hiding -->
                             <button class="btn btn-sm btn-white text-danger rounded-right py-2" onclick="event.stopPropagation(); deleteVariant({{ $variant->id }})" title="Delete Variant" style="border: none;">
                              <i class="fa fa-trash"></i>
                            </button>
                        </div>
                      </div>
                    </div>
                  </div>
                @endforeach
              @endforeach
            </div>
          </div>
        @endforeach
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
    $('#categoryTabs .nav-link').removeClass('active text-white btn-primary').addClass('text-dark bg-white border');
    $(el).addClass('active text-white btn-primary').removeClass('text-dark bg-white border');
    
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
    
    // Auto-switch to "All" tab if searching while inside a specific tab
    if (searchTerm.length > 0 && $('#categoryFilter').val() !== '') {
        triggerFromSearch = true;
        setCategoryFilter('', document.getElementById('all-tab'));
        return;
    }

    const categoryFilter = document.getElementById('categoryFilter').value;
    const cards = document.querySelectorAll('.product-card-wrapper');
    let count = 0;
    
    cards.forEach(card => {
      const name = card.dataset.productName || '';
      const supplier = card.dataset.productSupplier || '';
      const brand = card.dataset.productBrand || '';
      const categoryName = card.dataset.productCategoryName || '';
      
      const matchSearch = (!searchTerm || name.includes(searchTerm) || supplier.includes(searchTerm) || brand.includes(searchTerm) || categoryName.includes(searchTerm));
      
      card.style.display = matchSearch ? 'block' : 'none';
      if (matchSearch) count++;
    });
    
    document.getElementById('noResultsMessage').style.display = (count === 0) ? 'block' : 'none';
    
    document.querySelectorAll('.category-section').forEach(section => {
        const sectionCat = section.dataset.sectionCategory;
        const matchCategory = !categoryFilter || sectionCat === categoryFilter;
        const visibleItems = section.querySelectorAll('.product-card-wrapper[style*="display: block"]');
        
        section.style.display = (matchCategory && visibleItems.length > 0) ? 'block' : 'none';
    });
    
    document.getElementById('productsWrapper').style.display = (count === 0) ? 'none' : 'block';
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
    
    let mainImageHtml = '';
    if (p.image || (variants.length > 0 && variants[0].image)) {
        const imgSrc = p.image ? '{{ asset("storage") }}/' + p.image : '{{ asset("storage") }}/' + variants[0].image;
        mainImageHtml = `<img src="${imgSrc}" class="img-fluid rounded shadow-sm border" style="max-height: 250px;" onerror="this.onerror=null;this.src='{{ asset('dashboard_assets/images/room-placeholder.jpg') }}'">`;
    } else {
        mainImageHtml = `<div class="d-flex align-items-center justify-content-center rounded shadow-sm border" style="height: 250px; width: 100%; background: ${catStyle.grad};">
                            <i class="fa ${catStyle.icon} fa-5x text-white opacity-50"></i>
                         </div>`;
    }
    
    let html = `
      <div class="row align-items-center">
        <div class="col-md-4 text-center mb-4 mb-md-0">
          ${mainImageHtml}
        </div>
        <div class="col-md-8">
          <h3 class="font-weight-bold mb-3">${p.name}</h3>
          <ul class="list-group list-group-flush border rounded">
            <li class="list-group-item"><strong>Category:</strong> ${p.category_name || (p.category ? p.category.replace(/_/g, ' ').replace(/\b\w/g, l => l.toUpperCase()) : 'N/A')}</li>
            <li class="list-group-item"><strong>Supplier:</strong> ${p.supplier ? p.supplier.name : 'Direct'}</li>
            <li class="list-group-item"><strong>Type:</strong> <span class="badge badge-light border px-2">${p.type.toUpperCase()}</span></li>
            <li class="list-group-item"><strong>Registration Date:</strong> ${new Date(p.created_at).toLocaleDateString()}</li>
          </ul>
        </div>
      </div>
      <div class="mt-4">
        <h5 class="font-weight-bold border-bottom pb-2 mb-3"><i class="fa fa-list-ul mr-2 text-primary"></i> Product Variants & Pricing</h5>
        <div class="table-responsive">
          <table class="table table-hover table-bordered bg-white shadow-sm mb-0">
            <thead class="thead-light">
              <tr>
                <th style="width: 60px;" class="text-center">Image</th>
                <th>Variant Name & Size</th>
                <th>Methods</th>
                <th>Price (TSH)</th>
                <th>Price (USD)</th>
              </tr>
            </thead>
            <tbody>
              ${variants.map(v => {
                const innerStyle = getCategoryStyle(p.category);
                
                let tshPrices = [];
                let usdPrices = [];
                
                if (v.can_sell_as_pic) {
                    tshPrices.push(`<div class="mb-1"><span class="text-muted small">Bottle:</span> <strong>${Number(v.selling_price_per_pic || 0).toLocaleString()}</strong></div>`);
                    usdPrices.push(`<div class="mb-1"><span class="text-muted small">Bottle:</span> <strong>$${Number(v.selling_price_per_pic_usd || 0)}</strong></div>`);
                }
                if (v.can_sell_as_serving) {
                    tshPrices.push(`<div><span class="text-muted small">Glass:</span> <strong>${Number(v.selling_price_per_serving || 0).toLocaleString()}</strong></div>`);
                    usdPrices.push(`<div><span class="text-muted small">Glass:</span> <strong>$${Number(v.selling_price_per_serving_usd || 0)}</strong></div>`);
                }

                return `
                <tr>
                  <td class="text-center align-middle p-2">
                    ${v.image ? `<img src="{{ asset('storage') }}/${v.image}" class="rounded shadow-sm" style="width: 45px; height: 45px; object-fit: cover;" onerror="this.onerror=null;this.src='{{ asset('dashboard_assets/images/room-placeholder.jpg') }}'">` : 
                    `<div class="rounded d-flex align-items-center justify-content-center shadow-sm mx-auto" style="width: 45px; height: 45px; background: ${innerStyle.grad};">
                        <i class="fa ${innerStyle.icon} text-white opacity-75" style="font-size: 18px;"></i>
                     </div>`}
                  </td>
                  <td class="align-middle">
                      <div class="font-weight-bold" style="font-size: 14px; color: #333;">${v.variant_name || 'Standard'}</div>
                      <div class="text-muted small" style="font-weight: 500;"><i class="fa fa-balance-scale"></i> ${v.measurement || '-'}</div>
                  </td>
                  <td class="align-middle">
                      ${v.can_sell_as_pic ? '<span class="badge badge-success px-2 py-1 mr-1 mb-1"><i class="fa fa-check"></i> Bottle</span>' : ''}
                      ${v.can_sell_as_serving ? `<span class="badge badge-info px-2 py-1 mb-1"><i class="fa fa-glass"></i> Glass (${v.servings_per_pic || 1})</span>` : ''}
                  </td>
                  <td class="align-middle text-dark">
                      ${tshPrices.length > 0 ? tshPrices.join('') : '<span class="text-muted">-</span>'}
                  </td>
                  <td class="align-middle text-success">
                      ${usdPrices.length > 0 ? usdPrices.join('') : '<span class="text-muted">-</span>'}
                  </td>
                </tr>
              `}).join('')}
            </tbody>
          </table>
        </div>
      </div>
    `;
    content.html(html);
  })
  .catch(err => {
    content.html('<div class="alert alert-danger">Error loading product details. Please try again.</div>');
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
