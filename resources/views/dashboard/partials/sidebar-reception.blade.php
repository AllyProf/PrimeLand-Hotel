{{-- Reception Sidebar Menu (Clean & Organized) --}}
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
 
  $badges = $sidebarBadges ?? [
    'extensions' => 0,
    'room_issues' => 0,
    'service_requests' => 0,
    'issues' => 0
  ];
@endphp

{{-- 1. DASHBOARD --}}
<li>
    <a class="app-menu__item {{ str_contains($activePage, 'reception/dashboard') ? 'active' : '' }}" href="{{ route('reception.dashboard') }}">
        <i class="app-menu__icon fa fa-dashboard"></i>
        <span class="app-menu__label">Dashboard</span>
    </a>
</li>

<li class="nav-header">Front Office Operations</li>

<li class="treeview {{ str_contains($activePage, 'reception/bookings') || str_contains($activePage, 'reception/reservations') || str_contains($activePage, 'reception/extension-requests') || str_contains($activePage, 'admin/bookings/calendar') ? 'is-expanded' : '' }}">
    <a class="app-menu__item" href="#" data-toggle="treeview"><i class="app-menu__icon fa fa-calendar-check-o"></i><span class="app-menu__label">Bookings & Front Desk</span><i class="treeview-indicator fa fa-angle-right"></i></a>
    <ul class="treeview-menu">
        <li><a class="treeview-item" href="{{ route('reception.bookings') }}"><i class="icon fa fa-list"></i> All Bookings</a></li>
        <li><a class="treeview-item" href="{{ route('admin.bookings.calendar') }}"><i class="icon fa fa-calendar"></i> Calendar View</a></li>
        <li class="treeview-divider"></li>
        <li><a class="treeview-item" href="{{ route('reception.bookings.manual.create') }}"><i class="icon fa fa-plus-circle"></i> New Booking</a></li>
        <li><a class="treeview-item" href="{{ route('admin.bookings.corporate.create') }}"><i class="icon fa fa-building"></i> Corporate Booking</a></li>
        <li class="treeview-divider"></li>
        <li><a class="treeview-item" href="{{ route('reception.reservations.check-in') }}"><i class="icon fa fa-sign-in"></i> Guest Check-In</a></li>
        <li><a class="treeview-item" href="{{ route('reception.reservations.check-out') }}"><i class="icon fa fa-sign-out"></i> Guest Check-Out</a></li>
        <li><a class="treeview-item" href="{{ route('reception.extension-requests') }}"><i class="icon fa fa-clock-o"></i> Extensions @if($badges['extensions'] > 0)<span class="badge badge-info badge-pill ml-2">{{ $badges['extensions'] }}</span>@endif</a></li>
    </ul>
</li>

<li class="treeview {{ str_contains($activePage, 'reception/day-services') || str_contains($activePage, 'reception/service-catalog') ? 'is-expanded' : '' }}">
    <a class="app-menu__item" href="#" data-toggle="treeview"><i class="app-menu__icon fa fa-coffee"></i><span class="app-menu__label">Leisure & Day Services</span><i class="treeview-indicator fa fa-angle-right"></i></a>
    <ul class="treeview-menu">
        <li><a class="treeview-item" href="{{ route('reception.day-services.index') }}"><i class="icon fa fa-list-alt"></i> All Services</a></li>
        <li><a class="treeview-item" href="{{ route('reception.day-services.swimming') }}"><i class="icon fa fa-tint"></i> Swimming Pool</a></li>
        <li><a class="treeview-item" href="{{ route('reception.day-services.ceremony') }}"><i class="icon fa fa-birthday-cake"></i> Ceremonies</a></li>
        <li><a class="treeview-item" href="{{ route('reception.service-catalog.index') }}"><i class="icon fa fa-list"></i> Service Catalog</a></li>
    </ul>
</li>

<li><a class="app-menu__item {{ str_contains($activePage, 'reception/guests') ? 'active' : '' }}" href="{{ route('reception.guests') }}"><i class="app-menu__icon fa fa-users"></i><span class="app-menu__label">Guest Directory</span></a></li>

<li class="nav-header">Finance & Reporting</li>

<li class="treeview {{ str_contains($activePage, 'shift/') ? 'is-expanded' : '' }}">
    <a class="app-menu__item" href="#" data-toggle="treeview"><i class="app-menu__icon fa fa-lock"></i><span class="app-menu__label">Shift Management</span><i class="treeview-indicator fa fa-angle-right"></i></a>
    <ul class="treeview-menu">
        <li><a class="treeview-item" href="{{ route('reception.shift.open') }}"><i class="icon fa fa-key"></i> Open New Shift</a></li>
        <li><a class="treeview-item" href="{{ route('reception.shift.close') }}"><i class="icon fa fa-sign-out"></i> Close & Reconcile</a></li>
        <li><a class="treeview-item" href="{{ route('reception.reports.waiter-sales') }}"><i class="icon fa fa-line-chart"></i> Waiter Sales</a></li>
    </ul>
