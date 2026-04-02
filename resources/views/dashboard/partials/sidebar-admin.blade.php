{{-- Manager/Admin Sidebar Menu (Clean & Organized) --}}
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

@if($isChef)
    {{-- HEAD CHEF VIEW --}}
    <li class="nav-header">Kitchen Operations</li>
    <li><a class="app-menu__item {{ str_contains($activePage, 'chef-master/inventory') ? 'active' : '' }}" href="{{ route('chef-master.inventory') }}"><i class="app-menu__icon fa fa-cubes"></i><span class="app-menu__label">Kitchen Stock</span></a></li>
    <li><a class="app-menu__item {{ str_contains($activePage, 'orders/monitor') ? 'active' : '' }}" href="{{ route('reception.orders.monitor') }}"><i class="app-menu__icon fa fa-television"></i><span class="app-menu__label">Order Monitor</span></a></li>
    <li><a class="app-menu__item {{ str_contains($activePage, 'recipes') ? 'active' : '' }}" href="{{ route('admin.recipes.index') }}"><i class="app-menu__icon fa fa-book"></i><span class="app-menu__label">Food Menu</span></a></li>

    <li class="nav-header">Purchasing</li>
    <li class="treeview {{ str_contains($activePage, 'purchase-requests') ? 'is-expanded' : '' }}">
        <a class="app-menu__item" href="#" data-toggle="treeview"><i class="app-menu__icon fa fa-shopping-cart"></i><span class="app-menu__label">Purchase Requests</span><i class="treeview-indicator fa fa-angle-right"></i></a>
        <ul class="treeview-menu">
            <li><a class="treeview-item" href="{{ route('chef-master.purchase-requests.create') }}"><i class="icon fa fa-plus-circle"></i> New Request</a></li>
            <li><a class="treeview-item" href="{{ route('chef-master.purchase-requests.my') }}"><i class="icon fa fa-list-alt"></i> My Requests</a></li>
            <li><a class="treeview-item" href="{{ route('chef-master.purchase-requests.history') }}"><i class="icon fa fa-history"></i> History</a></li>
        </ul>
    </li>

    <li class="nav-header">Reports</li>
    <li><a class="app-menu__item {{ (str_contains($activePage, 'chef-master/reports') && request('type') != 'weekly') ? 'active' : '' }}" href="{{ route('chef-master.reports') }}"><i class="app-menu__icon fa fa-file-text-o"></i><span class="app-menu__label">Daily Stock Sheet</span></a></li>
    <li><a class="app-menu__item {{ (str_contains($activePage, 'chef-master/reports') && request('type') == 'weekly') ? 'active' : '' }}" href="{{ route('chef-master.reports', ['type' => 'weekly']) }}"><i class="app-menu__icon fa fa-calendar"></i><span class="app-menu__label">Weekly Summary</span></a></li>

