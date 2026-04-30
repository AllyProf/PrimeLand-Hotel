{{-- Manager/Admin Sidebar Menu (Clean & Organized - Matched to Reference Design) --}}
@php
  use App\Services\RolePermissionService;
  
  $currentRoute = request()->route() ? request()->route()->getName() : '';
  $activePage = request()->path();
  
  $hasPermission = function($permission) use ($currentUser) {
    return \App\Services\RolePermissionService::hasPermission($currentUser, $permission);
  };
  
  $isSuperAdmin = function() use ($currentUser) {
    return $currentUser && method_exists($currentUser, 'isSuperAdmin') ? $currentUser->isSuperAdmin() : false;
  };

  $hasRole = function($role) use ($currentUser) {
    return \App\Services\RolePermissionService::hasRole($currentUser, $role);
  };
  
  $isChef = $hasRole('head_chef');
@endphp

{{-- 1. DASHBOARD --}}
<li>
    <a class="app-menu__item {{ (str_contains($activePage, 'admin/dashboard') || str_contains($activePage, 'restaurant/food/dashboard') || str_contains($activePage, 'chef-master/dashboard')) ? 'active' : '' }}" 
       href="{{ $isChef ? route('chef-master.dashboard') : route('admin.dashboard') }}">
        <i class="app-menu__icon fa fa-dashboard"></i>
        <span class="app-menu__label">Dashboard</span>
    </a>
</li>

<li class="nav-header">Inventory Control</li>

{{-- 2. STOCK MANAGEMENT --}}
<li class="treeview {{ (str_contains($activePage, 'products') || str_contains($activePage, 'recipes') || str_contains($activePage, 'bar-keeper/stock') || str_contains($activePage, 'chef-master/inventory') || str_contains($activePage, 'housekeeping-inventory') || str_contains($activePage, 'suppliers') || str_contains($activePage, 'purchase-requests')) ? 'is-expanded' : '' }}">
    <a class="app-menu__item" href="#" data-toggle="treeview">
        <i class="app-menu__icon fa fa-archive"></i>
        <span class="app-menu__label">Stock Management</span>
        <i class="treeview-indicator fa fa-angle-right"></i>
    </a>
    <ul class="treeview-menu">
        <li><a class="treeview-item {{ str_contains($activePage, 'products/create') ? 'active' : '' }}" href="{{ route('admin.products.create') }}"><i class="icon fa fa-plus-circle"></i> Register Products</a></li>
        <li><a class="treeview-item {{ (str_contains($activePage, 'products') && !str_contains($activePage, 'create')) ? 'active' : '' }}" href="{{ route('admin.products.index') }}"><i class="icon fa fa-list-ul"></i> Products List</a></li>
        <li><a class="treeview-item {{ str_contains($activePage, 'purchase-requests') ? 'active' : '' }}" href="{{ route('admin.purchase-requests.index') }}"><i class="icon fa fa-download"></i> Receiving Stock</a></li>
        <li class="treeview-divider"></li>
        <li><a class="treeview-item {{ str_contains($activePage, 'bar-keeper/stock') ? 'active' : '' }}" href="{{ route('bar-keeper.stock.index') }}"><i class="icon fa fa-glass"></i> Bar Warehouse</a></li>
        <li><a class="treeview-item {{ str_contains($activePage, 'chef-master/inventory') ? 'active' : '' }}" href="{{ route('chef-master.inventory') }}"><i class="icon fa fa-cubes"></i> Kitchen Warehouse</a></li>
        <li><a class="treeview-item {{ str_contains($activePage, 'housekeeping-inventory') ? 'active' : '' }}" href="{{ route('admin.housekeeping-inventory') }}"><i class="icon fa fa-bed"></i> HK Warehouse</a></li>
        <li class="treeview-divider"></li>
        <li><a class="treeview-item {{ str_contains($activePage, 'suppliers') ? 'active' : '' }}" href="{{ route('admin.suppliers.index') }}"><i class="icon fa fa-truck"></i> Suppliers Directory</a></li>
        <li><a class="treeview-item {{ str_contains($activePage, 'recipes') ? 'active' : '' }}" href="{{ route('admin.recipes.index') }}"><i class="icon fa fa-book"></i> Menu Recipes</a></li>
    </ul>
</li>

<li class="nav-header">Hospitality Ops</li>

{{-- 3. FRONT OFFICE --}}
<li class="treeview {{ (str_contains($activePage, 'bookings') || str_contains($activePage, 'reservations') || str_contains($activePage, 'day-services') || str_contains($activePage, 'guests')) ? 'is-expanded' : '' }}">
    <a class="app-menu__item" href="#" data-toggle="treeview">
        <i class="app-menu__icon fa fa-calendar-check-o"></i>
        <span class="app-menu__label">Reservation Desk</span>
        <i class="treeview-indicator fa fa-angle-right"></i>
    </a>
    <ul class="treeview-menu">
        <li><a class="treeview-item" href="{{ route('admin.bookings.index') }}"><i class="icon fa fa-list"></i> All Bookings</a></li>
        <li><a class="treeview-item" href="{{ route('admin.bookings.calendar') }}"><i class="icon fa fa-calendar"></i> Calendar View</a></li>
        <li><a class="treeview-item" href="{{ route('admin.reservations.check-in') }}"><i class="icon fa fa-sign-in"></i> Check-In</a></li>
        <li><a class="treeview-item" href="{{ route('admin.reservations.check-out') }}"><i class="icon fa fa-sign-out"></i> Check-Out</a></li>
        <li class="treeview-divider"></li>
        <li><a class="treeview-item" href="{{ route('admin.guests') }}"><i class="icon fa fa-users"></i> Guest Directory</a></li>
        <li><a class="treeview-item" href="{{ route('admin.day-services.index') }}"><i class="icon fa fa-coffee"></i> Day Services</a></li>
    </ul>
