@extends('dashboard.layouts.app')

@php
use Illuminate\Support\Facades\Storage;
$routePrefix = request()->is('bar-keeper*') ? 'bar-keeper' : 'admin';
@endphp

@section('content')

{{-- Page Title --}}
<div class="app-title">
  <div>
    <h1><i class="fa fa-cube"></i> Products</h1>
    <p>Manage restaurant &amp; bar products — drinks, food, and variants</p>
  </div>
  <ul class="app-breadcrumb breadcrumb">
    <li class="breadcrumb-item"><i class="fa fa-home fa-lg"></i></li>
    <li class="breadcrumb-item"><a href="{{ route($routePrefix === 'admin' ? 'admin.dashboard' : 'bar-keeper.dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item active">Products</li>
  </ul>
</div>

@php
  $type = request('type');

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
      'soft_drinks'        => ['icon' => 'fa-flask',   'color' => '#e74c3c', 'bg' => '#fdf0ef'],
      'water'              => ['icon' => 'fa-tint',    'color' => '#3498db', 'bg' => '#eaf4fd'],
      'alcoholic_beverage' => ['icon' => 'fa-beer',    'color' => '#e67e22', 'bg' => '#fef5ec'],
      'wines'              => ['icon' => 'fa-glass',   'color' => '#8e44ad', 'bg' => '#f5eefb'],
      'spirits'            => ['icon' => 'fa-glass',   'color' => '#2c3e50', 'bg' => '#eaecee'],
      'hot_beverages'      => ['icon' => 'fa-coffee',  'color' => '#795548', 'bg' => '#f5f0eb'],
      'cocktails'          => ['icon' => 'fa-magic',   'color' => '#e91e8c', 'bg' => '#fdeef7'],
  ];

  $totalVariants = $products->sum(fn($p) => $p->variants->count());
@endphp

{{-- Summary Stats --}}
<div class="row">
  <div class="col-md-4">
    <div class="widget-small primary coloured-icon">
      <i class="icon fa fa-tags fa-3x"></i>
      <div class="info">
        <h4>Product Brands</h4>
        <p><b>{{ $summaryStats['brands'] }}</b></p>
      </div>
    </div>
  </div>
  <div class="col-md-4">
    <div class="widget-small warning coloured-icon">
      <i class="icon fa fa-folder fa-3x"></i>
      <div class="info">
        <h4>Categories</h4>
        <p><b>{{ $summaryStats['categories'] }}</b></p>
      </div>
    </div>
  </div>
  <div class="col-md-4">
    <div class="widget-small success coloured-icon">
      <i class="icon fa fa-check-circle fa-3x"></i>
      <div class="info">
        <h4>Active Variants</h4>
        <p><b>{{ $totalVariants }}</b></p>
      </div>
    </div>
  </div>
</div>