</li>

<li><a class="app-menu__item {{ str_contains($activePage, 'reception/payments') ? 'active' : '' }}" href="{{ route('reception.payments') }}"><i class="app-menu__icon fa fa-money"></i><span class="app-menu__label">Payment Logs</span></a></li>

<li class="treeview {{ str_contains($activePage, 'reception/invoices') ? 'is-expanded' : '' }}">
    <a class="app-menu__item" href="#" data-toggle="treeview"><i class="app-menu__icon fa fa-file-text-o"></i><span class="app-menu__label">Invoices & Quotations</span><i class="treeview-indicator fa fa-angle-right"></i></a>
    <ul class="treeview-menu">
        <li><a class="treeview-item" href="{{ route('reception.invoices.create') }}"><i class="icon fa fa-plus-square"></i> New Invoice</a></li>
        <li><a class="treeview-item" href="{{ route('reception.invoices.index') }}"><i class="icon fa fa-file-text"></i> Sent Quotations</a></li>
    </ul>
</li>

<li class="nav-header">Maintenance & Facility</li>

<li class="treeview {{ str_contains($activePage, 'reception/rooms') || str_contains($activePage, 'orders/monitor') || str_contains($activePage, 'service-requests') ? 'is-expanded' : '' }}">
    <a class="app-menu__item" href="#" data-toggle="treeview"><i class="app-menu__icon fa fa-bed"></i><span class="app-menu__label">Rooms & Housekeeping</span><i class="treeview-indicator fa fa-angle-right"></i></a>
    <ul class="treeview-menu">
        <li><a class="treeview-item" href="{{ route('reception.rooms') }}"><i class="icon fa fa-info-circle"></i> Room Occupancy</a></li>
        <li><a class="treeview-item" href="{{ route('reception.rooms.cleaning') }}"><i class="icon fa fa-broom"></i> Cleaning Schedule</a></li>
        <li class="treeview-divider"></li>
        <li><a class="treeview-item" href="{{ route('reception.orders.monitor') }}"><i class="icon fa fa-television"></i> Live Order Monitor</a></li>
        <li><a class="treeview-item" href="{{ route('reception.service-requests') }}"><i class="icon fa fa-bell"></i> Service Requests @if($badges['service_requests'] > 0)<span class="badge badge-warning badge-pill ml-2">{{ $badges['service_requests'] }}</span>@endif</a></li>
    </ul>
</li>

<li><a class="app-menu__item {{ str_contains($activePage, 'reception/issues') ? 'active' : '' }}" href="{{ route('reception.issues.index') }}"><i class="app-menu__icon fa fa-exclamation-triangle"></i><span class="app-menu__label">Guest Issues</span> @if(isset($badges['issues']) && $badges['issues'] > 0)<span class="badge badge-warning badge-pill ml-2">{{ $badges['issues'] }}</span>@endif</a></li>

<li><a class="app-menu__item {{ str_contains($activePage, 'admin/room-issues') ? 'active' : '' }}" href="{{ route('admin.rooms.issues') }}"><i class="app-menu__icon fa fa-wrench"></i><span class="app-menu__label">Room Repairs</span> @if($badges['room_issues'] > 0)<span class="badge badge-danger badge-pill ml-2">{{ $badges['room_issues'] }}</span>@endif</a></li>

<li class="nav-header">Tools & Profile</li>

<li><a class="app-menu__item {{ str_contains($activePage, 'exchange-rates') ? 'active' : '' }}" href="{{ route('exchange-rates') }}"><i class="app-menu__icon fa fa-exchange"></i><span class="app-menu__label">Exchange Rates</span></a></li>

<li><a class="app-menu__item {{ str_contains($activePage, 'reception/messaging') ? 'active' : '' }}" href="{{ route('reception.sms.index') }}"><i class="app-menu__icon fa fa-envelope"></i><span class="app-menu__label">Messaging Center</span></a></li>

<li><a class="app-menu__item {{ str_contains($activePage, 'profile') ? 'active' : '' }}" href="{{ route('reception.profile') }}"><i class="app-menu__icon fa fa-user-circle"></i><span class="app-menu__label">Account Settings</span></a></li>