</li>

{{-- 4. FACILITY MANAGEMENT --}}
<li class="treeview {{ (str_contains($activePage, 'rooms') || str_contains($activePage, 'lost-found')) ? 'is-expanded' : '' }}">
    <a class="app-menu__item" href="#" data-toggle="treeview">
        <i class="app-menu__icon fa fa-building"></i>
        <span class="app-menu__label">Facility Management</span>
        <i class="treeview-indicator fa fa-angle-right"></i>
    </a>
    <ul class="treeview-menu">
        <li><a class="treeview-item" href="{{ route('admin.rooms.index') }}"><i class="icon fa fa-bed"></i> Room List</a></li>
        <li><a class="treeview-item" href="{{ route('admin.rooms.status') }}"><i class="icon fa fa-info-circle"></i> Occupancy Status</a></li>
        <li><a class="treeview-item" href="{{ route('admin.rooms.cleaning') }}"><i class="icon fa fa-broom"></i> Housekeeping</a></li>
        <li class="treeview-divider"></li>
        <li><a class="treeview-item" href="{{ route('admin.rooms.issues') }}"><i class="icon fa fa-wrench"></i> Maintenance Issues</a></li>
        <li><a class="treeview-item" href="{{ route('reception.lost-found.index') }}"><i class="icon fa fa-suitcase"></i> Lost & Found</a></li>
    </ul>
</li>

<li class="nav-header">Administration</li>

{{-- 5. HUMAN RESOURCES --}}
<li class="treeview {{ (str_contains($activePage, 'users') || str_contains($activePage, 'messaging')) ? 'is-expanded' : '' }}">
    <a class="app-menu__item" href="#" data-toggle="treeview">
        <i class="app-menu__icon fa fa-users"></i>
        <span class="app-menu__label">Human Resources</span>
        <i class="treeview-indicator fa fa-angle-right"></i>
    </a>
    <ul class="treeview-menu">
        <li><a class="treeview-item" href="{{ route('admin.users') }}"><i class="icon fa fa-user-circle"></i> Staff Access</a></li>
        <li><a class="treeview-item" href="{{ route('manager.sms.index') }}"><i class="icon fa fa-envelope"></i> Messaging Center</a></li>
    </ul>
</li>

{{-- 6. FINANCIAL RECONCILIATION --}}
<li class="treeview {{ (str_contains($activePage, 'shift') || str_contains($activePage, 'exchange-rates')) ? 'is-expanded' : '' }}">
    <a class="app-menu__item" href="#" data-toggle="treeview">
        <i class="app-menu__icon fa fa-calculator"></i>
        <span class="app-menu__label">Financial Reconciliation</span>
        <i class="treeview-indicator fa fa-angle-right"></i>
    </a>
    <ul class="treeview-menu">
        <li><a class="treeview-item" href="{{ route('reception.shift.history') }}"><i class="icon fa fa-history"></i> Shift History</a></li>
        @if(!$hasRole('manager') || $isSuperAdmin())
            <li><a class="treeview-item" href="{{ route('reception.shift.open') }}"><i class="icon fa fa-key"></i> Open New Shift</a></li>
        @endif
        <li class="treeview-divider"></li>
        @if($hasPermission('manage_exchange_rates') || $isSuperAdmin())
            <li><a class="treeview-item" href="{{ route('exchange-rates') }}"><i class="icon fa fa-exchange"></i> Exchange Rates</a></li>
        @endif
    </ul>
</li>

{{-- 7. REPORTS --}}
<li>
    <a class="app-menu__item {{ str_contains($activePage, 'admin/reports') ? 'active' : '' }}" href="{{ route('admin.reports.index') }}">
        <i class="app-menu__icon fa fa-bar-chart"></i>
        <span class="app-menu__label">Business Reports</span>
    </a>
</li>

{{-- 8. SETTINGS --}}
<li class="treeview {{ str_contains($activePage, 'admin/settings') ? 'is-expanded' : '' }}">
    <a class="app-menu__item" href="#" data-toggle="treeview">
        <i class="app-menu__icon fa fa-cog"></i>
        <span class="app-menu__label">Global Settings</span>
        <i class="treeview-indicator fa fa-angle-right"></i>
    </a>
    <ul class="treeview-menu">
        <li><a class="treeview-item" href="{{ route('admin.settings.hotel') }}"><i class="icon fa fa-building"></i> Hotel Info</a></li>
        <li><a class="treeview-item" href="{{ route('admin.settings.rooms') }}"><i class="icon fa fa-bed"></i> Room Config</a></li>
    </ul>
</li>