{{-- Main Table Tile --}}
<div class="row">
  <div class="col-md-12">
    <div class="tile">

      {{-- Tile Header --}}
      <div class="tile-title-w-btn">
        <h3 class="title"><i class="fa fa-list-ul"></i> Product Inventory</h3>
        <div class="btn-group">
          <a class="btn btn-primary btn-sm" href="{{ route($routePrefix . '.products.create') }}">
            <i class="fa fa-plus-circle"></i> &nbsp;Register New Product
          </a>
        </div>
      </div>

      <hr style="margin-top: 10px; margin-bottom: 15px;">

      {{-- Filters Row --}}
      <div class="row mb-3 align-items-center">

        {{-- Category Tabs --}}
        <div class="col-md-9">
          <ul class="nav nav-tabs" role="tablist">
            <li class="nav-item">
              <a class="nav-link {{ !request('category') ? 'active' : '' }}"
                 href="#" id="all-tab" onclick="setCategoryFilter('', this); return false;">
                <i class="fa fa-th-large"></i> All Items
              </a>
            </li>
            @foreach($sortedCategories as $categoryKey)
              @php
                $style  = $categoryStyles[$categoryKey] ?? ['icon' => 'fa-cube', 'color' => '#747d8c', 'bg' => '#f8f9fa'];
                $cName  = ($categoryKey === 'soft_drinks') ? 'Soft Drinks' : ucfirst(str_replace('_', ' ', $categoryKey));
                $isActive = request('category') == $categoryKey;
              @endphp
              <li class="nav-item">
                <a class="nav-link {{ $isActive ? 'active' : '' }}"
                   id="tab-{{ $categoryKey }}" href="#"
                   onclick="setCategoryFilter('{{ $categoryKey }}', this); return false;">
                  <i class="fa {{ $style['icon'] }}"></i> {{ $cName }}
                  <span class="badge badge-default">{{ $groupedProducts[$categoryKey]->count() }}</span>
                </a>
              </li>
            @endforeach
          </ul>
        </div>

        {{-- Search --}}
        <div class="col-md-3">
          <div class="input-group input-group-sm">
            <span class="input-group-addon"><i class="fa fa-search"></i></span>
            <input type="text" class="form-control" id="searchInput"
                   placeholder="Search product or supplier..."
                   value="{{ request('search') }}" oninput="filterProducts()">
            <input type="hidden" id="categoryFilter" value="{{ request('category') }}">
          </div>
        </div>

      </div>

      {{-- Products Table --}}
      @if($products->count() > 0)
      <div id="productsWrapper" class="table-responsive">
        <table class="table table-hover table-bordered" id="productsTable">
          <thead>
            <tr>
              <th style="width: 50px;" class="text-center">&nbsp;</th>
              <th>Product Name &amp; Brand</th>
              <th>Supplier</th>
              <th>Volume / Size</th>
              <th>Sale Configuration</th>
              <th class="text-center" style="width: 140px;">Actions</th>
            </tr>
          </thead>
          <tbody>
            @foreach($sortedCategories as $categoryKey)
              @php
                $items       = $groupedProducts[$categoryKey];
                $style       = $categoryStyles[$categoryKey] ?? ['icon' => 'fa-cube', 'color' => '#747d8c', 'bg' => '#f8f9fa'];
                $displayName = ($categoryKey === 'soft_drinks') ? 'Soft Drinks &amp; Sodas' : ucfirst(str_replace('_', ' ', $categoryKey));
              @endphp

              {{-- Category Separator Row --}}
              <tr class="category-header" data-section-category="{{ $categoryKey }}"
                  style="background: #f9f9f9;">
                <td colspan="6" style="padding: 8px 14px;">
                  <div class="d-flex align-items-center justify-content-between">
                    <span style="font-weight: 700; font-size: 12px; text-transform: uppercase; letter-spacing: 1px; color: {{ $style['color'] }};">
                      <i class="fa {{ $style['icon'] }} mr-1"></i> {{ $displayName }}
                    </span>
                    <span class="badge badge-default">{{ $items->count() }} items</span>
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

                    {{-- Icon --}}
                    <td class="text-center align-middle">
                      <span class="badge" style="background: {{ $style['bg'] }}; color: {{ $style['color'] }}; font-size: 15px; border-radius: 50%; width: 34px; height: 34px; line-height: 34px; display: inline-block;">
                        <i class="fa {{ $style['icon'] }}"></i>
                      </span>
                    </td>

                    {{-- Name --}}
                    <td class="align-middle">
                      <strong class="text-dark">{{ $variant->variant_name }}</strong>
                      <div class="text-muted small">{{ $product->name }}</div>
                    </td>

                    {{-- Supplier --}}
                    <td class="align-middle">
                      <span class="text-muted">{{ $product->supplier->name ?? '—' }}</span>
                    </td>

                    {{-- Volume --}}
                    <td class="align-middle">
                      <span class="badge badge-default">{{ $variant->measurement }}</span>
                    </td>

                    {{-- Sale Config --}}
                    <td class="align-middle">
                      @if($variant->can_sell_as_pic)
                        <span class="badge badge-success"><i class="fa fa-shopping-bag"></i> Bottle</span>
                      @endif
                      @if($variant->can_sell_as_serving)
                        <span class="badge badge-info"><i class="fa fa-glass"></i> {{ ucfirst($variant->selling_unit ?? 'Glass') }}</span>
                      @endif
                    </td>

                    {{-- Actions --}}
                    <td class="text-center align-middle">
                      <div class="btn-group">
                        <button class="btn btn-sm btn-default" onclick="viewProduct({{ $product->id }})" title="View Details">
                          <i class="fa fa-eye text-primary"></i>
                        </button>
                        <a href="{{ route($routePrefix . '.products.edit', $product->id) }}"
                           class="btn btn-sm btn-info" title="Edit">
                          <i class="fa fa-edit"></i>
                        </a>
                        <button class="btn btn-sm btn-danger" onclick="deleteVariant({{ $variant->id }})" title="Delete Variant">
                          <i class="fa fa-trash"></i>
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

      {{-- Pagination --}}
      <div class="text-center mt-3">
        {{ $products->appends(request()->input())->links() }}
      </div>

      @else
      {{-- Empty State --}}
      <div class="text-center" style="padding: 60px 20px;">
        <i class="fa fa-cube fa-5x text-muted" style="opacity: 0.25;"></i>
        <h3 class="mt-4">No Products Registered Yet</h3>
        <p class="text-muted">Start your bar inventory by registering your first product.</p>
        <a href="{{ route($routePrefix . '.products.create') }}" class="btn btn-primary btn-lg">
          <i class="fa fa-plus"></i> Register First Product
        </a>
      </div>
      @endif

      {{-- No Search Results --}}
      <div id="noResultsMessage" class="text-center" style="display: none; padding: 60px 20px;">
        <i class="fa fa-search fa-5x text-muted" style="opacity: 0.25;"></i>
        <h3 class="mt-4">No Matches Found</h3>
        <p class="text-muted">Try adjusting your search terms or filters.</p>
        <button class="btn btn-default" onclick="resetFilters()">
          <i class="fa fa-refresh"></i> Show All Products
        </button>
      </div>

    </div>{{-- /.tile --}}
  </div>