@else
    {{-- MANAGER/ADMIN VIEW --}}

    <li class="nav-header">Revenue & Sales</li>
    <li class="treeview {{ (str_contains($activePage, 'restaurant/food') || str_contains($activePage, 'recipes') || str_contains($activePage, 'bar-keeper')) ? 'is-expanded' : '' }}">
        <a class="app-menu__item" href="#" data-toggle="treeview"><i class="app-menu__icon fa fa-cutlery"></i><span class="app-menu__label">F&B Operations</span><i class="treeview-indicator fa fa-angle-right"></i></a>
        <ul class="treeview-menu">
            <li><a class="treeview-item" href="{{ route('admin.recipes.index') }}"><i class="icon fa fa-book"></i> Menu Recipes</a></li>
            <li><a class="treeview-item" href="{{ route('bar-keeper.stock.index') }}"><i class="icon fa fa-glass"></i> Bar Stock</a></li>
            <li><a class="treeview-item" href="{{ route('chef-master.inventory') }}"><i class="icon fa fa-cubes"></i> Kitchen Stock</a></li>
        </ul>
    </li>

    <li class="treeview {{ str_contains($activePage, 'shift/') || str_contains($activePage, 'shifts/') ? 'is-expanded' : '' }}">
        <a class="app-menu__item" href="#" data-toggle="treeview"><i class="app-menu__icon fa fa-lock"></i><span class="app-menu__label">Shift Management</span><i class="treeview-indicator fa fa-angle-right"></i></a>
        <ul class="treeview-menu">
            <li><a class="treeview-item" href="{{ route('reception.shift.history') }}"><i class="icon fa fa-history"></i> Shift History</a></li>
            @if(!$hasRole('manager') || $isSuperAdmin())
                <li><a class="treeview-item" href="{{ route('reception.shift.open') }}"><i class="icon fa fa-key"></i> Open New Shift</a></li>
            @endif
        </ul>
    </li>

    <li class="nav-header">Guest & Bookings</li>
    <li class="treeview {{ str_contains($activePage, 'admin/bookings') || str_contains($activePage, 'admin/reservations') || str_contains($activePage, 'admin/extension-requests') ? 'is-expanded' : '' }}">
        <a class="app-menu__item" href="#" data-toggle="treeview"><i class="app-menu__icon fa fa-calendar-check-o"></i><span class="app-menu__label">Reservation Desk</span><i class="treeview-indicator fa fa-angle-right"></i></a>
        <ul class="treeview-menu">
            <li><a class="treeview-item" href="{{ route('admin.bookings.index') }}"><i class="icon fa fa-list"></i> All Bookings</a></li>
            <li><a class="treeview-item" href="{{ route('admin.bookings.calendar') }}"><i class="icon fa fa-calendar"></i> Calendar View</a></li>
            <li class="treeview-divider"></li>
            <li><a class="treeview-item" href="{{ route('admin.reservations.check-in') }}"><i class="icon fa fa-sign-in"></i> Check-In</a></li>
            <li><a class="treeview-item" href="{{ route('admin.reservations.check-out') }}"><i class="icon fa fa-sign-out"></i> Check-Out</a></li>
            <li><a class="treeview-item" href="{{ route('admin.extension-requests') }}"><i class="icon fa fa-clock-o"></i> Extensions @if(isset($sidebarBadges['extensions']) && $sidebarBadges['extensions'] > 0)<span class="badge badge-info badge-pill ml-2">{{ $sidebarBadges['extensions'] }}</span>@endif</a></li>
        </ul>
    </li>

    <li class="treeview {{ str_contains($activePage, 'admin/day-services') ? 'is-expanded' : '' }}">
        <a class="app-menu__item" href="#" data-toggle="treeview"><i class="app-menu__icon fa fa-coffee"></i><span class="app-menu__label">Day Services</span><i class="treeview-indicator fa fa-angle-right"></i></a>
        <ul class="treeview-menu">
            <li><a class="treeview-item" href="{{ route('admin.day-services.index') }}"><i class="icon fa fa-list-alt"></i> Activity Log</a></li>
            <li><a class="treeview-item" href="{{ route('admin.day-services.reports') }}"><i class="icon fa fa-file-pdf-o"></i> Service Reports</a></li>
        </ul>
    </li>

    <li><a class="app-menu__item {{ str_contains($activePage, 'admin/guests') ? 'active' : '' }}" href="{{ route('admin.guests') }}"><i class="app-menu__icon fa fa-users"></i><span class="app-menu__label">Guest Directory</span></a></li>

    <li class="nav-header">Facility & Housekeeping</li>
    <li class="treeview {{ str_contains($activePage, 'admin/rooms') ? 'is-expanded' : '' }}">
        <a class="app-menu__item" href="#" data-toggle="treeview"><i class="app-menu__icon fa fa-bed"></i><span class="app-menu__label">Room Management</span><i class="treeview-indicator fa fa-angle-right"></i></a>
        <ul class="treeview-menu">
            <li><a class="treeview-item" href="{{ route('admin.rooms.index') }}"><i class="icon fa fa-list"></i> Room List</a></li>
            <li><a class="treeview-item" href="{{ route('admin.rooms.status') }}"><i class="icon fa fa-info-circle"></i> Occupancy Status</a></li>
            <li><a class="treeview-item" href="{{ route('admin.rooms.cleaning') }}"><i class="icon fa fa-broom"></i> Housekeeping</a></li>
            <li><a class="treeview-item" href="{{ route('reception.lost-found.index') }}"><i class="icon fa fa-suitcase"></i> Lost & Found</a></li>
        </ul>
    </li>

    <li><a class="app-menu__item {{ str_contains($activePage, 'housekeeper/dashboard') ? 'active' : '' }}" href="{{ route('housekeeper.dashboard') }}"><i class="app-menu__icon fa fa-home"></i><span class="app-menu__label">HK Overview</span></a></li>
    
    <li><a class="app-menu__item {{ str_contains($activePage, 'admin/room-issues') ? 'active' : '' }}" href="{{ route('admin.rooms.issues') }}"><i class="app-menu__icon fa fa-wrench"></i><span class="app-menu__label">Maintenance</span>@if(isset($sidebarBadges['room_issues']) && $sidebarBadges['room_issues'] > 0)<span class="badge badge-danger badge-pill ml-2">{{ $sidebarBadges['room_issues'] }}</span>@endif</a></li>

    <li class="nav-header">Inventory & Supply</li>
    <li class="treeview {{ str_contains($activePage, 'suppliers') || str_contains($activePage, 'purchase-requests') || str_contains($activePage, 'housekeeping-inventory') ? 'is-expanded' : '' }}">
        <a class="app-menu__item" href="#" data-toggle="treeview"><i class="app-menu__icon fa fa-archive"></i><span class="app-menu__label">Inventory Control</span><i class="treeview-indicator fa fa-angle-right"></i></a>
        <ul class="treeview-menu">
            <li><a class="treeview-item" href="{{ route('admin.housekeeping-inventory') }}"><i class="icon fa fa-bed"></i> Housekeeping Stock</a></li>
            <li><a class="treeview-item" href="{{ route('admin.suppliers.index') }}"><i class="icon fa fa-truck"></i> Suppliers</a></li>
            <li class="treeview-divider"></li>
            <li><a class="treeview-item" href="{{ route('admin.purchase-requests.index') }}"><i class="icon fa fa-shopping-cart"></i> Purchase Tracking</a></li>
        </ul>
    </li>

    <li class="nav-header">Analytics & Admin</li>
    <li><a class="app-menu__item {{ str_contains($activePage, 'admin/reports') ? 'active' : '' }}" href="{{ route('admin.reports.index') }}"><i class="app-menu__icon fa fa-bar-chart"></i><span class="app-menu__label">Business Insights</span></a></li>
    <li><a class="app-menu__item {{ str_contains($activePage, 'admin/users') ? 'active' : '' }}" href="{{ route('admin.users') }}"><i class="app-menu__icon fa fa-user-secret"></i><span class="app-menu__label">Staff Access</span></a></li>
    
    <li class="treeview {{ str_contains($activePage, 'admin/settings') ? 'is-expanded' : '' }}">
        <a class="app-menu__item" href="#" data-toggle="treeview"><i class="app-menu__icon fa fa-cog"></i><span class="app-menu__label">Global Settings</span><i class="treeview-indicator fa fa-angle-right"></i></a>
        <ul class="treeview-menu">
            <li><a class="treeview-item" href="{{ route('admin.settings.hotel') }}"><i class="icon fa fa-building"></i> Hotel Info</a></li>
            <li><a class="treeview-item" href="{{ route('admin.settings.rooms') }}"><i class="icon fa fa-bed"></i> Room Config</a></li>
            @if($hasPermission('manage_exchange_rates') || $isSuperAdmin())
                <li><a class="treeview-item" href="{{ route('exchange-rates') }}"><i class="icon fa fa-exchange"></i> Exchange Rates</a></li>
            @endif
        </ul>
    </li>
@endif

<li class="nav-header">Communication</li>
<li><a class="app-menu__item {{ str_contains($activePage, 'messaging') ? 'active' : '' }}" href="{{ route('manager.sms.index') }}"><i class="app-menu__icon fa fa-envelope"></i><span class="app-menu__label">Messaging Center</span></a></li>