</div>

{{-- Product Details Modal --}}
<div class="modal fade" id="productDetailsModal" tabindex="-1" role="dialog">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
        <h4 class="modal-title"><i class="fa fa-cube"></i> Product Details</h4>
      </div>
      <div class="modal-body" id="productDetailsContent">
        {{-- Loaded via JS --}}
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>

@endsection

@section('scripts')
<script>
// ── Category icon/gradient map ──────────────────────────────────────────────
function getCategoryStyle(category) {
    const styles = {
        'non_alcoholic_beverage': {icon: 'fa-flask',  color: '#e74c3c', bg: '#fdf0ef'},
        'soft_drinks':            {icon: 'fa-flask',  color: '#e74c3c', bg: '#fdf0ef'},
        'energy_drinks':          {icon: 'fa-flask',  color: '#e74c3c', bg: '#fdf0ef'},
        'juices':                 {icon: 'fa-flask',  color: '#e74c3c', bg: '#fdf0ef'},
        'water':                  {icon: 'fa-tint',   color: '#3498db', bg: '#eaf4fd'},
        'alcoholic_beverage':     {icon: 'fa-beer',   color: '#e67e22', bg: '#fef5ec'},
        'wines':                  {icon: 'fa-glass',  color: '#8e44ad', bg: '#f5eefb'},
        'spirits':                {icon: 'fa-glass',  color: '#2c3e50', bg: '#eaecee'},
        'hot_beverages':          {icon: 'fa-coffee', color: '#795548', bg: '#f5f0eb'},
        'cocktails':              {icon: 'fa-magic',  color: '#e91e8c', bg: '#fdeef7'},
    };
    return styles[category] || {icon: 'fa-cube', color: '#747d8c', bg: '#f8f9fa'};
}

// ── Filter / Search ─────────────────────────────────────────────────────────
let searchTimeout;
let triggerFromSearch = false;

function setCategoryFilter(val, el) {
    $('#categoryFilter').val(val);
    $('.nav-tabs .nav-link').removeClass('active');
    $(el).addClass('active');
    if (!triggerFromSearch) {
        document.getElementById('searchInput').value = '';
    }
    triggerFromSearch = false;
    filterProducts();
}

function filterProducts() {
    clearTimeout(searchTimeout);
    searchTimeout = setTimeout(function() {
        const searchTerm    = document.getElementById('searchInput').value.toLowerCase();
        const categoryFilter = document.getElementById('categoryFilter').value;

        if (searchTerm.length > 0 && categoryFilter !== '') {
            triggerFromSearch = true;
            setCategoryFilter('', document.getElementById('all-tab'));
            return;
        }

        const rows    = document.querySelectorAll('.product-row');
        const headers = document.querySelectorAll('.category-header');
        let totalVisible = 0;

        rows.forEach(row => {
            const name     = row.dataset.productName     || '';
            const supplier = row.dataset.productSupplier || '';
            const matchSearch   = !searchTerm || name.includes(searchTerm) || supplier.includes(searchTerm);
            const matchCategory = !categoryFilter || row.dataset.sectionCategory === categoryFilter;
            const visible = matchSearch && matchCategory;
            row.style.display = visible ? 'table-row' : 'none';
            if (visible) totalVisible++;
        });

        headers.forEach(header => {
            const sectionCat    = header.dataset.sectionCategory;
            const matchCategory = !categoryFilter || sectionCat === categoryFilter;
            let hasVisible = false;
            let next = header.nextElementSibling;
            while (next && next.classList.contains('product-row')) {
                if (next.style.display !== 'none') { hasVisible = true; break; }
                next = next.nextElementSibling;
            }
            header.style.display = (matchCategory && hasVisible) ? 'table-row' : 'none';
        });

        const noResults = document.getElementById('noResultsMessage');
        const wrapper   = document.getElementById('productsWrapper');
        if (noResults) noResults.style.display = (totalVisible === 0) ? 'block' : 'none';
        if (wrapper)   wrapper.style.display   = (totalVisible === 0) ? 'none'  : 'block';
    }, 250);
}

function resetFilters() {
    document.getElementById('searchInput').value = '';
    setCategoryFilter('', document.getElementById('all-tab'));
}

// ── View Product Modal ───────────────────────────────────────────────────────
function viewProduct(id) {
    const content = $('#productDetailsContent');
    $('#productDetailsModal').modal('show');
    content.html('<div class="text-center py-5"><i class="fa fa-spinner fa-spin fa-3x text-primary"></i><p class="mt-3">Loading...</p></div>');

    fetch(`{{ route($routePrefix . ".products.show", ":id") }}`.replace(':id', id), {
        headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' }
    })
    .then(r => r.json())
    .then(data => {
        if (!data.success) throw new Error('Failed');
        const p        = data.product;
        const variants = p.variants || [];
        const s        = getCategoryStyle(p.category);

        let html = `
          <div class="text-center mb-4">
            <div class="d-inline-flex align-items-center justify-content-center rounded-circle mb-3"
                 style="width:80px;height:80px;background:${s.bg};color:${s.color};">
              <i class="fa ${s.icon} fa-3x"></i>
            </div>
            <h4 class="mb-0">${p.name}</h4>
            <small class="text-muted">${p.category_name || p.category}</small>
          </div>

          <div class="row mb-4">
            <div class="col-md-6">
              <div class="form-group">
                <label class="control-label">Supplier</label>
                <p class="form-control-static">${p.supplier ? p.supplier.name : 'Direct Supply'}</p>
              </div>
            </div>
            <div class="col-md-6">
              <div class="form-group">
                <label class="control-label">Product Type</label>
                <p class="form-control-static">${p.type ? p.type.toUpperCase() : '—'}</p>
              </div>
            </div>
          </div>

          <h5 class="text-muted" style="font-size:13px; text-transform:uppercase; letter-spacing:1px; border-bottom:1px solid #eee; padding-bottom:8px;">
            Variants &amp; Sizes
          </h5>
          <table class="table table-bordered table-sm mt-2">
            <thead>
              <tr>
                <th>Variant</th>
                <th>Size</th>
                <th>Bottle Price</th>
                <th>Glass Price</th>
              </tr>
            </thead>
            <tbody>
              ${variants.map(v => `
                <tr>
                  <td>${v.variant_name}</td>
                  <td>${v.measurement}</td>
                  <td>${v.can_sell_as_pic    ? '<span class="badge badge-success">' + Number(v.selling_price_per_pic || 0).toLocaleString() + ' TZS</span>' : '<span class="text-muted">—</span>'}</td>
                  <td>${v.can_sell_as_serving ? '<span class="badge badge-info">'    + Number(v.selling_price_per_serving || 0).toLocaleString() + ' TZS</span>' : '<span class="text-muted">—</span>'}</td>
                </tr>
              `).join('')}
            </tbody>
          </table>
        `;
        content.html(html);
    })
    .catch(() => {
        content.html('<div class="alert alert-danger">Error loading product details.</div>');
    });
}

// ── Delete Variant ───────────────────────────────────────────────────────────
function deleteVariant(id) {
    Swal.fire({
        title: 'Delete this variant?',
        text: 'This will remove this specific product variant from the inventory.',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'Yes, delete it!'
    }).then(result => {
        if (result.isConfirmed) {
            fetch(`{{ route($routePrefix . ".products.variants.destroy", ":id") }}`.replace(':id', id), {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json'
                }
            })
            .then(r => r.json())
            .then(data => {
                if (data.success) {
                    Swal.fire('Deleted!', data.message, 'success').then(() => location.reload());
                } else {
                    Swal.fire('Error', data.message || 'Something went wrong.', 'error');
                }
            });
        }
    });
}
</script>
@endsection
